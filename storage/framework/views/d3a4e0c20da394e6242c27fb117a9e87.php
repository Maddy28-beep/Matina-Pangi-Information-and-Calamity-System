<div class="row g-3">
    <!-- Personal Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_first_name" 
               name="<?php echo e($prefix); ?>[first_name]" value="<?php echo e(old($prefix.'.first_name')); ?>" required>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_middle_name" class="form-label">Middle Name</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_middle_name" 
               name="<?php echo e($prefix); ?>[middle_name]" value="<?php echo e(old($prefix.'.middle_name')); ?>">
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_last_name" 
               name="<?php echo e($prefix); ?>[last_name]" value="<?php echo e(old($prefix.'.last_name')); ?>" required>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_suffix" class="form-label">Suffix</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_suffix" 
               name="<?php echo e($prefix); ?>[suffix]" value="<?php echo e(old($prefix.'.suffix')); ?>" placeholder="Jr., Sr., III">
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_birthdate" class="form-label">Birthdate <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="<?php echo e($prefix); ?>_birthdate" 
               name="<?php echo e($prefix); ?>[birthdate]" value="<?php echo e(old($prefix.'.birthdate')); ?>" required>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_sex" class="form-label">Sex <span class="text-danger">*</span></label>
        <select class="form-select" id="<?php echo e($prefix); ?>_sex" name="<?php echo e($prefix); ?>[sex]" required>
            <option value="">Select Sex</option>
            <option value="male" <?php echo e(old($prefix.'.sex') == 'male' ? 'selected' : ''); ?>>Male</option>
            <option value="female" <?php echo e(old($prefix.'.sex') == 'female' ? 'selected' : ''); ?>>Female</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_civil_status" class="form-label">Civil Status <span class="text-danger">*</span></label>
        <select class="form-select" id="<?php echo e($prefix); ?>_civil_status" name="<?php echo e($prefix); ?>[civil_status]" required>
            <option value="single" <?php echo e(old($prefix.'.civil_status', 'single') == 'single' ? 'selected' : ''); ?>>Single</option>
            <option value="married" <?php echo e(old($prefix.'.civil_status') == 'married' ? 'selected' : ''); ?>>Married</option>
            <option value="widowed" <?php echo e(old($prefix.'.civil_status') == 'widowed' ? 'selected' : ''); ?>>Widowed</option>
            <option value="separated" <?php echo e(old($prefix.'.civil_status') == 'separated' ? 'selected' : ''); ?>>Separated</option>
            <option value="divorced" <?php echo e(old($prefix.'.civil_status') == 'divorced' ? 'selected' : ''); ?>>Divorced</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_place_of_birth" class="form-label">Place of Birth</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_place_of_birth" 
               name="<?php echo e($prefix); ?>[place_of_birth]" value="<?php echo e(old($prefix.'.place_of_birth')); ?>">
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_nationality" class="form-label">Nationality</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_nationality" 
               name="<?php echo e($prefix); ?>[nationality]" value="<?php echo e(old($prefix.'.nationality', 'Filipino')); ?>">
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_religion" class="form-label">Religion</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_religion" 
               name="<?php echo e($prefix); ?>[religion]" value="<?php echo e(old($prefix.'.religion')); ?>">
    </div>
    
    <div class="col-md-3">
        <label for="<?php echo e($prefix); ?>_blood_type" class="form-label">Blood Type</label>
        <select class="form-select" id="<?php echo e($prefix); ?>_blood_type" name="<?php echo e($prefix); ?>[blood_type]">
            <option value="">Select</option>
            <option value="A+" <?php echo e(old($prefix.'.blood_type') == 'A+' ? 'selected' : ''); ?>>A+</option>
            <option value="A-" <?php echo e(old($prefix.'.blood_type') == 'A-' ? 'selected' : ''); ?>>A-</option>
            <option value="B+" <?php echo e(old($prefix.'.blood_type') == 'B+' ? 'selected' : ''); ?>>B+</option>
            <option value="B-" <?php echo e(old($prefix.'.blood_type') == 'B-' ? 'selected' : ''); ?>>B-</option>
            <option value="AB+" <?php echo e(old($prefix.'.blood_type') == 'AB+' ? 'selected' : ''); ?>>AB+</option>
            <option value="AB-" <?php echo e(old($prefix.'.blood_type') == 'AB-' ? 'selected' : ''); ?>>AB-</option>
            <option value="O+" <?php echo e(old($prefix.'.blood_type') == 'O+' ? 'selected' : ''); ?>>O+</option>
            <option value="O-" <?php echo e(old($prefix.'.blood_type') == 'O-' ? 'selected' : ''); ?>>O-</option>
        </select>
    </div>
    
    <!-- Contact Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Contact Information</h6>
    </div>
    
    <div class="col-md-6">
        <label for="<?php echo e($prefix); ?>_contact_number" class="form-label">Contact Number</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_contact_number" 
               name="<?php echo e($prefix); ?>[contact_number]" value="<?php echo e(old($prefix.'.contact_number')); ?>">
    </div>
    
    <div class="col-md-6">
        <label for="<?php echo e($prefix); ?>_email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="<?php echo e($prefix); ?>_email" 
               name="<?php echo e($prefix); ?>[email]" value="<?php echo e(old($prefix.'.email')); ?>">
    </div>
    
    <!-- Special Categories -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Special Categories</h6>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="<?php echo e($prefix); ?>_is_pwd" 
                           name="<?php echo e($prefix); ?>[is_pwd]" value="1" <?php echo e(old($prefix.'.is_pwd') ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-bold" for="<?php echo e($prefix); ?>_is_pwd">
                        <i class="bi bi-universal-access"></i> Person with Disability (PWD)
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="<?php echo e($prefix); ?>[pwd_id]" placeholder="PWD ID Number" value="<?php echo e(old($prefix.'.pwd_id')); ?>">
                    <input type="text" class="form-control form-control-sm mt-1" 
                           name="<?php echo e($prefix); ?>[disability_type]" placeholder="Disability Type" value="<?php echo e(old($prefix.'.disability_type')); ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="<?php echo e($prefix); ?>_is_voter" 
                           name="<?php echo e($prefix); ?>[is_voter]" value="1" <?php echo e(old($prefix.'.is_voter') ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-bold" for="<?php echo e($prefix); ?>_is_voter">
                        <i class="bi bi-check-circle"></i> Registered Voter
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="<?php echo e($prefix); ?>[precinct_number]" placeholder="Precinct Number" value="<?php echo e(old($prefix.'.precinct_number')); ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="<?php echo e($prefix); ?>_is_4ps_beneficiary" 
                           name="<?php echo e($prefix); ?>[is_4ps_beneficiary]" value="1" <?php echo e(old($prefix.'.is_4ps_beneficiary') ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-bold" for="<?php echo e($prefix); ?>_is_4ps_beneficiary">
                        <i class="bi bi-cash-coin"></i> 4Ps Beneficiary
                    </label>
                </div>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" 
                           name="<?php echo e($prefix); ?>[4ps_id]" placeholder="4Ps ID Number" value="<?php echo e(old($prefix.'.4ps_id')); ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Employment & Income -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Employment & Income</h6>
    </div>
    
    <div class="col-md-4">
        <label for="<?php echo e($prefix); ?>_occupation" class="form-label">Occupation</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_occupation" 
               name="<?php echo e($prefix); ?>[occupation]" value="<?php echo e(old($prefix.'.occupation')); ?>">
    </div>
    
    <div class="col-md-4">
        <label for="<?php echo e($prefix); ?>_employment_status" class="form-label">Employment Status</label>
        <select class="form-select" id="<?php echo e($prefix); ?>_employment_status" name="<?php echo e($prefix); ?>[employment_status]">
            <option value="">Select Status</option>
            <option value="employed" <?php echo e(old($prefix.'.employment_status') == 'employed' ? 'selected' : ''); ?>>Employed</option>
            <option value="unemployed" <?php echo e(old($prefix.'.employment_status') == 'unemployed' ? 'selected' : ''); ?>>Unemployed</option>
            <option value="self-employed" <?php echo e(old($prefix.'.employment_status') == 'self-employed' ? 'selected' : ''); ?>>Self-Employed</option>
            <option value="student" <?php echo e(old($prefix.'.employment_status') == 'student' ? 'selected' : ''); ?>>Student</option>
            <option value="retired" <?php echo e(old($prefix.'.employment_status') == 'retired' ? 'selected' : ''); ?>>Retired</option>
        </select>
    </div>
    
    <div class="col-md-4">
        <label for="<?php echo e($prefix); ?>_monthly_income" class="form-label">Monthly Income (₱)</label>
        <input type="number" class="form-control" id="<?php echo e($prefix); ?>_monthly_income" 
               name="<?php echo e($prefix); ?>[monthly_income]" value="<?php echo e(old($prefix.'.monthly_income')); ?>" 
               step="0.01" min="0">
    </div>
    
    <div class="col-md-6">
        <label for="<?php echo e($prefix); ?>_employer_name" class="form-label">Employer Name</label>
        <input type="text" class="form-control" id="<?php echo e($prefix); ?>_employer_name" 
               name="<?php echo e($prefix); ?>[employer_name]" value="<?php echo e(old($prefix.'.employer_name')); ?>">
    </div>
    
    <div class="col-md-6">
        <label for="<?php echo e($prefix); ?>_educational_attainment" class="form-label">Educational Attainment</label>
        <select class="form-select" id="<?php echo e($prefix); ?>_educational_attainment" name="<?php echo e($prefix); ?>[educational_attainment]">
            <option value="">Select</option>
            <option value="no formal education" <?php echo e(old($prefix.'.educational_attainment') == 'no formal education' ? 'selected' : ''); ?>>No Formal Education</option>
            <option value="elementary level" <?php echo e(old($prefix.'.educational_attainment') == 'elementary level' ? 'selected' : ''); ?>>Elementary Level</option>
            <option value="elementary graduate" <?php echo e(old($prefix.'.educational_attainment') == 'elementary graduate' ? 'selected' : ''); ?>>Elementary Graduate</option>
            <option value="high school level" <?php echo e(old($prefix.'.educational_attainment') == 'high school level' ? 'selected' : ''); ?>>High School Level</option>
            <option value="high school graduate" <?php echo e(old($prefix.'.educational_attainment') == 'high school graduate' ? 'selected' : ''); ?>>High School Graduate</option>
            <option value="college level" <?php echo e(old($prefix.'.educational_attainment') == 'college level' ? 'selected' : ''); ?>>College Level</option>
            <option value="college graduate" <?php echo e(old($prefix.'.educational_attainment') == 'college graduate' ? 'selected' : ''); ?>>College Graduate</option>
            <option value="vocational" <?php echo e(old($prefix.'.educational_attainment') == 'vocational' ? 'selected' : ''); ?>>Vocational</option>
            <option value="post graduate" <?php echo e(old($prefix.'.educational_attainment') == 'post graduate' ? 'selected' : ''); ?>>Post Graduate</option>
        </select>
    </div>
    
    <!-- Additional Information -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3">Additional Information</h6>
    </div>
    
    <div class="col-md-12">
        <label for="<?php echo e($prefix); ?>_medical_conditions" class="form-label">Medical Conditions</label>
        <textarea class="form-control" id="<?php echo e($prefix); ?>_medical_conditions" 
                  name="<?php echo e($prefix); ?>[medical_conditions]" rows="2"><?php echo e(old($prefix.'.medical_conditions')); ?></textarea>
    </div>
    
    <div class="col-md-12">
        <label for="<?php echo e($prefix); ?>_remarks" class="form-label">Remarks</label>
        <textarea class="form-control" id="<?php echo e($prefix); ?>_remarks" 
                  name="<?php echo e($prefix); ?>[remarks]" rows="2"><?php echo e(old($prefix.'.remarks')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\matina_final\resources\views/households/partials/resident-form.blade.php ENDPATH**/ ?>