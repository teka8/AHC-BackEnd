<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div class="flex items-start gap-6">
                    @if($ahcLeader->image_url)
                        <img 
                            src="{{ $ahcLeader->image_url }}" 
                            alt="{{ $ahcLeader->name }}" 
                            class="w-32 h-32 rounded-lg object-cover shadow-md"
                        >
                    @else
                        <div class="w-32 h-32 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <iconify-icon icon="lucide:user" class="w-16 h-16 text-gray-400"></iconify-icon>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ahcLeader->name }}</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mt-1">{{ $ahcLeader->position }}</p>
                        <div class="mt-4 flex items-center gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sort Order') }}</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $ahcLeader->sort_order }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Created') }}</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $ahcLeader->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($ahcLeader->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ $ahcLeader->is_active ? __('Active') : __('Inactive') }}
                </span>
            </div>
        </div>

        <!-- Description -->
        @if($ahcLeader->description)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Description') }}</h3>
            <div class="prose dark:prose-invert max-w-none">
                {!! $ahcLeader->description !!}
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.ahc-leaders.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                    {{ __('Back to List') }}
                </a>
                <div class="flex gap-2">
                    @can('update', $ahcLeader)
                    <a href="{{ route('admin.ahc-leaders.edit', $ahcLeader) }}" class="btn btn-primary">
                        <iconify-icon icon="lucide:edit" class="mr-2"></iconify-icon>
                        {{ __('Edit') }}
                    </a>
                    @endcan
                    @can('delete', $ahcLeader)
                    <form method="POST" action="{{ route('admin.ahc-leaders.destroy', $ahcLeader) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this leader?') }}')">
                            <iconify-icon icon="lucide:trash-2" class="mr-2"></iconify-icon>
                            {{ __('Delete') }}
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
