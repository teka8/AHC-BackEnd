<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(PageFilterHook::PAGE_CREATE_AFTER_BREADCRUMBS, '', $postType) !!}

    <form action="{{ route('admin.pages.store', $postType) }}" method="POST" enctype="multipart/form-data"
        data-prevent-unsaved-changes>
        @csrf
        @include('backend.pages.posts.partials.form', [
            'post' => null,
            'selectedTerms' => [],
            'postType' => 'news',
            'postTypeModel' => $postTypeModel,
            'taxonomies' => $taxonomies ?? [],
            'parentPosts' => $parentPosts ?? [],
            'mode' => 'create',
        ])
    </form>

    {!! Hook::applyFilters(PageFilterHook::AFTER_PAGE_FORM, '', $postType) !!}

    @push('scripts')
        <x-quill-editor :editor-id="'content'" height="200px" maxHeight="-1" />
    @endpush
</x-layouts.backend-layout>
