@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Files Dashboard</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Filename</th>
                <th>Upload Date</th>
                <th>Status</th>
                <!-- Add any other relevant columns here -->
            </tr>
            </thead>
            <tbody>
            @foreach($files as $file)
                <tr>
                    <td>
                        {{ $file->file_path }}
                        @if ($file->status == 'completed')
                            <br />
                            <a href="{{ route('download.file', ['file' => $file->download_file_path]) }}">Download</a>
                        @endif
                    </td>  <!-- You might want to extract just the filename instead of the full path -->
                    <td>{{ $file->uploaded_at }}</td>
                    <td>{{ $file->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $files->links() }} <!-- This will render pagination links -->
    </div>
@endsection
