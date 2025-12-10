@extends('layouts.app')

@section('title', 'My Submissions')

@push('styles')
    <style>
        .card-header .btn.btn-light {
            color: #111 !important;
            background-color: #fff !important;
            border-color: #fff !important;
        }

        .card-header .btn.btn-light .bi,
        .card-header .btn.btn-light i {
            color: #111 !important;
        }
    </style>
@endpush

@section('content')<div class="ds-page">
    <div class="page-header mb-3">
        <div class="d-flex flex-column">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text"></i>
                <span class="h2 fw-bold mb-0">My Submissions</span>
            </div>
            <div class="text-muted">Your pending residents, households, and transfers</div>
        </div>
    </div>

    <form method="GET" action="{{ route('staff.submissions.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Residents Status</label>
                    <select name="res_status" class="form-select">
                        @foreach(['pending', 'approved', 'rejected'] as $opt)
                            <option value="{{ $opt }}" @selected(($resStatus ?? 'pending') === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Households Status</label>
                    <select name="hh_status" class="form-select">
                        @foreach(['pending', 'approved', 'rejected'] as $opt)
                            <option value="{{ $opt }}" @selected(($hhStatus ?? 'pending') === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transfers Status</label>
                    <select name="tr_status" class="form-select">
                        @foreach(['all', 'pending', 'approved', 'completed', 'rejected'] as $opt)
                            <option value="{{ $opt }}" @selected(($trStatus ?? 'all') === $opt)>{{ ucfirst($opt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ $from ? $from->format('Y-m-d') : '' }}"
                        class="form-control" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ $to ? $to->format('Y-m-d') : '' }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-light text-dark"><i class="bi bi-funnel text-black"></i> Apply
                        Filters</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Pending Residents -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Residents ({{ $myResidents->total() }})</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.submissions.export', ['section' => 'residents', 'status' => $resStatus, 'from' => optional($from)->format('Y-m-d'), 'to' => optional($to)->format('Y-m-d')]) }}"
                    class="btn btn-sm btn-light">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($myResidents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Resident ID</th>
                                <th>Name</th>
                                <th>Household</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myResidents as $resident)
                                <tr>
                                    <td><strong>{{ $resident->resident_id }}</strong></td>
                                    <td>{{ $resident->full_name }}</td>
                                    <td>
                                        @if($resident->household)
                                            <a
                                                href="{{ route('households.show', $resident->household) }}">{{ $resident->household->household_id }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $resident->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('staff.residents.show', $resident) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $myResidents->links() }}
            @else
                <p class="text-muted mb-0">No pending residents submitted by you.</p>
            @endif
        </div>
    </div>

    <!-- Pending Households -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0"><i class="bi bi-house-add me-2"></i>Households ({{ $myHouseholds->total() }})</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.households.index') }}" class="btn btn-sm btn-light"><i class="bi bi-house"></i>
                    Open Households</a>
                <a href="{{ route('staff.submissions.export', ['section' => 'households', 'status' => $hhStatus, 'from' => optional($from)->format('Y-m-d'), 'to' => optional($to)->format('Y-m-d')]) }}"
                    class="btn btn-sm btn-light">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($myHouseholds->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Household ID</th>
                                <th>Address</th>
                                <th>Members</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myHouseholds as $household)
                                <tr>
                                    <td><strong>{{ $household->household_id }}</strong></td>
                                    <td>{{ $household->full_address }}</td>
                                    <td>{{ $household->residents->count() }}</td>
                                    <td>{{ $household->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('staff.households.show', $household) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $myHouseholds->links() }}
            @else
                <p class="text-muted mb-0">No pending households submitted by you.</p>
            @endif
        </div>
    </div>

    <!-- Transfer Requests -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transfer Requests ({{ $myTransfers->total() }})
            </h6>
            <div class="d-flex gap-2">
                <a href="{{ auth()->user()->isSecretary() ? route('resident-transfers.create') : route('staff.resident-transfers.create') }}"
                    class="btn btn-sm btn-light"><i class="bi bi-plus-lg"></i> Request Transfer</a>
                <a href="{{ route('staff.submissions.export', ['section' => 'transfers', 'status' => $trStatus, 'from' => optional($from)->format('Y-m-d'), 'to' => optional($to)->format('Y-m-d')]) }}"
                    class="btn btn-sm btn-light">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($myTransfers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-clickable" id="myTransfersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resident</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myTransfers as $transfer)
                                                <tr @if(auth()->user()->isSecretary())
                                                data-href="{{ route('resident-transfers.show', $transfer) }}" @else
                                                    data-href="{{ route('staff.resident-transfers.show', $transfer) }}" @endif>
                                                    <td>{{ $transfer->id }}</td>
                                                    <td>
                                                        @if($transfer->resident)
                                                            <a
                                                                href="{{ route('residents.show', $transfer->resident) }}">{{ $transfer->resident->full_name }}</a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $transfer->transfer_type)) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ match ($transfer->status) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'completed' => 'primary',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                } }}">{{ ucfirst($transfer->status) }}</span>
                                                    </td>
                                                    <td>{{ $transfer->created_at->format('M d, Y h:i A') }}</td>
                                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $myTransfers->links() }}
            @else
                <p class="text-muted mb-0">No transfer requests submitted by you yet.</p>
            @endif
        </div>
    </div>

</div>@endsection