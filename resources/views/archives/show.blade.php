@extends('layouts.app')

@section('title', 'Archive Details')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('archives.index') }}">Archives</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-archive"></i> Archive Details</h2>
  <div class="btn-group">
    <form action="{{ route('archives.destroy', $archive) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this archive? This cannot be undone.')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-trash"></i> Delete Permanently
      </button>
    </form>
    <a href="{{ route('archives.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Archive Information</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <strong>Module Type:</strong>
          <p><span class="badge bg-{{ $archive->module_badge_color }}">{{ $archive->module_type }}</span></p>
        </div>
        <div class="mb-3">
          <strong>Title:</strong>
          <p>{{ $archive->title }}</p>
        </div>
        <div class="mb-3">
          <strong>Original ID:</strong>
          <p><code>{{ $archive->original_id }}</code></p>
        </div>
        <div class="mb-3">
          <strong>Archived By:</strong>
          <p>{{ optional($archive->archivedBy)->name ?? 'System' }}</p>
        </div>
        <div class="mb-3">
          <strong>Archived At:</strong>
          <p>{{ $archive->archived_at->format('F d, Y h:i A') }}</p>
        </div>
        @if($archive->reason)
        <div class="mb-3">
          <strong>Reason:</strong>
          <p>{{ $archive->reason }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-database"></i> Archived Data</h5>
      </div>
      <div class="card-body">
        <div class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
          <pre class="mb-0" style="font-size: 0.85rem;">{{ json_encode($archive->data, JSON_PRETTY_PRINT) }}</pre>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
@endsection
