<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $scholarship->title }}</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Program Type</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst($scholarship->program_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Deadline</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $scholarship->deadline->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Available Slots</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $scholarship->available_slots }}</p>
                        </div>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($scholarship->status === 'open') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($scholarship->status === 'closed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @endif">
                    {{ ucfirst($scholarship->status) }}
                </span>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($scholarship->description)) !!}
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Eligibility Criteria -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Eligibility Criteria</h3>
                <div class="prose dark:prose-invert max-w-none text-sm">
                    {!! nl2br(e($scholarship->eligibility_criteria)) !!}
                </div>
            </div>

            <!-- Benefits -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Benefits</h3>
                @if(is_array($scholarship->benefits))
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-900 dark:text-white">
                        @foreach($scholarship->benefits as $benefit)
                            <li>{{ $benefit }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($scholarship->benefits)) !!}</p>
                @endif
            </div>

            <!-- Required Documents -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Required Documents</h3>
                @if(is_array($scholarship->required_documents))
                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-900 dark:text-white">
                        @foreach($scholarship->required_documents as $document)
                            <li>{{ $document }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($scholarship->required_documents)) !!}</p>
                @endif
            </div>

            <!-- Coverage & Amount -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Coverage & Amount</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Coverage</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $scholarship->coverage }}</dd>
                    </div>
                    @if($scholarship->amount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($scholarship->amount) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Applications Stats -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $scholarship->applications->count() }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Under Review</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $scholarship->applications->where('status', 'under-review')->count() }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Accepted</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $scholarship->applications->where('status', 'accepted')->count() }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Rejected</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $scholarship->applications->where('status', 'rejected')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                    Back to List
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('admin.scholarship-applications.index', ['scholarship_id' => $scholarship->id]) }}" class="btn btn-primary">
                        <iconify-icon icon="lucide:file-text" class="mr-2"></iconify-icon>
                        View Applications
                    </a>
                    @can('update', $scholarship)
                    <a href="{{ route('admin.scholarships.edit', $scholarship) }}" class="btn btn-primary">
                        <iconify-icon icon="lucide:edit" class="mr-2"></iconify-icon>
                        Edit
                    </a>
                    @endcan
                    @can('delete', $scholarship)
                    <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this scholarship?')">
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
