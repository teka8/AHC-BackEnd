<?php

namespace App\Exports;

use App\Models\EmailSubscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubscribersExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $selectedIds;

    public function __construct(array $selectedIds = [])
    {
        $this->selectedIds = $selectedIds;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = EmailSubscription::query()->orderBy('created_at', 'desc');

        // If specific IDs are provided, filter by them
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }

        return $query->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Email',
            'Name',
            'Wants News',
            'Wants Events',
            'Wants Announcements',
            'Wants Scholarships',
            'Status',
            'Subscribed Date',
            'Last Notified',
        ];
    }

    /**
     * Map each subscription to a row in the Excel file
     */
    public function map($subscription): array
    {
        return [
            $subscription->email,
            $subscription->name ?? '-',
            $subscription->wants_news ? 'Yes' : 'No',
            $subscription->wants_events ? 'Yes' : 'No',
            $subscription->wants_announcements ? 'Yes' : 'No',
            $subscription->wants_scholarships ? 'Yes' : 'No',
            $subscription->unsubscribed_at ? 'Unsubscribed' : 'Active',
            $subscription->created_at?->format('Y-m-d H:i:s') ?? '-',
            $subscription->last_notified_at?->format('Y-m-d H:i:s') ?? 'Never',
        ];
    }
}
