@extends('layouts.app')

@section('content')

    <div class="row">
        <h2>Gutschein PDF hochladen</h2>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::open(['route' => 'barcode.file', 'files' => true]) }}
            <div class="form-group">
                {{ Form::label('pdfFile', 'Gutscheincodes PDF hochladen') }}
                {{ Form::file('pdfFile', ['class' => 'form-control-file']) }}
                <div>{{ $errors->first('pdfFile') }}</div>
            </div>
            {{ Form::submit('Upload', ['class' => 'btn btn-primary my-3']) }}
            {{ Form::close() }}
        </div>
    </div>
@endsection
