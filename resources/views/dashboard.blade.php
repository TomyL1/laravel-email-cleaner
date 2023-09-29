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
        <div class="mt-3 mb-3">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="enableDelete" value="" id="enableDelete">
                <label class="form-check-label fw-bold" for="enableDelete">@lang('lngDashboard.enableDelete')</label>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Instance / FileName</th>
                <th>Upload Date</th>
                <th>Message</th>
                <th>Status</th>
                <th>Download</th>
                <th class="delete-column" style="display:none;">Delete</th>
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
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('view.file', ['file' => $file->file_id]) }}">Edit file</a>
                        @endif
                        @if ($file->status === 'download_ready')
                            <a class="btn btn-sm btn-primary btn-success" href="{{ route('download.file', ['file' => basename($file->download_file_path)]) }}">Download</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('view.file', ['file' => $file->file_id]) }}">View file</a>
                        @endif
                    </td>
                    <td class="delete-column" style="display:none;">
                        <form action="{{ route('delete.file', ['file' => $file->file_id]) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $files->links() }} <!-- This will render pagination links -->
    </div>

@endsection

@section('end-body-scripts')
    <script>
        var enableDelete = document.querySelector('#enableDelete');
        enableDelete.checked = false;
        var deleteColumn = document.querySelectorAll('.delete-column');

        enableDelete.addEventListener('change', function() {
            console.log('Checkbox state:', this.checked);

            if (this.checked) {
                deleteColumn.forEach(function(element) {
                    element.style.display = 'table-cell';
                });
            } else {
                deleteColumn.forEach(function(element) {
                    element.style.display = 'none';
                });
            }
        });
    </script>
@endsection


