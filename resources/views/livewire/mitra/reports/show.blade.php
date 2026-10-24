@extends('layouts.mitra')

@section('content')
    @include('partials.report-show', [
        'routePrefix' => 'mitra',
        'reportedLabel' => 'Customer yang Dilaporkan',
    ])
@endsection
