@extends('layouts.app')

@section('title', 'Affected Household Details')

@push('styles')
<style>
  .affected-scope .badge { background: transparent !important; border: 0 !important; border-radius: 0 !important; padding: 0 !important; font-weight: 600; box-shadow: none !important; }
  .affected-scope .badge.bg-success { color: var(--success) !important; }
  .affected-scope .badge.bg-warning { color: var(--warning) !important; }
  .affected-scope .badge.bg-info { color: var(--info) !important; }
  .affected-scope .badge.bg-danger { color: var(--danger, #dc3545) !important; }
  .affected-scope .badge.bg-secondary { color: var(--gray-600, #6c757d) !important; }
  .affected-scope .badge.bg-light { color: #374151 !important; }
</style>
@endpush

@section('content')
<div class="section-offset affected-scope">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.calamity-affected-households.index') }}">Affected Households</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Household</div><div>{{ optional($calamity_affected_household->household)->household_id }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Damage Level</div><div><span class="badge bg-{{ in_array($calamity_affected_household->damage_level,['severe','total'])?'danger':($calamity_affected_household->damage_level==='moderate'?'warning':'success') }}">{{ ucfirst($calamity_affected_household->damage_level) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Evacuation</div><div><span class="badge bg-{{ $calamity_affected_household->evacuation_status==='evacuated'?'info':($calamity_affected_household->evacuation_status==='returned'?'success':'secondary') }}">{{ ucfirst(str_replace('_',' ',$calamity_affected_household->evacuation_status)) }}</span></div></div>
      <div class="col-md-3"><div class="fw-semibold">Casualties</div><div><span class="badge bg-danger">{{ $calamity_affected_household->casualties }}</span></div></div>
      <div class="col-md-3"><div class="fw-semibold">Injured</div><div><span class="badge bg-warning">{{ $calamity_affected_household->injured }}</span></div></div>
      <div class="col-md-3"><div class="fw-semibold">Missing</div><div><span class="badge bg-secondary">{{ $calamity_affected_household->missing }}</span></div></div>
      <div class="col-md-3"><div class="fw-semibold">Relief Received</div><div><span class="badge bg-{{ $calamity_affected_household->relief_received?'success':'secondary' }}">{{ $calamity_affected_household->relief_received?'Yes':'No' }}</span></div></div>
      <div class="col-md-6"><div class="fw-semibold">Needs</div><div>{{ $calamity_affected_household->needs }}</div></div>
      <div class="col-md-6"><div class="fw-semibold">Relief Items</div><div>{{ is_array($calamity_affected_household->relief_items) ? implode(', ', $calamity_affected_household->relief_items) : $calamity_affected_household->relief_items }}</div></div>
    </div>
  </div>
</div>

@php
  $rescues = $calamity_affected_household->rescueOperations()->with(['rescuer','evacuationCenter'])->orderByDesc('rescue_time')->get();
  $responders = \App\Models\ResponseTeamMember::orderBy('name')->get();
  $centers = \App\Models\EvacuationCenter::orderBy('name')->get();
  $responderNames = $rescues->pluck('rescuer.name')->filter()->unique()->values();
@endphp

<div class="card" id="rescue-operations">
  <div class="card-header">
    @php $shouldShowForm = ($rescues->count() === 0) || ($errors->any()); @endphp
    <div class="d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Rescue Operations</span>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary">{{ $rescues->count() }}</span>
        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addRescueFormCollapse" aria-expanded="{{ $shouldShowForm ? 'true' : 'false' }}" aria-controls="addRescueFormCollapse">
          <i class="bi bi-plus-circle"></i> Add Rescue
        </button>
      </div>
    </div>
  </div>
  <div class="card-body">
    @if($responderNames->count())
      <div class="mb-3">
        <div class="fw-semibold mb-1">Responders</div>
        <div class="d-flex flex-wrap gap-2">
          @foreach($responderNames as $n)
            <span class="badge bg-light text-dark">{{ $n }}</span>
          @endforeach
        </div>
      </div>
    @endif
    @if($rescues->count())
      <div class="table-responsive mb-4">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Rescue Time</th>
              <th>Rescued By</th>
              <th>Ambulance</th>
              <th>Evacuation Center</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rescues as $r)
              <tr>
                <td>{{ optional($r->rescue_time)->format('Y-m-d H:i') }}</td>
                <td>
                  @if($r->rescuer?->name)
                    <a href="{{ route('response-team-members.index') }}?search={{ urlencode($r->rescuer->name) }}" class="text-decoration-none">{{ $r->rescuer->name }}</a>
                  @else
                    {{ ucfirst($r->rescuer_type ?? 'n/a') }}
                  @endif
                </td>
                <td>{{ $r->ambulance_vehicle ?? 'N/A' }}</td>
                <td>{{ $r->evacuationCenter?->name ?? 'N/A' }}</td>
                <td>{{ $r->notes }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-muted mb-3">No rescue records yet.</div>
    @endif

    <div id="addRescueFormCollapse" class="collapse {{ $shouldShowForm ? 'show' : '' }}">
      <form method="POST" action="{{ route('web.rescue-operations.store') }}" class="row g-3 mt-3">
        @csrf
        <input type="hidden" name="calamity_affected_household_id" value="{{ $calamity_affected_household->id }}">
        <div class="col-md-4">
          <label class="form-label">Rescuer Type</label>
          <select name="rescuer_type" class="form-select">
            <option value="">Select Type</option>
            <option value="response_team_member">Barangay Responder</option>
            <option value="ambulance_team">Ambulance Team</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Rescued By</label>
          <select name="rescuer_id" class="form-select">
            <option value="">Select Responder</option>
            @foreach($responders as $rm)
              <option value="{{ $rm->id }}">{{ $rm->name }} {{ $rm->role ? '• '.$rm->role : '' }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Rescue Time</label>
          <input type="datetime-local" name="rescue_time" class="form-control" required>
        </div>
        <div class="col-md-5">
          <label class="form-label">Ambulance Used</label>
          <input type="text" name="ambulance_vehicle" class="form-control" placeholder="e.g., Ambulance Unit 01 / Plate XYZ-123">
        </div>
        <div class="col-md-4">
          <label class="form-label">Evacuation Center Destination</label>
          <select name="evacuation_center_id" class="form-select">
            <option value="">None</option>
            @foreach($centers as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-12">
          <label class="form-label">Notes</label>
          <input type="text" name="notes" class="form-control" placeholder="Optional notes">
        </div>
        <div class="col-12 d-flex justify-content-end">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Record Rescue</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
@endsection
