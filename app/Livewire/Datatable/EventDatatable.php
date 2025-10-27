<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Enums\Hooks\DatatableHook;
use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class EventDatatable extends Datatable
{
    public string $status = '';
    public string $event_type = '';
    public string $event_date = '';
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'event_type' => [],
        'event_date' => [],
    ];
    public string $model = Event::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by event name, description, or location...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingEventType()
    {
        $this->resetPage();
    }

    public function updatingEventDate()
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        parent::mount();

        // Hook to allow additional initialization from other modules
        $this->addHooks(
            $this,
            DatatableHook::EVENT_DATATABLE_MOUNTED
        );
    }

    public function getFilters(): array
    {
        $filters = [
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Status'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Statuses'),
                'options' => [
                    'draft' => __('Draft'),
                    'under_review' => __('Under Review'),
                    'approved' => __('Approved'),
                    'published' => __('Published'),
                    'completed' => __('Completed'),
                    'archived' => __('Archived'),
                    'cancelled' => __('Cancelled'),
                ],
                'selected' => $this->status,
            ],
            [
                'id' => 'event_type',
                'label' => __('Event Type'),
                'filterLabel' => __('Event Type'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Types'),
                'options' => [
                    'in-person' => __('In person'),
                    'virtual' => __('Virtual'),
                ],
                'selected' => $this->event_type,
            ],
            [
                'id' => 'event_date',
                'label' => __('Event Date'),
                'filterLabel' => __('Event Date'),
                'icon' => 'lucide:calendar',
                'allLabel' => __('All Dates'),
                'options' => [
                    'today' => __('Today'),
                    'this_week' => __('This Week'),
                    'this_month' => __('This Month'),
                    'past' => __('Past Events'),
                ],
                'selected' => $this->event_date,
            ],
        ];

        // Allow external modules to modify filters
        $filters = $this->addHooks(
            $filters,
            null,
            DatatableHook::DATATABLE_MOUNTED
        );

        return $filters;
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['event' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'title',
                'title' => __('Event Name'),
                'sortable' => true,
                'sortBy' => 'title',
            ],
            [
                'id' => 'event_type',
                'title' => __('Event Type'),
                'sortable' => true,
                'sortBy' => 'event_type',
            ],
            [
                'id' => 'event_date',
                'title' => __('Event Date'),
                'sortable' => true,
                'sortBy' => 'event_date',
            ],
            [
                'id' => 'location',
                'title' => __('Location'),
                'sortable' => true,
                'sortBy' => 'location',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
            ],
            [
                'id' => 'created_at',
                'title' => __('Created At'),
                'sortable' => true,
                'sortBy' => 'created_at',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'is_action' => true,
            ],
        ];
    }

    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->model)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->event_type, function ($q) {
                $q->where('event_type', $this->event_type);
            })
            ->when($this->event_date, function ($q) {
                $today = now()->startOfDay();
                $q->when($this->event_date === 'today', fn($sub) => $sub->whereDate('event_date', $today))
                    ->when($this->event_date === 'this_week', fn($sub) => $sub->whereBetween('event_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]))
                    ->when($this->event_date === 'this_month', fn($sub) => $sub->whereBetween('event_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()]))
                    ->when($this->event_date === 'past', fn($sub) => $sub->whereDate('event_date', '<', $today));
            });

        return $this->sortQuery($query);
    }

    public function renderTitleColumn(Event $event): string|Renderable
    {
        return <<<HTML
            <a href="{$this->getEditUrl($event)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$event->title}
            </a>
        HTML;
    }

    public function renderEventTypeColumn(Event $event): string|Renderable
    {
        return e(ucfirst($event->event_type ?? __('N/A')));
    }

    public function renderEventDateColumn(Event $event): string|Renderable
    {
        return $event->event_date ? $event->event_date->format('M d, Y') : __('N/A');
    }

    public function renderStatusColumn(Event $event): string|Renderable
    {
        $class = match ($event->status) {
            'upcoming' => 'badge badge-info',
            'ongoing' => 'badge badge-success',
            'completed' => 'badge badge-secondary',
            'cancelled' => 'badge badge-danger',
            default => 'badge badge-light',
        };

        return "<span class='{$class}'>" . ucfirst($event->status) . "</span>";
    }

    public function renderLocationColumn(Event $event): string|Renderable
    {
        return e($event->location ?? __('N/A'));
    }

    protected function getEditUrl(Event $event): string
    {
        return route('admin.events.edit', $event->id);
    }
}
