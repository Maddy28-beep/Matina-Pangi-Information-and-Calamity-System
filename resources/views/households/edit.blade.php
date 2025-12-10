@extends('layouts.app')

@section('content')<div class="ds-page">
    <h2 class="mb-4">Edit Household</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('households.update', $household) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="mb-3">Household Information</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Household ID</label>
                        <input type="text" class="form-control" value="{{ $household->household_id }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Household Type <span class="text-danger">*</span></label>
                        <select name="household_type" class="form-select @error('household_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="solo" {{ $household->household_type == 'solo' ? 'selected' : '' }}>Solo (Living Alone)</option>
                            <option value="family" {{ $household->household_type == 'family' ? 'selected' : '' }}>Family</option>
                            <option value="extended" {{ $household->household_type == 'extended' ? 'selected' : '' }}>Extended Family</option>
                        </select>
                        @error('household_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Purok</label>
                        <select class="form-select @error('purok') is-invalid @enderror" name="purok">
                            <option value="">Select Purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok }}" {{ old('purok', $household->purok) == $purok ? 'selected' : '' }}>
                                    {{ $purok }}
                                </option>
                            @endforeach
                        </select>
                        @error('purok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-8">
                        <label class="form-label">Street Address <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">Select an existing address or enter a new one (e.g., Km1, Km2, 123 Main St)</p>
                        <select class="form-select @error('address') is-invalid @enderror" id="address" name="address" required>
                            <option value="">🏘️ Select Existing Address</option>
                            @foreach($addresses as $addr)
                                <option value="{{ $addr }}" {{ old('address', $household->address) == $addr ? 'selected' : '' }}>
                                    {{ $addr }}
                                </option>
                            @endforeach
                            <option value="__new__">➕ Enter New Address</option>
                        </select>
                        <input type="text" class="form-control mt-2 d-none" id="new_address" 
                               placeholder="Enter new address (e.g., Km5, 456 Riverside Ave)">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Members</label>
                        <input type="number" class="form-control" value="{{ $household->total_members }}" disabled>
                        <small class="text-muted">To change members, add or remove residents from this household</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Household
                    </button>
                    <a href="{{ route('households.show', $household) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    document.querySelector('form').addEventListener('submit', function(e) {
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
});
</script>
@endpush
