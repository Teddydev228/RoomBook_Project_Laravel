@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-people me-2"></i>Gestion des Utilisateurs
            </h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Ajouter un utilisateur
            </a>
        </div>

        @if(empty($users))
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people display-4 text-secondary mb-3"></i>
                    <h5>Aucun utilisateur enregistré</h5>
                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-person-plus me-2"></i>Ajouter un utilisateur
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $userItem)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    {{ strtoupper(substr($userItem['name'] ?? 'U', 0, 1)) }}
                                                </div>
                                                <strong>{{ $userItem['name'] }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $userItem['email'] }}</td>
                                        <td>
                                            @php
                                                $roleLabels = [
                                                    'admin' => 'Administrateur',
                                                    'responsable' => 'Responsable',
                                                    'enseignant' => 'Enseignant',
                                                ];
                                                $roleClass = [
                                                    'admin' => 'bg-danger',
                                                    'responsable' => 'bg-warning',
                                                    'enseignant' => 'bg-info',
                                                ][$userItem['role']] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $roleClass }} role-badge">
                                                {{ $roleLabels[$userItem['role']] ?? $userItem['role'] }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($userItem['created_at'])->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('users.edit', $userItem['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('users.destroy', $userItem['id']) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
