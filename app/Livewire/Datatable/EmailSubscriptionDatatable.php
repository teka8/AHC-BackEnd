<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\EmailSubscription;
use Illuminate\Contracts\Support\Renderable;
use Spatie\QueryBuilder\QueryBuilder;

class EmailSubscriptionDatatable extends Datatable
{
    public string $model = EmailSubscription::class;
    public string $status = '';
    public string $preference = '';
    public array $disabledRoutes = ['create', 'view', 'edit'];
    public string $exportRoute = '';

    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => ['except' => ''],
        'preference' => ['except' => ''],
    ];

    public function mount(): void
    {
        parent::mount();
        $this->exportRoute = route('admin.subscriptions.bulk-export');
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search by email or name...');
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPreference(): void
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
                    'active' => __('Active'),
                    'unsubscribed' => __('Unsubscribed'),
                ],
                'selected' => $this->status,
            ],
            [
                'id' => 'preference',
                'label' => __('Interest'),
                'filterLabel' => __('Filter by Interest'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All interests'),
                'options' => [
                    'news' => __('News'),
                    'events' => __('Events'),
                    'announcements' => __('Announcements'),
                    'scholarships' => __('Scholarships'),
                ],
                'selected' => $this->preference,
            ],
        ];
    }

    protected function getPermissions(): array
    {
        return [
            'view' => 'subscription.view',
            'edit' => 'subscription.update',
            'delete' => 'subscription.delete',
        ];
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'email',
                'title' => __('Email'),
                'sortable' => true,
                'sortBy' => 'email',
                'searchable' => true,
            ],
            [
                'id' => 'name',
                'title' => __('Name'),
                'sortable' => true,
                'sortBy' => 'name',
                'searchable' => true,
            ],
            [
                'id' => 'preferences',
                'title' => __('Interests'),
                'sortable' => false,
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'unsubscribed_at',
            ],
            [
                'id' => 'last_notified_at',
                'title' => __('Last Notified'),
                'sortable' => true,
                'sortBy' => 'last_notified_at',
            ],
            [
                'id' => 'created_at',
                'title' => __('Subscribed'),
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
                    $inner->where('email', 'like', $search)
                        ->orWhere('name', 'like', $search);
                });
            })
            ->when($this->status === 'active', fn ($q) => $q->whereNull('unsubscribed_at'))
            ->when($this->status === 'unsubscribed', fn ($q) => $q->whereNotNull('unsubscribed_at'))
            ->when($this->preference, function ($q) {
                $column = 'wants_' . $this->preference;
                if (in_array($column, ['wants_news', 'wants_events', 'wants_announcements', 'wants_scholarships'], true)) {
                    $q->where($column, true);
                }
            });

        return $this->sortQuery($query);
    }

    public function renderPreferencesColumn(EmailSubscription $subscription): string
    {
        $map = [
            'wants_news' => __('News'),
            'wants_events' => __('Events'),
            'wants_announcements' => __('Announcements'),
            'wants_scholarships' => __('Scholarships'),
        ];

        $selected = collect($map)
            ->filter(fn ($_label, $key) => (bool) $subscription->{$key})
            ->values()
            ->all();

        if (empty($selected)) {
            return '<span class="badge badge-soft-secondary">' . __('None') . '</span>';
        }

        return collect($selected)
            ->map(fn ($label) => '<span class="badge badge-soft-primary me-1 mb-1 inline-flex">' . e($label) . '</span>')
            ->implode('');
    }

    public function renderStatusColumn(EmailSubscription $subscription): string
    {
        $isActive = $subscription->unsubscribed_at === null;
        $class = $isActive ? 'badge badge-success' : 'badge badge-danger';
        $label = $isActive ? __('Active') : __('Unsubscribed');

        return '<span class="' . $class . '">' . e($label) . '</span>';
    }

    public function renderLastNotifiedAtColumn(EmailSubscription $subscription): string
    {
        return $subscription->last_notified_at
            ? e($subscription->last_notified_at->timezone(config('app.timezone', 'UTC'))->format('M j, Y g:i A'))
            : '—';
    }

    public function renderCreatedAtColumn($subscription): string
    {
        return $subscription->created_at
            ? e($subscription->created_at->timezone(config('app.timezone', 'UTC'))->format('M j, Y'))
            : '—';
    }

    public function renderAfterActionDelete($subscription): string|Renderable
    {
        return view('backend.pages.subscriptions.partials.status-action', [
            'subscription' => $subscription,
        ]);
    }

    public function unsubscribe(int $id): void
    {
        $subscription = EmailSubscription::query()->find($id);

        if (! $subscription) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Subscriber not found'),
                'message' => __('The selected subscriber could not be located.'),
            ]);
            return;
        }

        $this->authorize('update', $subscription);

        if ($subscription->unsubscribed_at) {
            $this->dispatch('notify', [
                'variant' => 'info',
                'title' => __('Already unsubscribed'),
                'message' => __('This subscriber is already unsubscribed.'),
            ]);
            return;
        }

        $subscription->markUnsubscribed();

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Subscriber updated'),
            'message' => __('The subscriber will no longer receive updates.'),
        ]);

        $this->resetPage();
    }

    public function resubscribe(int $id): void
    {
        $subscription = EmailSubscription::query()->find($id);

        if (! $subscription) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Subscriber not found'),
                'message' => __('The selected subscriber could not be located.'),
            ]);
            return;
        }

        $this->authorize('update', $subscription);

        $subscription->markSubscribed();
        $subscription->regenerateUnsubscribeToken();

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Subscriber updated'),
            'message' => __('The subscriber has been reactivated.'),
        ]);

        $this->resetPage();
    }
}
