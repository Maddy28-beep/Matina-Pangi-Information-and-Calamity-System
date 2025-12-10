@extends('layouts.app')

@section('title', 'Transfer Request Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-arrow-left-right"></i> Transfer Request Details</h2>
    <div class="btn-group">
        @if(auth()->user()->isSecretary() && $residentTransfer->status === 'pending')
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
            <i class="bi bi-check-circle"></i> Approve
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-circle"></i> Reject
        </button>
        @endif
        <a href="{{ auth()->user()->isSecretary() ? route('resident-transfers.index') : route('staff.resident-transfers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="alert 
    @if($residentTransfer->status === 'pending') alert-warning
    @elseif($residentTransfer->status === 'approved') alert-info
    @elseif($residentTransfer->status === 'completed') alert-success
    @else alert-danger
    @endif">
    <h5 class="mb-0">
        <i class="bi 
            @if($residentTransfer->status === 'pending') bi-clock-history
            @elseif($residentTransfer->status === 'approved') bi-check-circle
            @elseif($residentTransfer->status === 'completed') bi-check-circle-fill
            @else bi-x-circle
            @endif"></i>
        Status: <strong>{{ ucfirst($residentTransfer->status) }}</strong>
    </h5>
</div>

<div class="row">
    <!-- Resident Information -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Resident Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $residentTransfer->resident->full_name }}</p>
                <p><strong>Resident ID:</strong> {{ $residentTransfer->resident->resident_id }}</p>
                <p><strong>Age:</strong> {{ $residentTransfer->resident->age }} years old</p>
                <p><strong>Sex:</strong> {{ ucfirst($residentTransfer->resident->sex) }}</p>
                <a href="{{ auth()->user()->isSecretary() ? route('residents.show', $residentTransfer->resident) : route('staff.residents.show', $residentTransfer->resident) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> View Full Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Transfer Details -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Transfer Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Transfer Type:</strong> 
                    @if($residentTransfer->transfer_type === 'transfer_in')
                        <span class="badge bg-info">Internal Transfer (Within Matina Pangi)</span>
                        @if($residentTransfer->internal_transfer_type === 'create_new')
                            <br><small class="text-muted ms-2">→ Creating New Household (Building Own House)</small>
                        @elseif($residentTransfer->internal_transfer_type === 'join_existing')
                            <br><small class="text-muted ms-2">→ Joining Existing Household</small>
                        @endif
                    @elseif($residentTransfer->transfer_type === 'transfer_out')
                        <span class="badge bg-warning">External Transfer (Outside Matina Pangi)</span>
                    @else
                        <span class="badge bg-secondary">Unknown</span>
                    @endif
                </p>
                <p><strong>Transfer Date:</strong> {{ $residentTransfer->transfer_date->format('F d, Y') }}</p>
                <p><strong>Requested By:</strong> {{ $residentTransfer->creator->name ?? 'N/A' }}</p>
                <p><strong>Requested On:</strong> {{ $residentTransfer->created_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- From/To Information -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Transfer Route</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <h6 class="text-danger">From (Current Location)</h6>
                        @if($residentTransfer->oldHousehold)
                            <p><strong>Household:</strong> {{ $residentTransfer->oldHousehold->household_id }}</p>
                            <p><strong>Purok:</strong> {{ $residentTransfer->old_purok }}</p>
                            <p><strong>Address:</strong> {{ $residentTransfer->oldHousehold->address }}</p>
                            <a href="{{ route('households.show', $residentTransfer->oldHousehold) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-house"></i> View Household
                            </a>
                        @else
                            <p class="text-muted">No household information</p>
                        @endif
                    </div>
                    <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-right" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <div class="col-md-5">
                        <h6 class="text-success">To (New Location)</h6>
                        @if($residentTransfer->transfer_type === 'transfer_in')
                            @if($residentTransfer->internal_transfer_type === 'create_new')
                                {{-- NEW HOUSEHOLD CREATION SCENARIO --}}
                                @if($residentTransfer->status === 'pending' || $residentTransfer->status === 'approved')
                                    <div class="alert alert-info mb-2">
                                        <i class="bi bi-house-add"></i> <strong>Creating New Household</strong>
                                    </div>
                                    <p><strong>Purok:</strong> {{ $residentTransfer->new_purok }}</p>
                                    <p><strong>Address:</strong> {{ $residentTransfer->new_address }}, {{ $residentTransfer->new_purok }}, Matina Pangi, Davao City</p>
                                    
                                    @if($residentTransfer->new_household_head_option === 'self')
                                        <p><strong>Household Head:</strong> <span class="badge bg-success">{{ $residentTransfer->resident->full_name }} (Transferring Resident)</span></p>
                                    @elseif($residentTransfer->new_household_head_option === 'existing_resident' && $residentTransfer->newHouseholdHead)
                                        <p><strong>Household Head:</strong> <span class="badge bg-primary">{{ $residentTransfer->newHouseholdHead->full_name }} (Existing Resident)</span></p>
                                        <p><strong>Transferring Resident:</strong> {{ $residentTransfer->resident->full_name }} <small class="text-muted">(will be a member)</small></p>
                                    @elseif($residentTransfer->new_household_head_option === 'new_person')
                                        <p><strong>Household Head:</strong> <span class="badge bg-warning">New Person (To be registered)</span></p>
                                        <p><strong>Transferring Resident:</strong> {{ $residentTransfer->resident->full_name }} <small class="text-muted">(will be a member)</small></p>
                                    @else
                                        <p><strong>Role:</strong> <span class="badge bg-success">Will become Household Head</span></p>
                                    @endif
                                    
                                    @if($residentTransfer->status === 'pending')
                                        <small class="text-muted">New household will be created upon approval</small>
                                    @endif
                                @else
                                    {{-- After completion, show the created household --}}
                                    @if($residentTransfer->newHousehold)
                                        <div class="alert alert-success mb-2">
                                            <i class="bi bi-check-circle"></i> <strong>New Household Created</strong>
                                        </div>
                                        <p><strong>Household:</strong> {{ $residentTransfer->newHousehold->household_id }}</p>
                                        <p><strong>Purok:</strong> {{ $residentTransfer->new_purok ?? $residentTransfer->newHousehold->purok }}</p>
                                        <p><strong>Address:</strong> {{ $residentTransfer->newHousehold->address }}</p>
                                        
                                        @php
                                            $primaryFamily = $residentTransfer->newHousehold->subFamilies()->where('is_primary_family', true)->first();
                                            $head = $primaryFamily ? $primaryFamily->subHead : null;
                                        @endphp
                                        
                                        @if($head)
                                            <p><strong>Household Head:</strong> <span class="badge bg-success">{{ $head->full_name }}</span></p>
                                        @endif
                                        
                                        <p><strong>Total Members:</strong> {{ $residentTransfer->newHousehold->total_members }}</p>
                                        
                                        <a href="{{ auth()->user()->isStaff() ? route('staff.households.show', $residentTransfer->newHousehold) : route('households.show', $residentTransfer->newHousehold) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-house"></i> View Household
                                        </a>
                                    @else
                                        <p class="text-muted">New household information not available</p>
                                    @endif
                                @endif
                            @else
                                {{-- JOIN EXISTING HOUSEHOLD SCENARIO --}}
                                @if($residentTransfer->newHousehold)
                                    <p><strong>Household:</strong> {{ $residentTransfer->newHousehold->household_id }}</p>
                                    <p><strong>Purok:</strong> {{ $residentTransfer->new_purok ?? $residentTransfer->newHousehold->purok }}</p>
                                    <p><strong>Address:</strong> {{ $residentTransfer->newHousehold->address }}</p>
                                    <a href="{{ auth()->user()->isStaff() ? route('staff.households.show', $residentTransfer->newHousehold) : route('households.show', $residentTransfer->newHousehold) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-house"></i> View Household
                                    </a>
                                @else
                                    <p class="text-muted">Pending assignment</p>
                                @endif
                            @endif
                        @else
                            <p><strong>Location:</strong> Outside Matina Pangi</p>
                            @if($residentTransfer->destination_address)
                                <p><strong>Address:</strong> {{ $residentTransfer->destination_address }}</p>
                            @endif
                            @if($residentTransfer->destination_barangay)
                                <p><strong>Barangay:</strong> {{ $residentTransfer->destination_barangay }}</p>
                            @endif
                            @if($residentTransfer->destination_municipality)
                                <p><strong>Municipality:</strong> {{ $residentTransfer->destination_municipality }}</p>
                            @endif
                            @if($residentTransfer->destination_province)
                                <p><strong>Province:</strong> {{ $residentTransfer->destination_province }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reason -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> Reason for Transfer</h5>
            </div>
            <div class="card-body">
                <p><strong>Category:</strong> {{ ucfirst($residentTransfer->reason) }}</p>
                <p><strong>Detailed Reason:</strong></p>
                <p class="bg-light p-3 rounded">{{ $residentTransfer->reason_for_transfer ?? $residentTransfer->reason_details ?? 'No details provided' }}</p>
            </div>
        </div>
    </div>

    <!-- Approval Information -->
    @if($residentTransfer->status !== 'pending')
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-check"></i> Approval Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Processed By:</strong> {{ $residentTransfer->approver->name ?? 'N/A' }}</p>
                <p><strong>Processed On:</strong> {{ $residentTransfer->approved_at ? $residentTransfer->approved_at->format('F d, Y h:i A') : 'N/A' }}</p>
                @if($residentTransfer->remarks)
                    <p><strong>Remarks:</strong></p>
                    <p class="bg-light p-3 rounded">{{ $residentTransfer->remarks }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Approve Modal -->
@if(auth()->user()->isSecretary() && $residentTransfer->status === 'pending')
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Transfer Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resident-transfers.approve', $residentTransfer) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this transfer request?</p>
                    <div class="alert alert-info">
                        <strong>Note:</strong> Approving this request will:
                        <ul class="mb-0">
                            @if($residentTransfer->transfer_type === 'internal')
                                <li>Move the resident to the new household</li>
                                <li>Update household member counts</li>
                                <li>Create household events for both households</li>
                            @else
                                <li>Mark the resident as "Relocated"</li>
                                <li>Archive the resident record</li>
                                <li>Create a household event</li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Approve Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">Reject Transfer Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resident-transfers.reject', $residentTransfer) }}" method="POST" class="js-reject-form" novalidate>
                @csrf
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this transfer request:</p>
                    <textarea name="remarks" class="form-control" rows="4" required minlength="3" placeholder="Enter rejection reason..."></textarea>
                    <div class="invalid-feedback">A rejection reason is required.</div>
                    <div class="alert alert-danger d-none js-reject-error" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" data-submit><span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span><span><i class="bi bi-x-circle"></i> Reject Transfer</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
