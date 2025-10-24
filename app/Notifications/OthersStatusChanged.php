<?php

namespace App\Notifications;

use App\Models\Others;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OthersStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $resource;
    public $oldStatus;
    public $newStatus;
    public $action;
    public $changerName;

    public function __construct(Others $resource, $oldStatus, $newStatus, $action, $changerName)
    {
        $this->resource = $resource;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->action = $action;
        $this->changerName = $changerName;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'others_id' => $this->resource->id,
            'title' => $this->resource->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action' => $this->action,
            'changer_name' => $this->changerName,
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'url' => route('admin.others.edit', $this->resource->id),
        ];
    }

    private function getMessage(): string
    {
        $title = $this->resource->title;
        $name = $this->changerName;
        $messages = [
            'send_for_review' => "File '$title' has been sent for review by $name",
            'approve' => "File '$title' has been approved by $name",
            'reject' => "File '$title' requires changes (rejected by $name)",
            'publish' => "File '$title' has been published by $name",
            'unpublish' => "File '$title' has been unpublished by $name",
            'archive' => "File '$title' has been archived by $name",
            'restore' => "File '$title' has been restored by $name",
            'send_back' => "File '$title' has been sent back for review by $name",
        ];
        return $messages[$this->action] ?? "File '$title' status changed from {$this->oldStatus} to {$this->newStatus} by $name";
    }

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
