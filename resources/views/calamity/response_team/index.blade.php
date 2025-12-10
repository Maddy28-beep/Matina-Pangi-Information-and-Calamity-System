@extends('layouts.app')

@section('title', 'Response Team')

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
    <li class="breadcrumb-item active" aria-current="page">Response Team</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-people-fill"></i> Response Team</h2>
  <a href="{{ route('web.response-team-members.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

@php
  $calamities = \App\Models\Calamity::orderByDesc('date_occurred')->get();
  $query = \App\Models\ResponseTeamMember::with(['calamity','evacuationCenter']);
  if (request('search')) { $s = request('search'); $query->where(function($q) use($s){ $q->where('name','like',"%{$s}%")->orWhere('role','like',"%{$s}%"); }); }
  if (request('role')) { $query->where('role','like',"%".request('role')."%"); }
  if (request('calamity_id')) { $query->where('calamity_id', request('calamity_id')); }
  $members = $query->latest()->paginate(20);
@endphp

<form method="GET" action="{{ route('web.response-team-members.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Response Team</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Search</label>
        <input type="text" name="search" class="form-control" placeholder="Name or role" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Role</label>
        <input type="text" name="role" class="form-control" value="{{ request('role') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Calamity</label>
        <select name="calamity_id" class="form-select">
          <option value="">All</option>
          @foreach($calamities as $c)
            <option value="{{ $c->id }}" {{ request('calamity_id')==$c->id?'selected':'' }}>{{ $c->calamity_name }} ({{ $c->date_occurred }})</option>
          @endforeach
        </select>
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.response-team-members.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

 

<div class="card">
  <div class="card-body">
    @if($members->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Response Team Members">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Name') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Role') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Skills') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Assigned Center') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Rescues') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Households Helped') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($members as $m)
          <tr class="clickable-row" style="cursor: pointer;" data-href="{{ route('web.response-team-members.show', $m->id) }}">
            <td><strong class="text-dark">{{ $m->name }}</strong></td>
            <td><span class="badge bg-info">{{ $m->role }}</span></td>
            <td>{{ is_array($m->skills) ? implode(', ', $m->skills) : $m->skills }}</td>
            <td>{{ optional($m->evacuationCenter)->name }}</td>
            <td>{{ $m->rescueOperations()->count() }}</td>
            <td>{{ $m->rescueOperations()->distinct('calamity_affected_household_id')->count('calamity_affected_household_id') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $members->withQueryString()->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-people-fill" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No response team members found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
