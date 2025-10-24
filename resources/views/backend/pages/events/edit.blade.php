<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" data-prevent-unsaved-changes>
        @csrf
        @method('PUT')
        @include('backend.pages.events.partials.form', ['event' => $event, 'mode' => 'edit'])
    </form>

    @push('scripts')
        <x-quill-editor editor-id="description" height="200px" />

        <script>
            // Change event status function
async function changeEventStatus(eventId, action) {
    try {
        let comment = '';
        
        // Ask for comment for certain actions
        if (['reject', 'cancel', 'send_back'].includes(action)) {
            comment = prompt('{{ __("Please provide a reason (optional):") }}', '');
            if (comment === null) return; // User cancelled
        } else {
            comment = prompt('{{ __("Add a note (optional):") }}', '');
            if (comment === null) return; // User cancelled
        }

        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = `
            <iconify-icon icon="lucide:loader-2" class="animate-spin w-4 h-4 mr-2"></iconify-icon>
            {{ __('Processing...') }}
        `;
        button.disabled = true;

        const response = await fetch(`/admin/events/${eventId}/change-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                comment: comment
            })
        });

        const data = await response.json();

        if (data.success) {
            if (window.showToast) {
                window.showToast('success', '{{ __('Success') }}', data.message);
            }
            // Refresh the page to show updated status
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(data.message);
        }

    } catch (error) {
        console.error('Event status change failed:', error);
        if (window.showToast) {
            window.showToast('error', '{{ __('Error') }}', error.message);
        }
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
    }
}
        </script>
    @endpush
</x-layouts.backend-layout>