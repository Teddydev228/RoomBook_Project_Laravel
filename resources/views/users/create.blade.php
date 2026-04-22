@extends('layouts.app')

@section('title', isset($editUser) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-person me-2"></i>
                    {{ isset($editUser) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($editUser) ? route('users.update', $editUser['id']) : route('users.store') }}">
                    @csrf
                    @if(isset($editUser))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               value="{{ old('name', $editUser['name'] ?? '') }}" 
                               required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               value="{{ old('email', $editUser['email'] ?? '') }}" 
                               required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Mot de passe 
                            @if(!isset($editUser))<span class="text-danger">*</span>@endif
                        </label>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               {{ !isset($editUser) ? 'required' : '' }}>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @if(isset($editUser))
                            <small class="text-secondary">Laisser vide pour ne pas modifier</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="form-control"
                               {{ !isset($editUser) ? 'required' : '' }}>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Choisir un rôle --</option>
                            <option value="enseignant" 
                                    {{ (old('role', $editUser['role'] ?? '') === 'enseignant') ? 'selected' : '' }}>
                                Enseignant
                            </option>
                            <option value="responsable" 
                                    {{ (old('role', $editUser['role'] ?? '') === 'responsable') ? 'selected' : '' }}>
                                Responsable
                            </option>
                        </select>
                        <small class="text-secondary">L'administrateur ne peut pas être créé</small>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            {{ isset($editUser) ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
