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
                <!-- Meta Information Cards -->
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-600 to-slate-800 px-6 py-4">
                            <h4 class="text-xl font-bold text-white flex items-center">
                                <iconify-icon icon="lucide:info" class="mr-2"></iconify-icon>
                                {{ __('Post Information') }}
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Author -->
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <h5 class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">{{ __('Author') }}</h5>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $post->user->full_name }}</p>
                                </div>
                                
                                <!-- Created -->
                                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <h5 class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-2">{{ __('Created') }}</h5>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $post->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->format('h:i A') }}</p>
                                </div>
                                
                                <!-- Updated -->
                                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <h5 class="text-sm font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-2">{{ __('Updated') }}</h5>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $post->updated_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $post->updated_at->format('h:i A') }}</p>
                                    @if($post->created_at == $post->updated_at)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">{{ __('(Same as created)') }}</p>
                                    @endif
                                </div>
                                
                                <!-- Status -->
                                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <h5 class="text-sm font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-2">{{ __('Status') }}</h5>
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-bold {{ get_post_status_class($post->status) }}">{{ ucfirst($post->status) }}</span>
                                </div>
                            </div>
                        </div>
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

                <!-- Additional Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Taxonomies -->
                    @if($post->terms->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                                <h4 class="text-xl font-bold text-white flex items-center">
                                    <iconify-icon icon="lucide:tags" class="mr-2"></iconify-icon>
                                    {{ __('Taxonomies') }}
                                </h4>
                            </div>
                            <div class="p-6">
                                @php
                                    $groupedTerms = $post->terms->groupBy('taxonomy');
                                @endphp
                                <div class="space-y-4">
                                    @foreach($groupedTerms as $taxonomy => $terms)
                                        <div>
                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">{{ ucfirst($taxonomy) }}</h5>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($terms as $term)
                                                    <span class="px-3 py-1 bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium border border-blue-300 dark:border-blue-600">{{ $term->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Post Meta Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                            <h4 class="text-xl font-bold text-white flex items-center">
                                <iconify-icon icon="lucide:info" class="mr-2"></iconify-icon>
                                {{ __('Post Information') }}
                            </h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Post ID') }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">#{{ $post->id }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Post Type') }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $postType }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Word Count') }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ str_word_count(strip_tags($post->content)) }} {{ __('words') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Character Count') }}</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ strlen(strip_tags($post->content)) }} {{ __('characters') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(PostFilterHook::POSTS_SHOW_AFTER_CONTENT, '', $postType) !!}
</x-layouts.backend-layout>
