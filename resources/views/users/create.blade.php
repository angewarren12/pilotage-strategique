@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus me-2"></i>
                Créer un Utilisateur
            </h1>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">Rôle</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 