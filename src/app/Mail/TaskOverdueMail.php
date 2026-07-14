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

class TaskOverdueMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $daysOverdue;
    public $hasBossCopied;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, int $daysOverdue, bool $hasBossCopied)
    {
        $this->task = $task;
        $this->user = $user;
        $this->daysOverdue = $daysOverdue;
        $this->hasBossCopied = $hasBossCopied;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '🔴 URGENTE: Tarea vencida por ' . $this->daysOverdue . ' día(s) - ' . $this->task->title;

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
            markdown: 'emails.tasks.overdue',
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
