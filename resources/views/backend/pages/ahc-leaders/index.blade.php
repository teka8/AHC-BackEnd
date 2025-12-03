<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    @livewire('datatable.ahc-leader-datatable', ['lazy' => true])
</x-layouts.backend-layout>
