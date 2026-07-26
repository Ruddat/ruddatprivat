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

        abort_unless($admin && (int) $file->owner_id === (int) $admin->id, 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return RangeFileResponse::make(
            $request,
            Storage::disk($file->disk)->path($file->path),
            $file->mime_type ?: 'application/octet-stream'
        );
    }
}
