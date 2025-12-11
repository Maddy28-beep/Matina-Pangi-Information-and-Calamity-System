@extends('layouts.app')
@section('title','Calamity Report')
@section('content')<div class="ds-page">
@push('styles')
<style>
  .filter-inline .form-control, .filter-inline .form-select { min-height: 44px; border-radius: 12px; }
  .filter-inline .btn { min-height: 44px; border-radius: 12px; }
  .btn-filter-primary { background:#2f6b45; border-color:#2f6b45; color:#fff; }
  .btn-filter-outline { border-color:#2f6b45; color:#2f6b45; }
</style>
@endpush
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-file-text"></i> Calamity Reports</h2>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('calamities.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-list-ul"></i> View All Calamities
      </a>
      <div class="dropdown">
        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-download"></i> Export All
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('web.calamity-reports.export.pdf', request()->query()) }}">
              <i class="bi bi-file-pdf"></i> Export to PDF
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('web.calamity-reports.export.excel', request()->query()) }}">
              <i class="bi bi-file-excel"></i> Export to Excel
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <form method="GET" action="{{ route('web.calamity-reports.index') }}" class="card mb-4 filter-inline">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Reports</h6>
      </div>
      <div class="border-top mb-3"></div>
      <div class="d-flex flex-wrap align-items-end gap-3">
        <div class="flex-grow-1" style="min-width:240px">
          <label class="form-label small fw-semibold text-uppercase">Name, type, area, description</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, type, affected area, or description">
        </div>
        <div style="min-width:180px">
          <label class="form-label small fw-semibold text-uppercase">Type</label>
          <select name="calamity_type" class="form-select">
            <option value="">All</option>
            <option value="typhoon" {{ request('calamity_type')=='typhoon'?'selected':'' }}>Typhoon</option>
            <option value="flood" {{ request('calamity_type')=='flood'?'selected':'' }}>Flood</option>
            <option value="earthquake" {{ request('calamity_type')=='earthquake'?'selected':'' }}>Earthquake</option>
            <option value="landslide" {{ request('calamity_type')=='landslide'?'selected':'' }}>Landslide</option>
            <option value="drought" {{ request('calamity_type')=='drought'?'selected':'' }}>Drought</option>
            <option value="other" {{ request('calamity_type')=='other'?'selected':'' }}>Other</option>
          </select>
        </div>
        <div style="min-width:160px">
          <label class="form-label small fw-semibold text-uppercase">Severity</label>
          <select name="severity" class="form-select">
            <option value="">All</option>
            <option value="critical" {{ request('severity')=='critical'?'selected':'' }}>Critical</option>
            <option value="high" {{ request('severity')=='high'?'selected':'' }}>High</option>
            <option value="moderate" {{ request('severity')=='moderate'?'selected':'' }}>Moderate</option>
            <option value="low" {{ request('severity')=='low'?'selected':'' }}>Low</option>
          </select>
        </div>
        <div style="min-width:160px">
          <label class="form-label small fw-semibold text-uppercase">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="ongoing" {{ request('status')=='ongoing'?'selected':'' }}>Ongoing</option>
            <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
            <option value="monitoring" {{ request('status')=='monitoring'?'selected':'' }}>Monitoring</option>
          </select>
        </div>
        <div style="min-width:180px">
          <label class="form-label small fw-semibold text-uppercase">From</label>
          <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div style="min-width:180px">
          <label class="form-label small fw-semibold text-uppercase">To</label>
          <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="ms-auto d-flex gap-2">
          <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
          <a href="{{ route('web.calamity-reports.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
        </div>
      </div>
    </div>
  </form>
  <div class="card">
    <div class="card-body">
      @if($calamities->count())
      <div class="table-responsive">
        <table class="table table-striped ds-table sortable-table table-clickable" role="grid" aria-label="Calamity Reports">
          <thead>
            <tr>
              <th scope="col" role="columnheader" aria-sort="none">{{ __('ID') }}</th>
              <th scope="col" role="columnheader" aria-sort="none">{{ __('Name') }}</th>
              <th scope="col" role="columnheader" aria-sort="none">{{ __('Type') }}</th>
              <th scope="col" role="columnheader" aria-sort="none">{{ __('Date') }}</th>
              <th scope="col" role="columnheader" aria-sort="none">{{ __('Severity') }}</th>
              <th scope="col" role="columnheader" aria-sort="none" class="text-center">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($calamities as $c)
            <tr data-href="{{ route('web.calamity-reports.show', $c) }}">
              <td>{{ $c->id }}</td>
              <td>{{ $c->calamity_name ?? ucfirst($c->calamity_type) }}</td>
              <td>{{ ucfirst($c->calamity_type) }}</td>
              <td>{{ optional($c->date_occurred)->format('Y-m-d') }}</td>
              <td>
                @if($c->severity_level === 'critical')
                  <span class="badge bg-danger">Critical</span>
                @elseif($c->severity_level === 'high')
                  <span class="badge bg-warning">High</span>
                @elseif($c->severity_level === 'moderate')
                  <span class="badge bg-info">Moderate</span>
                @else
                  <span class="badge bg-secondary">{{ ucfirst($c->severity_level) }}</span>
                @endif
              </td>
              <td class="text-center">
                <div class="btn-group" role="group">
                  <a href="{{ route('web.calamity-reports.pdf', $c) }}" class="btn btn-sm btn-success" title="Download PDF" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                  </a>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $calamities->links() }}</div>
      @else
      <div class="text-center py-5 text-muted">No calamity records found.</div>
      @endif
    </div>
  </div>
</div>
@endsection
