@extends('layouts.app')

@section('content')
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                @foreach($content[0] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach(array_slice($content, 1) as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
