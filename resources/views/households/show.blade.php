@extends('layouts.app')

@section('title', 'Household Details - ' . $household->household_id)

@section('content')<div class="ds-page">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}">Households</a></li>
            <li class="breadcrumb-item active">{{ $household->household_id }}</li>
        </ol>
    </nav>
</div>

<!-- Header with Actions -->
<div class="d-flex justify-content-between align-items-end mb-2">
    <h2><i class="bi bi-house-door-fill"></i> Household <span class="no-wrap">{{ $household->household_id }}</span></h2>
    <div class="btn-group action-buttons" style="max-width: 100%;">
        <a href="{{ route('household-events.by-household', $household) }}" class="btn btn-info">
            <i class="bi bi-calendar-event"></i> View Events
        </a>
        @if(auth()->user()->isSecretary() || auth()->user()->isStaff())
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExtendedFamilyModal">
                <i class="bi bi-people-fill"></i> Add Extended Family
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="bi bi-person-plus-fill"></i> Add Member
            </button>
        @endif
        @if(auth()->user()->isSecretary())
            <a href="{{ route('households.edit', $household) }}" class="btn btn-warning">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal">
                <i class="bi bi-archive-fill"></i> Archive
            </button>
        @endif
        <a href="{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}" class="btn btn-secondary btn-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Household Overview Card -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0">
                    <i class="bi bi-house-fill"></i> 
                    <span class="no-wrap">{{ $household->household_id }}</span>
                    @if($household->officialHead)
                        - {{ $household->officialHead->full_name }}
                        <span class="badge bg-warning text-dark ms-2">
                            <i class="bi bi-star-fill"></i> PRIMARY HEAD
                        </span>
                    @endif
                </h4>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-light text-dark fs-6">
                    <i class="bi bi-people-fill"></i> {{ $statistics['total_residents'] }} Members
                </span>
                <span class="badge bg-light text-dark fs-6 ms-2">
                    <i class="bi bi-diagram-3-fill"></i> {{ $statistics['total_families'] }} Families
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Address Information -->
            <div class="col-md-6">
                <h6 class="text-muted mb-2"><i class="bi bi-geo-alt-fill"></i> Address</h6>
                <p class="mb-0 fw-bold">{{ $household->address }}</p>
                @if($household->purok)
                    <p class="mb-0 text-muted">{{ optional($household->purok)->purok_name ?? $household->purok }}</p>
                @endif
                <p class="mb-0 text-muted">Barangay Matina Pangi, Davao City</p>
            </div>
            
            <!-- Household Heads -->
            <div class="col-md-3">
                <h6 class="text-muted mb-2"><i class="bi bi-person-badge-fill"></i> Household Heads</h6>
                @php
                    $primaryFam = $household->subFamilies->where('is_primary_family', true)->first();
                    $coHeadFams = $household->subFamilies->where('is_primary_family', false);
                @endphp
                @if($primaryFam && $primaryFam->subHead)
                    <p class="mb-1">
                        <span class="text-success fw-bold">⭐ {{ $primaryFam->subHead->full_name }}</span>
                        <br><small class="text-muted">Primary Head</small>
                    </p>
                @endif
                @if($coHeadFams->count() > 0)
                    @foreach($coHeadFams as $coFam)
                        @if($coFam->subHead)
                            <p class="mb-1">
                                <span class="text-primary fw-bold">👤 {{ $coFam->subHead->full_name }}</span>
                                <br><small class="text-muted">Co-Head</small>
                            </p>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Housing Details -->
            <div class="col-md-3">
                <h6 class="text-muted mb-2"><i class="bi bi-house-fill"></i> Housing</h6>
                <p class="mb-1">
                    <strong>Type:</strong> 
                    <span class="badge bg-{{ $household->housing_type === 'owned' ? 'success' : ($household->housing_type === 'rented' ? 'warning' : 'info') }}">
                        {{ ucfirst($household->housing_type) }}
                    </span>
                </p>
                <p class="mb-0">
                    <strong>Electricity:</strong> 
                    @if($household->has_electricity)
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                        @if($household->electric_account_number)
                            <br><small class="text-muted">Acct: {{ $household->electric_account_number }}</small>
                        @endif
                    @else
                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> No</span>
                    @endif
                </p>
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <h6 class="text-muted mb-2"><i class="bi bi-info-circle-fill"></i> Status</h6>
                <p class="mb-1">
                    <span class="badge bg-{{ $household->approval_badge_color }}">
                        {{ ucfirst($household->approval_status) }}
                    </span>
                </p>
                @if($household->approved_at)
                    <small class="text-muted">
                        Approved: {{ $household->approved_at->format('M d, Y') }}
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Household Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card border-0">
            <div class="card-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <h5 class="mb-0" style="color: #1f2937; font-weight: 600;"><i class="bi bi-bar-chart-fill" style="color: #4A6F52;"></i> Household Statistics</h5>
            </div>
            <div class="card-body p-4">
                <div class="row text-center g-3">
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['total_residents'] }}</h3>
                            <small class="text-muted">Total Residents</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['seniors'] }}</h3>
                            <small class="text-muted">Senior Citizens</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['teens'] }}</h3>
                            <small class="text-muted">Teens (13-19)</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['pwd'] }}</h3>
                            <small class="text-muted">PWD</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['voters'] }}</h3>
                            <small class="text-muted">Voters</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="p-3 rounded" style="background-color: #f9fafb; border-left: 4px solid #4A6F52;">
                            <h3 class="mb-0" style="color: #374151;">{{ $statistics['four_ps'] }}</h3>
                            <small class="text-muted">4Ps Beneficiaries</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PRIMARY FAMILY -->
@if($primaryFamily && $primaryMembers->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning bg-opacity-10">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0">
                    <i class="bi bi-house-heart-fill text-warning"></i> 
                    PRIMARY FAMILY
                    @if($household->officialHead)
                        <small style="color: #1f2937; font-weight: 600; opacity: 0.8;">({{ $household->officialHead->full_name }})</small>
                    @endif
                </h4>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge" style="background: white; color: #1f2937; border: 2px solid #1f2937; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $primaryStats['total'] }} Members</span>
                @if($primaryStats['seniors'] > 0)
                    <span class="badge" style="background: white; color: #0ea5e9; border: 2px solid #0ea5e9; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $primaryStats['seniors'] }} Seniors</span>
                @endif
                @if($primaryStats['pwd'] > 0)
                    <span class="badge" style="background: white; color: #dc2626; border: 2px solid #dc2626; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $primaryStats['pwd'] }} PWD</span>
                @endif
                @if($primaryStats['four_ps'] > 0)
                    <span class="badge" style="background: white; color: #4A6F52; border: 2px solid #4A6F52; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $primaryStats['four_ps'] }} 4Ps</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Age/Sex</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Categories</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($primaryMembers as $member)
                    <tr class="table-row-clickable {{ $member->status !== 'active' ? 'table-secondary' : '' }}" 
                        onclick="window.location='{{ route('residents.show', $member) }}'" 
                        style="cursor: pointer;">
                        <td>
                            <strong>{{ $member->full_name }}</strong>
                            @if($member->is_primary_head)
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-star-fill"></i> PRIMARY HEAD
                                </span>
                            @endif
                        </td>
                        <td>{{ $member->age }} / {{ ucfirst($member->sex) }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($member->household_role) }}</span>
                        </td>
                        <td>
                            @if($member->approval_status === 'pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock"></i> Pending Approval
                                </span>
                            @elseif($member->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($member->status === 'reallocated')
                                <span class="badge bg-warning">Reallocated</span>
                            @else
                                <span class="badge bg-dark">Deceased</span>
                            @endif
                        </td>
                        <td>
                            @if($member->is_senior_citizen)
                                <span class="badge bg-info">Senior</span>
                            @endif
                            @if($member->is_teen)
                                <span class="badge bg-warning">Teen</span>
                            @endif
                            @if($member->is_pwd)
                                <span class="badge bg-danger">PWD</span>
                            @endif
                            @if($member->is_voter)
                                <span class="badge bg-success">Voter</span>
                            @endif
                            @if($member->is_4ps_beneficiary)
                                <span class="badge bg-primary">4Ps</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- EXTENDED FAMILIES (CO-HEADS) -->
@if($extendedFamilies->count() > 0)
    @foreach($extendedFamilies as $index => $extendedFamily)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary bg-opacity-10">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">
                        <i class="bi bi-people-fill text-primary"></i> 
                        EXTENDED FAMILY
                        @if($extendedFamily->subHead)
                            <small style="color: #1f2937; font-weight: 600; opacity: 0.8;">({{ $extendedFamily->sub_family_name }})</small>
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                    <i class="bi bi-person-badge-fill"></i> CO-HEAD: {{ $extendedFamily->subHead->full_name }}
                                </span>
                            </div>
                        @endif
                    </h4>
                </div>
                <div class="col-md-4 text-end">
                    @php
                        $familyMembers = $extendedFamily->members;
                        $familyStats = [
                            'total' => $familyMembers->count(),
                            'seniors' => $familyMembers->where('is_senior_citizen', true)->count(),
                            'pwd' => $familyMembers->where('is_pwd', true)->count(),
                            'four_ps' => $familyMembers->where('is_4ps_beneficiary', true)->count(),
                        ];
                    @endphp
                    <span class="badge" style="background: white; color: #1f2937; border: 2px solid #1f2937; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $familyStats['total'] }} Members</span>
                    @if($familyStats['seniors'] > 0)
                        <span class="badge" style="background: white; color: #0ea5e9; border: 2px solid #0ea5e9; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $familyStats['seniors'] }} Seniors</span>
                    @endif
                    @if($familyStats['pwd'] > 0)
                        <span class="badge" style="background: white; color: #dc2626; border: 2px solid #dc2626; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $familyStats['pwd'] }} PWD</span>
                    @endif
                    @if($familyStats['four_ps'] > 0)
                        <span class="badge" style="background: white; color: #4A6F52; border: 2px solid #4A6F52; font-size: 1rem; padding: 0.5rem 1rem; font-weight: 700;">{{ $familyStats['four_ps'] }} 4Ps</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Age/Sex</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Categories</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($familyMembers as $member)
                        <tr class="table-row-clickable {{ $member->status !== 'active' ? 'table-secondary' : '' }}" 
                            onclick="window.location='{{ route('residents.show', $member) }}'" 
                            style="cursor: pointer;">
                            <td>
                                <strong>{{ $member->full_name }}</strong>
                                @if($member->is_co_head)
                                    <span class="badge bg-primary ms-2">
                                        <i class="bi bi-person-badge-fill"></i> CO-HEAD
                                    </span>
                                @endif
                            </td>
                            <td>{{ $member->age }} / {{ ucfirst($member->sex) }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($member->household_role) }}</span>
                            </td>
                            <td>
                                @if($member->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($member->status === 'reallocated')
                                    <span class="badge bg-warning">Reallocated</span>
                                @else
                                    <span class="badge bg-dark">Deceased</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_senior_citizen)
                                    <span class="badge bg-info">Senior</span>
                                @endif
                                @if($member->is_teen)
                                    <span class="badge bg-warning">Teen</span>
                                @endif
                                @if($member->is_pwd)
                                    <span class="badge bg-danger">PWD</span>
                                @endif
                                @if($member->is_voter)
                                    <span class="badge bg-success">Voter</span>
                                @endif
                                @if($member->is_4ps_beneficiary)
                                    <span class="badge bg-primary">4Ps</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@endif

<!-- Archive Modal -->
@if(auth()->user()->isSecretary())
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-archive-fill"></i> Archive Household</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive this household?</p>
                <p class="text-muted">This will archive all members and can be restored later.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('households.archive', $household) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-archive-fill"></i> Archive
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Add Member Modal -->
@if(auth()->user()->isSecretary() || auth()->user()->isStaff())
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('households.store-member', $household) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        <i class="bi bi-person-plus"></i> Add Member to Household {{ $household->household_id }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Important:</strong> Select which head this member belongs to.
                    </div>
                    
                    <!-- Head Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Head <span class="text-danger">*</span></label>
                        <select name="sub_family_id" class="form-select form-select-lg" required>
                            <option value="">-- Select Head --</option>
                            
                            @php
                                $primaryFamily = $household->subFamilies->where('is_primary_family', true)->first();
                                $extendedFamilies = $household->subFamilies->where('is_primary_family', false);
                            @endphp
                            
                            @if($primaryFamily && $primaryFamily->subHead)
                                <option value="{{ $primaryFamily->id }}" class="fw-bold">
                                    ⭐ PRIMARY HEAD: {{ $primaryFamily->subHead->full_name }}
                                </option>
                            @endif
                            
                            @foreach($extendedFamilies as $family)
                                @if($family->subHead)
                                    <option value="{{ $family->id }}">
                                        👤 CO-HEAD: {{ $family->subHead->full_name }} ({{ $family->sub_family_name }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <hr class="my-3">
                    
                    <!-- Personal Information -->
                    <h6 class="text-primary mb-3"><i class="bi bi-person"></i> Personal Information</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Suffix</label>
                            <input type="text" name="suffix" class="form-control" placeholder="Jr., Sr.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                            <input type="date" name="birthdate" id="birthdate_modal" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-select" required>
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select name="household_role" class="form-select" required>
                                <option value="">Select</option>
                                <option value="spouse">Spouse</option>
                                <option value="child">Child</option>
                                <option value="parent">Parent</option>
                                <option value="sibling">Sibling</option>
                                <option value="relative">Relative</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="09XX-XXX-XXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Place of Birth</label>
                            <input type="text" name="place_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Filipino">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-primary"><i class="bi bi-briefcase"></i> Employment & Education</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="">Select</option>
                                <option value="employed">Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="self-employed">Self-Employed</option>
                                <option value="student">Student</option>
                                <option value="retired">Retired</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monthly Income</label>
                            <input type="number" name="monthly_income" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employer Name</label>
                            <input type="text" name="employer_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Educational Attainment</label>
                            <select name="educational_attainment" class="form-select">
                                <option value="">Select</option>
                                <option value="No Formal Education">No Formal Education</option>
                                <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                                <option value="Elementary Graduate">Elementary Graduate</option>
                                <option value="High School Undergraduate">High School Undergraduate</option>
                                <option value="High School Graduate">High School Graduate</option>
                                <option value="College Undergraduate">College Undergraduate</option>
                                <option value="College Graduate">College Graduate</option>
                                <option value="Vocational">Vocational</option>
                                <option value="Post Graduate">Post Graduate</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">School Name (if student)</label>
                            <input type="text" name="school_name" class="form-control">
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-success"><i class="bi bi-heart-pulse"></i> Health Information</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Blood Type</label>
                            <select name="blood_type" class="form-select">
                                <option value="">Select</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-warning"><i class="bi bi-star-fill"></i> Special Categories & IDs</h6></div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_pwd" class="form-check-input" id="is_pwd_modal" value="1">
                                <label class="form-check-label fw-bold" for="is_pwd_modal">Person with Disability (PWD)</label>
                            </div>
                            <input type="text" name="pwd_id" id="pwd_id_input_modal" class="form-control" placeholder="PWD ID Number" disabled>
                            <input type="text" name="disability_type" id="disability_type_input_modal" class="form-control mt-2" placeholder="Type of Disability" disabled>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_4ps_beneficiary" class="form-check-input" id="is_4ps_modal" value="1">
                                <label class="form-check-label fw-bold" for="is_4ps_modal">4Ps Beneficiary</label>
                            </div>
                            <input type="text" name="4ps_id" id="4ps_id_input_modal" class="form-control" placeholder="4Ps ID Number" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Voting Status</label>
                            <select name="is_voter" id="voting_status_select_modal" class="form-select mb-2" disabled>
                                <option value="">-- Select Age First --</option>
                            </select>
                            <input type="text" name="precinct_number" id="precinct_input_modal" class="form-control" placeholder="Precinct Number" disabled>
                            <small class="text-muted" id="voting_hint_modal">Determined by age</small>
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-info"><i class="bi bi-chat-square-text"></i> Additional Information</h6></div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Extended Family Modal -->
<div class="modal fade" id="addExtendedFamilyModal" tabindex="-1" aria-labelledby="addExtendedFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('sub-families.store') }}" method="POST">
                @csrf
                <input type="hidden" name="household_id" value="{{ $household->id }}">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addExtendedFamilyModalLabel">
                        <i class="bi bi-people-fill"></i> Add Extended Family (Co-Head)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Note:</strong> This creates a new family group with its own head (co-head) within this household.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Family Name <span class="text-danger">*</span></label>
                            <input type="text" name="sub_family_name" class="form-control" required placeholder="e.g., Extended Family, Relative's Family">
                            <small class="text-muted">Give this family group a descriptive name</small>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <h6 class="text-primary"><i class="bi bi-person"></i> Co-Head Information</h6>
                            <p class="text-muted small">The head of this extended family group</p>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Suffix</label>
                            <input type="text" name="suffix" class="form-control" placeholder="Jr., Sr.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                            <input type="date" name="birthdate" id="birthdate_extended" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-select" required>
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Relationship to Primary Head <span class="text-danger">*</span></label>
                            <select name="household_role" class="form-select" required>
                                <option value="">Select</option>
                                <option value="spouse">Spouse</option>
                                <option value="child">Child</option>
                                <option value="parent">Parent</option>
                                <option value="sibling">Sibling</option>
                                <option value="relative">Relative</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="09XX-XXX-XXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Place of Birth</label>
                            <input type="text" name="place_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Filipino">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-primary"><i class="bi bi-briefcase"></i> Employment & Education</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="">Select</option>
                                <option value="employed">Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="self-employed">Self-Employed</option>
                                <option value="student">Student</option>
                                <option value="retired">Retired</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monthly Income</label>
                            <input type="number" name="monthly_income" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employer Name</label>
                            <input type="text" name="employer_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Educational Attainment</label>
                            <select name="educational_attainment" class="form-select">
                                <option value="">Select</option>
                                <option value="No Formal Education">No Formal Education</option>
                                <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                                <option value="Elementary Graduate">Elementary Graduate</option>
                                <option value="High School Undergraduate">High School Undergraduate</option>
                                <option value="High School Graduate">High School Graduate</option>
                                <option value="College Undergraduate">College Undergraduate</option>
                                <option value="College Graduate">College Graduate</option>
                                <option value="Vocational">Vocational</option>
                                <option value="Post Graduate">Post Graduate</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">School Name (if student)</label>
                            <input type="text" name="school_name" class="form-control">
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-success"><i class="bi bi-heart-pulse"></i> Health Information</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Blood Type</label>
                            <select name="blood_type" class="form-select">
                                <option value="">Select</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-warning"><i class="bi bi-star-fill"></i> Special Categories & IDs</h6></div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_pwd" class="form-check-input" id="is_pwd_extended" value="1">
                                <label class="form-check-label fw-bold" for="is_pwd_extended">Person with Disability (PWD)</label>
                            </div>
                            <input type="text" name="pwd_id" id="pwd_id_input_extended" class="form-control mb-2" placeholder="PWD ID Number" disabled>
                            <select name="disability_type" id="disability_type_input_extended" class="form-select" disabled>
                                <option value="">Select Disability Type</option>
                                <option value="Visual Impairment">Visual Impairment</option>
                                <option value="Hearing Impairment">Hearing Impairment</option>
                                <option value="Speech Impairment">Speech Impairment</option>
                                <option value="Physical Disability">Physical Disability</option>
                                <option value="Mental/Intellectual Disability">Mental/Intellectual Disability</option>
                                <option value="Psychosocial Disability">Psychosocial Disability</option>
                                <option value="Multiple Disabilities">Multiple Disabilities</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_4ps_beneficiary" class="form-check-input" id="is_4ps_extended" value="1">
                                <label class="form-check-label fw-bold" for="is_4ps_extended">4Ps Beneficiary</label>
                            </div>
                            <input type="text" name="4ps_id" id="4ps_id_input_extended" class="form-control" placeholder="4Ps ID Number" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Voting Status</label>
                            <select name="is_voter" id="voting_status_select_extended" class="form-select mb-2" disabled>
                                <option value="">-- Select Age First --</option>
                            </select>
                            <input type="text" name="precinct_number" id="precinct_input_extended" class="form-control" placeholder="Precinct Number" disabled>
                            <small class="text-muted" id="voting_hint_extended">Determined by age</small>
                        </div>
                        
                        <div class="col-12"><hr><h6 class="text-info"><i class="bi bi-chat-square-text"></i> Additional Information</h6></div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Add Extended Family
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== ADD MEMBER MODAL ==========
    const birthdateInputModal = document.getElementById('birthdate_modal');
    const pwdCheckboxModal = document.getElementById('is_pwd_modal');
    const pwdIdInputModal = document.getElementById('pwd_id_input_modal');
    const disabilityTypeInputModal = document.getElementById('disability_type_input_modal');
    const fourPsCheckboxModal = document.getElementById('is_4ps_modal');
    const fourPsIdInputModal = document.getElementById('4ps_id_input_modal');
    
    // PWD checkbox handler for Add Member modal
    if (pwdCheckboxModal) {
        pwdCheckboxModal.addEventListener('change', function() {
            if (this.checked) {
                pwdIdInputModal.disabled = false;
                disabilityTypeInputModal.disabled = false;
            } else {
                pwdIdInputModal.disabled = true;
                disabilityTypeInputModal.disabled = true;
                pwdIdInputModal.value = '';
                disabilityTypeInputModal.value = '';
            }
        });
    }
    
    // 4Ps checkbox handler for Add Member modal
    if (fourPsCheckboxModal) {
        fourPsCheckboxModal.addEventListener('change', function() {
            if (this.checked) {
                fourPsIdInputModal.disabled = false;
            } else {
                fourPsIdInputModal.disabled = true;
                fourPsIdInputModal.value = '';
            }
        });
    }
    
    // Voting status calculator for Add Member modal
    function calculateVotingStatusModal() {
        const birthdate = birthdateInputModal.value;
        const votingSelect = document.getElementById('voting_status_select_modal');
        const votingHint = document.getElementById('voting_hint_modal');
        const precinctInput = document.getElementById('precinct_input_modal');
        
        if (!birthdate) {
            votingSelect.innerHTML = '<option value="">-- Select Age First --</option>';
            votingSelect.disabled = true;
            precinctInput.disabled = true;
            votingHint.textContent = 'Determined by age';
            votingHint.className = 'text-muted';
            return;
        }
        
        const age = calculateAge(birthdate);
        updateVotingStatus(age, votingSelect, votingHint, precinctInput);
    }
    
    // ========== ADD EXTENDED FAMILY MODAL ==========
    const birthdateInputExtended = document.getElementById('birthdate_extended');
    const pwdCheckboxExtended = document.getElementById('is_pwd_extended');
    const pwdIdInputExtended = document.getElementById('pwd_id_input_extended');
    const disabilityTypeInputExtended = document.getElementById('disability_type_input_extended');
    const fourPsCheckboxExtended = document.getElementById('is_4ps_extended');
    const fourPsIdInputExtended = document.getElementById('4ps_id_input_extended');
    
    // PWD checkbox handler for Extended Family modal
    if (pwdCheckboxExtended) {
        pwdCheckboxExtended.addEventListener('change', function() {
            if (this.checked) {
                pwdIdInputExtended.disabled = false;
                disabilityTypeInputExtended.disabled = false;
            } else {
                pwdIdInputExtended.disabled = true;
                disabilityTypeInputExtended.disabled = true;
                pwdIdInputExtended.value = '';
                disabilityTypeInputExtended.value = '';
            }
        });
    }
    
    // 4Ps checkbox handler for Extended Family modal
    if (fourPsCheckboxExtended) {
        fourPsCheckboxExtended.addEventListener('change', function() {
            if (this.checked) {
                fourPsIdInputExtended.disabled = false;
            } else {
                fourPsIdInputExtended.disabled = true;
                fourPsIdInputExtended.value = '';
            }
        });
    }
    
    // Voting status calculator for Extended Family modal
    function calculateVotingStatusExtended() {
        const birthdate = birthdateInputExtended.value;
        const votingSelect = document.getElementById('voting_status_select_extended');
        const votingHint = document.getElementById('voting_hint_extended');
        const precinctInput = document.getElementById('precinct_input_extended');
        
        if (!birthdate) {
            votingSelect.innerHTML = '<option value="">-- Select Age First --</option>';
            votingSelect.disabled = true;
            precinctInput.disabled = true;
            votingHint.textContent = 'Determined by age';
            votingHint.className = 'text-muted';
            return;
        }
        
        const age = calculateAge(birthdate);
        updateVotingStatus(age, votingSelect, votingHint, precinctInput);
    }
    
    // ========== SHARED FUNCTIONS ==========
    function calculateAge(birthdate) {
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }
    
    function updateVotingStatus(age, votingSelect, votingHint, precinctInput) {
        if (age < 15) {
            votingSelect.innerHTML = '<option value="0" selected>Not Eligible (Below 15 years old)</option>';
            votingSelect.disabled = true;
            votingSelect.className = 'form-select mb-2 text-muted';
            precinctInput.disabled = true;
            precinctInput.value = '';
            votingHint.textContent = 'Not eligible to vote';
            votingHint.className = 'text-muted';
        } else if (age >= 15 && age <= 17) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered SK Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-info fw-bold';
            votingHint.textContent = 'SK Voter eligibility (15-17 years old)';
            votingHint.className = 'text-info fw-bold';
        } else if (age >= 18) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-success fw-bold';
            votingHint.textContent = 'Regular Voter eligibility (18+ years old)';
            votingHint.className = 'text-success fw-bold';
        }
    }
    
    // Handle voting status selection change for both modals
    document.addEventListener('change', function(e) {
        if (e.target.id === 'voting_status_select_modal') {
            const precinctInput = document.getElementById('precinct_input_modal');
            if (e.target.value === '1') {
                precinctInput.disabled = false;
            } else {
                precinctInput.disabled = true;
                precinctInput.value = '';
            }
        }
        
        if (e.target.id === 'voting_status_select_extended') {
            const precinctInput = document.getElementById('precinct_input_extended');
            if (e.target.value === '1') {
                precinctInput.disabled = false;
            } else {
                precinctInput.disabled = true;
                precinctInput.value = '';
            }
        }
    });
    
    // Calculate when birthdate changes - Add Member modal
    if (birthdateInputModal) {
        birthdateInputModal.addEventListener('change', calculateVotingStatusModal);
        birthdateInputModal.addEventListener('blur', calculateVotingStatusModal);
    }
    
    // Calculate when birthdate changes - Extended Family modal
    if (birthdateInputExtended) {
        birthdateInputExtended.addEventListener('change', calculateVotingStatusExtended);
        birthdateInputExtended.addEventListener('blur', calculateVotingStatusExtended);
    }
});
</script>
@endpush

@endsection
