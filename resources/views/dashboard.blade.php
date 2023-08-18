@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">DeBounce Dashboard</h1>

        {{-- Pending Files --}}
        <h2>Pending Files</h2>
        <ul>
            @foreach($pendingFiles as $file)
                <li>{{ $file->filename }} - {{ $file->created_at }}</li>
            @endforeach
        </ul>

        {{-- Processing Files --}}
        <h2>Processing Files</h2>
        <ul>
            @foreach($processingFiles as $file)
                <li>{{ $file->filename }} - {{ $file->created_at }}</li>
            @endforeach
        </ul>

        {{-- Completed Files --}}
        <h2>Completed Files</h2>
        <ul>
            @foreach($completedFiles as $file)
                <li>{{ $file->filename }} - {{ $file->created_at }} - <a href="{{ $file->download_link }}">Download</a></li>
            @endforeach
        </ul>

        {{-- Error Files --}}
        <h2>Error Logs</h2>
        <ul>
            @foreach($errorFiles as $file)
                <li>{{ $file->filename }} - {{ $file->created_at }} - Error: {{ $file->response }}</li>
            @endforeach
        </ul>

    </div>
@endsection
