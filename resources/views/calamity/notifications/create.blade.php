@extends('layouts.app')

@section('title', 'Add Notification')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.notifications.index') }}">Notifications</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('web.notifications.store') }}" id="notificationForm">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="Enter notification title">
        </div>
        <div class="col-md-6">
          <label class="form-label">Type</label>
          <select name="type" class="form-select" required>
            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select type</option>
            <option value="sms" {{ old('type')==='sms' ? 'selected' : '' }}>SMS</option>
            <option value="email" {{ old('type')==='email' ? 'selected' : '' }}>Email</option>
            <option value="system" {{ old('type')==='system' ? 'selected' : '' }}>System</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4" placeholder="Write notification message">{{ old('message') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Target</label>
          <select name="target" id="target" class="form-select">
            <option value="" disabled {{ old('target') ? '' : 'selected' }}>Select target</option>
            <option value="Barangay Wide" {{ old('target')==='Barangay Wide' ? 'selected' : '' }}>Barangay Wide</option>
            @for($i=1;$i<=10;$i++)
              <option value="Purok {{ $i }}" {{ old('target')==="Purok $i" ? 'selected' : '' }}>Purok {{ $i }}</option>
            @endfor
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" id="calamity_id" class="form-select">
            <option value="">Select Calamity</option>
            @if(isset($calamities) && $calamities->count())
              @foreach($calamities as $c)
                <option value="{{ $c->id }}" {{ old('calamity_id')==$c->id ? 'selected' : '' }}>
                  {{ $c->calamity_name ?? ucfirst($c->calamity_type) ?? 'Calamity #'.$c->id }}
                  {{ $c->date_occurred ? ' • '.$c->date_occurred->format('Y-m-d') : '' }}
                  {{ $c->severity_level ? ' • '.$c->severity_level : '' }}
                  {{ $c->status ? ' • '.$c->status : '' }}
                </option>
              @endforeach
            @else
              <option value="" disabled>No calamities found</option>
            @endif
          </select>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('web.notifications.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>
</div>
<script>
// No JS population needed; calamities are rendered server-side
</script>
@endsection