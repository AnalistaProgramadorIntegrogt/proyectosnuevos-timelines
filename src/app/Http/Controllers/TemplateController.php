<?php

namespace App\Http\Controllers;

use App\Models\ProcessTemplate;
use App\Models\ProcessTemplateVersion;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index()
    {
        return view('templates.index');
    }

    /**
     * Show the template editor for the given template.
     */
    public function edit(ProcessTemplate $template, ?Request $request = null)
    {
        $versionId = request()->query('version');
        $version = null;

        if ($versionId) {
            $version = ProcessTemplateVersion::where('process_template_id', $template->id)
                ->find($versionId);
        }

        return view('templates.edit', [
            'template' => $template,
            'version' => $version,
        ]);
    }

    /**
     * Show version history for the given template.
     */
    public function versions(ProcessTemplate $template)
    {
        return view('templates.versions', [
            'template' => $template,
        ]);
    }
}
