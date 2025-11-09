@extends('backend.layouts.app')

@section('title', __('Programs'))

@section('content')
    <div class="container-fluid">
        @livewire('datatable.program-datatable')
    </div>
@endsection
