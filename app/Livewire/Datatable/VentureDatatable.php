<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Venture;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class VentureDatatable extends Datatable
{
    public string $focus_area = '';
    public string $stage = '';
    public string $featured = '';
    
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'focus_area' => [],
        'stage' => [],
        'featured' => [],
    ];
    
    public string $model = Venture::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by venture name, description...');
    }

    public function updatingFocusArea()
    {
        $this->resetPage();
    }

    public function updatingStage()
    {
        $this->resetPage();
    }

    public function updatingFeatured()
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'focus_area',
                'label' => __('Focus Area'),
                'filterLabel' => __('Focus Area'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Areas'),
                'options' => [
                    'mental-health' => __('Mental Health'),
                    'telemedicine' => __('Telemedicine'),
                    'pharmaceuticals' => __('Pharmaceuticals'),
                    'biotech' => __('Biotech'),
                    'medtech' => __('MedTech'),
                    'diagnostics' => __('Diagnostics'),
                    'health-tech' => __('Health Tech'),
                    'other' => __('Other'),
                ],
                'selected' => $this->focus_area,
            ],
            [
                'id' => 'stage',
                'label' => __('Stage'),
                'filterLabel' => __('Stage'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Stages'),
                'options' => [
                    'idea' => __('Idea'),
                    'prototype' => __('Prototype'),
                    'early-stage' => __('Early Stage'),
                    'growth' => __('Growth'),
                    'scale' => __('Scale'),
                ],
                'selected' => $this->stage,
            ],
            [
                'id' => 'featured',
                'label' => __('Featured'),
                'filterLabel' => __('Featured'),
                'icon' => 'lucide:star',
                'allLabel' => __('All'),
                'options' => [
                    '1' => __('Featured'),
                    '0' => __('Not Featured'),
                ],
                'selected' => $this->featured,
            ],
        ];
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['venture' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Venture Name'),
                'sortable' => true,
                'sortBy' => 'name',
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
                'id' => 'country',
                'title' => __('Country'),
                'sortable' => true,
                'sortBy' => 'country',
            ],
            [
                'id' => 'votes_count',
                'title' => __('Votes'),
                'sortable' => true,
                'sortBy' => 'votes_count',
            ],
            [
                'id' => 'featured',
                'title' => __('Featured'),
                'sortable' => true,
                'sortBy' => 'featured',
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
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('tagline', 'like', "%{$this->search}%");
                });
            })
            ->when($this->focus_area, function ($q) {
                $q->where('focus_area', $this->focus_area);
            })
            ->when($this->stage, function ($q) {
                $q->where('stage', $this->stage);
            })
            ->when($this->featured !== '', function ($q) {
                $q->where('featured', (bool) $this->featured);
            });

        return $this->sortQuery($query);
    }

    public function renderNameColumn(Venture $venture): string|Renderable
    {
        return <<<HTML
            <a href="{$this->getEditUrl($venture)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$venture->name}
            </a>
        HTML;
    }

    public function renderFocusAreaColumn(Venture $venture): string|Renderable
    {
        return e(ucfirst(str_replace('-', ' ', $venture->focus_area)));
    }

    public function renderStageColumn(Venture $venture): string|Renderable
    {
        return e(ucfirst($venture->stage));
    }

    public function renderCountryColumn(Venture $venture): string|Renderable
    {
        return e($venture->country);
    }

    public function renderVotesCountColumn(Venture $venture): string|Renderable
    {
        return e(number_format($venture->votes_count));
    }

    public function renderFeaturedColumn(Venture $venture): string|Renderable
    {
        $icon = $venture->featured ? 'lucide:star' : 'lucide:star-off';
        $class = $venture->featured ? 'text-yellow-500' : 'text-gray-400';
        return "<iconify-icon icon='{$icon}' class='{$class}'></iconify-icon>";
    }

    protected function getEditUrl(Venture $venture): string
    {
        return route('admin.ventures.show', $venture->id);
    }
}
