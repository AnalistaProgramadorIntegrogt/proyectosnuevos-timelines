<?php

namespace App\Http\Livewire\Documents;

use App\Models\DocumentFile;
use App\Models\DocumentFileVersion;
use App\Models\DocumentFolder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentExplorer extends Component
{
    use WithFileUploads;

    public $currentFolderId = null;

    // Modals state
    public $showCreateFolderModal = false;
    public $showUploadFileModal = false;
    public $showVersionsModal = false;

    // Form inputs
    public $newFolderName = '';
    public $newFolderDescription = '';
    
    public $uploadFile = null;
    public $uploadFileName = '';
    public $uploadFileDescription = '';
    
    // Uploading a new version to existing document
    public $targetDocumentId = null;
    public $newVersionNote = '';

    // Data for versions modal
    public $viewingDocument = null;

    protected $rules = [
        'newFolderName' => 'required|string|max:255',
    ];

    public function mount($folderId = null)
    {
        $this->currentFolderId = $folderId;
    }

    public function getBreadcrumbsProperty()
    {
        $breadcrumbs = [];
        $folder = $this->currentFolderId ? DocumentFolder::find($this->currentFolderId) : null;
        
        while ($folder) {
            array_unshift($breadcrumbs, $folder);
            $folder = $folder->parent;
        }

        return $breadcrumbs;
    }

    public function getCurrentFolderProperty()
    {
        return $this->currentFolderId ? DocumentFolder::find($this->currentFolderId) : null;
    }

    public function getFoldersProperty()
    {
        return DocumentFolder::where('parent_id', $this->currentFolderId)
            ->orderBy('name')
            ->get();
    }

    public function getDocumentsProperty()
    {
        return DocumentFile::with('latestVersion')
            ->where('document_folder_id', $this->currentFolderId)
            ->orderBy('name')
            ->get();
    }

    public function navigateTo($folderId)
    {
        $this->currentFolderId = $folderId;
    }

    public function createFolder()
    {
        Gate::authorize('manage-repository');

        $this->validate([
            'newFolderName' => 'required|string|max:255',
            'newFolderDescription' => 'nullable|string',
        ]);

        DocumentFolder::create([
            'name' => $this->newFolderName,
            'description' => $this->newFolderDescription,
            'parent_id' => $this->currentFolderId,
            'created_by' => Auth::id(),
        ]);

        $this->showCreateFolderModal = false;
        $this->reset(['newFolderName', 'newFolderDescription']);
        session()->flash('flash.banner', 'Carpeta creada exitosamente.');
    }

    public function openUploadModal()
    {
        Gate::authorize('manage-repository');
        $this->reset(['uploadFile', 'uploadFileName', 'uploadFileDescription', 'targetDocumentId', 'newVersionNote']);
        $this->showUploadFileModal = true;
    }

    public function openNewVersionModal($documentId)
    {
        Gate::authorize('manage-repository');
        $this->reset(['uploadFile', 'newVersionNote']);
        $this->targetDocumentId = $documentId;
        
        $document = DocumentFile::findOrFail($documentId);
        $this->uploadFileName = $document->name;
        
        $this->showUploadFileModal = true;
    }

    public function uploadDocument()
    {
        Gate::authorize('manage-repository');

        $this->validate([
            'uploadFile' => 'required|file|max:102400', // 100MB
            'uploadFileName' => 'required|string|max:255',
        ]);

        $originalName = $this->uploadFile->getClientOriginalName();
        $mimeType = $this->uploadFile->getMimeType();
        $size = $this->uploadFile->getSize();

        // Store file
        $path = $this->uploadFile->store('documents', 'local');

        // Check if updating existing document or creating new
        if ($this->targetDocumentId) {
            $document = DocumentFile::findOrFail($this->targetDocumentId);
            $latestVersion = $document->latestVersion ? $document->latestVersion->version_number : 0;
            
            DocumentFileVersion::create([
                'document_file_id' => $document->id,
                'version_number' => $latestVersion + 1,
                'file_path' => $path,
                'original_filename' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_by' => Auth::id(),
                'upload_note' => $this->newVersionNote,
            ]);

            session()->flash('flash.banner', 'Nueva versión subida exitosamente.');
        } else {
            $document = DocumentFile::create([
                'document_folder_id' => $this->currentFolderId,
                'name' => $this->uploadFileName,
                'description' => $this->uploadFileDescription,
                'created_by' => Auth::id(),
            ]);

            DocumentFileVersion::create([
                'document_file_id' => $document->id,
                'version_number' => 1,
                'file_path' => $path,
                'original_filename' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_by' => Auth::id(),
                'upload_note' => 'Versión inicial',
            ]);

            session()->flash('flash.banner', 'Documento subido exitosamente.');
        }

        $this->showUploadFileModal = false;
        $this->reset(['uploadFile', 'uploadFileName', 'uploadFileDescription', 'targetDocumentId', 'newVersionNote']);
    }

    public function deleteFolder($id)
    {
        Gate::authorize('manage-repository');
        $folder = DocumentFolder::findOrFail($id);
        $folder->delete(); // Automatically cascades if setup or handles recursively via observer/DB schema
        session()->flash('flash.banner', 'Carpeta eliminada.');
    }

    public function deleteDocument($id)
    {
        Gate::authorize('manage-repository');
        $document = DocumentFile::findOrFail($id);
        
        // Delete all physical files
        foreach ($document->versions as $version) {
            if (Storage::disk('local')->exists($version->file_path)) {
                Storage::disk('local')->delete($version->file_path);
            }
        }
        
        $document->delete();
        session()->flash('flash.banner', 'Documento eliminado.');
    }

    public function viewVersions($documentId)
    {
        $this->viewingDocument = DocumentFile::with(['versions' => function ($query) {
            $query->orderBy('version_number', 'desc');
        }, 'versions.uploader'])->findOrFail($documentId);
        
        $this->showVersionsModal = true;
    }

    public function downloadVersion($versionId)
    {
        $version = DocumentFileVersion::findOrFail($versionId);
        
        if (!Storage::disk('local')->exists($version->file_path)) {
            session()->flash('flash.banner', 'El archivo físico no se encuentra en el servidor.');
            session()->flash('flash.bannerStyle', 'danger');
            return null;
        }

        return Storage::disk('local')->download($version->file_path, $version->original_filename);
    }

    public function render()
    {
        return view('livewire.documents.document-explorer', [
            'folders' => $this->folders,
            'documents' => $this->documents,
            'breadcrumbs' => $this->breadcrumbs,
            'currentFolder' => $this->currentFolder,
            'canManage' => Gate::allows('manage-repository'),
        ])->layout('layouts.app');
    }
}
