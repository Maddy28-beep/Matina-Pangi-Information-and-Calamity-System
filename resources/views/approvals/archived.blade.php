@extends('layouts.app')

@section('title', 'Archived Records')

@section('content')<div class="ds-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-archive"></i> Archived Records</h2>
    <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Approvals
    </a>
</div>

<!-- Archived Residents -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-people"></i> Archived Residents ({{ $archivedResidents->total() }})</h5>
    </div>
    <div class="card-body">
        @if($archivedResidents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Resident ID</th>
                            <th>Name</th>
                            <th>Age/Sex</th>
                            <th>Household</th>
                            <th>Status</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedResidents as $resident)
                        <tr>
                            <td><strong>{{ $resident->resident_id }}</strong></td>
                            <td>{{ $resident->full_name }}</td>
                            <td>{{ $resident->age }} / {{ ucfirst($resident->sex) }}</td>
                            <td>
                                @if($resident->household)
                                    {{ $resident->household->household_id }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $resident->approval_badge_color }}">
                                    {{ ucfirst($resident->approval_status) }}
                                </span>
                                @if($resident->rejection_reason)
                                    <br><small class="text-muted">{{ Str::limit($resident->rejection_reason, 30) }}</small>
                                @endif
                            </td>
                            <td>{{ $resident->deleted_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('archived.resident.restore', $resident->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to restore this resident?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                                
                                <form action="{{ route('archived.resident.delete', $resident->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('⚠️ WARNING: This will PERMANENTLY delete this resident. This action CANNOT be undone. Are you absolutely sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $archivedResidents->links() }}
            </div>
        @else
            <p class="text-muted text-center mb-0">No archived residents.</p>
        @endif
    </div>
</div>

<!-- Archived Households -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-house"></i> Archived Households ({{ $archivedHouseholds->total() }})</h5>
    </div>
    <div class="card-body">
        @if($archivedHouseholds->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Household ID</th>
                            <th>Address</th>
                            <th>Head</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedHouseholds as $household)
                        <tr>
                            <td><strong>{{ $household->household_id }}</strong></td>
                            <td>{{ $household->full_address }}</td>
                            <td>{{ $household->head ? $household->head->full_name : 'N/A' }}</td>
                            <td>{{ $household->total_members }}</td>
                            <td>
                                <span class="badge bg-{{ $household->approval_badge_color }}">
                                    {{ ucfirst($household->approval_status) }}
                                </span>
                                @if($household->rejection_reason)
                                    <br><small class="text-muted">{{ Str::limit($household->rejection_reason, 30) }}</small>
                                @endif
                            </td>
                            <td>{{ $household->deleted_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('archived.household.restore', $household->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to restore this household and all its members?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                                
                                <form action="{{ route('archived.household.delete', $household->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('⚠️ WARNING: This will PERMANENTLY delete this household and ALL its members. This action CANNOT be undone. Are you absolutely sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $archivedHouseholds->links() }}
            </div>
        @else
            <p class="text-muted text-center mb-0">No archived households.</p>
        @endif
    </div>
</div>
</div>
@endsection
