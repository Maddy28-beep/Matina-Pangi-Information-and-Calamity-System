@extends('layouts.app')

@section('title', 'Edit Notification')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.notifications.index') }}">Notifications</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('web.notifications.update', $notification) }}">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required value="{{ old('title', $notification->title) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Type</label>
          @php $type = old('type', $notification->type); @endphp
          <select name="type" class="form-select" required>
            <option value="sms" {{ $type=='sms'?'selected':'' }}>SMS</option>
            <option value="email" {{ $type=='email'?'selected':'' }}>Email</option>
            <option value="system" {{ $type=='system'?'selected':'' }}>System</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4">{{ old('message', $notification->message) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Target</label>
          <input type="text" name="target" class="form-control" placeholder="Purok / Household / General" value="{{ old('target') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select">
            <option value="">Select Calamity</option>
            @foreach($calamities as $c)
              <option value="{{ $c->id }}" {{ (old('calamity_id', $notification->calamity_id)==$c->id)?'selected':'' }}>
                {{ $c->calamity_name ?? ucfirst($c->calamity_type) }}
                {{ $c->date_occurred ? ' • '.$c->date_occurred->format('Y-m-d') : '' }}
                {{ $c->severity_level ? ' • '.$c->severity_level : '' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('web.notifications.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection