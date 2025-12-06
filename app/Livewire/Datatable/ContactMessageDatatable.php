<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ContactMessage;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class ContactMessageDatatable extends Datatable
{
    public string $model = ContactMessage::class;
    public string $status = '';
    public array $disabledRoutes = ['create', 'edit'];

    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => ['except' => ''],
    ];

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by name, email or subject...');
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Filter by Status'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All statuses'),
                'options' => [
                    'new' => __('New'),
                    'read' => __('Read'),
                    'replied' => __('Replied'),
                ],
                'selected' => $this->status,
            ],
        ];
    }

    protected function getPermissions(): array
    {
        return [
            'view' => 'contact_message.view',
            'delete' => 'contact_message.delete',
        ];
    }

    public function getRoutes(): array
    {
        return [
            'view' => 'admin.contact-messages.show',
            'delete' => 'admin.contact-messages.destroy',
        ];
    }

    protected function getItemRouteParameters($item): array
    {
        return ['contactMessage' => $item->id];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortable' => true,
                'sortBy' => 'first_name',
                'searchable' => true,
            ],
            [
                'id' => 'email',
                'title' => __('Email'),
                'sortable' => true,
                'sortBy' => 'email',
                'searchable' => true,
            ],
            [
                'id' => 'subject',
                'title' => __('Subject'),
                'sortable' => true,
                'sortBy' => 'subject',
                'searchable' => true,
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
            ],
            [
                'id' => 'created_at',
                'title' => __('Received'),
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
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('subject', 'like', $search);
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status));

        return $this->sortQuery($query);
    }

    public function renderNameColumn(ContactMessage $message): string
    {
        $name = e($message->full_name);
        $isNew = $message->status === 'new';
        $fontWeight = $isNew ? 'font-semibold' : 'font-normal';

        return '<span class="' . $fontWeight . ' text-gray-900 dark:text-white">' . $name . '</span>';
    }

    public function renderEmailColumn(ContactMessage $message): string
    {
        return '<a href="mailto:' . e($message->email) . '" class="text-blue-600 hover:underline dark:text-blue-400">' . e($message->email) . '</a>';
    }

    public function renderSubjectColumn(ContactMessage $message): string
    {
        $subject = e(Str::limit($message->subject, 40));
        $isNew = $message->status === 'new';
        $fontWeight = $isNew ? 'font-semibold' : 'font-normal';

        return '<span class="' . $fontWeight . ' text-gray-900 dark:text-white">' . $subject . '</span>';
    }

    public function renderStatusColumn(ContactMessage $message): string
    {
        $statusClasses = [
            'new' => 'badge badge-primary',
            'read' => 'badge badge-secondary',
            'replied' => 'badge badge-success',
        ];

        $statusLabels = [
            'new' => __('New'),
            'read' => __('Read'),
            'replied' => __('Replied'),
        ];

        $class = $statusClasses[$message->status] ?? 'badge badge-secondary';
        $label = $statusLabels[$message->status] ?? ucfirst($message->status);

        return '<span class="' . $class . '">' . e($label) . '</span>';
    }

    public function renderCreatedAtColumn($message): string
    {
        return $message->created_at
            ? e($message->created_at->timezone(config('app.timezone', 'UTC'))->format('M j, Y g:i A'))
            : '—';
    }

    public function render(): Renderable
    {
        $this->headers = $this->getHeaders();

        return view('livewire.datatable.contact-message-datatable', [
            'headers' => $this->headers,
            'data' => $this->getData(),
            'perPage' => $this->perPage,
            'perPageOptions' => $this->perPageOptions,
        ]);
    }
}
