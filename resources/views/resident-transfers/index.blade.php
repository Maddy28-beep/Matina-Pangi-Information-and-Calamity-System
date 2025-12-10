@extends('layouts.app')

@section('title', 'Resident Transfers')

@push('styles')
<style>
    .page-title { font-weight: 600; }
    .ds-page__subtitle { color: #6b7280; }
    .ds-toolbar__group .btn { border-radius: 10px; }
</style>
@endpush

@section('content')<div class="ds-page">
<div class="ds-page__header mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title d-flex align-items-center gap-2 mb-1"><i class="bi bi-arrow-left-right"></i>Resident Transfers</h1>
        <p class="ds-page__subtitle mb-0">Manage resident movement and approvals</p>
    </div>
    <div class="ds-toolbar__group d-flex gap-2">
        @if(auth()->user()->isSecretary())
            <a href="{{ route('resident-transfers.pending') }}" class="btn btn-outline-warning">
                <i class="bi bi-clock-history"></i>
                <span>Pending Approvals</span>
            </a>
            <a href="{{ route('resident-transfers.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i>
                <span>Request Transfer</span>
            </a>
        @else
            <a href="{{ route('staff.resident-transfers.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i>
                <span>Request Transfer</span>
            </a>
        @endif
    </div>
</div>

@php
$transferSearchFields = [
    [
        'name' => 'search',
        'type' => 'text',
        'label' => 'Resident Name',
        'placeholder' => 'Search by resident name...',
        'col' => 4
    ],
    [
        'name' => 'status',
        'type' => 'select',
        'label' => 'Status',
        'placeholder' => 'All Status',
        'options' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'rejected' => 'Rejected'
        ],
        'col' => 3
    ],
    [
        'name' => 'type',
        'type' => 'select',
        'label' => 'Transfer Type',
        'placeholder' => 'All Types',
        'options' => [
            'internal' => 'Internal Transfer',
            'external' => 'External Transfer'
        ],
        'col' => 3
    ],
    [
        'name' => 'reason',
        'type' => 'select',
        'label' => 'Reason',
        'placeholder' => 'All Reasons',
        'options' => [
            'marriage' => 'Marriage',
            'work' => 'Work/Employment',
            'education' => 'Education',
            'family' => 'Family Reasons',
            'housing' => 'Housing',
            'other' => 'Other'
        ],
        'col' => 2
    ]
];
@endphp

<x-search-filter 
    :route="auth()->user()->isSecretary() ? route('resident-transfers.index') : route('staff.resident-transfers.index')" 
    title="Search & Filter Resident Transfers"
    icon="bi-arrow-left-right"
    :fields="$transferSearchFields"
    :advanced="false"
    :inline="true" />

<!-- Transfers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover transfer-table table-clickable" id="residentTransfersTable">
                <thead>
                    <tr>
                        <th><i class="bi bi-person"></i> Resident</th>
                        <th><i class="bi bi-house-door"></i> From</th>
                        <th><i class="bi bi-house-check"></i> To</th>
                        <th><i class="bi bi-tag"></i> Type</th>
                        <th><i class="bi bi-calendar"></i> Date</th>
                        <th><i class="bi bi-info-circle"></i> Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr
                            @if(auth()->user()->isSecretary())
                                data-href="{{ route('resident-transfers.show', $transfer) }}"
                            @else
                                data-href="{{ route('staff.resident-transfers.show', $transfer) }}"
                            @endif
                        >
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                        <i class="bi bi-person-fill text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $transfer->resident->full_name }}</strong>
                                        <small class="text-muted">{{ $transfer->resident->resident_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($transfer->oldHousehold)
                                    <strong class="text-danger">{{ $transfer->oldHousehold->household_id }}</strong><br>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $transfer->old_purok }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->transfer_type === 'transfer_in' && $transfer->newHousehold)
                                    <strong class="text-success">{{ $transfer->newHousehold->household_id }}</strong><br>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $transfer->new_purok ?? $transfer->newHousehold->purok }}</small>
                                @elseif($transfer->transfer_type === 'transfer_out')
                                    <span class="badge bg-warning">External Location</span><br>
                                    <small class="text-muted">{{ $transfer->destination_barangay ?? 'Outside Barangay' }}</small>
                                @else
                                    <span class="text-muted">Pending Assignment</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->transfer_type === 'transfer_in')
                                    <span class="badge bg-info"><i class="bi bi-arrow-left-right"></i> Internal</span>
                                @elseif($transfer->transfer_type === 'transfer_out')
                                    <span class="badge bg-warning"><i class="bi bi-box-arrow-right"></i> External</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-calendar-event"></i>
                                {{ $transfer->transfer_date->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $transfer->transfer_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="status-badge pending">
                                        <i class="bi bi-clock-history"></i> Pending
                                    </span>
                                @elseif($transfer->status === 'approved')
                                    <span class="status-badge approved">
                                        <i class="bi bi-check-circle"></i> Approved
                                    </span>
                                @elseif($transfer->status === 'completed')
                                    <span class="status-badge completed">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </span>
                                @else
                                    <span class="status-badge rejected">
                                        <i class="bi bi-x-circle"></i> Rejected
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="ds-empty-state" style="margin: 3rem 2rem;">
                                    <div class="ds-empty-state__icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h3 class="ds-empty-state__title">No Transfer Records Found</h3>
                                    <p class="ds-empty-state__description">There are no resident transfer records matching your criteria.</p>
                                    <a href="{{ auth()->user()->isSecretary() ? route('resident-transfers.create') : route('staff.resident-transfers.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Create New Transfer Request
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
        <div class="mt-3">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
