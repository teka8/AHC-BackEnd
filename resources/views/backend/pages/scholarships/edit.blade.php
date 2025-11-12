<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Edit Scholarship') }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ __('Update the details for') }} "{{ $scholarship->title }}"</p>
            </div>

            <form method="POST" action="{{ route('admin.scholarships.update', $scholarship) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Left Column - Main Form Content -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Basic Information') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Essential details about the scholarship') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Scholarship Title') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="title" 
                                        id="title" 
                                        value="{{ old('title', $scholarship->title) }}" 
                                        required
                                        placeholder="e.g., STEM Excellence Scholarship 2025"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                    >
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Description') }} <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                        {{ __('Describe the scholarship in detail. Include benefits, requirements, and any other relevant information.') }}
                                    </p>
                                    <textarea 
                                        name="description" 
                                        id="description" 
                                        rows="6" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                                        placeholder="Provide a comprehensive description of the scholarship..."
                                    >{{ old('description', $scholarship->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Program Type') }} <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            name="program_type" 
                                            id="program_type" 
                                            required
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('program_type') border-red-500 @enderror"
                                        >
                                            <option value="">— {{ __('Select Program Type') }} —</option>
                                            <option value="undergraduate" {{ old('program_type', $scholarship->program_type) == 'undergraduate' ? 'selected' : '' }}>{{ __('Undergraduate') }}</option>
                                            <option value="graduate" {{ old('program_type', $scholarship->program_type) == 'graduate' ? 'selected' : '' }}>{{ __('Graduate') }}</option>
                                            <option value="postgraduate" {{ old('program_type', $scholarship->program_type) == 'postgraduate' ? 'selected' : '' }}>{{ __('Postgraduate') }}</option>
                                            <option value="research" {{ old('program_type', $scholarship->program_type) == 'research' ? 'selected' : '' }}>{{ __('Research') }}</option>
                                        </select>
                                        @error('program_type')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Status') }} <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            name="status" 
                                            id="status" 
                                            required
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror"
                                        >
                                            <option value="open" {{ old('status', $scholarship->status) == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                            <option value="closed" {{ old('status', $scholarship->status) == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                            <option value="upcoming" {{ old('status', $scholarship->status) == 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Eligibility Criteria') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        name="eligibility_criteria" 
                                        id="eligibility_criteria" 
                                        rows="4" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('eligibility_criteria') border-red-500 @enderror"
                                        placeholder="List the eligibility requirements for applicants..."
                                    >{{ old('eligibility_criteria', $scholarship->eligibility_criteria) }}</textarea>
                                    @error('eligibility_criteria')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Financial Details -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Financial Details') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Scholarship funding and benefits information') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Amount') }} ($)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="amount" 
                                            id="amount" 
                                            value="{{ old('amount', $scholarship->amount) }}" 
                                            min="0" 
                                            step="0.01"
                                            placeholder="0.00"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('amount') border-red-500 @enderror"
                                        >
                                        @error('amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Coverage') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="coverage" 
                                            id="coverage" 
                                            value="{{ old('coverage', $scholarship->coverage) }}" 
                                            required
                                            placeholder="e.g., Full tuition + $1,500/month"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('coverage') border-red-500 @enderror"
                                        >
                                        @error('coverage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Benefits') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="space-y-3" id="benefits-container">
                                        @php
                                            $benefits = old('benefits', $scholarship->benefits ?? []);
                                            $benefits = is_array($benefits) ? $benefits : [];
                                        @endphp
                                        @if(count($benefits) > 0)
                                            @foreach($benefits as $index => $benefit)
                                                <div class="flex gap-2">
                                                    <input 
                                                        type="text" 
                                                        name="benefits[]" 
                                                        value="{{ $benefit }}" 
                                                        {{ $index === 0 ? 'required' : '' }}
                                                        placeholder="Benefit {{ $index + 1 }}"
                                                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    >
                                                    @if($index === 0)
                                                        <button type="button" class="add-benefit-btn px-3 py-2.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <button type="button" class="remove-benefit-btn px-3 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex gap-2">
                                                <input 
                                                    type="text" 
                                                    name="benefits[]" 
                                                    value="" 
                                                    required
                                                    placeholder="Benefit 1 (e.g., Tuition coverage)"
                                                    class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                >
                                                <button type="button" class="add-benefit-btn px-3 py-2.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Enter at least one benefit. Click + to add more.') }}</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Required Documents') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="space-y-3" id="documents-container">
                                        @php
                                            $documents = old('required_documents', $scholarship->required_documents ?? []);
                                            $documents = is_array($documents) ? $documents : [];
                                        @endphp
                                        @if(count($documents) > 0)
                                            @foreach($documents as $index => $document)
                                                <div class="flex gap-2">
                                                    <input 
                                                        type="text" 
                                                        name="required_documents[]" 
                                                        value="{{ $document }}" 
                                                        {{ $index === 0 ? 'required' : '' }}
                                                        placeholder="Document {{ $index + 1 }}"
                                                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    >
                                                    @if($index === 0)
                                                        <button type="button" class="add-document-btn px-3 py-2.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <button type="button" class="remove-document-btn px-3 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex gap-2">
                                                <input 
                                                    type="text" 
                                                    name="required_documents[]" 
                                                    value="" 
                                                    required
                                                    placeholder="Document 1 (e.g., CV)"
                                                    class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                >
                                                <button type="button" class="add-document-btn px-3 py-2.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Enter at least one required document. Click + to add more.') }}</p>
                                </div>
                            </div>
                        </div>

                        
                    </div>

                    <!-- Right Column - Actions & Additional Info -->
                    <div class="xl:col-span-1 space-y-6">
                        <!-- Scholarship Status & Stats -->
                        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <!-- Header with Current Status -->
                            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</h3>
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-800',
                                            'closed' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 border-red-200 dark:border-red-800',
                                            'upcoming' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        ];
                                        $colorClass = $statusColors[$scholarship->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $colorClass }}">
                                        {{ ucfirst(__($scholarship->status)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Scholarship Information -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Created') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $scholarship->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Last Updated') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $scholarship->updated_at->format('M d, Y') }}
                                    </span>
                                </div>

                                @if($scholarship->application_start_date)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Applications Start') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $scholarship->application_start_date->format('M d, Y') }}
                                    </span>
                                </div>
                                @endif

                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Deadline') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $scholarship->deadline->format('M d, Y') }}
                                    </span>
                                </div>

                                @if($scholarship->available_slots)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Available Slots') }}</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ $scholarship->available_slots }}
                                    </span>
                                </div>
                                @endif

                                <!-- Status Information -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <span>
                                            @switch($scholarship->status)
                                                @case('open')
                                                    {{ __('This scholarship is currently accepting applications.') }}
                                                    @break
                                                @case('closed')
                                                    {{ __('This scholarship is no longer accepting applications.') }}
                                                    @break
                                                @case('upcoming')
                                                    {{ __('This scholarship will be available for applications soon.') }}
                                                    @break
                                                @default
                                                    {{ __('Current status: ') . ucfirst($scholarship->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Scholarship Image --}}
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Scholarship Image') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">{{ __('Update scholarship visual') }}</p>
                            </div>
                            <div class="p-6">
                                @if(isset($scholarship) && $scholarship->scholarship_image)
                                    <div class="relative mb-4">
                                        <img 
                                            src="{{ asset('storage/' . $scholarship->scholarship_image) }}" 
                                            alt="Scholarship preview" 
                                            class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-opacity"
                                            onclick="showScholarshipImageModal('{{ asset('storage/' . $scholarship->scholarship_image) }}')"
                                        >
                                        <div class="absolute top-2 right-2">
                                            <button 
                                                type="button" 
                                                class="bg-blue-500 text-white p-1 rounded-full hover:bg-blue-600 transition-colors text-xs"
                                                onclick="showScholarshipImageModal('{{ asset('storage/' . $scholarship->scholarship_image) }}')"
                                                title="{{ __('View full image') }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3-3H7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                    <input 
                                        id="scholarship_image" 
                                        name="scholarship_image" 
                                        type="file" 
                                        accept="image/*" 
                                        class="hidden"
                                        onchange="previewScholarshipImage(this)"
                                    >
                                    <label 
                                        for="scholarship_image" 
                                        class="cursor-pointer flex flex-col items-center gap-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-sm text-gray-500">
                                            {{ isset($scholarship) && $scholarship->scholarship_image ? __('Change scholarship image') : __('Upload scholarship image') }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ __('PNG, JPG up to 10MB') }}
                                        </span>
                                    </label>
                                </div>
                                
                                <p class="text-xs text-gray-500 mt-3">
                                    {{ __('Upload a new image or keep the current one') }}
                                </p>
                                
                                <div id="scholarship-image-preview" class="mt-4 hidden">
                                    <div class="relative">
                                        <img id="scholarship-preview" class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-opacity" onclick="showScholarshipImageModal(this.src)">
                                        <div class="absolute top-2 right-2 flex gap-1">
                                            <button 
                                                type="button" 
                                                class="bg-blue-500 text-white p-1 rounded-full hover:bg-blue-600 transition-colors text-xs"
                                                onclick="showScholarshipImageModal(document.getElementById('scholarship-preview').src)"
                                                title="{{ __('View full image') }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3-3H7" />
                                                </svg>
                                            </button>
                                            <button type="button" onclick="removeScholarshipPreview()" class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors text-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scholarship Image Modal -->
                        <div id="scholarshipImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4 hidden">
                            <div class="relative max-w-4xl max-h-full w-full">
                                <!-- Close Button -->
                                <button 
                                    type="button" 
                                    onclick="hideScholarshipImageModal()"
                                    class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors z-10"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                
                                <!-- Image -->
                                <img 
                                    id="modalScholarshipImage" 
                                    src="" 
                                    alt="Scholarship image" 
                                    class="w-full h-auto max-h-[80vh] object-contain rounded-lg"
                                >
                                
                                <!-- Download Button -->
                                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                                    <a 
                                        id="downloadScholarshipImage" 
                                        href="#" 
                                        download
                                        class="bg-white text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors flex items-center gap-2 shadow-lg"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Dates & Availability -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Dates & Availability') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Application timeline and availability information') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Application Start Date') }}
                                        </label>
                                        <input 
                                            type="date" 
                                            name="application_start_date" 
                                            id="application_start_date" 
                                            value="{{ old('application_start_date', $scholarship->application_start_date?->format('Y-m-d')) }}"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('application_start_date') border-red-500 @enderror"
                                        >
                                        @error('application_start_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Application Deadline') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            name="deadline" 
                                            id="deadline" 
                                            value="{{ old('deadline', $scholarship->deadline->format('Y-m-d')) }}" 
                                            required
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('deadline') border-red-500 @enderror"
                                        >
                                        @error('deadline')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Available Slots') }}
                                        </label>
                                        <input 
                                            type="number" 
                                            name="available_slots" 
                                            id="available_slots" 
                                            value="{{ old('available_slots', $scholarship->available_slots) }}" 
                                            min="1"
                                            placeholder="e.g., 10"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('available_slots') border-red-500 @enderror"
                                        >
                                        @error('available_slots')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Update Scholarship Card -->
                        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Finalize Changes') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">{{ __('Complete your scholarship updates') }}</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="text-center">
                                        <p class="text-gray-600 mb-4 dark:text-gray-400">{{ __('Review all changes and submit when ready') }}</p>
                                        
                                        <div class="flex justify-center gap-3">
                                            <a 
                                                href="{{ route('admin.scholarships.index') }}" 
                                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:text-gray-400"
                                            >
                                                {{ __('Cancel') }}
                                            </a>
                                            <button 
                                                type="submit" 
                                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                                            >
                                                {{ __('Update Scholarship') }}
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-xs text-blue-700 dark:text-blue-400">
                                                {{ __('Make sure all required fields are filled and information is accurate before submitting.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scholarship Image Modal -->
    <div id="scholarshipImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4 hidden">
        <div class="relative max-w-4xl max-h-full w-full">
            <!-- Close Button -->
            <button 
                type="button" 
                onclick="hideScholarshipImageModal()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors z-10"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Image -->
            <img 
                id="modalScholarshipImage" 
                src="" 
                alt="Scholarship image" 
                class="w-full h-auto max-h-[80vh] object-contain rounded-lg"
            >
            
            <!-- Download Button -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                <a 
                    id="downloadScholarshipImage" 
                    href="#" 
                    download
                    class="bg-white text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors flex items-center gap-2 shadow-lg"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Download') }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>

<script>

    // Dynamic benefits and documents fields
    document.addEventListener('DOMContentLoaded', function() {
        // Benefits functionality
        const benefitsContainer = document.getElementById('benefits-container');
        const addBenefitBtn = document.querySelector('.add-benefit-btn');
        
        if (addBenefitBtn) {
            addBenefitBtn.addEventListener('click', function() {
                const benefitCount = benefitsContainer.querySelectorAll('input[name="benefits[]"]').length;
                const newBenefitField = document.createElement('div');
                newBenefitField.className = 'flex gap-2';
                newBenefitField.innerHTML = `
                    <input 
                        type="text" 
                        name="benefits[]" 
                        placeholder="Benefit ${benefitCount + 1}"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                    <button type="button" class="remove-benefit-btn px-3 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;
                benefitsContainer.appendChild(newBenefitField);
                
                // Add event listener to the new remove button
                newBenefitField.querySelector('.remove-benefit-btn').addEventListener('click', function() {
                    newBenefitField.remove();
                });
            });
        }
        
        // Add event listeners to existing remove buttons for benefits
        document.querySelectorAll('.remove-benefit-btn').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
        
        // Documents functionality
        const documentsContainer = document.getElementById('documents-container');
        const addDocumentBtn = document.querySelector('.add-document-btn');
        
        if (addDocumentBtn) {
            addDocumentBtn.addEventListener('click', function() {
                const documentCount = documentsContainer.querySelectorAll('input[name="required_documents[]"]').length;
                const newDocumentField = document.createElement('div');
                newDocumentField.className = 'flex gap-2';
                newDocumentField.innerHTML = `
                    <input 
                        type="text" 
                        name="required_documents[]" 
                        placeholder="Document ${documentCount + 1}"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                    <button type="button" class="remove-document-btn px-3 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;
                documentsContainer.appendChild(newDocumentField);
                
                // Add event listener to the new remove button
                newDocumentField.querySelector('.remove-document-btn').addEventListener('click', function() {
                    newDocumentField.remove();
                });
            });
        }
        
        // Add event listeners to existing remove buttons for documents
        document.querySelectorAll('.remove-document-btn').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
    });


    // Scholarship Image preview functionality
    function previewScholarshipImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('scholarship-preview').src = e.target.result;
                document.getElementById('scholarship-image-preview').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeScholarshipPreview() {
        document.getElementById('scholarship-image-preview').classList.add('hidden');
        document.getElementById('scholarship_image').value = '';
    }

    // Scholarship Image Modal Functions
    function showScholarshipImageModal(imageSrc) {
        const modal = document.getElementById('scholarshipImageModal');
        const modalImage = document.getElementById('modalScholarshipImage');
        const downloadLink = document.getElementById('downloadScholarshipImage');
        
        modalImage.src = imageSrc;
        downloadLink.href = imageSrc;
        downloadLink.download = 'scholarship-image.' + getScholarshipFileExtension(imageSrc);
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideScholarshipImageModal() {
        const modal = document.getElementById('scholarshipImageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function getScholarshipFileExtension(filename) {
        return filename.split('.').pop().split('?')[0];
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideScholarshipImageModal();
        }
    });

    // Close modal when clicking on backdrop
    document.getElementById('scholarshipImageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideScholarshipImageModal();
        }
    });
</script>