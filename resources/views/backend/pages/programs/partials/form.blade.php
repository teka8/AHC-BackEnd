<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">    
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-300">{{ __('Fill in the details to') }} {{ isset($program) ? 'update' : 'create' }} {{ __('your program') }}</p>
        </div>

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
                        <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Essential details about your program') }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Program Title') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                value="{{ old('title', $program->title ?? '') }}" 
                                placeholder="e.g., Health Innovation Workshop" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                required
                            >
                            @error('title')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Host') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="host" 
                                value="{{ old('host', $program->host ?? '') }}" 
                                placeholder="e.g., Africa Health Collaborative" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('host') border-red-500 @enderror"
                                required
                            >
                            @error('host')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Country') }}
                            </label>
                            <input
                                type="text"
                                name="country"
                                value="{{ old('country', $program->country ?? '') }}"
                                placeholder="e.g., Rwanda"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('country') border-red-500 @enderror"
                            >
                            @error('country')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            $rawContacts = old('contacts', $program->contact_people ?? []);
                            if (! is_array($rawContacts)) {
                                $rawContacts = [];
                            }
                            $preparedContacts = collect($rawContacts)
                                ->map(function ($contact) {
                                    if (! is_array($contact)) {
                                        return ['name' => '', 'bio' => '', 'contact' => ''];
                                    }

                                    return [
                                        'name' => $contact['name'] ?? ($contact['contact_name'] ?? ''),
                                        'bio' => $contact['bio'] ?? ($contact['contact_bio'] ?? ''),
                                        'contact' => $contact['contact'] ?? ($contact['contact_details'] ?? ''),
                                    ];
                                })
                                ->map(fn ($contact) => [
                                    'name' => trim((string) $contact['name']),
                                    'bio' => trim((string) $contact['bio']),
                                    'contact' => trim((string) $contact['contact']),
                                ])
                                ->filter(fn ($contact) => $contact['name'] !== '' || $contact['bio'] !== '' || $contact['contact'] !== '')
                                ->values()
                                ->toArray();

                            if (empty($preparedContacts)) {
                                $preparedContacts = [['name' => '', 'bio' => '', 'contact' => '']];
                            }
                        @endphp

                        <div class="space-y-4" x-data='programContacts(@json($preparedContacts))' x-cloak>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Contact Persons') }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Add one or more contact persons, including their bio and preferred contact details.') }}</p>
                                </div>
                                <button type="button" x-on:click="addContact()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                                    <span class="text-lg leading-none">+</span>
                                    <span>{{ __('Add Contact') }}</span>
                                </button>
                            </div>

                            <template x-for="(contact, index) in contacts" :key="index">
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ __('Contact Person') }} <span x-text="index + 1"></span>
                                        </h4>
                                        <button
                                            type="button"
                                            x-show="contacts.length > 1"
                                            x-on:click="removeContact(index)"
                                            class="inline-flex items-center gap-1 text-sm font-semibold text-red-600 hover:text-red-700"
                                        >
                                            <span class="text-lg leading-none">×</span>
                                            <span>{{ __('Remove') }}</span>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Name') }}</label>
                                            <input
                                                type="text"
                                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="`contacts[${index}][name]`"
                                                x-model="contact.name"
                                                placeholder="e.g., Dr. Jane Doe"
                                            >
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Contact Details') }}</label>
                                            <textarea
                                                rows="3"
                                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="`contacts[${index}][contact]`"
                                                x-model="contact.contact"
                                                placeholder="{{ __('Email, phone number, or preferred contact method') }}"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Bio') }}</label>
                                        <textarea
                                            rows="3"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            :name="`contacts[${index}][bio]`"
                                            x-model="contact.bio"
                                            placeholder="{{ __('Brief background for this contact person') }}"
                                        ></textarea>
                                    </div>
                                </div>
                            </template>

                            @foreach($errors->get('contacts.*.name') as $message)
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @endforeach
                            @foreach($errors->get('contacts.*.bio') as $message)
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @endforeach
                            @foreach($errors->get('contacts.*.contact') as $message)
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Partners Involved') }}
                            </label>
                            <textarea
                                name="partners_involved"
                                rows="4"
                                placeholder="{{ __('List partner universities or organizations participating in this program.') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('partners_involved') border-red-500 @enderror"
                            >{{ old('partners_involved', $program->partners_involved ?? '') }}</textarea>
                            @error('partners_involved')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            $categoryOptions = \App\Models\Program::getProgramCategories();
                            $defaultCategories = $program?->categories ?? [\App\Enums\ProgramCategory::UNCATEGORIZED->value];
                            $selectedCategories = \Illuminate\Support\Arr::wrap(old('categories', $defaultCategories));
                        @endphp

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Program Categories') }}
                            </label>
                            <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                {{ __('Select all categories that apply. Choose “Uncategorized” when none of the pillars fit.') }}
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($categoryOptions as $value => $label)
                                    <label class="flex items-start gap-2 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-400 dark:hover:border-blue-400 transition-colors bg-white dark:bg-gray-800">
                                        <input
                                            type="checkbox"
                                            name="categories[]"
                                            value="{{ $value }}"
                                            class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            @checked(in_array($value, $selectedCategories, true))
                                        >
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('categories')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            @error('categories.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                            <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                {{ __('Describe your program in detail. Use the toolbar to format text, add images, and create links.') }}
                            </p>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="8" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >{!! old('description', $program->description ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Program Image and State -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Program Image -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Program Image') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('Add program visual') }}</p>
                    </div>
                    <div class="p-6">
                        <x-media-selector name="image" :multiple="false" allowedTypes="images"
                            :existing-media="isset($program) && $program->hasImage() ? $program->getImageUrl() : null"
                            :existing-alt-text="isset($program) ? $program->title : ''"
                            remove-checkbox-name="remove_image" remove-checkbox-label="Remove image"
                            :show-preview="true" button-text="Select Image" />
                    </div>
                </div>



                <!-- Finalize Program Card -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Finalize Program') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('Complete your program setup') }}</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="text-center">
                                <p class="text-gray-600 mb-4 dark:text-gray-400">{{ __('Review all information and submit when ready') }}</p>
                                
                                <div class="flex justify-center gap-3">
                                    <a 
                                        href="{{ route('admin.programs.index') }}" 
                                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:text-gray-400"
                                    >
                                       {{ __('Cancel') }}
                                    </a>
                                    <button 
                                        type="submit" 
                                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                                    >
                                        {{ isset($program) ? __('Update Program') : __('Create Program') }}
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
    </div>
</div>
