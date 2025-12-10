@extends('layouts.app')

@section('title', 'Damage Assessment')

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
    <li class="breadcrumb-item active" aria-current="page">Damage Assessment</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-clipboard-check"></i> Damage Assessment</h2>
  <a href="{{ route('web.damage-assessments.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<form method="GET" action="{{ route('web.damage-assessments.index') }}" class="card mb-4 filter-inline">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Damage Assessments</h6>
    </div>
    <div class="border-top mb-3"></div>
    <div class="filter-grid-3">
      <div>
        <label class="form-label small fw-semibold text-uppercase">Calamity</label>
        <select name="calamity_id" class="form-select">
          <option value="">All</option>
          @isset($calamities)
          @foreach($calamities as $c)
            <option value="{{ $c->id }}" {{ request('calamity_id') == $c->id ? 'selected' : '' }}>
              {{ $c->calamity_name }}
            </option>
          @endforeach
          @endisset
        </select>
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Damage level</label>
        <select name="damage_level" class="form-select">
          <option value="">All</option>
          <option value="minor">Minor</option>
          <option value="moderate">Moderate</option>
          <option value="severe">Severe</option>
          <option value="total">Total</option>
        </select>
      </div>
      <div>
        <label class="form-label small fw-semibold text-uppercase">Assessed date</label>
        <input type="date" name="assessed_at" class="form-control" value="{{ request('assessed_at') }}">
      </div>
      <div class="actions d-flex gap-2" style="justify-self:end">
        <button type="submit" class="btn btn-filter-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('web.damage-assessments.index') }}" class="btn btn-filter-outline"><i class="bi bi-x-circle"></i> Clear</a>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if(isset($assessments) && $assessments->count())
    <div class="table-responsive">
      <table class="table table-hover ds-table sortable-table" role="grid" aria-label="Damage Assessments">
        <thead class="table-light">
          <tr>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Calamity') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Household') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Damage Level') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Estimated Cost') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Assessed At') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Assessor') }}</th>
            <th scope="col" role="columnheader" aria-sort="none">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($assessments as $a)
          <tr class="clickable-row" style="cursor: pointer;" data-href="{{ route('web.damage-assessments.show', $a->id) }}">
            <td>{{ optional($a->calamity)->calamity_name }}</td>
            <td>{{ optional($a->household)->household_id }}</td>
            <td><span class="badge bg-{{ in_array($a->damage_level,['severe','total'])?'danger':($a->damage_level==='moderate'?'warning':'success') }}">{{ ucfirst($a->damage_level) }}</span></td>
            <td><span class="badge bg-info">{{ number_format($a->estimated_cost,2) }}</span></td>
            <td>{{ optional($a->assessed_at)->format('Y-m-d') }}</td>
            <td>{{ optional($a->assessor)->name }}</td>
            <td onclick="event.stopPropagation()">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('web.damage-assessments.edit', $a->id) }}" class="btn btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <form action="{{ route('web.damage-assessments.destroy', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this assessment?')">
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
    <div class="mt-3">{{ $assessments->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-clipboard-check" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No assessments found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
