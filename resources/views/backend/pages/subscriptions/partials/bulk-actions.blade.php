\u003cdiv class="relative flex items-center justify-center" x-show="selectedItems.length > 0" x-data="{ open: false}">
    \u003cbutton @click="open = !open" class="btn-secondary flex items-center justify-center gap-2 text-sm" type="button">
        \u003ciconify-icon icon="lucide:more-vertical">\u003c/iconify-icon>
        \u003cspan>{{ __('Bulk Actions') }} (\u003cspan x-text="selectedItems.length">\u003c/span>)\u003c/span>
        \u003ciconify-icon icon="lucide:chevron-down">\u003c/iconify-icon>
    \u003c/button>
    \u003cdiv x-show="open" @click.outside="open = false" x-transition
         class="absolute right-0 top-10 mt-2 w-48 rounded-md shadow bg-white dark:bg-gray-700 z-10 p-2">
        \u003cul class="space-y-2">
            {{-- Export to Excel Action --}}
            \u003cli>
                \u003cform action="{{ route('admin.subscriptions.bulk-export') }}" method="POST">
                    @csrf
                    \u003ctemplate x-for="id in selectedItems" :key="id">
                        \u003cinput type="hidden" name="selected[]" :value="id">
                    \u003c/template>
                    \u003cbutton type="submit"
                            class="w-full cursor-pointer flex items-center gap-2 text-sm text-green-600 dark:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/30 dark:hover:text-green-400 px-2 py-1.5 rounded transition-colors duration-300">
                        \u003ciconify-icon icon="lucide:download">\u003c/iconify-icon>
                        {{ __('Export to Excel') }}
                    \u003c/button>
                \u003c/form>
            \u003c/li>
            
            {{-- Delete Action --}}
            \u003cli class="cursor-pointer flex items-center gap-2 text-sm text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-500 dark:hover:text-red-50 px-2 py-1.5 rounded transition-colors duration-300"
                @click="open = false; bulkDeleteModalOpen = true">
                \u003ciconify-icon icon="lucide:trash">\u003c/iconify-icon>
                {{ __('Delete Selected') }}
            \u003c/li>
        \u003c/ul>
    \u003c/div>
\u003c/div>
