@extends('layouts.app')

@section('saveFile')
    <div class="mb-3">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('save.file', ['file' => $file, 'encoding' => $encoding]) }}" method="POST">
                    @csrf  <!-- CSRF token for security -->
                    <input type="hidden" name="separator" value="{{ session('separator', '') }}">
                    <div class="row">
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">@lang('lngViewFile.saveFile')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section ('changeEncoding')
    <div class="container mb-5">
        <h4>@lang('lngViewFile.changeEncoding')</h4>
        <form action="{{ route('view.file', ['file' => $file]) }}" method="GET">
            <div class="row">
                <div class="col-4">
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
                </div>
            </div>
        </form>
    </div>
@endsection


@section('content')
    @if(!empty($rows))
        @yield('changeEncoding')
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
            <p>@lang('lngViewFile.noData')</p>
        @endif
    </div>
@endsection
