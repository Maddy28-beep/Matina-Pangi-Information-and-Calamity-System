@extends('layouts.app')
@section('title','Calamity Report')
@section('content')<div class="ds-page">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Calamity Report</h4>
    <div>
      <a href="{{ route('web.calamity-reports.pdf',$calamity) }}" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Export PDF</a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">1. Calamity Incident Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Type of Calamity</label><div>{{ ucfirst($calamity->calamity_type) }}</div></div>
        <div class="col-md-4"><label class="form-label">Date & Time</label><div>{{ optional($calamity->date_occurred)->format('Y-m-d') }}</div></div>
        <div class="col-md-4"><label class="form-label">Severity Level</label><div>{{ $calamity->severity_level }}</div></div>
        <div class="col-md-12"><label class="form-label">Affected Puroks/Areas</label><div>{{ $calamity->affected_areas ?? (is_array($calamity->affected_puroks)? implode(', ',$calamity->affected_puroks):'N/A') }}</div></div>
        <div class="col-md-12"><label class="form-label">Short Description</label><div>{{ $calamity->description ?? 'N/A' }}</div></div>
      </div>
      @if(is_array($calamity->photos) && count($calamity->photos))
      <div class="mt-3">
        <label class="form-label">Attached Photos</label>
        <div class="d-flex flex-wrap gap-2">
          @foreach($calamity->photos as $p)
            <img src="{{ asset($p) }}" alt="Incident Photo" style="height:120px;border-radius:6px;object-fit:cover;border:1px solid #eee;">
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">2. Affected Population Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Total Affected Households</label><div>{{ $totalAffectedHouseholds }}</div></div>
        <div class="col-md-4"><label class="form-label">Total Affected Residents</label><div>{{ $totalAffectedResidents }}</div></div>
      </div>
      @if($affectedHouseholds->count())
      <details class="mt-3">
        <summary class="mb-2">Optional: List of affected households</summary>
        <ul class="list-group">
          @foreach($affectedHouseholds as $ah)
            <li class="list-group-item">HH-{{ $ah->household?->household_id }} • {{ $ah->household?->full_address }}</li>
          @endforeach
        </ul>
      </details>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">3. Evacuation Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Total Evacuees</label><div>{{ $totalEvacuees }}</div></div>
        <div class="col-md-4"><label class="form-label">Total Families Evacuated</label><div>{{ $totalFamiliesEvacuated }}</div></div>
        <div class="col-md-4"><label class="form-label">Evacuation Center Occupancy</label>
          <div>
            @if($evacuationCenterOccupancy->count())
              @foreach($evacuationCenterOccupancy as $centerId => $count)
                <span class="badge bg-info me-1">Center #{{ $centerId }}: {{ $count }}</span>
              @endforeach
            @else
              <span class="text-muted">No records</span>
            @endif
          </div>
        </div>
      </div>
      <div class="row g-3 mt-2">
        <div class="col-md-4"><label class="form-label">Center Capacity vs Occupants</label><div>Not recorded</div></div>
        <div class="col-md-4"><label class="form-label">Duration of Evacuation</label><div>Not recorded</div></div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">4. Rescue Operations Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Total Rescues</label><div>{{ $totalRescues }}</div></div>
      </div>
      @if($rescueSummaryByHousehold->count())
      <details class="mt-3">
        <summary class="mb-2">Who rescued each household and when</summary>
        <ul class="list-group">
          @foreach($rescueSummaryByHousehold as $ahId => $rescs)
            <li class="list-group-item">
              HH-{{ optional($rescs->first()->affectedHousehold->household)->household_id }}:
              @foreach($rescs as $r)
                {{ optional($r->rescuer)->name ?? 'Unknown' }} • {{ optional($r->rescue_time)->format('Y-m-d H:i') }}@if($r->evacuationCenter) → {{ $r->evacuationCenter->name }} @endif @if(!$loop->last); @endif
              @endforeach
            </li>
          @endforeach
        </ul>
      </details>
      @else
        <div class="text-muted">No recorded rescues.</div>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">5. Relief Distribution Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Total Relief Goods Distributed</label><div>{{ $totalReliefDistributed }}</div></div>
      </div>
      @if($reliefSummaryPerHousehold->count())
      <details class="mt-3">
        <summary class="mb-2">Summary of items given per household</summary>
        <ul class="list-group">
          @foreach($reliefSummaryPerHousehold as $hhId => $items)
            <li class="list-group-item">
              HH-{{ optional($items->first()->household)->household_id }}:
              @php $byItem = $items->groupBy('relief_item_id'); @endphp
              @foreach($byItem as $itemId => $dist)
                {{ optional($dist->first()->item)->name }} ({{ $dist->sum('quantity') }})@if(!$loop->last), @endif
              @endforeach
            </li>
          @endforeach
        </ul>
      </details>
      @endif
      <div class="mt-2"><small class="text-muted">Remaining relief goods summary is based on inventory module.</small></div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">6. Damage Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Partially Damaged Houses</label><div>{{ $partiallyDamaged }}</div></div>
        <div class="col-md-4"><label class="form-label">Totally Damaged Houses</label><div>{{ $totallyDamaged }}</div></div>
        <div class="col-md-4"><label class="form-label">Basic Cost Estimate</label><div>{{ number_format($estimatedDamageCost,2) }}</div></div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">7. Casualty Report</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Total Injured</label><div>{{ $calamity->total_injured ?? $calamity->getTotalInjuredAttribute() }}</div></div>
        <div class="col-md-4"><label class="form-label">Total Missing</label><div>{{ $affectedHouseholds->sum('missing') }}</div></div>
        <div class="col-md-4"><label class="form-label">Total Fatalities</label><div>{{ $affectedHouseholds->sum('casualties') }}</div></div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header fw-bold">8. Official Signature</div>
    <div class="card-body">
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="border-top pt-2">Prepared by: {{ $calamity->reporter?->name ?? 'N/A' }}</div>
        </div>
        <div class="col-md-6">
          <div class="border-top pt-2">Verified by: Barangay Secretary</div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection