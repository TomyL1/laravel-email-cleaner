@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>View File</h2>
        @if(!empty($rows))
            <table class="table">
                <thead>
                <tr>
                    @foreach($rows[0] as $key => $value)
                        <th>Column {{ $key + 1 }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
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
