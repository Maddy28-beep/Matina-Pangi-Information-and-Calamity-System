@extends('layouts.app')

@section('title', 'Distribution Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('relief-distributions.index') }}">Relief Distribution</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Calamity</div><div>{{ optional($relief_distribution->calamity)->calamity_name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Household</div><div>{{ optional($relief_distribution->household)->household_id }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Item</div><div>{{ optional($relief_distribution->item)->name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Quantity</div><div><span class="badge bg-success">{{ $relief_distribution->quantity }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Date</div><div>{{ optional($relief_distribution->distributed_at)->format('Y-m-d') }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Staff</div><div>{{ optional($relief_distribution->staff)->name }}</div></div>
    </div>
  </div>
</div>
</div>
@endsection