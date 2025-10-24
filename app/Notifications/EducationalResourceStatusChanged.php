<?php

namespace App\Notifications;

use App\Models\EducationalResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EducationalResourceStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $resource;
    public $oldStatus;
    public $newStatus;
    public $action;
    public $changerName;

    public function __construct(EducationalResource $resource, $oldStatus, $newStatus, $action, $changerName)
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
            'resource_id' => $this->resource->id,
            'resource_title' => $this->resource->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action' => $this->action,
            'changer_name' => $this->changerName,
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'url' => route('admin.education.edit', $this->resource->id),
        ];
    }

    private function getMessage(): string
    {
        $title = $this->resource->title;
        $name = $this->changerName;
        $messages = [
            'send_for_review' => "Educational resource '$title' has been sent for review by $name",
            'approve' => "Educational resource '$title' has been approved by $name",
            'reject' => "Educational resource '$title' requires changes (rejected by $name)",
            'publish' => "Educational resource '$title' has been published by $name",
            'unpublish' => "Educational resource '$title' has been unpublished by $name",
            'archive' => "Educational resource '$title' has been archived by $name",
            'restore' => "Educational resource '$title' has been restored by $name",
            'send_back' => "Educational resource '$title' has been sent back for review by $name",
        ];
        return $messages[$this->action] ?? "Educational resource '$title' status changed from {$this->oldStatus} to {$this->newStatus} by $name";
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
