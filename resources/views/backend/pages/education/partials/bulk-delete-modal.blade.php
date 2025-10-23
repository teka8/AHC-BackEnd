<div x-cloak x-show="bulkDeleteModalOpen"
     x-transition.opacity.duration.200ms
     x-trap.inert.noscroll="bulkDeleteModalOpen"
     x-on:keydown.esc.window="bulkDeleteModalOpen = false"
     x-on:click.self="bulkDeleteModalOpen = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md"
     role="dialog"
     aria-modal="true">
    
    <div x-show="bulkDeleteModalOpen"
         x-transition:enter="transition ease-out duration-200 delay-100"
         x-transition:enter-start="opacity-0 scale-50"
         x-transition:enter-end="opacity-100 scale-100"
         class="flex max-w-md flex-col gap-4 overflow-hidden rounded-md border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-700 dark:text-gray-300">
        
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-800">
            <div class="flex items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 p-1">
                <iconify-icon icon="lucide:alert-triangle" class="w-6 h-6"></iconify-icon>
            </div>
            <h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">
                <template x-if="selectedDocuments.length === 1">
                    {{ __('Delete Resource') }}
                </template>
                <template x-if="selectedDocuments.length > 1">
                    {{ __('Delete Selected Resources') }}
                </template>
            </h3>
            <button x-on:click="bulkDeleteModalOpen = false"
                    aria-label="close modal"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white">
                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
            </button>
        </div>
        
        <div class="px-4 text-center">
            <template x-if="selectedDocuments.length === 1">
                <p class="text-gray-500 dark:text-gray-300">
                    {{ __('Are you sure you want to delete this resource?') }}
                    {{ __('This action cannot be undone.') }}
                </p>
            </template>
            <template x-if="selectedDocuments.length > 1">
                <p class="text-gray-500 dark:text-gray-300">
                    {{ __('Are you sure you want to delete the selected resources?') }}
                    {{ __('This action cannot be undone.') }}
                </p>
            </template>
        </div>
        
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">
            <form id="bulk-delete-form" action="{{ route('admin.education.bulk-delete') }}" method="POST">
                @method('DELETE')
                @csrf
                
                <template x-for="id in selectedDocuments" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                
                <button type="button" 
                        x-on:click="bulkDeleteModalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                    {{ __('No, Cancel') }}
                </button>
                
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">
                    <template x-if="selectedDocuments.length === 1">
                        {{ __('Yes, Delete') }}
                    </template>
                    <template x-if="selectedDocuments.length > 1">
                        {{ __('Yes, Delete All') }}
                    </template>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bulk-delete-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <iconify-icon icon="lucide:loader-2" class="animate-spin mr-1"></iconify-icon>
                {{ __('Deleting...') }}
            `;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form)
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Failed');
                setTimeout(() => location.reload(), 600);
            })
            .catch(err => {
                if (window.showToast) window.showToast('error', '{{ __('Error') }}', err.message || '{{ __('Failed to delete resources') }}');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    }
});
</script>
