@extends('layouts.app')

@section('title', 'Household Events - ' . $household->household_id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-calendar-event"></i> Events History
        <small class="text-muted">{{ $household->household_id }}</small>
    </h2>
    <a href="{{ route('households.show', $household) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Household
    </a>
</div>

<!-- Household Info Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Household ID:</strong> {{ $household->household_id }}</p>
                <p><strong>Address:</strong> {{ $household->address }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Purok:</strong> {{ $household->purok }}</p>
                <p><strong>Total Members:</strong> {{ $household->total_members }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Events Timeline -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Event Timeline ({{ $events->total() }} events)</h5>
    </div>
    <div class="card-body">
        @forelse($events as $event)
        <div class="d-flex mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="me-3">
                <div class="rounded-circle bg-{{ 
                    $event->event_type == 'member_added' ? 'success' : 
                    ($event->event_type == 'member_removed' ? 'danger' : 
                    ($event->event_type == 'relocation' ? 'warning' : 'info'))
                }} text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi 
                        @if($event->event_type == 'member_added') bi-person-plus
                        @elseif($event->event_type == 'member_removed') bi-person-dash
                        @elseif($event->event_type == 'head_change') bi-arrow-repeat
                        @elseif($event->event_type == 'relocation') bi-geo-alt
                        @elseif($event->event_type == 'household_split') bi-scissors
                        @elseif($event->event_type == 'household_merged') bi-union
                        @elseif($event->event_type == 'new_family_created') bi-house-add
                        @else bi-calendar-event
                        @endif"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-1">
                    <span class="badge bg-{{ 
                        $event->event_type == 'member_added' ? 'success' : 
                        ($event->event_type == 'member_removed' ? 'danger' : 
                        ($event->event_type == 'relocation' ? 'warning' : 'info'))
                    }}">{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</span>
                </h6>
                <p class="mb-2">{{ $event->description }}</p>
                
                @if($event->event_type == 'head_change' && $event->oldHead && $event->newHead)
                    <div class="alert alert-info py-2 mb-2">
                        <small>
                            <strong>Old Head:</strong> {{ $event->oldHead->full_name }}<br>
                            <strong>New Head:</strong> {{ $event->newHead->full_name }}
                        </small>
                    </div>
                @endif
                
                <small class="text-muted">
                    <i class="bi bi-calendar"></i> {{ $event->event_date->format('F d, Y') }} •
                    <i class="bi bi-person"></i> {{ $event->processor->name ?? 'System' }} •
                    <i class="bi bi-tag"></i> {{ ucfirst($event->reason) }}
                </small>
                
                @if($event->notes)
                    <p class="mt-2 mb-0 bg-light p-2 rounded">
                        <small><strong>Notes:</strong> {{ $event->notes }}</small>
                    </p>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3 text-muted">No events recorded for this household yet.</p>
        </div>
        @endforelse

        @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
