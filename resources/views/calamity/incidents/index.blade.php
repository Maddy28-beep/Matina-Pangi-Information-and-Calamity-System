@extends('layouts.app')

@section('title', 'Calamity Incidents')

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
    <li class="breadcrumb-item active" aria-current="page">Calamity Incidents</li>
  </ol>
</nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-lightning-fill"></i> Calamity Incidents</h2>
    <a href="{{ route('calamities.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
  </div>

<form method="GET" action="{{ route('calamities.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Incidents</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div class="flex-grow-1">
        <label class="form-label small fw-semibold text-uppercase">Name, type, area, description</label>
        <input type="text" name="search" class="form-control" placeholder="Name, type, affected areas, or description" value="{{ request('search') }}">
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Type</label>
        <select name="type" class="form-select">
          <option value="">All</option>
          <option value="typhoon" {{ request('type')=='typhoon'?'selected':'' }}>Typhoon</option>
          <option value="flood" {{ request('type')=='flood'?'selected':'' }}>Flood</option>
          <option value="earthquake" {{ request('type')=='earthquake'?'selected':'' }}>Earthquake</option>
          <option value="fire" {{ request('type')=='fire'?'selected':'' }}>Fire</option>
          <option value="landslide" {{ request('type')=='landslide'?'selected':'' }}>Landslide</option>
          <option value="drought" {{ request('type')=='drought'?'selected':'' }}>Drought</option>
          <option value="other" {{ request('type')=='other'?'selected':'' }}>Other</option>
        </select>
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          <option value="ongoing" {{ request('status')=='ongoing'?'selected':'' }}>Ongoing</option>
          <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
          <option value="monitoring" {{ request('status')=='monitoring'?'selected':'' }}>Monitoring</option>
        </select>
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('calamities.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($calamities) && $calamities->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Calamity Incidents">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Name') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Type') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Date') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Time') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Severity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Status') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Affected Areas') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($calamities as $calamity)
          <tr class="clickable-row" style="cursor: pointer;" data-href="{{ route('calamities.show', $calamity->id) }}">
            <td><strong class="text-dark">{{ $calamity->calamity_name }}</strong></td>
            <td><span class="badge bg-info">{{ ucfirst($calamity->calamity_type) }}</span></td>
            <td>{{ $calamity->date_occurred?->format('Y-m-d') }}</td>
            <td>{{ $calamity->occurred_time }}</td>
            <td>
              @php $sev = $calamity->severity ?? $calamity->severity_level; $color = $sev==='severe'||$sev==='catastrophic'?'danger':($sev==='moderate'?'warning':'success'); @endphp
              <span class="badge bg-{{ $color }}">{{ ucfirst($sev) }}</span>
            </td>
            <td><span class="badge bg-{{ $calamity->status==='ongoing'?'warning':($calamity->status==='resolved'?'success':'secondary') }}">{{ ucfirst($calamity->status) }}</span></td>
            <td>{{ $calamity->affected_areas }}</td>
            <td onclick="event.stopPropagation()">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('calamities.report', $calamity->id) }}" class="btn btn-outline-primary" title="View Report">
                  <i class="bi bi-file-earmark-text"></i>
                </a>
                <a href="{{ route('calamities.edit', $calamity->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <form action="{{ route('calamities.destroy', $calamity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this incident? You can restore it from Archived Incidents.')">
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
    <div class="mt-3">{{ $calamities->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-lightning-fill" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No incidents found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
