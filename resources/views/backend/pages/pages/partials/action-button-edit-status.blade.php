<button 
    wire:click="updatePageStatus({{ $post->id }}, 'edited')"
    class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/30"
    title="{{ __('Mark as Edited') }}">
    <iconify-icon icon="lucide:edit-3" class="w-3 h-3 mr-1"></iconify-icon>
    {{ __('Mark as Edited') }}
</button>