@extends('layouts.app')

@section('content')
    <form action="{{ route('view.file', ['file' => $file]) }}" method="GET">
        <select name="encoding">
            <option value="UTF-8" {{ $encoding === 'UTF-8' ? 'selected' : '' }}>UTF-8</option>
            <option value="ISO-8859-1" {{ $encoding === 'ISO-8859-1' ? 'selected' : '' }}>ISO-8859-1</option>
            <option value="ISO-8859-2" {{ $encoding === 'ISO-8859-2' ? 'selected' : '' }}>ISO-8859-2</option>
            <option value="Windows-1250" {{ $encoding === 'Windows-1250' ? 'selected' : '' }}>Windows-1250</option>
            <option value="Windows-1252" {{ $encoding === 'Windows-1252' ? 'selected' : '' }}>Windows-1252</option>

            <option value="Windows-1251" {{ $encoding === 'Windows-1251' ? 'selected' : '' }}>Windows-1251</option>
            <option value="ISO-8859-15" {{ $encoding === 'ISO-8859-15' ? 'selected' : '' }}>ISO-8859-15</option>
            <option value="UTF-16" {{ $encoding === 'UTF-16' ? 'selected' : '' }}>UTF-16</option>
            <option value="UTF-32" {{ $encoding === 'UTF-32' ? 'selected' : '' }}>UTF-32</option>
            <!-- Even more options if needed -->
        </select>
        <input type="text" name="separator" value="{{ session('separator', '') }}">
        <button type="submit">Change Encoding</button>
    </form>

    <form action="{{ route('save.file', ['file' => $file, 'encoding' => $encoding]) }}" method="POST">
        @csrf  <!-- CSRF token for security -->
        <input type="hidden" name="separator" disabled value="{{ session('separator', '') }}">
        <button type="submit">Save File</button>
    </form>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <h2>View File</h2>
        @if(!empty($rows))
            <table class="table">
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>No data found.</p>
        @endif
    </div>
@endsection
