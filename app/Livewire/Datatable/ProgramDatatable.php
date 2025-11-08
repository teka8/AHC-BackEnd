<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\On;

class ProgramDatatable extends Datatable
{
    public string $status = '';
    public string $programType = ''; // Not used for now, but kept for consistency with PostDatatable
    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
    ];
    public string $model = Program::class;

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by title or host...');
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        $filters = [];
        
        $statuses = ProgramStatus::cases();

        $translatedStatuses = collect($statuses)->mapWithKeys(function ($enum) {
            return [$enum->value => __(strval($enum->value))];
        })->toArray();

        $filters[] = [
            'id' => 'status',
            'label' => __('Status'),
            'filterLabel' => __('Status'),
            'icon' => 'lucide:filter',
            'allLabel' => __('All Statuses'),
            'options' => $translatedStatuses,
            'selected' => $this->status,
        ];

        return $filters;
    }

    protected function getRouteParameters(): array
    {
        return [];
    }

    protected function getItemRouteParameters($item):
    array
    {
        return [
            'program' => $item->id,
        ];
    }

    protected function getHeaders(): array
    {
        $headers = [
            [
                'id' => 'title',
                'title' => __('Title'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'title',
            ],
            [
                'id' => 'host',
                'title' => __('Host'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'host',
            ],
            [
                'id' => 'state',
                'title' => __('Status'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'state',
            ],
            [
                'id' => 'status_actions',
                'title' => __('Status Actions'),
                'width' => null,
                'sortable' => false,
                'is_action' => true,
            ],
            [
                'id' => 'created_at',
                'title' => __('Created At'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'created_at',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'width' => null,
                'sortable' => false,
                'is_action' => true,
            ],
        ];

        return $headers;
    }

    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->model)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('host', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, function ($q) {
                $q->where('state', $this->status);
            });

        return $this->sortQuery($query);
    }

    public function renderTitleColumn(Program $program): string|Renderable
    {
        ob_start();
        ?>
        <div class="flex gap-0.5 items-center">
            <?php if ($program->hasImage()): ?>
                <img src="<?php echo $program->getImageUrl() ?>" alt="<?php echo $program->title ?>"
                    class="w-12 object-cover rounded mr-3 min-w-10">
            <?php else: ?>
                <div class="bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center mr-2 h-10 w-10 min-w-10">
                    <iconify-icon icon="lucide:award" class=" text-center text-gray-400"></iconify-icon>
                </div>
            <?php endif; ?>
            <a href="<?php echo route('admin.programs.edit', $program->id) ?>"
                class="text-gray-700 dark:text-white font-medium hover:text-primary dark:hover:text-primary">
                <?php echo $program->title; ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    public function renderStateColumn(Program $program): string|Renderable
    {
        $status = $program->state->value;
        $html = "<span class='" . get_program_status_class($program->state->value) . "'>" . ucfirst(__($status)) . "</span>";

        return $html;
    }

    public function renderHostColumn(Program $program): string|Renderable
    {
        return $program->host;
    }

    public function renderStatusActionsColumn($item): string|Renderable
    {
        /** @var \App\Models\Program $program */
        $program = $item;
        $actions = $program->getAvailableActions();

        if (count($actions) === 0) {
            return '';
        }

        return Blade::render('
            <div class="flex flex-wrap gap-1">
                @foreach($actions as $action => $config)
                    <button x-on:click="showChangeStatusModal({{ $program->id }}, \'{{ $action }}\', {{ json_encode($config) }})"
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
                                   bg-{{ $config["color"] }}-100 text-{{ $config["color"] }}-800 
                                   hover:bg-{{ $config["color"] }}-200 dark:bg-{{ $config["color"] }}-900/20 
                                   dark:text-{{ $config["color"] }}-300 dark:hover:bg-{{ $config["color"] }}-900/30
                                   transition-colors duration-200"
                            title="{{ $config["label"] }}">
                        <iconify-icon icon="{{ $config["icon"] }}" class="w-3 h-3 mr-1"></iconify-icon>
                        {{ $config["label"] }}
                    </button>
                @endforeach
            </div>
        ', ['program' => $program, 'actions' => $actions]);
    }

    public function renderActionsColumn($item): string|Renderable
    {
        return '';
    }

    #[On('showDeleteModal')]
    public function showDeleteModal(int $programId): void
    {
        $this->dispatch('showDeleteModal', ['id' => $programId]);
    }

    #[On('showChangeStatusModal')]
    public function showChangeStatusModal(int $programId, string $action, array $config): void
    {
        $this->dispatch('showChangeStatusModal', [
            'id' => $programId,
            'action' => $action,
            'config' => $config,
            'modelNameSingular' => $this->getModelNameSingular(),
        ]);
    }

    #[On('changeProgramStatus')]
    public function changeProgramStatus(int $programId, string $action, string $comment = '')
    {
        $program = Program::findOrFail($programId);
        if ($program->changeStatus($action, auth()->user(), $comment)) {
            $this->dispatch('showToast', ['type' => 'success', 'message' => __('Program status updated successfully.')]);
        } else {
            $this->dispatch('showToast', ['type' => 'error', 'message' => __('Failed to update program status.')]);
        }
        $this->dispatch('$refresh');
    }
}
