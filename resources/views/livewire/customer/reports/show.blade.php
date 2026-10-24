@extends('layouts.app')

@section('content')
    @include('partials.report-show', [
        'routePrefix' => 'customer',
        'reportedLabel' => 'Mitra yang Dilaporkan',
    ])
@endsection
