<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Erzeugt responsive WebP-Varianten mit PHP-GD (keine zusätzliche Abhängigkeit).
 *
 * Das Neukodieren entfernt zugleich Metadaten wie EXIF/GPS aus Screenshots und Fotos.
 */
class ImageOptimizer
{
    /** Zielbreiten der Varianten in Pixel. */
    public const WIDTHS = [640, 1280, 1920];

    public const QUALITY = 82;

    /**
     * @return array{path: string, variants: array<int, string>, width: int, height: int}
     */
    public function storeWebpVariants(string $sourceFile, string $directory, string $disk = 'public'): array
    {
        if (! function_exists('imagewebp')) {
            throw new ImageOptimizationException('GD ohne WebP-Unterstützung – Bilder können nicht optimiert werden.');
        }

        $info = @getimagesize($sourceFile);
        if ($info === false) {
            throw new ImageOptimizationException('Datei ist kein lesbares Bild.');
        }

        [$width, $height, $type] = $info;

        $image = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourceFile),
            IMAGETYPE_PNG => imagecreatefrompng($sourceFile),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourceFile),
            IMAGETYPE_GIF => imagecreatefromgif($sourceFile),
            default => throw new ImageOptimizationException('Bildformat wird nicht unterstützt (erlaubt: JPG, PNG, WebP, GIF).'),
        };

        if ($image === false) {
            throw new ImageOptimizationException('Bild konnte nicht gelesen werden.');
        }

        $targets = collect(self::WIDTHS)->filter(fn ($w) => $w < $width)->push(min($width, max(self::WIDTHS)))
            ->unique()->sort()->values();

        $basename = Str::random(24);
        $variants = [];

        try {
            foreach ($targets as $targetWidth) {
                $targetHeight = (int) round($height * $targetWidth / $width);
                $resized = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

                ob_start();
                imagewebp($resized, null, self::QUALITY);
                $binary = ob_get_clean();
                imagedestroy($resized);

                $path = trim($directory, '/') . "/{$basename}-{$targetWidth}.webp";
                Storage::disk($disk)->put($path, $binary, 'public');
                $variants[$targetWidth] = $path;
            }
        } finally {
            imagedestroy($image);
        }

        $largest = max(array_keys($variants));

        return [
            'path' => $variants[$largest],
            'variants' => $variants,
            'width' => $largest,
            'height' => (int) round($height * $largest / $width),
        ];
    }

    /** Löscht alle Varianten eines Bildes. */
    public function delete(?array $variants, string $disk = 'public'): void
    {
        foreach ($variants ?? [] as $path) {
            if ($path && ! str_starts_with($path, '/') && ! str_starts_with($path, 'http')) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
