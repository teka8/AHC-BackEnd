<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Scholarship;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class ScholarshipDatatable extends Datatable
{
    public string $status = '';
    public string $program_type = '';
    
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'program_type' => [],
    ];
    
    public string $model = Scholarship::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by scholarship title...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingProgramType()
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Status'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Statuses'),
                'options' => [
                    'open' => __('Open'),
                    'closed' => __('Closed'),
                    'upcoming' => __('Upcoming'),
                ],
                'selected' => $this->status,
            ],
            [
                'id' => 'program_type',
                'label' => __('Program Type'),
                'filterLabel' => __('Program Type'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Types'),
                'options' => [
                    'undergraduate' => __('Undergraduate'),
                    'graduate' => __('Graduate'),
                    'postgraduate' => __('Postgraduate'),
                    'research' => __('Research'),
                ],
                'selected' => $this->program_type,
            ],
        ];
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['scholarship' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'title',
                'title' => __('Title'),
                'sortable' => true,
                'sortBy' => 'title',
            ],
            [
                'id' => 'program_type',
                'title' => __('Program Type'),
                'sortable' => true,
                'sortBy' => 'program_type',
            ],
            [
                'id' => 'amount',
                'title' => __('Amount'),
                'sortable' => true,
                'sortBy' => 'amount',
            ],
            [
                'id' => 'deadline',
                'title' => __('Deadline'),
                'sortable' => true,
                'sortBy' => 'deadline',
            ],
            [
                'id' => 'available_slots',
                'title' => __('Available Slots'),
                'sortable' => true,
                'sortBy' => 'available_slots',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
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
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->program_type, function ($q) {
                $q->where('program_type', $this->program_type);
            });

        return $this->sortQuery($query);
    }

    public function renderTitleColumn(Scholarship $scholarship): string|Renderable
    {
        return <<<HTML
            <a href="{$this->getEditUrl($scholarship)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$scholarship->title}
            </a>
        HTML;
    }

    public function renderProgramTypeColumn(Scholarship $scholarship): string|Renderable
    {
        return e(ucfirst($scholarship->program_type));
    }

    public function renderAmountColumn(Scholarship $scholarship): string|Renderable
    {
        return $scholarship->amount 
            ? '$' . number_format((float)$scholarship->amount) 
            : 'N/A';
    }

    public function renderDeadlineColumn(Scholarship $scholarship): string|Renderable
    {
        return $scholarship->deadline
            ? $scholarship->deadline->format('M d, Y')
            : '<span class="text-gray-400 italic text-xs">No deadline</span>';
    }

    public function renderAvailableSlotsColumn(Scholarship $scholarship): string|Renderable
    {
        return e($scholarship->available_slots);
    }

    public function renderStatusColumn(Scholarship $scholarship): string|Renderable
    {
        $class = match ($scholarship->status) {
            'open' => 'badge badge-success',
            'closed' => 'badge badge-danger',
            'upcoming' => 'badge badge-info',
            default => 'badge badge-light',
        };

        return "<span class='{$class}'>" . e(ucfirst($scholarship->status)) . "</span>";
    }

    protected function getEditUrl(Scholarship $scholarship): string
    {
        return route('admin.scholarships.show', $scholarship->id);
    }
}
