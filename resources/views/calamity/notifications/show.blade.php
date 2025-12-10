@extends('layouts.app')

@section('title', 'Notification Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.notifications.index') }}">Notifications</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Title</div><div>{{ $notification->title }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Type</div><div><span class="badge bg-info">{{ strtoupper($notification->type) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Status</div><div><span class="badge bg-{{ $notification->status==='sent'?'success':($notification->status==='failed'?'danger':'secondary') }}">{{ ucfirst($notification->status) }}</span></div></div>
      <div class="col-md-6"><div class="fw-semibold">Message</div><div>{{ $notification->message }}</div></div>
      <div class="col-md-6"><div class="fw-semibold">Calamity</div><div>{{ optional($notification->calamity)->calamity_name }}</div></div>
    </div>
  </div>
</div>
</div>
@endsection