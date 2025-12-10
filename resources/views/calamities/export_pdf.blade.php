<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Calamities</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background: #f6f6f6; }
  </style>
  </head>
<body>
  <h2>Calamity Incidents</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Date</th>
        <th>Severity</th>
        <th>Affected Areas</th>
        <th>Affected Puroks</th>
        <th>Total Affected</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($calamities as $c)
        <tr>
          <td>{{ $c->id }}</td>
          <td>{{ $c->calamity_name }}</td>
          <td>{{ ucfirst($c->calamity_type) }}</td>
          <td>{{ optional($c->date_occurred)->format('Y-m-d') }}</td>
          <td>{{ ucfirst($c->severity_level) }}</td>
          <td>{{ $c->affected_areas }}</td>
          <td>{{ is_array($c->affected_puroks) ? implode(', ', $c->affected_puroks) : '' }}</td>
          <td>{{ $c->affected_households_count ?? $c->affectedHouseholds()->count() }}</td>
          <td>{{ ucfirst($c->status) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>