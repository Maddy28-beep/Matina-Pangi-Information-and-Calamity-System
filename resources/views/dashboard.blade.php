@extends('layouts.app')

@section('title', 'Dashboard - Barangay Matina Pangi')

@push('styles')
<style>
    .dashboard-content-wrapper { display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
    .dashboard-content { max-width: 1200px; width: 100%; margin: 0 auto; padding: 0; padding-top: 0; margin-left: 120px; }
    .page-header { margin-bottom: 8px !important; padding-bottom: 0; border-bottom: none; }
    .stats-grid { display: grid; gap: 1.25rem; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 1.25rem; align-items: start; }
    .stat-card { cursor: pointer; transition: background 0.15s ease, transform 0.15s ease; height: 100%; }
    .stat-card:hover { background: #f9fafb; transform: translateY(-2px); }
    .stat-card .card-body { padding: 1.5rem; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .stat-card h3 { font-size: 2.25rem; margin-bottom: 0.25rem; line-height: 1; }
    .analytics-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .chart-wrapper { display: block; }
    .chart-wrapper canvas { width: 100% !important; max-width: 100% !important; min-height: 220px; }
    .quick-actions-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; align-items: stretch; }
    .quick-actions-grid > a.btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 48px; padding: 12px 16px !important; box-shadow: none !important; margin: 0 !important; white-space: nowrap; box-sizing: border-box; }
    @media (max-width: 1200px) { .quick-actions-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    @media (max-width: 992px) { .quick-actions-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 576px) { .quick-actions-grid { grid-template-columns: 1fr; } }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 992px) { .analytics-grid { grid-template-columns: 1fr; } }
    @media (max-width: 576px) {
        .dashboard-content { padding: 0; }
        .stats-grid { grid-template-columns: 1fr; }
        .analytics-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-content-wrapper" style="background: #f6f8f7; min-height: 100vh;">
    <div class="dashboard-content" style="background: #f6f8f7; border-radius: 24px; box-shadow: 0 4px 32px 0 rgba(44, 111, 82, 0.08); padding: 40px 32px; max-width: 1400px; margin: 0 auto;">
    <div class="page-header">
        <h2 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
        <p class="text-muted mb-0">Quick snapshot of barangay records.</p>
    </div>
    @if(auth()->user()->isSecretary())
    @php $pendingApprovals = (\App\Models\Resident::pending()->count()) + (\App\Models\Household::pending()->count()); @endphp
    @if($pendingApprovals > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
      <div><i class="bi bi-clock-history"></i> Pending approvals: <strong>{{ $pendingApprovals }}</strong></div>
      <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-warning"><i class="bi bi-arrow-right"></i> Open Approvals</a>
    </div>
    @endif
    @endif

    

    <div class="stats-grid">
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}?age_min=13&age_max=19'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_teens']) }}</h3>
                    <p class="text-muted mb-0">Teens (13–19)</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}?voter_status=1'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_voters']) }}</h3>
                    <p class="text-muted mb-0">Registered Voters</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}?is_4ps=1'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_4ps']) }}</h3>
                    <p class="text-muted mb-0">4Ps Beneficiaries</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['average_household_size'], 1) }}</h3>
                    <p class="text-muted mb-0">Avg Household Size</p>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_residents']) }}</h3>
                    <p class="text-muted mb-0">Total Residents</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_households']) }}</h3>
                    <p class="text-muted mb-0">Households</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}?age_min=60'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_senior_citizens']) }}</h3>
                    <p class="text-muted mb-0">Senior Citizens</p>
                </div>
            </div>
        </div>
        <div>
            <div class="card stat-card h-100" onclick="window.location.href='{{ route('residents.index') }}?is_pwd=1'">
                <div class="card-body">
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_pwd']) }}</h3>
                    <p class="text-muted mb-0">PWD Residents</p>
                </div>
            </div>
        </div>
    </div>

    <div class="analytics-grid mb-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-gender-ambiguous me-2 text-primary"></i>Gender Distribution</h5>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="genderChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Age Distribution</h5>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="ageChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-info"></i>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="quick-actions-grid">
                @if(auth()->user()->isSecretary())
                    <a class="btn btn-outline-primary w-100" href="{{ route('households.create') }}">Register Household</a>
                    <a class="btn btn-outline-primary w-100" href="{{ route('households.index') }}">Add Resident</a>
                    <a class="btn btn-outline-primary w-100" href="{{ route('certificates.create') }}">Issue Certificate</a>
                    <a class="btn btn-outline-primary w-100" href="{{ route('census.index') }}">View Census</a>
                    <a class="btn btn-outline-primary w-100" href="{{ route('settings.users.index') }}">Manage Users</a>
                @else
                    @if(auth()->user()->isStaff())
                        <a class="btn btn-outline-primary w-100" href="{{ route('staff.households.create') }}">Register Household</a>
                        <a class="btn btn-outline-primary w-100" href="{{ route('staff.residents.index') }}">Add Resident</a>
                        <a class="btn btn-outline-primary w-100" href="{{ route('certificates.create') }}">Issue Certificate</a>
                        <a class="btn btn-outline-primary w-100" href="{{ route('census.index') }}">View Census</a>
                        <a class="btn btn-outline-primary w-100" href="{{ route('staff.submissions.index') }}">My Submissions</a>
                    @else
                        <a class="btn btn-outline-primary w-100" href="{{ route('census.index') }}">View Census</a>
                        <a class="btn btn-outline-primary w-100" href="{{ route('residents.index') }}">View Residents</a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if($recentResidents->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-info"></i>Recently Registered Residents</h5>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-sm btn-outline-light text-white" href="{{ route('census.index') }}">View Census</a>
                    <a class="btn btn-sm btn-outline-light text-white" href="{{ route('residents.index') }}">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Resident ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th>Household</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentResidents as $resident)
                                <tr class="clickable-row" data-href="{{ auth()->user()->isStaff() ? route('staff.residents.show', $resident) : route('residents.show', $resident) }}">
                                    <td><span class="badge bg-light text-dark">{{ $resident->resident_id }}</span></td>
                                    <td class="fw-semibold">{{ $resident->full_name }}</td>
                                    <td>{{ $resident->age }} yrs</td>
                                    <td>
                                        @if($resident->sex === 'male')
                                            <span class="badge" style="background:#dbeafe;color:#1e40af;"><i class="bi bi-gender-male"></i> Male</span>
                                        @else
                                            <span class="badge" style="background:#fee2e2;color:#991b1b;"><i class="bi bi-gender-female"></i> Female</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($resident->household)
                                            <span class="text-decoration-none"><i class="bi bi-house-fill text-success"></i> {{ $resident->household->household_id }}</span>
                                        @else
                                            <span class="text-muted">No household</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted"><i class="bi bi-clock"></i> {{ $resident->created_at->diffForHumans() }}</small></td>
                                </tr>
                            @endforeach
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.clickable-row').forEach(function(row) {
        row.style.cursor = 'pointer';
        row.addEventListener('mouseenter', function() { this.classList.add('table-active'); });
        row.addEventListener('mouseleave', function() { this.classList.remove('table-active'); });
        row.addEventListener('click', function() { window.location = this.getAttribute('data-href'); });
    });
});
</script>
@endpush
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
  </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    const primary = '#1E3A8A';
    const neutral = '#E5E7EB';
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        new Chart(genderCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [
                        {{ (int)($stats['male_count'] ?? 0) }},
                        {{ (int)($stats['female_count'] ?? 0) }}
                    ],
                    backgroundColor: [primary, '#93C5FD'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                cutout: '60%'
            }
        });
    }
    const ageCtx = document.getElementById('ageChart');
    if (ageCtx) {
        new Chart(ageCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Children', 'Teens', 'Adults', 'Seniors'],
                datasets: [{
                    label: 'Count',
                    data: [
                        {{ (int)($ageDistribution['children'] ?? 0) }},
                        {{ (int)($ageDistribution['teens'] ?? 0) }},
                        {{ (int)($ageDistribution['adults'] ?? 0) }},
                        {{ (int)($ageDistribution['seniors'] ?? 0) }}
                    ],
                    backgroundColor: primary,
                    borderRadius: 6
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: neutral } }
                }
            }
        });
    }
    
})();
</script>
@endpush
