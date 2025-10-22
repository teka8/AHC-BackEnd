{!! Hook::applyFilters('filter.event.form_start', '') !!}

<input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h1>
            <p class="text-gray-600 text-lg">Fill in the details to {{ isset($event) ? 'update' : 'create' }} your event</p>

            @if($mode === 'edit')
                {{-- status --}}
                <label class="block text-sm font-medium text-gray-700 mt-4">Status</label>
                <x-inputs.select name="status" :options="['draft' => 'Draft', 'published' => 'Published']" :value="old('status', $event->status ?? 'draft')"/>
            @endif
        </div>

        <form method="POST" action="{{ isset($event) ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

            <!-- Basic Information -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        Basic Information
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Essential details about your event</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title', $event->title ?? '') }}" 
                            placeholder="e.g., Annual Tech Conference 2025" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                            required
                        >
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <input 
                            type="text" 
                            name="category" 
                            value="{{ old('category', $event->category ?? '') }}" 
                            placeholder="e.g., Technology, Business, Arts" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                    </div> --}}

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select 
                            name="category" 
                            id="category" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                        >
                            <option value="">— Select —</option>
                            <option value="lectures" {{ old('category', $event->category ?? '') === 'lectures' ? 'selected' : '' }}>Lectures</option>
                            <option value="festivals" {{ old('category', $event->category ?? '') === 'festivals' ? 'selected' : '' }}>Festivals</option>
                            <option value="seminars" {{ old('category', $event->category ?? '') === 'seminars' ? 'selected' : '' }}>Seminars</option>
                            <option value="anniversary" {{ old('category', $event->category ?? '') === 'anniversary' ? 'selected' : '' }}>Anniversary</option>
                            <option value="hackathon" {{ old('category', $event->category ?? '') === 'hackathon' ? 'selected' : '' }}>Hackathon</option>
                            <option value="custom" {{ old('category', $event->category ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <div id="custom-category-field" class="space-y-2 hidden">
                        <label class="block text-sm font-medium text-gray-700">Custom Category</label>
                        <input 
                            type="text" 
                            name="category" 
                            value="{{ old('category', $event->category ?? '') }}" 
                            placeholder="Add your custom category" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                    </div>

                    

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="text-xs text-gray-500 mb-2">
                            Describe your event in detail. Use the toolbar to format text, add images, and create links.
                        </p>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="10" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >{!! old('description', $event->description ?? '') !!}</textarea>
                    </div>
                </div>
            </div>

            {!! Hook::applyFilters('filter.event.form_after_title', '') !!}

            <!-- Event Image -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        Event Image
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Add a visual representation of your event</p>
                </div>
                <div class="p-6">
                    @if(isset($event) && $event->image_url)
                        <div class="relative mb-4">
                            <img 
                                src="{{ $event->image_url }}" 
                                alt="Event preview" 
                                class="w-full h-48 object-cover rounded-lg border border-gray-300"
                            >
                        </div>
                    @endif
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                        <input 
                            id="image" 
                            name="image_url" 
                            type="file" 
                            accept="image/*" 
                            class="hidden"
                            onchange="previewImage(this)"
                        >
                        <label 
                            for="image" 
                            class="cursor-pointer flex flex-col items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-sm text-gray-500">
                                Click to upload event image
                            </span>
                            <span class="text-xs text-gray-400">
                                PNG, JPG up to 10MB
                            </span>
                        </label>
                    </div>
                    
                    
                    <p class="text-xs text-gray-500 mt-3">
                        You can upload an image or provide an image URL. Uploaded images are stored in media library.
                    </p>
                    
                    <div id="image-preview" class="mt-4 hidden">
                        <div class="relative">
                            <img id="preview" class="w-full h-48 object-cover rounded-lg border border-gray-300">
                            <button type="button" onclick="removePreview()" class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-full hover:bg-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date, Time & Location -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Date, Time & Location
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">When and where is your event happening?</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Event Date <span class="text-red-500">*</span>
                        </label>
                        <x-inputs.date-picker name="event_date" value="{{ old('event_date', isset($event) ? $event->event_date?->format('Y-m-d') : '') }}" required/>
                        @error('event_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                       
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <x-inputs.time-picker name="start_time" value="{{ old('start_time', $event->start_time ?? '') }}"
                                required/>
                        
                            @error('start_time')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">End Time</label>
                            <x-inputs.time-picker name="end_time" value="{{ old('end_time', $event->end_time ?? '') }}"/>
                            @error('end_time')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>

                        Event Type
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Select the type of event you are creating.</p>
                

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="event_type" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                        >
                            <option value="in-person" {{ old('event_type', $event->event_type ?? 'in-person') === 'in-person' ? 'selected' : '' }}>In-Person</option>
                            <option value="virtual" {{ old('event_type', $event->event_type ?? '') === 'virtual' ? 'selected' : '' }}>Virtual (Online)</option>
                        </select>
                    </div>

                    <div id="location-fields">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Location
                            </label>
                            <input 
                                type="text" 
                                name="location" 
                                value="{{ old('location', $event->location ?? '') }}" 
                                placeholder="Venue address or location name" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                        </div>

                        <div class="space-y-2 mt-3">
                            <label class="block text-sm font-medium text-gray-700">Google Maps Link</label>
                            <input 
                                type="url" 
                                name="google_map_location_link" 
                                value="{{ old('google_map_location_link', $event->google_map_location_link ?? '') }}" 
                                placeholder="https://maps.google.com/..." 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration & Participants -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        Registration & Participants
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Manage how people can register for your event</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Target Audience</label>
                        <select 
                            name="target_audience" 
                            id="target_audience" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                        >
                            <option value="">— Select —</option>
                            <option value="public" {{ old('target_audience', $event->target_audience ?? '') === 'public' ? 'selected' : '' }}>Public</option>
                            <option value="for_students" {{ old('target_audience', $event->target_audience ?? '') === 'for_students' ? 'selected' : '' }}>Students</option>
                            <option value="for_staff" {{ old('target_audience', $event->target_audience ?? '') === 'for_staff' ? 'selected' : '' }}>Staff</option>
                            <option value="for_employees" {{ old('target_audience', $event->target_audience ?? '') === 'for_employees' ? 'selected' : '' }}>Employees</option>
                            <option value="for_alumni" {{ old('target_audience', $event->target_audience ?? '') === 'for_alumni' ? 'selected' : '' }}>Alumni</option>
                            <option value="custom" {{ old('target_audience', $event->target_audience ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <div id="custom-audience-field" class="space-y-2 hidden">
                        <label class="block text-sm font-medium text-gray-700">Custom Target Audience</label>
                        <input 
                            type="text" 
                            name="target_audience" 
                            value="{{ old('target_audience', $event->target_audience ?? '') }}" 
                            placeholder="e.g., Industry professionals, Investors, etc." 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="space-y-0.5">
                            <label class="block text-base font-medium text-gray-700">On-site Registration</label>
                            <p class="text-sm text-gray-500">Allow participants to register at the venue</p>
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
                        <label class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                            </svg>
                            Registration Link
                        </label>
                        <input 
                            type="url" 
                            name="registration_link" 
                            value="{{ old('registration_link', $event->registration_link ?? '') }}" 
                            placeholder="https://example.com/register" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                        </svg>
                        Pricing
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Set the cost for your event</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="space-y-0.5">
                            <label class="block text-base font-medium text-gray-700">Free Event</label>
                            <p class="text-sm text-gray-500">This event is free to attend</p>
                        </div>
                        <div class="relative inline-block w-12 h-6">
                            <input 
                                type="checkbox" 
                                name="free_event" 
                                id="free_event" 
                                value="1" 
                                class="sr-only" 
                                {{ (old('free_event', $event->cost_amount ?? 0) == 0) ? 'checked' : '' }}
                            >
                            <label 
                                for="free_event" 
                                class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300"
                            ></label>
                            <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                        </div>
                    </div>

                    <div id="cost-amount-field" class="space-y-2 hidden">
                        <label class="block text-sm font-medium text-gray-700">Cost Amount (ETB)</label>
                        <input 
                            type="number" 
                            name="cost_amount" 
                            step="0.01" 
                            value="{{ old('cost_amount', $event->cost_amount ?? '') }}" 
                            placeholder="0.00" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        Attachments
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Upload additional files for your event</p>
                </div>
                <div class="p-6 space-y-5">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                        <input 
                            id="attachments" 
                            name="attachments[]" 
                            type="file" 
                            multiple 
                            class="hidden"
                        >
                        <label 
                            for="attachments" 
                            class="cursor-pointer flex flex-col items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-sm text-gray-500">
                                Click to upload attachments
                            </span>
                            <span class="text-xs text-gray-400">
                                PDF, DOC, DOCX, or any file type up to 10MB
                            </span>
                        </label>
                    </div>

                    @if(isset($event) && $event->attachments && count($event->attachments) > 0)
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Current Attachments</label>
                            <div class="space-y-2">
                                @foreach($event->attachments as $attachment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium">{{ $attachment->filename }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ number_format($attachment->size / 1024, 2) }} KB
                                                </p>
                                            </div>
                                        </div>
                                        <a 
                                            href="{{ route('admin.events.attachment.delete', [$event, $attachment]) }}" 
                                            class="text-gray-400 hover:text-red-500 transition-colors"
                                            onclick="return confirm('Are you sure you want to delete this attachment?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

                

            <div class="flex justify-end gap-4 pt-4">
                <a 
                    href="{{ route('admin.events.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                >
                    {{ isset($event) ? 'Update Event' : 'Create Event' }}
                </button>
            </div>
               
          
        </form>
    </div>
</div>

{!! Hook::applyFilters('filter.event.form_end', '') !!}

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
        document.getElementById('image').value = '';
    }

    // Toggle location fields based on event type
    document.addEventListener('DOMContentLoaded', function() {
        const eventTypeSelect = document.querySelector('select[name="event_type"]');
        const locationFields = document.getElementById('location-fields');
        
        function toggleLocationFields() {
            if (eventTypeSelect.value === 'virtual') {
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
        
        function toggleCustomAudience() {
            if (targetAudienceSelect.value === 'custom') {
                customAudienceField.classList.remove('hidden');
            } else {
                customAudienceField.classList.add('hidden');
            }
        }
        
        targetAudienceSelect.addEventListener('change', toggleCustomAudience);
        toggleCustomAudience(); // Initial call

        // Toggle custom audience field
        const categorySelect = document.getElementById('category');
        const customCategoryField = document.getElementById('custom-category-field');
        
        function toggleCustomCategory() {
            if (categorySelect.value === 'custom') {
                customCategoryField.classList.remove('hidden');
            } else {
                customCategoryField.classList.add('hidden');
            }
        }
        
        categorySelect.addEventListener('change', toggleCustomCategory);
        toggleCustomCategory(); // Initial call
        
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