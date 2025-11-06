<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ScholarshipEvaluation;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class ScholarshipEvaluationDatatable extends Datatable
{
    public string $recommendation = '';
    public string $reviewer_id = '';
    
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'recommendation' => [],
        'reviewer_id' => [],
    ];
    
    public string $model = ScholarshipEvaluation::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by applicant name or email...');
    }

    public function updatingRecommendation()
    {
        $this->resetPage();
    }

    public function updatingReviewerId()
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'recommendation',
                'label' => __('Recommendation'),
                'filterLabel' => __('Recommendation'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Recommendations'),
                'options' => [
                    'strong-accept' => __('Strong Accept'),
                    'accept' => __('Accept'),
                    'waitlist' => __('Waitlist'),
                    'reject' => __('Reject'),
                ],
                'selected' => $this->recommendation,
            ],
        ];
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['scholarshipEvaluation' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'applicant',
                'title' => __('Applicant'),
                'sortable' => false,
            ],
            [
                'id' => 'reviewer',
                'title' => __('Reviewer'),
                'sortable' => false,
            ],
            [
                'id' => 'overall_score',
                'title' => __('Overall Score'),
                'sortable' => true,
                'sortBy' => 'overall_score',
            ],
            [
                'id' => 'score_breakdown',
                'title' => __('Score Breakdown'),
                'sortable' => false,
            ],
            [
                'id' => 'recommendation',
                'title' => __('Recommendation'),
                'sortable' => true,
                'sortBy' => 'recommendation',
            ],
            [
                'id' => 'evaluated_at',
                'title' => __('Evaluated At'),
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
            ->with(['application', 'reviewer'])
            ->when($this->search, function ($query) {
                $query->whereHas('application', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->recommendation, function ($q) {
                $q->where('recommendation', $this->recommendation);
            })
            ->when($this->reviewer_id, function ($q) {
                $q->where('reviewer_id', $this->reviewer_id);
            });

        return $this->sortQuery($query);
    }

    public function renderApplicantColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        $name = e($evaluation->application->first_name . ' ' . $evaluation->application->last_name);
        $email = e($evaluation->application->email);
        $url = route('admin.scholarship-applications.show', $evaluation->application->id);
        
        return <<<HTML
            <div>
                <a href="{$url}" class="text-gray-700 dark:text-white font-medium hover:text-primary">
                    {$name}
                </a>
                <div class="text-xs text-gray-500 dark:text-gray-400">{$email}</div>
            </div>
        HTML;
    }

    public function renderReviewerColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        return e($evaluation->reviewer->name ?? 'N/A');
    }

    public function renderOverallScoreColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        $score = number_format(floatval($evaluation->overall_score), 1);
        
        return <<<HTML
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{$score}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">/10</div>
            </div>
        HTML;
    }

    public function renderScoreBreakdownColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        $academic = number_format(floatval($evaluation->academic_performance_score), 1);
        $motivation = number_format(floatval($evaluation->motivation_score), 1);
        $research = number_format(floatval($evaluation->research_quality_score), 1);
        $financial = number_format(floatval($evaluation->financial_need_score), 1);
        
        return <<<HTML
            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                <div title="Academic Performance">A: {$academic}</div>
                <div title="Motivation & Commitment">M: {$motivation}</div>
                <div title="Research Quality">R: {$research}</div>
                <div title="Financial Need">F: {$financial}</div>
            </div>
        HTML;
    }

    public function renderRecommendationColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        $class = match ($evaluation->recommendation) {
            'strong-accept' => 'badge badge-success',
            'accept' => 'badge badge-success-light',
            'waitlist' => 'badge badge-warning',
            'reject' => 'badge badge-danger',
            default => 'badge badge-light',
        };

        $label = match ($evaluation->recommendation) {
            'strong-accept' => '✓✓ ' . __('Strong Accept'),
            'accept' => '✓ ' . __('Accept'),
            'waitlist' => '⏸ ' . __('Waitlist'),
            'reject' => '✗ ' . __('Reject'),
            default => e(ucfirst(str_replace('-', ' ', $evaluation->recommendation))),
        };

        return "<span class='{$class}'>{$label}</span>";
    }

    public function renderEvaluatedAtColumn(ScholarshipEvaluation $evaluation): string|Renderable
    {
        return e($evaluation->created_at->format('M d, Y'));
    }

    protected function getEditUrl(ScholarshipEvaluation $evaluation): string
    {
        return route('admin.scholarship-evaluation.show', $evaluation->id);
    }

    public function getRoutes(): array
    {
        return [
            'view' => 'admin.scholarship-evaluation.show',
            'edit' => 'admin.scholarship-evaluation.edit',
            'delete' => 'admin.scholarship-evaluation.destroy',
        ];
    }

    public function renderAfterActionEdit($item): string
    {
        $applicationUrl = route('admin.scholarship-applications.show', $item->application_id);
        
        return <<<HTML
            <x-buttons.action-item
                href="{$applicationUrl}"
                icon="lucide:file-text"
                label="View Application"
            />
        HTML;
    }

    public function renderBeforeSearchbar(): string|Renderable
    {
        // Statistics cards
        $totalEvaluations = ScholarshipEvaluation::count();
        $avgScore = ScholarshipEvaluation::avg('overall_score');
        $acceptedCount = ScholarshipEvaluation::whereIn('recommendation', ['strong-accept', 'accept'])->count();
        $rejectedCount = ScholarshipEvaluation::where('recommendation', 'reject')->count();
        
        return view('components.datatable.evaluation-stats', [
            'totalEvaluations' => $totalEvaluations,
            'avgScore' => $avgScore,
            'acceptedCount' => $acceptedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }
}
