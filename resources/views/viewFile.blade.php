@extends('layouts.app')

@section ('revertFile')
    <form action="{{ route('revert.file', ['file' => $file]) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12 d-grid">
                <button type="submit" class="btn btn-outline-primary">@lang('lngViewFile.revertFile')</button>
            </div>
        </div>
    </form>
@endsection

@section ('submitToProcess')
    <form action="{{ route('submitToProcess.file', ['file' => $file]) }}" method="POST">
        @csrf
        <div class="row mt-1">
            <div class="col-12 d-grid">
                <button type="submit" class="btn btn-success">@lang('lngViewFile.submitToProcess')</button>
            </div>
        </div>
@endsection

@section ('changeEncoding')
    <h4>@lang('lngViewFile.changeEncoding')</h4>
    <form action="{{ route('view.file', ['file' => $file]) }}" method="GET">
        <div class="row">
            <div class="col-12 mb-1">
                <div class="input-group">
                    <span class="input-group-text">@lang('lngViewFile.separator')</span>
                    <input class="form-control" type="text" name="separator" value="{{ session('separator', '') }}">
                    <span class="input-group-text">@lang('lngViewFile.encoding')</span>
                    <select class="form-select" name="encoding">
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
                </div>
            </div>
            <div class="col-12 d-grid">
                <button type="submit" class="btn btn-outline-primary">@lang('lngViewFile.changeEncodingBtn')</button>
            </div>
        </div>
    </form>
@endsection


@section('content')
    @if(!empty($rows))
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-12">
                    @yield('changeEncoding')
                </div>
                <div class="col-md-3 offset-md-5 col-12">
                    @yield('revertFile')
                    @yield('submitToProcess')
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-12">
                <h2>@lang('lngViewFile.viewFile')</h2>
            </div>
            <div class="col-md-4 col-12 d-grid ">
                @if(!empty($rows))
                    @yield('saveFile')
                @endif
            </div>
        </div>


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

        @if(!empty($rows))
            <form action="{{ route('save.file', ['file' => $file, 'encoding' => $encoding]) }}" method="POST">
                <div class="card">

                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8 col-12">
                                <h5>Select columns to save</h5>
                            </div>
                            <div class="col-md-4 col-12 d-grid justify-content-end">
                                <button type="submit" class="btn btn-primary">@lang('lngViewFile.save')</button>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="deleteFirst" value="" id="deleteFirst">
                                    <label class="form-check-label" for="deleteFirst">@lang('lngViewFile.deleteFirstRow')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @csrf

                    <div class="card-content">
                        <input type="hidden" name="separator" value="{{ session('separator', ',') }}">
                    <table class="table">
                        <tbody>
                        <tr>
                            @foreach($rows[0] as $index => $header)
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $index }}" id="delCol_{{ $index }}">
                                        <label for="delCol_{{ $index }}"></label>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                        @foreach($rows as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 d-grid justify-content-end">
                                <button type="submit" class="btn btn-primary">@lang('lngViewFile.save')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <p>@lang('lngViewFile.noData')</p>
        @endif

    </div>
@endsection
