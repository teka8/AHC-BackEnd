<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $application->venture_name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $application->tagline }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Submitted: {{ $application->submitted_at ? $application->submitted_at->format('M d, Y') : 'Draft' }}
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($application->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @elseif($application->status === 'under-review') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @endif">
                    {{ ucfirst(str_replace('-', ' ', $application->status)) }}
                </span>
            </div>
        </div>

        <!-- Status Update Form -->
        @can('update', $application)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.venture-applications.update-status', $application) }}">
                @csrf
                <div class="flex gap-4">
                    <select name="status" class="form-select flex-1">
                        <option value="draft" {{ $application->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $application->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="under-review" {{ $application->status === 'under-review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="lucide:save" class="mr-2"></iconify-icon>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
        @endcan

        <!-- Contact Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->contact_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->contact_email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->contact_phone }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($application->website)
                            <a href="{{ $application->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $application->website }}</a>
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Venture Details -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Venture Details</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{!! nl2br(e($application->description)) !!}</dd>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Focus Area</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('-', ' ', $application->focus_area)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stage</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($application->stage) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->country }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Founded</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->founded_year }}</dd>
                    </div>
                </div>
            </dl>
        </div>

        <!-- Problem & Solution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Problem Statement</h3>
                <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($application->problem_statement)) !!}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Solution Description</h3>
                <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($application->solution_description)) !!}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.venture-applications.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                    Back to List
                </a>
                @can('delete', $application)
                <form method="POST" action="{{ route('admin.venture-applications.destroy', $application) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this application?')">
                        <iconify-icon icon="lucide:trash-2" class="mr-2"></iconify-icon>
                        Delete
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
