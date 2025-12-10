@extends('layouts.app')

@section('title', 'Center Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('evacuation-centers.index') }}">Evacuation Centers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Name</div><div>{{ $evacuation_center->name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Location</div><div>{{ $evacuation_center->location }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Capacity</div><div><span class="badge bg-info">{{ $evacuation_center->capacity }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Current Occupancy</div><div><span class="badge bg-{{ ($evacuation_center->current_occupancy ?? 0) > ($evacuation_center->capacity ?? 0) ? 'danger' : 'success' }}">{{ $evacuation_center->current_occupancy }}</span></div></div>
      <div class="col-md-8"><div class="fw-semibold">Facilities</div><div>{{ is_array($evacuation_center->facilities) ? implode(', ', $evacuation_center->facilities) : $evacuation_center->facilities }}</div></div>
    </div>
  </div>
</div>
</div>
@endsection