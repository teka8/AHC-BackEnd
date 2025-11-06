<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('focus_area'))
                    <span class="badge">{{ ucfirst(str_replace('-', ' ', request('focus_area'))) }}</span>
                @endif
                @if (request('stage'))
                    <span class="badge">{{ ucfirst(request('stage')) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>

    @livewire('datatable.venture-datatable', ['lazy' => true])
</x-layouts.backend-layout>
