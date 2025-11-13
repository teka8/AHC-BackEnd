<form
    action="{{ $term ? route('admin.terms.update', [$taxonomy, $term->id]) : route('admin.terms.store', $taxonomy) }}"
    method="POST"
    enctype="multipart/form-data"
    data-prevent-unsaved-changes
>
    @method($term ? 'PUT' : 'POST')
    @csrf

    <x-card>
        <x-slot name="header">
            {{ $term ? __("Edit {$taxonomyModel->label_singular}") : __("Add New {$taxonomyModel->label_singular}") }}
        </x-slot>
        <div x-data="slugGenerator('{{ old('name', $term ? $term->name : '') }}', '{{ old('slug', $term ? $term->slug : '') }}')">
            <input type="hidden" name="context_post_type" value="{{ $postType ?? 'announcement' }}">
            <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Name') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required x-model="title" class="form-control">
            </div>

            <div class="mt-2 space-y-1">
                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Slug') }}
                    <button type="button" @click="toggleSlugEdit"
                        class="ml-2 text-xs text-gray-500 dark:text-gray-300 hover:text-brand-500 dark:hover:text-brand-400">
                        <span x-show="!showSlugEdit">{{ __('Edit') }}</span>
                        <span x-show="showSlugEdit">{{ __('Hide') }}</span>
                    </button>
                </label>
                <div class="relative">
                    <input type="text" name="slug" id="slug" x-model="slug" x-bind:readonly="!showSlugEdit"
                        class="form-control" placeholder="{{ __('Leave empty to auto-generate') }}"
                        x-bind:class="{ 'bg-gray-50 dark:bg-gray-800': !showSlugEdit }">
                    <button type="button" @click="generateSlug" x-show="showSlugEdit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        {{ __('Generate') }}
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-2 space-y-1">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="3" class="form-control !h-30">{{ old('description', $term ? $term->description : '') }}</textarea>
            </div>

            <div class="flex">
                @if ($taxonomyModel->show_featured_image)
                    <div class="mt-2">
                        <x-media-selector
                            name="featured_image"
                            label="{{ __('Featured Image') }}"
                            :multiple="false"
                            allowedTypes="images"
                            :existingMedia="($term && $term->hasFeaturedImage()) ? $term->getFeaturedImageUrl() : null"
                            :existingAltText="$term ? $term->featured_image_alt_text : ''"
                            removeCheckboxName="remove_featured_image"
                            removeCheckboxLabel="{{ __('Remove featured image') }}"
                            :showPreview="true"
                        />
                    </div>
                @endif
            </div>

            @php
                $postTypeOptions = ($availablePostTypes ?? collect())
                    ->map(function ($type, $key) {
                        $label = $type->label ?? \Illuminate\Support\Str::title($key);

                        return [
                            'key' => $key,
                            'label' => $label,
                        ];
                    })
                    ->values();
                $selectedPostTypes = collect(old('post_types', $term?->post_types ?? (($postType ?? null) ? [strtolower($postType)] : ['announcement'])))
                    ->map(fn ($value) => strtolower($value))
                    ->unique()
                    ->all();
            @endphp

            @if($postTypeOptions->isNotEmpty())
                <div class="mt-4 space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Applies to post types') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($postTypeOptions as $typeOption)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                <input
                                    type="checkbox"
                                    name="post_types[]"
                                    value="{{ $typeOption['key'] }}"
                                    class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800"
                                    {{ in_array(strtolower($typeOption['key']), $selectedPostTypes, true) ? 'checked' : '' }}
                                >
                                <span>{{ $typeOption['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('post_types')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if ($taxonomyModel->hierarchical)
                <div class="mt-2">
                    <x-posts.term-selector name="parent_id" :taxonomyModel="$taxonomyModel" :term="$term" :parentTerms="$parentTerms"
                        :placeholder="__('Select Parent ' . $taxonomyModel->label_singular)" :label='__("Parent {$taxonomyModel->label_singular}")' searchable="false" />
                </div>
            @endif
            <x-slot name="footer">
                <x-buttons.submit-buttons cancelUrl="{{ $term ? route('admin.terms.index', $taxonomy) : '' }}" />
            </x-slot>
        </div>
    </x-card>

</form>