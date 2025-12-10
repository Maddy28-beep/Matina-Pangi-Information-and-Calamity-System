@extends('layouts.app')

@section('title', 'Add Extended Family')

@section('content')<div class="ds-page">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-people-fill"></i> Add Extended Family (Co-Head)</h2>
            <p class="text-muted">Add an extended family group with a co-head to the household</p>
        </div>
    </div>

    @if(isset($household))
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Selected Household</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Household ID:</strong> {{ $household->household_id }}</p>
                    <p><strong>Address:</strong> {{ $household->address }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Primary Head:</strong> {{ $household->officialHead ? $household->officialHead->full_name : 'N/A' }}</p>
                    <p><strong>Current Members:</strong> {{ $household->total_members }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Extended Family Information</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sub-families.store') }}" method="POST">
                @csrf
                
                @if(!isset($household))
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">Select Household <span class="text-danger">*</span></label>
                        <select name="household_id" class="form-select" required>
                            <option value="">-- Select Household --</option>
                            @foreach($households as $h)
                                <option value="{{ $h['id'] }}" {{ old('household_id') == $h['id'] ? 'selected' : '' }}>
                                    {{ $h['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <input type="hidden" name="household_id" value="{{ $household->id }}">
                @endif

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Extended Family Name <span class="text-danger">*</span></label>
                        <input type="text" name="sub_family_name" class="form-control" placeholder="e.g., Reyes Extended Family" required value="{{ old('sub_family_name') }}">
                        <small class="text-muted">A descriptive name for this extended family group</small>
                    </div>

                    <div class="col-12"><hr><h6 class="text-primary">Co-Head Information</h6></div>

                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="head[first_name]" class="form-control" required value="{{ old('head.first_name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="head[middle_name]" class="form-control" value="{{ old('head.middle_name') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="head[last_name]" class="form-control" required value="{{ old('head.last_name') }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="head[suffix]" class="form-control" placeholder="Jr." value="{{ old('head.suffix') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                        <input type="date" name="head[birthdate]" id="birthdate" class="form-control" required value="{{ old('head.birthdate') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sex <span class="text-danger">*</span></label>
                        <select name="head[sex]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="male" {{ old('head.sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('head.sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                        <select name="head[civil_status]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="single" {{ old('head.civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('head.civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('head.civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('head.civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                            <option value="divorced" {{ old('head.civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Relationship to Primary Head <span class="text-danger">*</span></label>
                        <select name="head[household_role]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="spouse" {{ old('head.household_role') == 'spouse' ? 'selected' : '' }}>Spouse</option>
                            <option value="child" {{ old('head.household_role') == 'child' ? 'selected' : '' }}>Child</option>
                            <option value="parent" {{ old('head.household_role') == 'parent' ? 'selected' : '' }}>Parent</option>
                            <option value="sibling" {{ old('head.household_role') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="relative" {{ old('head.household_role') == 'relative' ? 'selected' : '' }}>Relative</option>
                            <option value="other" {{ old('head.household_role') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="head[contact_number]" class="form-control" value="{{ old('head.contact_number') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="head[email]" class="form-control" value="{{ old('head.email') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="head[occupation]" class="form-control" value="{{ old('head.occupation') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Employment Status</label>
                        <select name="head[employment_status]" class="form-select">
                            <option value="">Select</option>
                            <option value="employed" {{ old('head.employment_status') == 'employed' ? 'selected' : '' }}>Employed</option>
                            <option value="unemployed" {{ old('head.employment_status') == 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="self-employed" {{ old('head.employment_status') == 'self-employed' ? 'selected' : '' }}>Self-Employed</option>
                            <option value="student" {{ old('head.employment_status') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="retired" {{ old('head.employment_status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Monthly Income</label>
                        <input type="number" name="head[monthly_income]" class="form-control" step="0.01" min="0" value="{{ old('head.monthly_income') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Add Extended Family
                    </button>
                    @if(isset($household))
                    <a href="{{ route('households.show', $household) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    @else
                    <a href="{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
