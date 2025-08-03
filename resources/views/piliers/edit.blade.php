@extends('layouts.app')

@section('title', 'Modifier le Pilier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2"></i>
                Modifier le Pilier
            </h1>
            <p class="text-muted">{{ $pilier->code }} - {{ $pilier->libelle }}</p>
        </div>
        <a href="{{ route('piliers.show', $pilier) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('piliers.update', $pilier) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code', $pilier->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select class="form-select @error('owner_id') is-invalid @enderror" id="owner_id" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('owner_id', $pilier->owner_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('owner_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                           id="libelle" name="libelle" value="{{ old('libelle', $pilier->libelle) }}" required>
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $pilier->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('piliers.show', $pilier) }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 