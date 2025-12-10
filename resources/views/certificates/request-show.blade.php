@extends('layouts.app')

@section('title', 'Certificate Request Details')

@section('content')<div class="ds-page">
<div class="page-header">
    <div>
        <h2 class="mb-1"><i class="bi bi-file-earmark-text"></i> Certificate Request Details</h2>
        <p class="text-muted mb-0">Review request information before approval</p>
    </div>
    <a href="{{ route('approvals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Approvals</a>
    </div>

<div class="card border-0">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-semibold">Requester</div>
                    <div>{{ optional($certificateRequest->resident)->full_name }}</div>
                    @if(optional($certificateRequest->resident)->household)
                        <small class="text-muted">HH# {{ optional($certificateRequest->resident->household)->household_number }}</small>
                    @endif
                </div>
                <div class="mb-3">
                    <div class="fw-semibold">Certificate Type</div>
                    <div>{{ ucwords(str_replace('_',' ', $certificateRequest->certificate_type)) }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold">Purpose</div>
                    <div>{{ $certificateRequest->purpose }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="fw-semibold">Status</div>
                    @php $statusClass = $certificateRequest->status === 'approved' ? 'bg-success' : ($certificateRequest->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark'); @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($certificateRequest->status) }}</span>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold">Requested On</div>
                    <div>{{ $certificateRequest->created_at?->format('M d, Y h:i A') }}</div>
                </div>
                @if($certificateRequest->notes)
                <div class="mb-3">
                    <div class="fw-semibold">Notes</div>
                    <div>{{ $certificateRequest->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        @if(auth()->user()->isSecretary())
        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            @if($certificateRequest->status === 'pending')
            <form action="{{ route('approvals.certificate.approve', $certificateRequest) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve & Issue</button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectCertificateModal"><i class="bi bi-x-circle"></i> Reject</button>
            @else
            <div class="alert alert-success mb-0">This request has been {{ strtoupper($certificateRequest->status) }}.</div>
            @endif
        </div>

        @if($certificateRequest->status === 'pending')
        <div class="modal fade" id="rejectCertificateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('approvals.certificate.reject', $certificateRequest) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Certificate Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea class="form-control" name="rejection_reason" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>
</div>
@endsection
