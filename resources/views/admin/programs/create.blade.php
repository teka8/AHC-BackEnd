@extends('backend.layouts.app')

@section('title', __('Create Program'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('Create Program')</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="title">@lang('Title')</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="host">@lang('Host')</label>
                                <input type="text" name="host" id="host" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="description">@lang('Description')</label>
                                <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">@lang('Image')</label>
                                <input type="file" name="image" id="image" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label for="state">@lang('Status')</label>
                                <select name="state" id="state" class="form-control" required>
                                    @foreach(App\Enums\ProgramStatus::cases() as $status)
                                        <option value="{{ $status->value }}">{{ __($status->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('Create')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
