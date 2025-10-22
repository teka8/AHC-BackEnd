<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(\App\Enums\Hooks\EventFilterHook::EVENTS_SHOW_AFTER_BREADCRUMBS, '', \App\Models\Event::class) !!}

    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">{{ __('Event Details') }}</h3>
                <div class="flex gap-2">
                    @can('update', $event)
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn-primary">
                            <iconify-icon icon="lucide:pencil" class="mr-2"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('admin.events.index') }}" class="btn-default">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            <div class="px-5 py-4 sm:px-6 sm:py-5">
                {{-- Meta --}}
                <div class="mb-6 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300">
                

                    <div class="flex items-center">
                        <iconify-icon icon="lucide:calendar" class="mr-1"></iconify-icon>
                        <span>{{ __('Event Date:') }}</span>
                        <span class="ml-1 text-gray-700 dark:text-white/90">
                            {{ $event->event_date ? $event->event_date->format('M d, Y') : '—' }}
                        </span>
                    </div>

                    <div class="flex items-center">
                        <iconify-icon icon="lucide:clock" class="mr-1"></iconify-icon>
                        <span>{{ __('Time:') }}</span>
                        <span class="ml-1 text-gray-700 dark:text-white/90">
                            @if($event->start_time)
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $event->start_time)->format('h:i A') }}
                                @if($event->end_time)
                                    — {{ \Carbon\Carbon::createFromFormat('H:i:s', $event->end_time)->format('h:i A') }}
                                @endif
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center">
                        <iconify-icon icon="lucide:tag" class="mr-1"></iconify-icon>
                        {{ __('Status:') }}
                        <span class="ml-1 {{ function_exists('get_post_status_class') ? get_post_status_class($event->status) : 'badge' }}">{{ ucfirst($event->status) }}</span>
                    </div>

                    @if($event->location)
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:map-pin" class="mr-1"></iconify-icon>
                            <span class="ml-1 text-gray-700 dark:text-white/90">{{ Str::limit($event->location, 80) }}</span>
                        </div>
                    @endif

                    @if($event->registration_link)
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:link" class="mr-1"></iconify-icon>
                            <a href="{{ $event->registration_link }}" target="_blank" class="text-primary hover:underline ml-1">
                                {{ __('Registration Link') }}
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Featured image (media or url) --}}
                @php
                    $featuredUrl = null;
                    try {
                        if (method_exists($event, 'getFirstMediaUrl')) {
                            $mediaUrl = $event->getFirstMediaUrl('featured');
                            if (!empty($mediaUrl)) {
                                $featuredUrl = $mediaUrl;
                            }
                        }
                    } catch (\Throwable $e) {
                        $featuredUrl = null;
                    }
                    if (!$featuredUrl) {
                        $featuredUrl = $event->image_url;
                    }
                @endphp

                @if($featuredUrl)
                    <div class="mb-6">
                        <img src="{{ $featuredUrl }}" alt="{{ $event->title }}" class="max-h-64 rounded-md w-full object-cover">
                    </div>
                @endif

                {{-- Short info panel --}}
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded">
                        <div class="text-sm text-gray-500">{{ __('Category') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">{{ $event->category ?? '—' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded">
                        <div class="text-sm text-gray-500">{{ __('Type') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">{{ ucfirst($event->event_type ?? '—') }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded">
                        <div class="text-sm text-gray-500">{{ __('Cost') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ $event->cost_amount !== null ? number_format($event->cost_amount, 2) . ' ETB' : __('Free / Not set') }}
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($event->description)
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-2">{{ __('Description') }}</h4>
                        <div class="prose max-w-none dark:prose-invert">
                            {!! $event->description !!}
                        </div>
                    </div>
                @endif

                {{-- Google maps link --}}
                @if($event->google_map_location_link)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-white/90 mb-1">{{ __('Location on Map') }}</h4>
                        <a href="{{ $event->google_map_location_link }}" target="_blank" class="text-primary hover:underline">
                            {{ __('Open in Google Maps') }}
                        </a>
                    </div>
                @endif

                {{-- Attachments (media collection or attachments json) --}}
                @php
                    $attachments = [];
                    if (method_exists($event, 'getMedia')) {
                        try {
                            $attachments = $event->getMedia('attachments') ?: [];
                        } catch (\Throwable $e) {
                            $attachments = [];
                        }
                    }
                @endphp

                @if(!empty($attachments) && count($attachments) > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-2">{{ __('Attachments') }}</h4>
                        <div class="space-y-2">
                            @foreach($attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <iconify-icon icon="lucide:file-text" class="text-gray-500"></iconify-icon>
                                        <div>
                                            <p class="text-sm font-medium">{{ $attachment->name ?? $attachment->file_name }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format(($attachment->size ?? 0) / 1024, 2) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="{{ $attachment->getUrl() }}" target="_blank" class="text-primary hover:underline text-sm">{{ __('Download') }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(!empty($event->attachments) && is_array($event->attachments))
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-white/90 mb-2">{{ __('Attachments') }}</h4>
                        <ul class="list-disc pl-5">
                            @foreach($event->attachments as $att)
                                <li><a href="{{ $att['url'] ?? '#' }}" target="_blank" class="text-primary hover:underline">{{ $att['filename'] ?? ($att['name'] ?? __('Attachment')) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Created / Updated --}}
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">
                    <div>{{ __('Created:') }} {{ $event->created_at->format('M d, Y h:i A') }}</div>
                    @if($event->created_at != $event->updated_at)
                        <div>{{ __('Updated:') }} {{ $event->updated_at->format('M d, Y h:i A') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(\App\Enums\Hooks\EventFilterHook::EVENTS_SHOW_AFTER_CONTENT, '', $event) !!}
</x-layouts.backend-layout>
