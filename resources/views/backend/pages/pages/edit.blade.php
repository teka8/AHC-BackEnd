<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" data-prevent-unsaved-changes>
        @csrf
        @method('PUT')
        @include('backend.pages.pages.partials.form', ['page' => $page, 'mode' => 'edit'])
    </form>

   @push('scripts')
    <x-quill-editor editor-id="content" height="400px" />


@endpush
</x-layouts.backend-layout>