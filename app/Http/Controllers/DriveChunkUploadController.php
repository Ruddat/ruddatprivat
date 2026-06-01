<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\DriveShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DriveChunkUploadController extends Controller
{
    private function publicUploadKey(Request $request, string $token): string
    {
        $sessionKey = 'drive_share_upload_key_'.$token;

        if (! $request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, Str::random(48));
        }

        return (string) $request->session()->get($sessionKey);
    }

    private function shareCanAccessFolder(DriveShare $share, DriveFolder $folder): bool
    {
        if ((int) $folder->id === (int) $share->folder_id) {
            return true;
        }

        $current = $folder;

        while ($current->parent_id) {
            if ((int) $current->parent_id === (int) $share->folder_id) {
                return true;
            }

            $current = DriveFolder::query()->find($current->parent_id);

            if (! $current) {
                return false;
            }
        }

        return false;
    }

    public function store(Request $request, string $token)
    {
        $share = DriveShare::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_upload, 403);

        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'max:120'],
            'chunk' => ['required', 'file', 'max:102400'], // 100 MB pro Chunk
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'file_name' => ['required', 'string', 'max:255'],
            'file_size' => ['required', 'integer', 'min:1'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'folder_id' => ['nullable', 'integer', 'exists:drive_folders,id'],
        ]);

        $targetFolderId = $validated['folder_id'] ?? $share->folder_id;
        $targetFolder = DriveFolder::query()->findOrFail($targetFolderId);

        abort_unless($this->shareCanAccessFolder($share, $targetFolder), 403);

        $uploadId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $validated['upload_id']);
        $chunkIndex = (int) $validated['chunk_index'];
        $totalChunks = (int) $validated['total_chunks'];

        $tmpDir = "private/drive/chunks/{$share->id}/{$uploadId}";
        $chunkPath = "{$tmpDir}/chunk_{$chunkIndex}";

        Storage::disk('local')->put(
            $chunkPath,
            file_get_contents($validated['chunk']->getRealPath())
        );

        if ($chunkIndex < $totalChunks - 1) {
            return response()->json([
                'done' => false,
                'chunk' => $chunkIndex,
            ]);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            if (! Storage::disk('local')->exists("{$tmpDir}/chunk_{$i}")) {
                return response()->json([
                    'done' => false,
                    'error' => "Chunk {$i} fehlt.",
                ], 422);
            }
        }

        $extension = pathinfo($validated['file_name'], PATHINFO_EXTENSION);
        $storedName = Str::uuid()->toString().($extension ? '.'.$extension : '');
        $finalPath = "private/drive/admins/{$share->owner_id}/shares/{$share->id}/folders/{$targetFolder->id}/{$storedName}";

        $absoluteFinalPath = Storage::disk('local')->path($finalPath);

        if (! is_dir(dirname($absoluteFinalPath))) {
            mkdir(dirname($absoluteFinalPath), 0775, true);
        }

        $output = fopen($absoluteFinalPath, 'ab');

        for ($i = 0; $i < $totalChunks; $i++) {
            $absoluteChunkPath = Storage::disk('local')->path("{$tmpDir}/chunk_{$i}");
            $input = fopen($absoluteChunkPath, 'rb');

            stream_copy_to_stream($input, $output);

            fclose($input);
        }

        fclose($output);

        Storage::disk('local')->deleteDirectory($tmpDir);

        $driveFile = DriveFile::create([
            'owner_id' => $share->owner_id,
            'folder_id' => $targetFolder->id,
            'uploaded_by' => null,
            'public_upload_key' => $this->publicUploadKey($request, $token),
            'original_name' => $validated['file_name'],
            'stored_name' => $storedName,
            'mime_type' => $validated['mime_type'] ?: 'application/octet-stream',
            'size' => $validated['file_size'],
            'disk' => 'local',
            'path' => $finalPath,
            'checksum' => hash_file('sha256', $absoluteFinalPath),
        ]);

        return response()->json([
            'done' => true,
            'file_id' => $driveFile->id,
        ]);
    }
}
