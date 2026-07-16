<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::with('groups.tasks')->first();
echo "Project: " . $project->name . "\n";
foreach ($project->groups->sortBy('order') as $group) {
    echo "Group: " . $group->name . " (Status: " . $group->status . ")\n";
    foreach ($group->tasks->sortBy('order') as $task) {
        $calc = $task->calculated_end_date ? $task->calculated_end_date->format('Y-m-d') : 'NULL';
        $base = $task->baseline_end_date ? $task->baseline_end_date->format('Y-m-d') : 'NULL';
        $delayed = ($task->calculated_end_date && $task->baseline_end_date && $task->calculated_end_date->isAfter($task->baseline_end_date)) ? 'YES' : 'NO';
        echo "  - Task: " . $task->title . " | Base: $base | Calc: $calc | Delayed: $delayed\n";
    }
}
