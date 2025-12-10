@extends('layouts.app')

@section('title', 'Response Team Member Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web.response-team-members.index') }}">Response Team</a></li>
    <li class="breadcrumb-item active" aria-current="page">Member Details</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-person-badge"></i> {{ $response_team_member->name }}</h2>
  <a href="{{ route('web.response-team-members.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Member Information</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <strong>Name:</strong>
          <p>{{ $response_team_member->name }}</p>
        </div>
        <div class="mb-3">
          <strong>Role:</strong>
          <p><span class="badge bg-info">{{ $response_team_member->role ?? 'N/A' }}</span></p>
        </div>
        <div class="mb-3">
          <strong>Skills:</strong>
          <p>
            @if(is_array($response_team_member->skills) && count($response_team_member->skills))
              @foreach($response_team_member->skills as $skill)
                <span class="badge bg-secondary me-1">{{ $skill }}</span>
              @endforeach
            @else
              <span class="text-muted">No skills listed</span>
            @endif
          </p>
        </div>
        <div class="mb-3">
          <strong>Assigned Evacuation Center:</strong>
          <p>{{ optional($response_team_member->evacuationCenter)->name ?? 'Not assigned' }}</p>
        </div>
        <div class="mb-3">
          <strong>Related Calamity:</strong>
          <p>{{ optional($response_team_member->calamity)->calamity_name ?? 'N/A' }}</p>
        </div>
        @if($response_team_member->assignment_notes)
        <div class="mb-3">
          <strong>Assignment Notes:</strong>
          <p>{{ $response_team_member->assignment_notes }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistics</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <strong>Total Rescue Operations:</strong>
          <p class="h4 text-primary">{{ $response_team_member->rescueOperations()->count() }}</p>
        </div>
        <div class="mb-3">
          <strong>Households Helped:</strong>
          <p class="h4 text-success">{{ $response_team_member->rescueOperations()->distinct('calamity_affected_household_id')->count('calamity_affected_household_id') }}</p>
        </div>
        <div class="mb-3">
          <strong>Member Since:</strong>
          <p>{{ $response_team_member->created_at->format('F d, Y') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

@if($response_team_member->rescueOperations()->exists())
<div class="card">
  <div class="card-header bg-light">
    <h5 class="mb-0"><i class="bi bi-life-preserver"></i> Recent Rescue Operations</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Household</th>
            <th>Operation Type</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($response_team_member->rescueOperations()->latest()->limit(10)->get() as $operation)
          <tr>
            <td>{{ optional($operation->operation_date)->format('M d, Y') ?? 'N/A' }}</td>
            <td>{{ optional($operation->affectedHousehold->household)->household_id ?? 'N/A' }}</td>
            <td><span class="badge bg-info">{{ $operation->operation_type ?? 'Rescue' }}</span></td>
            <td>
              <span class="badge bg-{{ $operation->status == 'completed' ? 'success' : 'warning' }}">
                {{ ucfirst($operation->status ?? 'pending') }}
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

</div>
@endsection
