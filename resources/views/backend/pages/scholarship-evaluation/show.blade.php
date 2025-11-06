<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Evaluation Details') }}</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        {{ __('Evaluation for') }} <strong>{{ $evaluation->application->first_name }} {{ $evaluation->application->last_name }}</strong>
                    </p>
                </div>
                <a href="{{ route('admin.scholarship-evaluation.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column - Evaluation Details -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Application Info -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Applicant Information') }}</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $evaluation->application->first_name }} {{ $evaluation->application->last_name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->application->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Field of Study') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->application->field_of_study }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Institution') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->application->institution_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('GPA') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->application->gpa }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Scholarship') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $evaluation->application->scholarship->title ?? 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                            <div class="mt-4">
                                <a href="{{ route('admin.scholarship-applications.show', $evaluation->application) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                    {{ __('View Full Application') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation Scores -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                {{ __('Evaluation Scores') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Academic Performance -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Academic Performance') }}</span>
                                        <span class="text-2xl font-bold text-blue-600">{{ number_format($evaluation->academic_performance_score, 1) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($evaluation->academic_performance_score / 10) * 100 }}%"></div>
                                    </div>
                                </div>

                                <!-- Motivation -->
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Motivation & Commitment') }}</span>
                                        <span class="text-2xl font-bold text-green-600">{{ number_format($evaluation->motivation_score, 1) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($evaluation->motivation_score / 10) * 100 }}%"></div>
                                    </div>
                                </div>

                                <!-- Research Quality -->
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Research Quality') }}</span>
                                        <span class="text-2xl font-bold text-purple-600">{{ number_format($evaluation->research_quality_score, 1) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($evaluation->research_quality_score / 10) * 100 }}%"></div>
                                    </div>
                                </div>

                                <!-- Financial Need -->
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Financial Need') }}</span>
                                        <span class="text-2xl font-bold text-orange-600">{{ number_format($evaluation->financial_need_score, 1) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($evaluation->financial_need_score / 10) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Qualitative Feedback -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Qualitative Feedback') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            @if($evaluation->strengths)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Strengths') }}
                                </h4>
                                <div class="text-sm text-gray-700 dark:text-gray-300 bg-green-50 dark:bg-green-900/10 rounded-lg p-4 prose dark:prose-invert max-w-none">
                                    {!! nl2br(e($evaluation->strengths)) !!}
                                </div>
                            </div>
                            @endif

                            @if($evaluation->weaknesses)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Areas for Improvement') }}
                                </h4>
                                <div class="text-sm text-gray-700 dark:text-gray-300 bg-orange-50 dark:bg-orange-900/10 rounded-lg p-4 prose dark:prose-invert max-w-none">
                                    {!! nl2br(e($evaluation->weaknesses)) !!}
                                </div>
                            </div>
                            @endif

                            @if($evaluation->notes)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Additional Notes') }}
                                </h4>
                                <div class="text-sm text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4 prose dark:prose-invert max-w-none">
                                    {!! nl2br(e($evaluation->notes)) !!}
                                </div>
                            </div>
                            @endif

                            @if(!$evaluation->strengths && !$evaluation->weaknesses && !$evaluation->notes)
                            <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                                <p>{{ __('No additional feedback provided') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Overall Score Card -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Overall Assessment') }}</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('Overall Score') }}</div>
                                <div class="text-6xl font-bold text-blue-600">{{ number_format($evaluation->overall_score, 1) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('out of 10.0') }}</div>
                            </div>

                            <div class="mb-6">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Recommendation') }}</div>
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium w-full justify-center
                                    @if($evaluation->recommendation === 'strong-accept') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                    @elseif($evaluation->recommendation === 'accept') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($evaluation->recommendation === 'waitlist') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    @if($evaluation->recommendation === 'strong-accept')
                                        ✓✓ {{ __('Strong Accept') }}
                                    @elseif($evaluation->recommendation === 'accept')
                                        ✓ {{ __('Accept') }}
                                    @elseif($evaluation->recommendation === 'waitlist')
                                        ⏸ {{ __('Waitlist') }}
                                    @else
                                        ✗ {{ __('Reject') }}
                                    @endif
                                </span>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-4">

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Academic Performance') }}</span>
                                    <span class="font-medium text-blue-600">{{ number_format($evaluation->academic_performance_score, 1) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Motivation') }}</span>
                                    <span class="font-medium text-green-600">{{ number_format($evaluation->motivation_score, 1) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Research Quality') }}</span>
                                    <span class="font-medium text-purple-600">{{ number_format($evaluation->research_quality_score, 1) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Financial Need') }}</span>
                                    <span class="font-medium text-orange-600">{{ number_format($evaluation->financial_need_score, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation Meta -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Evaluation Info') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Reviewer') }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $evaluation->reviewer->first_name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Evaluated On') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->created_at->format('M d, Y \a\t h:i A') }}</dd>
                            </div>
                            @if($evaluation->updated_at != $evaluation->created_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $evaluation->updated_at->format('M d, Y \a\t h:i A') }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Actions') }}</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('admin.scholarship-applications.show', $evaluation->application) }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                                {{ __('View Application') }}
                            </a>
                            
                            <a href="{{ route('admin.scholarship-evaluation.index') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('All Evaluations') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
