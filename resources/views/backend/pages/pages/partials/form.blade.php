{!! Hook::applyFilters('filter.page.form_start', '') !!}

<input type="hidden" name="page_id" value="{{ $page->id ?? '' }}">

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/20 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900/50 py-8 px-4">    
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            
            <p class="text-gray-600 dark:text-gray-300">{{ __('Fill in the details to') }} {{ isset($page) ? __('update') : __('create') }} {{ __('your static page') }}</p>
        </div>

        <form method="POST" action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($page))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column - Main Form Content -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">                            
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Basic Information') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Essential details about your page') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Title and Section in same row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Page Title') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="title" 
                                        value="{{ old('title', $page->title ?? '') }}" 
                                        placeholder="{{ __('e.g., About Our Company') }}" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                        required
                                    >
                                    @error('title')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Section') }} <span class="text-red-500">*</span></label>
                                    <select 
                                        name="section" 
                                        id="section" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('section') border-red-500 @enderror"
                                    >
                                        <option value="">— {{ __('Select') }} —</option>
                                        <option value="about" {{ old('section', $page->section ?? '') === 'about' ? 'selected' : '' }}>{{ __('About Us') }}</option>
                                        <option value="terms" {{ old('section', $page->section ?? '') === 'terms' ? 'selected' : '' }}>{{ __('Terms & Conditions') }}</option>
                                        <option value="privacy" {{ old('section', $page->section ?? '') === 'privacy' ? 'selected' : '' }}>{{ __('Privacy Policy') }}</option>
                                        <option value="contact" {{ old('section', $page->section ?? '') === 'contact' ? 'selected' : '' }}>{{ __('Contact Information') }}</option>
                                        <option value="faq" {{ old('section', $page->section ?? '') === 'faq' ? 'selected' : '' }}>{{ __('FAQ') }}</option>
                                        <option value="shipping" {{ old('section', $page->section ?? '') === 'shipping' ? 'selected' : '' }}>{{ __('Shipping Policy') }}</option>
                                        <option value="returns" {{ old('section', $page->section ?? '') === 'returns' ? 'selected' : '' }}>{{ __('Return Policy') }}</option>
                                        <option value="custom" {{ !in_array(old('section', $page->section ?? ''), ['about','terms','privacy','contact','faq','shipping','returns','']) ? 'selected' : '' }}>{{ __('Custom') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="custom-section-field" class="space-y-2 hidden">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Custom Section') }}</label>
                                <input 
                                    type="text" 
                                    id="custom_section"
                                    name="custom_section" 
                                    value="{{ !in_array($page->section ?? '', ['about','terms','privacy','contact','faq','shipping','returns','']) ? ($page->section ?? '') : '' }}" 
                                    placeholder="{{ __('Add your custom section') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >
                            </div>

                            <!-- Slug Field -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Slug') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="slug" 
                                    value="{{ old('slug', $page->slug ?? '') }}" 
                                    placeholder="{{ __('e.g., about-our-company') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slug') border-red-500 @enderror"
                                    required
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('URL-friendly version of the title. Use lowercase letters, numbers, and hyphens.') }}
                                </p>
                                @error('slug')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Content') }}</label>
                                <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">
                                    {{ __('Write your page content. Use the toolbar to format text, add images, and create links.') }}
                                </p>
                                <textarea 
                                    name="content" 
                                    id="content" 
                                    rows="12" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >{!! old('content', $page->content ?? '') !!}</textarea>
                            </div>
                        </div>
                    </div>

                    {!! Hook::applyFilters('filter.page.form_after_title', '') !!}

                    <!-- SEO Information -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">                            
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                </svg>
                                {{ __('SEO Optimization') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ __('Improve search engine visibility') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Title') }}</label>
                                <input 
                                    type="text" 
                                    name="meta_title" 
                                    value="{{ old('meta_title', $page->meta_title ?? '') }}" 
                                    placeholder="{{ __('e.g., About Our Company - Comprehensive Overview') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Recommended: 50-60 characters') }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Meta Description') }}</label>
                                <textarea 
                                    name="meta_description" 
                                    rows="3" 
                                    placeholder="Brief description of the page content for search engines..."
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Recommended: 150-160 characters') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Settings and Actions -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Page Status -->
                    @if (!empty($page))
                        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <!-- Header with Current Status -->
                            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</h3>
                                    @php
                                        $statusColors = [
                                            'published' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-800',
                                            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                            'archived' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        ];
                                        $colorClass = $statusColors[$page->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $colorClass }}">
                                        {{ __(ucfirst($page->status)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Actions -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Page Actions') }}</span>
                                    <iconify-icon icon="lucide:file-text" class="text-blue-500"></iconify-icon>
                                </div>

                                <div class="grid grid-cols-1 gap-2">
                                    @if($page->status === 'draft')
                                        <button type="button"
                                                onclick="changePageStatus({{ $page->id }}, 'published', this)"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent transition-all duration-200
                                                    bg-green-100 text-green-800 
                                                    hover:bg-green-200 hover:scale-105
                                                    dark:bg-green-900/20 dark:text-green-300 
                                                    dark:hover:bg-green-900/30
                                                    focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                title="{{ __('Publish Page') }}">
                                            <iconify-icon icon="lucide:eye" class="w-4 h-4 mr-2"></iconify-icon>
                                            {{ __('Publish') }}
                                        </button>
                                    @endif

                                    @if($page->status === 'published')
                                        <button type="button"
                                                onclick="changePageStatus({{ $page->id }}, 'draft', this)"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent transition-all duration-200
                                                    bg-gray-100 text-gray-800 
                                                    hover:bg-gray-200 hover:scale-105
                                                    dark:bg-gray-900/20 dark:text-gray-300 
                                                    dark:hover:bg-gray-900/30
                                                    focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                title="{{ __('Unpublish Page') }}">
                                            <iconify-icon icon="lucide:eye-off" class="w-4 h-4 mr-2"></iconify-icon>
                                            {{ __('Unpublish') }}
                                        </button>
                                    @endif

                                    @if($page->status === 'draft' || $page->status === 'published')
                                    <button type="button"
                                            onclick="changePageStatus({{ $page->id }}, 'archived', this)"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent transition-all duration-200
                                                bg-orange-100 text-orange-800 
                                                hover:bg-orange-200 hover:scale-105
                                                dark:bg-orange-900/20 dark:text-orange-300 
                                                dark:hover:bg-orange-900/30
                                                focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                            title="{{ __('Archive Page') }}">
                                        <iconify-icon icon="lucide:archive" class="w-4 h-4 mr-2"></iconify-icon>
                                        {{ __('Archive') }}
                                    </button>
                                    @endif

                                    @if($page->status === 'archived')
                                        <button type="button"
                                                onclick="changePageStatus({{ $page->id }}, 'draft', this)"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent transition-all duration-200
                                                    bg-gray-100 text-gray-800 
                                                    hover:bg-gray-200 hover:scale-105
                                                    dark:bg-gray-900/20 dark:text-gray-300 
                                                    dark:hover:bg-gray-900/30
                                                    focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                title="{{ __('Unpublish Page') }}">
                                            <iconify-icon icon="lucide:archive-restore" class="w-4 h-4 mr-2"></iconify-icon>
                                            {{ __('Unarchive') }}
                                        </button>
                                    @endif
                                </div>

                                <!-- Page Status Information -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <iconify-icon icon="lucide:info" class="w-4 h-4 mr-2 text-blue-500"></iconify-icon>
                                        <span>
                                            @switch($page->status)
                                                @case('draft')
                                                    {{ __('This page is in draft mode and not visible to the public.') }}
                                                    @break
                                                @case('published')
                                                    {{ __('This page is live and visible to the public.') }}
                                                    @break
                                                @case('archived')
                                                    {{ __('This page has been archived and is no longer visible.') }}
                                                    @break
                                                @default
                                                    {{ __('Current status: ') . ucfirst($page->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>

                                <!-- Last Updated Information -->
                                @if($page->updated_at)
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    <iconify-icon icon="lucide:calendar" class="w-4 h-4 mr-2 text-green-500"></iconify-icon>
                                    <span>
                                        {{ __('Last updated:') . $page->updated_at->format('M d, Y g:i A') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Page Settings -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Page Settings') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1">{{ __('Configure page behavior') }}</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Custom Section Toggle -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                                <div class="space-y-0.5">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Custom Section') }}</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Use custom section name') }}</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    {{-- Hidden input for unchecked state --}}
                                    <input type="hidden" name="is_custom_section" value="0">
                                    <input 
                                        type="checkbox" 
                                        name="is_custom_section" 
                                        id="is_custom_section" 
                                        value="1" 
                                        class="sr-only" 
                                        {{ old('is_custom_section', $page->is_custom_section ?? false) ? 'checked' : '' }}
                                    >
                                    <label 
                                        for="is_custom_section" 
                                        class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300 dark:bg-gray-700"
                                    ></label>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                                </div>
                            </div>

                            <!-- Show in Navigation -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                                <div class="space-y-0.5">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Show in Navigation') }}</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Display in site navigation menus') }}</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    {{-- Hidden input for unchecked state --}}
                                    <input type="hidden" name="show_in_nav" value="0">
                                    <input 
                                        type="checkbox" 
                                        name="show_in_nav" 
                                        id="show_in_nav" 
                                        value="1" 
                                        class="sr-only" 
                                        {{ old('show_in_nav', $page->show_in_nav ?? true) ? 'checked' : '' }}
                                    >
                                    <label 
                                        for="show_in_nav" 
                                        class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300 dark:bg-gray-700"
                                    ></label>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                                </div>
                            </div>

                            <!-- Footer Visibility -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-gray-800">
                                <div class="space-y-0.5">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __('Show in Footer') }}</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Display in footer links') }}</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    {{-- Hidden input for unchecked state --}}
                                    <input type="hidden" name="show_in_footer" value="0">
                                    <input 
                                        type="checkbox" 
                                        name="show_in_footer" 
                                        id="show_in_footer" 
                                        value="1" 
                                        class="sr-only" 
                                        {{ old('show_in_footer', $page->show_in_footer ?? false) ? 'checked' : '' }}
                                    >
                                    <label 
                                        for="show_in_footer" 
                                        class="block w-12 h-6 rounded-full cursor-pointer transition-colors duration-200 ease-in-out bg-gray-300 dark:bg-gray-700"
                                    ></label>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create Page Card -->
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Finalize Page') }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-1">{{ __('Complete your page setup') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="text-center">
                                    <p class="text-gray-600 mb-4 dark:text-gray-400">{{ __('Review all information and submit when ready') }}</p>
                                    
                                    <div class="flex justify-center gap-3">
                                        <a 
                                            href="{{ route('admin.pages.index') }}" 
                                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors dark:text-gray-400"
                                        >
                                           {{ __('Cancel') }}
                                        </a>
                                        <button 
                                            type="submit" 
                                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md"
                                        >
                                            {{ isset($page) ? __('Update Page') : __('Create Page') }}
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200 dark:bg-gray-800 dark:border-gray-700">
                                    <div class="flex items-start gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-xs text-blue-700 dark:text-blue-400">
                                            {{ __('Make sure all required fields are filled and information is accurate before submitting.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{!! Hook::applyFilters('filter.page.form_end', '') !!}

<script>
    // Toggle custom section field
    document.addEventListener('DOMContentLoaded', function() {
        const sectionSelect = document.getElementById('section');
        const customSectionField = document.getElementById('custom-section-field');
        const customSectionInput = document.getElementById('custom_section');
        const isCustomSectionCheckbox = document.getElementById('is_custom_section');
        
        function toggleCustomSection() {
            if (!sectionSelect || !customSectionField) return;
            
            const isCustom = sectionSelect.value === 'custom' || (isCustomSectionCheckbox && isCustomSectionCheckbox.checked);
            
            if (isCustom) {
                customSectionField.classList.remove('hidden');
                // Only force select value if checkbox is checked
                if (isCustomSectionCheckbox && isCustomSectionCheckbox.checked && sectionSelect.value !== 'custom') {
                    sectionSelect.value = 'custom';
                }
            } else {
                customSectionField.classList.add('hidden');
                // Don't clear custom section input when switching away
            }
        }
        
        function handleCustomSectionToggle() {
            if (isCustomSectionCheckbox.checked) {
                sectionSelect.value = 'custom';
            } else {
                // When unchecking custom section, set to a default value if current is custom
                if (sectionSelect.value === 'custom') {
                    sectionSelect.value = 'about'; // or whatever default you prefer
                }
            }
            toggleCustomSection();
        }
        
        function handleSectionChange() {
            // When section select changes, update checkbox state
            if (isCustomSectionCheckbox) {
                isCustomSectionCheckbox.checked = sectionSelect.value === 'custom';
            }
            toggleCustomSection();
        }
        
        if (sectionSelect) {
            sectionSelect.addEventListener('change', handleSectionChange);
        }
        
        if (isCustomSectionCheckbox) {
            isCustomSectionCheckbox.addEventListener('change', handleCustomSectionToggle);
        }
        
        // Initial call
        toggleCustomSection();

        // Remove the form submission handling - let the backend handle it
    });

    // Page status change function
    async function changePageStatus(pageId, status, buttonElement) {
        try {
            let confirmMessage = '';
            
            switch (status) {
                case 'published':
                    confirmMessage = '{{ __("Are you sure you want to publish this page? It will become visible to the public.") }}';
                    break;
                case 'draft':
                    confirmMessage = '{{ __("Are you sure you want to change this page to draft? It will no longer be visible to the public.") }}';
                    break;
                case 'archived':
                    confirmMessage = '{{ __("Are you sure you want to archive this page? It will be removed from navigation and public access.") }}';
                    break;
                default:
                    confirmMessage = '{{ __("Are you sure you want to change the page status?") }}';
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading state
            const button = buttonElement;
            const originalText = button.innerHTML;
            button.innerHTML = `
                <iconify-icon icon="lucide:loader-2" class="animate-spin w-4 h-4 mr-2"></iconify-icon>
                {{ __('Processing...') }}
            `;
            button.disabled = true;

            // Use the correct route - match your web.php
            const response = await fetch(`/admin/pages/${pageId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            });

            const data = await response.json();

            if (data.success) {
                if (window.showToast) {
                    window.showToast('success', '{{ __('Success') }}', data.message);
                } else {
                    alert(data.message);
                }
                // Refresh the page to show updated status
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message);
            }

        } catch (error) {
            console.error('Page status change failed:', error);
            if (window.showToast) {
                window.showToast('error', '{{ __('Error') }}', error.message);
            } else {
                alert('Error: ' + error.message);
            }
            // Reset button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }
</script>

<style>
    /* Custom checkbox styling */
    input[type="checkbox"]:checked + label {
        background-color: #3b82f6;
    }
    
    input[type="checkbox"]:checked + label span {
        transform: translateX(1.5rem);
    }
    
    /* Focus styles for better accessibility */
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
</style>