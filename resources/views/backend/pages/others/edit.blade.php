<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
@php
    $articles = $document->newsletterArticles->map(fn($a) => [
        'id' => $a->id,
        'title' => $a->title,
        'subtitle' => $a->subtitle,
        'content' => $a->content,
        'image_url' => $a->image_url,
    ]);
@endphp
    <div x-data="{ 
        isNewsletter: {{ $document->resource_type === \App\Models\Others::TYPE_NEWSLETTER ? 'true' : 'false' }},
        articles: @json($articles),
        newsletter_volume: @json(old('newsletter_volume', (string)$document->newsletter_volume)),
        newsletter_issue: @json(old('newsletter_issue', (string)$document->newsletter_issue))
    }" class="space-y-6">
        <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white">
                    {{ __('Edit Other Resource') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Update resource information and metadata') }}
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <form action="{{ route('admin.others.update', $document->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Resource Information -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:file-text" class="mr-2"></iconify-icon>
                                {{ __('Resource Information') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Title -->
                                <div class="col-span-2">
                                    <label for="title"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Title') }} *
                                    </label>
                                    <input type="text" id="title" name="title"
                                        value="{{ old('title', $document->title) }}" required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Creator (Author) -->
                                <div>
                                    <label for="author"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Author') }} *
                                    </label>
                                    <input type="text" id="author" name="author"
                                        value="{{ old('author', $document->creator) }}" required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('author')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Publication Date (optional mapping) -->
                                <div>
                                    <label for="publication_date"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Publication Date') }} *
                                    </label>
                                    <input type="date" id="publication_date" name="publication_date"
                                        value="{{ old('publication_date', $document->published_at?->format('Y-m-d')) }}"
                                        required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                    @error('publication_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description (Abstract) -->
                            <div class="mt-4" x-show="!isNewsletter">
                                <label for="abstract"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Abstract / Summary') }} *
                                </label>
                                <textarea id="abstract" name="abstract" :required="!isNewsletter" rows="4"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                                    placeholder="{{ __('Provide a brief summary of the resource...') }}">{{ old('abstract', $document->description) }}</textarea>
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
                                <!-- Resource Type (uses types defined for Educational Resource Hub) -->
                                <div>
                                    <label for="document_type"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Resource Type') }} *
                                    </label>
                                    <div class="relative">
                                        <select id="document_type" name="document_type" required 
                                            :disabled="isNewsletter"
                                            @change="isNewsletter = ($event.target.value === 'Newsletter')"
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white disabled:bg-gray-100 dark:disabled:bg-gray-800/50 disabled:cursor-not-allowed">
                                            <option value="">{{ __('Select Resource Type') }}</option>
                                            @foreach ($documentTypes as $type)
                                                <option value="{{ $type }}"
                                                    {{ old('document_type', $document->resource_type) == $type ? 'selected' : '' }}>
                                                    {{ __($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <template x-if="isNewsletter">
                                            <input type="hidden" name="document_type" value="Newsletter">
                                        </template>
                                    </div>
                                    @error('document_type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Subject Area (Category) -->
                                <div>
                                    <label for="category"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Category') }} *
                                    </label>
                                    <select id="category" name="category" required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->name }}"
                                                {{ old('category', $document->subject_area) == $category->name ? 'selected' : '' }}>
                                                {{ __($category->name) }}
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
                                <label for="tags"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Tags') }}
                                </label>
                                <input type="text" id="tags" name="tags"
                                    value="{{ old('tags', $document->tags_list) }}"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                                    placeholder="{{ __('Add relevant tags separated by commas (e.g., research, health, education)') }}">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Add tags to make this resource easier to find through search') }}
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
                                <!-- Version (optional) -->
                                <div>
                                    <label for="version"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Version') }}
                                    </label>
                                    <input type="text" id="version" name="version"
                                        value="{{ old('version', $document->version ?? '1.0') }}" placeholder="1.0"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Status') }} *
                                    </label>
                                    <select id="status" name="status" required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('status', $document->status) == $value ? 'selected' : '' }}>
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
                                    <label for="access_level"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Access Level') }} *
                                    </label>
                                    <select id="access_level" name="access_level" required
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                        @foreach ($accessLevels as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('access_level', $document->access_level) == $value ? 'selected' : '' }}>
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
                                <!-- Featured Resource -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                        {{ old('is_featured', $document->is_featured) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-800">
                                    <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ __('Feature this resource') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Newsletter Articles (Dynamic) -->
                        <div x-show="isNewsletter" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-medium text-gray-700 dark:text-white">
                                    <iconify-icon icon="lucide:newspaper" class="mr-2"></iconify-icon>
                                    {{ __('Newsletter Articles') }}
                                </h4>
                                <button type="button" @click="articles.push({ id: null, title: '', subtitle: '', content: '', image_url: null })" 
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-primary/10 text-primary hover:bg-primary/20">
                                    <iconify-icon icon="lucide:plus" class="mr-1"></iconify-icon>
                                    {{ __('Add More Articles') }}
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50/50 dark:bg-gray-800/30 rounded-lg">
                                <div>
                                    <label for="newsletter_volume" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                        {{ __('Newsletter Volume (Global)') }}
                                    </label>
                                    <input type="text" id="newsletter_volume" name="newsletter_volume" x-model="newsletter_volume"
                                        class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm dark:text-white"
                                        placeholder="e.g., Vol 1">
                                </div>
                                <div>
                                    <label for="newsletter_issue" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                        {{ __('Newsletter Issue Number (Global)') }}
                                    </label>
                                    <input type="text" id="newsletter_issue" name="newsletter_issue" x-model="newsletter_issue"
                                        class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm dark:text-white"
                                        placeholder="e.g., Issue 5">
                                </div>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(article, index) in articles" :key="index">
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 relative shadow-sm">
                                        <button type="button" @click="articles.splice(index, 1)" x-show="articles.length > 1"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1">
                                            <iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>
                                        </button>
                                        
                                        <input type="hidden" :name="'articles['+index+'][id]'" x-model="article.id">
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 mt-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Main Title (Above Image) - Optional') }}</label>
                                                <input type="text" :name="'articles['+index+'][title]'" x-model="article.title"
                                                    class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm focus:ring-primary focus:border-primary dark:text-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Subtitle (Below Image) - Optional') }}</label>
                                                <input type="text" :name="'articles['+index+'][subtitle]'" x-model="article.subtitle"
                                                    class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm focus:ring-primary focus:border-primary dark:text-white">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 gap-4 mb-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Article Image (Optional)') }}</label>
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1">
                                                        <input type="file" :name="'articles['+index+'][image]'" accept="image/*"
                                                            @change="
                                                                const file = $event.target.files[0];
                                                                if (file) {
                                                                    const reader = new FileReader();
                                                                    reader.onload = (e) => { article.image_url = e.target.result; };
                                                                    reader.readAsDataURL(file);
                                                                }
                                                            "
                                                            class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                                    </div>
                                                    <template x-if="article.image_url">
                                                        <div class="relative group">
                                                            <img :src="article.image_url" class="h-16 w-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-lg transition-opacity duration-200">
                                                                <iconify-icon icon="lucide:eye" class="text-white w-4 h-4"></iconify-icon>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Article Text') }}</label>
                                            <textarea :name="'articles['+index+'][content]'" x-model="article.content" required rows="4"
                                                class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm dark:text-white"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- File Update -->
                        <div x-show="!isNewsletter" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">
                                <iconify-icon icon="lucide:upload" class="mr-2"></iconify-icon>
                                {{ __('File Update') }}
                            </h4>

                            <!-- Current File Info -->
                            <div
                                class="mb-4 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    {{ __('Current File') }}
                                </h5>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <iconify-icon icon="lucide:file-text"
                                            class="text-blue-500 mr-2"></iconify-icon>
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-white">
                                                {{ $document->file_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $document->file_size_formatted }} @if (!empty($document->file_extension))
                                                    • {{ strtoupper($document->file_extension) }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ url('storage/' . $document->file_path) }}"
                                            target="_blank"
                                            class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-300"
                                            title="{{ __('View') }}">
                                            <iconify-icon icon="lucide:eye" class="text-sm"></iconify-icon>
                                        </a>
                                        <a href="{{ url('storage/' . $document->file_path) }}" download
                                            class="text-green-400 hover:text-green-600 dark:hover:text-green-300"
                                            title="{{ __('Download') }}">
                                            <iconify-icon icon="lucide:download" class="text-sm"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- New File Upload -->
                            <div>
                                <label for="file"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Update File (Optional)') }}
                                </label>
                                <input type="file" id="file" name="file"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Leave empty to keep the current file.') }}
                                </p>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('admin.others.index') }}" class="btn-default">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2"></iconify-icon>
                            {{ __('Update Resource') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resource Activity -->
        <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white">
                    {{ __('Resource Activity') }}
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
                            {{ $document->created_at?->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:user" class="text-gray-400 mr-2"></iconify-icon>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Created By') }}</span>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $document->creator?->name ?? ($document->creator ?? 'Unknown') }}
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
                    @if ($document->published_at)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <iconify-icon icon="lucide:globe" class="text-gray-400 mr-2"></iconify-icon>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Published At') }}</span>
                            </div>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $document->published_at?->format('M d, Y g:i A') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
