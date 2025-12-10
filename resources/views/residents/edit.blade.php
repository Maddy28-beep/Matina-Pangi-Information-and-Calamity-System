@extends('layouts.app')

@section('content')<div class="ds-page">
    <h2 class="mb-4">Edit Resident</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('residents.update', $resident) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Household <span class="text-danger">*</span></label>
                        <select name="household_id" class="form-select @error('household_id') is-invalid @enderror" required>
                            <option value="">Select Household</option>
                            @foreach($households as $household)
                                <option value="{{ $household->id }}" {{ $resident->household_id == $household->id ? 'selected' : '' }}>
                                    {{ $household->household_id }} - {{ $household->head->full_name ?? 'No Head' }}
                                </option>
                            @endforeach
                        </select>
                        @error('household_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $resident->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $resident->middle_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $resident->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $resident->suffix) }}" placeholder="Jr, Sr, III">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                        <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate', $resident->birthdate?->format('Y-m-d')) }}" required>
                        @error('birthdate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sex <span class="text-danger">*</span></label>
                        <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="male" {{ $resident->sex == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $resident->sex == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                        <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="single" {{ $resident->civil_status == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ $resident->civil_status == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ $resident->civil_status == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ $resident->civil_status == 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @error('civil_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Select</option>
                            <option value="active" {{ $resident->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="reallocated" {{ $resident->status == 'reallocated' ? 'selected' : '' }}>Reallocated</option>
                            <option value="deceased" {{ $resident->status == 'deceased' ? 'selected' : '' }}>Deceased</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $resident->contact_number) }}" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $resident->email) }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Resident
                    </button>
                    <a href="{{ route('residents.show', $resident) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection