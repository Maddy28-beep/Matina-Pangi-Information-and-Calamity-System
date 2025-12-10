@extends('layouts.app')

@section('title', 'Add Member to Household')

@section('content')
<div class="ds-page">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-person-plus"></i> Add Member to Household {{ $household->household_id }}</h2>
            <p class="text-muted">Add a new resident to this household</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-house-door"></i> Household Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Household ID:</strong> <span class="badge bg-primary">{{ $household->household_id }}</span></p>
                    <p class="mb-2"><strong>Address:</strong> {{ $household->address }}</p>
                    <p class="mb-2"><strong>Purok:</strong> {{ optional($household->purok)->purok_name ?? $household->purok ?? 'N/A' }}</p>
                    @php $primaryFamily = $household->subFamilies->where('is_primary_family', true)->first(); @endphp
                    <p class="mb-2"><strong>Primary Head:</strong>
                        @if($primaryFamily && $primaryFamily->subHead)
                            <span class="text-success fw-bold">⭐ {{ $primaryFamily->subHead->full_name }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </p>
                    @php
                        $coHeads = $household->subFamilies->where('is_primary_family', false);
                    @endphp
                    @if($coHeads->count() > 0)
                        <p class="mb-2"><strong>Co-Heads:</strong></p>
                        <ul class="mb-0">
                            @foreach($coHeads as $coHead)
                                @if($coHead->subHead)
                                    <li class="text-primary">👤 {{ $coHead->subHead->full_name }} <small class="text-muted">({{ $coHead->sub_family_name }})</small></li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Address to Purok mapping (should be provided by backend in real use)
    // Example: window.addressToPurok = { 'Km1 Matina Pangi, Davao City': 'Purok 1', ... };
    window.addressToPurok = window.addressToPurok || {};

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

    // ...existing code for voting status, PWD, 4Ps, etc...
    const birthdateInput = document.getElementById('birthdate');
    const votingStatusInput = document.getElementById('voting_status');
    const isVoterHidden = document.getElementById('is_voter_hidden');
    const precinctInput = document.getElementById('precinct_input');
    const pwdCheckbox = document.getElementById('is_pwd');
    const pwdIdInput = document.getElementById('pwd_id_input');
    const disabilityTypeInput = document.getElementById('disability_type_input');
    const fourPsCheckbox = document.getElementById('is_4ps');
    const fourPsIdInput = document.getElementById('4ps_id_input');

    function calculateVotingStatus() {
        const birthdate = birthdateInput.value;
        const votingSelect = document.getElementById('voting_status_select');
        const votingHint = document.getElementById('voting_hint');
        if (!birthdate) {
            votingSelect.innerHTML = '<option value="">-- Select Age First --</option>';
            votingSelect.disabled = true;
            precinctInput.disabled = true;
            votingHint.textContent = 'Determined by age';
            votingHint.className = 'text-muted';
            return;
        }
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        if (age < 15) {
            votingSelect.innerHTML = '<option value="0" selected>Not Eligible (Below 15 years old)</option>';
            votingSelect.disabled = true;
            votingSelect.className = 'form-select mb-2 text-muted';
            precinctInput.disabled = true;
            precinctInput.value = '';
            votingHint.textContent = 'Not eligible to vote';
            votingHint.className = 'text-muted';
        } else if (age >= 15 && age <= 17) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered SK Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-info fw-bold';
            votingHint.textContent = 'SK Voter eligibility (15-17 years old)';
            votingHint.className = 'text-info fw-bold';
        } else if (age >= 18) {
            votingSelect.innerHTML = `
                <option value="">-- Select Status --</option>
                <option value="1">Registered Voter</option>
                <option value="0">Not Registered</option>
            `;
            votingSelect.disabled = false;
            votingSelect.className = 'form-select mb-2 text-success fw-bold';
            votingHint.textContent = 'Regular Voter eligibility (18+ years old)';
            votingHint.className = 'text-success fw-bold';
        }
    }
    document.addEventListener('change', function(e) {
        if (e.target.id === 'voting_status_select') {
            const votingSelect = document.getElementById('voting_status_select');
            if (votingSelect.value === '1') {
                precinctInput.disabled = false;
            } else {
                precinctInput.disabled = true;
                precinctInput.value = '';
            }
        }
    });
    pwdCheckbox.addEventListener('change', function() {
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
    fourPsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            fourPsIdInput.disabled = false;
        } else {
            fourPsIdInput.disabled = true;
            fourPsIdInput.value = '';
        }
    });
    if (birthdateInput.value) {
        calculateVotingStatus();
    }
    birthdateInput.addEventListener('change', calculateVotingStatus);
    birthdateInput.addEventListener('blur', calculateVotingStatus);
});
</script>
@endpush

@endsection
