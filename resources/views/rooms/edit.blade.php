@extends('layouts.app')

@section('title', 'Modifier la salle')

@section('content')
@php
$room = $room ?? [];
@endphp
@include('rooms.create')
@endsection
