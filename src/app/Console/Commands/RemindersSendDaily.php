<?php

namespace App\Console\Commands;

use App\Mail\TaskDeadlineApproachingMail;
use App\Mail\TaskOverdueMail;
use App\Mail\TaskStartsSoonMail;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindersSendDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily email reminders for tasks (starting soon, deadline approaching, overdue)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily task reminders...');
        
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // 1. Find active tasks (not finished, in active project)
        $activeTasks = Task::with(['responsibles.boss', 'projectGroup.project'])
            ->whereNotIn('status', ['entregado', 'aprobado', 'rechazado'])
            ->whereHas('projectGroup.project', function ($q) {
                $q->where('archived', false)
                  ->whereNotIn('lifecycle_status', ['culminado', 'rechazado']);
            })
            ->get();

        $countStartsSoon = 0;
        $countDeadline = 0;
        $countOverdue = 0;

        foreach ($activeTasks as $task) {
            // Skip tasks with no assignees
            if ($task->responsibles->isEmpty()) {
                continue;
            }

            // A. TASK STARTS SOON (Today or Tomorrow)
            if ($task->calculated_start_date) {
                $startDate = $task->calculated_start_date->startOfDay();
                if ($startDate->equalTo($today)) {
                    $this->sendStartsSoonMails($task, 0);
                    $countStartsSoon++;
                } elseif ($startDate->equalTo($tomorrow)) {
                    $this->sendStartsSoonMails($task, 1);
                    $countStartsSoon++;
                }
            }

            // B. DEADLINE APPROACHING (< 3 days left)
            if ($task->calculated_end_date) {
                $endDate = $task->calculated_end_date->startOfDay();
                if ($endDate->isAfter($today) && $today->diffInDays($endDate) <= 3) {
                    $daysLeft = $today->diffInDays($endDate);
                    $this->sendDeadlineApproachingMails($task, $daysLeft);
                    $countDeadline++;
                }
                
                // C. OVERDUE
                if ($endDate->isBefore($today)) {
                    $daysOverdue = $today->diffInDays($endDate);
                    $this->sendOverdueMails($task, $daysOverdue);
                    $countOverdue++;
                }
            }
        }

        $this->info("Reminders sent:");
        $this->info("- Starts soon: {$countStartsSoon}");
        $this->info("- Deadline approaching: {$countDeadline}");
        $this->info("- Overdue: {$countOverdue}");
        
        return Command::SUCCESS;
    }

    private function sendStartsSoonMails(Task $task, int $daysUntilStart)
    {
        foreach ($task->responsibles as $user) {
            Mail::to($user->email)->send(new TaskStartsSoonMail($task, $user, $daysUntilStart));
        }
    }

    private function sendDeadlineApproachingMails(Task $task, int $daysLeft)
    {
        foreach ($task->responsibles as $user) {
            Mail::to($user->email)->send(new TaskDeadlineApproachingMail($task, $user, $daysLeft));
        }
    }

    private function sendOverdueMails(Task $task, int $daysOverdue)
    {
        foreach ($task->responsibles as $user) {
            $mail = new TaskOverdueMail($task, $user, $daysOverdue, $user->boss_id !== null);
            
            $pendingMail = Mail::to($user->email);
            if ($user->boss && $user->boss->email) {
                $pendingMail->cc($user->boss->email);
            }
            
            $pendingMail->send($mail);
        }
    }
}
