<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriveDownloadController extends Controller
{
    public function __invoke(Request $request, DriveFile $file)
    {
        abort_unless($request->user() && (int) $file->owner_id === (int) $request->user()->id, 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }
}
