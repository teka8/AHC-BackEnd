<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <form action="{{ route('admin.ahc-leaders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('backend.pages.ahc-leaders.partials.form', ['leader' => null])
    </form>

    @push('scripts')
        <x-quill-editor :editor-id="'description'" height="300px" maxHeight="-1" />
    @endpush
</x-layouts.backend-layout>
