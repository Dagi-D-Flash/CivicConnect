<?php

namespace App\Notifications;

use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueUpvoted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $issue;
    protected $votesCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Issue $issue, int $votesCount)
    {
        $this->issue = $issue;
        $this->votesCount = $votesCount;
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
            ->subject('Your Issue Reached ' . $this->votesCount . ' Votes!')
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('Your issue "' . $this->issue->title . '" has reached ' . $this->votesCount . ' votes!')
            ->line('The more votes your issue gets, the higher priority it becomes.')
            ->action('View Issue', url('/issues/' . $this->issue->id))
            ->line('Thank you for making your community better!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'issue_id' => $this->issue->id,
            'issue_title' => $this->issue->title,
            'votes_count' => $this->votesCount,
        ];
    }
}
