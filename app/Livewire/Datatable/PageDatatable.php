<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Enums\Hooks\DatatableHook;
use App\Models\Page;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class PageDatatable extends Datatable
{
    public string $status = '';
    public string $section = '';
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'section' => [],
    ];
    public string $model = Page::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by page title, content, or slug...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingSection()
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        parent::mount();

        // Hook to allow additional initialization from other modules
        $this->addHooks(
            $this,
            DatatableHook::PAGE_DATATABLE_MOUNTED
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
                    'published' => __('Published'),
                    'draft' => __('Draft'),
                    'archived' => __('Archived'),
                ],
                'selected' => $this->status,
            ],
            [
                'id' => 'section',
                'label' => __('Section'),
                'filterLabel' => __('Section'),
                'icon' => 'lucide:folder',
                'allLabel' => __('All Sections'),
                'options' => [
                    'about' => __('About Us'),
                    'terms' => __('Terms & Conditions'),
                    'privacy' => __('Privacy Policy'),
                    'contact' => __('Contact Information'),
                    'faq' => __('FAQ'),
                    'shipping' => __('Shipping Policy'),
                    'returns' => __('Return Policy'),
                    'custom' => __('Custom Sections'),
                ],
                'selected' => $this->section,
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
        return ['page' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'title',
                'title' => __('Page Title'),
                'sortable' => true,
                'sortBy' => 'title',
            ],
            [
                'id' => 'section',
                'title' => __('Section'),
                'sortable' => true,
                'sortBy' => 'section',
            ],
            [
                'id' => 'slug',
                'title' => __('Slug'),
                'sortable' => true,
                'sortBy' => 'slug',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
            ],
            [
                'id' => 'navigation',
                'title' => __('Navigation'),
                'sortable' => true,
                'sortBy' => 'show_in_nav',
            ],
            [
                'id' => 'footer',
                'title' => __('Footer'),
                'sortable' => true,
                'sortBy' => 'show_in_footer',
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
                        ->orWhere('content', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('section', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->section, function ($q) {
                if ($this->section === 'custom') {
                    $q->where('is_custom_section', true);
                } else {
                    $q->where('section', $this->section);
                }
            });

        return $this->sortQuery($query);
    }

    public function renderTitleColumn(Page $page): string|Renderable
    {
        return <<<HTML
            <a href="{$this->getEditUrl($page)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$page->title}
            </a>
        HTML;
    }

    public function renderSectionColumn(Page $page): string|Renderable
    {
        $section = e($page->section ?? __('N/A'));
        if ($page->is_custom_section) {
            $section .= ' <span class="text-xs text-blue-600 dark:text-blue-400 ml-1">(' . __('Custom') . ')</span>';
        }
        return $section;
    }

    public function renderSlugColumn(Page $page): string|Renderable
    {
        return '<code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">/' . e($page->slug) . '</code>';
    }

    public function renderStatusColumn(Page $page): string|Renderable
    {
        $class = match ($page->status) {
            'published' => 'badge badge-success',
            'draft' => 'badge badge-info',
            'archived' => 'badge badge-secondary',
            default => 'badge badge-light',
        };

        return "<span class='{$class}'>" . ucfirst($page->status) . "</span>";
    }

    public function renderNavigationColumn(Page $page): string|Renderable
    {
        if ($page->show_in_nav) {
            return '<span class="badge badge-success">' . __('Visible') . '</span>';
        } else {
            return '<span class="badge badge-secondary">' . __('Hidden') . '</span>';
        }
    }

    public function renderFooterColumn(Page $page): string|Renderable
    {
        if ($page->show_in_footer) {
            return '<span class="badge badge-success">' . __('Visible') . '</span>';
        } else {
            return '<span class="badge badge-secondary">' . __('Hidden') . '</span>';
        }
    }

    protected function getEditUrl(Page $page): string
    {
        return route('admin.pages.edit', $page->id);
    }
}