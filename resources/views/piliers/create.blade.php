@extends('layouts.app')

@section('title', 'Créer un Pilier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Créer un Pilier
            </h1>
        </div>
        <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('piliers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select class="form-select" id="owner_id" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libellé</label>
                    <input type="text" class="form-control" id="libelle" name="libelle" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le pilier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 