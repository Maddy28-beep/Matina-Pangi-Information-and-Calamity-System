<?php $__env->startSection('title', 'Resident Details'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.ds-profile-card__metrics{display:flex;gap:12px;flex-wrap:wrap;align-items:stretch}
.ds-profile-card__metrics .ds-stat{flex:0 0 calc(50% - 6px)}
@media (max-width:768px){.ds-profile-card__metrics .ds-stat{flex:1 1 100%}}
</style>
<?php $__env->stopPush(); ?>

<?php
    use Illuminate\Support\Str;

    $initials = Str::upper(Str::substr((string) $resident->first_name, 0, 1) . Str::substr((string) $resident->last_name, 0, 1));
    $statusLabel = ucfirst($resident->status ?? 'Unknown');
    $statusBadgeClass = match ($resident->status) {
        'active' => 'ds-badge ds-badge--success',
        'inactive' => 'ds-badge ds-badge--neutral',
        'deceased' => 'ds-badge ds-badge--danger',
        'reallocated' => 'ds-badge ds-badge--warning',
        default => 'ds-badge ds-badge--info',
    };

    $profileTags = collect([
        ['icon' => 'bi-hash', 'label' => $resident->resident_id],
        $resident->is_household_head ? ['icon' => 'bi-person-badge', 'label' => 'Household Head'] : null,
        $resident->is_primary_head ? ['icon' => 'bi-people', 'label' => 'Primary Head'] : null,
        $resident->is_co_head ? ['icon' => 'bi-people', 'label' => 'Co-Head'] : null,
        $resident->is_pwd ? ['icon' => 'bi-universal-access', 'label' => 'PWD'] : null,
        $resident->is_senior_citizen ? ['icon' => 'bi-person-cane', 'label' => 'Senior'] : null,
        $resident->is_4ps_beneficiary ? ['icon' => 'bi-cash-coin', 'label' => '4Ps'] : null,
        $resident->is_voter ? ['icon' => 'bi-check-circle', 'label' => 'Voter'] : null,
    ])->filter()->values();

    $specialCategories = collect([
        $resident->is_pwd ? [
            'icon' => 'bi-universal-access',
            'title' => 'Person with Disability',
            'meta' => array_filter([
                $resident->pwd_id ? 'ID: ' . $resident->pwd_id : null,
                $resident->disability_type,
            ]),
        ] : null,
        $resident->is_senior_citizen ? [
            'icon' => 'bi-person-cane',
            'title' => 'Senior Citizen',
            'meta' => array_filter([
                $resident->senior_id ? 'ID: ' . $resident->senior_id : null,
            ]),
        ] : null,
        $resident->is_teen ? [
            'icon' => 'bi-person-standing',
            'title' => 'Teen (13-19)',
            'meta' => ['Youth monitoring category'],
        ] : null,
        $resident->is_voter ? [
            'icon' => 'bi-check-circle',
            'title' => 'Registered Voter',
            'meta' => array_filter([
                $resident->precinct_number ? 'Precinct: ' . $resident->precinct_number : null,
            ]),
        ] : null,
        $resident->is_4ps_beneficiary ? [
            'icon' => 'bi-cash-coin',
            'title' => '4Ps Beneficiary',
            'meta' => array_filter([
                data_get($resident, '4ps_id') ? 'ID: ' . data_get($resident, '4ps_id') : null,
            ]),
        ] : null,
    ])->filter()->values();

    $household = $resident->household;
    $purokName = null;

    if ($household) {
        $purokData = $household->purok ?? null;
        $purokName = is_object($purokData) ? ($purokData->purok_name ?? null) : $purokData;
    }
?>

<?php $__env->startSection('content'); ?>
<div class="ds-page ds-page--detail">
    <nav class="ds-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
        <span class="ds-breadcrumbs__separator">/</span>
        <a href="<?php echo e(route('residents.index')); ?>">Residents</a>
        <span class="ds-breadcrumbs__separator">/</span>
        <span><?php echo e($resident->resident_id); ?></span>
    </nav>

    <div class="ds-page__header">
        <div>
            <h1 class="page-title">Resident Profile</h1>
            <p class="ds-page__subtitle">
                Complete record for <?php echo e($resident->full_name); ?>.
                <?php if(method_exists($resident, 'isPending') && $resident->isPending()): ?>
                    <span class="ds-badge ds-badge--warning" style="margin-left: 0.5rem;">
                        <i class="bi bi-clock-history"></i> Pending Approval
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="ds-toolbar__group">
            <span class="<?php echo e($statusBadgeClass); ?>"><?php echo e($statusLabel); ?></span>
        </div>
    </div>

    <section class="ds-card ds-profile-card">
        <div class="ds-profile-card__body">
            <div class="ds-profile-card__identity">
                <div>
                    <h2 class="ds-profile-card__name"><?php echo e($resident->full_name); ?></h2>
                    <p class="ds-profile-card__sub"><?php echo e($resident->age); ?> years • <?php echo e(ucfirst($resident->sex)); ?></p>
                    <div class="ds-profile-card__tags">
                        <?php $__currentLoopData = $profileTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="ds-chip ds-chip--neutral"><i class="bi <?php echo e($tag['icon']); ?>"></i><?php echo e($tag['label']); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($resident->approval_status && $resident->approval_status !== 'approved'): ?>
                            <span class="ds-chip"><i class="bi bi-shield-check"></i><?php echo e(ucfirst($resident->approval_status)); ?> status</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="ds-profile-card__metrics">
                <div class="ds-stat">
                    <span class="ds-stat__label">Household</span>
                    <span class="ds-stat__value">
                        <?php if($household): ?>
                            <a href="<?php echo e(auth()->user()->isStaff() ? route('staff.households.show', $household) : route('households.show', $household)); ?>" class="ds-stat__link"><?php echo e($household->household_id); ?></a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </span>
                    <span class="ds-stat__hint"><?php echo e($purokName ?? 'No household assigned'); ?></span>
                </div>
                <div class="ds-stat">
                    <span class="ds-stat__label">Registered</span>
                    <span class="ds-stat__value"><?php echo e($resident->created_at->format('M d, Y')); ?></span>
                    <span class="ds-stat__hint"><?php echo e($resident->creator ? 'by ' . $resident->creator->name : 'System'); ?></span>
                </div>
            </div>
        </div>

        <div class="ds-profile-card__actions">
            <a href="<?php echo e(route('residents.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Residents</span>
            </a>
            <a href="<?php echo e(auth()->user()->isSecretary() ? route('resident-transfers.create', ['resident_id' => $resident->id]) : route('staff.resident-transfers.create', ['resident_id' => $resident->id])); ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left-right"></i>
                <span>Request Transfer</span>
            </a>
            <?php if(auth()->user()->isSecretary()): ?>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Change Status</span>
                </button>
                <a href="<?php echo e(route('residents.edit', $resident)); ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i>
                    <span>Edit Resident</span>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <div class="row g-4 ds-detail-layout">
        <div class="col-xl-7">
            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-person-badge"></i>Personal Information</h2>
                </header>
                <div class="ds-detail-card__body">
                    <div class="ds-detail-grid">
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Resident ID</span>
                            <span class="ds-field-value"><?php echo e($resident->resident_id); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Full Name</span>
                            <span class="ds-field-value"><?php echo e($resident->full_name); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Birthdate</span>
                            <span class="ds-field-value"><?php echo e($resident->birthdate?->format('M d, Y') ?? '—'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Age</span>
                            <span class="ds-field-value"><?php echo e($resident->age); ?> yrs</span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Sex</span>
                            <span class="ds-field-value"><?php echo e(ucfirst($resident->sex)); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Civil Status</span>
                            <span class="ds-field-value"><?php echo e(ucfirst($resident->civil_status)); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Nationality</span>
                            <span class="ds-field-value"><?php echo e($resident->nationality ?: 'N/A'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Religion</span>
                            <span class="ds-field-value"><?php echo e($resident->religion ?: 'N/A'); ?></span>
                        </div>
                        <?php if($resident->blood_type): ?>
                            <div class="ds-detail-grid__item">
                                <span class="ds-field-label">Blood Type</span>
                                <span class="ds-field-value"><?php echo e($resident->blood_type); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($resident->place_of_birth): ?>
                            <div class="ds-detail-grid__item ds-detail-grid__item--full">
                                <span class="ds-field-label">Place of Birth</span>
                                <span class="ds-field-value"><?php echo e($resident->place_of_birth); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-telephone"></i>Contact</h2>
                </header>
                <div class="ds-detail-card__body">
                    <div class="ds-detail-grid">
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Contact Number</span>
                            <span class="ds-field-value"><?php echo e($resident->contact_number ?: 'N/A'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Email</span>
                            <span class="ds-field-value"><?php echo e($resident->email ?: 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-stars"></i>Special Categories</h2>
                </header>
                <div class="ds-detail-card__body">
                    <?php if($specialCategories->isNotEmpty()): ?>
                        <div class="ds-spotlight">
                            <?php $__currentLoopData = $specialCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ds-spotlight__item">
                                    <div class="ds-spotlight__icon"><i class="bi <?php echo e($category['icon']); ?>"></i></div>
                                    <div>
                                        <h3 class="ds-spotlight__title"><?php echo e($category['title']); ?></h3>
                                        <?php $__currentLoopData = $category['meta']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p class="ds-spotlight__meta"><?php echo e($meta); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="ds-empty-note">
                            <i class="bi bi-inboxes"></i>
                            <p>No tagged special programs for this resident.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if($resident->medical_conditions || $resident->remarks): ?>
                <section class="ds-card ds-detail-card">
                    <header class="ds-detail-card__header">
                        <h2 class="ds-detail-card__title"><i class="bi bi-clipboard-data"></i>Notes</h2>
                    </header>
                    <div class="ds-detail-card__body ds-detail-card__body--stack">
                        <?php if($resident->medical_conditions): ?>
                            <div class="ds-note">
                                <span class="ds-field-label">Medical Conditions</span>
                                <p><?php echo e($resident->medical_conditions); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($resident->remarks): ?>
                            <div class="ds-note">
                                <span class="ds-field-label">Remarks</span>
                                <p><?php echo e($resident->remarks); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <div class="col-xl-5">
            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-house"></i>Household</h2>
                </header>
                <div class="ds-detail-card__body">
                    <div class="ds-detail-grid">
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Household ID</span>
                            <span class="ds-field-value">
                                <?php if($household): ?>
                                    <a href="<?php echo e(route('households.show', $household)); ?>" class="ds-stat__link"><?php echo e($household->household_id); ?></a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Role</span>
                            <span class="ds-field-value"><?php echo e($resident->household_role ? ucfirst($resident->household_role) : '—'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Head Status</span>
                            <span class="ds-field-value"><?php echo e($resident->is_household_head ? 'Head of Household' : 'Member'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item ds-detail-grid__item--full">
                            <span class="ds-field-label">Address</span>
                            <span class="ds-field-value"><?php echo e($household?->full_address ?? '—'); ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-briefcase"></i>Employment & Education</h2>
                </header>
                <div class="ds-detail-card__body">
                    <div class="ds-detail-grid">
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Occupation</span>
                            <span class="ds-field-value"><?php echo e($resident->occupation ?: 'N/A'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Employment Status</span>
                            <span class="ds-field-value"><?php echo e($resident->employment_status ? ucfirst($resident->employment_status) : 'N/A'); ?></span>
                        </div>
                        <?php if($resident->employer_name): ?>
                            <div class="ds-detail-grid__item ds-detail-grid__item--full">
                                <span class="ds-field-label">Employer</span>
                                <span class="ds-field-value"><?php echo e($resident->employer_name); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Monthly Income</span>
                            <span class="ds-field-value">₱<?php echo e(number_format($resident->monthly_income ?? 0, 2)); ?></span>
                        </div>
                        <?php if($resident->educational_attainment): ?>
                            <div class="ds-detail-grid__item ds-detail-grid__item--full">
                                <span class="ds-field-label">Educational Attainment</span>
                                <span class="ds-field-value"><?php echo e(ucfirst($resident->educational_attainment)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="ds-card ds-detail-card">
                <header class="ds-detail-card__header">
                    <h2 class="ds-detail-card__title"><i class="bi bi-clock-history"></i>Record Timeline</h2>
                </header>
                <div class="ds-detail-card__body">
                    <div class="ds-detail-grid">
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Registered By</span>
                            <span class="ds-field-value"><?php echo e($resident->creator->name ?? 'System'); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Registered On</span>
                            <span class="ds-field-value"><?php echo e($resident->created_at->format('M d, Y')); ?></span>
                        </div>
                        <div class="ds-detail-grid__item">
                            <span class="ds-field-label">Current Status</span>
                            <span class="ds-field-value"><?php echo e(ucfirst($resident->status)); ?></span>
                        </div>
                        <?php if($resident->status_changed_at): ?>
                            <div class="ds-detail-grid__item">
                                <span class="ds-field-label">Updated On</span>
                                <span class="ds-field-value"><?php echo e($resident->status_changed_at->format('M d, Y')); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($resident->statusChanger): ?>
                            <div class="ds-detail-grid__item">
                                <span class="ds-field-label">Updated By</span>
                                <span class="ds-field-value"><?php echo e($resident->statusChanger->name); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($resident->status_notes): ?>
                        <div class="ds-note ds-note--compact">
                            <span class="ds-field-label">Status Notes</span>
                            <p><?php echo e($resident->status_notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <?php if(auth()->user()->isSecretary()): ?>
        <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="<?php echo e(route('residents.change-status', $resident)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeStatusModalLabel">Change Resident Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Status</label>
                                <p><span class="badge bg-<?php echo e($resident->status_badge_color); ?>"><?php echo e(ucfirst($resident->status)); ?></span></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="">Select status</option>
                                    <option value="active" <?php echo e($resident->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="reallocated" <?php echo e($resident->status === 'reallocated' ? 'selected' : ''); ?>>Reallocated</option>
                                    <option value="deceased" <?php echo e($resident->status === 'deceased' ? 'selected' : ''); ?>>Deceased</option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <strong>Active:</strong> Currently living in household<br>
                                    <strong>Reallocated:</strong> Moved to another barangay/household<br>
                                    <strong>Deceased:</strong> Mark resident as deceased
                                </small>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="status_notes" rows="3" placeholder="Optional notes about this status change"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\matina_final\resources\views/residents/show.blade.php ENDPATH**/ ?>