<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('recommendation'))
                    <span class="badge">{{ ucfirst(str_replace('-', ' ', request('recommendation'))) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>

    @livewire('datatable.scholarship-evaluation-datatable', ['lazy' => true])
</x-layouts.backend-layout>
