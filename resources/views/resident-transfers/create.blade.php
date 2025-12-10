@extends('layouts.app')

@section('title', 'Request Resident Transfer')

@section('content')<div class="ds-page">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-arrow-left-right"></i> Request Resident Transfer</h2>
            <p class="text-muted">Submit a transfer request for a resident</p>
        </div>
    </div>

    @if(isset($resident))
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Selected Resident</h5>
        </div>
        <div class="card-body">
            @if($resident->household)
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $resident->full_name }}</p>
                        <p><strong>Resident ID:</strong> {{ $resident->resident_id }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Current Household:</strong> {{ $resident->household->household_id }}</p>
                        <p><strong>Current Purok:</strong> {{ $resident->household->purok }}</p>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>{{ $resident->full_name }}</strong> ({{ $resident->resident_id }}) has no household. Assign to household first.
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Transfer Request Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ auth()->user()->isSecretary() ? route('resident-transfers.store') : route('staff.resident-transfers.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    @if(!isset($resident))
                    <div class="col-12">
                        <label class="form-label fw-bold">Select Resident <span class="text-danger">*</span></label>
                        <select name="resident_id" id="resident_id" class="form-select" required>
                            <option value="">-- Select Resident --</option>
                            @foreach($residents as $r)
                                <option value="{{ $r['id'] }}" {{ old('resident_id') == $r['id'] ? 'selected' : '' }}>
                                    {{ $r['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="resident_id" value="{{ $resident->id }}">
                    @endif

                    <div class="col-12"><hr><h6 class="text-primary">Transfer Details</h6></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Transfer Type <span class="text-danger">*</span></label>
                        <select name="transfer_type" id="transfer_type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="internal" {{ old('transfer_type') == 'internal' ? 'selected' : '' }}>Internal (Within Matina Pangi)</option>
                            <option value="external" {{ old('transfer_type') == 'external' ? 'selected' : '' }}>External (Moving Out)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Transfer Date <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" class="form-control" required value="{{ old('transfer_date', date('Y-m-d')) }}">
                    </div>

                    <!-- Internal Transfer Fields -->
                    <div id="internal-fields" style="display: none;" class="col-12">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Transfer Destination <span class="text-danger">*</span></label>
                                <select name="internal_transfer_type" id="internal_transfer_type" class="form-select">
                                    <option value="">-- Select Option --</option>
                                    <option value="join_existing" {{ old('internal_transfer_type') == 'join_existing' ? 'selected' : '' }}>
                                        🏘️ Join Existing Household
                                    </option>
                                    <option value="create_new" {{ old('internal_transfer_type') == 'create_new' ? 'selected' : '' }}>
                                        🏗️ Create New Household (Building Own House)
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Join Existing Household -->
                            <div id="join-existing-fields" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Select Household <span class="text-danger">*</span></label>
                                        <select name="new_household_id" id="new_household_id" class="form-select">
                                            <option value="">-- Select Household --</option>
                                            @foreach($households as $h)
                                                <option value="{{ $h['id'] }}" {{ old('new_household_id') == $h['id'] ? 'selected' : '' }}>
                                                    {{ $h['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12" id="sub-family-selection" style="display: none;">
                                        <label class="form-label fw-bold">Select Head to Join <span class="text-danger">*</span></label>
                                        <p class="text-muted small mb-2">Choose which family group this resident will join</p>
                                        <select name="sub_family_id" id="sub_family_id" class="form-select">
                                            <option value="">-- Select Household First --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Create New Household -->
                            <div id="create-new-fields" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="hidden" name="new_purok" id="new_purok" value="{{ old('new_purok') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">New Street Address <span class="text-danger">*</span></label>
                                        <select name="new_address_select" id="new_address_select" class="form-select mb-2">
                                            <option value="">🏘️ Select Existing Street</option>
                                            @if(isset($streetAddresses))
                                                @foreach($streetAddresses as $street)
                                                    <option value="{{ $street }}" {{ old('new_address') == $street ? 'selected' : '' }}>
                                                        {{ $street }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input type="hidden" name="new_address" id="new_address" value="{{ old('new_address') }}">
                                        <small class="text-muted">Purok will be added automatically from your selection above.</small>
                                    @push('scripts')
                                    <script>
                                    // Address to Purok mapping (provided by backend)
                                    window.addressToPurok = window.addressToPurok || {};
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const addressSelect = document.getElementById('new_address_select');
                                        const purokInput = document.getElementById('new_purok');
                                        const newAddressInput = document.getElementById('new_address');
                                        function updatePurokField() {
                                            const selectedAddress = addressSelect.value;
                                            if (window.addressToPurok && window.addressToPurok[selectedAddress]) {
                                                purokInput.value = window.addressToPurok[selectedAddress];
                                            } else {
                                                purokInput.value = '';
                                            }
                                        }
                                        function updateNewAddressField() {
                                            newAddressInput.value = addressSelect.value;
                                        }
                                        if (addressSelect) {
                                            addressSelect.addEventListener('change', updatePurokField);
                                            addressSelect.addEventListener('change', updateNewAddressField);
                                            // Set on page load if address is pre-selected
                                            updatePurokField();
                                            updateNewAddressField();
                                        }
                                    });
                                    </script>
                                    @endpush
                                    </div>
                                    
                                    <div class="col-12"><hr></div>
                                    
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Who will be the Household Head? <span class="text-danger">*</span></label>
                                        <div class="alert alert-warning mb-3">
                                            <i class="bi bi-info-circle"></i> <strong>Important:</strong> Select who will be the head of the new household.
                                        </div>
                                        <select name="new_household_head_option" id="new_household_head_option" class="form-select">
                                            <option value="">-- Select Option --</option>
                                            <option value="self" {{ old('new_household_head_option') == 'self' ? 'selected' : '' }}>
                                                👤 I will be the Household Head (Transferring resident becomes head)
                                            </option>
                                            <option value="existing_resident" {{ old('new_household_head_option') == 'existing_resident' ? 'selected' : '' }}>
                                                👥 Another Existing Resident will be the Head
                                            </option>
                                            <option value="new_person" {{ old('new_household_head_option') == 'new_person' ? 'selected' : '' }}>
                                                ➕ A New Person (Not yet registered as resident) will be the Head
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <!-- Select Existing Resident as Head -->
                                    <div id="existing-resident-head-field" style="display: none;" class="col-12">
                                        <label class="form-label fw-bold">Select Existing Resident <span class="text-danger">*</span></label>
                                        <select name="new_household_head_id" id="new_household_head_id" class="form-select">
                                            <option value="">-- Select Resident --</option>
                                            @foreach($residents as $r)
                                                <option value="{{ $r['id'] }}" {{ old('new_household_head_id') == $r['id'] ? 'selected' : '' }}>
                                                    {{ $r['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">This resident will be transferred to the new household as the head, along with the requesting resident.</small>
                                    </div>
                                    
                                    <!-- New Person as Head -->
                                    <div id="new-person-head-field" style="display: none;" class="col-12">
                                        <div class="alert alert-primary">
                                            <i class="bi bi-person-plus"></i> <strong>Add New Person:</strong> After submitting this transfer request, you'll be redirected to register the new person who will be the household head.
                                            <br><span class="text-muted small">You will fill out the new resident's details on the next page.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- External Transfer Fields -->
                    <div id="external-fields" style="display: none;" class="col-12">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Destination Address <span class="text-danger">*</span></label>
                                <textarea name="destination_address" class="form-control" rows="2">{{ old('destination_address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Barangay</label>
                                <input type="text" name="destination_barangay" class="form-control" value="{{ old('destination_barangay') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Municipality/City</label>
                                <input type="text" name="destination_municipality" class="form-control" value="{{ old('destination_municipality') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Province</label>
                                <input type="text" name="destination_province" class="form-control" value="{{ old('destination_province') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-12"><hr><h6 class="text-primary">Reason for Transfer</h6></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Reason Category <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">-- Select Reason --</option>
                            <option value="work" {{ old('reason') == 'work' ? 'selected' : '' }}>Work/Employment</option>
                            <option value="marriage" {{ old('reason') == 'marriage' ? 'selected' : '' }}>Marriage</option>
                            <option value="school" {{ old('reason') == 'school' ? 'selected' : '' }}>Education</option>
                            <option value="family" {{ old('reason') == 'family' ? 'selected' : '' }}>Family Reasons</option>
                            <option value="health" {{ old('reason') == 'health' ? 'selected' : '' }}>Health Reasons</option>
                            <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Detailed Reason <span class="text-danger">*</span></label>
                        <textarea name="reason_for_transfer" class="form-control" rows="3" required placeholder="Please provide a detailed explanation for this transfer request...">{{ old('reason_for_transfer') }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Submit Transfer Request
                    </button>
                    <a href="{{ auth()->user()->isSecretary() ? route('resident-transfers.index') : route('staff.resident-transfers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferType = document.getElementById('transfer_type');
    const internalFields = document.getElementById('internal-fields');
    const externalFields = document.getElementById('external-fields');
    const internalTransferType = document.getElementById('internal_transfer_type');
    const joinExistingFields = document.getElementById('join-existing-fields');
    const createNewFields = document.getElementById('create-new-fields');
    const newHouseholdSelect = document.getElementById('new_household_id');
    const subFamilySelection = document.getElementById('sub-family-selection');
    const subFamilySelect = document.getElementById('sub_family_id');

    function toggleFields() {
        const type = transferType.value;
        
        if (type === 'internal') {
            internalFields.style.display = 'block';
            externalFields.style.display = 'none';
            internalFields.querySelectorAll('select, input, textarea').forEach(el => {
                if (el.id === 'internal_transfer_type') {
                    el.required = true;
                }
            });
            externalFields.querySelectorAll('select, input, textarea').forEach(el => el.required = false);
        } else if (type === 'external') {
            internalFields.style.display = 'none';
            externalFields.style.display = 'block';
            internalFields.querySelectorAll('select, input, textarea').forEach(el => el.required = false);
            externalFields.querySelectorAll('select, input, textarea').forEach(el => el.required = true);
            // Hide internal sub-options
            joinExistingFields.style.display = 'none';
            createNewFields.style.display = 'none';
        } else {
            internalFields.style.display = 'none';
            externalFields.style.display = 'none';
            internalFields.querySelectorAll('select, input, textarea').forEach(el => el.required = false);
            externalFields.querySelectorAll('select, input, textarea').forEach(el => el.required = false);
        }
    }

    function toggleInternalOptions() {
        const type = internalTransferType.value;
        
        if (type === 'join_existing') {
            joinExistingFields.style.display = 'block';
            createNewFields.style.display = 'none';
            // Set required for join existing
            newHouseholdSelect.required = true;
            document.getElementById('new_purok').required = false;
            document.getElementById('new_address').required = false;
            if (document.getElementById('new_household_head_option')) {
                document.getElementById('new_household_head_option').required = false;
            }
        } else if (type === 'create_new') {
            joinExistingFields.style.display = 'none';
            createNewFields.style.display = 'block';
            // Set required for create new
            newHouseholdSelect.required = false;
            subFamilySelect.required = false;
            document.getElementById('new_purok').required = true;
            document.getElementById('new_address').required = true;
            if (document.getElementById('new_household_head_option')) {
                document.getElementById('new_household_head_option').required = true;
            }
            // Hide sub-family selection
            subFamilySelection.style.display = 'none';
        } else {
            joinExistingFields.style.display = 'none';
            createNewFields.style.display = 'none';
            newHouseholdSelect.required = false;
            document.getElementById('new_purok').required = false;
            document.getElementById('new_address').required = false;
            if (document.getElementById('new_household_head_option')) {
                document.getElementById('new_household_head_option').required = false;
            }
        }
    }
    
    // Handle household head option change
    const headOption = document.getElementById('new_household_head_option');
    const existingResidentField = document.getElementById('existing-resident-head-field');
    const newPersonField = document.getElementById('new-person-head-field');
    const newHouseholdHeadSelect = document.getElementById('new_household_head_id');
    
    // Handle street address selection
    const addressSelect = document.getElementById('new_address_select');
    const addressInput = document.getElementById('new_address');
    
    if (addressSelect) {
        addressSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                // Show text input for custom address
                addressInput.style.display = 'block';
                addressInput.required = true;
                addressInput.value = '';
                addressInput.focus();
            } else if (this.value) {
                // Use selected address
                addressInput.style.display = 'none';
                addressInput.required = false;
                addressInput.value = this.value;
            } else {
                // Nothing selected
                addressInput.style.display = 'none';
                addressInput.required = false;
                addressInput.value = '';
            }
        });
        
        // Trigger on page load if custom was previously selected
        if (addressSelect.value === 'custom') {
            addressInput.style.display = 'block';
            addressInput.required = true;
        } else if (addressSelect.value) {
            addressInput.value = addressSelect.value;
        }
    }
    
    if (headOption) {
        headOption.addEventListener('change', function() {
            const option = this.value;
            
            if (option === 'existing_resident') {
                existingResidentField.style.display = 'block';
                newPersonField.style.display = 'none';
                newHouseholdHeadSelect.required = true;
            } else if (option === 'new_person') {
                existingResidentField.style.display = 'none';
                newPersonField.style.display = 'block';
                newHouseholdHeadSelect.required = false;
            } else {
                existingResidentField.style.display = 'none';
                newPersonField.style.display = 'none';
                newHouseholdHeadSelect.required = false;
            }
        });
        
        // Trigger on page load if value exists
        if (headOption.value) {
            headOption.dispatchEvent(new Event('change'));
        }
    }

    // Load sub-families when household is selected
    if (newHouseholdSelect) {
        newHouseholdSelect.addEventListener('change', function() {
            const householdId = this.value;
            
            if (!householdId) {
                subFamilySelection.style.display = 'none';
                subFamilySelect.innerHTML = '<option value="">-- Select Household First --</option>';
                return;
            }
            
            // Fetch sub-families for selected household
            fetch(`/api/households/${householdId}/sub-families`)
                .then(response => response.json())
                .then(data => {
                    subFamilySelect.innerHTML = '<option value="">-- Select Head --</option>';
                    
                    data.forEach(family => {
                        const option = document.createElement('option');
                        option.value = family.id;
                        
                        if (family.is_primary_family) {
                            option.textContent = `⭐ PRIMARY HEAD: ${family.head_name}`;
                            option.style.fontWeight = 'bold';
                        } else {
                            option.textContent = `👤 CO-HEAD: ${family.head_name} (${family.sub_family_name})`;
                        }
                        
                        subFamilySelect.appendChild(option);
                    });
                    
                    subFamilySelection.style.display = 'block';
                    subFamilySelect.required = true;
                })
                .catch(error => {
                    console.error('Error loading sub-families:', error);
                    alert('Error loading family groups. Please try again.');
                });
        });
    }

    transferType.addEventListener('change', toggleFields);
    if (internalTransferType) {
        internalTransferType.addEventListener('change', toggleInternalOptions);
    }
    
    // Initialize on page load
    if (transferType.value) {
        toggleFields();
    }
    if (internalTransferType && internalTransferType.value) {
        toggleInternalOptions();
    }
});
</script>
@endpush
@endsection
