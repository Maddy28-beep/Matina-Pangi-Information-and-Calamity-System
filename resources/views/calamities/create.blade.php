@extends('layouts.app')

@section('title', 'Record Calamity')

@push('styles')
<style>
#affected_puroks {
    min-height: 150px;
}

#affected_puroks option {
    padding: 8px 12px;
    margin: 2px 0;
}

#affected_puroks option:hover {
    background-color: #4A6F52 !important;
    color: white;
}

#affected_puroks option:checked {
    background-color: #4A6F52;
    color: white;
}
</style>
@endpush

@section('content')
<div class="ds-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Record Calamity</h2>
    <a href="{{ route('calamities.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('calamities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="calamity_name" class="form-label">Calamity Name <span class="text-danger">*</span></label>
                    <input type="text" name="calamity_name" id="calamity_name" class="form-control" required placeholder="e.g., Typhoon Odette">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="calamity_type" class="form-label">Calamity Type <span class="text-danger">*</span></label>
                    <select name="calamity_type" id="calamity_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="typhoon">Typhoon</option>
                        <option value="flood">Flood</option>
                        <option value="earthquake">Earthquake</option>
                        <option value="fire">Fire</option>
                        <option value="landslide">Landslide</option>
                        <option value="drought">Drought</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="date_occurred" class="form-label">Date Occurred <span class="text-danger">*</span></label>
                    <input type="date" name="date_occurred" id="date_occurred" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="severity_level" class="form-label">Severity Level <span class="text-danger">*</span></label>
                    <select name="severity_level" id="severity_level" class="form-select" required>
                        <option value="">Select Severity</option>
                        <option value="minor">Minor</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                        <option value="catastrophic">Catastrophic</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="affected_puroks" class="form-label">Affected Puroks/Areas</label>
                    <select name="affected_puroks[]" id="affected_puroks" class="form-select" multiple size="6">
                        @foreach($puroks as $purok)
                            <option value="{{ $purok }}">{{ $purok }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) to select multiple puroks.</div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Describe the calamity..."></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="response_actions" class="form-label">Response Actions</label>
                    <textarea name="response_actions" id="response_actions" rows="3" class="form-control" placeholder="List response actions taken..."></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="ongoing">Ongoing</option>
                        <option value="monitoring">Monitoring</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('calamities.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Record Calamity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
