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
    public string $category = '';
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'category' => [],
    ];
    public string $model = Event::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by event name or description...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
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
                    'upcoming' => __('Upcoming'),
                    'ongoing' => __('Ongoing'),
                    'completed' => __('Completed'),
                    'cancelled' => __('Cancelled'),
                ],
                'selected' => $this->status,
            ],
        ];

        // Add more filters if your Event model supports categories or tags
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
                'id' => 'date',
                'title' => __('Date'),
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

    public function renderDateColumn(Event $event): string|Renderable
    {
        return $event->event_date ? $event->event_date->format('M d, Y') : __('N/A');
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
