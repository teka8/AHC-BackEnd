<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white">
                    {{ __('Edit Document') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Update document information and metadata') }}
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <form action="{{ route('admin.document.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Document Information -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:file-text" class="mr-2"></iconify-icon>
                                {{ __('Document Information') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Title -->
                                <div class="col-span-2">
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Document Title') }} *
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $document->title) }}"
                                           required
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Author -->
                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Author') }} *
                                    </label>
                                    <input type="text" 
                                           id="author" 
                                           name="author" 
                                           value="{{ old('author', $document->author) }}"
                                           required
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('author')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Publication Date -->
                                <div>
                                    <label for="publication_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Publication Date') }} *
                                    </label>
                                    <input type="date" 
                                           id="publication_date" 
                                           name="publication_date" 
                                           value="{{ old('publication_date', $document->publication_date->format('Y-m-d')) }}"
                                           required
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('publication_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Abstract -->
                            <div class="mt-4">
                                <label for="abstract" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Abstract / Summary') }} *
                                </label>
                                <textarea id="abstract" 
                                          name="abstract" 
                                          required
                                          rows="4"
                                          class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                                          placeholder="{{ __('Provide a brief summary of the document content...') }}">{{ old('abstract', $document->abstract) }}</textarea>
                                @error('abstract')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Categorization -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:folder" class="mr-2"></iconify-icon>
                                {{ __('Categorization') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Document Type -->
                                <div>
                                    <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Document Type') }} *
                                    </label>
                                    <select id="document_type" 
                                            name="document_type" 
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        <option value="">{{ __('Select Document Type') }}</option>
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type }}" {{ old('document_type', $document->document_type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('document_type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Category -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Category') }} *
                                    </label>
                                    <select id="category" 
                                            name="category" 
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" {{ old('category', $document->category) == $category->name ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mt-4">
                                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Tags') }}
                                </label>
                                <input type="text" 
                                       id="tags" 
                                       name="tags" 
                                       value="{{ old('tags', $document->tags_list) }}"
                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                                       placeholder="{{ __('Add relevant tags separated by commas (e.g., research, health, education)') }}">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Add tags to make this document easier to find through search') }}
                                </p>
                            </div>
                        </div>

                        <!-- Settings & Status -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:settings" class="mr-2"></iconify-icon>
                                {{ __('Settings & Status') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Version -->
                                <div>
                                    <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Version') }}
                                    </label>
                                    <input type="text" 
                                           id="version" 
                                           name="version" 
                                           value="{{ old('version', $document->version) }}"
                                           placeholder="1.0"
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Status') }} *
                                    </label>
                                    <select id="status" 
                                            name="status" 
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', $document->status) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Access Level -->
                                <div>
                                    <label for="access_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Access Level') }} *
                                    </label>
                                    <select id="access_level" 
                                            name="access_level" 
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        @foreach($accessLevels as $value => $label)
                                            <option value="{{ $value }}" {{ old('access_level', $document->access_level) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('access_level')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Options -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <!-- Featured Document -->
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="is_featured" 
                                           name="is_featured" 
                                           value="1"
                                           {{ old('is_featured', $document->is_featured) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-800">
                                    <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ __('Feature this document') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- File Update -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:upload" class="mr-2"></iconify-icon>
                                {{ __('File Update') }}
                            </h4>

                            <!-- Current File Info -->
                            <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    {{ __('Current File') }}
                                </h5>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <iconify-icon icon="lucide:file-text" class="text-blue-500 mr-2"></iconify-icon>
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-white">{{ $document->file_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $document->file_size_formatted }} • {{ strtoupper($document->file_extension) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Storage::disk('public')->url($document->file_path) }}" 
                                           target="_blank"
                                           class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-300"
                                           title="{{ __('View') }}">
                                            <iconify-icon icon="lucide:eye" class="text-sm"></iconify-icon>
                                        </a>
                                        <a href="{{ Storage::disk('public')->url($document->file_path) }}" 
                                           download
                                           class="text-green-400 hover:text-green-600 dark:hover:text-green-300"
                                           title="{{ __('Download') }}">
                                            <iconify-icon icon="lucide:download" class="text-sm"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- New File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Update File (Optional)') }}
                                </label>
                                <input type="file" 
                                       id="file" 
                                       name="file"
                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.odt,.ods,.odp">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Leave empty to keep the current file. Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV') }}
                                </p>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('admin.document.index') }}" class="btn-default">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2"></iconify-icon>
                            {{ __('Update Document') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Document Activity -->
        <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white">
                    {{ __('Document Activity') }}
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:calendar" class="text-gray-400 mr-2"></iconify-icon>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Created') }}</span>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $document->created_at->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:user" class="text-gray-400 mr-2"></iconify-icon>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Created By') }}</span>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $document->creator->name ?? 'Unknown' }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:download" class="text-gray-400 mr-2"></iconify-icon>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Downloads') }}</span>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $document->download_count }}
                        </div>
                    </div>
                    @if($document->published_at)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:globe" class="text-gray-400 mr-2"></iconify-icon>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Published At') }}</span>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $document->published_at->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>