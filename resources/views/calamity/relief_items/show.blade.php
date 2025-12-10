@extends('layouts.app')

@section('title', 'Item Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.relief-items.index') }}">Relief Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4"><div class="fw-semibold">Item</div><div>{{ $relief_item->name }}</div></div>
      <div class="col-md-4"><div class="fw-semibold">Category</div><div><span class="badge bg-info">{{ ucfirst($relief_item->category) }}</span></div></div>
      <div class="col-md-4"><div class="fw-semibold">Quantity</div><div><span class="badge bg-success">{{ $relief_item->quantity }}</span></div></div>
    </div>
  </div>
</div>
</div>
@endsection