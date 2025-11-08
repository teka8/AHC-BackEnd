<!-- Change Status Modal -->
<div x-cloak x-show="changeStatusModalOpen"
     x-transition.opacity.duration.200ms
     x-trap.inert.noscroll="changeStatusModalOpen"
     x-on:keydown.esc.window="changeStatusModalOpen = false"
     x-on:click.self="changeStatusModalOpen = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md"
     role="dialog"
     aria-modal="true">
    
    <div x-show="changeStatusModalOpen"
         x-transition:enter="transition ease-out duration-200 delay-100"
         x-transition:enter-start="opacity-0 scale-50"
         x-transition:enter-end="opacity-100 scale-100"
         class="flex max-w-md flex-col gap-4 overflow-hidden rounded-md border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-700 dark:text-gray-300">
        
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-800">
            <div class="flex items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 p-1">
                <iconify-icon icon="lucide:git-branch" class="w-6 h-6"></iconify-icon>
            </div>
            <h3 class="font-semibold tracking-wide text-gray-700 dark:text-white ml-2" x-text="changeStatusModalTitle">
                {{ __('Change Program Status') }}
            </h3>
            <button x-on:click="changeStatusModalOpen = false"
                    aria-label="close modal"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white ml-auto">
                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
            </button>
        </div>
        
        <div class="px-4">
            <p class="text-gray-500 dark:text-gray-300 mb-4" x-text="changeStatusModalMessage">
                {{ __('Are you sure you want to change the program status?') }}
            </p>
            
            <!-- Comment Input -->
            <div class="mb-4">
                <label for="status-comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Comment (Optional)') }}
                </label>
                <textarea id="status-comment" 
                          x-model="changeStatusComment"
                          rows="3"
                          class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                          placeholder="{{ __('Add a comment about this status change...') }}"></textarea>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">
            <button type="button" 
                    x-on:click="changeStatusModalOpen = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                {{ __('Cancel') }}
            </button>
            
            <button type="button" 
                    x-on:click="performStatusChange()"
                    x-bind:disabled="changeStatusLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed dark:focus:ring-blue-800">
                <template x-if="!changeStatusLoading">
                    <span x-text="changeStatusButtonText">{{ __('Change Status') }}</span>
                </template>
                <template x-if="changeStatusLoading">
                    <span class="flex items-center">
                        <iconify-icon icon="lucide:loader-2" class="animate-spin mr-2 w-4 h-4"></iconify-icon>
                        {{ __('Processing...') }}
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>