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

class TaskStartsSoonMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $daysUntilStart;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $user, int $daysUntilStart)
    {
        $this->task = $task;
        $this->user = $user;
        $this->daysUntilStart = $daysUntilStart;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->daysUntilStart === 0 
            ? '🚀 Tu tarea inicia hoy: ' . $this->task->title 
            : '⏱️ Tu tarea inicia mañana: ' . $this->task->title;

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
            markdown: 'emails.tasks.starts_soon',
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
