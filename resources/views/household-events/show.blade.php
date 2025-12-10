@extends('layouts.app')

@section('title', 'Household Event Details')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('household-events.index') }}">Household Events</a></li>
        <li class="breadcrumb-item active">Event #{{ $householdEvent->id }}</li>
    </ol>
</nav>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-calendar-event"></i> Household Event Details
    </h2>
    <a href="{{ route('household-events.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<!-- Event Information Card -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Event Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Event Type:</strong> 
                    <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $householdEvent->event_type)) }}</span>
                </p>
                <p><strong>Event Date:</strong> {{ $householdEvent->event_date->format('F d, Y') }}</p>
                <p><strong>Reason:</strong> {{ ucfirst($householdEvent->reason) }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Household:</strong> 
                    <a href="{{ route('households.show', $householdEvent->household) }}">
                        {{ $householdEvent->household->household_id }}
                    </a>
                </p>
                <p><strong>Processed By:</strong> {{ $householdEvent->processor->name ?? 'N/A' }}</p>
                <p><strong>Recorded On:</strong> {{ $householdEvent->created_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Event Description -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> Description</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $householdEvent->description ?? 'No description provided.' }}</p>
    </div>
</div>

<!-- Additional Notes -->
@if($householdEvent->notes)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-sticky"></i> Notes</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $householdEvent->notes }}</p>
    </div>
</div>
@endif

<!-- Head Change Details -->
@if($householdEvent->event_type === 'head_change')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Head Change Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-danger">Previous Head</h6>
                @if($householdEvent->oldHead)
                    <p><strong>Name:</strong> {{ $householdEvent->oldHead->full_name }}</p>
                    <p><strong>ID:</strong> {{ $householdEvent->oldHead->resident_id }}</p>
                @else
                    <p class="text-muted">No information</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="text-success">New Head</h6>
                @if($householdEvent->newHead)
                    <p><strong>Name:</strong> {{ $householdEvent->newHead->full_name }}</p>
                    <p><strong>ID:</strong> {{ $householdEvent->newHead->resident_id }}</p>
                @else
                    <p class="text-muted">No information</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endsection