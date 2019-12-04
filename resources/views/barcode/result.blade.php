@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-6">
            <p>Anzahl der Gutscheine: {{ count($codes) }}</p>
            <p>Summe: {{ $codes['sum'] }}</p>
        </div>
    </div>
@endsection
