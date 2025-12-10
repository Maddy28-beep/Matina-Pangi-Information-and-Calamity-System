@extends('layouts.app')

@section('title', 'Assessment Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.damage-assessments.index') }}">Damage Assessment</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Calamity</div><div>{{ optional($damage_assessment->calamity)->calamity_name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Household</div><div>{{ optional($damage_assessment->household)->household_id }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Damage Level</div><div><span class="badge bg-{{ in_array($damage_assessment->damage_level,['severe','total'])?'danger':($damage_assessment->damage_level==='moderate'?'warning':'success') }}">{{ ucfirst($damage_assessment->damage_level) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Estimated Cost</div><div><span class="badge bg-info">{{ number_format($damage_assessment->estimated_cost,2) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Assessed At</div><div>{{ optional($damage_assessment->assessed_at)->format('Y-m-d') }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Assessor</div><div>{{ optional($damage_assessment->assessor)->name }}</div></div>
      <div class="col-12"><div class="fw-semibold">Description</div><div>{{ $damage_assessment->description }}</div></div>
    </div>
  </div>
</div>
</div>
@endsection
