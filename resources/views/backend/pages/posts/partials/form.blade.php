{!! Hook::applyFilters(PostFilterHook::INSIDE_POST_FORM_START, '') !!}

<input type="hidden" name="post_id" value="{{ $post->id ?? '' }}" data-post-id="{{ $post->id ?? '' }}">
<input type="hidden" name="post_type" value="{{ $postType }}" data-post-type="{{ $postType }}">

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Main Content Area -->
    <div class="lg:col-span-3 space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Content Details') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Enter the main content information') }}</p>
            </div>
            <div class="p-6 space-y-6">
                <!-- Title and Slug with Alpine.js -->
                <div x-data="slugGenerator('{{ old('title', $post->title ?? '') }}', '{{ old('slug', $post->slug ?? '') }}')">
                    <!-- Title -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Title') }}</label>
                            @can('ai_content.generate')
                                <div x-data="{ aiModalOpen: false }">
                                    @include('backend.pages.posts.partials.ai-content-generator')
                                </div>
                            @endcan
                        </div>
                        <input type="text" name="title" id="title" required x-model="title" maxlength="255"
                            class="form-control">
                    </div>
                    {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_TITLE, '') !!}

                    <!-- Compact Slug UI -->
                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-300">
                        <span class="mr-1">{{ __('Permalink') }}:</span>
                        <span class="flex-1 truncate" x-show="!showSlugEdit">
                            <span class="text-gray-400">{{ url('/') }}/</span><span
                                class="font-medium text-primary" x-text="slug || '{{ __('auto-generated') }}'"></span>
                        </span>
                        <div class="flex-1" x-show="showSlugEdit">
                            <input type="text" name="slug" id="slug" x-model="slug" maxlength="200"
                                class="h-7 w-full rounded border border-gray-300 bg-transparent px-2 py-1 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                placeholder="{{ __('Leave empty to auto-generate') }}">
                        </div>
                        <div class="ml-2 flex space-x-1">
                            <!-- Edit/Save Button -->
                            <button type="button" @click="toggleSlugEdit()"
                                class="text-xs text-primary hover:underline">
                                <span x-show="!showSlugEdit">{{ __('Edit') }}</span>
                                <span x-show="showSlugEdit">{{ __('OK') }}</span>
                            </button>
                            <!-- Generate Button -->
                            <button type="button" @click="generateSlug()"
                                class="text-xs text-primary hover:underline ml-2">
                                {{ __('Generate') }}
                            </button>
                        </div>
                    </div>
                    {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_SLUG, '') !!}
                </div>

                @if ($postTypeModel->supports_editor)
                    <div class="space-y-1">
                        <label for="content"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Content') }}</label>
                        <textarea name="content" id="content" rows="10">{!! old('content', $post->content ?? '') !!}</textarea>
                    </div>
                @endif
                @if ($postTypeModel->supports_editor)
                    @role('News Admin')
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Admin Controls') }}</h4>
                            <div class="flex items-center gap-3">
                                <input name="status" type="checkbox" id="status" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    @if (!empty($post) && $post->status === 'approved') checked disabled @endif />
                                <label for="status" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Approved') }}</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input name="status" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                    @if (!empty($post) && $post->status === 'approved') disabled @endif value="created" />
                                <label for="status" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Change Status to Editable') }}</label>
                            </div>
                        </div>
                    @endrole
                @endif
                {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_CONTENT, '') !!}

                @if ($postTypeModel->supports_excerpt)
                    <div class="space-y-1">
                        <label for="excerpt"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Excerpt') }}</label>
                        <textarea name="excerpt" id="excerpt" rows="3" class="form-control-textarea">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                            {{ __('A short summary of the content') }}.
                            {{ __('Leave empty to auto-generate from content') }}</p>
                    </div>
                @endif
                {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_EXCERPT, '') !!}
            </div>
        </div>

        @if ($postTypeModel->supports_thumbnail)
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Media Gallery') }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Upload images for your content') }}</p>
                </div>
                <div class="p-6">
                    <div x-data="{ files: [], dragOver: false }">
                        <div 
                            @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false"
                            @drop.prevent="dragOver = false; files = [...files, ...$event.dataTransfer.files]"
                            :class="dragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                            class="border-2 border-dashed rounded-lg p-8 text-center transition-colors">
                            <div class="space-y-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-300">{{ __('Drag and drop images here, or') }}</p>
                                    <label class="cursor-pointer text-blue-600 hover:text-blue-500">
                                        <span>{{ __('browse files') }}</span>
                                        <input type="file" name="images[]" multiple accept="image/*" class="hidden" 
                                            @change="files = [...files, ...$event.target.files]">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">{{ __('PNG, JPG, GIF up to 10MB each') }}</p>
                            </div>
                        </div>
                        <div x-show="files.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="relative group">
                                    <img :src="URL.createObjectURL(file)" :alt="file.name" class="w-full h-24 object-cover rounded-lg">
                                    <button type="button" @click="files.splice(index, 1)" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate" x-text="file.name"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_FEATURED_IMAGE, '') !!}
    </div>

    <!-- Sidebar Area -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Current Status -->
        @if (!empty($post))
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Current Status') }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    @php
                        $statusColors = [
                            'published' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                            'created' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                            'edited' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                            'archived' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                        ];
                        $colorClass = $statusColors[$post->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                        {{ ucfirst($post->status) }}
                    </span>
                    

                </div>
            </div>
        @endif

        <!-- Status and Visibility -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Publish') }}</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Author Info -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('Author Details') }}
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Email') }}</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ auth()->user()->email }}</span>
                        </div>
                        @if(auth()->user()->roles->isNotEmpty())
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Role') }}</span>
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                    {{ auth()->user()->roles->first()->name }}
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date') }}</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- <!-- Status with Combobox -->
                @php
                    $statusOptions = Hook::applyFilters(PostFilterHook::POST_STATUS_OPTIONS, [
                        ['value' => 'draft', 'label' => __('Draft')],
                        ['value' => 'published', 'label' => __('Published')],
                        ['value' => 'pending', 'label' => __('Pending Review')],
                        ['value' => 'scheduled', 'label' => __('Scheduled')],
                        ['value' => 'private', 'label' => __('Private')],
                    ]);
                    $currentStatus = old('status', $post->status ?? App\Enums\PostStatus::DRAFT->value);
                @endphp

                <x-inputs.combobox
                    name="status"
                    label="{{ __('Status') }}"
                    :options="$statusOptions"
                    :selected="$currentStatus"
                    :multiple="false"
                    :searchable="false"
                    x-model="status"
                />

                {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_STATUS, '') !!}

                <!-- Publish Date (for scheduled posts) -->
                <div x-data="{
                    showSchedule: {{ isset($post) && (old('status', $post->status) === 'scheduled' || $post->published_at) ? 'true' : 'false' }},
                    status: '{{ old('status', $post->status ?? App\Enums\PostStatus::DRAFT->value) }}',
                    init() {
                        this.$watch('status', value => {
                            if (value === 'scheduled') {
                                this.showSchedule = true;
                            }
                        });
                    }
                }">
                    <div class="mb-2">
                        <input type="checkbox" id="schedule_post" name="schedule_post" x-model="showSchedule"
                            x-on:change="if(showSchedule && status !== 'scheduled') status = 'scheduled'; $dispatch('input', status)"
                            class="form-checkbox mr-2">
                        <label for="schedule_post"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Schedule this post') }}</label>
                    </div>
                    <div x-show="showSchedule" class="mt-2">
                        <x-inputs.datetime-picker id="published_at" name="published_at" :label="__('Publish Date')"
                            :value="old(
                                'published_at',
                                isset($post) && $post->published_at
                                    ? $post->published_at->format('Y-m-d H:i')
                                    : now()->addDay()->format('Y-m-d H:i'),
                            )"
                            :min-date="now()->format('Y-m-d')"
                            :help-text="__('Schedule when this post should be published')"
                        />
                    </div>
                </div> --}}
                {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_PUBLISH_DATE, '') !!}
                <div class="mt-4">
                    <x-buttons.submit-buttons 
                        :submitLabel="isset($post) && $post->exists ? __('Update Post') : __('Create Post')"
                        cancelUrl="{{ route('admin.posts.index', $postType) }}" />
                </div>
                {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_SUBMIT_BUTTONS, '') !!}
            </div>
        </div>

        @if ($postTypeModel->hierarchical)
            <!-- Parent -->
            <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-4 py-3 sm:px-6 sm:py-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-medium text-gray-700 dark:text-white">{{ __('Parent') }}</h3>
                </div>
                <div class="p-3 space-y-2 sm:p-4">
                    @php
                        $parentOptions = [['value' => '', 'label' => __('None')]];
                        foreach ($parentPosts as $id => $title) {
                            $parentOptions[] = [
                                'value' => $id,
                                'label' => $title,
                            ];
                        }
                    @endphp

                    <x-inputs.combobox name="parent_id" :label="__('Parent ' . $postTypeModel->label_singular)" :placeholder="__('Select Parent')" :options="$parentOptions"
                        :selected="old('parent_id', $post->parent_id ?? '')" :searchable="true" />
                </div>
            </div>
        @endif
        {!! Hook::applyFilters(PostFilterHook::POST_FORM_AFTER_CONTENT_PARENT, '') !!}

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
