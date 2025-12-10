@extends('layouts.app')

@section('title', 'Residents')

@push('styles')
<style>
.ds-table th.col-address,
.ds-table td[data-label="Address"] { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
@endpush

@php
    use Illuminate\Support\Facades\Route;
    use App\Models\Purok;

    $residentCount = method_exists($residents, 'total') ? $residents->total() : $residents->count();
    $purokOptions = $purokOptions ?? Purok::orderBy('purok_name')->pluck('purok_name', 'purok_name')->toArray();
@endphp

@section('content')
<div class="ds-page" data-search-scope>
    <nav class="ds-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ds-breadcrumbs__separator">/</span>
        <span>Residents</span>
    </nav>

    <div class="ds-page__header">
        <div>
            <h1 class="page-title d-flex align-items-center gap-2"><i class="bi bi-people"></i>Residents</h1>
            <p class="ds-page__subtitle">Manage resident records with instant filtering, responsive tables, and accessible actions.</p>
        </div>
        <div class="ds-toolbar__group">
            <div class="ds-metric" role="presentation">
                <span class="ds-metric__label">Total Residents</span>
                <span class="ds-metric__value">{{ number_format($residentCount) }}</span>
            </div>
        </div>
    </div>

    <div class="ds-info-banner" role="note">
        <i class="bi bi-info-circle"></i>
        <span>Add residents through <strong>Households → Add Member</strong> so household relationships stay in sync.</span>
    </div>

    <div class="ds-toolbar mt-3">
        <div class="ds-search" role="search">
            <i class="bi bi-search ds-search__icon" aria-hidden="true"></i>
            <input
                type="search"
                class="live-search-input"
                placeholder="Search by name, ID, household, address…"
                data-target-table="#residentsTableBody"
                data-empty-state="#residentsEmptyState"
                data-result-count="#residentsResultCount"
                aria-label="Search residents"
                autocomplete="off">
            <button class="ds-search__clear clear-search-btn" type="button" aria-label="Clear search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <span class="ds-result-count" id="residentsResultCount" aria-live="polite"></span>
    </div>

    @php
        $residentSearchFields = [
            [
                'name' => 'search',
                'label' => 'Name or ID',
                'type' => 'text',
                'placeholder' => 'e.g., Maria Santos or 2024-001'
            ],
            [
                'name' => 'category',
                'label' => 'Focus Segment',
                'type' => 'select',
                'options' => [
                    'pwd' => 'Persons with Disability',
                    'senior' => 'Senior Citizens',
                    'teen' => 'Teens',
                    'voter' => 'Registered Voters',
                    '4ps' => '4Ps Beneficiaries',
                    'head' => 'Household Heads'
                ],
                'placeholder' => 'All segments'
            ],
            [
                'name' => 'gender',
                'label' => 'Gender',
                'type' => 'select',
                'options' => [
                    'Male' => 'Male',
                    'Female' => 'Female'
                ],
                'placeholder' => 'All genders'
            ],
            [
                'name' => 'purok',
                'label' => 'Purok',
                'type' => 'select',
                'options' => $purokOptions,
                'placeholder' => 'All puroks'
            ],
        ];
    @endphp

    <x-search-filter
        :route="route('residents.index')"
        title="Filter Residents"
        icon="bi-people"
        :fields="$residentSearchFields"
        :advanced="false"
        :inline="true" />

    @if($residents->count() > 0)
        <div class="ds-table-wrapper" id="residentsTableWrapper">
            <div class="table-responsive">
                <table class="table ds-table" id="residentsTable">
                    <thead>
                        <tr>
                            <th scope="col">Resident ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Age / Sex</th>
                            <th scope="col">Household</th>
                            <th scope="col" class="col-address">Address</th>
                            <th scope="col">Categories</th>
                            <th scope="col">Status</th>
                            @if(auth()->user()->isSecretary())
                                <th scope="col" class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="residentsTableBody">
                        @foreach($residents as $resident)
                            @php
                                $categories = [];
                                if($resident->is_household_head) $categories[] = 'Head';
                                if($resident->is_pwd) $categories[] = 'PWD';
                                if($resident->is_senior_citizen) $categories[] = 'Senior';
                                if($resident->is_voter) $categories[] = 'Voter';
                                if($resident->is_4ps_beneficiary) $categories[] = '4Ps';

                                $searchText = implode(' ', [
                                    $resident->resident_id,
                                    $resident->full_name,
                                    $resident->age . ' yrs',
                                    ucfirst($resident->sex),
                                    $resident->household ? $resident->household->household_id : 'No Household',
                                    $resident->household ? $resident->household->full_address : '',
                                    ucfirst($resident->status),
                                    implode(' ', $categories)
                                ]);
                            @endphp
                            <tr
                                class="clickable-row"
                                data-href="{{ route('residents.show', $resident) }}"
                                data-search-text="{{ $searchText }}"
                                onclick="window.location.href='{{ route('residents.show', $resident) }}'"
                                title="Click to view resident details"
                            >
                                <td data-label="Resident ID">
                                    <strong class="text-primary">{{ $resident->resident_id }}</strong>
                                </td>
                                <td data-label="Name">
                                    <div class="fw-semibold">{{ $resident->full_name }}</div>
                                </td>
                                <td data-label="Age / Sex">{{ $resident->age }} / {{ ucfirst($resident->sex) }}</td>
                                <td data-label="Household">{{ $resident->household ? $resident->household->household_id : '—' }}</td>
                                <td data-label="Address" class="ds-text-truncate">{{ $resident->household ? $resident->household->full_address : '—' }}</td>
                                <td data-label="Categories">
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse($categories as $category)
                                            <span class="ds-chip">{{ $category }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @if($resident->status === 'active')
                                        <span class="ds-badge ds-badge--success">Active</span>
                                    @elseif($resident->status === 'inactive')
                                        <span class="ds-badge ds-badge--neutral">Inactive</span>
                                    @elseif($resident->status === 'deceased')
                                        <span class="ds-badge ds-badge--danger">Deceased</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isSecretary())
                                    <td data-label="Actions" class="text-end" onclick="event.stopPropagation()">
                                        <div class="ds-actions">
                                            <a href="{{ route('residents.edit', $resident) }}" class="btn btn-icon btn-outline-secondary" title="Edit resident">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-outline-success"
                                                title="Archive resident"
                                                onclick="event.stopPropagation(); if(confirm('Archive this resident?')) { document.getElementById('archive-form-{{ $resident->id }}').submit(); }">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                        <form id="archive-form-{{ $resident->id }}" action="{{ route('residents.archive', $resident) }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ds-table-foot">
                @if(method_exists($residents, 'firstItem') && $residents->count() > 0)
                    <span class="ds-table-foot__meta">
                        Showing {{ number_format($residents->firstItem()) }}–{{ number_format($residents->lastItem()) }} of {{ number_format($residentCount) }} residents
                    </span>
                @endif
                {{ $residents->links() }}
            </div>
        </div>
    @else
        <div class="ds-empty-state">
            <div class="ds-empty-state__icon"><i class="bi bi-people"></i></div>
            <p class="ds-empty-state__title">No residents yet</p>
            <p class="ds-empty-state__description">Start by creating households and adding members so records appear here.</p>
            @if(auth()->user()->isSecretary())
                <a href="{{ route('households.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Register first household</span>
                </a>
            @endif
        </div>
    @endif

    <div id="residentsEmptyState" class="ds-empty-state search-empty-state" hidden>
        <div class="ds-empty-state__icon"><i class="bi bi-search"></i></div>
        <p class="ds-empty-state__title">No results</p>
        <p class="ds-empty-state__description">We couldn’t find residents matching your filters.</p>
        <button class="btn btn-secondary clear-search-btn" type="button">Clear search</button>
    </div>

    @if(auth()->user()->isSecretary() && Route::has('residents.create'))
        <a href="{{ route('residents.create') }}" class="fab d-md-none" aria-label="Add resident">
            <i class="bi bi-plus-lg"></i>
        </a>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.clickable-row').forEach(function(row) {
        row.style.cursor = 'pointer';
        row.addEventListener('mouseenter', function() {
            this.classList.add('table-active');
        });
        row.addEventListener('mouseleave', function() {
            this.classList.remove('table-active');
        });
        row.addEventListener('click', function(e) {
            // Prevent click if clicking on action buttons
            if(e.target.closest('.ds-actions')) return;
            window.location = this.getAttribute('data-href');
        });
    });
});
</script>
@endpush
