@extends('layouts.app')

@section('title', 'Relief Inventory')

@section('content')<div class="ds-page">
@push('styles')
<style>
  .filter-inline .form-control, .filter-inline .form-select { min-height: 44px; border-radius: 12px; }
  .filter-inline .btn { min-height: 44px; border-radius: 12px; }
  .btn-filter-primary { background:#2f6b45; border-color:#2f6b45; color:#fff; }
  .btn-filter-outline { border-color:#2f6b45; color:#2f6b45; }
  .filter-grid-3 { display:grid; grid-template-columns: minmax(0,2fr) minmax(0,1fr) minmax(0,1fr) auto; gap:16px; align-items:end; }
  @media (max-width: 992px) { .filter-grid-3 { grid-template-columns: minmax(0,1fr) minmax(0,1fr) auto; } }
  @media (max-width: 576px) { .filter-grid-3 { grid-template-columns: 1fr; } }
</style>
@endpush
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Relief Inventory</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-box-seam"></i> Relief Inventory</h2>
  <a href="{{ route('web.relief-items.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<form method="GET" action="{{ route('web.relief-items.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Relief Inventory</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Item or category</label>
        <input type="text" name="search" class="form-control" placeholder="Item name or category" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Category</label>
        <select name="category" class="form-select">
          <option value="">All</option>
          <option value="food">Food</option>
          <option value="water">Water</option>
          <option value="blanket">Blanket</option>
          <option value="medicine">Medicine</option>
          <option value="clothes">Clothes</option>
        </select>
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Min quantity</label>
        <input type="number" name="min_qty" class="form-control" placeholder="Min Qty" value="{{ request('min_qty') }}">
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.relief-items.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($items) && $items->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Relief Inventory">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Item') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Category') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Quantity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
          <tr class="clickable-row" style="cursor: pointer;" data-href="{{ route('web.relief-items.show', $item->id) }}">
            <td><strong class="text-dark">{{ $item->name }}</strong></td>
            <td><span class="badge bg-info">{{ ucfirst($item->category) }}</span></td>
            <td><span class="badge bg-success">{{ $item->quantity }}</span></td>
            <td onclick="event.stopPropagation()">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('web.relief-items.edit', $item->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <form action="{{ route('web.relief-items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this item?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-success" title="Archive">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-box-seam" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No inventory items found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
