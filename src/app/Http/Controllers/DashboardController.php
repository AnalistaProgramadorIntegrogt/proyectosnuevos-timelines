<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard with project stats.
     */
    public function index()
    {
        $user = auth()->user();

        // Get IDs of projects the user can see (owned or member)
        $visibleProjectIds = Project::where('owner_id', $user->id)
            ->orWhereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->pluck('id');

        // Total de proyectos (non-archived)
        $totalProyectos = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->count();

        // Proyectos "en evaluación" (gate no aprobado)
        $enEvaluacion = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where('lifecycle_status', 'en_proceso')
            ->whereHas('groups', function ($q) {
                $q->where('is_gate', true)
                  ->where('status', '!=', 'completed_viable');
            })
            ->count();

        // Proyectos "en proceso" (gate aprobado o sin gate)
        $enProceso = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where('lifecycle_status', 'en_proceso')
            ->where(function ($query) {
                $query->whereHas('groups', function ($q) {
                    $q->where('is_gate', true)
                      ->where('status', 'completed_viable');
                })->orWhereDoesntHave('groups', function ($q) {
                    $q->where('is_gate', true);
                });
            })
            ->count();

        // Proyectos "atrasados" (end_date < now AND status NOT in entregado/aprobado/rechazado)
        $atrasados = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where('lifecycle_status', '!=', 'entregado')
            ->where('lifecycle_status', '!=', 'aprobado')
            ->where('lifecycle_status', '!=', 'rechazado')
            ->where('lifecycle_status', '!=', 'culminado')
            ->whereHas('groups.tasks', function ($q) {
                $q->where('calculated_end_date', '<', now())
                    ->where('status', '!=', 'entregado')
                    ->where('status', '!=', 'aprobado')
                    ->where('status', '!=', 'rechazado');
            })
            ->count();

        // Proyectos "entregados" + "aprobados" (combined count)
        $entregadosAprobados = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where(function ($q) {
                $q->where('lifecycle_status', 'entregado')
                    ->orWhere('lifecycle_status', 'aprobado');
            })
            ->count();

        // Proyectos "rechazados"
        $rechazados = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where('lifecycle_status', 'rechazado')
            ->count();

        // Proyectos "culminados"
        $culminados = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->where('lifecycle_status', 'culminado')
            ->count();

        // Proyectos asignados al current user (via project_members)
        $misProyectos = Project::whereIn('id', $visibleProjectIds)
            ->where('archived', false)
            ->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        // Last 10 audit events for projects the user belongs to
        $recentActivity = AuditEvent::with(['user', 'project'])
            ->whereIn('project_id', $visibleProjectIds)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalProyectos',
            'enEvaluacion',
            'enProceso',
            'atrasados',
            'entregadosAprobados',
            'rechazados',
            'culminados',
            'misProyectos',
            'recentActivity'
        ));
    }
}
