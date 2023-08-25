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
            <input type="file" name="file" required>
            <input type="text" name="instance_name" placeholder="Instance Name" required>
            <input type="text" name="message" placeholder="Any important info">
            <button type="submit">Upload</button>
        </form>
    </div>

@endsection
