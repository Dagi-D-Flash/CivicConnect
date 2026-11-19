<?php

namespace App\Notifications;

use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $issue;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Issue $issue, string $oldStatus, string $newStatus)
    {
        $this->issue = $issue;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusMessage = $this->getStatusMessage();

        return (new MailMessage)
            ->subject('Issue Status Update: ' . $this->issue->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your issue "' . $this->issue->title . '" status has been updated.')
            ->line('Status changed from "' . ucfirst($this->oldStatus) . '" to "' . ucfirst($this->newStatus) . '"')
            ->line($statusMessage)
            ->action('View Issue', url('/issues/' . $this->issue->id))
            ->line('Thank you for your patience!');
    }

    /**
     * Get status-specific message
     */
    protected function getStatusMessage(): string
    {
        return match($this->newStatus) {
            'pending' => 'Your issue is awaiting review by our team.',
            'in_progress' => 'Great news! Work has started on your issue.',
            'resolved' => 'Your issue has been resolved. Thank you for reporting!',
            'rejected' => 'Unfortunately, your issue could not be processed at this time.',
            default => 'The status of your issue has been updated.',
        };
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'issue_id' => $this->issue->id,
            'issue_title' => $this->issue->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
