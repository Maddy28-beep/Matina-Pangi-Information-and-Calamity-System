@extends('layouts.app')

@section('title', 'Archives')

@section('content')<div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Archives</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="bi bi-archive"></i> Global Archives</h2>
</div>

<form method="GET" action="{{ route('archives.index') }}" class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small">Search</label>
        <input type="text" name="search" class="form-control" placeholder="Search by title" value="{{ request('search') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label small">Module Type</label>
        <select name="module_type" class="form-select">
          <option value="">All Modules</option>
          @foreach($moduleTypes as $type)
            <option value="{{ $type }}" {{ request('module_type') == $type ? 'selected' : '' }}>
              {{ $type }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 align-self-end">
        <button class="btn btn-outline-secondary w-100"><i class="bi bi-search"></i> Filter</button>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if($archives->count())
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>Module Type</th>
            <th>Title</th>
            <th>Archived By</th>
            <th>Archived At</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($archives as $archive)
          <tr class="clickable-row" data-href="{{ route('archives.show', $archive) }}" style="cursor: pointer;">
            <td>
              <span class="badge bg-{{ $archive->module_badge_color }}">
                {{ $archive->module_type }}
              </span>
            </td>
            <td><strong class="text-dark">{{ $archive->title }}</strong></td>
            <td>{{ optional($archive->archivedBy)->name ?? 'System' }}</td>
            <td>{{ $archive->archived_at->format('M d, Y h:i A') }}</td>
            <td class="text-end" onclick="event.stopPropagation()">
              <div class="btn-group btn-group-sm">
                <form action="{{ route('archives.destroy', $archive) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this archive? This cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Delete Permanently">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $archives->links() }}</div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-archive" style="font-size:64px;color:#ccc;"></i>
      <p class="text-muted mt-3">No archived items found.</p>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
