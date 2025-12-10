@extends('layouts.app')

@section('title', 'Add Members to Household')

@section('content')
<div class="ds-page">
    <h2 class="mb-4">Add Members to Household</h2>
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> New Member Information</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('residents.store-members', $household->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">How many members to add?</label>
                    <input type="number" id="member_count" class="form-control" min="1" value="1">
                </div>
                <div id="membersContainer"></div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Add Members
                    </button>
                    <a href="{{ auth()->user()->isStaff() ? route('staff.households.show', $household) : route('households.show', $household) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function createMemberForm(index) {
        // Advanced fields for each member, adapted from households/add-member.blade.php
        return `
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Member ${index + 1}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <label class="form-label fw-bold">Select Head <span class="text-danger">*</span></label>
                                <select name="members[${index}][sub_family_id]" class="form-select form-select-lg" required>
                                    <option value="">-- Select Head --</option>
                                    ${window.subFamiliesOptions || ''}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12"><hr></div>
                    <div class="col-md-12">
                        <label class="form-label">Street Address <span class="text-danger">*</span></label>
                        <select class="form-select address-select" name="members[${index}][address]" data-index="${index}" required>
                            <option value="">🏘️ Select Address</option>
                            ${window.addressOptions || ''}
                        </select>
                        <small class="text-muted">Purok will be added automatically from your selection above.</small>
                        <input type="hidden" name="members[${index}][purok]" class="purok-input" data-index="${index}" value="">
                    </div>
                    <!-- Basic fields removed as per request. Only advanced/detailed fields remain. -->
                    <!-- Additional Personal Info -->
                    <div class="col-md-4">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" name="members[${index}][place_of_birth]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="members[${index}][nationality]" class="form-control" value="Filipino">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Religion</label>
                        <input type="text" name="members[${index}][religion]" class="form-control">
                    </div>
                    <div class="col-12"><hr><h6 class="text-primary"><i class="bi bi-briefcase"></i> Employment & Education</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="members[${index}][occupation]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Employment Status</label>
                        <select name="members[${index}][employment_status]" class="form-select">
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
                        <input type="number" name="members[${index}][monthly_income]" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employer Name</label>
                        <input type="text" name="members[${index}][employer_name]" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Educational Attainment</label>
                        <select name="members[${index}][educational_attainment]" class="form-select">
                            <option value="">Select</option>
                            <option value="No Formal Education">No Formal Education</option>
                            <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                            <option value="Elementary Graduate">Elementary Graduate</option>
                            <option value="High School Undergraduate">High School Undergraduate</option>
                            <option value="High School Graduate">High School Graduate</option>
                            <option value="College Undergraduate">College Undergraduate</option>
                            <option value="College Graduate">College Graduate</option>
                            <option value="Vocational">Vocational</option>
                            <option value="Post Graduate">Post Graduate</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">School Name (if student)</label>
                        <input type="text" name="members[${index}][school_name]" class="form-control">
                    </div>
                    <div class="col-12"><hr><h6 class="text-success"><i class="bi bi-heart-pulse"></i> Health Information</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Type</label>
                        <select name="members[${index}][blood_type]" class="form-select">
                            <option value="">Select</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Medical Conditions</label>
                        <textarea name="members[${index}][medical_conditions]" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12"><hr><h6 class="text-warning"><i class="bi bi-star-fill"></i> Special Categories & IDs</h6></div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="members[${index}][is_pwd]" class="form-check-input pwd-checkbox" data-index="${index}" value="1">
                            <label class="form-check-label fw-bold">Person with Disability (PWD)</label>
                        </div>
                        <input type="text" name="members[${index}][pwd_id]" class="form-control mb-2 pwd-id-input" placeholder="PWD ID Number" data-index="${index}" disabled>
                        <select name="members[${index}][disability_type]" class="form-select disability-type-input" data-index="${index}" disabled>
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
                            <input type="checkbox" name="members[${index}][is_4ps_beneficiary]" class="form-check-input fourps-checkbox" data-index="${index}" value="1">
                            <label class="form-check-label fw-bold">4Ps Beneficiary</label>
                        </div>
                        <input type="text" name="members[${index}][4ps_id]" class="form-control fourps-id-input" placeholder="4Ps ID Number" data-index="${index}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Voting Status</label>
                        <select name="members[${index}][is_voter]" class="form-select mb-2 voting-status-select" data-index="${index}" disabled>
                            <option value="">-- Select Age First --</option>
                        </select>
                        <input type="text" name="members[${index}][precinct_number]" class="form-control precinct-input" placeholder="Precinct Number (if registered)" data-index="${index}" disabled>
                        <small class="text-muted voting-hint" data-index="${index}">Determined by age</small>
                    </div>
                    <div class="col-12"><hr><h6 class="text-info"><i class="bi bi-chat-square-text"></i> Additional Information</h6></div>
                    <div class="col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="members[${index}][remarks]" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
        `;
    }
    function updateMemberForms() {
        const count = parseInt(document.getElementById('member_count').value) || 1;
        const container = document.getElementById('membersContainer');
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            container.innerHTML += createMemberForm(i);
        }
        attachDynamicLogic();
    }

    function attachDynamicLogic() {
        // Address to Purok
        document.querySelectorAll('.address-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const idx = this.getAttribute('data-index');
                const purokInput = document.querySelector(`.purok-input[data-index='${idx}']`);
                if (window.addressToPurok && window.addressToPurok[this.value]) {
                    purokInput.value = window.addressToPurok[this.value];
                } else {
                    purokInput.value = '';
                }
            });
        });
        // Voting status
        document.querySelectorAll('.birthdate-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const idx = this.getAttribute('data-index');
                calculateVotingStatus(idx);
            });
            input.addEventListener('blur', function() {
                const idx = this.getAttribute('data-index');
                calculateVotingStatus(idx);
            });
        });
        // PWD
        document.querySelectorAll('.pwd-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const idx = this.getAttribute('data-index');
                const pwdIdInput = document.querySelector(`.pwd-id-input[data-index='${idx}']`);
                const disabilityTypeInput = document.querySelector(`.disability-type-input[data-index='${idx}']`);
                if (this.checked) {
                    pwdIdInput.disabled = false;
                    disabilityTypeInput.disabled = false;
                } else {
                    pwdIdInput.disabled = true;
                    disabilityTypeInput.disabled = true;
                    pwdIdInput.value = '';
                    disabilityTypeInput.value = '';
                }
            });
        });
        // 4Ps
        document.querySelectorAll('.fourps-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const idx = this.getAttribute('data-index');
                const fourPsIdInput = document.querySelector(`.fourps-id-input[data-index='${idx}']`);
                if (this.checked) {
                    fourPsIdInput.disabled = false;
                } else {
                    fourPsIdInput.disabled = true;
                    fourPsIdInput.value = '';
                }
            });
        });
    }

    function calculateVotingStatus(idx) {
        const birthdateInput = document.querySelector(`.birthdate-input[data-index='${idx}']`);
        const votingSelect = document.querySelector(`.voting-status-select[data-index='${idx}']`);
        const votingHint = document.querySelector(`.voting-hint[data-index='${idx}']`);
        const precinctInput = document.querySelector(`.precinct-input[data-index='${idx}']`);
        if (!birthdateInput.value) {
            votingSelect.innerHTML = '<option value="">-- Select Age First --</option>';
            votingSelect.disabled = true;
            precinctInput.disabled = true;
            votingHint.textContent = 'Determined by age';
            votingHint.className = 'text-muted voting-hint';
            return;
        }
        const today = new Date();
        const birth = new Date(birthdateInput.value);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        if (age < 15) {
            votingSelect.innerHTML = '<option value="0" selected>Not Eligible (Below 15 years old)</option>';
            votingSelect.disabled = true;
            votingSelect.className = 'form-select mb-2 text-muted voting-status-select';
            precinctInput.disabled = true;
            precinctInput.value = '';
            votingHint.textContent = 'Not eligible to vote';
            votingHint.className = 'text-muted voting-hint';
        } else if (age >= 15 && age <= 17) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered SK Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-info fw-bold voting-status-select';
            votingHint.textContent = 'SK Voter eligibility (15-17 years old)';
            votingHint.className = 'text-info fw-bold voting-hint';
        } else if (age >= 18) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-success fw-bold voting-status-select';
            votingHint.textContent = 'Regular Voter eligibility (18+ years old)';
            votingHint.className = 'text-success fw-bold voting-hint';
        }
        votingSelect.addEventListener('change', function() {
            if (votingSelect.value === '1') {
                precinctInput.disabled = false;
            } else {
                precinctInput.disabled = true;
                precinctInput.value = '';
            }
        });
    }

    document.getElementById('member_count').addEventListener('input', updateMemberForms);
    // Prepare options for select fields from backend
window.addressToPurok = window.addressToPurok || {};
    window.addressOptions = `{!! isset($addresses) ? collect($addresses)->map(fn($a) => '<option value="'.e($a).'">'.e($a).'</option>')->implode('') : '' !!}`;
    window.subFamiliesOptions = `{!! isset($household) ? $household->subFamilies->map(function($sf){
        if($sf->is_primary_family && $sf->subHead) return '<option value="'.$sf->id.'">⭐ PRIMARY HEAD: '.e($sf->subHead->full_name).'</option>';
        if(!$sf->is_primary_family && $sf->subHead) return '<option value="'.$sf->id.'">👤 CO-HEAD: '.e($sf->subHead->full_name).' ('.e($sf->sub_family_name).')</option>';
        return '';
    })->implode('') : '' !!}`;
    updateMemberForms();
});
});
</script>
@endpush

@endsection
