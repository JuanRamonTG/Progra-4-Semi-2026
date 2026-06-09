@extends('layouts.public')

@section('title', 'Emergencias - Stellar Traffic')

@section('content')
    <emergencias-public
        csrf-token="{{ csrf_token() }}"
        route-accidentes="{{ url('/accidentes') }}"
    ></emergencias-public>
@endsection
