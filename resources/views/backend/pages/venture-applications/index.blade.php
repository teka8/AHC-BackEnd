<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('status'))
                    <span class="badge">{{ ucfirst(str_replace('-', ' ', request('status'))) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>

    @livewire('datatable.venture-application-datatable', ['lazy' => true])
</x-layouts.backend-layout>
