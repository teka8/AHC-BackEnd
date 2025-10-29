{!! Hook::applyFilters('filter.event.form_start', '') !!}

<input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">    
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-300">{{ __('Fill in the details to') }} {{ isset($event) ? 'update' : 'create' }} {{ __('your event') }}</p>
        </div>

        <form method="POST" action="{{ isset($event) ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

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
                            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Essential details about your event') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Title and Category in same row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{   __('Event Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="title" 
                                        value="{{ old('title', $event->title ?? '') }}" 
                                        placeholder="e.g., Annual Tech Conference 2025" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                        required
                                    >
                                    @error('title')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Category') }}</label>
                                    <select 
                                        name="category" 
                                        id="category" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('category') border-red-500 @enderror"
                                    >
                                        <option value="">— {{__("Select")}} —</option>
                                        <option value="lectures" {{ old('category', $event->category ?? '') === 'lectures' ? 'selected' : '' }}>{{__("Lectures")}}</option>
                                        <option value="festivals" {{ old('category', $event->category ?? '') === 'festivals' ? 'selected' : '' }}>{{__("Festivals")}}</option>
                                        <option value="seminars" {{ old('category', $event->category ?? '') === 'seminars' ? 'selected' : '' }}>{{__("Seminars")}}</option>
                                        <option value="anniversary" {{ old('category', $event->category ?? '') === 'anniversary' ? 'selected' : '' }}>{{__("Anniversary")}}</option>
                                        <option value="hackathon" {{ old('category', $event->category ?? '') === 'hackathon' ? 'selected' : '' }}>{{__("Hackathon")}}</option>
                                        <option value="custom" {{ !in_array(old('category', $event->category ?? ''), ['lectures','festivals','seminars','anniversary','hackathon','']) ? 'selected' : '' }}>{{__("Custom")}}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="custom-category-field" class="space-y-2 hidden">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Custom Category') }}</label>
                                <input 
                                    type="text" 
                                    id="custom_category"
                                    name="custom_category" 
                                    value="{{ !in_array($event->category ?? '', ['lectures','festivals','seminars','anniversary','hackathon','']) ? ($event->category ?? '') : '' }}" 
                                    placeholder="Add your custom category" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                                <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                    {{ __('Describe your event in detail. Use the toolbar to format text, add images, and create links.') }}
                                </p>
                                <textarea 
                                    name="description" 
                                    id="description" 
                                    rows="8" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >{!! old('description', $event->description ?? '') !!}</textarea>
                            </div>
                        </div>
                    </div>

                    {!! Hook::applyFilters('filter.event.form_after_title', '') !!}
                    {{-- Event Image --}}
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Event Image') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">{{ __('Add event visual') }}</p>
                            </div>
                            <div class="p-6">
    @if(isset($event) && $event->event_image)
        <div class="relative mb-4">
            <img 
                src="{{ asset('storage/' . $event->event_image) }}" 
                alt="Event preview" 
                class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-opacity"
                onclick="showEventImageModal('{{ asset('storage/' . $event->event_image) }}')"
            >
            <div class="absolute top-2 right-2">
                <button 
                    type="button" 
                    class="bg-blue-500 text-white p-1 rounded-full hover:bg-blue-600 transition-colors text-xs"
                    onclick="showEventImageModal('{{ asset('storage/' . $event->event_image) }}')"
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
            id="event_image" 
            name="event_image" 
            type="file" 
            accept="image/*" 
            class="hidden"
            onchange="previewImage(this)"
        >
        <label 
            for="event_image" 
            class="cursor-pointer flex flex-col items-center gap-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span class="text-sm text-gray-500">
                {{ __('Upload event image') }}
            </span>
            <span class="text-xs text-gray-400">
                {{ __('PNG, JPG up to 10MB') }}
            </span>
        </label>
    </div>
    
    <p class="text-xs text-gray-500 mt-3">
        {{ __('Upload an image or provide image URL') }}
    </p>
    
    <div id="image-preview" class="mt-4 hidden">
        <div class="relative">
            <img id="preview" class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-opacity" onclick="showEventImageModal(this.src)">
            <div class="absolute top-2 right-2 flex gap-1">
                <button 
                    type="button" 
                    class="bg-blue-500 text-white p-1 rounded-full hover:bg-blue-600 transition-colors text-xs"
                    onclick="showEventImageModal(document.getElementById('preview').src)"
                    title="{{ __('View full image') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3-3H7" />
                    </svg>
                </button>
                <button type="button" onclick="removePreview()" class="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Image Modal -->
<div id="eventImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4 hidden">
    <div class="relative max-w-4xl max-h-full w-full">
        <!-- Close Button -->
        <button 
            type="button" 
            onclick="hideEventImageModal()"
            class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors z-10"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <!-- Image -->
        <img 
            id="modalEventImage" 
            src="" 
            alt="Event image" 
            class="w-full h-auto max-h-[80vh] object-contain rounded-lg"
        >
        
        <!-- Download Button -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
            <a 
                id="downloadEventImage" 
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

                    </div>
                    

                    <!-- Row 1: Date, Time & Location AND Registration & Participants -->
                    <div class="flex flex-nowrap gap-6">
                        <!-- Date, Time & Location -->
                        <div class="flex-1 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Date, Time & Location') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">{{ __('When and where is your event?') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Event Date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <x-inputs.date-picker name="event_date" value="{{ old('event_date', isset($event) ? $event->event_date?->format('Y-m-d') : '') }}" required/>
                                    @error('event_date')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Start and {{ __('End Time') }} in same row -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Start Time') }} <span class="text-red-500">*</span>
                                        </label>
                                        <x-inputs.time-picker name="start_time" value="{{ old('start_time', $event->start_time ?? '') }}" required/>
                                        @error('start_time')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('End Time') }}</label>
                                        <x-inputs.time-picker name="end_time" value="{{ old('end_time', $event->end_time ?? '') }}"/>
                                        @error('end_time')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Event Type') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        name="event_type" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:border-gray-700 dark:bg-gray-900"
                                    >
                                        <option value="event_type_Select">— {{ __('Select')}} —</option>
                                        <option value="virtual" {{ old('event_type', $event->event_type ?? '') === 'virtual' ? 'selected' : '' }}>{{ __('Virtual (Online)') }}</option>
                                        <option value="in-person" {{ old('event_type', $event->event_type ?? '') === 'in-person' ? 'selected' : '' }}>{{ __('In-Person') }}</option>
                                    </select>
                                    @error('event_type')
                                        <p class="text-sm text-red-600 mt-1">{{ __($message) }}</p>
                                    @enderror
                                </div>

                                <div id="location-fields">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center gap-2 dark:text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{__("Location")}}
                                        </label>
                                        <input 
                                            type="text" 
                                            name="location" 
                                            value="{{ old('location', $event->location ?? '') }}" 
                                            placeholder="{{ __('Venue address or location name') }}" 
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:border-gray-700 dark:bg-gray-900"
                                        >
                                    </div>

                                    <div class="space-y-2 mt-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Google Maps Link') }}</label>
                                        <input 
                                            type="url" 
                                            name="google_map_location_link" 
                                            value="{{ old('google_map_location_link', $event->google_map_location_link ?? '') }}" 
                                            placeholder="https://maps.google.com/..." 
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:border-gray-700 dark:bg-gray-900"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- {{ __('Registration & Participants') }} -->
                        <div class="flex-1 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    {{ __('Registration & Participants') }}
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">{{ __('Manage event registration') }}</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Target Audience') }}</label>
                                    <select 
                                        name="target_audience" 
                                        id="target_audience" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white dark:border-gray-700 dark:bg-gray-900"
                                    >
                                        <option value="">— {{__("Select")}} —</option>
                                        <option value="public" {{ old('target_audience', $event->target_audience ?? '') === 'public' ? 'selected' : '' }}>{{ __('Public') }}</option>
                                        <option value="for_students" {{ old('target_audience', $event->target_audience ?? '') === 'for_students' ? 'selected' : '' }}>{{ __('Students') }}</option>
                                        <option value="for_staff" {{ old('target_audience', $event->target_audience ?? '') === 'for_staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                                        <option value="for_alumni" {{ old('target_audience', $event->target_audience ?? '') === 'for_alumni' ? 'selected' : '' }}>{{ __('Alumni') }}</option>
                                        <option value="custom" {{ !in_array(old('target_audience', $event->target_audience ?? ''), ['public', 'for_students', 'for_staff', 'for_employees', 'for_alumni', '']) ? 'selected' : '' }}>{{ __('Custom') }}</option>
                                    </select>
                                </div>

                                <div id="custom-audience-field" class="space-y-2 hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Custom') }} {{ __('Target Audience') }}</label>
                                    <input 
                                        type="text" 
                                        id="custom_target_audience"
                                        name="custom_target_audience" 
                                        value="{{ !in_array($event->target_audience ?? '', ['public','for_students','for_staff','for_employees','for_alumni', '']) ? ($event->target_audience ?? '') : '' }}"
                                        placeholder="{{ __('e.g., Industry professionals, Investors, etc.') }}" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:border-gray-700 dark:bg-gray-900"
                                    >
                                </div>

                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                                    <div class="space-y-0.5">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('On-site Registration') }}</label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Allow registration at venue') }}</p>
                                    </div>
                                    <div class="relative inline-block w-12 h-6">
                                        <input 
                                            type="checkbox" 
                                            name="register_on_site" 
                                            id="register_on_site" 
                                            value="1" 
                                            class="sr-only" 
                                            {{ old('register_on_site', $event->register_on_site ?? false) ? 'checked' : '' }}
                                        >
                                        <label 
                                            for="register_on_site" 
                                            class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300"
                                        ></label>
                                        <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                                    </div>
                                </div>

                                <div id="registration-link-field" class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 flex items-center gap-2 dark:text-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Registration Link') }}
                                    </label>
                                    <input 
                                        type="url" 
                                        name="registration_link" 
                                        value="{{ old('registration_link', $event->registration_link ?? '') }}" 
                                        placeholder="https://example.com/register" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:border-gray-700 dark:bg-gray-900"
                                    >
                                </div>
                            </div>
                        </div>
   
                    </div>
    
                </div>


                <!-- Right Column - Media and {{ __('Attachments') }} -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Event Image -->
                    @if (!empty($event))
                        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <!-- Header with Current Status -->
                            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</h3>
                                    @php
                                        $statusColors = [
                                            'published' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-800',
                                            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                            'under_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                            'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 border-red-200 dark:border-red-800',
                                            'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                            'archived' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        ];
                                        $colorClass = $statusColors[$event->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $colorClass }}">
                                    
                                        {{ ucfirst(str_replace('_', ' ', __($event->status))) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Workflow Actions -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Event Actions') }}</span>
                                    <iconify-icon icon="lucide:calendar" class="text-blue-500"></iconify-icon>
                                </div>

                                <div class="grid grid-cols-1 gap-2">
                                    @php
                                        // Define available transitions based on current status
                                        $availableActions = $event->getAvailableActions();
                                        $translatedActions = collect($availableActions)->map(function ($action) {
                                            $action['label'] = __($action['label']); // Translate the label
                                            return $action;
                                        })->toArray();
                                    @endphp

                                    @foreach($translatedActions as $action => $config)
                                        <button type="button"
                                                onclick="changeEventStatus({{ $event->id }}, '{{ $action }}')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent transition-all duration-200
                                                    bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 
                                                    hover:bg-{{ $config['color'] }}-200 hover:scale-105
                                                    dark:bg-{{ $config['color'] }}-900/20 dark:text-{{ $config['color'] }}-300 
                                                    dark:hover:bg-{{ $config['color'] }}-900/30
                                                    focus:outline-none focus:ring-2 focus:ring-{{ $config['color'] }}-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                title="{{ $config['label'] }}">
                                            <iconify-icon icon="{{ $config['icon'] }}" class="w-4 h-4 mr-2"></iconify-icon>
                                            {{ $config['label'] }}
                                        </button>
                                    @endforeach

                                    @if(empty($availableActions))
                                        <div class="text-center py-3 text-gray-500 dark:text-gray-400">
                                            <iconify-icon icon="lucide:lock" class="w-5 h-5 mx-auto mb-2"></iconify-icon>
                                            <p class="text-sm">{{ __('No actions available for current status') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Event Status Information -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <iconify-icon icon="lucide:info" class="w-4 h-4 mr-2 text-blue-500"></iconify-icon>
                                        <span>
                                            @switch($event->status)
                                                @case('draft')
                                                    {{ __('This event is in draft mode and not visible to the public.') }}
                                                    @break
                                                @case('under_review')
                                                    {{ __('This event is under review by the event management team.') }}
                                                    @break
                                                @case('approved')
                                                    {{ __('This event has been approved and is ready for publishing.') }}
                                                    @break
                                                @case('published')
                                                    {{ __('This event is live and visible to the public. Registration is open.') }}
                                                    @break
                                                @case('cancelled')
                                                    {{ __('This event has been cancelled and is no longer accepting registrations.') }}
                                                    @break
                                                @case('completed')
                                                    {{ __('This event has been completed.') }}
                                                    @break
                                                @case('archived')
                                                    {{ __('This event has been archived and is no longer visible.') }}
                                                    @break
                                                @default
                                                    {{ __('Current status: ') . ucfirst(str_replace('_', ' ', $event->status)) }}
                                            @endswitch
                                        </span>
                                    </div>

                                    <!-- Event Date Information -->
                                    @if($event->event_date)
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        <span>
                                            @if($event->status === 'published')
                                                <iconify-icon icon="lucide:calendar" class="w-4 h-4 mr-2 text-green-500"></iconify-icon>
                                                {{ __('Event scheduled for: ') . $event->event_date->format('M d, Y g:i A') }}
                                            @elseif($event->status === 'completed')
                                                <iconify-icon icon="lucide:calendar" class="w-4 h-4 mr-2 text-green-500"></iconify-icon>
                                                {{ __('Event was held on: ') . $event->event_date->format('M d, Y g:i A') }} 
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Event Timeline Progress -->
                                @if(in_array($event->status, ['draft', 'under_review', 'approved', 'published', 'completed']))
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        <span>{{ __('Event Timeline') }}</span>
                                        <span>
                                            @php
                                                $progressStages = ['draft', 'under_review', 'approved', 'published', 'completed'];
                                                $currentProgress = array_search($event->status, $progressStages);
                                                $progressPercent = ($currentProgress / (count($progressStages) - 1)) * 100;
                                            @endphp
                                            {{ round($progressPercent) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                            style="width: {{ $progressPercent }}%">
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="text-center">Draft</span>
                                        <span class="text-center">Review</span>
                                        <span class="text-center">Approved</span>
                                        <span class="text-center">Live</span>
                                        <span class="text-center">Done</span>
                                    </div>
                                </div>
                                @endif
                                

                                <!-- Registration Stats (if published) -->
                                @if($event->status === 'published' && method_exists($event, 'registrations'))
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Registrations') }}</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ $event->registrations_count ?? 0 }} {{ __('registered') }}
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                                @endif


                    {{-- Attachments  --}}
                    <div 
                        class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden"
                        x-data="attachmentUploader()"
                    >
                        <!-- Delete Confirmation Modal -->
                        <div 
                            x-show="showDeleteModal" 
                            x-cloak
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            <div 
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                                x-show="showDeleteModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.away="cancelDelete()"
                            >
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex-shrink-0 h-10 w-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('Delete Attachment') }}
                                    </h3>
                                </div>
                                
                                <p class="text-gray-600 dark:text-gray-300 mb-6">
                                    {{ __('Are you sure you want to delete this attachment? This action cannot be undone.') }}
                                </p>
                                
                                <div class="flex justify-end gap-3">
                                    <button 
                                        type="button"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors"
                                        @click="cancelDelete()"
                                    >
                                        {{ __('Cancel') }}
                                    </button>
                                    <button 
                                        type="button"
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors flex items-center gap-2"
                                        @click="removeExistingAttachmentConfirmed()"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Attachments') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1">{{ __('Additional event files') }}</p>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Upload area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                <input 
                                    id="attachments" 
                                    name="attachments[]" 
                                    type="file" 
                                    multiple 
                                    class="hidden"
                                    @change="handleFiles($event)"
                                >
                                <label for="attachments" class="cursor-pointer flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-sm text-gray-500">{{ __('Upload attachments') }}</span>
                                    <span class="text-xs text-gray-400">{{ __('PDF, DOC, DOCX up to 10MB') }}</span>
                                </label>
                            </div>

                            <!-- Newly selected attachments -->
                            <template x-if="newAttachments.length > 0">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('New Attachments') }}</label>

                                        <button 
                                            type="button" 
                                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                            @click="clearNewAttachments()"
                                        >
                                            {{ __('Clear All') }}
                                        </button>
                                    </div>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <template x-for="(file, index) in newAttachments" :key="index">
                                            <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg border border-green-200 dark:bg-green-800 dark:border-green-700">
                                                <div class="flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs font-medium truncate max-w-[140px] dark:text-gray-100" x-text="file.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="(file.size / 1048576).toFixed(2) + ' MB'"></p>
                                                    </div>
                                                </div>
                                                <button type="button" class="text-red-500 hover:text-red-700 text-xs" @click.prevent="removeNewAttachment(index)">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Existing attachments (edit mode) -->
                            @if(isset($event) && $event->attachments && count($event->attachments) > 0)
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Current Attachments') }}</label>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        @foreach($event->attachments as $index => $attachment)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200 existing-attachment dark:bg-gray-800 dark:border-gray-700" id="currentAttachment_{{ $index }}">
                                                <div class="flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs font-medium truncate max-w-[140px] dark:text-gray-100">{{ $attachment['file_name'] }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment['size'] }}</p>
                                                    </div>
                                                </div>
                                                <!-- Remove Button -->
                                                <button 
                                                    type="button"
                                                    class="text-red-500 hover:text-red-700 text-xs transition-colors p-1 rounded hover:bg-red-50 dark:hover:bg-red-900"
                                                    @click.prevent="confirmDeleteExistingAttachment({{ $index }}, '{{ $attachment['path'] }}')"
                                                    title="{{ __('Delete attachment') }}"
                                                >
                                                    ✕
                                                </button>
                                                <input type="hidden" name="existing_attachments[]" value="{{ $attachment['path'] }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                    <!-- Pricing -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Pricing') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Set the cost for your event') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
                                <div class="space-y-0.5">
                                    <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ __('Free Event') }}</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('This event is free to attend') }}</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input 
                                        type="checkbox" 
                                        name="free_event" 
                                        id="free_event" 
                                        value="1" 
                                        class="sr-only dark:bg-gray-700" 
                                        {{ (old('free_event', $event->cost_amount ?? 0) == 0) ? 'checked' : '' }}
                                    >
                                    <label 
                                        for="free_event" 
                                        class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300 dark:bg-gray-700"
                                    ></label>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                                </div>
                            </div>

                            <div id="cost-amount-field" class="space-y-2 mt-4 hidden">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Cost Amount') }} (ETB)</label>
                                <input 
                                    type="number" 
                                    name="cost_amount" 
                                    step="0.01" 
                                    value="{{ old('cost_amount', $event->cost_amount ?? '') }}" 
                                    placeholder="0.00" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors dark:bg-gray-800 dark:border-gray-700"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Create Event Card -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Finalize Event') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1">{{ __('Complete your event setup') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="text-center">
                                    <p class="text-gray-600 mb-4 dark:text-gray-400">{{ __('Review all information and submit when ready') }}</p>
                                    
                                    <div class="flex justify-center gap-3">
                                        <a 
                                            href="{{ route('admin.events.index') }}" 
                                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:text-gray-400"
                                        >
                                           {{ __('Cancel') }}
                                        </a>
                                        <button 
                                            type="submit" 
                                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                                        >
                                            {{ isset($event) ? __('Update Event') : __('Create Event') }}
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

{!! Hook::applyFilters('filter.event.form_end', '') !!}


                    {{-- Alpine.js attachments upload and remove --}}
<script>
    function attachmentUploader() {
        return {
            newAttachments: [],
            existingAttachments: @json($event->attachments ?? []),
            removedAttachments: [],
            fileInput: null,
            showDeleteModal: false,
            attachmentToDelete: null,
            deleteIndex: null,

            init() {
                this.fileInput = this.$el.querySelector('input[type="file"][name="attachments[]"]');
            },

            handleFiles(e) {
                this.newAttachments = Array.from(e.target.files || []);
            },

            /**
             * Show confirmation modal for existing attachment deletion
             */
            confirmDeleteExistingAttachment(index, path) {
                if (!path) return;
                
                this.attachmentToDelete = path;
                this.deleteIndex = index;
                this.showDeleteModal = true;
            },

            /**
             * Confirm and remove existing attachment
             */
            removeExistingAttachmentConfirmed() {
                if (!this.attachmentToDelete) return;

                const path = this.attachmentToDelete;
                const index = this.deleteIndex;

                // 1) mark for removal (used by server)
                this.removedAttachments.push(path);

                // 2) Remove the visible wrapper element (closest .existing-attachment)
                const existingInputs = Array.from(this.$el.querySelectorAll('input[name="existing_attachments[]"]'));
                
                existingInputs.forEach((inputEl) => {
                    if (inputEl.value === path) {
                        const wrapper = inputEl.closest('.existing-attachment');
                        if (wrapper) {
                            wrapper.remove();
                        } else {
                            inputEl.remove();
                        }
                    }
                });

                // 3) Ensure the existing hidden input is removed (so it's not resubmitted)
                const leftoverInputs = this.$el.querySelectorAll('input[name="existing_attachments[]"]');
                leftoverInputs.forEach((el) => {
                    if (el.value === path) {
                        el.remove();
                    }
                });

                // 4) Create a remove_attachments[] hidden input so backend knows to delete it
                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_attachments[]';
                removeInput.value = path;
                this.$el.appendChild(removeInput);

                // Hide the attachment visually
                const attachmentElement = document.getElementById('currentAttachment_' + index);
                if (attachmentElement) {
                    attachmentElement.style.display = 'none';
                }

                // Close modal and reset
                this.cancelDelete();
            },

            /**
             * Cancel deletion
             */
            cancelDelete() {
                this.showDeleteModal = false;
                this.attachmentToDelete = null;
                this.deleteIndex = null;
            },

            clearNewAttachments() {
                this.newAttachments = [];
                if (this.fileInput) {
                    this.fileInput.value = '';
                }
            },

            removeNewAttachment(index) {
                this.newAttachments.splice(index, 1);

                if (this.fileInput) {
                    const dt = new DataTransfer();
                    this.newAttachments.forEach(f => dt.items.add(f));
                    this.fileInput.files = dt.files;
                }
            }
        };
    }
</script>



<!-- Keep the existing JavaScript and CSS unchanged -->
<script>
    // Image preview functionality
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview() {
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('event_image').value = '';
    }

    // Event Image Modal Functions
    function showEventImageModal(imageSrc) {
        const modal = document.getElementById('eventImageModal');
        const modalImage = document.getElementById('modalEventImage');
        const downloadLink = document.getElementById('downloadEventImage');
        
        modalImage.src = imageSrc;
        downloadLink.href = imageSrc;
        downloadLink.download = 'event-image.' + getFileExtension(imageSrc);
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function hideEventImageModal() {
        const modal = document.getElementById('eventImageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }

    function getFileExtension(filename) {
        return filename.split('.').pop().split('?')[0];
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideEventImageModal();
        }
    });

    // Close modal when clicking on backdrop
    document.getElementById('eventImageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideEventImageModal();
        }
    });


    // Toggle location fields based on event type
    document.addEventListener('DOMContentLoaded', function() {
        const eventTypeSelect = document.querySelector('select[name="event_type"]');
        const locationFields = document.getElementById('location-fields');
        
        function toggleLocationFields() {
            if (eventTypeSelect.value === 'virtual' || eventTypeSelect.value === 'event_type_Select') {
                locationFields.style.display = 'none';
            } else {
                locationFields.style.display = 'block';
            }
        }
        
        eventTypeSelect.addEventListener('change', toggleLocationFields);
        toggleLocationFields(); // Initial call
        
        // Toggle custom audience field
        const targetAudienceSelect = document.getElementById('target_audience');
        const customAudienceField = document.getElementById('custom-audience-field');
        const customAudienceInput = document.getElementById('custom_target_audience');
        
        function toggleCustomAudience() {
            if (!targetAudienceSelect || !customAudienceField) return;
            if (targetAudienceSelect.value === 'custom') {
                customAudienceField.classList.remove('hidden');
            } else {
                customAudienceField.classList.add('hidden');
                if (customAudienceInput) customAudienceInput.value = '';

            }
        }
        
        if (targetAudienceSelect) {
            targetAudienceSelect.addEventListener('change', toggleCustomAudience);
            toggleCustomAudience();

            const audienceForm = targetAudienceSelect.closest('form');
            if (audienceForm) {
                audienceForm.addEventListener('submit', () => {
                    if (targetAudienceSelect.value === 'custom') {
                        const customValue = (customAudienceInput?.value || '').trim();
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'target_audience';
                        hiddenInput.value = customValue || 'custom';
                        audienceForm.appendChild(hiddenInput);
                        targetAudienceSelect.disabled = true;
                    }
                });
            }
        }


        // Toggle custom audience field
        const categorySelect = document.getElementById('category');
        const customCategoryField = document.getElementById('custom-category-field');
        const customCategoryInput = document.getElementById('custom_category');
        
        function toggleCustomCategory() {
            if (!categorySelect || !customCategoryField) return;
            if (categorySelect.value === 'custom') {
                customCategoryField.classList.remove('hidden');
            } else {
                customCategoryField.classList.add('hidden');
                if (customCategoryInput) customCategoryInput.value = '';
            }
        }
        
        if (categorySelect) {
            categorySelect.addEventListener('change', toggleCustomCategory);
            toggleCustomCategory();

            const categoryForm = categorySelect.closest('form');
            if (categoryForm) {
                categoryForm.addEventListener('submit', () => {
                    if (categorySelect.value === 'custom') {
                        const customValue = (customCategoryInput?.value || '').trim();
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'category';
                        hiddenInput.value = customValue || 'custom';
                        categoryForm.appendChild(hiddenInput);
                        categorySelect.disabled = true;
                    }
                });
            }
        }
        
        // Toggle cost amount field
        const freeEventCheckbox = document.getElementById('free_event');
        const costAmountField = document.getElementById('cost-amount-field');
        
        function toggleCostAmount() {
            if (freeEventCheckbox.checked) {
                costAmountField.classList.add('hidden');
            } else {
                costAmountField.classList.remove('hidden');
            }
        }
        
        freeEventCheckbox.addEventListener('change', toggleCostAmount);
        toggleCostAmount(); // Initial call
        
        // Toggle registration link based on on-site registration
        const registerOnSiteCheckbox = document.getElementById('register_on_site');
        const registrationLinkField = document.getElementById('registration-link-field');
        
        function toggleRegistrationLink() {
            if (registerOnSiteCheckbox.checked) {
                registrationLinkField.style.display = 'none';
            } else {
                registrationLinkField.style.display = 'block';
            }
        }
        
        registerOnSiteCheckbox.addEventListener('change', toggleRegistrationLink);
        toggleRegistrationLink(); // Initial call
    });
</script>

<style>
    /* Custom checkbox styling */
    input[type="checkbox"]:checked + label {
        background-color: #3b82f6;
    }
    
    input[type="checkbox"]:checked + label span {
        transform: translateX(1.5rem);
    }
    
    /* Focus styles for better accessibility */
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
</style>