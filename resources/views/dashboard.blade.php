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

        <h2>Files Dashboard</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Instance / FileName</th>
                <th>Upload Date</th>
                <th>Message</th>
                <th>Status</th>
                <th>Download</th>
            </tr>
            </thead>
            <tbody>
            @foreach($files as $file)
                <tr>
                    <td>{{ $file->instance_name }}</td>
                    <td>
                        {{ $file->uploaded_at }} <br>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('download.original', ['file' => $file->file_id]) }}">Download original</a>
                        @if($file->status === 'edit_ready')
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('view.file', ['file' => $file->file_id]) }}">Edit file</a>
                        @endif
                    </td>
                    <td>{{ $file->message }}</td>
                    <td>{{ $file->status }}</td>
                    <td>
                        @if ($file->status === 'completed')
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('view.file', ['file' => $file->file_id, 'download'=> true]) }}">Edit file</a>
                        @endif
                        @if ($file->status === 'download_ready')
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('download.file', ['file' => basename($file->download_file_path)]) }}">Download</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $files->links() }} <!-- This will render pagination links -->
    </div>
@endsection
