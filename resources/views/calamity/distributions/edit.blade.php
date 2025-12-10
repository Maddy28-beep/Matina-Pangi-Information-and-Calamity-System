@extends('layouts.app')

@section('title', 'Edit Relief Distribution')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('relief-distributions.index') }}">Relief Distribution</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('relief-distributions.update', $relief_distribution) }}">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select"></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Household</label>
          <select name="household_id" class="form-select" required></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Item</label>
          <select name="relief_item_id" class="form-select" required></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', $relief_distribution->quantity) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Date Distributed</label>
          <input type="date" name="distributed_at" class="form-control" value="{{ old('distributed_at', optional($relief_distribution->distributed_at)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Staff</label>
          <select name="staff_in_charge" class="form-select"></select>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('relief-distributions.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection