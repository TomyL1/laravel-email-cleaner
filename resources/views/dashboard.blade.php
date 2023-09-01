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
                <th>View Content</th>
            </tr>
            </thead>
            <tbody>
            @foreach($files as $file)
                <tr>
                    <td>
                        {{ $file->instance_name }}
                    </td>  <!-- You might want to extract just the filename instead of the full path -->
                    <td>{{ $file->uploaded_at }}</td>
                    <td>{{ $file->message }}</td>
                    <td>{{ $file->status }}</td>
                    <td>
                        @if ($file->status == 'completed')
                            <a href="{{ route('download.file', ['file' => basename($file->download_file_path)]) }}">Download</a>
                        @endif
                    </td>
                    <td><a href="{{ route('files.showContent', $file->file_id) }}">View Content</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $files->links() }} <!-- This will render pagination links -->
    </div>
@endsection
