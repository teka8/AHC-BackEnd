<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ScholarshipApplication;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class ScholarshipApplicationDatatable extends Datatable
{
    public string $status = '';
    public string $scholarship_id = '';
    
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'scholarship_id' => [],
    ];
    
    public string $model = ScholarshipApplication::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by applicant name, email...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingScholarshipId()
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
                    'shortlisted' => __('Shortlisted'),
                    'interviewed' => __('Interviewed'),
                    'accepted' => __('Accepted'),
                    'rejected' => __('Rejected'),
                    'withdrawn' => __('Withdrawn'),
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
        return ['scholarshipApplication' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'applicant',
                'title' => __('Applicant'),
                'sortable' => true,
                'sortBy' => 'first_name',
            ],
            [
                'id' => 'scholarship',
                'title' => __('Scholarship'),
                'sortable' => false,
            ],
            [
                'id' => 'email',
                'title' => __('Email'),
                'sortable' => true,
                'sortBy' => 'email',
            ],
            [
                'id' => 'education_level',
                'title' => __('Education Level'),
                'sortable' => true,
                'sortBy' => 'current_education_level',
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
            ->with('scholarship')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->scholarship_id, function ($q) {
                $q->where('scholarship_id', $this->scholarship_id);
            });

        return $this->sortQuery($query);
    }

    public function renderApplicantColumn(ScholarshipApplication $application): string|Renderable
    {
        $name = $application->first_name . ' ' . $application->last_name;
        return <<<HTML
            <a href="{$this->getEditUrl($application)}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                {$name}
            </a>
        HTML;
    }

    public function renderScholarshipColumn(ScholarshipApplication $application): string|Renderable
    {
        return e($application->scholarship->title ?? 'N/A');
    }

    public function renderEmailColumn(ScholarshipApplication $application): string|Renderable
    {
        return e($application->email);
    }

    public function renderEducationLevelColumn(ScholarshipApplication $application): string|Renderable
    {
        return e(ucfirst(str_replace('-', ' ', $application->current_education_level)));
    }

    public function renderStatusColumn(ScholarshipApplication $application): string|Renderable
    {
        $class = match ($application->status) {
            'draft' => 'badge badge-secondary',
            'submitted' => 'badge badge-info',
            'under-review' => 'badge badge-primary',
            'shortlisted' => 'badge badge-warning',
            'interviewed' => 'badge badge-purple',
            'accepted' => 'badge badge-success',
            'rejected' => 'badge badge-danger',
            'withdrawn' => 'badge badge-light',
            default => 'badge badge-light',
        };

        return "<span class='{$class}'>" . e(ucfirst(str_replace('-', ' ', $application->status))) . "</span>";
    }

    public function renderSubmittedAtColumn(ScholarshipApplication $application): string|Renderable
    {
        return $application->submitted_at ? $application->submitted_at->format('M d, Y') : '<span class="text-gray-400">Draft</span>';
    }

    protected function getEditUrl(ScholarshipApplication $application): string
    {
        return route('admin.scholarship-applications.show', $application->id);
    }

    public function getRoutes(): array
    {
        return [
            'view' => 'admin.scholarship-applications.show',
            'delete' => 'admin.scholarship-applications.destroy',
        ];
    }

    public function renderAfterActionView($item): string
    {
        $evaluateUrl = route('admin.scholarship-evaluation.create', $item->id);
        
        return <<<HTML
            <x-buttons.action-item
                href="{$evaluateUrl}"
                icon="lucide:clipboard-check"
                label="Evaluate"
            />
        HTML;
    }
}
