@extends('layouts.app')

@section('title', 'Census')

@section('content')<div class="ds-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-bar-chart"></i> Barangay Census Report</h2>
        <div class="d-flex align-items-center gap-2">
            <div class="card shadow-sm" style="border-left: 4px solid #16a34a;">
                <div class="card-body py-2 px-3">
                    <small class="text-muted">Selected Year:</small>
                    <strong>{{ $year ?? now()->year }}</strong>
                </div>
            </div>
            @if(auth()->user()->isSecretary() || auth()->user()->isStaff())
            <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Export Census
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ auth()->user()->isSecretary() ? route('census.export.pdf') : route('staff.census.export.pdf') }}">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ auth()->user()->isSecretary() ? route('census.export.excel') : route('staff.census.export.excel') }}">
                            <i class="bi bi-file-excel"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ url()->current() }}" id="censusYearForm" class="d-flex align-items-center gap-2 mb-3">
        @php $startYear = now()->year - 10; $endYear = now()->year; @endphp
        <label class="text-muted small">Year</label>
        <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
            @for($y = $endYear; $y >= $startYear; $y--)
                <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="card census-two-tone mb-4">
    <div class="card-body">
        @php
            $pregnant_count = class_exists('App\\Models\\MaternalHealth') 
                ? \App\Models\MaternalHealth::active()->count() 
                : 0;
            $infants_count = \App\Models\Resident::approved()->active()->where('age','<',1)->count();
        @endphp
        <div class="table-responsive census-table-wrap">
        <table class="table mb-0 census-table">
            <thead>
                <tr><th colspan="2" class="table-section">Population Data</th></tr>
            </thead>
            <tbody>
                <tr><td>Total Population</td><td class="text-end"><strong>{{ number_format($census['total_population'] ?? 0) }}</strong></td></tr>
                <tr><td>Number of Households</td><td class="text-end"><strong>{{ number_format($census['total_households'] ?? 0) }}</strong></td></tr>
                <tr><td>Male Count</td><td class="text-end"><strong>{{ number_format($census['male_count'] ?? 0) }}</strong></td></tr>
                <tr><td>Female Count</td><td class="text-end"><strong>{{ number_format($census['female_count'] ?? 0) }}</strong></td></tr>
                <tr><td>Children (0–12)</td><td class="text-end"><strong>{{ number_format($census['children_count'] ?? 0) }}</strong></td></tr>
                <tr><td>Youth (13–17)</td><td class="text-end"><strong>{{ number_format(($census['youth_count'] ?? ($census['teens_count'] ?? 0))) }}</strong></td></tr>
                <tr><td>Adults (18–59)</td><td class="text-end"><strong>{{ number_format(($census['adults_18_59_count'] ?? ($census['adults_count'] ?? 0))) }}</strong></td></tr>
                <tr><td>Senior Citizens (60+)</td><td class="text-end"><strong>{{ number_format($census['seniors_count'] ?? 0) }}</strong></td></tr>
            </tbody>
            <thead>
                <tr><th colspan="2" class="table-section">Health Data</th></tr>
            </thead>
            <tbody>
                <tr><td>Pregnant Women</td><td class="text-end"><strong>{{ number_format($pregnant_count ?? 0) }}</strong></td></tr>
                <tr><td>Infants</td><td class="text-end"><strong>{{ number_format($infants_count ?? 0) }}</strong></td></tr>
                <tr><td>Malnourished Children</td><td class="text-end"><strong>{{ is_null($census['malnourished_children_count'] ?? null) ? 'N/A' : number_format($census['malnourished_children_count']) }}</strong></td></tr>
                <tr><td>PWD Count</td><td class="text-end"><strong>{{ number_format($census['pwd_count'] ?? 0) }}</strong></td></tr>
                <tr><td>Senior Citizen Members</td><td class="text-end"><strong>{{ number_format(($census['senior_citizen_members'] ?? ($census['senior_citizens_count'] ?? 0))) }}</strong></td></tr>
                <tr><td>Common Illness Cases</td><td class="text-end"><strong>{{ is_null($census['common_illness_cases'] ?? null) ? 'N/A' : number_format($census['common_illness_cases']) }}</strong></td></tr>
                <tr><td>Fully Immunized Children</td><td class="text-end"><strong>{{ is_null($census['fully_immunized_children'] ?? null) ? 'N/A' : number_format($census['fully_immunized_children']) }}</strong></td></tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

@if(false)
<!-- Census Summary Grid (7 cards, centered, 3 per row) -->
@php 
    $inactive = max(0, ($census['total_population'] ?? 0) - ($census['active_residents'] ?? 0)); 
    $inactiveCompare = isset($compare) ? max(0, ($compare['total_population'] ?? 0) - ($compare['active_residents'] ?? 0)) : null;
@endphp
<div class="census-grid census-grid--offset mb-4">
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($census['total_population']) }}</h3>
                            @if(isset($compare))
                                @php $delta = ($census['total_population'] - $compare['total_population']); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($compare['total_population']) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Total Population</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($census['active_residents']) }}</h3>
                            @if(isset($compare))
                                @php $delta = ($census['active_residents'] - $compare['active_residents']); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($compare['active_residents']) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Active Residents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-person"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($inactive) }}</h3>
                            @if(isset($compare) && $inactiveCompare !== null)
                                @php $delta = ($inactive - $inactiveCompare); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($inactiveCompare) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Inactive Residents</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($census['reallocated_residents']) }}</h3>
                            @if(isset($compare))
                                @php $delta = ($census['reallocated_residents'] - $compare['reallocated_residents']); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($compare['reallocated_residents']) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Reallocated</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-person-x"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($census['deceased_residents']) }}</h3>
                            @if(isset($compare))
                                @php $delta = ($census['deceased_residents'] - $compare['deceased_residents']); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($compare['deceased_residents']) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Deceased</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-house"></i></div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ number_format($census['total_households']) }}</h3>
                            @if(isset($compare))
                                @php $delta = ($census['total_households'] - $compare['total_households']); @endphp
                                <small class="text-muted">vs {{ $yearB }}: {{ number_format($compare['total_households']) }} ({{ $delta >= 0 ? '+' : '' }}{{ number_format($delta) }})</small>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Total Households</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['average_household_size'], 1) }}</h3>
                        <p class="text-muted mb-0">Avg Household Size</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Gender & Age (centered two-card row) -->
<div class="census-grid-2 census-grid--offset mb-4">
    <div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gender-ambiguous"></i> Gender Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary">{{ number_format($census['male_count']) }}</h3>
                        <p class="text-muted mb-0">Male</p>
                        <small class="text-muted">
                            {{ $census['total_population'] > 0 ? number_format(($census['male_count'] / $census['total_population']) * 100, 1) : 0 }}%
                        </small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-danger">{{ number_format($census['female_count']) }}</h3>
                        <p class="text-muted mb-0">Female</p>
                        <small class="text-muted">
                            {{ $census['total_population'] > 0 ? number_format(($census['female_count'] / $census['total_population']) * 100, 1) : 0 }}%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people"></i> Age Groups</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Children (0-12)</span>
                        <strong>{{ number_format($census['children_count']) }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: {{ $census['total_population'] > 0 ? ($census['children_count'] / $census['total_population'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Teens (13-19)</span>
                        <strong>{{ number_format($census['teens_count']) }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-secondary" style="width: {{ $census['total_population'] > 0 ? ($census['teens_count'] / $census['total_population'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Adults (20-59)</span>
                        <strong>{{ number_format($census['adults_count']) }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: {{ $census['total_population'] > 0 ? ($census['adults_count'] / $census['total_population'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Seniors (60+)</span>
                        <strong>{{ number_format($census['seniors_count']) }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: {{ $census['total_population'] > 0 ? ($census['seniors_count'] / $census['total_population'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Special Categories (Uniform 3-per-row, centered) -->
@php
    $pregnant_count = class_exists('App\\Models\\MaternalHealth')
        ? \App\Models\MaternalHealth::active()->count()
        : 0;
    $infants_count = \App\Models\Resident::approved()->active()->where('age','<',1)->count();
@endphp
<div class="census-grid census-grid--offset mb-4">
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-universal-access"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['pwd_count']) }}</h3>
                        <p class="text-muted mb-0">PWD</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-person-standing"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['senior_citizens_count']) }}</h3>
                        <p class="text-muted mb-0">Senior Citizens</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['voters_count']) }}</h3>
                        <p class="text-muted mb-0">Voters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['fourps_count']) }}</h3>
                        <p class="text-muted mb-0">4Ps Beneficiaries</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-heart-pulse"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($pregnant_count) }}</h3>
                        <p class="text-muted mb-0">Pregnant</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-emoji-smile"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($census['children_count']) }}</h3>
                        <p class="text-muted mb-0">Children (0–12)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-baby"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ number_format($infants_count) }}</h3>
                        <p class="text-muted mb-0">Infants (<1)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Civil Status & Employment (2 cards centered in one row) -->
<div class="census-grid-2 census-grid--offset mb-4">
    <div>
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-heart"></i> Civil Status</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>Single</td><td class="text-end"><strong>{{ number_format($census['single_count']) }}</strong></td></tr>
                    <tr><td>Married</td><td class="text-end"><strong>{{ number_format($census['married_count']) }}</strong></td></tr>
                    <tr><td>Widowed</td><td class="text-end"><strong>{{ number_format($census['widowed_count']) }}</strong></td></tr>
                    <tr><td>Separated</td><td class="text-end"><strong>{{ number_format($census['separated_count']) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div>
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-briefcase"></i> Employment Status</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>Employed</td><td class="text-end"><strong>{{ number_format($census['employed_count']) }}</strong></td></tr>
                    <tr><td>Unemployed</td><td class="text-end"><strong>{{ number_format($census['unemployed_count']) }}</strong></td></tr>
                    <tr><td>Self-Employed</td><td class="text-end"><strong>{{ number_format($census['self_employed_count']) }}</strong></td></tr>
                    <tr><td>Students</td><td class="text-end"><strong>{{ number_format($census['students_count']) }}</strong></td></tr>
                    <tr><td>Retired</td><td class="text-end"><strong>{{ number_format($census['retired_count']) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Housing & Income (2 cards centered in one row) -->
<div class="census-grid-2 census-grid--offset mb-4">
    <div>
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-house"></i> Housing Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>Owned Houses</td><td class="text-end"><strong>{{ number_format($census['owned_houses']) }}</strong></td></tr>
                    <tr><td>Rented Houses</td><td class="text-end"><strong>{{ number_format($census['rented_houses']) }}</strong></td></tr>
                    <tr><td>Rent-Free Houses</td><td class="text-end"><strong>{{ number_format($census['rent_free_houses']) }}</strong></td></tr>
                    <tr><td>With Electricity</td><td class="text-end"><strong>{{ number_format($census['with_electricity']) }}</strong></td></tr>
                    <tr><td>Without Electricity</td><td class="text-end"><strong>{{ number_format($census['without_electricity']) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div>
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cash"></i> Income Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Total Monthly Income</label>
                    <h3 class="mb-0 text-success">₱{{ number_format($census['total_income'], 2) }}</h3>
                </div>
                <div>
                    <label class="text-muted small">Average Monthly Income</label>
                    <h3 class="mb-0 text-primary">₱{{ number_format($census['average_income'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Household Types -->
<div class="row g-3 section-offset">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Household Types</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3>{{ number_format($census['solo_households']) }}</h3>
                        <p class="text-muted mb-0">Solo Households</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{{ number_format($census['family_households']) }}</h3>
                        <p class="text-muted mb-0">Family Households</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{{ number_format($census['extended_households']) }}</h3>
                        <p class="text-muted mb-0">Extended Households</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

<!-- Generated Info -->
<div class="section-offset">
    <div class="card mt-3">
        <div class="card-body">
            <small class="text-muted">
                <i class="bi bi-clock"></i> Generated on: {{ now()->format('F d, Y h:i A') }}
            </small>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .section-offset { padding-left: 0.5rem; padding-right: 0.5rem; }
    .census-two-tone { background: #ffffff; color: #111; border: 1px solid #e5e7eb; border-left: 4px solid #16a34a; width: 100%; max-width: 980px; margin: 0 auto; }
    .census-table { width: 100%; margin: 0 auto; }
    .census-table-wrap { max-width: 880px; margin: 0 auto; padding: 0 1.25rem; }
    .census-two-tone .card-body { padding-left: 2rem; padding-right: 2rem; }
    .census-table th.table-section { background: #fff; border-bottom: 2px solid #16a34a; color: #111; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 0.6rem 0.75rem; }
    .census-table td { padding: 0.55rem 0.75rem; border-top: 1px solid #eef2f7; }
    .census-table tr td:first-child { color: #111; width: 68%; }
    .census-table tr td:last-child { color: #111; width: 32%; }
    .table tbody tr:hover { background-color: transparent !important; }
    @media (max-width: 576px){
        .census-table-wrap { max-width: 100%; padding: 0 0.75rem; }
    }
    .card .card-header, .card .card-body { background: #fff; color: #111; }
    .btn-primary { background-color: #16a34a !important; border-color: #16a34a !important; color: #fff !important; }
    .text-muted { color: #6b7280 !important; }
</style>
@endpush
