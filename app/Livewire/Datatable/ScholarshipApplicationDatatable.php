<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;
use App\Services\ScholarshipApplicationPdfService;
use Illuminate\Support\Facades\File;

class ScholarshipApplicationDatatable extends Datatable
{
    public string $status = '';
    public string $scholarship_id = '';
    public int $selectedCount = 0;

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

    public function updatedSelectedItems()
    {
        $this->selectedCount = count($this->selectedItems);
    }

    public function render(): Renderable
    {
        $this->headers = $this->getHeaders();

        $customBulkActions = view('backend.pages.scholarship-applications.partials.bulk-actions', [
            'selectedCount' => $this->selectedCount,
            'bulkDeleteAction' => $this->getBulkDeleteAction(),
        ])->render();

        return view('backend.livewire.datatable.datatable', [
            'headers' => $this->headers,
            'data' => $this->getData(),
            'perPage' => $this->perPage,
            'perPageOptions' => $this->perPageOptions,
            'customBulkActions' => $customBulkActions,
        ]);
    }

    public function getFilters(): array
    {
        $scholarships = Scholarship::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();

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
            [
                'id' => 'scholarship_id',
                'label' => __('Scholarship'),
                'filterLabel' => __('Scholarship'),
                'icon' => 'lucide:award',
                'allLabel' => __('All Scholarships'),
                'options' => $scholarships,
                'selected' => $this->scholarship_id,
            ],
        ];
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    public function getBulkActions(): array
    {
        return [
            'downloadZip' => 'Download ZIP (' . $this->selectedCount . ')',
        ];
    }

    public function processZipChunk(array $ids, string $batchId)
    {
        $applications = ScholarshipApplication::whereIn('id', $ids)->with('scholarship')->get();
        $zipFileName = 'temp_zip_' . $batchId . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        // Open zip, create if not exists
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $pdfService = app(ScholarshipApplicationPdfService::class);

            foreach ($applications as $application) {
                $folderName = Str::slug($application->first_name . '_' . $application->last_name . '_' . $application->id);
                
                // 1. Generate PDF Details
                try {
                    $pdfContent = $pdfService->generatePdf($application);
                    $zip->addFromString($folderName . '/Application_Details.pdf', $pdfContent);
                } catch (\Exception $e) {
                    $htmlContent = view('backend.pages.scholarship-applications.pdf', ['application' => $application])->render();
                    $zip->addFromString($folderName . '/Application_Details.html', $htmlContent);
                }

                // 2. Add Uploaded Documents
                $documents = [
                    'cv' => 'CV',
                    'transcript' => 'Transcript',
                    'motivation_letter_file' => 'Motivation_Letter',
                    'recommendation_letter_1' => 'Recommendation_Letter_1',
                    'recommendation_letter_2' => 'Recommendation_Letter_2',
                    'id_document' => 'ID_Document',
                    'proof_of_enrollment' => 'Proof_of_Enrollment'
                ];

                foreach ($documents as $field => $prefix) {
                    if ($application->$field && Storage::disk('public')->exists($application->$field)) {
                        $filePath = Storage::disk('public')->path($application->$field);
                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                        $fileName = 'Documents_' . $prefix . '.' . $extension;
                        $zip->addFile($filePath, $folderName . '/' . $fileName);
                    }
                }
            }
            $zip->close();
        }
        return true;
    }

    public function finalizeZipDownload(string $batchId)
    {
        $tempZipName = 'temp_zip_' . $batchId . '.zip';
        $tempZipPath = storage_path('app/public/' . $tempZipName);
        
        if (!file_exists($tempZipPath)) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => 'Error',
                'message' => 'Zip file generation failed.',
            ]);
            return;
        }

        $finalZipName = 'scholarship_applications_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $finalZipPath = storage_path('app/public/' . $finalZipName);
        
        rename($tempZipPath, $finalZipPath);

        return response()->download($finalZipPath)->deleteFileAfterSend(true);
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
