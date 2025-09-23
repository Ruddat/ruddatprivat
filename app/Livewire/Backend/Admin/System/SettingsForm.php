<?php

namespace App\Livewire\Backend\Admin\System;

use Livewire\Component;
use App\Models\Setting;

class SettingsForm extends Component
{
    public array $groups = [];      // ["company","limits", ...]
    public ?string $activeGroup = null;
    public $items = [];             // Collection der Settings für aktive Gruppe
    public array $values = [];      // [id => value]

    public function mount(): void
    {
        $this->groups = Setting::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();

        $this->activeGroup = $this->groups[0] ?? null;

        if ($this->activeGroup) {
            $this->loadGroup($this->activeGroup);
        }
    }

    public function switchGroup(string $group): void
    {
        $this->activeGroup = $group;
        $this->loadGroup($group);
    }

    protected function loadGroup(string $group): void
    {
        $this->items = Setting::where('group', $group)
            ->orderBy('key')
            ->get();

        $this->values = [];
        foreach ($this->items as $item) {
            // Werte in das Binding-Array spiegeln
            $this->values[$item->id] = $item->value;
        }
    }

public function save(): void
{
    foreach ($this->values as $id => $val) {
        $item = Setting::find($id);

        if (!$item) {
            continue;
        }

        // Typ-Casts
        if ($item->type === 'boolean') {
            $val = $val ? '1' : '0';
        } elseif ($item->type === 'number') {
            $val = is_numeric($val) ? (string)$val : '0';
        } elseif ($item->type === 'json') {
            json_decode((string)$val);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('values.'.$id, 'Ungültiges JSON.');
                continue;
            }
        }

        $item->update(['value' => $val]);
    }

    session()->flash('success', 'Einstellungen gespeichert ✅');

    // neu laden, um aktuelle Werte wieder im Formular zu sehen
    $this->loadGroup($this->activeGroup);
}

    public function render()
    {
        return view('livewire.backend.admin.system.settings-form')
            ->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
