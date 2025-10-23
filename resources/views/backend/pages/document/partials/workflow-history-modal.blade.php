<div x-cloak x-show="workflowHistoryModalOpen"
     x-transition.opacity.duration.200ms
     x-trap.inert.noscroll="workflowHistoryModalOpen"
     x-on:keydown.esc.window="workflowHistoryModalOpen = false"
     x-on:click.self="workflowHistoryModalOpen = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md"
     role="dialog"
     aria-modal="true">
    
    <div x-show="workflowHistoryModalOpen"
         x-transition:enter="transition ease-out duration-200 delay-100"
         x-transition:enter-start="opacity-0 scale-50"
         x-transition:enter-end="opacity-100 scale-100"
         class="flex max-w-4xl w-full flex-col gap-4 overflow-hidden rounded-md border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-700 dark:text-gray-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800 sticky top-0 bg-white dark:bg-gray-700 z-10">
            <h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">
                <iconify-icon icon="lucide:history" class="mr-2"></iconify-icon>
                {{ __('Document Workflow History') }}
            </h3>
            <button x-on:click="workflowHistoryModalOpen = false"
                    aria-label="close modal"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white">
                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
            </button>
        </div>
        
        <div class="px-6 pb-6">
            <!-- Loading State -->
            <div id="workflow-history-loading" class="text-center py-8">
                <iconify-icon icon="lucide:loader-2" class="animate-spin text-2xl text-gray-400 mb-2"></iconify-icon>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Loading workflow history...') }}</p>
            </div>

            <!-- Empty State -->
            <div id="workflow-history-empty" class="hidden text-center py-8">
                <iconify-icon icon="lucide:inbox" class="text-4xl text-gray-300 dark:text-gray-600 mb-4"></iconify-icon>
                <p class="text-gray-500 dark:text-gray-400">{{ __('No workflow history found.') }}</p>
            </div>

            <!-- History List -->
            <div id="workflow-history-content" class="hidden space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-gray-800">
            <button x-on:click="workflowHistoryModalOpen = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</div>