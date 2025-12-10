@extends('layouts.app')

@section('title', 'Affected Households')

{{-- removed full_width to match calamity incident layout --}}
@push('styles')
<style>
  .affected-scope .badge { background: transparent !important; border: 0 !important; border-radius: 0 !important; padding: 0 !important; font-weight: 600; box-shadow: none !important; }
  .affected-scope .badge.bg-success { color: var(--success) !important; }
  .affected-scope .badge.bg-warning { color: var(--warning) !important; }
  .affected-scope .badge.bg-info { color: var(--info) !important; }
  .affected-scope .badge.bg-danger { color: var(--danger, #dc3545) !important; }
  .affected-scope .badge.bg-secondary { color: var(--gray-600, #6c757d) !important; }
  .affected-scope .badge.bg-light { color: #374151 !important; }
  .filter-inline .form-control, .filter-inline .form-select { min-height: 44px; border-radius: 12px; }
  .filter-inline .btn { min-height: 44px; border-radius: 12px; }
  .btn-filter-primary { background:#2f6b45; border-color:#2f6b45; color:#fff; }
  .btn-filter-outline { border-color:#2f6b45; color:#2f6b45; }
  .filter-grid-3 { display:grid; grid-template-columns: minmax(0,2fr) minmax(0,1fr) minmax(0,1fr) auto; gap:16px; align-items:end; }
  @media (max-width: 992px) { .filter-grid-3 { grid-template-columns: minmax(0,1fr) minmax(0,1fr) auto; } }
</style>
@endpush

@section('content')
<div class="section-offset affected-scope">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Affected Households</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-people"></i> Affected Households</h2>
  <a href="{{ route('web.calamity-affected-households.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<form method="GET" action="{{ route('web.calamity-affected-households.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Affected Households</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Household ID or notes</label>
        <input type="text" name="search" class="form-control" placeholder="Household ID or notes" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Damage level</label>
        <select name="damage_level" class="form-select">
          <option value="">All</option>
          <option value="minor" {{ request('damage_level')=='minor'?'selected':'' }}>Minor</option>
          <option value="moderate" {{ request('damage_level')=='moderate'?'selected':'' }}>Moderate</option>
          <option value="severe" {{ request('damage_level')=='severe'?'selected':'' }}>Severe</option>
          <option value="total" {{ request('damage_level')=='total'?'selected':'' }}>Total</option>
        </select>
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Evacuation status</label>
        <select name="evacuation_status" class="form-select">
          <option value="">All</option>
          <option value="in_home" {{ request('evacuation_status')=='in_home'?'selected':'' }}>In Home</option>
          <option value="evacuated" {{ request('evacuation_status')=='evacuated'?'selected':'' }}>Evacuated</option>
          <option value="returned" {{ request('evacuation_status')=='returned'?'selected':'' }}>Returned</option>
        </select>
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.calamity-affected-households.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($affectedHouseholds) && $affectedHouseholds->count())
    <div class="table-responsive affected-table">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Affected Households">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Household') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Damage') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Casualties') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Injured') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Missing') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Evacuation') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Relief') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($affectedHouseholds as $ah)
          <tr class="clickable-row" style="cursor: pointer;" data-href="{{ route('web.calamity-affected-households.show', $ah->id) }}">
            <td>{{ optional($ah->household)->household_id }}</td>
            <td><span class="badge bg-{{ in_array($ah->damage_level,['severe','total'])?'danger':($ah->damage_level==='moderate'?'warning':'success') }}">{{ ucfirst($ah->damage_level) }}</span></td>
            <td><span class="badge bg-danger">{{ $ah->casualties }}</span></td>
            <td><span class="badge bg-warning">{{ $ah->injured }}</span></td>
            <td><span class="badge bg-secondary">{{ $ah->missing }}</span></td>
            <td><span class="badge bg-{{ $ah->evacuation_status==='evacuated'?'info':($ah->evacuation_status==='returned'?'success':'secondary') }}">{{ ucfirst(str_replace('_',' ',$ah->evacuation_status)) }}</span></td>
            <td><span class="badge bg-{{ $ah->relief_received?'success':'secondary' }}">{{ $ah->relief_received?'Yes':'No' }}</span></td>
            <td onclick="event.stopPropagation()">
              <a href="{{ route('web.calamity-affected-households.show', $ah->id) }}#rescue-operations" class="btn btn-sm btn-outline-secondary" title="Rescue Operations">
                <i class="bi bi-life-preserver"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $affectedHouseholds->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-people" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No affected households recorded.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
