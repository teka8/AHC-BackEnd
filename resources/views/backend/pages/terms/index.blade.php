<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(TermFilterHook::TERM_AFTER_BREADCRUMBS, '', $taxonomyModel) !!}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            @include('backend.pages.terms.partials.form', [
                'postType' => $postType ?? 'announcement',
                'availablePostTypes' => $availablePostTypes ?? collect(),
                'postTypeModel' => $postTypeModel ?? null,
            ])
        </div>

        <div class="lg:col-span-2 space-y-6">
            @livewire('datatable.term-datatable', [
                'taxonomy' => $taxonomy,
                'postType' => $postType ?? 'announcement',
            ])
        </div>
    </div>
</x-layouts.backend-layout>