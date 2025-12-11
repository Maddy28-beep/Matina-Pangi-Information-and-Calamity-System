@extends('layouts.app')

@section('title', 'Relief Distribution')

@section('content')<div class="ds-page">
@php
  $itemsList = \App\Models\ReliefItem::orderBy('name')->get();
  $distQuery = \App\Models\ReliefDistribution::with(['calamity','household','item','staff']);
  if (request('search')) {
    $s = request('search');
    $distQuery->where(function($q) use ($s){
      $q->whereHas('household', function($h) use ($s){
            $h->where('household_id','like',"%$s%");
        })
        ->orWhereHas('item', function($i) use ($s){
            $i->where('name','like',"%$s%");
        });
    });
  }
  if (request('date')) {
    $distQuery->whereDate('distributed_at', request('date'));
  }
  if (request('item')) {
    $distQuery->whereHas('item', function($i){
        $i->where('id', request('item'));
    });
  }
  $distributions = $distQuery->latest('distributed_at')->paginate(15)->appends(request()->query());
@endphp
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
    <li class="breadcrumb-item active" aria-current="page">Relief Distribution</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-truck"></i> Relief Distribution</h2>
  <a href="{{ route('web.relief-distributions.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<form method="GET" action="{{ route('web.relief-distributions.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Distributions</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Household or item</label>
        <input type="text" name="search" class="form-control" placeholder="Household ID or item name" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Date</label>
        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Item</label>
        <select name="item" class="form-select">
          <option value="">All</option>
          @foreach($itemsList as $it)
            <option value="{{ $it->id }}" {{ request('item')==$it->id?'selected':'' }}>{{ $it->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.relief-distributions.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($distributions) && $distributions->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Relief Distributions">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Calamity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Household') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Item') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Quantity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Date') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Staff') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($distributions as $d)
          <tr>
            <td>{{ optional($d->calamity)->calamity_name }}</td>
            <td>{{ optional($d->household)->household_id }}</td>
            <td>{{ optional($d->item)->name }}</td>
            <td><span class="badge bg-success">{{ $d->quantity }}</span></td>
            <td>{{ optional($d->distributed_at)->format('Y-m-d') }}</td>
            <td>{{ optional($d->staff)->name }}</td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $distributions->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-truck" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No distributions found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
