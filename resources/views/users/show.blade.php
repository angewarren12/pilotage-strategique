@extends('layouts.app')

@section('title', 'Détails Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user me-2"></i>
                Détails de l'Utilisateur
            </h1>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informations</h5>
                    <p><strong>Nom:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Rôle:</strong> {{ $user->role ? $user->role->libelle : 'Aucun rôle' }}</p>
                    <p><strong>Date de création:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 