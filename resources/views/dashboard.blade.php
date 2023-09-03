@extends('layouts.app')

@section('content')
    <div class="container">
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
                        <a href="{{ route('view.file', ['file' => basename($file->file_path)]) }}">View original file</a>
                    </td>
                    <td>{{ $file->message }}</td>
                    <td>{{ $file->status }}</td>
                    <td>
                        @if ($file->status == 'completed')
                            <a href="{{ route('download.file', ['file' => basename($file->download_file_path)]) }}">Download</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $files->links() }} <!-- This will render pagination links -->
    </div>
@endsection
