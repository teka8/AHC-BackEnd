
@php
use App\Enums\Hooks\EventFilterHook;
@endphp

<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('status'))
                    <span class="badge">{{ ucfirst(request('status')) }}</span>
                @endif
                @if (request('category'))
                    <span class="badge">{{ __('Category: :category', ['category' => request('category')]) }}</span>
                @endif
                @if (request('tag'))
                    <span class="badge">{{ __('Tag: :tag', ['tag' => request('tag')]) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>
  

    {!! Hook::applyFilters(EventFilterHook::EVENTS_AFTER_BREADCRUMBS, '', Event::class) !!}

@livewire('datatable.event-datatable', ['lazy' => true])

    {!! Hook::applyFilters(EventFilterHook::EVENTS_AFTER_TABLE, '', Event::class) !!}
</x-layouts.backend-layout>