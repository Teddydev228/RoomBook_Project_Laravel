@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
@php
$editUser = $editUser ?? [];
@endphp
@include('users.create')
@endsection
