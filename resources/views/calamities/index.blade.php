@extends('layouts.app')

@section('title', 'Calamity Management - Barangay Matina Pangi')

@push('styles')
<style>
/* Clickable Table Rows */
.clickable-row {
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.clickable-row:hover {
    background-color: rgba(74, 111, 82, 0.06) !important;
}

.clickable-row td {
    vertical-align: middle;
}

.table thead th {
    background-color: #4A6F52;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8125rem;
    letter-spacing: 0.5px;
    border: none;
}

.btn-outline-primary {
    border-color: #4A6F52 !important;
    color: #4A6F52 !important;
}

.btn-outline-primary:hover {
    background-color: #4A6F52 !important;
    color: white !important;
}
</style>
@endpush

@section('content')
<div class="ds-page" data-search-scope>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Calamity Management</h2>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('calamities.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Record Calamity
        </a>
        
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('calamities.export.pdf', request()->query()) }}">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('calamities.export.excel', request()->query()) }}">
                        <i class="bi bi-file-excel"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('calamities.archived') }}" class="btn btn-outline-secondary">
            <i class="bi bi-archive"></i> Archived Incidents
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('calamities.index') }}" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" 
                       placeholder="Search calamities by name, type, or affected area...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('calamities.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('calamities.index') }}">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        @foreach(['typhoon','flood','earthquake','fire','landslide','drought','other'] as $t)
                            <option value="{{ $t }}" {{ ($filters['type'] ?? '') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select">
                        <option value="">All</option>
                        @foreach(['minor','moderate','severe','catastrophic'] as $s)
                            <option value="{{ $s }}" {{ ($filters['severity'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Affected Area / Purok</label>
                    <input type="text" name="area" class="form-control" value="{{ $filters['area'] ?? '' }}" placeholder="Enter area or purok">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select">
                        @php $sort = $filters['sort'] ?? 'date_desc'; @endphp
                        <option value="date_desc" {{ $sort==='date_desc' ? 'selected' : '' }}>Date (Newest)</option>
                        <option value="date_asc" {{ $sort==='date_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                        <option value="type_asc" {{ $sort==='type_asc' ? 'selected' : '' }}>Type (A→Z)</option>
                        <option value="type_desc" {{ $sort==='type_desc' ? 'selected' : '' }}>Type (Z→A)</option>
                        <option value="severity_asc" {{ $sort==='severity_asc' ? 'selected' : '' }}>Severity (Low→High)</option>
                        <option value="severity_desc" {{ $sort==='severity_desc' ? 'selected' : '' }}>Severity (High→Low)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-filter"></i> Apply Filters</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Calamities Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Calamity Name</th>
                        <th>Type</th>
                        <th>Date Occurred</th>
                        <th>Severity</th>
                        <th>Affected Households</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calamities as $calamity)
                        <tr class="clickable-row" data-href="{{ route('calamities.show', $calamity) }}">
                            <td><strong>{{ $calamity->calamity_name }}</strong></td>
                            <td><span class="badge bg-warning">{{ ucfirst($calamity->calamity_type) }}</span></td>
                            <td>{{ $calamity->date_occurred->format('M d, Y') }}</td>
                            <td>
                                @if($calamity->severity_level == 'catastrophic')
                                    <span class="badge bg-danger">Catastrophic</span>
                                @elseif($calamity->severity_level == 'severe')
                                    <span class="badge bg-danger">Severe</span>
                                @elseif($calamity->severity_level == 'moderate')
                                    <span class="badge bg-warning">Moderate</span>
                                @else
                                    <span class="badge bg-info">Minor</span>
                                @endif
                            </td>
                            <td>{{ $calamity->affected_households_count }} households</td>
                            <td>
                                @if($calamity->status == 'ongoing')
                                    <span class="badge bg-danger">Ongoing</span>
                                @elseif($calamity->status == 'monitoring')
                                    <span class="badge bg-warning">Monitoring</span>
                                @else
                                    <span class="badge bg-success">Resolved</span>
                                @endif
                            </td>
                            <td class="text-end" onclick="event.stopPropagation()">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('calamities.edit', $calamity) }}" 
                                       class="btn btn-outline-secondary" 
                                       title="Edit"
                                       onclick="event.stopPropagation()">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-success" 
                                            title="Archive"
                                            onclick="event.stopPropagation(); if(confirm('Archive this record? You can restore it from Archived Incidents.')) { document.getElementById('archive-form-{{ $calamity->id }}').submit(); }">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </div>
                                <form id="archive-form-{{ $calamity->id }}" 
                                      method="POST" 
                                      action="{{ route('calamities.destroy', $calamity) }}" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #D1D5DB;"></i>
                                <p class="mt-2" style="font-weight: 500;">No calamity records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($calamities->hasPages())
        <div class="p-3 border-top">
            {{ $calamities->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
