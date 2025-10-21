{!! Hook::applyFilters(PostFilterHook::INSIDE_POST_FORM_START, '') !!}

<input type="hidden" name="post_id" value="{{ $post->id ?? '' }}" data-post-id="{{ $post->id ?? '' }}">
<input type="hidden" name="post_type" value="{{ $postType }}" data-post-type="{{ $postType }}">

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Main Content Area -->
    <div class="lg:col-span-3 space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 space-y-4 sm:p-6">
                <!-- Title and Slug -->
                <div x-data="slugGenerator('{{ old('title', $post->title ?? '') }}', '{{ old('slug', $post->slug ?? '') }}')">
                    <div class="space-y-1">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('News Title') }}</label>
                        <input type="text" name="title" id="title" required x-model="title" maxlength="255"
                            class="form-control" placeholder="{{ __('Enter news headline...') }}">
                    </div>

                    <!-- Slug -->
                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                        <span class="mr-1">{{ __('Permalink') }}:</span>
                        <span class="flex-1 truncate" x-show="!showSlugEdit">
                            <span class="text-gray-400">{{ url('/') }}/</span><span
                                class="font-medium text-primary" x-text="slug || '{{ __('auto-generated') }}'"></span>
                        </span>
                        <div class="flex-1" x-show="showSlugEdit">
                            <input type="text" name="slug" id="slug" x-model="slug" maxlength="200"
                                class="h-7 w-full rounded border border-gray-300 bg-transparent px-2 py-1 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        </div>
                        <div class="ml-2 flex space-x-1">
                            <button type="button" @click="toggleSlugEdit()" class="text-xs text-primary hover:underline">
                                <span x-show="!showSlugEdit">{{ __('Edit') }}</span>
                                <span x-show="showSlugEdit">{{ __('OK') }}</span>
                            </button>
                            <button type="button" @click="generateSlug()" class="text-xs text-primary hover:underline ml-2">
                                {{ __('Generate') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- News Content -->
                <div class="space-y-1">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('News Content') }}</label>
                    <textarea name="content" id="content" rows="15" 
                        class="form-control-textarea" 
                        placeholder="{{ __('Write your news article content here...') }}">{!! old('content', $post->content ?? '') !!}</textarea>
                </div>

                <!-- News-specific fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Author -->
                    <div class="space-y-1">
                        <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Author') }}</label>
                        <input type="text" name="author" id="author" 
                            class="form-control" 
                            value="{{ old('author', auth()->user()->full_name) }}"
                            placeholder="{{ __('News author name') }}">
                    </div>
                    
                    <!-- Publication Date -->
                    <div class="space-y-1">
                        <label for="published_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Publication Date') }}</label>
                        <input type="datetime-local" name="published_date" id="published_date" 
                            class="form-control" 
                            value="{{ old('published_date', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
                
                <!-- News Source -->
                <div class="space-y-1">
                    <label for="news_source" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('News Source') }}</label>
                    <input type="text" name="news_source" id="news_source" 
                        class="form-control" 
                        value="{{ old('news_source', '') }}"
                        placeholder="{{ __('e.g., University Press, Student Reporter, Faculty News') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Area -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Status -->
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-4 py-3 sm:px-6 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white">{{ __('Publish') }}</h3>
            </div>
            <div class="p-3 space-y-2 sm:p-4">
                @php
                    $userRole = auth()->user()->getRoleNames()->first();
                    $availableStatuses = [];
                    
                    if($userRole === 'admin' || $userRole === 'super-admin') {
                        $availableStatuses = [
                            ['value' => 'created', 'label' => __('Created'), 'class' => 'bg-yellow-100 text-yellow-800'],
                            ['value' => 'edited', 'label' => __('Edited'), 'class' => 'bg-blue-100 text-blue-800'],
                            ['value' => 'approved', 'label' => __('Approved'), 'class' => 'bg-green-100 text-green-800']
                        ];
                    } elseif($userRole === 'editor') {
                        $availableStatuses = [
                            ['value' => 'created', 'label' => __('Created'), 'class' => 'bg-yellow-100 text-yellow-800'],
                            ['value' => 'edited', 'label' => __('Edited'), 'class' => 'bg-blue-100 text-blue-800']
                        ];
                    } else {
                        $availableStatuses = [
                            ['value' => 'created', 'label' => __('Created'), 'class' => 'bg-yellow-100 text-yellow-800']
                        ];
                    }
                    
                    $currentStatus = old('status', $post->status ?? 'created');
                @endphp
                
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    
                    @foreach($availableStatuses as $status)
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $currentStatus === $status['value'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                        <input type="radio" name="status" value="{{ $status['value'] }}" 
                            class="mr-3" {{ $currentStatus === $status['value'] ? 'checked' : '' }}>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $status['label'] }}</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $status['class'] }}">{{ $status['label'] }}</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                    
                    <div class="mt-4">
                        <x-buttons.submit-buttons cancelUrl="{{ route('admin.news.index', $postType) }}" />
                    </div>
                </div>
            </div>
        </div>

        <!-- News Image -->
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-4 py-3 sm:px-6 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white">{{ __('News Image') }}</h3>
            </div>
            <div class="p-3 space-y-2 sm:p-4">
                <x-media-selector
                    name="featured_image"
                    label=""
                    :multiple="false"
                    allowedTypes="images"
                    :existingMedia="isset($post) && $post->hasFeaturedImage() ? $post->getFeaturedImageUrl() : null"
                    :existingAltText="isset($post) ? $post->title : ''"
                    removeCheckboxName="remove_featured_image"
                    removeCheckboxLabel="{{ __('Remove image') }}"
                    :showPreview="true"
                    class="mt-1"
                />
            </div>
        </div>


        <!-- Taxonomies -->
        @if (!empty($taxonomies))
            @foreach ($taxonomies as $taxonomy)
                @include('backend.pages.posts.partials.post-taxonomy-chooser', [
                    'taxonomy' => $taxonomy,
                    'post_type' => $postType,
                ])
            @endforeach
        @endif
    </div>
</div>