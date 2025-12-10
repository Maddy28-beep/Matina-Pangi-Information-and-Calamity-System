@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone"></i> Create Announcement</h2>
    <a href="{{ route('announcements.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('announcements.store') }}" id="announcementForm">
            @csrf
            <input type="hidden" name="filters_json" id="filters_json">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="Enter announcement title">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Urgency</label>
                    <select name="urgency" class="form-select">
                        <option value="" {{ old('urgency')==='' ? 'selected' : '' }}>Normal</option>
                        <option value="High" {{ old('urgency')==='High' ? 'selected' : '' }}>High</option>
                        <option value="Critical" {{ old('urgency')==='Critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea name="message" rows="4" class="form-control" required placeholder="Write announcement message">{{ old('message') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Target Audience</label>
                    
                    <!-- All Residents (Default) -->
                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="target_type" id="entire_barangay" value="all" checked>
                        <label class="form-check-label fw-semibold" for="entire_barangay">
                            <i class="bi bi-globe text-primary"></i> All Residents (Entire Barangay)
                        </label>
                    </div>

                    <!-- Specific Purok -->
                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="target_type" id="specific_purok_radio" value="purok">
                        <label class="form-check-label fw-semibold" for="specific_purok_radio">
                            <i class="bi bi-map text-success"></i> Specific Purok
                        </label>
                        <div class="mt-2 ms-4" id="purok_selector" style="display:none;">
                            <select name="target_purok" id="target_purok" class="form-select">
                                <option value="">Select Purok</option>
                                @if(isset($puroks) && $puroks->count())
                                    @foreach($puroks as $p)
                                        <option value="{{ $p->purok_name }}">{{ $p->purok_name }}</option>
                                    @endforeach
                                @else
                                    @for($i=1;$i<=10;$i++)
                                        <option value="Purok {{ $i }}">Purok {{ $i }}</option>
                                    @endfor
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Specific Households -->
                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="target_type" id="specific_households_radio" value="households">
                        <label class="form-check-label fw-semibold" for="specific_households_radio">
                            <i class="bi bi-house-door text-warning"></i> Specific Households
                        </label>
                        <div class="mt-2 ms-4" id="households_selector" style="display:none;">
                            <input type="text" id="household_search" class="form-control mb-2" placeholder="Search by household ID or head of family name...">
                            <select name="households[]" id="households_select" class="form-select" multiple size="8">
                                @if(isset($households) && $households->count())
                                    @foreach($households as $hh)
                                        <option value="{{ $hh->id }}" data-search="{{ strtolower($hh->household_id . ' ' . optional($hh->officialHead)->full_name . ' ' . $hh->purok) }}">
                                            {{ $hh->household_id }} - {{ optional($hh->officialHead)->full_name ?? 'No Head' }} ({{ $hh->purok }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple households</small>
                        </div>
                    </div>

                    <!-- Calamity-Related Filters -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <label class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle text-danger"></i> Calamity-Related Filters (Optional)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="only_affected" id="only_affected" value="1">
                            <label class="form-check-label" for="only_affected">Only Affected Residents</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="only_evacuees" id="only_evacuees" value="1">
                            <label class="form-check-label" for="only_evacuees">Only Evacuees</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="form-label">Delivery Options</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_sms" id="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_sms">Send SMS</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_email" id="send_email" value="1" {{ old('send_email') ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_email">Send Email</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetRadios = document.querySelectorAll('input[name="target_type"]');
    const purokSelector = document.getElementById('purok_selector');
    const householdsSelector = document.getElementById('households_selector');
    const purokSelect = document.getElementById('target_purok');
    const householdsSelect = document.getElementById('households_select');
    const searchInput = document.getElementById('household_search');
    
    // Handle target type changes
    targetRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all selectors
            purokSelector.style.display = 'none';
            householdsSelector.style.display = 'none';
            
            // Clear selections
            purokSelect.value = '';
            Array.from(householdsSelect.options).forEach(opt => opt.selected = false);
            
            // Show appropriate selector
            if (this.value === 'purok') {
                purokSelector.style.display = 'block';
            } else if (this.value === 'households') {
                householdsSelector.style.display = 'block';
            }
        });
    });
    
    // Household search functionality
    if (searchInput && householdsSelect) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            Array.from(householdsSelect.options).forEach(option => {
                const searchData = option.getAttribute('data-search') || '';
                if (searchData.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection