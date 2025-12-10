<div class="row g-3">
    <!-- Personal Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="{{ $prefix }}_first_name" 
               name="{{ $prefix }}[first_name]" value="{{ old($prefix.'.first_name') }}" required>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_middle_name" class="form-label">Middle Name</label>
        <input type="text" class="form-control" id="{{ $prefix }}_middle_name" 
               name="{{ $prefix }}[middle_name]" value="{{ old($prefix.'.middle_name') }}">
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="{{ $prefix }}_last_name" 
               name="{{ $prefix }}[last_name]" value="{{ old($prefix.'.last_name') }}" required>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_suffix" class="form-label">Suffix</label>
        <input type="text" class="form-control" id="{{ $prefix }}_suffix" 
               name="{{ $prefix }}[suffix]" value="{{ old($prefix.'.suffix') }}" placeholder="Jr., Sr., III">
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_birthdate" class="form-label">Birthdate <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="{{ $prefix }}_birthdate" 
               name="{{ $prefix }}[birthdate]" value="{{ old($prefix.'.birthdate') }}" required>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_sex" class="form-label">Sex <span class="text-danger">*</span></label>
        <select class="form-select" id="{{ $prefix }}_sex" name="{{ $prefix }}[sex]" required>
            <option value="">Select Sex</option>
            <option value="male" {{ old($prefix.'.sex') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old($prefix.'.sex') == 'female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_civil_status" class="form-label">Civil Status <span class="text-danger">*</span></label>
        <select class="form-select" id="{{ $prefix }}_civil_status" name="{{ $prefix }}[civil_status]" required>
            <option value="single" {{ old($prefix.'.civil_status', 'single') == 'single' ? 'selected' : '' }}>Single</option>
            <option value="married" {{ old($prefix.'.civil_status') == 'married' ? 'selected' : '' }}>Married</option>
            <option value="widowed" {{ old($prefix.'.civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
            <option value="separated" {{ old($prefix.'.civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
            <option value="divorced" {{ old($prefix.'.civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_place_of_birth" class="form-label">Place of Birth</label>
        <input type="text" class="form-control" id="{{ $prefix }}_place_of_birth" 
               name="{{ $prefix }}[place_of_birth]" value="{{ old($prefix.'.place_of_birth') }}">
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_nationality" class="form-label">Nationality</label>
        <input type="text" class="form-control" id="{{ $prefix }}_nationality" 
               name="{{ $prefix }}[nationality]" value="{{ old($prefix.'.nationality', 'Filipino') }}">
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_religion" class="form-label">Religion</label>
        <input type="text" class="form-control" id="{{ $prefix }}_religion" 
               name="{{ $prefix }}[religion]" value="{{ old($prefix.'.religion') }}">
    </div>
    
    <div class="col-md-3">
        <label for="{{ $prefix }}_blood_type" class="form-label">Blood Type</label>
        <select class="form-select" id="{{ $prefix }}_blood_type" name="{{ $prefix }}[blood_type]">
            <option value="">Select</option>
            <option value="A+" {{ old($prefix.'.blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
            <option value="A-" {{ old($prefix.'.blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
            <option value="B+" {{ old($prefix.'.blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
            <option value="B-" {{ old($prefix.'.blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
            <option value="AB+" {{ old($prefix.'.blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
            <option value="AB-" {{ old($prefix.'.blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
            <option value="O+" {{ old($prefix.'.blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
            <option value="O-" {{ old($prefix.'.blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
        </select>
    </div>
    
    <!-- Contact Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Contact Information</h6>
    </div>
    
    <div class="col-md-6">
        <label for="{{ $prefix }}_contact_number" class="form-label">Contact Number</label>
        <input type="text" class="form-control" id="{{ $prefix }}_contact_number" 
               name="{{ $prefix }}[contact_number]" value="{{ old($prefix.'.contact_number') }}">
    </div>
    
    <div class="col-md-6">
        <label for="{{ $prefix }}_email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="{{ $prefix }}_email" 
               name="{{ $prefix }}[email]" value="{{ old($prefix.'.email') }}">
    </div>
    
    <!-- Special Categories -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Special Categories</h6>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="{{ $prefix }}_is_pwd" 
                           name="{{ $prefix }}[is_pwd]" value="1" {{ old($prefix.'.is_pwd') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="{{ $prefix }}_is_pwd">
                        <i class="bi bi-universal-access"></i> Person with Disability (PWD)
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="{{ $prefix }}[pwd_id]" placeholder="PWD ID Number" value="{{ old($prefix.'.pwd_id') }}">
                    <input type="text" class="form-control form-control-sm mt-1" 
                           name="{{ $prefix }}[disability_type]" placeholder="Disability Type" value="{{ old($prefix.'.disability_type') }}">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="{{ $prefix }}_is_voter" 
                           name="{{ $prefix }}[is_voter]" value="1" {{ old($prefix.'.is_voter') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="{{ $prefix }}_is_voter">
                        <i class="bi bi-check-circle"></i> Registered Voter
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="{{ $prefix }}[precinct_number]" placeholder="Precinct Number" value="{{ old($prefix.'.precinct_number') }}">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="{{ $prefix }}_is_4ps_beneficiary" 
                           name="{{ $prefix }}[is_4ps_beneficiary]" value="1" {{ old($prefix.'.is_4ps_beneficiary') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="{{ $prefix }}_is_4ps_beneficiary">
                        <i class="bi bi-cash-coin"></i> 4Ps Beneficiary
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="{{ $prefix }}[4ps_id]" placeholder="4Ps ID Number" value="{{ old($prefix.'.4ps_id') }}">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Employment & Income -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Employment & Income</h6>
    </div>
    
    <div class="col-md-4">
        <label for="{{ $prefix }}_occupation" class="form-label">Occupation</label>
        <input type="text" class="form-control" id="{{ $prefix }}_occupation" 
               name="{{ $prefix }}[occupation]" value="{{ old($prefix.'.occupation') }}">
    </div>
    
    <div class="col-md-4">
        <label for="{{ $prefix }}_employment_status" class="form-label">Employment Status</label>
        <select class="form-select" id="{{ $prefix }}_employment_status" name="{{ $prefix }}[employment_status]">
            <option value="">Select Status</option>
            <option value="employed" {{ old($prefix.'.employment_status') == 'employed' ? 'selected' : '' }}>Employed</option>
            <option value="unemployed" {{ old($prefix.'.employment_status') == 'unemployed' ? 'selected' : '' }}>Unemployed</option>
            <option value="self-employed" {{ old($prefix.'.employment_status') == 'self-employed' ? 'selected' : '' }}>Self-Employed</option>
            <option value="student" {{ old($prefix.'.employment_status') == 'student' ? 'selected' : '' }}>Student</option>
            <option value="retired" {{ old($prefix.'.employment_status') == 'retired' ? 'selected' : '' }}>Retired</option>
        </select>
    </div>
    
    <div class="col-md-4">
        <label for="{{ $prefix }}_monthly_income" class="form-label">Monthly Income (₱)</label>
        <input type="number" class="form-control" id="{{ $prefix }}_monthly_income" 
               name="{{ $prefix }}[monthly_income]" value="{{ old($prefix.'.monthly_income') }}" 
               step="0.01" min="0">
    </div>
    
    <div class="col-md-6">
        <label for="{{ $prefix }}_employer_name" class="form-label">Employer Name</label>
        <input type="text" class="form-control" id="{{ $prefix }}_employer_name" 
               name="{{ $prefix }}[employer_name]" value="{{ old($prefix.'.employer_name') }}">
    </div>
    
    <div class="col-md-6">
        <label for="{{ $prefix }}_educational_attainment" class="form-label">Educational Attainment</label>
        <select class="form-select" id="{{ $prefix }}_educational_attainment" name="{{ $prefix }}[educational_attainment]">
            <option value="">Select</option>
            <option value="no formal education" {{ old($prefix.'.educational_attainment') == 'no formal education' ? 'selected' : '' }}>No Formal Education</option>
            <option value="elementary level" {{ old($prefix.'.educational_attainment') == 'elementary level' ? 'selected' : '' }}>Elementary Level</option>
            <option value="elementary graduate" {{ old($prefix.'.educational_attainment') == 'elementary graduate' ? 'selected' : '' }}>Elementary Graduate</option>
            <option value="high school level" {{ old($prefix.'.educational_attainment') == 'high school level' ? 'selected' : '' }}>High School Level</option>
            <option value="high school graduate" {{ old($prefix.'.educational_attainment') == 'high school graduate' ? 'selected' : '' }}>High School Graduate</option>
            <option value="college level" {{ old($prefix.'.educational_attainment') == 'college level' ? 'selected' : '' }}>College Level</option>
            <option value="college graduate" {{ old($prefix.'.educational_attainment') == 'college graduate' ? 'selected' : '' }}>College Graduate</option>
            <option value="vocational" {{ old($prefix.'.educational_attainment') == 'vocational' ? 'selected' : '' }}>Vocational</option>
            <option value="post graduate" {{ old($prefix.'.educational_attainment') == 'post graduate' ? 'selected' : '' }}>Post Graduate</option>
        </select>
    </div>
    
    <!-- Additional Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Additional Information</h6>
    </div>
    
    <div class="col-md-12">
        <label for="{{ $prefix }}_medical_conditions" class="form-label">Medical Conditions</label>
        <textarea class="form-control" id="{{ $prefix }}_medical_conditions" 
                  name="{{ $prefix }}[medical_conditions]" rows="2">{{ old($prefix.'.medical_conditions') }}</textarea>
    </div>
    
    <div class="col-md-12">
        <label for="{{ $prefix }}_remarks" class="form-label">Remarks</label>
        <textarea class="form-control" id="{{ $prefix }}_remarks" 
                  name="{{ $prefix }}[remarks]" rows="2">{{ old($prefix.'.remarks') }}</textarea>
    </div>
</div>
