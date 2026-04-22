@extends('layouts.app')

@section('title', 'Modifier le matériel')

@section('content')
@php
$equipmentItem = $equipment ?? [];
@endphp
@include('equipment.create')
@endsection
