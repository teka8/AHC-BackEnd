<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(PageFilterHook::PAGE_EDIT_AFTER_BREADCRUMBS, '', $postType) !!}

    <x-card>
        <form
            action="{{ route('admin.pages.update', [$postType, $post->id]) }}"
            method="POST"
            class="space-y-6"
            enctype="multipart/form-data"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')

            @include('backend.pages.posts.partials.form', [
                'post' => $post,
                'selectedTerms' => $selectedTerms ?? [],
                'postType' => $postType,
                'postTypeModel' => $postTypeModel,
                'taxonomies' => $taxonomies ?? [],
                'parentPosts' => $parentPosts ?? [],
                'mode' => 'edit',
            ])
        </form>
    </x-card>

    {!! Hook::applyFilters(PageFilterHook::AFTER_PAGE_FORM, '', $postType) !!}

    @push('scripts')
        <x-quill-editor :editor-id="'content'" height="200px" maxHeight="-1" />

        <script>
// Change news status function
async function changeNewsStatus(newsId, action) {
    try {
        const comment = prompt('{{ __("Add a comment (optional):") }}', '');
        if (comment === null) return; // User cancelled

        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = `
            <iconify-icon icon="lucide:loader-2" class="animate-spin w-4 h-4 mr-2"></iconify-icon>
            {{ __('Processing...') }}
        `;
        button.disabled = true;

        const response = await fetch(`/admin/news/${newsId}/change-status`, {
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
        console.error('Status change failed:', error);
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
