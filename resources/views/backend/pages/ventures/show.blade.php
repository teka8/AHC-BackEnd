<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $venture->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $venture->tagline }}</p>
                </div>
                <div class="flex gap-2">
                    @if($venture->featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            <iconify-icon icon="lucide:star" class="mr-1"></iconify-icon>
                            Featured
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        {{ ucfirst($venture->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Focus Area</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ucfirst(str_replace('-', ' ', $venture->focus_area)) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stage</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ ucfirst($venture->stage) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Country</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $venture->country }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Votes</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ number_format($venture->votes_count) }}</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($venture->description)) !!}
            </div>
        </div>

        <!-- Team & Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Founders</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $venture->founders }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Size</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $venture->team_size }} members</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Founded</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $venture->founded_year }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Impact Metrics</h3>
                <dl class="space-y-3">
                    @if($venture->funding_raised)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Funding Raised</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($venture->funding_raised) }}</dd>
                    </div>
                    @endif
                    @if($venture->patients_impacted)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Patients Impacted</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ number_format($venture->patients_impacted) }}</dd>
                    </div>
                    @endif
                    @if($venture->countries_reached)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Countries Reached</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $venture->countries_reached }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.ventures.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                    Back to List
                </a>
                <div class="flex gap-2">
                    @can('update', $venture)
                    <a href="{{ route('admin.ventures.edit', $venture) }}" class="btn btn-primary">
                        <iconify-icon icon="lucide:edit" class="mr-2"></iconify-icon>
                        Edit
                    </a>
                    @endcan
                    @can('delete', $venture)
                    <form method="POST" action="{{ route('admin.ventures.destroy', $venture) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this venture?')">
                            <iconify-icon icon="lucide:trash-2" class="mr-2"></iconify-icon>
                            Delete
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
