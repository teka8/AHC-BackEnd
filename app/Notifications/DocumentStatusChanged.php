<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $oldStatus;
    public $newStatus;
    public $action;
    public $changerName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, $oldStatus, $newStatus, $action, $changerName)
    {
        $this->document = $document;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->action = $action;
        $this->changerName = $changerName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast']; // Add 'mail' if you want email notifications too
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action' => $this->action,
            'changer_name' => $this->changerName,
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'url' => route('admin.document.edit', $this->document->id),
        ];
    }

    /**
     * Get the notification message.
     */
    private function getMessage(): string
    {
        $messages = [
            'send_for_review' => "Document '{$this->document->title}' has been sent for review by {$this->changerName}",
            'approve' => "Document '{$this->document->title}' has been approved by {$this->changerName}",
            'reject' => "Document '{$this->document->title}' requires changes (rejected by {$this->changerName})",
            'publish' => "Document '{$this->document->title}' has been published by {$this->changerName}",
            'unpublish' => "Document '{$this->document->title}' has been unpublished by {$this->changerName}",
            'archive' => "Document '{$this->document->title}' has been archived by {$this->changerName}",
            'restore' => "Document '{$this->document->title}' has been restored by {$this->changerName}",
            'send_back' => "Document '{$this->document->title}' has been sent back for review by {$this->changerName}",
        ];

        return $messages[$this->action] ?? "Document '{$this->document->title}' status changed from {$this->oldStatus} to {$this->newStatus} by {$this->changerName}";
    }

    /**
     * Get the notification icon.
     */
    private function getIcon(): string
    {
        $icons = [
            'send_for_review' => 'lucide:send',
            'approve' => 'lucide:check-circle',
            'reject' => 'lucide:arrow-left',
            'publish' => 'lucide:globe',
            'unpublish' => 'lucide:eye-off',
            'archive' => 'lucide:archive',
            'restore' => 'lucide:refresh-cw',
            'send_back' => 'lucide:arrow-left',
        ];

        return $icons[$this->action] ?? 'lucide:file-text';
    }

    /**
     * Get the notification color.
     */
    private function getColor(): string
    {
        $colors = [
            'send_for_review' => 'yellow',
            'approve' => 'green',
            'reject' => 'red',
            'publish' => 'blue',
            'unpublish' => 'gray',
            'archive' => 'orange',
            'restore' => 'blue',
            'send_back' => 'yellow',
        ];

        return $colors[$this->action] ?? 'gray';
    }
}