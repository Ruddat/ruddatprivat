<?php

namespace App\Livewire\Backend\Drive;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\DriveShare;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileManager extends Component
{
    use WithFileUploads;

    public ?int $currentFolderId = null;
    public string $folderName = '';
    public $upload;
    public string $shareName = '';
    public bool $shareCanUpload = true;
    public bool $shareCanDownload = true;

    protected function rules(): array
    {
        return [
            'folderName' => ['nullable', 'string', 'max:120'],
            'upload' => ['nullable', 'file', 'max:512000'],
            'shareName' => ['nullable', 'string', 'max:120'],
        ];
    }

    private function adminId(): int
    {
        return (int) auth('admin')->id();
    }

    public function getCurrentFolderProperty(): ?DriveFolder
    {
        if (! $this->currentFolderId) {
            return null;
        }

        return DriveFolder::query()
            ->where('owner_id', $this->adminId())
            ->find($this->currentFolderId);
    }

    public function openFolder(?int $folderId = null): void
    {
        if ($folderId) {
            DriveFolder::query()
                ->where('owner_id', $this->adminId())
                ->findOrFail($folderId);
        }

        $this->currentFolderId = $folderId;
    }

    public function createFolder(): void
    {
        $this->validateOnly('folderName');

        $name = trim($this->folderName);

        if ($name === '') {
            return;
        }

        DriveFolder::create([
            'owner_id' => $this->adminId(),
            'parent_id' => $this->currentFolderId,
            'name' => $name,
        ]);

        $this->folderName = '';
        session()->flash('success', 'Ordner wurde erstellt.');
    }

    public function saveUpload(): void
    {
        $this->validateOnly('upload');

        if (! $this->upload) {
            return;
        }

        $ownerId = $this->adminId();
        $storedName = Str::uuid()->toString().'.'.$this->upload->getClientOriginalExtension();
        $folderPart = $this->currentFolderId ?: 'root';
        $path = "private/drive/admins/{$ownerId}/folders/{$folderPart}/{$storedName}";

        Storage::disk('local')->put($path, file_get_contents($this->upload->getRealPath()));

        DriveFile::create([
            'owner_id' => $ownerId,
            'folder_id' => $this->currentFolderId,
            'uploaded_by' => $ownerId,
            'original_name' => $this->upload->getClientOriginalName(),
            'stored_name' => $storedName,
            'mime_type' => $this->upload->getMimeType(),
            'size' => $this->upload->getSize(),
            'disk' => 'local',
            'path' => $path,
            'checksum' => hash_file('sha256', $this->upload->getRealPath()),
        ]);

        $this->reset('upload');
        session()->flash('success', 'Datei wurde hochgeladen.');
    }

    public function deleteFile(int $fileId): void
    {
        $file = DriveFile::query()
            ->where('owner_id', $this->adminId())
            ->findOrFail($fileId);

        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }

        $file->delete();
        session()->flash('success', 'Datei wurde gelöscht.');
    }

    public function deleteFolder(int $folderId): void
    {
        $folder = DriveFolder::query()
            ->where('owner_id', $this->adminId())
            ->withCount(['files', 'children'])
            ->findOrFail($folderId);

        if ($folder->files_count > 0 || $folder->children_count > 0) {
            session()->flash('error', 'Ordner ist nicht leer. Erst Dateien/Unterordner löschen.');
            return;
        }

        $folder->delete();
        session()->flash('success', 'Ordner wurde gelöscht.');
    }

    public function createShare(): void
    {
        $this->validateOnly('shareName');

        if (! $this->currentFolderId) {
            session()->flash('error', 'Bitte erst einen Ordner öffnen. Freigaben werden auf Ordner gesetzt.');
            return;
        }

        $folder = DriveFolder::query()
            ->where('owner_id', $this->adminId())
            ->findOrFail($this->currentFolderId);

        $share = DriveShare::create([
            'owner_id' => $this->adminId(),
            'folder_id' => $folder->id,
            'name' => trim($this->shareName) !== '' ? trim($this->shareName) : $folder->name,
            'can_view' => true,
            'can_download' => $this->shareCanDownload,
            'can_upload' => $this->shareCanUpload,
            'can_delete' => false,
            'is_active' => true,
        ]);

        $this->shareName = '';
        session()->flash('success', 'Freigabe erstellt: '.route('drive.share.show', $share->token));
    }

    public function render()
    {
        $folders = DriveFolder::query()
            ->where('owner_id', $this->adminId())
            ->where('parent_id', $this->currentFolderId)
            ->orderBy('name')
            ->get();

        $files = DriveFile::query()
            ->where('owner_id', $this->adminId())
            ->where('folder_id', $this->currentFolderId)
            ->latest()
            ->get();

        $shares = DriveShare::query()
            ->where('owner_id', $this->adminId())
            ->latest()
            ->get();

        return view('livewire.backend.drive.file-manager', [
            'folders' => $folders,
            'files' => $files,
            'shares' => $shares,
            'currentFolder' => $this->currentFolder,
        ])->extends('backend.layouts.backend');
    }
}
