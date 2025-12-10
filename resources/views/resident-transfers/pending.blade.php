@extends('layouts.app')

@section('title', 'Pending Transfer Approvals')

@section('content')
<div class="page-header mb-3">
    <div class="page-header__title"><i class="bi bi-clock-history"></i> Pending Transfer Approvals</div>
    <div class="page-header__meta">
        <span class="truncate">Review and process transfer requests</span>
    </div>
    <div class="page-header__spacer"></div>
    <div class="page-header__actions">
        <a href="{{ route('resident-transfers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> All Transfers
        </a>
    </div>
</div>

@if($pendingTransfers->count() > 0)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>{{ $pendingTransfers->total() }}</strong> transfer request(s) waiting for your approval.
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-clickable" id="pendingTransfersTable">
                <thead>
                    <tr>
                        <th><i class="bi bi-person"></i> Resident</th>
                        <th><i class="bi bi-house-door"></i> From</th>
                        <th><i class="bi bi-house-check"></i> To</th>
                        <th><i class="bi bi-tag"></i> Type</th>
                        <th><i class="bi bi-calendar"></i> Date</th>
                        <th><i class="bi bi-person-lines"></i> Requested By</th>
                        <th><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingTransfers as $transfer)
                        <tr data-href="{{ route('resident-transfers.show', $transfer) }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                                        <i class="bi bi-person-fill text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $transfer->resident->full_name }}</strong>
                                        <small class="text-muted">{{ $transfer->resident->resident_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $transfer->oldHousehold->household_id ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $transfer->old_purok }}</small>
                            </td>
                            <td>
                                @if($transfer->transfer_type === 'internal')
                                    <strong>{{ $transfer->newHousehold->household_id ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $transfer->new_purok ?? $transfer->newHousehold->purok ?? '' }}</small>
                                @else
                                    <span class="badge bg-warning">External Location</span><br>
                                    <small class="text-muted">{{ $transfer->destination_barangay ?? 'Outside Barangay' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($transfer->transfer_type === 'internal')
                                    <span class="badge bg-info">Internal</span>
                                @else
                                    <span class="badge bg-warning">External</span>
                                @endif
                            </td>
                            <td>
                                {{ $transfer->transfer_date->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $transfer->transfer_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $transfer->creator->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $transfer->id }}">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $transfer->id }}">
                                        <i class="bi bi-x"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No Pending Transfers</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($pendingTransfers as $transfer)
<div class="modal fade" id="approveModal{{ $transfer->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $transfer->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel{{ $transfer->id }}">Approve Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resident-transfers.approve', $transfer) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Approve transfer for <strong>{{ $transfer->resident->full_name }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
 </div>

<div class="modal fade" id="rejectModal{{ $transfer->id }}" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectModalLabel{{ $transfer->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel{{ $transfer->id }}">Reject Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resident-transfers.reject', $transfer) }}" method="POST" class="js-reject-form" novalidate>
                @csrf
                <div class="modal-body">
                    <p>Reject transfer for <strong>{{ $transfer->resident->full_name }}</strong>?</p>
                    <label class="form-label">Reason for rejection:</label>
                    <textarea name="remarks" class="form-control" rows="3" required minlength="3" aria-describedby="rejectHelp{{ $transfer->id }}"></textarea>
                    <div id="rejectHelp{{ $transfer->id }}" class="form-text">Provide a clear reason. Minimum 3 characters.</div>
                    <div class="invalid-feedback">A rejection reason is required.</div>
                    <div class="alert alert-danger d-none js-reject-error" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" data-submit>
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                        <span>Reject</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>
@endforeach

@if($pendingTransfers->hasPages())
<div class="mt-4">
    {{ $pendingTransfers->links() }}
</div>
@endif
@endsection
