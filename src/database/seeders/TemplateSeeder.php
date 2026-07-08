<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcessTemplate;
use App\Models\ProcessTemplateVersion;
use App\Models\TemplateGroup;
use App\Models\TemplateTask;
use App\Models\TemplateSubtask;

class TemplateSeeder extends Seeder
{
    /**
     * Map of task title → is_deliverable for Development group.
     * 'all' means all instances of that title are deliverable.
     */
    private function isDeliverableDev(string $title): bool
    {
        $deliverable = [
            'SHA', 'Firma de promesa', 'Forma de Inversión',
            'Estudios de Mercado Terceros', 'Presentación de estudios',
            'Estudios Técnicos', 'Anteproyecto Inicial',
            'MF Factibilidad', 'Flujo Proyecto',
            'Presupuesto Clase 5', 'Presupuesto Apto Modelo + SV',
            'Catálogo de Acabados', 'Plan de Mercadeo', 'Precios de Venta',
            'Memoria Descriptiva', 'Manual de Garantías',
            'Inventario', 'Cronograma', 'Presupuesto',
            'Anotaciones Presupuesto', 'Presupuesto Clase 3',
            'Cronograma de Obra',
        ];

        $planificaciones = [
            'Planificación Arquitectura', 'Planificación Interiores',
            'Planificación Conjunto / Urba', 'Planificación Sostenibilidad',
            'Planificación Estructuras', 'Planificación Eléctricas',
            'Planificación Hidrosanitarias', 'Planificación Mecánicas',
        ];

        return in_array($title, $deliverable) || in_array($title, $planificaciones);
    }

    public function run(): void
    {
        $template = ProcessTemplate::create([
            'name' => 'Viabilidad / Compra / Desarrollo',
            'description' => 'Plantilla predefinida para proyectos de nuevos desarrollos con etapas de viabilidad, compra y desarrollo.',
            'status' => 'published',
        ]);

        $version = ProcessTemplateVersion::create([
            'process_template_id' => $template->id,
            'version_number' => 1,
            'status' => 'published',
            'notes' => 'Versión inicial con datos del Anexo A del PRD',
        ]);

        // === Full PRD Appendix A structure ===
        $groupsData = [
            // ---- Group 1: Viabilidad (gate) ----
            [
                'name' => 'Viabilidad',
                'order' => 1,
                'is_gate' => true,
                'tasks' => [
                    ['title' => 'Oferta', 'duration_days' => 7, 'is_deliverable' => true],
                    ['title' => 'Due Diligence del terreno / prop.', 'duration_days' => 20, 'is_deliverable' => true],
                    ['title' => 'Validación de cumplimiento', 'duration_days' => 10, 'is_deliverable' => true],
                    ['title' => 'Presentación de Inversión', 'duration_days' => 3, 'is_deliverable' => true],
                    ['title' => 'MF Factibilidad Inversión', 'duration_days' => 15, 'is_deliverable' => true],
                    ['title' => 'Flujo Inicial Inversión', 'duration_days' => 10, 'is_deliverable' => true],
                ],
            ],

            // ---- Group 2: Purchase (no gate) ----
            [
                'name' => 'Purchase',
                'order' => 2,
                'is_gate' => false,
                'tasks' => [
                    ['title' => 'Avalúo del Terreno', 'duration_days' => 15, 'is_deliverable' => true],
                ],
            ],

            // ---- Group 3: Development (no gate, 83 tasks) ----
            [
                'name' => 'Development',
                'order' => 3,
                'is_gate' => false,
                'tasks' => [
                    ['title' => 'SHA', 'duration_days' => 15],
                    ['title' => 'Firma de promesa', 'duration_days' => 10],
                    ['title' => 'Consulta Registro + Planos', 'duration_days' => 7],
                    ['title' => 'Forma de Inversión', 'duration_days' => 10],
                    ['title' => 'Kick Off Interno', 'duration_days' => 1],
                    ['title' => 'Estudios de Mercado Terceros', 'duration_days' => 30],
                    ['title' => 'Presentación de estudios', 'duration_days' => 3],
                    ['title' => 'Ideation Workshop', 'duration_days' => 2],
                    ['title' => 'SA del Proyecto', 'duration_days' => 7],
                    ['title' => 'RTU Proyecto', 'duration_days' => 10],
                    ['title' => 'Definición de asignación SA', 'duration_days' => 3],
                    ['title' => 'Propuesta de Marca Inicial', 'duration_days' => 15],
                    ['title' => 'Propuesta de Marca Aprobada', 'duration_days' => 7],
                    ['title' => 'Kick Off Proyecto Nuevo', 'duration_days' => 1],
                    ['title' => 'MF Factibilidad', 'duration_days' => 15],
                    ['title' => 'Especificaciones Técnicas', 'duration_days' => 10],
                    ['title' => 'Resumen de Solicitud', 'duration_days' => 5],
                    ['title' => 'Flujo Proyecto', 'duration_days' => 10],
                    ['title' => 'Concepto Inicial de Sinergia', 'duration_days' => 10],
                    ['title' => 'MF Factibilidad', 'duration_days' => 15],
                    ['title' => 'Memoria Descriptiva', 'duration_days' => 10],
                    // Task 22: Estudios Técnicos (PARENT with subtasks)
                    [
                        'title' => 'Estudios Técnicos',
                        'duration_days' => 30,
                        'subtasks' => [
                            ['title' => 'Topografía', 'duration_days' => 15],
                            ['title' => 'Geofísica y tomografía', 'duration_days' => 20],
                            ['title' => 'Estudio de suelos', 'duration_days' => 20],
                            ['title' => 'Estudio hidrogeológico', 'duration_days' => 20],
                            ['title' => 'EIA', 'duration_days' => 30],
                        ],
                    ],
                    ['title' => 'Consulta Muni', 'duration_days' => 10],
                    ['title' => 'Consulta DGAC', 'duration_days' => 10],
                    ['title' => 'Anteproyecto Inicial', 'duration_days' => 30],
                    ['title' => 'MF Factibilidad', 'duration_days' => 15],
                    ['title' => 'Flujo Proyecto', 'duration_days' => 10],
                    ['title' => 'Crédito Fiduciario', 'duration_days' => 25],
                    ['title' => 'Anteproyecto Avances', 'duration_days' => 20],
                    ['title' => 'Presupuesto Clase 5', 'duration_days' => 15],
                    ['title' => 'MF Factibilidad', 'duration_days' => 15],
                    ['title' => 'Flujo Proyecto', 'duration_days' => 10],
                    ['title' => 'Memoria Descriptiva', 'duration_days' => 10],
                    ['title' => 'Apartamento Modelo + SV', 'duration_days' => 30],
                    ['title' => 'Presupuesto Apto Modelo + SV', 'duration_days' => 10],
                    ['title' => 'Catálogo de Acabados', 'duration_days' => 15],
                    ['title' => 'Plan de Mercadeo', 'duration_days' => 20],
                    ['title' => 'Concepto Eventos', 'duration_days' => 7],
                    ['title' => 'Presupuesto Eventos', 'duration_days' => 7],
                    ['title' => 'Plan de Eventos', 'duration_days' => 10],
                    ['title' => 'Precios de Venta', 'duration_days' => 10],
                    ['title' => 'Avance Comercial', 'duration_days' => 5],
                    ['title' => 'Anteproyecto Aprobado', 'duration_days' => 20],
                    ['title' => 'Planificación Arquitectura', 'duration_days' => 30],
                    ['title' => 'Planificación Interiores', 'duration_days' => 30],
                    ['title' => 'Planificación Conjunto / Urba', 'duration_days' => 30],
                    ['title' => 'Planificación Sostenibilidad', 'duration_days' => 20],
                    ['title' => 'Material Comercial', 'duration_days' => 20],
                    ['title' => 'Renders', 'duration_days' => 20],
                    ['title' => 'Anexo de Ventas / Equipamiento', 'duration_days' => 15],
                    ['title' => 'Memoria Descriptiva', 'duration_days' => 10],
                    ['title' => 'Manual de Garantías', 'duration_days' => 15],
                    ['title' => 'Inventario', 'duration_days' => 10],
                    ['title' => 'Cronograma', 'duration_days' => 15],
                    ['title' => 'Presupuesto', 'duration_days' => 20],
                    ['title' => 'Anotaciones Presupuesto', 'duration_days' => 7],
                    ['title' => 'MF Factibilidad', 'duration_days' => 15],
                    ['title' => 'Flujo Proyecto', 'duration_days' => 10],
                    ['title' => 'Stakeholders Sale', 'duration_days' => 5],
                    ['title' => 'Open Sale AICSA', 'duration_days' => 3],
                    ['title' => 'Friends and Family', 'duration_days' => 3],
                    ['title' => 'Lanzamiento / Primera Piedra', 'duration_days' => 5],
                    ['title' => 'Planificación de Licencias', 'duration_days' => 20],
                    ['title' => 'Ingreso IFE', 'duration_days' => 5],
                    ['title' => 'Resolución IFE', 'duration_days' => 30],
                    ['title' => 'Ingreso de Licencia Construcción', 'duration_days' => 5],
                    ['title' => 'IDAEH', 'duration_days' => 30],
                    ['title' => 'Ingreso MARN', 'duration_days' => 5],
                    ['title' => 'Planificación Arquitectura', 'duration_days' => 30],
                    ['title' => 'Planificación Interiores', 'duration_days' => 30],
                    ['title' => 'Planificación Conjunto / Urba', 'duration_days' => 30],
                    ['title' => 'Planificación Estructuras', 'duration_days' => 30],
                    ['title' => 'Planificación Eléctricas', 'duration_days' => 30],
                    ['title' => 'Planificación Hidrosanitarias', 'duration_days' => 30],
                    ['title' => 'Planificación Mecánicas', 'duration_days' => 30],
                    ['title' => 'Planificación Sostenibilidad', 'duration_days' => 20],
                    ['title' => 'Certificación Pre Diseño Sostenibilidad', 'duration_days' => 20],
                    ['title' => 'Presupuesto Clase 3', 'duration_days' => 20],
                    ['title' => 'Cronograma de Obra', 'duration_days' => 15],
                    ['title' => 'PTAR', 'duration_days' => 20],
                    ['title' => 'EEGSA', 'duration_days' => 20],
                    ['title' => 'Elegibilidad FHA', 'duration_days' => 20],
                    ['title' => 'Régimen Copropiedad', 'duration_days' => 30],
                ],
            ],
        ];

        // === Create relational records AND populate template_data ===
        $versionData = [];

        foreach ($groupsData as $groupData) {
            $group = TemplateGroup::create([
                'process_template_version_id' => $version->id,
                'name' => $groupData['name'],
                'order' => $groupData['order'],
                'is_gate' => $groupData['is_gate'],
            ]);

            $groupRecord = [
                'name' => $groupData['name'],
                'order' => $groupData['order'],
                'is_gate' => $groupData['is_gate'],
                'tasks' => [],
            ];

            $taskOrder = 1;
            foreach ($groupData['tasks'] as $taskData) {
                $subtasks = $taskData['subtasks'] ?? [];
                unset($taskData['subtasks']);

                // Determine is_deliverable
                $isDeliverable = $taskData['is_deliverable']
                    ?? ($groupData['name'] === 'Development' ? $this->isDeliverableDev($taskData['title']) : false);

                $taskData['order'] = $taskOrder;
                $taskData['template_group_id'] = $group->id;
                $taskData['is_deliverable'] = $isDeliverable;

                $task = TemplateTask::create($taskData);

                $taskRecord = [
                    'title' => $taskData['title'],
                    'duration_days' => $taskData['duration_days'],
                    'order' => $taskOrder,
                    'is_required' => true,
                    'is_deliverable' => $isDeliverable,
                    'subtasks' => [],
                ];

                $subOrder = 1;
                foreach ($subtasks as $subData) {
                    $subData['template_task_id'] = $task->id;
                    $subData['order'] = $subOrder;
                    TemplateSubtask::create($subData);

                    $taskRecord['subtasks'][] = [
                        'title' => $subData['title'],
                        'duration_days' => $subData['duration_days'],
                        'order' => $subOrder,
                    ];

                    $subOrder++;
                }

                $groupRecord['tasks'][] = $taskRecord;
                $taskOrder++;
            }

            $versionData[] = $groupRecord;
        }

        // Save the full nested structure as template_data JSON
        $version->template_data = ['groups' => $versionData];
        $version->save();
    }
}
