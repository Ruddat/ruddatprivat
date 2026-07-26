<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use App\Support\RangeFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriveStreamController extends Controller
{
    public function __invoke(Request $request, DriveFile $file)
    {
        $admin = auth('admin')->user();

        abort_unless(
            $admin && (int) $file->owner_id === (int) $admin->id,
            403
        );

        $disk = Storage::disk($file->disk);

        abort_unless($disk->exists($file->path), 404);

        $absolutePath = $disk->path($file->path);
        $mimeType = $this->resolveMimeType($file);

        return RangeFileResponse::make(
            $request,
            $absolutePath,
            $mimeType
        );
    }

    private function resolveMimeType(DriveFile $file): string
    {
        $storedMimeType = strtolower(
            trim((string) $file->mime_type)
        );

        $fileName = $file->original_name ?: $file->path;

        $extension = strtolower(
            pathinfo((string) $fileName, PATHINFO_EXTENSION)
        );

        $mimeTypesByExtension = [
            'mov' => 'video/quicktime',
            'qt' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'm4v' => 'video/x-m4v',
            'webm' => 'video/webm',
            'mkv' => 'video/x-matroska',
            'avi' => 'video/x-msvideo',

            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            'ogg' => 'audio/ogg',

            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',

            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
        ];

        /*
         * Gespeicherte unspezifische MIME-Typen anhand der Endung
         * korrigieren.
         */
        if (
            $storedMimeType === ''
            || $storedMimeType === 'application/octet-stream'
            || $storedMimeType === 'binary/octet-stream'
        ) {
            return $mimeTypesByExtension[$extension]
                ?? 'application/octet-stream';
        }

        /*
         * MOV-Dateien konsequent korrekt ausliefern, auch wenn der
         * Upload einen abweichenden MIME-Typ gespeichert hat.
         */
        if ($extension === 'mov' || $extension === 'qt') {
            return 'video/quicktime';
        }

        return $storedMimeType;
    }
}
