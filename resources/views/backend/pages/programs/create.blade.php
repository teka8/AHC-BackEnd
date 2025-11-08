<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data" x-data="programForm()">
        @csrf
        @include('backend.pages.programs.partials.form', ['program' => null])
    </form>

    @push('scripts')
        {{-- Quill editor for description --}}
        <x-quill-editor :editor-id="'description'" height="300px" maxHeight="-1" />

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('programForm', () => ({
                    // Add any Alpine.js data or functions specific to the program form here
                }))
            });
        </script>
    @endpush
</x-layouts.backend-layout>
