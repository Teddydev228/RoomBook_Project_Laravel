@extends('layouts.app')

@section('title', 'Accès non autorisé')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="bi bi-shield-lock display-1 text-danger mb-4"></i>
                <h2>Accès non autorisé</h2>
                <p class="lead text-secondary">
                    Vous n'avez pas les permissions nécessaires pour accéder à cette page.
                </p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-house me-2"></i>Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
