@extends('layouts.app')

@section('content')

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('upload.file') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12 offset-md-3">
                    <div class="card">
                        <div class="card-header">
                            @lang('lngUpload.uploadFileHeader')
                        </div>
                        <div class="card-body">

                            <div class="card-content">
                                <div class="mb-3">
                                    <input class="form-control" type="file" name="file" required>
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="text" name="instance_name" placeholder="@lang('lngUpload.instanceName')" required>
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="text" name="message" placeholder="@lang('lngUpload.message')">
                                </div>
                                <button type="submit" class="form-control btn btn-success btn-lg">@lang('lngUpload.uploadBtn')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
