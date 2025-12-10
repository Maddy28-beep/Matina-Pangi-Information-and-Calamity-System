@extends('layouts.app')

@section('title', 'Edit Calamity Incident')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calamities.index') }}">Calamity Incidents</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('calamities.update', $calamity) }}">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="calamity_name" class="form-control" required value="{{ old('calamity_name', $calamity->calamity_name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Type</label>
          <select name="calamity_type" class="form-select" required>
            @php $type = old('calamity_type', $calamity->calamity_type); @endphp
            <option value="typhoon" {{ $type=='typhoon'?'selected':'' }}>Typhoon</option>
            <option value="flood" {{ $type=='flood'?'selected':'' }}>Flood</option>
            <option value="earthquake" {{ $type=='earthquake'?'selected':'' }}>Earthquake</option>
            <option value="fire" {{ $type=='fire'?'selected':'' }}>Fire</option>
            <option value="landslide" {{ $type=='landslide'?'selected':'' }}>Landslide</option>
            <option value="drought" {{ $type=='drought'?'selected':'' }}>Drought</option>
            <option value="other" {{ $type=='other'?'selected':'' }}>Other</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Date</label>
          <input type="date" name="date_occurred" class="form-control" required value="{{ old('date_occurred', optional($calamity->date_occurred)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Time</label>
          <input type="time" name="occurred_time" class="form-control" value="{{ old('occurred_time', $calamity->occurred_time) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Severity</label>
          @php $sev = old('severity', $calamity->severity ?? $calamity->severity_level); @endphp
          <select name="severity" class="form-select">
            <option value="minor" {{ $sev=='minor'?'selected':'' }}>Minor</option>
            <option value="moderate" {{ $sev=='moderate'?'selected':'' }}>Moderate</option>
            <option value="severe" {{ $sev=='severe'?'selected':'' }}>Severe</option>
            <option value="catastrophic" {{ $sev=='catastrophic'?'selected':'' }}>Catastrophic</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          @php $status = old('status', $calamity->status); @endphp
          <select name="status" class="form-select">
            <option value="ongoing" {{ $status=='ongoing'?'selected':'' }}>Ongoing</option>
            <option value="resolved" {{ $status=='resolved'?'selected':'' }}>Resolved</option>
            <option value="monitoring" {{ $status=='monitoring'?'selected':'' }}>Monitoring</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Affected Areas</label>
          <input type="text" name="affected_areas" class="form-control" value="{{ old('affected_areas', $calamity->affected_areas) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Affected Puroks</label>
          <select name="affected_puroks[]" class="form-select" multiple></select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4">{{ old('description', $calamity->description) }}</textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Response Actions</label>
          <textarea name="response_actions" class="form-control" rows="3">{{ old('response_actions', $calamity->response_actions) }}</textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('calamities.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection