@extends('layouts.app')

@push('scripts')
<script>
// Your JS here (if needed)
</script>
@endpush

@section('content')
<div class="ds-page d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="w-100" style="max-width: 600px;">
        <h2 class="mb-4 text-center">Register New Resident</h2>
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">Resident Information</div>
            <div class="card-body p-4">
                <form action="{{ route('residents.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Suffix</label>
                            <input type="text" name="suffix" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                            <input type="date" name="birthdate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-select" required>
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com">
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">Register Resident</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>