@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Bulk Email Validation Results</h1>
        <pre>{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
    </div>
@endsection
