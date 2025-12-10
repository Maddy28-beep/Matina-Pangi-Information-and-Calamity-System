@extends('layouts.app')

@section('title', 'Edit Relief Item')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.relief-items.index') }}">Relief Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('web.relief-items.update', $relief_item) }}">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Item Name</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name', $relief_item->name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Category</label>
          @php $cat = old('category', $relief_item->category); @endphp
          <select name="category" class="form-select" required>
            <option value="food" {{ $cat=='food'?'selected':'' }}>Food</option>
            <option value="water" {{ $cat=='water'?'selected':'' }}>Water</option>
            <option value="blanket" {{ $cat=='blanket'?'selected':'' }}>Blanket</option>
            <option value="medicine" {{ $cat=='medicine'?'selected':'' }}>Medicine</option>
            <option value="clothes" {{ $cat=='clothes'?'selected':'' }}>Clothes</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" min="0" value="{{ old('quantity', $relief_item->quantity) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit</label>
          <input type="text" name="unit" class="form-control" value="{{ old('unit') }}">
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('web.relief-items.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
