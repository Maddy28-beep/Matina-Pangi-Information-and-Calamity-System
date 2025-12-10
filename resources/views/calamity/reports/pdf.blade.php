<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Calamity Report</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
    h1 { font-size: 18px; margin: 0 0 8px; }
    h2 { font-size: 14px; margin: 18px 0 6px; }
    .section { margin-bottom: 12px; }
    .grid { display: table; width: 100%; }
    .grid .col { display: table-cell; padding-right: 10px; vertical-align: top; }
    .grid .col-3 { width: 33%; }
    .grid .col-2 { width: 50%; }
    .box { border: 1px solid #ddd; padding: 8px; }
    .photos img { height: 90px; margin-right: 6px; border: 1px solid #ccc; }
    .list li { margin-bottom: 4px; }
    .signature { margin-top: 24px; }
    .signature .sig { border-top: 1px solid #000; padding-top: 4px; width: 60%; }
    .header { text-align: center; margin-bottom: 16px; }
    .header .title { font-size: 20px; font-weight: bold; }
    .header .subtitle { font-size: 12px; color: #444; }
    .divider { height: 2px; background: #0a7a3b; margin: 8px 0 16px; }
    .meta { font-size: 11px; color: #555; margin-bottom: 12px; }
  </style>
</head>
<body>
  <div class="header">
    <div class="title">Barangay Matina Pangi</div>
    <div class="subtitle">City Hall, Davao City • Official Calamity Report</div>
    <div class="divider"></div>
  </div>
  <h1>Calamity Report</h1>
  <div class="meta">
    Prepared by: {{ $exporter ?? ($calamity->reporter?->name ?? 'N/A') }} • Date: {{ now()->format('F d, Y') }}
  </div>

  <div class="section">
    <h2>1. Calamity Incident Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Type of Calamity:</strong> {{ ucfirst($calamity->calamity_type) }}</div>
      <div class="col col-3"><strong>Date & Time:</strong> {{ optional($calamity->date_occurred)->format('Y-m-d') }}</div>
      <div class="col col-3"><strong>Severity Level:</strong> {{ $calamity->severity_level }}</div>
    </div>
    <div class="box" style="margin-top:6px"><strong>Affected Puroks/Areas:</strong> {{ $calamity->affected_areas ?? (is_array($calamity->affected_puroks)? implode(', ',$calamity->affected_puroks):'N/A') }}</div>
    <div class="box" style="margin-top:6px"><strong>Short Description:</strong> {{ $calamity->description ?? 'N/A' }}</div>
    @if(is_array($calamity->photos) && count($calamity->photos))
      <div class="photos" style="margin-top:6px">
        @foreach($calamity->photos as $p)
          <img src="{{ public_path($p) }}" alt="Incident Photo">
        @endforeach
      </div>
    @endif
  </div>

  <div class="section">
    <h2>2. Affected Population Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Total Affected Households:</strong> {{ $totalAffectedHouseholds }}</div>
      <div class="col col-3"><strong>Total Affected Residents:</strong> {{ $totalAffectedResidents }}</div>
    </div>
  </div>

  <div class="section">
    <h2>3. Evacuation Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Total Evacuees:</strong> {{ $totalEvacuees }}</div>
      <div class="col col-3"><strong>Total Families Evacuated:</strong> {{ $totalFamiliesEvacuated }}</div>
      <div class="col col-3"><strong>Evacuation Center Occupancy:</strong>
        @if($evacuationCenterOccupancy->count())
          @foreach($evacuationCenterOccupancy as $centerId => $count)
            Center #{{ $centerId }}: {{ $count }}@if(!$loop->last); @endif
          @endforeach
        @else
          No records
        @endif
      </div>
    </div>
    <div class="grid" style="margin-top:6px">
      <div class="col col-2"><strong>Center Capacity vs Occupants:</strong> Not recorded</div>
      <div class="col col-2"><strong>Duration of Evacuation:</strong> Not recorded</div>
    </div>
  </div>

  <div class="section">
    <h2>4. Rescue Operations Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Total Rescues:</strong> {{ $totalRescues }}</div>
    </div>
    @if($rescueSummaryByHousehold->count())
      <ul class="list" style="margin-top:6px">
        @foreach($rescueSummaryByHousehold as $ahId => $rescs)
          <li>
            HH-{{ optional($rescs->first()->affectedHousehold->household)->household_id }}:
            @foreach($rescs as $r)
              {{ optional($r->rescuer)->name ?? 'Unknown' }} • {{ optional($r->rescue_time)->format('Y-m-d H:i') }}@if($r->evacuationCenter) → {{ $r->evacuationCenter->name }} @endif @if(!$loop->last); @endif
            @endforeach
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="section">
    <h2>5. Relief Distribution Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Total Relief Goods Distributed:</strong> {{ $totalReliefDistributed }}</div>
    </div>
    @if($reliefSummaryPerHousehold->count())
      <ul class="list" style="margin-top:6px">
        @foreach($reliefSummaryPerHousehold as $hhId => $items)
          <li>
            HH-{{ optional($items->first()->household)->household_id }}:
            @php $byItem = $items->groupBy('relief_item_id'); @endphp
            @foreach($byItem as $itemId => $dist)
              {{ optional($dist->first()->item)->name }} ({{ $dist->sum('quantity') }})@if(!$loop->last), @endif
            @endforeach
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="section">
    <h2>6. Damage Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Partially Damaged Houses:</strong> {{ $partiallyDamaged }}</div>
      <div class="col col-3"><strong>Totally Damaged Houses:</strong> {{ $totallyDamaged }}</div>
      <div class="col col-3"><strong>Basic Cost Estimate:</strong> {{ number_format($estimatedDamageCost,2) }}</div>
    </div>
  </div>

  <div class="section">
    <h2>7. Casualty Report</h2>
    <div class="grid">
      <div class="col col-3"><strong>Total Injured:</strong> {{ $calamity->getTotalInjuredAttribute() }}</div>
      <div class="col col-3"><strong>Total Missing:</strong> {{ $affectedHouseholds->sum('missing') }}</div>
      <div class="col col-3"><strong>Total Fatalities:</strong> {{ $affectedHouseholds->sum('casualties') }}</div>
    </div>
  </div>

  <div class="section signature">
    <div class="grid">
      <div class="col col-2"><div class="sig">Prepared by: {{ $exporter ?? ($calamity->reporter?->name ?? 'N/A') }}</div></div>
      <div class="col col-2"><div class="sig">Certified by: {{ $barangayCaptain ?? 'Barangay Captain' }}</div></div>
    </div>
  </div>
</body>
</html>
