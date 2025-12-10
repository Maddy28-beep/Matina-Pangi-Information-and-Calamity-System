@extends('layouts.app')

@section('title', 'Register Household')

@section('content')<div class="ds-page">
    <h2><i class="bi bi-house-add"></i> Register New Household</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}">Households</a></li>
            <li class="breadcrumb-item active">Register</li>
        </ol>
    </nav>
</div>

<form action="{{ auth()->user()->isStaff() ? route('staff.households.store') : route('households.store') }}" method="POST" id="householdForm">
    @csrf
    
    <!-- Household Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-house"></i> Household Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="household_type" class="form-label">Household Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="household_type" name="household_type" required>
                        <option value="">Select Type</option>
                        <option value="solo" {{ old('household_type') == 'solo' ? 'selected' : '' }}>Solo (Living Alone)</option>
                        <option value="family" {{ old('household_type') == 'family' ? 'selected' : '' }}>Head of the Family</option>
                        <option value="extended" {{ old('household_type') == 'extended' ? 'selected' : '' }}>Extended Family</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="total_members" class="form-label">Total Members <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_members" name="total_members" 
                           value="{{ old('total_members', 1) }}" min="1" max="20" required>
                    <small class="text-muted">Including household head</small>
                </div>
                
                <!-- Parent Household - Only for Extended Family (SHOWN FIRST) -->
                <div class="col-md-12" id="parent_household_container" style="display: none;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Extended Family:</strong> Select the main household that this extended family belongs to. Address and household details will be inherited from the parent household.
                    </div>
                    <label for="parent_household_id" class="form-label">
                        <i class="bi bi-diagram-3"></i> Search & Select Parent Household <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="parent_household_id" name="parent_household_id" style="width: 100%;">
                        <option value="">-- Type to search household --</option>
                        @foreach($households as $household)
                            <option value="{{ $household->id }}" 
                                    data-address="{{ $household->address }}"
                                    data-purok="{{ $household->purok }}"
                                    data-housing-type="{{ $household->housing_type }}"
                                    data-has-electricity="{{ $household->has_electricity }}"
                                    data-electric-account="{{ $household->electric_account_number }}"
                                    {{ old('parent_household_id') == $household->id ? 'selected' : '' }}>
                                {{ $household->household_id }} - {{ $household->head ? $household->head->full_name : 'N/A' }} ({{ $household->full_address }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-search"></i> Start typing the household ID, head name, or address to search
                    </small>
                </div>
                
                <!-- Regular Household Fields (Hidden for Extended Family) -->
                <div id="regular_household_fields">
                    <input type="hidden" id="purok" name="purok" value="{{ old('purok') }}">
                    
                    <div class="col-md-12">
                        <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <select class="form-select" id="address" name="address" required style="margin-bottom: 10px;">
                                <option value="">Select Address</option>
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr }}" {{ old('address') == $addr ? 'selected' : '' }}>
                                        {{ $addr }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Purok will be added automatically from your selection above.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="housing_type" class="form-label">Housing Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="housing_type" name="housing_type" required>
                            <option value="owned" {{ old('housing_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                            <option value="rented" {{ old('housing_type') == 'rented' ? 'selected' : '' }}>Rented</option>
                            <option value="rent-free" {{ old('housing_type') == 'rent-free' ? 'selected' : '' }}>Rent-Free</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Electricity</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_electricity" name="has_electricity" 
                                   value="1" {{ old('has_electricity', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_electricity">
                                Has Electricity Connection
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="electric_account_number" class="form-label">Electric Account Number</label>
                        <input type="text" class="form-control" id="electric_account_number" 
                               name="electric_account_number" value="{{ old('electric_account_number') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Household Head Information -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Household Head Information</h5>
        </div>
        <div class="card-body">
            @include('households.partials.resident-form', ['prefix' => 'head', 'isHead' => true])
        </div>
    </div>
    
    <!-- Members Forms Container -->
    <div id="membersContainer"></div>
    
    <!-- Submit Button -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ auth()->user()->isStaff() ? route('staff.households.index') : route('households.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Register Household
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Address to Purok mapping (provided by backend)
window.addressToPurok = window.addressToPurok || {};

$(document).ready(function() {
    // Initialize Select2 on parent household dropdown with modal-style display
    $('#parent_household_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Type to search household --',
        allowClear: true,
        dropdownCssClass: 'select2-dropdown-modal',
        language: {
            searching: function() {
                return 'Searching households...';
            },
            noResults: function() {
                return 'No households found';
            }
        }
    });
    
    // Add backdrop when dropdown opens
    $('#parent_household_id').on('select2:open', function() {
        if (!$('.select2-backdrop').length) {
            $('<div class="select2-backdrop"></div>').insertAfter('.select2-container');
        }
    });
    
    // Remove backdrop when dropdown closes
    $('#parent_household_id').on('select2:close', function() {
        $('.select2-backdrop').remove();
    });

    let memberCount = 0;
    
    // Update member forms when total_members changes
    $('#total_members').on('change', function() {
        updateMemberForms();
    });
    
    // Update on household type change
    $('#household_type').on('change', function() {
        const householdType = $(this).val();
        
        if (householdType === 'solo') {
            $('#total_members').val(1).prop('readonly', true);
            updateMemberForms();
        } else {
            $('#total_members').prop('readonly', false);
        }
        
        // Show/hide fields based on type
        if (householdType === 'extended') {
            // Extended Family: Show parent household selector, hide address fields
            $('#parent_household_container').slideDown();
            $('#regular_household_fields').slideUp();
            $('#parent_household_id').prop('required', true);
            // Make address fields not required since they'll be inherited
            $('#address').prop('required', false);
            $('#housing_type').prop('required', false);
        } else {
            // Solo/Family: Show address fields, hide parent household
            $('#parent_household_container').slideUp();
            $('#regular_household_fields').slideDown();
            $('#parent_household_id').val('').prop('required', false);
            // Make address fields required
            $('#address').prop('required', true);
            $('#housing_type').prop('required', true);
        }
    });
    
    // Auto-fill household details when parent household is selected
    $('#parent_household_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            // Get data from selected option
            const address = selectedOption.data('address');
            const purok = selectedOption.data('purok');
            const housingType = selectedOption.data('housing-type');
            const hasElectricity = selectedOption.data('has-electricity');
            const electricAccount = selectedOption.data('electric-account');
            
            // Set values (these fields are hidden but still submitted)
            $('#address').val(address);
            $('#purok').val(purok);
            $('#housing_type').val(housingType);
            $('#has_electricity').prop('checked', hasElectricity == 1);
            $('#electric_account_number').val(electricAccount || '');
        }
    });
    
    // Initialize on page load
    if ($('#household_type').val() === 'extended') {
        $('#parent_household_container').show();
        $('#regular_household_fields').hide();
        $('#parent_household_id').prop('required', true);
        $('#address').prop('required', false);
        $('#housing_type').prop('required', false);
    } else {
        $('#regular_household_fields').show();
    }
    
    function updateMemberForms() {
        const totalMembers = parseInt($('#total_members').val()) || 1;
        const membersNeeded = totalMembers - 1; // Subtract household head
        
        const container = $('#membersContainer');
        
        // Remove excess member forms
        if (memberCount > membersNeeded) {
            for (let i = memberCount; i > membersNeeded; i--) {
                $(`#member-${i}`).remove();
            }
        }
        
        // Add new member forms
        if (memberCount < membersNeeded) {
            for (let i = memberCount + 1; i <= membersNeeded; i++) {
                addMemberForm(i);
            }
        }
        
        memberCount = membersNeeded;
    }
    
    function addMemberForm(index) {
        const formHtml = `
            <div class="card mb-4" id="member-${index}">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Member ${index} Information</h5>
                </div>
                <div class="card-body">
                    ${generateMemberFormFields(index)}
                </div>
            </div>
        `;
        
        $('#membersContainer').append(formHtml);
        
        // Add event listeners for the newly added member form
        $(`#member_${index}_pwd`).on('change', function() {
            const card = $(this).closest('.card-body');
            card.find('.member-pwd-id, .member-disability-type').prop('disabled', !this.checked);
        });
        
        $(`#member_${index}_4ps`).on('change', function() {
            const card = $(this).closest('.card-body');
            card.find('.member-4ps-id').prop('disabled', !this.checked);
        });
        
        $(`#member_${index}_voter`).on('change', function() {
            const card = $(this).closest('.card-body');
            card.find('.member-precinct').prop('disabled', !this.checked);
        });
    }
    
    function generateMemberFormFields(index) {
        return `
            <div class="row g-3">
                <!-- Personal Information -->
                <div class="col-md-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="members[${index-1}][first_name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="members[${index-1}][middle_name]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="members[${index-1}][last_name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Suffix</label>
                    <input type="text" class="form-control" name="members[${index-1}][suffix]" placeholder="Jr., Sr., III">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="members[${index-1}][birthdate]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sex <span class="text-danger">*</span></label>
                    <select class="form-select" name="members[${index-1}][sex]" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="members[${index-1}][civil_status]" required>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Household Role <span class="text-danger">*</span></label>
                    <select class="form-select" name="members[${index-1}][household_role]" required>
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="relative">Relative</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <!-- Contact -->
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" class="form-control" name="members[${index-1}][contact_number]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="members[${index-1}][email]">
                </div>
                
                <!-- Special Categories -->
                <div class="col-12"><hr><h6 class="text-warning"><i class="bi bi-star-fill"></i> Special Categories & IDs</h6></div>
                
                <div class="col-md-4">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input member-pwd-check" id="member_${index}_pwd" name="members[${index-1}][is_pwd]" value="1">
                        <label class="form-check-label fw-bold" for="member_${index}_pwd">Person with Disability (PWD)</label>
                    </div>
                    <input type="text" class="form-control mb-2 member-pwd-id" name="members[${index-1}][pwd_id]" placeholder="PWD ID Number" disabled>
                    <select class="form-select member-disability-type" name="members[${index-1}][disability_type]" disabled>
                        <option value="">Select Disability Type</option>
                        <option value="Visual Impairment">Visual Impairment</option>
                        <option value="Hearing Impairment">Hearing Impairment</option>
                        <option value="Speech Impairment">Speech Impairment</option>
                        <option value="Physical Disability">Physical Disability</option>
                        <option value="Mental/Intellectual Disability">Mental/Intellectual Disability</option>
                        <option value="Psychosocial Disability">Psychosocial Disability</option>
                        <option value="Multiple Disabilities">Multiple Disabilities</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input member-4ps-check" id="member_${index}_4ps" name="members[${index-1}][is_4ps_beneficiary]" value="1">
                        <label class="form-check-label fw-bold" for="member_${index}_4ps">4Ps Beneficiary</label>
                    </div>
                    <input type="text" class="form-control member-4ps-id" name="members[${index-1}][4ps_id]" placeholder="4Ps ID Number" disabled>
                </div>
                
                <div class="col-md-4">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input member-voter-check" id="member_${index}_voter" name="members[${index-1}][is_voter]" value="1">
                        <label class="form-check-label fw-bold" for="member_${index}_voter">Registered Voter</label>
                    </div>
                    <input type="text" class="form-control member-precinct" name="members[${index-1}][precinct_number]" placeholder="Precinct Number" disabled>
                </div>
                
                <div class="col-12"><hr><h6 class="text-primary"><i class="bi bi-briefcase"></i> Employment & Education</h6></div>
                <div class="col-md-4">
                    <label class="form-label">Occupation</label>
                    <input type="text" class="form-control" name="members[${index-1}][occupation]">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employment Status</label>
                    <select class="form-select" name="members[${index-1}][employment_status]">
                        <option value="">Select</option>
                        <option value="employed">Employed</option>
                        <option value="unemployed">Unemployed</option>
                        <option value="self-employed">Self-Employed</option>
                        <option value="student">Student</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monthly Income</label>
                    <input type="number" class="form-control" name="members[${index-1}][monthly_income]" step="0.01" min="0">
                </div>
            </div>
        `;
    }
    
    // Initialize on page load
    updateMemberForms();
    
    // Handle "Add New Address" option
    document.getElementById('address').addEventListener('change', function() {
        const newAddressInput = document.getElementById('new_address');
        if (this.value === '__new__') {
            newAddressInput.classList.remove('d-none');
            newAddressInput.required = true;
            newAddressInput.focus();
        } else {
            newAddressInput.classList.add('d-none');
            newAddressInput.required = false;
            newAddressInput.value = '';
        }
    });
    
    // Before form submit, use new address if provided
    document.getElementById('householdForm').addEventListener('submit', function(e) {
        const addressSelect = document.getElementById('address');
        const newAddressInput = document.getElementById('new_address');
        
        if (addressSelect.value === '__new__' && newAddressInput.value.trim()) {
            // Create a hidden input with the new address
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'address';
            hiddenInput.value = newAddressInput.value.trim();
            this.appendChild(hiddenInput);
            
            // Remove name from select to avoid conflict
            addressSelect.removeAttribute('name');
        }
    });

    $(document).on('focus mousedown', '.form-select', function() {
        const rect = this.getBoundingClientRect();
        const vh = window.innerHeight || document.documentElement.clientHeight;
        const needed = 280;
        const gap = vh - rect.bottom;
        if (gap < needed) {
            window.scrollBy({ top: needed - gap + 20, behavior: 'smooth' });
        }
    });
    // Auto-set purok when address is selected
    const addressSelect = document.getElementById('address');
    const purokInput = document.getElementById('purok');
    function updatePurokField() {
        const selectedAddress = addressSelect.value;
        if (window.addressToPurok && window.addressToPurok[selectedAddress]) {
            purokInput.value = window.addressToPurok[selectedAddress];
        } else {
            purokInput.value = '';
        }
    }
    addressSelect.addEventListener('change', updatePurokField);
    // Set on page load if address is pre-selected
    updatePurokField();

    // Duplicate name check (real-time) for household head and dynamic members
    const dupCheckUrl = '{{ route('residents.check-duplicate') }}';
    const submitBtn = document.querySelector('#householdForm button[type="submit"]');
    let hasDup = false;
    function debounce(fn, delay){ let t; return function(){ const args=arguments; const ctx=this; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,args); }, delay||300); }; }
    function renderError(el, msg, url){
        let box = el.parentElement.querySelector('.dup-error');
        if(!box){ box = document.createElement('div'); box.className = 'dup-error alert alert-danger mt-2 p-2'; el.parentElement.appendChild(box); }
        box.innerHTML = msg + (url ? ` <a href="${url}" target="_blank" class="btn btn-sm btn-outline-danger ms-2">View record</a>` : '');
        box.style.display = 'block';
    }
    function clearError(el){ const box = el.parentElement.querySelector('.dup-error'); if(box){ box.style.display='none'; box.innerHTML=''; } }
    function checkName(first, last){
        first = (first||'').trim(); last = (last||'').trim();
        if(!first || !last){ return Promise.resolve({exists:false}); }
        const url = dupCheckUrl + `?first_name=${encodeURIComponent(first)}&last_name=${encodeURIComponent(last)}`;
        return fetch(url, { headers: { 'Accept':'application/json' } }).then(r => r.json()).catch(() => ({exists:false}));
    }
    function updateSubmitDisabled(){ submitBtn && (submitBtn.disabled = !!hasDup); }

    // Head fields
    const headFirst = document.getElementById('head_first_name');
    const headLast = document.getElementById('head_last_name');
    function headHandler(){ checkName(headFirst.value, headLast.value).then(res => {
        if(res && res.exists){
            hasDup = true; updateSubmitDisabled();
            renderError(headLast, `Resident '${headFirst.value.trim()} ${headLast.value.trim()}' already exists in the system`, res.url);
        } else {
            hasDup = false; updateSubmitDisabled(); clearError(headLast);
        }
    }); }
    if(headFirst && headLast){ headFirst.addEventListener('input', debounce(headHandler, 400)); headLast.addEventListener('input', debounce(headHandler, 400)); }

    // Dynamic member fields
    function attachMemberDupCheck(card){
        const first = card.querySelector('input[name^="members"][name$="[first_name]"]');
        const last = card.querySelector('input[name^="members"][name$="[last_name]"]');
        if(!first || !last) return;
        const handler = debounce(function(){ checkName(first.value, last.value).then(res => {
            if(res && res.exists){ hasDup = true; updateSubmitDisabled(); renderError(last, `Resident '${first.value.trim()} ${last.value.trim()}' already exists in the system`, res.url); }
            else { hasDup = false; updateSubmitDisabled(); clearError(last); }
        }); }, 400);
        first.addEventListener('input', handler); last.addEventListener('input', handler);
    }
    // Attach for existing members
    document.querySelectorAll('#membersContainer .card').forEach(attachMemberDupCheck);
    // Attach after new member forms are added
    const membersContainer = document.getElementById('membersContainer');
    const observer = new MutationObserver(function(muts){ muts.forEach(function(m){ m.addedNodes.forEach(function(n){ if(n.nodeType===1 && n.classList.contains('card')) attachMemberDupCheck(n); }); }); });
    observer.observe(membersContainer, { childList: true });
});
</script>
@endpush
