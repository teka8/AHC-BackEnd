<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $application->first_name }} {{ $application->last_name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Application for: <strong>{{ $application->scholarship->title ?? 'N/A' }}</strong></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Submitted: {{ $application->submitted_at ? $application->submitted_at->format('M d, Y h:i A') : 'Draft' }}
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($application->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @elseif($application->status === 'under-review') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($application->status === 'shortlisted') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
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
            <form method="POST" action="{{ route('admin.scholarship-applications.update-status', $application) }}">
                @csrf
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <select name="status" class="form-select flex-1">
                            <option value="draft" {{ $application->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ $application->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="under-review" {{ $application->status === 'under-review' ? 'selected' : '' }}>Under Review</option>
                            <option value="shortlisted" {{ $application->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="interviewed" {{ $application->status === 'interviewed' ? 'selected' : '' }}>Interviewed</option>
                            <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="withdrawn" {{ $application->status === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2"></iconify-icon>
                            Update Status
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note (optional)</label>
                        <textarea name="note" rows="2" class="form-textarea w-full" placeholder="Add a note about this status change..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        @endcan

        <!-- Evaluations Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Evaluations ({{ $application->evaluations->count() }})</h3>
                <a href="{{ route('admin.scholarship-evaluation.create', $application) }}" class="btn btn-primary">
                    <iconify-icon icon="lucide:plus" class="mr-2"></iconify-icon>
                    Add Evaluation
                </a>
            </div>

            @if($application->evaluations->count() > 0)
                <div class="space-y-4">
                    @foreach($application->evaluations as $evaluation)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reviewed by: {{ $evaluation->reviewer->first_name ?? 'N/A' }} {{ $evaluation->reviewer->last_name ?? '' }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($evaluation->recommendation === 'strong-accept') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                        @elseif($evaluation->recommendation === 'accept') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($evaluation->recommendation === 'waitlist') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        @if($evaluation->recommendation === 'strong-accept')
                                            ✓✓ Strong Accept
                                        @elseif($evaluation->recommendation === 'accept')
                                            ✓ Accept
                                        @elseif($evaluation->recommendation === 'waitlist')
                                            ⏸ Waitlist
                                        @else
                                            ✗ Reject
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-3">
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Overall</div>
                                        <div class="text-xl font-bold text-blue-600">{{ number_format($evaluation->overall_score, 1) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Academic</div>
                                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ number_format($evaluation->academic_performance_score, 1) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Motivation</div>
                                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ number_format($evaluation->motivation_score, 1) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Research</div>
                                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ number_format($evaluation->research_quality_score, 1) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Financial</div>
                                        <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ number_format($evaluation->financial_need_score, 1) }}</div>
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Evaluated on {{ $evaluation->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>

                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('admin.scholarship-evaluation.show', $evaluation) }}" class="btn btn-sm btn-secondary">
                                    <iconify-icon icon="lucide:eye" class="mr-1"></iconify-icon>
                                    View
                                </a>
                                <a href="{{ route('admin.scholarship-evaluation.edit', $evaluation) }}" class="btn btn-sm btn-primary">
                                    <iconify-icon icon="lucide:edit" class="mr-1"></iconify-icon>
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <iconify-icon icon="lucide:clipboard-list" class="text-gray-400 dark:text-gray-600" style="font-size: 48px;"></iconify-icon>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No evaluations yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Click "Add Evaluation" to review this application</p>
                </div>
            @endif
        </div>

        <!-- Status History -->
        @if($application->statusHistory->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status History</h3>
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($application->statusHistory as $history)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 
                                        @if($history->status === 'accepted') bg-green-500
                                        @elseif($history->status === 'rejected') bg-red-500
                                        @elseif($history->status === 'under-review') bg-blue-500
                                        @else bg-gray-400
                                        @endif">
                                        <iconify-icon icon="lucide:check" class="text-white"></iconify-icon>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            Status changed to <strong>{{ ucfirst(str_replace('-', ' ', $history->status)) }}</strong>
                                        </p>
                                        @if($history->note)
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $history->note }}</p>
                                        @endif
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        <time>{{ $history->timestamp->format('M d, Y h:i A') }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Personal Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->phone }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nationality</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->nationality }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country of Residence</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->country_of_residence }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->address }}</dd>
                </div>
            </dl>
        </div>

        <!-- Academic Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Education Level</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('-', ' ', $application->current_education_level)) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Institution</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->institution_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Field of Study</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->field_of_study }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">GPA</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->gpa }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Achievements</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{!! nl2br(e($application->academic_achievements)) !!}</dd>
                </div>
            </dl>
        </div>

        <!-- Motivation & Goals -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Motivation & Career Goals</h3>
            <div class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Motivation Letter</dt>
                    <dd class="text-sm text-gray-900 dark:text-white prose dark:prose-invert max-w-none">{!! nl2br(e($application->motivation_letter)) !!}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Career Goals</dt>
                    <dd class="text-sm text-gray-900 dark:text-white prose dark:prose-invert max-w-none">{!! nl2br(e($application->career_goals)) !!}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Why This Scholarship</dt>
                    <dd class="text-sm text-gray-900 dark:text-white prose dark:prose-invert max-w-none">{!! nl2br(e($application->why_this_scholarship)) !!}</dd>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.scholarship-applications.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                    Back to List
                </a>
                @can('delete', $application)
                <form method="POST" action="{{ route('admin.scholarship-applications.destroy', $application) }}" class="inline">
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
