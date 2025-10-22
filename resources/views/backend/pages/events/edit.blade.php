<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" data-prevent-unsaved-changes>
        @csrf
        @method('PUT')
        @include('backend.pages.events.partials.form', ['event' => $event, 'mode' => 'edit'])
    </form>

    @push('scripts')
        <x-quill-editor editor-id="description" height="200px" />
    @endpush
</x-layouts.backend-layout>