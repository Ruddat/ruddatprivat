<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\DriveShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DriveShareController extends Controller
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

    private function shareCanAccessFile(DriveShare $share, DriveFile $file): bool
    {
        if ((int) $file->folder_id === (int) $share->folder_id) {
            return true;
        }

        if (! $file->folder_id) {
            return false;
        }

        $folder = DriveFolder::query()->find($file->folder_id);

        if (! $folder) {
            return false;
        }

        return $this->shareCanAccessFolder($share, $folder);
    }

    public function show(Request $request, string $token)
    {
        $share = DriveShare::query()
            ->with('folder')
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_view, 403);

        $folder = $share->folder;

        $folders = $folder?->children()
            ->orderBy('name')
            ->get() ?? collect();

        $files = $folder?->files()
            ->latest()
            ->get() ?? collect();

        $publicUploadKey = $this->publicUploadKey($request, $token);

        return view('drive.share', compact(
            'share',
            'folder',
            'folders',
            'files',
            'publicUploadKey'
        ));
    }

    public function folder(Request $request, string $token, DriveFolder $folder)
    {
        $share = DriveShare::query()
            ->with('folder')
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_view, 403);
        abort_unless($this->shareCanAccessFolder($share, $folder), 403);

        $folders = $folder->children()
            ->orderBy('name')
            ->get();

        $files = $folder->files()
            ->latest()
            ->get();

        $publicUploadKey = $this->publicUploadKey($request, $token);

        return view('drive.share', compact(
            'share',
            'folder',
            'folders',
            'files',
            'publicUploadKey'
        ));
    }

    public function upload(Request $request, string $token)
    {
        $share = DriveShare::query()
            ->with('folder')
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_upload, 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:512000'],
            'folder_id' => ['nullable', 'integer', 'exists:drive_folders,id'],
        ]);

        $targetFolderId = $validated['folder_id'] ?? $share->folder_id;

        $targetFolder = DriveFolder::query()->find($targetFolderId);

        abort_unless($targetFolder && $this->shareCanAccessFolder($share, $targetFolder), 403);

        $uploadedFile = $validated['file'];
        $ownerId = $share->owner_id;
        $storedName = Str::uuid()->toString().'.'.$uploadedFile->getClientOriginalExtension();
        $path = "private/drive/admins/{$ownerId}/shares/{$share->id}/folders/{$targetFolder->id}/{$storedName}";

        Storage::disk('local')->put($path, file_get_contents($uploadedFile->getRealPath()));

        DriveFile::create([
            'owner_id' => $ownerId,
            'folder_id' => $targetFolder->id,
            'uploaded_by' => null,
            'public_upload_key' => $this->publicUploadKey($request, $token),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_name' => $storedName,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'disk' => 'local',
            'path' => $path,
            'checksum' => hash_file('sha256', $uploadedFile->getRealPath()),
        ]);

        return back()->with('success', 'Datei wurde hochgeladen.');
    }

    public function download(string $token, DriveFile $file)
    {
        $share = DriveShare::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_download, 403);
        abort_unless($this->shareCanAccessFile($share, $file), 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function stream(string $token, DriveFile $file)
    {
        $share = DriveShare::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_view, 403);
        abort_unless($this->shareCanAccessFile($share, $file), 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return response()->file(Storage::disk($file->disk)->path($file->path), [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    public function destroy(Request $request, string $token, DriveFile $file)
    {
        $share = DriveShare::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($share->isUsable() && $share->can_delete, 403);
        abort_unless($this->shareCanAccessFile($share, $file), 403);
        abort_unless(
            filled($file->public_upload_key)
            && hash_equals((string) $file->public_upload_key, $this->publicUploadKey($request, $token)),
            403
        );

        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }

        $file->delete();

        return back()->with('success', 'Eigener Upload wurde gelöscht.');
    }
}
