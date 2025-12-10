@extends('layouts.app')
@section('title', 'Archived Calamity Incidents')
@section('content')
<div class="ds-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-archive"></i> Archived Calamity Incidents</h2>
    <a href="{{ route('calamities.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      @if($calamities->count())
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Type</th>
              <th>Date Occurred</th>
              <th>Severity</th>
              <th>Deleted At</th>
              <th class="text-end">Actions</th>
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
              <td>{{ optional($c->deleted_at)->format('Y-m-d H:i') }}</td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <form method="POST" action="{{ route('calamities.restore', $c->id) }}" class="me-2 d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" title="Restore"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                  </form>
                  <form method="POST" action="{{ route('calamities.delete', $c->id) }}" onsubmit="return confirm('Permanently delete this incident? This cannot be undone.');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" title="Delete Permanently"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $calamities->links() }}</div>
      @else
      <div class="text-center py-5 text-muted">No archived incidents.</div>
      @endif
    </div>
  </div>
</div>
@endsection