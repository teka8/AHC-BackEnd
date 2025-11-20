<div class="relative flex items-center justify-center" x-show="selectedItems.length > 0" x-data="{ open: false }">
    <button @click="open = !open" class="btn-secondary flex items-center justify-center gap-2 text-sm" type="button">
        <iconify-icon icon="lucide:more-vertical"></iconify-icon>
        <span>{{ __('Bulk Actions') }} (<span x-text="selectedItems.length"></span>)</span>
        <iconify-icon icon="lucide:chevron-down"></iconify-icon>
    </button>
    <div x-show="open" @click.outside="open = false" x-transition
        class="absolute right-0 top-10 mt-2 w-48 rounded-md shadow bg-white dark:bg-gray-700 z-10 p-2">
        <ul class="space-y-2">
            <li class="cursor-pointer flex items-center gap-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 px-2 py-1.5 rounded transition-colors duration-300"
                @click="open = false; $dispatch('start-zip-download')">
                <iconify-icon icon="lucide:download"></iconify-icon> {{ __('Download ZIP') }}
            </li>

            <li class="cursor-pointer flex items-center gap-1 text-sm text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-500 dark:hover:text-red-50 px-2 py-1.5 rounded transition-colors duration-300"
                @click="open = false; bulkDeleteModalOpen = true">
                <iconify-icon icon="lucide:trash"></iconify-icon> {{ __('Delete Selected') }}
            </li>
        </ul>
    </div>
</div>

<!-- Progress Modal -->
<div x-cloak x-show="zipProgressModalOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md"
    x-data="{
        zipProgressModalOpen: false,
        progress: 0,
        statusMessage: 'Preparing...',
        batchId: '',
        
        async startZipDownload() {
            if (selectedItems.length === 0) return;
            
            this.zipProgressModalOpen = true;
            this.progress = 0;
            this.statusMessage = 'Initializing...';
            this.batchId = 'batch_' + Date.now();
            
            const chunkSize = 5; // Process 5 applicants at a time
            const total = selectedItems.length;
            const chunks = [];
            
            for (let i = 0; i < total; i += chunkSize) {
                chunks.push(selectedItems.slice(i, i + chunkSize));
            }
            
            for (let i = 0; i < chunks.length; i++) {
                this.statusMessage = `Processing batch ${i + 1} of ${chunks.length}...`;
                try {
                    await $wire.processZipChunk(chunks[i], this.batchId);
                    this.progress = Math.round(((i + 1) / chunks.length) * 100);
                } catch (error) {
                    console.error('Chunk failed', error);
                    this.statusMessage = 'Error processing chunk. Aborting.';
                    return;
                }
            }
            
            this.statusMessage = 'Finalizing ZIP file...';
            this.progress = 100;
            
            try {
                const response = await $wire.finalizeZipDownload(this.batchId);
                // Livewire handles the download response automatically
                setTimeout(() => {
                    this.zipProgressModalOpen = false;
                }, 1000);
            } catch (error) {
                console.error('Finalization failed', error);
                this.statusMessage = 'Error finalizing ZIP.';
            }
        }
    }"
    @start-zip-download.window="startZipDownload()">
    
    <div class="w-full max-w-md rounded-md border border-gray-100 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('Generating ZIP File') }}
        </h3>
        
        <div class="mb-2 flex justify-between text-sm text-gray-600 dark:text-gray-400">
            <span x-text="statusMessage"></span>
            <span x-text="progress + '%'"></span>
        </div>
        
        <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
            <div class="h-2.5 rounded-full bg-primary-600 transition-all duration-300"
                :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
</div>

<div x-cloak x-show="bulkDeleteModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="bulkDeleteModalOpen"
    x-on:keydown.esc.window="bulkDeleteModalOpen = false" x-on:click.self="bulkDeleteModalOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog"
    aria-modal="true" aria-labelledby="bulk-delete-modal-title">
    <div x-show="bulkDeleteModalOpen"
        x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
        x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
        class="flex max-w-md flex-col gap-4 overflow-hidden rounded-md border border-outline border-gray-100 dark:border-gray-800 bg-white text-on-surface dark:border-outline-dark dark:bg-gray-700 dark:text-gray-300">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-800">
            <div
                class="flex items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 p-1">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 id="bulk-delete-modal-title" class="font-semibold tracking-wide text-gray-700 dark:text-white">
                {{ __('Delete Selected Items') }}
            </h3>
            <button x-on:click="bulkDeleteModalOpen = false" aria-label="close modal"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor"
                    fill="none" stroke-width="1.4" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-4 text-center">
            <p class="text-gray-500 dark:text-gray-300">
                {{ __('Are you sure you want to delete the selected items?') }}
                {{ __('This action cannot be undone.') }}
            </p>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">
            @if ($bulkDeleteAction['url'])
                <form id="bulk-delete-form" action="{{ $bulkDeleteAction['url'] }}" method="POST">
                    @method($bulkDeleteAction['method'])
                    @csrf

                    <template x-for="id in selectedItems" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>

                    <button type="button" x-on:click="bulkDeleteModalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                        {{ __('No, Cancel') }}
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">
                        {{ __('Yes, Delete') }}
                    </button>
                </form>
            @else
                <button type="button" x-on:click="bulkDeleteModalOpen = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                    {{ __('No, Cancel') }}
                </button>
                <button type="button" @click="bulkDeleteModalOpen = false"
                    @if ($enableLivewire ?? true) wire:click="bulkDelete"
                                    wire:loading.attr="disabled" @endif
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800">
                    {{ __('Yes, Delete') }}
                </button>
            @endif
        </div>
    </div>
</div>
