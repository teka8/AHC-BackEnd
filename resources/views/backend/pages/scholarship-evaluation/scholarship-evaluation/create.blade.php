<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Evaluate Scholarship Application') }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Review and score the application for') }} <strong>{{ $application->first_name }} {{ $application->last_name }}</strong></p>
            </div>

            <!-- Application Quick View -->
            <div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application Summary</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applicant</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $application->first_name }} {{ $application->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Field of Study</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->field_of_study }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Institution</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->institution_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">GPA</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->gpa }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($application->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($application->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($application->status === 'under-review') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    {{ ucfirst(str_replace('-', ' ', $application->status)) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('admin.scholarship-applications.show', $application) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            View Full Application
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.scholarship-evaluation.store', $application) }}">
                @csrf

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Left Column - Evaluation Scores -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Scoring Section -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ __('Evaluation Scores') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Rate each criterion on a scale of 0-10') }}</p>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Academic Performance Score -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Academic Performance') }} <span class="text-red-500">*</span>
                                        </label>
                                        <span id="academic-score-display" class="text-2xl font-bold text-blue-600">0.0</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Evaluate GPA, academic achievements, and overall academic record') }}</p>
                                    <input 
                                        type="range" 
                                        name="academic_performance_score" 
                                        id="academic_performance_score" 
                                        min="0" 
                                        max="10" 
                                        step="0.1" 
                                        value="{{ old('academic_performance_score', 0) }}"
                                        required
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600"
                                        oninput="updateScoreDisplay('academic', this.value)"
                                    >
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>0 - Poor</span>
                                        <span>5 - Average</span>
                                        <span>10 - Excellent</span>
                                    </div>
                                    @error('academic_performance_score')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700">

                                <!-- Motivation Score -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Motivation & Commitment') }} <span class="text-red-500">*</span>
                                        </label>
                                        <span id="motivation-score-display" class="text-2xl font-bold text-green-600">0.0</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Assess motivation letter, career goals, and commitment to the field') }}</p>
                                    <input 
                                        type="range" 
                                        name="motivation_score" 
                                        id="motivation_score" 
                                        min="0" 
                                        max="10" 
                                        step="0.1" 
                                        value="{{ old('motivation_score', 0) }}"
                                        required
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-green-600"
                                        oninput="updateScoreDisplay('motivation', this.value)"
                                    >
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>0 - Poor</span>
                                        <span>5 - Average</span>
                                        <span>10 - Excellent</span>
                                    </div>
                                    @error('motivation_score')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700">

                                <!-- Research Quality Score -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Research Quality & Potential') }} <span class="text-red-500">*</span>
                                        </label>
                                        <span id="research-score-display" class="text-2xl font-bold text-purple-600">0.0</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Evaluate research interests, proposal quality, and academic potential') }}</p>
                                    <input 
                                        type="range" 
                                        name="research_quality_score" 
                                        id="research_quality_score" 
                                        min="0" 
                                        max="10" 
                                        step="0.1" 
                                        value="{{ old('research_quality_score', 0) }}"
                                        required
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-purple-600"
                                        oninput="updateScoreDisplay('research', this.value)"
                                    >
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>0 - Poor</span>
                                        <span>5 - Average</span>
                                        <span>10 - Excellent</span>
                                    </div>
                                    @error('research_quality_score')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700">

                                <!-- Financial Need Score -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Financial Need') }} <span class="text-red-500">*</span>
                                        </label>
                                        <span id="financial-score-display" class="text-2xl font-bold text-orange-600">0.0</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Assess the applicant\'s financial need and impact of the scholarship') }}</p>
                                    <input 
                                        type="range" 
                                        name="financial_need_score" 
                                        id="financial_need_score" 
                                        min="0" 
                                        max="10" 
                                        step="0.1" 
                                        value="{{ old('financial_need_score', 0) }}"
                                        required
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-orange-600"
                                        oninput="updateScoreDisplay('financial', this.value)"
                                    >
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>0 - Low Need</span>
                                        <span>5 - Moderate</span>
                                        <span>10 - High Need</span>
                                    </div>
                                    @error('financial_need_score')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Qualitative Assessment -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Qualitative Assessment') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Provide detailed feedback and observations') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Strengths -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Strengths') }}
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Highlight the applicant\'s key strengths and positive attributes') }}</p>
                                    <textarea 
                                        name="strengths" 
                                        id="strengths" 
                                        rows="4" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('strengths') border-red-500 @enderror"
                                        placeholder="List the applicant's notable strengths..."
                                    >{{ old('strengths') }}</textarea>
                                    @error('strengths')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Weaknesses -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Areas for Improvement') }}
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Identify any concerns or areas where the applicant could improve') }}</p>
                                    <textarea 
                                        name="weaknesses" 
                                        id="weaknesses" 
                                        rows="4" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('weaknesses') border-red-500 @enderror"
                                        placeholder="Note any weaknesses or areas of concern..."
                                    >{{ old('weaknesses') }}</textarea>
                                    @error('weaknesses')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Additional Notes -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Additional Notes') }}
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Any other observations or comments about the application') }}</p>
                                    <textarea 
                                        name="notes" 
                                        id="notes" 
                                        rows="3" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('notes') border-red-500 @enderror"
                                        placeholder="Additional comments or observations..."
                                    >{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Overall Assessment & Actions -->
                    <div class="xl:col-span-1 space-y-6">
                        <!-- Overall Score Card -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                    </svg>
                                    {{ __('Overall Assessment') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="text-center mb-6">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('Overall Score') }}</div>
                                    <div id="overall-score-display" class="text-5xl font-bold text-blue-600">0.0</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('out of 10.0') }}</div>
                                    <input type="hidden" name="overall_score" id="overall_score" value="0">
                                </div>

                                <div class="space-y-3 mb-6">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Academic') }}</span>
                                        <span id="score-breakdown-academic" class="font-medium">0.0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Motivation') }}</span>
                                        <span id="score-breakdown-motivation" class="font-medium">0.0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Research') }}</span>
                                        <span id="score-breakdown-research" class="font-medium">0.0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Financial') }}</span>
                                        <span id="score-breakdown-financial" class="font-medium">0.0</span>
                                    </div>
                                </div>

                                <!-- Recommendation -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Final Recommendation') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        name="recommendation" 
                                        id="recommendation" 
                                        required
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('recommendation') border-red-500 @enderror"
                                    >
                                        <option value="">{{ __('Select recommendation') }}</option>
                                        <option value="strong-accept" {{ old('recommendation') == 'strong-accept' ? 'selected' : '' }}>✓✓ {{ __('Strong Accept') }}</option>
                                        <option value="accept" {{ old('recommendation') == 'accept' ? 'selected' : '' }}>✓ {{ __('Accept') }}</option>
                                        <option value="waitlist" {{ old('recommendation') == 'waitlist' ? 'selected' : '' }}>⏸ {{ __('Waitlist') }}</option>
                                        <option value="reject" {{ old('recommendation') == 'reject' ? 'selected' : '' }}>✗ {{ __('Reject') }}</option>
                                    </select>
                                    @error('recommendation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Card -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Complete Evaluation') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Review all scores and feedback before submitting') }}</p>
                                    
                                    <div class="flex flex-col gap-3">
                                        <button 
                                            type="submit" 
                                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                                        >
                                            {{ __('Submit Evaluation') }}
                                        </button>
                                        
                                        <a 
                                            href="{{ route('admin.scholarship-applications.show', $application) }}" 
                                            class="w-full px-6 py-3 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-center"
                                        >
                                            {{ __('Cancel') }}
                                        </a>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-blue-50 dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-gray-700">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-xs text-blue-700 dark:text-blue-400">
                                                {{ __('Your evaluation will be recorded and may influence the final decision on this application.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluation Guidelines -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Guidelines') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-0.5">•</span>
                                        <span>{{ __('Be objective and fair in your assessment') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-0.5">•</span>
                                        <span>{{ __('Consider the full context of the application') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-0.5">•</span>
                                        <span>{{ __('Scores should reflect relative performance') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 mt-0.5">•</span>
                                        <span>{{ __('Provide constructive feedback in comments') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.backend-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize score displays with old values if they exist
    @if(old('academic_performance_score'))
        updateScoreDisplay('academic', {{ old('academic_performance_score') }});
    @endif
    @if(old('motivation_score'))
        updateScoreDisplay('motivation', {{ old('motivation_score') }});
    @endif
    @if(old('research_quality_score'))
        updateScoreDisplay('research', {{ old('research_quality_score') }});
    @endif
    @if(old('financial_need_score'))
        updateScoreDisplay('financial', {{ old('financial_need_score') }});
    @endif
    
    // Calculate initial overall score
    calculateOverallScore();
});

function updateScoreDisplay(category, value) {
    const formattedValue = parseFloat(value).toFixed(1);
    document.getElementById(`${category}-score-display`).textContent = formattedValue;
    document.getElementById(`score-breakdown-${category}`).textContent = formattedValue;
    calculateOverallScore();
}

function calculateOverallScore() {
    const academic = parseFloat(document.getElementById('academic_performance_score').value) || 0;
    const motivation = parseFloat(document.getElementById('motivation_score').value) || 0;
    const research = parseFloat(document.getElementById('research_quality_score').value) || 0;
    const financial = parseFloat(document.getElementById('financial_need_score').value) || 0;
    
    const overall = (academic + motivation + research + financial) / 4;
    const formattedOverall = overall.toFixed(1);
    
    document.getElementById('overall-score-display').textContent = formattedOverall;
    document.getElementById('overall_score').value = formattedOverall;
}
</script>
