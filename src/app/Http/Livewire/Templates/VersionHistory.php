<?php

namespace App\Http\Livewire\Templates;

use App\Models\ProcessTemplate;
use App\Models\ProcessTemplateVersion;
use Livewire\Component;

class VersionHistory extends Component
{
    public ProcessTemplate $template;
    public $selectedVersionId = null;

    public function mount(ProcessTemplate $template)
    {
        $this->template = $template;
    }

    /**
     * Rollback: create a new draft version based on the selected old version's template_data.
     */
    public function rollback($versionId)
    {
        $sourceVersion = ProcessTemplateVersion::findOrFail($versionId);
        $nextNumber = $this->template->versions()->max('version_number') + 1;

        $newVersion = $this->template->versions()->create([
            'version_number' => $nextNumber,
            'template_data' => $sourceVersion->template_data,
            'status' => 'draft',
            'notes' => 'Rollback desde v' . $sourceVersion->version_number . ': ' . ($sourceVersion->notes ?? 'Sin notas'),
        ]);

        session()->flash('flash.banner', 'Se ha creado la versión ' . $nextNumber . ' basada en v' . $sourceVersion->version_number . ' como borrador.');

        return redirect()->route('templates.edit', ['template' => $this->template, 'version' => $newVersion->id]);
    }

    public function render()
    {
        $versions = $this->template->versions()
            ->orderBy('version_number', 'desc')
            ->get();

        return view('livewire.templates.version-history', [
            'versions' => $versions,
        ]);
    }
}
