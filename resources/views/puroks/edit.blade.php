@extends('layouts.app')

@section('content')<div class="ds-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Purok</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('puroks.update', $purok) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- First Row - Basic Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Purok Name <span class="text-danger">*</span></label>
                                <input type="text" name="purok_name" class="form-control @error('purok_name') is-invalid @enderror" 
                                       value="{{ old('purok_name', $purok->purok_name) }}" required>
                                @error('purok_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Purok Code <span class="text-danger">*</span></label>
                                <input type="text" name="purok_code" class="form-control @error('purok_code') is-invalid @enderror" 
                                       value="{{ old('purok_code', $purok->purok_code) }}" required>
                                @error('purok_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Second Row - Leader Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Purok Leader Name</label>
                                <input type="text" name="purok_leader_name" class="form-control @error('purok_leader_name') is-invalid @enderror" 
                                       value="{{ old('purok_leader_name', $purok->purok_leader_name) }}">
                                @error('purok_leader_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Leader Contact Number</label>
                                <input type="text" name="purok_leader_contact" class="form-control @error('purok_leader_contact') is-invalid @enderror" 
                                       value="{{ old('purok_leader_contact', $purok->purok_leader_contact) }}">
                                @error('purok_leader_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Third Row - Description & Boundaries -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4">{{ old('description', $purok->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Boundaries</label>
                                <textarea name="boundaries" class="form-control @error('boundaries') is-invalid @enderror" 
                                          rows="4">{{ old('boundaries', $purok->boundaries) }}</textarea>
                                @error('boundaries')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Purok
                            </button>
                            <a href="{{ route('puroks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            @if($purok->households()->count() == 0)
                            <button type="button" class="btn btn-outline-success ms-auto" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                <i class="bi bi-archive"></i> Archive
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Archive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive this purok? Archived items move to the global archive and can be restored later.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('puroks.destroy', $purok) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-success">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
