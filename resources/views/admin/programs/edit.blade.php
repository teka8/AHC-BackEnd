@extends('backend.layouts.app')

@section('title', __('Edit Program'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('Edit Program')</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.programs.update', $program->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="title">@lang('Title')</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $program->title) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="host">@lang('Host')</label>
                                <input type="text" name="host" id="host" class="form-control" value="{{ old('host', $program->host) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">@lang('Description')</label>
                                <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $program->description) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">@lang('Image')</label>
                                <input type="file" name="image" id="image" class="form-control-file">
                                @if($program->hasImage())
                                    <img src="{{ $program->getImageUrl() }}" alt="{{ $program->title }}" class="img-thumbnail mt-2" width="100">
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="state">@lang('Status')</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">@lang('Select Status')</option>
                                    @foreach(App\Enums\ProgramStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('state', $program->state->value) === $status->value ? 'selected' : '' }}>{{ __($status->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('Update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
