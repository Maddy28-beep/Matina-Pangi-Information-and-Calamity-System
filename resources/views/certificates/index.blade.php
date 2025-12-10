@extends('layouts.app')

@section('title', 'Certificates - Barangay Matina Pangi')

@push('styles')
<style>
/* Live Search Styling */
.live-search-container .input-group {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 0.5rem;
    overflow: hidden;
}

.live-search-container .form-control {
    border: 1px solid #E5E7EB;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
}

.live-search-container .form-control:focus {
    box-shadow: none;
    border-color: #4A6F52;
}

.live-search-container .input-group-text {
    border: 1px solid #E5E7EB;
    border-right: none;
    padding: 0.75rem 1rem;
}

.live-search-container .clear-search-btn {
    border: 1px solid #E5E7EB;
    border-left: none;
    background: white !important;
    color: #6B7280 !important;
    padding: 0.5rem 1rem !important;
}

.live-search-container .clear-search-btn:hover {
    background: #F9FAFB !important;
    color: #4A6F52 !important;
}

.search-result-count {
    font-size: 0.875rem;
    color: #4A6F52;
    font-weight: 600;
}

.certificates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.certificate-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    border-top: 3px solid #4A6F52;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    transition: all 0.2s ease;
    overflow: hidden;
}

.certificate-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 111, 82, 0.12);
    border-top-color: #5a9275;
}

.certificate-card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.certificate-number {
    font-size: 0.6875rem;
    font-weight: 700;
    color: #4A6F52;
    background: #f0fdf4;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    align-self: flex-start;
}

.certificate-resident {
    font-size: 1rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
    line-height: 1.3;
}

.certificate-type {
    margin-bottom: 0.25rem;
}

.type-badge {
    display: inline-block;
    background: rgba(74, 111, 82, 0.1);
    color: #4A6F52;
    font-size: 0.6875rem;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
}

.certificate-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background: #f9fafb;
    padding: 0.75rem;
    border-radius: 8px;
}

.info-item {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-item i {
    color: #4A6F52;
    width: 14px;
    font-size: 0.875rem;
}

.certificate-status {
    margin-top: 0.5rem;
}

.certificate-status .badge {
    font-size: 0.6875rem;
    padding: 4px 10px;
    font-weight: 600;
    border-radius: 6px;
}

.status-badge-issued {
    background-color: #3B82F6 !important;
    color: white !important;
}

.status-badge-claimed {
    background-color: #10B981 !important;
    color: white !important;
}

.status-badge-cancelled {
    background-color: #EF4444 !important;
    color: white !important;
}

.certificate-actions {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f3f4f6;
}

.certificate-actions .btn {
    width: 100%;
    font-weight: 600;
    background-color: #4A6F52 !important;
    border: none !important;
    color: white !important;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.certificate-actions .btn:hover {
    background-color: #5a9275 !important;
}
    transition: all 0.2s ease;
}

.certificate-actions .btn:hover {
    background-color: #5a9275 !important;
    transform: translateY(-1px);
}

/* Compact Empty State */
.empty-state {
    text-align: center;
    padding: 2rem 1.5rem;
    background: #F9FAFB;
    border-radius: 0.75rem;
    border: 1px solid #E5E7EB;
}

.empty-state i {
    font-size: 2.5rem;
    color: #D1D5DB;
    margin-bottom: 0.75rem;
    opacity: 0.6;
}

.empty-state p {
    color: #6B7280;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.empty-state small {
    color: #9CA3AF;
    font-size: 0.8125rem;
    display: block;
    margin-bottom: 1rem;
}

@media (max-width: 991px) {
    .certificates-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 576px) {
    .certificates-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    
    .certificate-card-body {
        padding: 1.5rem;
    }
}
</style>
<style>
.clickable-row { cursor: pointer; }
.clickable-row:hover { background-color: #F9FAFB; }
</style>
@endpush

@section('content')<div class="ds-page">
<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-file-earmark-text-fill"></i> Certificates Management
        </h2>
        <p class="text-muted mb-0">View, manage, and issue barangay certificates</p>
    </div>
    <a href="{{ route('certificates.create') }}" class="btn btn-gradient">
        <i class="bi bi-plus-circle-fill"></i> {{ auth()->user()->isSecretary() ? 'Issue New Certificate' : 'Submit Certificate Request' }}
    </a>
</div>

<!-- Inline Filters -->
<div class="card border-0 filter-bar mb-3">
    <div class="card-body">
        <form id="certFilterForm" method="GET" action="{{ route('certificates.index') }}" class="row g-2 align-items-end">
            <div class="col-xl-6 col-lg-6 col-md-12">
                <input type="text" name="search" class="form-control" placeholder="🔍 Certificate #, OR #, or resident name..." value="{{ request('search') }}">
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="barangay_clearance" {{ request('type') == 'barangay_clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                    <option value="certificate_of_indigency" {{ request('type') == 'certificate_of_indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                    <option value="certificate_of_residency" {{ request('type') == 'certificate_of_residency' ? 'selected' : '' }}>Certificate of Residency</option>
                    <option value="business_clearance" {{ request('type') == 'business_clearance' ? 'selected' : '' }}>Business Clearance</option>
                    <option value="good_moral" {{ request('type') == 'good_moral' ? 'selected' : '' }}>Good Moral</option>
                    <option value="travel_permit" {{ request('type') == 'travel_permit' ? 'selected' : '' }}>Travel Permit</option>
                </select>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-6">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Claimed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-xl-1 col-lg-2 col-md-12 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">
                    Search / Filter
                </button>
                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
                    Clear
                </a>
            </div>
        </form>
    </div>
    </div>

 

<!-- Certificates Table -->
<div class="card border-0">
    <div class="card-body">
        @php
            $baseParams = request()->except(['page', 'sort', 'direction']);
            $currentSort = request('sort');
            $currentDir = request('direction', 'asc');
            $dirToggle = fn($col) => ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
        @endphp

        <div class="table-responsive">
            <table class="table table-hover align-middle certificates-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('certificates.index', array_merge($baseParams, ['sort' => 'type', 'direction' => $dirToggle('type')])) }}" class="text-decoration-none">Certificate Name</a>
                        </th>
                        <th>
                            <a href="{{ route('certificates.index', array_merge($baseParams, ['sort' => 'resident', 'direction' => $dirToggle('resident')])) }}" class="text-decoration-none">Requester Details</a>
                        </th>
                        <th class="text-nowrap">
                            <a href="{{ route('certificates.index', array_merge($baseParams, ['sort' => 'date', 'direction' => $dirToggle('date')])) }}" class="text-decoration-none">Request Date</a>
                        </th>
                        <th>
                            <a href="{{ route('certificates.index', array_merge($baseParams, ['sort' => 'status', 'direction' => $dirToggle('status')])) }}" class="text-decoration-none">Status</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        @php
                            $searchText = implode(' ', [
                                $certificate->certificate_number,
                                $certificate->resident->full_name,
                                $certificate->type_label,
                                $certificate->or_number ?: '',
                                ucfirst($certificate->status),
                                $certificate->issued_date->format('M d Y'),
                                number_format($certificate->amount_paid, 2)
                            ]);
                        @endphp
                        <tr class="clickable-row" data-search-text="{{ $searchText }}" data-href="{{ route('certificates.show', $certificate) }}">
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ $certificate->type_label }}</div>
                                <small class="text-muted">{{ $certificate->certificate_number }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $certificate->resident->full_name }}</div>
                                @if($certificate->resident->household)
                                    <small class="text-muted">HH# {{ $certificate->resident->household->household_number }}</small>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $certificate->issued_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge status-badge-{{ strtolower($certificate->status) }}">{{ ucfirst($certificate->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="ds-empty-state" style="margin: 1rem auto; max-width: 600px;">
                                    <div class="ds-empty-state__icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <h3 class="ds-empty-state__title">No Certificates Found</h3>
                                    <p class="ds-empty-state__description">Try adjusting your filters or issue a new certificate to get started.</p>
                                    <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> {{ auth()->user()->isSecretary() ? 'Issue Certificate' : 'Submit Certificate Request' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
        <div class="mt-3">
            {{ $certificates->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('certFilterForm');
        document.querySelectorAll('#certFilterForm select').forEach(function(sel){
            sel.addEventListener('change', function(){ form && form.submit(); });
        });
        var search = document.querySelector('#certFilterForm input[name="search"]');
        if (search) {
            search.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); form && form.submit(); } });
        }
        document.querySelectorAll('tr.clickable-row').forEach(function(row){
            row.addEventListener('click', function(){
                var href = this.getAttribute('data-href');
                if (href) window.location.href = href;
            });
        });
    });
</script>
@endpush
