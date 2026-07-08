<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\DeliverableVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeliverableController extends Controller
{
    /**
     * Download a deliverable version file.
     */
    public function download(DeliverableVersion $version)
    {
        $user = Auth::user();

        // Verify file exists
        if (!Storage::disk('local')->exists($version->storage_key)) {
            abort(404, 'El archivo no se encuentra en el servidor.');
        }

        // Audit log
        AuditEvent::create([
            'user_id' => $user->id,
            'project_id' => $version->task->projectGroup->project_id,
            'task_id' => $version->task_id,
            'action' => 'deliverable_downloaded',
            'entity_type' => 'deliverable_version',
            'entity_id' => (string) $version->id,
            'after_data' => [
                'version_number' => $version->version_number,
                'filename' => $version->original_filename,
            ],
            'reason' => 'Descarga del entregable v' . $version->version_number . ' de la tarea: ' . $version->task->title,
            'ip_address' => request()->ip(),
        ]);

        return Storage::disk('local')->download(
            $version->storage_key,
            $version->original_filename
        );
    }
}
