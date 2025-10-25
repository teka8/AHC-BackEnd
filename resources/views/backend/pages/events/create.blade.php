<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters('filter.event.create.after_breadcrumbs', '') !!}

    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" data-prevent-unsaved-changes x-data="eventForm()">
        @csrf

        @include('backend.pages.events.partials.form', [
            'event' => null,
            'mode' => 'create'
        ])
    </form>
    
    {!! Hook::applyFilters('filter.event.create.after_form', '') !!}


    @push('scripts')
        {{-- Quill editor for description --}}
        <x-quill-editor :editor-id="'description'" height="300px" maxHeight="-1" />

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('eventForm', () => ({
                    event_type: @json(old('event_type', 'in-person')),
                    register_on_site: @json((bool) old('register_on_site', false)),
                    // no schedule field in the new migration - keep empty
                    addSlot() { /* no-op */ },
                    removeSlot() { /* no-op */ }
                }))
            });
        </script>
    @endpush
</x-layouts.backend-layout>
