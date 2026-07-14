<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDeadlineApproachingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, int $daysLeft)
    {
        $this->task = $task;
        $this->user = $user;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '⚠️ Recordatorio: Entrega próxima para la tarea "' . $this->task->title . '"';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.deadline_approaching',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
