<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(PostFilterHook::POSTS_SHOW_AFTER_BREADCRUMBS, '', $postType) !!}

    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">

            {{-- Header --}}
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white/90">{{ __('News Details') }}</h3>
                <div class="flex gap-2">
                    @can('post.edit')
                        <a href="{{ route('admin.posts.edit', [$postType, $post->id]) }}" class="btn-primary flex items-center">
                            <iconify-icon icon="lucide:pencil" class="mr-2"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('admin.posts.index', $postType) }}" class="btn-default flex items-center">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            {{-- Meta Info --}}
            <div class="px-5 py-4 sm:px-6 sm:py-5 space-y-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
                    {{-- Author --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:user" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Author:') }}</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">{{ $post->user->full_name ?? '—' }}</span>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:tag" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Status:') }}</span>
                        <span class="ml-auto px-2 py-1 text-xs rounded {{ get_post_status_class($post->status) }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </div>

                    {{-- Created --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:calendar" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Created:') }}</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            {{ $post->created_at->format('M d, Y h:i A') }}
                        </span>
                    </div>

                    {{-- Updated --}}
                    @if($post->created_at != $post->updated_at)
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="lucide:clock" class="text-gray-500"></iconify-icon>
                            <span class="font-medium">{{ __('Updated:') }}</span>
                            <span class="ml-auto text-gray-700 dark:text-white/90">
                                {{ $post->updated_at->format('M d, Y h:i A') }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Taxonomies --}}
                @if($post->terms->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('Taxonomies') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php $groupedTerms = $post->terms->groupBy('taxonomy'); @endphp
                            @foreach($groupedTerms as $taxonomy => $terms)
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                                    <div class="text-sm text-gray-500">{{ ucfirst($taxonomy) }}</div>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @foreach($terms as $term)
                                            <span class="badge">{{ $term->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Title & Slug --}}
                <div class="mt-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $post->title }}</h1>
                    @if($post->slug)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Slug:') }} {{ $post->slug }}</p>
                    @endif
                </div>
                {{-- Featured Image + Content (Side by Side, Enhanced) --}}
                @if($post->hasFeaturedImage() || $post->content)
                    <div class="mt-8 flex flex-col lg:flex-row gap-8 items-start">

                        {{-- Content --}}
                        @if($post->content)
                            <div class="w-full lg:w-1/2">
                                <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-3">
                                    {{ __('Content') }}
                                </h4>
                                <div class="prose max-w-none dark:prose-invert text-base leading-relaxed text-gray-800 dark:text-gray-200">
                                    {!! $post->content !!}
                                </div>
                            </div>
                        @endif

                        {{-- Featured Image --}}
                        @if($post->hasFeaturedImage())
                            <div class="w-full lg:w-1/2">
                                <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-3">
                                    {{ __('Featured Image') }}
                                </h4>
                                <img src="{{ $post->getFeaturedImageUrl() }}" 
                                    alt="{{ $post->title }}" 
                                    class="w-full h-auto max-h-[500px] object-contain rounded-md border border-gray-300 dark:border-gray-700 shadow-sm hover:shadow-md transition cursor-pointer"
                                    onclick="window.open('{{ $post->getFeaturedImageUrl() }}', '_blank')">
                            </div>
                        @endif
                    </div>
                @endif
                {{-- Excerpt --}}
                @if($post->excerpt)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-md">
                        <div class="text-sm text-gray-500 mb-1">{{ __('Excerpt') }}</div>
                        <div class="text-gray-700 dark:text-white/90">{{ $post->excerpt }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(PostFilterHook::POSTS_SHOW_AFTER_CONTENT, '', $postType) !!}

    @push('styles')
        <style>
            .prose img {
                max-width: 300px;
                height: auto;
                border-radius: 0.375rem;
            }
        </style>
    @endpush
</x-layouts.backend-layout>
