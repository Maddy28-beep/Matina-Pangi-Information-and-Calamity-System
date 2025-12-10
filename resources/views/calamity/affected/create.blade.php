@extends('layouts.app')

@section('title', 'Add Affected Household')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.calamity-affected-households.index') }}">Affected Households</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    @php
      $calamities = \App\Models\Calamity::orderBy('date_occurred','desc')->get();
      $households = \App\Models\Household::approved()->with('head')->orderBy('household_id')->get();
    @endphp
    <form method="POST" action="{{ route('web.calamity-affected-households.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select" required>
            <option value="" disabled {{ old('calamity_id') ? '' : 'selected' }}>Select Calamity</option>
            @foreach($calamities as $c)
              <option value="{{ $c->id }}" {{ old('calamity_id')==$c->id ? 'selected' : '' }}>
                {{ $c->calamity_name ?? ucfirst($c->calamity_type) }} {{ $c->date_occurred ? '• '.$c->date_occurred->format('Y-m-d') : '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Household</label>
          <select name="household_id" class="form-select" required>
            <option value="" disabled {{ old('household_id') ? '' : 'selected' }}>Select Household</option>
            @foreach($households as $hh)
              <option value="{{ $hh->id }}" {{ old('household_id')==$hh->id ? 'selected' : '' }}>
                {{ $hh->household_id }} {{ $hh->head?->full_name ? '• '.$hh->head->full_name : '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Damage Level</label>
          <select name="damage_level" class="form-select" required>
            <option value="minor">Minor</option>
            <option value="moderate">Moderate</option>
            <option value="severe">Severe</option>
            <option value="total">Total</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Injuries</label>
          <input type="number" name="injured" class="form-control" min="0" value="{{ old('injured',0) }}" placeholder="Enter injured count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Missing</label>
          <input type="number" name="missing" class="form-control" min="0" value="{{ old('missing',0) }}" placeholder="Enter missing count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Deceased</label>
          <input type="number" name="casualties" class="form-control" min="0" value="{{ old('casualties',0) }}" placeholder="Enter deceased count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Evacuation Status</label>
          <select name="evacuation_status" class="form-select">
            <option value="in_home">In Home</option>
            <option value="evacuated">Evacuated</option>
            <option value="returned">Returned</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Damage Cost</label>
          <input type="number" step="0.01" name="house_damage_cost" class="form-control" value="{{ old('house_damage_cost') }}" placeholder="Enter estimated damage cost">
        </div>
        <div class="col-md-4">
          <label class="form-label">Needs Temporary Shelter</label>
          <select name="needs_temporary_shelter" class="form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Relief Received</label>
          <select name="relief_received" class="form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Items</label>
          <input type="text" name="relief_items[]" class="form-control" placeholder="Comma-separated">
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Date</label>
          <input type="date" name="relief_date" class="form-control" value="{{ old('relief_date') }}">
        </div>
        <div class="col-12">
          <label class="form-label">Needs</label>
          <textarea name="needs" class="form-control" rows="3">{{ old('needs') }}</textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('web.calamity-affected-households.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection