@extends('layouts.app')

@section('title', 'Household Events History')

@section('content')<div class="ds-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-calendar-event"></i> Household Events History</h2>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('household-events.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search household ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="household_id" class="form-select">
                    <option value="">All Households</option>
                    @foreach($households as $h)
                        <option value="{{ $h->id }}" {{ request('household_id') == $h->id ? 'selected' : '' }}>
                            {{ $h->household_id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="event_type" class="form-select">
                    <option value="">All Event Types</option>
                    <option value="head_change" {{ request('event_type') == 'head_change' ? 'selected' : '' }}>Head Change</option>
                    <option value="member_added" {{ request('event_type') == 'member_added' ? 'selected' : '' }}>Member Added</option>
                    <option value="member_removed" {{ request('event_type') == 'member_removed' ? 'selected' : '' }}>Member Removed</option>
                    <option value="household_split" {{ request('event_type') == 'household_split' ? 'selected' : '' }}>Household Split</option>
                    <option value="household_merged" {{ request('event_type') == 'household_merged' ? 'selected' : '' }}>Household Merged</option>
                    <option value="new_family_created" {{ request('event_type') == 'new_family_created' ? 'selected' : '' }}>New Family Created</option>
                    <option value="relocation" {{ request('event_type') == 'relocation' ? 'selected' : '' }}>Relocation</option>
                    <option value="dissolution" {{ request('event_type') == 'dissolution' ? 'selected' : '' }}>Dissolution</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Events Timeline -->
<div class="card">
    <div class="card-body">
        @forelse($events as $event)
        <div class="d-flex mb-4 pb-4 border-bottom">
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
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">
                            <a href="{{ route('households.show', $event->household) }}" class="text-decoration-none">
                                {{ $event->household->household_id }}
                            </a>
                            <span class="badge bg-{{ 
                                $event->event_type == 'member_added' ? 'success' : 
                                ($event->event_type == 'member_removed' ? 'danger' : 
                                ($event->event_type == 'relocation' ? 'warning' : 'info'))
                            }}">{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</span>
                        </h5>
                        <p class="mb-2">{{ $event->description }}</p>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> {{ $event->event_date->format('F d, Y') }} •
                            <i class="bi bi-person"></i> Processed by: {{ $event->processor->name ?? 'System' }} •
                            <i class="bi bi-tag"></i> Reason: {{ ucfirst($event->reason) }}
                        </small>
                        @if($event->notes)
                            <p class="mt-2 mb-0"><small><strong>Notes:</strong> {{ $event->notes }}</small></p>
                        @endif
                    </div>
                    <a href="{{ route('household-events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3 text-muted">No household events found.</p>
        </div>
        @endforelse

        @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
