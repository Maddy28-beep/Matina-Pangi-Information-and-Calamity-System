@extends('layouts.app')

@section('title', 'Edit Affected Household')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calamity-affected-households.index') }}">Affected Households</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    @php
      $calamities = \App\Models\Calamity::orderBy('date_occurred','desc')->get();
      $households = \App\Models\Household::approved()->with('head')->orderBy('household_id')->get();
    @endphp
    <form method="POST" action="{{ route('calamity-affected-households.update', $calamity_affected_household) }}">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select" required>
            <option value="" disabled>Select Calamity</option>
            @foreach($calamities as $c)
              <option value="{{ $c->id }}" {{ (old('calamity_id',$calamity_affected_household->calamity_id)==$c->id)?'selected':'' }}>
                {{ $c->calamity_name ?? ucfirst($c->calamity_type) }} {{ $c->date_occurred ? '• '.$c->date_occurred->format('Y-m-d') : '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Household</label>
          <select name="household_id" class="form-select" required>
            <option value="" disabled>Select Household</option>
            @foreach($households as $hh)
              <option value="{{ $hh->id }}" {{ (old('household_id',$calamity_affected_household->household_id)==$hh->id)?'selected':'' }}>
                {{ $hh->household_id }} {{ $hh->head?->full_name ? '• '.$hh->head->full_name : '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Damage Level</label>
          @php $dl = old('damage_level', $calamity_affected_household->damage_level); @endphp
          <select name="damage_level" class="form-select" required>
            <option value="minor" {{ $dl=='minor'?'selected':'' }}>Minor</option>
            <option value="moderate" {{ $dl=='moderate'?'selected':'' }}>Moderate</option>
            <option value="severe" {{ $dl=='severe'?'selected':'' }}>Severe</option>
            <option value="total" {{ $dl=='total'?'selected':'' }}>Total</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Injuries</label>
          <input type="number" name="injured" class="form-control" min="0" value="{{ old('injured', $calamity_affected_household->injured) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Missing</label>
          <input type="number" name="missing" class="form-control" min="0" value="{{ old('missing', $calamity_affected_household->missing) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Evacuation Status</label>
          @php $es = old('evacuation_status', $calamity_affected_household->evacuation_status); @endphp
          <select name="evacuation_status" class="form-select">
            <option value="in_home" {{ $es=='in_home'?'selected':'' }}>In Home</option>
            <option value="evacuated" {{ $es=='evacuated'?'selected':'' }}>Evacuated</option>
            <option value="returned" {{ $es=='returned'?'selected':'' }}>Returned</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Damage Cost</label>
          <input type="number" step="0.01" name="house_damage_cost" class="form-control" value="{{ old('house_damage_cost', $calamity_affected_household->house_damage_cost) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Needs Temporary Shelter</label>
          @php $nts = old('needs_temporary_shelter', $calamity_affected_household->needs_temporary_shelter?1:0); @endphp
          <select name="needs_temporary_shelter" class="form-select">
            <option value="0" {{ $nts==0?'selected':'' }}>No</option>
            <option value="1" {{ $nts==1?'selected':'' }}>Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Relief Received</label>
          @php $rr = old('relief_received', $calamity_affected_household->relief_received?1:0); @endphp
          <select name="relief_received" class="form-select">
            <option value="0" {{ $rr==0?'selected':'' }}>No</option>
            <option value="1" {{ $rr==1?'selected':'' }}>Yes</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Items</label>
          <input type="text" name="relief_items[]" class="form-control" value="">
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Date</label>
          <input type="date" name="relief_date" class="form-control" value="{{ old('relief_date', optional($calamity_affected_household->relief_date)->format('Y-m-d')) }}">
        </div>
        <div class="col-12">
          <label class="form-label">Needs</label>
          <textarea name="needs" class="form-control" rows="3">{{ old('needs', $calamity_affected_household->needs) }}</textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('calamity-affected-households.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection