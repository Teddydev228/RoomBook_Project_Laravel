@extends('layouts.app')

@section('title', isset($room) ? 'Modifier la salle' : 'Créer une salle')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-door-open me-2"></i>
                    {{ isset($room) ? 'Modifier la salle' : 'Nouvelle salle' }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($room) ? route('rooms.update', $room['id']) : route('rooms.store') }}">
                    @csrf
                    @if(isset($room))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               value="{{ old('name', $room['name'] ?? '') }}" 
                               required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bâtiment <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="building" 
                               class="form-control" 
                               value="{{ old('building', $room['building'] ?? '') }}" 
                               required>
                        @error('building')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capacité <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="capacity" 
                               class="form-control" 
                               value="{{ old('capacity', $room['capacity'] ?? '') }}" 
                               min="1" 
                               required>
                        @error('capacity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_available"
                                   name="is_available" 
                                   value="1"
                                   {{ (old('is_available', $room['is_available'] ?? true) ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_available">
                                Salle disponible pour réservation
                            </label>
                        </div>
                        @error('is_available')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            {{ isset($room) ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
