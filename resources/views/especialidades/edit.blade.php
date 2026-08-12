@extends('layouts.app')

@section('title', 'Editar Especialidad')
@section('page-title', 'Editar Especialidad: ' . $especialidad->nombre)
@section('breadcrumb', 'Especialidades / Editar')

@section('content')
<div class="card" style="max-width: 600px;">
    <form action="{{ route('especialidades.update', $especialidad->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nombre de la Especialidad *</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $especialidad->nombre) }}" required autofocus>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $especialidad->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="activo" class="form-control">
                <option value="1" {{ old('activo', $especialidad->activo) == 1 ? 'selected' : '' }}>Activa (Disponible)</option>
                <option value="0" {{ old('activo', $especialidad->activo) == 0 ? 'selected' : '' }}>Inactiva (Oculta)</option>
            </select>
            @error('activo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-2" style="justify-content: flex-end; margin-top: 1rem;">
            <a href="{{ route('especialidades.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Especialidad</button>
        </div>
    </form>
</div>
@endsection
