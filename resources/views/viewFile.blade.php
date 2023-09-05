@extends('layouts.app')

@section('content')
    <form action="{{ route('view.file', ['file' => $file]) }}" method="GET">
        <select name="encoding">
            <option value="UTF-8" {{ $encoding === 'UTF-8' ? 'selected' : '' }}>UTF-8</option>
            <option value="ISO-8859-1" {{ $encoding === 'ISO-8859-1' ? 'selected' : '' }}>ISO-8859-1</option>
            <option value="Windows-1252" {{ $encoding === 'Windows-1252' ? 'selected' : '' }}>Windows-1252</option>
            <option value="ISO-8859-15" {{ $encoding === 'ISO-8859-15' ? 'selected' : '' }}>ISO-8859-15</option>
            <option value="UTF-16" {{ $encoding === 'UTF-16' ? 'selected' : '' }}>UTF-16</option>
            <option value="UTF-16BE" {{ $encoding === 'UTF-16BE' ? 'selected' : '' }}>UTF-16BE</option>
            <option value="UTF-16LE" {{ $encoding === 'UTF-16LE' ? 'selected' : '' }}>UTF-16LE</option>
            <option value="UTF-32" {{ $encoding === 'UTF-32' ? 'selected' : '' }}>UTF-32</option>
            <option value="Windows-1250" {{ $encoding === 'Windows-1250' ? 'selected' : '' }}>Windows-1250</option>
            <option value="Windows-1251" {{ $encoding === 'Windows-1251' ? 'selected' : '' }}>Windows-1251</option>
            <option value="Windows-1253" {{ $encoding === 'Windows-1253' ? 'selected' : '' }}>Windows-1253</option>
            <option value="Windows-1254" {{ $encoding === 'Windows-1254' ? 'selected' : '' }}>Windows-1254</option>
            <option value="Windows-1255" {{ $encoding === 'Windows-1255' ? 'selected' : '' }}>Windows-1255</option>
            <option value="Windows-1256" {{ $encoding === 'Windows-1256' ? 'selected' : '' }}>Windows-1256</option>
            <option value="Windows-1257" {{ $encoding === 'Windows-1257' ? 'selected' : '' }}>Windows-1257</option>
            <option value="Windows-1258" {{ $encoding === 'Windows-1258' ? 'selected' : '' }}>Windows-1258</option>
            <option value="ISO-2022-JP" {{ $encoding === 'ISO-2022-JP' ? 'selected' : '' }}>ISO-2022-JP</option>
            <option value="EUC-JP" {{ $encoding === 'EUC-JP' ? 'selected' : '' }}>EUC-JP</option>
            <option value="Shift_JIS" {{ $encoding === 'Shift_JIS' ? 'selected' : '' }}>Shift_JIS</option>
            <option value="ISO-2022-KR" {{ $encoding === 'ISO-2022-KR' ? 'selected' : '' }}>ISO-2022-KR</option>
            <option value="EUC-KR" {{ $encoding === 'EUC-KR' ? 'selected' : '' }}>EUC-KR</option>
            <option value="GBK" {{ $encoding === 'GBK' ? 'selected' : '' }}>GBK</option>
            <option value="GB2312" {{ $encoding === 'GB2312' ? 'selected' : '' }}>GB2312</option>
            <option value="ISO-2022-CN" {{ $encoding === 'ISO-2022-CN' ? 'selected' : '' }}>ISO-2022-CN</option>
            <option value="Big5" {{ $encoding === 'Big5' ? 'selected' : '' }}>Big5</option>
            <!-- Even more options if needed -->
        </select>
        <input type="text" name="separator" value="{{ session('separator', '') }}">
        <button type="submit">Change Encoding</button>
    </form>

    <form action="{{ route('save.file', ['file' => $file, 'encoding' => $encoding]) }}" method="POST">
        @csrf  <!-- CSRF token for security -->
        <input type="text" name="separator" disabled value="{{ session('separator', '') }}">
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
