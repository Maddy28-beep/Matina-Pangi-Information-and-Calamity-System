@extends('layouts.app')

@section('title', 'Evacuation Centers')

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
    <li class="breadcrumb-item active" aria-current="page">Evacuation Centers</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-building"></i> Evacuation Centers</h2>
  <a href="{{ route('web.evacuation-centers.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<form method="GET" action="{{ route('web.evacuation-centers.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Evacuation Centers</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Center name or location</label>
        <input type="text" name="search" class="form-control" placeholder="Center name or location" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Capacity min</label>
        <input type="number" name="capacity_min" class="form-control" placeholder="Capacity Min" value="{{ request('capacity_min') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Occupancy max</label>
        <input type="number" name="occupancy_max" class="form-control" placeholder="Occupancy Max" value="{{ request('occupancy_max') }}">
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.evacuation-centers.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($centers) && $centers->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Evacuation Centers">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Name') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Location') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Capacity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Occupancy') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Facilities') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($centers as $center)
          <tr>
            <td><strong class="text-dark">{{ $center->name }}</strong></td>
            <td>{{ $center->location }}</td>
            <td><span class="badge bg-info">{{ $center->capacity }}</span></td>
            <td><span class="badge bg-{{ ($center->current_occupancy ?? 0) > ($center->capacity ?? 0) ? 'danger' : 'success' }}">{{ $center->current_occupancy }}</span></td>
            <td>{{ is_array($center->facilities) ? implode(', ', $center->facilities) : $center->facilities }}</td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $centers->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-building" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No evacuation centers found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
