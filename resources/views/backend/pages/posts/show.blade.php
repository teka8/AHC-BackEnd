<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(PostFilterHook::POSTS_SHOW_AFTER_BREADCRUMBS, '', $postType) !!}

    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">{{ __('Post Details') }}</h3>
                <div class="flex gap-2">
                    @if (auth()->user()->can('post.edit'))
                        <a href="{{ route('admin.posts.edit', [$postType, $post->id]) }}" class="btn-primary">
                            <iconify-icon icon="lucide:pencil" class="mr-2"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endif
                    <a href="{{ route('admin.posts.index', $postType) }}" class="btn-default">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <!-- Meta Information -->
                <div class="mb-6 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex items-center">
                        <iconify-icon icon="lucide:user" class="mr-1"></iconify-icon>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Author:') }} {{ $post->user->full_name }}</span>
                    </div>
                    <div class="flex items-center">
                        <iconify-icon icon="lucide:calendar" class="mr-1"></iconify-icon>
                        {{ __('Created:') }} {{ $post->created_at->format('M d, Y h:i A') }}
                    </div>
                    @if($post->created_at != $post->updated_at)
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:clock" class="mr-1"></iconify-icon>
                            {{ __('Updated:') }} {{ $post->updated_at->format('M d, Y h:i A') }}
                        </div>
                    @endif
                    <div class="flex items-center">
                        <iconify-icon icon="lucide:tag" class="mr-1"></iconify-icon>
                        {{ __('Status:') }}
                        <span class="ml-1 {{ get_post_status_class($post->status) }}">{{ ucfirst($post->status) }}</span>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $post->title }}</h1>
                    @if($post->slug)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Slug:') }} {{ $post->slug }}</p>
                    @endif
                </div>

                <!-- Content and Featured Image Flex Layout -->
                <div class="mb-6 flex flex-col lg:flex-row gap-6">
                    <!-- Content -->
                    <div class="lg:w-1/2">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-2">{{ __('Content') }}</h4>
                        @if($post->excerpt)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-md text-gray-700 dark:text-gray-300">
                                <strong>{{ __('Excerpt:') }}</strong> {{ $post->excerpt }}
                            </div>
                        @endif
                        <div class="prose max-w-none dark:prose-invert prose-headings:font-medium prose-headings:text-gray-700 dark:prose-headings:text-white/90 prose-p:text-gray-700 dark:prose-p:text-gray-300">
                            {!! $post->content !!}
                        </div>
                    </div>
                    
                    <!-- Featured Image -->
                    @if($post->hasFeaturedImage())
                        <div class="lg:w-1/2">
                            <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-3">{{ __('Featured Image') }}</h4>
                            <img src="{{ $post->getFeaturedImageUrl() }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="window.open('{{ $post->getFeaturedImageUrl() }}', '_blank')">
                        </div>
                    @endif
                </div>

                <!-- Taxonomies -->
                @if($post->terms->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-2">{{ __('Taxonomies') }}</h4>
                        <div class="space-y-3">
                            @php
                                $groupedTerms = $post->terms->groupBy('taxonomy');
                            @endphp

                            @foreach($groupedTerms as $taxonomy => $terms)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ ucfirst($taxonomy) }}</h5>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($terms as $term)
                                            <span class="badge">{{ $term->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(PostFilterHook::POSTS_SHOW_AFTER_CONTENT, '', $postType) !!}
</x-layouts.backend-layout>
