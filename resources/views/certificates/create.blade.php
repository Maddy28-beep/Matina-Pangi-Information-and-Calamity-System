@extends('layouts.app')

@section('title', (auth()->user()->isSecretary() ? 'Issue Certificate' : 'Submit Certificate Request').' - Barangay Matina Pangi')

@section('content')<div class="ds-page"><!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-file-earmark-text-fill"></i> {{ auth()->user()->isSecretary() ? 'Issue New Certificate' : 'Submit Certificate Request' }}
        </h2>
        <p class="text-muted mb-0">{{ auth()->user()->isSecretary() ? 'Create and issue official barangay certificates for residents' : 'Draft and submit a certificate request for secretary approval' }}</p>
    </div>
    <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Certificates
    </a>
</div>

<!-- Form Card -->
<div class="card border-0">
    <div class="card-header bg-gradient">
        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> {{ auth()->user()->isSecretary() ? 'Certificate Information' : 'Request Details' }}</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ auth()->user()->isSecretary() ? route('certificates.store') : route('certificates.requests.store') }}" method="POST">
            @csrf

            <!-- Resident & Certificate Type Section -->
            <div class="mb-4">
                <h6 class="text-uppercase fw-semibold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    <i class="bi bi-person-badge"></i> Resident & Certificate Details
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="resident_id" class="form-label">
                            <i class="bi bi-person-circle text-primary"></i> Resident <span class="text-danger">*</span>
                        </label>
                        <select name="resident_id" id="resident_id" class="form-select @error('resident_id') is-invalid @enderror" required>
                            <option value="">🔍 Search and select resident...</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->full_name }}{{ $resident->household ? ' - ' . $resident->household->household_number : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('resident_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="certificate_type" class="form-label">
                            <i class="bi bi-file-earmark-check text-success"></i> Certificate Type <span class="text-danger">*</span>
                        </label>
                        <select name="certificate_type" id="certificate_type" class="form-select @error('certificate_type') is-invalid @enderror" required>
                            <option value="">📄 Select certificate type...</option>
                            <option value="barangay_clearance" {{ old('certificate_type') == 'barangay_clearance' ? 'selected' : '' }}>🏘️ Barangay Clearance</option>
                            <option value="certificate_of_indigency" {{ old('certificate_type') == 'certificate_of_indigency' ? 'selected' : '' }}>💰 Certificate of Indigency</option>
                            <option value="certificate_of_residency" {{ old('certificate_type') == 'certificate_of_residency' ? 'selected' : '' }}>🏠 Certificate of Residency</option>
                            <option value="business_clearance" {{ old('certificate_type') == 'business_clearance' ? 'selected' : '' }}>💼 Business Clearance</option>
                            <option value="good_moral" {{ old('certificate_type') == 'good_moral' ? 'selected' : '' }}>⭐ Certificate of Good Moral</option>
                            <option value="travel_permit" {{ old('certificate_type') == 'travel_permit' ? 'selected' : '' }}>✈️ Travel Permit</option>
                        </select>
                        @error('certificate_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Purpose Section -->
            <div class="mb-4">
                <h6 class="text-uppercase fw-semibold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    <i class="bi bi-chat-left-text"></i> Purpose & Details
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="purpose" class="form-label">
                            <i class="bi bi-pencil text-info"></i> Purpose <span class="text-danger">*</span>
                        </label>
                        <textarea name="purpose" id="purpose" rows="4" class="form-control @error('purpose') is-invalid @enderror" placeholder="Enter the purpose for requesting this certificate..." required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Be specific about why this certificate is needed</small>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isSecretary())
            <!-- Payment & Validity Section -->
            <div class="mb-4">
                <h6 class="text-uppercase fw-semibold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    <i class="bi bi-cash-coin"></i> Payment & Validity
                </h6>
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="or_number" class="form-label">
                            <i class="bi bi-receipt text-warning"></i> OR Number
                        </label>
                        <input type="text" name="or_number" id="or_number" class="form-control @error('or_number') is-invalid @enderror" value="{{ old('or_number') }}" placeholder="e.g., OR-2025-001">
                        @error('or_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Official Receipt number</small>
                    </div>

                    <div class="col-md-4">
                        <label for="amount_paid" class="form-label">
                            <i class="bi bi-currency-dollar text-success"></i> Amount Paid <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount_paid" id="amount_paid" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid', '0.00') }}" placeholder="0.00" required>
                        </div>
                        @error('amount_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="valid_until" class="form-label">
                            <i class="bi bi-calendar-check text-info"></i> Valid Until
                        </label>
                        <input type="date" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave blank for no expiry</small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Notes Section -->
            <div class="mb-4">
                <h6 class="text-uppercase fw-semibold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    <i class="bi bi-sticky"></i> Additional Notes
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="remarks" class="form-label">
                            <i class="bi bi-chat-square-text text-secondary"></i> Remarks (Optional)
                        </label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Add any additional notes or special instructions...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-gradient px-5">
                    <i class="bi bi-check-circle-fill"></i> {{ auth()->user()->isSecretary() ? 'Issue Certificate' : 'Submit for Approval' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
