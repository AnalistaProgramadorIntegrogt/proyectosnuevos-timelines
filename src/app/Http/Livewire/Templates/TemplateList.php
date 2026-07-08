<?php

namespace App\Http\Livewire\Templates;

use App\Models\ProcessTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class TemplateList extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deactivate($templateId)
    {
        $template = ProcessTemplate::findOrFail($templateId);
        $template->update(['status' => 'archived']);

        session()->flash('flash.banner', 'Plantilla desactivada exitosamente.');
    }

    public function activate($templateId)
    {
        $template = ProcessTemplate::findOrFail($templateId);
        $template->update(['status' => 'draft']);

        session()->flash('flash.banner', 'Plantilla reactivada exitosamente.');
    }

    public function render()
    {
        $templates = ProcessTemplate::query()
            ->with('versions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('livewire.templates.template-list', [
            'templates' => $templates,
        ]);
    }
}
