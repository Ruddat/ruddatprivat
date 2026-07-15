<?php

namespace App\Livewire\Backend\Drive;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\DriveShare;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FileManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int