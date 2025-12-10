@extends('layouts.app')

@section('title', 'Add Response Team Member')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.response-team-members.index') }}">Response Team</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    @php
      $calamities = \App\Models\Calamity::orderBy('date_occurred','desc')->get();
      $centers = \App\Models\EvacuationCenter::orderBy('name')->get();
    @endphp
    <form method="POST" action="{{ route('web.response-team-members.store') }}" id="rtForm">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Enter full name">
        </div>
        <div class="col-md-6">
          <label class="form-label">Role</label>
          <input type="text" name="role" class="form-control" value="{{ old('role') }}" placeholder="e.g., Medic, Rescuer">
        </div>
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select">
            <option value="">Unassigned</option>
            @foreach($calamities as $c)
              <option value="{{ $c->id }}">{{ $c->calamity_name ?? ucfirst($c->calamity_type) }} {{ $c->date_occurred ? '• '.$c->date_occurred->format('Y-m-d') : '' }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Evacuation Center</label>
          <select name="evacuation_center_id" class="form-select">
            <option value="">Unassigned</option>
            @foreach($centers as $ec)
              <option value="{{ $ec->id }}">{{ $ec->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Skills (comma-separated)</label>
          <input type="text" name="skills_text" class="form-control" placeholder="First aid, Search and rescue, Logistics" value="{{ old('skills_text') }}">
        </div>
        <div class="col-12">
          <label class="form-label">Assignment Notes</label>
          <textarea name="assignment_notes" rows="3" class="form-control" placeholder="Details about assignment...">{{ old('assignment_notes') }}</textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('web.response-team-members.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection