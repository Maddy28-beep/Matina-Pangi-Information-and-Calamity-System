<?php $__env->startSection('title', 'Residents'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.ds-table th.col-address,
.ds-table td[data-label="Address"] { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
<?php $__env->stopPush(); ?>

<?php
    use Illuminate\Support\Facades\Route;
    use App\Models\Purok;

    $residentCount = method_exists($residents, 'total') ? $residents->total() : $residents->count();
    $purokOptions = $purokOptions ?? Purok::orderBy('purok_name')->pluck('purok_name', 'purok_name')->toArray();
?>

<?php $__env->startSection('content'); ?>
<div class="ds-page" data-search-scope>
    <nav class="ds-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
        <span class="ds-breadcrumbs__separator">/</span>
        <span>Residents</span>
    </nav>

    <div class="ds-page__header">
        <div>
            <h1 class="page-title d-flex align-items-center gap-2"><i class="bi bi-people"></i>Residents</h1>
            <p class="ds-page__subtitle">Manage resident records with instant filtering, responsive tables, and accessible actions.</p>
        </div>
        <div class="ds-toolbar__group">
            <div class="ds-metric" role="presentation">
                <span class="ds-metric__label">Total Residents</span>
                <span class="ds-metric__value"><?php echo e(number_format($residentCount)); ?></span>
            </div>
        </div>
    </div>

    <div class="ds-info-banner" role="note">
        <i class="bi bi-info-circle"></i>
        <span>Add residents through <strong>Households → Add Member</strong> so household relationships stay in sync.</span>
    </div>

    <div class="ds-toolbar mt-3">
        <div class="ds-search" role="search">
            <i class="bi bi-search ds-search__icon" aria-hidden="true"></i>
            <input
                type="search"
                class="live-search-input"
                placeholder="Search by name, ID, household, address…"
                data-target-table="#residentsTableBody"
                data-empty-state="#residentsEmptyState"
                data-result-count="#residentsResultCount"
                aria-label="Search residents"
                autocomplete="off">
            <button class="ds-search__clear clear-search-btn" type="button" aria-label="Clear search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <span class="ds-result-count" id="residentsResultCount" aria-live="polite"></span>
    </div>

    <?php
        $residentSearchFields = [
            [
                'name' => 'search',
                'label' => 'Name or ID',
                'type' => 'text',
                'placeholder' => 'e.g., Maria Santos or 2024-001'
            ],
            [
                'name' => 'category',
                'label' => 'Focus Segment',
                'type' => 'select',
                'options' => [
                    'pwd' => 'Persons with Disability',
                    'senior' => 'Senior Citizens',
                    'teen' => 'Teens',
                    'voter' => 'Registered Voters',
                    '4ps' => '4Ps Beneficiaries',
                    'head' => 'Household Heads'
                ],
                'placeholder' => 'All segments'
            ],
            [
                'name' => 'gender',
                'label' => 'Gender',
                'type' => 'select',
                'options' => [
                    'Male' => 'Male',
                    'Female' => 'Female'
                ],
                'placeholder' => 'All genders'
            ],
            [
                'name' => 'purok',
                'label' => 'Purok',
                'type' => 'select',
                'options' => $purokOptions,
                'placeholder' => 'All puroks'
            ],
        ];
    ?>

    <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['route' => route('residents.index'),'title' => 'Filter Residents','icon' => 'bi-people','fields' => $residentSearchFields,'advanced' => false,'inline' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('residents.index')),'title' => 'Filter Residents','icon' => 'bi-people','fields' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($residentSearchFields),'advanced' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'inline' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>

    <?php if($residents->count() > 0): ?>
        <div class="ds-table-wrapper" id="residentsTableWrapper">
            <div class="table-responsive">
                <table class="table ds-table" id="residentsTable">
                    <thead>
                        <tr>
                            <th scope="col">Resident ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Age / Sex</th>
                            <th scope="col">Household</th>
                            <th scope="col" class="col-address">Address</th>
                            <th scope="col">Categories</th>
                            <th scope="col">Status</th>
                            <?php if(auth()->user()->isSecretary()): ?>
                                <th scope="col" class="text-end">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="residentsTableBody">
                        <?php $__currentLoopData = $residents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $categories = [];
                                if($resident->is_household_head) $categories[] = 'Head';
                                if($resident->is_pwd) $categories[] = 'PWD';
                                if($resident->is_senior_citizen) $categories[] = 'Senior';
                                if($resident->is_voter) $categories[] = 'Voter';
                                if($resident->is_4ps_beneficiary) $categories[] = '4Ps';

                                $searchText = implode(' ', [
                                    $resident->resident_id,
                                    $resident->full_name,
                                    $resident->age . ' yrs',
                                    ucfirst($resident->sex),
                                    $resident->household ? $resident->household->household_id : 'No Household',
                                    $resident->household ? $resident->household->full_address : '',
                                    ucfirst($resident->status),
                                    implode(' ', $categories)
                                ]);
                            ?>
                            <tr
                                class="clickable-row"
                                data-href="<?php echo e(route('residents.show', $resident)); ?>"
                                data-search-text="<?php echo e($searchText); ?>"
                                onclick="window.location.href='<?php echo e(route('residents.show', $resident)); ?>'"
                                title="Click to view resident details"
                            >
                                <td data-label="Resident ID">
                                    <strong class="text-primary"><?php echo e($resident->resident_id); ?></strong>
                                </td>
                                <td data-label="Name">
                                    <div class="fw-semibold"><?php echo e($resident->full_name); ?></div>
                                </td>
                                <td data-label="Age / Sex"><?php echo e($resident->age); ?> / <?php echo e(ucfirst($resident->sex)); ?></td>
                                <td data-label="Household"><?php echo e($resident->household ? $resident->household->household_id : '—'); ?></td>
                                <td data-label="Address" class="ds-text-truncate"><?php echo e($resident->household ? $resident->household->full_address : '—'); ?></td>
                                <td data-label="Categories">
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="ds-chip"><?php echo e($category); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    <?php if($resident->status === 'active'): ?>
                                        <span class="ds-badge ds-badge--success">Active</span>
                                    <?php elseif($resident->status === 'inactive'): ?>
                                        <span class="ds-badge ds-badge--neutral">Inactive</span>
                                    <?php elseif($resident->status === 'deceased'): ?>
                                        <span class="ds-badge ds-badge--danger">Deceased</span>
                                    <?php endif; ?>
                                </td>
                                <?php if(auth()->user()->isSecretary()): ?>
                                    <td data-label="Actions" class="text-end" onclick="event.stopPropagation()">
                                        <div class="ds-actions">
                                            <a href="<?php echo e(route('residents.edit', $resident)); ?>" class="btn btn-icon btn-outline-secondary" title="Edit resident">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-outline-success"
                                                title="Archive resident"
                                                onclick="event.stopPropagation(); if(confirm('Archive this resident?')) { document.getElementById('archive-form-<?php echo e($resident->id); ?>').submit(); }">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                        <form id="archive-form-<?php echo e($resident->id); ?>" action="<?php echo e(route('residents.archive', $resident)); ?>" method="POST" class="d-none">
                                            <?php echo csrf_field(); ?>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="ds-table-foot">
                <?php if(method_exists($residents, 'firstItem') && $residents->count() > 0): ?>
                    <span class="ds-table-foot__meta">
                        Showing <?php echo e(number_format($residents->firstItem())); ?>–<?php echo e(number_format($residents->lastItem())); ?> of <?php echo e(number_format($residentCount)); ?> residents
                    </span>
                <?php endif; ?>
                <?php echo e($residents->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="ds-empty-state">
            <div class="ds-empty-state__icon"><i class="bi bi-people"></i></div>
            <p class="ds-empty-state__title">No residents yet</p>
            <p class="ds-empty-state__description">Start by creating households and adding members so records appear here.</p>
            <?php if(auth()->user()->isSecretary()): ?>
                <a href="<?php echo e(route('households.create')); ?>" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Register first household</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div id="residentsEmptyState" class="ds-empty-state search-empty-state" hidden>
        <div class="ds-empty-state__icon"><i class="bi bi-search"></i></div>
        <p class="ds-empty-state__title">No results</p>
        <p class="ds-empty-state__description">We couldn’t find residents matching your filters.</p>
        <button class="btn btn-secondary clear-search-btn" type="button">Clear search</button>
    </div>

    <?php if(auth()->user()->isSecretary() && Route::has('residents.create')): ?>
        <a href="<?php echo e(route('residents.create')); ?>" class="fab d-md-none" aria-label="Add resident">
            <i class="bi bi-plus-lg"></i>
        </a>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.clickable-row').forEach(function(row) {
        row.style.cursor = 'pointer';
        row.addEventListener('mouseenter', function() {
            this.classList.add('table-active');
        });
        row.addEventListener('mouseleave', function() {
            this.classList.remove('table-active');
        });
        row.addEventListener('click', function(e) {
            // Prevent click if clicking on action buttons
            if(e.target.closest('.ds-actions')) return;
            window.location = this.getAttribute('data-href');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\matina_final\resources\views/residents/index.blade.php ENDPATH**/ ?>