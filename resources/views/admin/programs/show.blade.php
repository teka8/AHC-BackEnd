@extends('backend.layouts.app')

@section('title', __('Show Program'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('Program Details')</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">@lang('Title')</label>
                            <p>{{ $program->title }}</p>
                        </div>
                        <div class="form-group">
                            <label for="host">@lang('Host')</label>
                            <p>{{ $program->host }}</p>
                        </div>
                        <div class="form-group">
                            <label for="description">@lang('Description')</label>
                            <p>{{ $program->description }}</p>
                        </div>
                        <div class="form-group">
                            <label for="image">@lang('Image')</label>
                            @if($program->hasImage())
                                <img src="{{ $program->getImageUrl() }}" alt="{{ $program->title }}" class="img-thumbnail mt-2" width="200">
                            @else
                                <p>@lang('No image available')</p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="state">@lang('Status')</label>
                            <p>{{ __($program->state->name) }}</p>
                        </div>
                        <a href="{{ route('admin.programs.edit', $program->id) }}" class="btn btn-primary">@lang('Edit')</a>
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">@lang('Back to List')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
