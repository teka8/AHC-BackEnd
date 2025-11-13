<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Term;
use Spatie\QueryBuilder\QueryBuilder;

class TermDatatable extends Datatable
{
    public string $taxonomy;
    public string $model = Term::class;
    public array $disabledRoutes = ['view'];
    public ?string $postType = null;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by :taxonomy name...', ['taxonomy' => $this->taxonomy]);
    }

    protected function getNoResultsMessage(): string
    {
        return __('No :items found.', ['items' => ucfirst($this->taxonomy)]);
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Name'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'name',
            ],
            [
                'id' => 'parent',
                'title' => __('Parent'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'parent_id',
            ],
            [
                'id' => 'posts_count',
                'title' => __('Posts'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'posts_count',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'width' => null,
                'sortable' => false,
                'is_action' => true,
            ],
        ];
    }

    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->model)
            ->where('taxonomy', $this->taxonomy)
            ->when($this->postType, fn ($q) => $q->forPostType($this->postType))
            ->with('parent')
            ->withCount(['posts as posts_count' => function ($relation) {
                if ($this->postType) {
                    $relation->where('post_type', $this->postType);
                }
            }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            });

        return $this->sortQuery($query);
    }

    public function renderParentColumn($term): string
    {
        return $term->parent->name ?? '-';
    }

    protected function getRouteParameters(): array
    {
        return array_filter([
            'taxonomy' => $this->taxonomy,
            'post_type' => $this->postType,
        ]);
    }

    protected function getItemRouteParameters($item): array
    {
        return array_filter([
            'taxonomy' => $this->taxonomy,
            'term' => $item->id,
            'post_type' => $this->postType,
        ]);
    }

    public function renderNameColumn($term): string
    {
        if ($term instanceof Term && $term->isProtectedForPostType($this->postType)) {
            return "<span class='text-gray-600 dark:text-gray-300'>{$term->name}</span>";
        }

        return "<a class='text-primary hover:underline'  href=\"".$this->getEditRouteUrl($term)."\">{$term->name}</a>";
    }

    public function getActionCellPermissions($item): array
    {
        $permissions = parent::getActionCellPermissions($item);

        if ($item instanceof Term && $item->isProtectedForPostType($this->postType)) {
            $permissions['edit'] = false;
            $permissions['delete'] = false;
        }

        return $permissions;
    }
}
