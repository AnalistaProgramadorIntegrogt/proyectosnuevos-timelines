<?php

namespace App\Console\Commands;

use App\Models\Subtask;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateTaskStatuses extends Command
{
    protected $signature = 'tasks:update-statuses';

    protected $description = 'Auto-update task and subtask statuses based on dates and deliverable state';

    public function handle(): int
    {
        $now = Carbon::now();

        $this->info('Updating task statuses...');
        $taskUpdates = $this->updateTasks($now);

        $this->info('Updating subtask statuses...');
        $subtaskUpdates = $this->updateSubtasks($now);

        $this->info("Tasks updated: {$taskUpdates}");
        $this->info("Subtasks updated: {$subtaskUpdates}");

        return Command::SUCCESS;
    }

    protected function updateTasks(Carbon $now): int
    {
        $updated = 0;

        // 1. Pending -> en_proceso: start date has arrived
        $tasks = Task::where('status', 'pending')
            ->where('calculated_start_date', '<=', $now->toDateString())
            ->get();

        foreach ($tasks as $task) {
            $task->status = 'en_proceso';
            $task->save();
            $updated++;
        }

        // 2. Overdue tasks: past end date and still in an active/unresolved state
        //    (only 'pending' or 'en_proceso' can become overdue; already-submitted
        //     or already-terminal tasks are left untouched)
        $tasks = Task::where('calculated_end_date', '<', $now->toDateString())
            ->whereIn('status', ['pending', 'en_proceso'])
            ->get();

        foreach ($tasks as $task) {
            if ($task->status === 'pending') {
                $task->status = 'en_proceso';
            }

            // Deliverable tasks with uploaded files -> entregado
            if ($task->is_deliverable && $task->deliverableVersions()->count() > 0) {
                $task->status = 'entregado';
            } else {
                $task->status = 'atrasado';
            }

            $task->save();
            $updated++;
        }

        // 3. Deliverable tasks: en_proceso -> entregado if files have been uploaded
        //    (catches tasks whose start date arrived but haven't gone overdue yet)
        $tasks = Task::where('is_deliverable', true)
            ->where('status', 'en_proceso')
            ->whereHas('deliverableVersions')
            ->get();

        foreach ($tasks as $task) {
            $task->status = 'entregado';
            $task->save();
            $updated++;
        }

        return $updated;
    }

    protected function updateSubtasks(Carbon $now): int
    {
        $updated = 0;

        // Subtasks past end date and still active
        $subtasks = Subtask::where('end_date', '<', $now->toDateString())
            ->whereIn('status', ['pending', 'en_proceso', 'entregado'])
            ->get();

        foreach ($subtasks as $subtask) {
            $subtask->status = 'atrasado';
            $subtask->save();
            $updated++;
        }

        return $updated;
    }
}
