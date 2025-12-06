<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\AhcLeader;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

class AhcLeaderDatatable extends Datatable
{
    public string $status = '';

    public string $type = '';

    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'type' => [],
    ];

    public string $model = AhcLeader::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by name or position...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'type',
                'label' => __('Type'),
                'filterLabel' => __('Type'),
                'icon' => 'lucide:users',
                'allLabel' => __('All Types'),
                'options' => [
                    'leader' => __('Leaders'),
                    'team' => __('Team Members'),
                ],
                'selected' => $this->type,
            ],
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Status'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Statuses'),
                'options' => [
                    '1' => __('Active'),
                    '0' => __('Inactive'),
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
        return ['ahcLeader' => $item->id];
    }

    public function getRoutes(): array
    {
        return [
            'create' => 'admin.ahc-leaders.create',
            'view' => 'admin.ahc-leaders.show',
            'edit' => 'admin.ahc-leaders.edit',
            'delete' => 'admin.ahc-leaders.destroy',
        ];
    }

    protected function getPermissions(): array
    {
        return [
            'create' => 'ahc-leader.create',
            'view' => 'ahc-leader.view',
            'edit' => 'ahc-leader.update',
            'delete' => 'ahc-leader.delete',
        ];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'image',
                'title' => __('Image'),
                'sortable' => false,
            ],
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortable' => true,
                'sortBy' => 'name',
            ],
            [
                'id' => 'position',
                'title' => __('Position'),
                'sortable' => true,
                'sortBy' => 'position',
            ],
            [
                'id' => 'type',
                'title' => __('Type'),
                'sortable' => true,
                'sortBy' => 'type',
            ],
            [
                'id' => 'sort_order',
                'title' => __('Order'),
                'sortable' => true,
                'sortBy' => 'sort_order',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'is_active',
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
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('position', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type !== '', function ($q) {
                $q->where('type', $this->type);
            })
            ->when($this->status !== '', function ($q) {
                $q->where('is_active', (bool) $this->status);
            });

        return $this->sortQuery($query);
    }

    public function renderImageColumn(AhcLeader $leader): string|Renderable
    {
        $imageUrl = $leader->image_url ?? asset('images/placeholder.png');

        return <<<HTML
            <img src="{$imageUrl}" alt="{$leader->name}" class="w-12 h-12 rounded-full object-cover">
        HTML;
    }

    public function renderNameColumn(AhcLeader $leader): string|Renderable
    {
        $showUrl = route('admin.ahc-leaders.show', $leader->id);

        return <<<HTML
            <a href="{$showUrl}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$leader->name}
            </a>
        HTML;
    }

    public function renderPositionColumn(AhcLeader $leader): string|Renderable
    {
        return e($leader->position);
    }

    public function renderTypeColumn(AhcLeader $leader): string|Renderable
    {
        $isLeader = $leader->type === 'leader';
        $class = $isLeader ? 'badge badge-primary' : 'badge badge-info';
        $text = $isLeader ? __('Leader') : __('Team');

        return "<span class='{$class}'>{$text}</span>";
    }

    public function renderSortOrderColumn(AhcLeader $leader): string|Renderable
    {
        return e($leader->sort_order);
    }

    public function renderStatusColumn(AhcLeader $leader): string|Renderable
    {
        $class = $leader->is_active ? 'badge badge-success' : 'badge badge-danger';
        $text = $leader->is_active ? __('Active') : __('Inactive');

        return "<span class='{$class}'>{$text}</span>";
    }

    protected function getEditUrl(AhcLeader $leader): string
    {
        return route('admin.ahc-leaders.edit', $leader->id);
    }
}
