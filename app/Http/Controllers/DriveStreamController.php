<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriveStreamController extends Controller
{
    public function __invoke(Request $request, DriveFile $file)
    {
        $admin = auth('admin')->user();

        abort_unless($admin && (int) $file->owner_id === (int) $admin->id, 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return response()->file(Storage::disk($file->disk)->path($file->path), [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
