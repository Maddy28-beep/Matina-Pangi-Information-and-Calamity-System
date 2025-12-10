@extends('layouts.app')

@section('title', 'Add Calamity Incident')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calamities.index') }}">Calamity Incidents</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('calamities.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="calamity_name" class="form-control" required value="{{ old('calamity_name') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Type</label>
          <select name="calamity_type" class="form-select" required>
            <option value="typhoon">Typhoon</option>
            <option value="flood">Flood</option>
            <option value="earthquake">Earthquake</option>
            <option value="fire">Fire</option>
            <option value="landslide">Landslide</option>
            <option value="drought">Drought</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Date</label>
          <input type="date" name="date_occurred" class="form-control" required value="{{ old('date_occurred') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Time</label>
          <input type="time" name="occurred_time" class="form-control" value="{{ old('occurred_time') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Severity</label>
          <select name="severity" class="form-select">
            <option value="minor">Minor</option>
            <option value="moderate">Moderate</option>
            <option value="severe">Severe</option>
            <option value="catastrophic">Catastrophic</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="ongoing">Ongoing</option>
            <option value="resolved">Resolved</option>
            <option value="monitoring">Monitoring</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Affected Areas</label>
          <input type="text" name="affected_areas" class="form-control" value="{{ old('affected_areas') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Affected Puroks</label>
          <select name="affected_puroks[]" class="form-select" multiple></select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Response Actions</label>
          <textarea name="response_actions" class="form-control" rows="3">{{ old('response_actions') }}</textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('calamities.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
 </div>
 </div>
@endsection
