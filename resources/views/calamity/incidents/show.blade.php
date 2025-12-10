@extends('layouts.app')

@section('title', 'Incident Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calamities.index') }}">Calamity Incidents</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Name</div><div>{{ $calamity->calamity_name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Type</div><div><span class="badge bg-info">{{ ucfirst($calamity->calamity_type) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Status</div><div><span class="badge bg-{{ $calamity->status==='ongoing'?'warning':($calamity->status==='resolved'?'success':'secondary') }}">{{ ucfirst($calamity->status) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Date</div><div>{{ $calamity->date_occurred?->format('Y-m-d') }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Time</div><div>{{ $calamity->occurred_time }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Severity</div><div>@php $sev=$calamity->severity ?? $calamity->severity_level; $color=$sev==='severe'||$sev==='catastrophic'?'danger':($sev==='moderate'?'warning':'success'); @endphp <span class="badge bg-{{ $color }}">{{ ucfirst($sev) }}</span></div></div>
      <div class="col-md-6"><div class="fw-semibold">Affected Areas</div><div>{{ $calamity->affected_areas }}</div></div>
      <div class="col-md-6"><div class="fw-semibold">Description</div><div>{{ $calamity->description }}</div></div>
    </div>
  </div>
</div>

@if($calamity->affectedHouseholds?->count())
<div class="card">
  <div class="card-header bg-light">Affected Households</div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>Household</th>
            <th>Damage Level</th>
            <th>Casualties</th>
            <th>Injured</th>
            <th>Missing</th>
            <th>Relief Received</th>
          </tr>
        </thead>
        <tbody>
          @foreach($calamity->affectedHouseholds as $ah)
          <tr>
            <td>{{ optional($ah->household)->household_id }}</td>
            <td><span class="badge bg-{{ in_array($ah->damage_level,['severe','total'])?'danger':($ah->damage_level==='moderate'?'warning':'success') }}">{{ ucfirst($ah->damage_level) }}</span></td>
            <td><span class="badge bg-danger">{{ $ah->casualties }}</span></td>
            <td><span class="badge bg-warning">{{ $ah->injured }}</span></td>
            <td><span class="badge bg-secondary">{{ $ah->missing }}</span></td>
            <td><span class="badge bg-{{ $ah->relief_received?'success':'secondary' }}">{{ $ah->relief_received?'Yes':'No' }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
 </div>
@endif
</div>
@endsection