<!-- Bulk Delete Modal -->
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
            <h3 class="font-semibold tracking-wide text-gray-700 dark:text-white ml-2">
                <span x-text="selectedDocuments.length === 1 ? '{{ __('Delete Document') }}' : '{{ __('Delete Selected Documents') }}'"></span>
            </h3>
            <button x-on:click="bulkDeleteModalOpen = false"
                    aria-label="close modal"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white ml-auto">
                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
            </button>
        </div>
        
        <div class="px-4 text-center">
            <p class="text-gray-500 dark:text-gray-300" x-text="selectedDocuments.length === 1 ? '{{ __('Are you sure you want to delete this document? This action cannot be undone.') }}' : '{{ __('Are you sure you want to delete the selected documents? This action cannot be undone.') }}'">
            </p>
        </div>
        
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">
            <button type="button" 
                    x-on:click="bulkDeleteModalOpen = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                {{ __('Cancel') }}
            </button>
            
            <button type="button" 
                    x-on:click="performBulkDelete()"
                    x-bind:disabled="bulkDeleteLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50 disabled:cursor-not-allowed dark:focus:ring-red-800">
                <template x-if="!bulkDeleteLoading">
                    <span x-text="selectedDocuments.length === 1 ? '{{ __('Delete') }}' : '{{ __('Delete All') }}'"></span>
                </template>
                <template x-if="bulkDeleteLoading">
                    <span class="flex items-center">
                        <iconify-icon icon="lucide:loader-2" class="animate-spin mr-2 w-4 h-4"></iconify-icon>
                        {{ __('Deleting...') }}
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>