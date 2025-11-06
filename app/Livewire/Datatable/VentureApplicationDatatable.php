<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\VentureApplication;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class VentureApplicationDatatable extends Datatable
{
    public string $status = '';
    
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
    ];
    
    public string $model = VentureApplication::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by venture name, contact name...');
    }

    public function updatingStatus()
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
                    'draft' => __('Draft'),
                    'submitted' => __('Submitted'),
                    'under-review' => __('Under Review'),
                    'approved' => __('Approved'),
                    'rejected' => __('Rejected'),
                ],
                'selected' => $this->status,
            ],
        ];
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['ventureApplication' => $item->id];
    }

    public function getRoutes(): array
    {
        return [
            'view' => 'admin.venture-applications.show',
            // 'edit' => 'admin.venture-applications.edit',
            'delete' => 'admin.venture-applications.destroy',
        ];
    }
    
    public function getPermissions(): array
    {
        return [
            'view' => 'view',
            // 'edit' => 'update',
            'delete' => 'delete',
        ];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'venture_name',
                'title' => __('Venture Name'),
                'sortable' => true,
                'sortBy' => 'venture_name',
            ],
            [
                'id' => 'contact_name',
                'title' => __('Contact Name'),
                'sortable' => true,
                'sortBy' => 'contact_name',
            ],
            [
                'id' => 'focus_area',
                'title' => __('Focus Area'),
                'sortable' => true,
                'sortBy' => 'focus_area',
            ],
            [
                'id' => 'stage',
                'title' => __('Stage'),
                'sortable' => true,
                'sortBy' => 'stage',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
            ],
            [
                'id' => 'submitted_at',
                'title' => __('Submitted At'),
                'sortable' => true,
                'sortBy' => 'submitted_at',
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
                    $q->where('venture_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            });

        return $this->sortQuery($query);
    }

    public function renderVentureNameColumn(VentureApplication $application): string|Renderable
    {
        return <<<HTML
            <a href="{$this->getEditUrl($application)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$application->venture_name}
            </a>
        HTML;
    }

    public function renderContactNameColumn(VentureApplication $application): string|Renderable
    {
        return e($application->contact_name);
    }

    public function renderFocusAreaColumn(VentureApplication $application): string|Renderable
    {
        return e(ucfirst(str_replace('-', ' ', $application->focus_area)));
    }

    public function renderStageColumn(VentureApplication $application): string|Renderable
    {
        return e(ucfirst($application->stage));
    }

    public function renderStatusColumn(VentureApplication $application): string|Renderable
    {
        $class = match ($application->status) {
            'draft' => 'badge badge-secondary',
            'submitted' => 'badge badge-info',
            'under-review' => 'badge badge-primary',
            'approved' => 'badge badge-success',
            'rejected' => 'badge badge-danger',
            default => 'badge badge-light',
        };

        return "<span class='{$class}'>" . e(ucfirst(str_replace('-', ' ', $application->status))) . "</span>";
    }

    public function renderSubmittedAtColumn(VentureApplication $application): string|Renderable
    {
        return $application->submitted_at ? $application->submitted_at->format('M d, Y') : '<span class="text-gray-400">Draft</span>';
    }

    protected function getEditUrl(VentureApplication $application): string
    {
        return route('admin.venture-applications.show', $application->id);
    }
}
