<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Venture: {{ $venture->name }}</h2>

        <form method="POST" action="{{ route('admin.ventures.update', $venture) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Venture Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $venture->name) }}" required
                                   class="form-input w-full @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tagline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tagline
                            </label>
                            <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $venture->tagline) }}"
                                   class="form-input w-full @error('tagline') border-red-500 @enderror">
                            @error('tagline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" required
                                      class="form-textarea w-full @error('description') border-red-500 @enderror">{{ old('description', $venture->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="focus_area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Focus Area <span class="text-red-500">*</span>
                            </label>
                            <select name="focus_area" id="focus_area" required
                                    class="form-select w-full @error('focus_area') border-red-500 @enderror">
                                <option value="">Select Focus Area</option>
                                <option value="mental-health" {{ old('focus_area', $venture->focus_area) == 'mental-health' ? 'selected' : '' }}>Mental Health</option>
                                <option value="telemedicine" {{ old('focus_area', $venture->focus_area) == 'telemedicine' ? 'selected' : '' }}>Telemedicine</option>
                                <option value="pharmaceuticals" {{ old('focus_area', $venture->focus_area) == 'pharmaceuticals' ? 'selected' : '' }}>Pharmaceuticals</option>
                                <option value="biotech" {{ old('focus_area', $venture->focus_area) == 'biotech' ? 'selected' : '' }}>Biotech</option>
                                <option value="medtech" {{ old('focus_area', $venture->focus_area) == 'medtech' ? 'selected' : '' }}>MedTech</option>
                                <option value="diagnostics" {{ old('focus_area', $venture->focus_area) == 'diagnostics' ? 'selected' : '' }}>Diagnostics</option>
                                <option value="health-tech" {{ old('focus_area', $venture->focus_area) == 'health-tech' ? 'selected' : '' }}>Health Tech</option>
                                <option value="other" {{ old('focus_area', $venture->focus_area) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('focus_area')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Stage <span class="text-red-500">*</span>
                            </label>
                            <select name="stage" id="stage" required
                                    class="form-select w-full @error('stage') border-red-500 @enderror">
                                <option value="">Select Stage</option>
                                <option value="idea" {{ old('stage', $venture->stage) == 'idea' ? 'selected' : '' }}>Idea</option>
                                <option value="prototype" {{ old('stage', $venture->stage) == 'prototype' ? 'selected' : '' }}>Prototype</option>
                                <option value="early-stage" {{ old('stage', $venture->stage) == 'early-stage' ? 'selected' : '' }}>Early Stage</option>
                                <option value="growth" {{ old('stage', $venture->stage) == 'growth' ? 'selected' : '' }}>Growth</option>
                                <option value="scale" {{ old('stage', $venture->stage) == 'scale' ? 'selected' : '' }}>Scale</option>
                            </select>
                            @error('stage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="founded_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Founded Year
                            </label>
                            <input type="number" name="founded_year" id="founded_year" value="{{ old('founded_year', $venture->founded_year) }}" 
                                   min="1900" max="{{ date('Y') }}"
                                   class="form-input w-full @error('founded_year') border-red-500 @enderror">
                            @error('founded_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="country" id="country" value="{{ old('country', $venture->country) }}" required
                                   class="form-input w-full @error('country') border-red-500 @enderror">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Website
                            </label>
                            <input type="url" name="website" id="website" value="{{ old('website', $venture->website) }}"
                                   class="form-input w-full @error('website') border-red-500 @enderror">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="founders" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Founders <span class="text-red-500">*</span>
                            </label>
                            <textarea name="founders" id="founders" rows="2" required
                                      class="form-textarea w-full @error('founders') border-red-500 @enderror">{{ old('founders', $venture->founders) }}</textarea>
                            @error('founders')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="team_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Size
                            </label>
                            <input type="number" name="team_size" id="team_size" value="{{ old('team_size', $venture->team_size) }}" min="1"
                                   class="form-input w-full @error('team_size') border-red-500 @enderror">
                            @error('team_size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Impact Metrics -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Impact Metrics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="funding_raised" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Funding Raised ($)
                            </label>
                            <input type="number" name="funding_raised" id="funding_raised" value="{{ old('funding_raised', $venture->funding_raised) }}" 
                                   min="0" step="0.01"
                                   class="form-input w-full @error('funding_raised') border-red-500 @enderror">
                            @error('funding_raised')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patients_impacted" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Patients Impacted
                            </label>
                            <input type="number" name="patients_impacted" id="patients_impacted" value="{{ old('patients_impacted', $venture->patients_impacted) }}" 
                                   min="0"
                                   class="form-input w-full @error('patients_impacted') border-red-500 @enderror">
                            @error('patients_impacted')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="countries_reached" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Countries Reached
                            </label>
                            <input type="number" name="countries_reached" id="countries_reached" value="{{ old('countries_reached', $venture->countries_reached) }}" 
                                   min="0"
                                   class="form-input w-full @error('countries_reached') border-red-500 @enderror">
                            @error('countries_reached')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="form-select w-full @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $venture->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $venture->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center pt-8">
                            <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $venture->featured) ? 'checked' : '' }}
                                   class="form-checkbox h-4 w-4 text-primary-600">
                            <label for="featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Mark as Featured
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.ventures.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Venture
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.backend-layout>
