<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $issue;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, Issue $issue)
    {
        $this->comment = $comment;
        $this->issue = $issue;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment on Your Issue')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->comment->user->name . ' commented on your issue: "' . $this->issue->title . '"')
            ->line('Comment: "' . $this->comment->content . '"')
            ->action('View Issue', url('/issues/' . $this->issue->id))
            ->line('Thank you for using CivicConnect!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'issue_id' => $this->issue->id,
            'issue_title' => $this->issue->title,
            'commenter_name' => $this->comment->user->name,
            'comment_content' => $this->comment->content,
        ];
    }
}
