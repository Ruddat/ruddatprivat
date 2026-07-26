<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RangeFileResponse
{
    public static function make(Request $request, string $path, string $mimeType): StreamedResponse
    {
        $size = filesize($path);

        abort_if($size === false, 404);

        $start = 0;
        $end = $size - 1;
        $status = 200;

        $range = $request->header('Range');

        if ($range && preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
            if ($matches[1] !== '') {
                $start = (int) $matches[1];
            }

            if ($matches[2] !== '') {
                $end = min((int) $matches[2], $size - 1);
            }

            if ($matches[1] === '' && $matches[2] !== '') {
                $suffixLength = min((int) $matches[2], $size);
                $start = $size - $suffixLength;
                $end = $size - 1;
            }

            abort_if($start < 0 || $start > $end || $start >= $size, 416);

            $status = 206;
        }

        $length = $end - $start + 1;

        $headers = [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Length' => (string) $length,
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
        }

        return response()->stream(function () use ($path, $start, $length): void {
            $handle = fopen($path, 'rb');

            if ($handle === false) {
                return;
            }

            try {
                fseek($handle, $start);
                $remaining = $length;

                while ($remaining > 0 && ! feof($handle)) {
                    $chunk = fread($handle, min(1024 * 1024, $remaining));

                    if ($chunk === false || $chunk === '') {
                        break;
                    }

                    echo $chunk;
                    $remaining -= strlen($chunk);

                    if (connection_aborted()) {
                        break;
                    }
                }
            } finally {
                fclose($handle);
            }
        }, $status, $headers);
    }
}
