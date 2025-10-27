<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters(\App\Enums\Hooks\EventFilterHook::EVENTS_SHOW_AFTER_BREADCRUMBS, '', \App\Models\Event::class) !!}

    <div class="space-y-6">
        <div class="rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">

            {{-- Header --}}
            <div
                class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white/90">{{ __('Event Details') }}</h3>
                <div class="flex gap-2">
                    @can('update', $event)
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn-primary flex items-center">
                            <iconify-icon icon="lucide:pencil" class="mr-2"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('admin.events.index') }}" class="btn-default flex items-center">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2"></iconify-icon>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="px-5 py-4 sm:px-6 sm:py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">

                    {{-- Event Date --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:calendar" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Event Date:') }}</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            {{ $event->event_date ? $event->event_date->format('M d, Y') : '—' }}
                        </span>
                    </div>

                    {{-- Event Time --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:clock" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Time:') }}</span>
                        <span class="ml-auto text-gray-700 dark:text-white/90">
                            @if ($event->start_time)
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $event->start_time)->format('h:i A') }}
                                @if ($event->end_time)
                                    — {{ \Carbon\Carbon::createFromFormat('H:i:s', $event->end_time)->format('h:i A') }}
                                @endif
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:tag" class="text-gray-500"></iconify-icon>
                        <span class="font-medium">{{ __('Status:') }}</span>
                        <span
                            class="ml-auto px-2 py-1 text-xs rounded {{ function_exists('get_post_status_class') ? get_post_status_class($event->status) : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white' }}">
                            {{ ucfirst($event->status ?? '—') }}
                        </span>
                    </div>

                    {{-- Registration Link --}}
                    @if($event->register_on_site == 0)
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="lucide:link" class="text-gray-500"></iconify-icon>
                            <span class="font-medium">{{ __('Registration Link:') }}</span>
                            @if ($event->registration_link)
                                <a href="{{ $event->registration_link }}" target="_blank"
                                    class="ml-auto text-primary hover:underline">
                                    {{ __('Register Here') }}
                                </a>
                            @else
                                <span class="ml-auto text-gray-700 dark:text-white/90">—</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Short Info Panel --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Category') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">{{ $event->category ?? '—' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Type') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ ucfirst($event->event_type ?? '—') }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Cost') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ (float) $event->cost_amount !== 0.0 ? number_format($event->cost_amount, 2) . ' ETB' : __('Free') }}
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded shadow-sm">
                        <div class="text-sm text-gray-500">{{ __('Target Audience') }}</div>
                        <div class="font-medium text-gray-800 dark:text-white mt-1">
                            {{ $event->target_audience ?? '—' }}
                        </div>
                    </div>
                </div>

                {{-- Location & Map --}}
                @if($event->event_type == 'in-person')
                    <div class="mt-6 text-sm text-gray-700 dark:text-white/90">
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="lucide:map-pin" class="text-gray-500"></iconify-icon>
                            <span class="font-medium">{{ __('Location:') }}</span>
                            <span class="ml-1">{{ $event->location ?? '—' }}</span>
                        </div>

                        @if ($event->google_map_location_link)
                            <div class="ml-[90px] mt-1">
                                <a href="{{ $event->google_map_location_link }}" target="_blank"
                                    class="text-primary hover:underline">
                                    {{ __('Open in Google Maps') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($event->event_image ?? false)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $event->event_image) }}"  alt="{{ $event->title }}"
                            class="w-full max-h-64 object-contain rounded-md shadow-sm m-10">
                    </div>
                @endif

                {{-- Description --}}
                @if($event->description !== "<p><br></p>")
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('Description') }}</h4>
                        <div class="prose max-w-none dark:prose-invert text-sm">
                            {!! $event->description ?? '<p>—</p>' !!}
                        </div>
                    </div>
                @endif

                {{-- Attachments --}}
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-white/90 mb-2">{{ __('Attachments') }}</h4>
                    @if (!empty($event->attachments))
                        <div class="space-y-2">
                            
                            @foreach ($event->attachments ?? [] as $att)
                                @php
                                    $name = $att['file_name'] ?? 'N/A';
                                    $url = isset($att['path']) ? asset('storage/' . $att['path']) : '#';
                                @endphp
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <iconify-icon icon="lucide:file-text" class="text-gray-500"></iconify-icon>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $name }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ $url }}" target="_blank"
                                        class="text-primary hover:underline text-sm">{{ __('Download') }}</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-700 dark:text-white/90">—</p>
                    @endif
                </div>

                {{-- Created / Updated --}}
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                    <div>{{ __('Created:') }} {{ $event->created_at->format('M d, Y h:i A') }}</div>
                    @if ($event->created_at != $event->updated_at)
                        <div>{{ __('Updated:') }} {{ $event->updated_at->format('M d, Y h:i A') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(\App\Enums\Hooks\EventFilterHook::EVENTS_SHOW_AFTER_CONTENT, '', $event) !!}

    @push('styles')
        <style>
            /* Make images inside the description smaller */
            .prose img {
                max-width: 300px;
                height: auto;
                border-radius: 0.375rem;
            }
        </style>
    @endpush
</x-layouts.backend-layout>
