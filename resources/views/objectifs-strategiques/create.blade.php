@extends('layouts.app')

@section('title', 'Créer un Objectif Stratégique')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Créer un Objectif Stratégique
            </h1>
        </div>
        <a href="{{ route('objectifs-strategiques.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('objectifs-strategiques.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code') }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pilier_id" class="form-label">Pilier <span class="text-danger">*</span></label>
                        <select class="form-select @error('pilier_id') is-invalid @enderror" id="pilier_id" name="pilier_id" required>
                            <option value="">Sélectionner un pilier</option>
                            @foreach($piliers as $pilier)
                                <option value="{{ $pilier->id }}" {{ old('pilier_id') == $pilier->id ? 'selected' : '' }}>
                                    {{ $pilier->code }} - {{ $pilier->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('pilier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select class="form-select @error('owner_id') is-invalid @enderror" id="owner_id" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
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
                           id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('objectifs-strategiques.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Créer l'objectif stratégique
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 