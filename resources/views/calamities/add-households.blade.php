@extends('layouts.app')

@section('title', 'Add Affected Household')

@section('content')
<div class="ds-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-house"></i> Add Affected Household</h2>
    <a href="{{ route('calamities.show', $calamity) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Calamity
    </a>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> <strong>Calamity:</strong> {{ $calamity->calamity_name }} ({{ $calamity->date_occurred->format('M d, Y') }})
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('calamities.add-household', $calamity) }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="household_id" class="form-label">Household <span class="text-danger">*</span></label>
                    <select name="household_id" id="household_id" class="form-select" required>
                        <option value="">Select Household</option>
                        @foreach($households as $household)
                            <option value="{{ $household->id }}">
                                {{ $household->household_number }} - {{ $household->address }} ({{ $household->purok->purok_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="damage_level" class="form-label">Damage Level <span class="text-danger">*</span></label>
                    <select name="damage_level" id="damage_level" class="form-select" required>
                        <option value="">Select Damage Level</option>
                        <option value="minor">Minor</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                        <option value="total">Total</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estimated_damage_cost" class="form-label">Estimated Damage Cost</label>
                    <input type="number" name="estimated_damage_cost" id="estimated_damage_cost" step="0.01" class="form-control" placeholder="0.00">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="assistance_needed" class="form-label">Assistance Needed</label>
                    <textarea name="assistance_needed" id="assistance_needed" rows="3" class="form-control" placeholder="List assistance needed..."></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="assistance_provided" class="form-label">Assistance Provided</label>
                    <textarea name="assistance_provided" id="assistance_provided" rows="3" class="form-control" placeholder="List assistance provided..."></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="form-control"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('calamities.show', $calamity) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Add Household
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
