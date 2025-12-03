<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-300">{{ __('Fill in the details to') }} {{ isset($leader) ? __('update') : __('create') }} {{ __('the leader profile') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Form Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Basic Information') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Essential details about the leader') }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Full Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $leader->name ?? '') }}" 
                                placeholder="e.g., Dr. John Doe" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                required
                            >
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Position') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="position" 
                                value="{{ old('position', $leader->position ?? '') }}" 
                                placeholder="e.g., Project Principal Investigator" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('position') border-red-500 @enderror"
                                required
                            >
                            @error('position')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Biography / Description') }}</label>
                            <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                {{ __('Provide a detailed biography or description of the leader.') }}
                            </p>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="8" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                            >{!! old('description', $leader->description ?? '') !!}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                
            </div>

            <!-- Right Column - Settings -->
            <div class="space-y-6">
                <!-- Settings Card -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Settings') }}
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Sort Order') }}
                            </label>
                            <input 
                                type="number" 
                                name="sort_order" 
                                value="{{ old('sort_order', $leader->sort_order ?? 0) }}" 
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('sort_order') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Lower numbers appear first') }}</p>
                            @error('sort_order')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="space-y-2">
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    value="1"
                                    {{ old('is_active', $leader->is_active ?? true) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                                >
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Active') }}</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Only active leaders are displayed on the website') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Leader Image -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Leader Photo') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('Upload a professional photo of the leader') }}</p>
                    </div>
                    <div class="p-6">
                        @if(isset($leader) && $leader->image)
                            <div class="relative mb-4">
                                <img 
                                    src="{{ asset('storage/' . $leader->image) }}" 
                                    alt="{{ $leader->name }}" 
                                    class="w-full h-48 object-cover rounded-lg border border-gray-300"
                                >
                                <div class="absolute top-2 right-2">
                                    <label class="flex items-center gap-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm cursor-pointer hover:bg-red-600 transition-colors">
                                        <input type="checkbox" name="remove_image" value="1" class="hidden" id="remove_image">
                                        <iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>
                                        {{ __('Remove') }}
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                            <input 
                                id="image" 
                                name="image" 
                                type="file" 
                                accept="image/*" 
                                class="hidden"
                                onchange="previewLeaderImage(this)"
                            >
                            <label 
                                for="image" 
                                class="cursor-pointer flex flex-col items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ isset($leader) && $leader->image ? __('Upload new photo') : __('Upload leader photo') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ __('PNG, JPG, GIF up to 10MB') }}
                                </span>
                            </label>
                        </div>

                        <div id="image-preview" class="mt-4 hidden">
                            <div class="relative">
                                <img id="preview" class="w-full h-48 object-cover rounded-lg border border-gray-300">
                                <button type="button" onclick="removeLeaderPreview()" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @error('image')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md overflow-hidden">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full btn btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2"></iconify-icon>
                            {{ isset($leader) ? __('Update Leader') : __('Create Leader') }}
                        </button>
                        <a href="{{ route('admin.ahc-leaders.index') }}" class="w-full btn btn-secondary block text-center">
                            <iconify-icon icon="lucide:x" class="mr-2"></iconify-icon>
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewLeaderImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeLeaderPreview() {
        document.getElementById('image').value = '';
        document.getElementById('image-preview').classList.add('hidden');
    }
</script>
@endpush
