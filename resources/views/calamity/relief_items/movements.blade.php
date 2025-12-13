@extends('layouts.app')

@section('title', 'Inventory Movements')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.relief-items.index') }}">Relief Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Inventory Movements</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-arrow-left-right"></i> Inventory Movements</h2>
  <a href="{{ route('web.relief-items.index') }}" class="btn btn-secondary"><i class="bi bi-box-seam"></i> Back to Inventory</a>
</div>

<form method="GET" action="{{ route('web.relief-items.movements') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Movements</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small fw-semibold text-uppercase">Item</label>
        <select name="item" class="form-select">
          <option value="">All</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}" {{ request('item')==$it->id?'selected':'' }}>{{ $it->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold text-uppercase">Action</label>
        @php $act = request('action'); @endphp
        <select name="action" class="form-select">
          <option value="">All</option>
          <option value="stock_out" {{ $act=='stock_out'?'selected':'' }}>Stock Out</option>
          <option value="stock_in" {{ $act=='stock_in'?'selected':'' }}>Stock In</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold text-uppercase">From</label>
        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold text-uppercase">To</label>
        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
      </div>
    </div>
  </div>
  </form>

<div class="card">
  <div class="card-body">
    @if(isset($logs) && $logs->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Inventory Movements">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Action</th>
            <th>Quantity</th>
            <th>Distribution</th>
            <th>By</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
          @php $nv = is_array($log->new_values) ? $log->new_values : []; @endphp
          <tr>
            <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
            <td>Item #{{ $log->model_id }}</td>
            <td><span class="badge {{ $log->action=='stock_out' ? 'bg-danger' : 'bg-success' }}">{{ str_replace('_',' ', $log->action) }}</span></td>
            <td><span class="badge bg-primary">{{ $nv['quantity'] ?? '—' }}</span></td>
            <td>{{ $nv['distribution_id'] ?? '—' }}</td>
            <td>{{ optional($log->user)->name }}</td>
            <td>{{ $log->description }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $logs->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-arrow-left-right" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No movements found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
