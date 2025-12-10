

<?php $__env->startSection('title', 'Households'); ?>

<?php
    use Illuminate\Support\Facades\Route;
    $householdCount = method_exists($households, 'total') ? $households->total() : $households->count();
?>

<?php $__env->startSection('content'); ?>
<div class="ds-page" data-search-scope>
    <nav class="ds-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
        <span class="ds-breadcrumbs__separator">/</span>
        <span>Households</span>
    </nav>

    <div class="ds-page__header">
        <div>
            <h1 class="page-title d-flex align-items-center gap-2"><i class="bi bi-house"></i>Households</h1>
            <p class="ds-page__subtitle">Track household records, heads, and program affiliations with responsive controls.</p>
        </div>
        <div class="ds-toolbar__group">
            <div class="ds-metric" role="presentation">
                <span class="ds-metric__label">Total Households</span>
                <span class="ds-metric__value"><?php echo e(number_format($householdCount)); ?></span>
            </div>
            <?php if(auth()->user()->isSecretary() && Route::has('households.create')): ?>
                <a href="<?php echo e(route('households.create')); ?>" class="btn btn-primary d-none d-md-inline-flex">
                    <i class="bi bi-plus-circle"></i>
                    <span>Register Household</span>
                </a>
            <?php elseif(auth()->user()->isStaff() && Route::has('staff.households.create')): ?>
                <a href="<?php echo e(route('staff.households.create')); ?>" class="btn btn-success d-none d-md-inline-flex">
                    <i class="bi bi-house-add"></i>
                    <span>Register Household</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="ds-info-banner" role="note">
        <i class="bi bi-layers"></i>
        <span>Keep household information updated to ensure resident and calamity analytics remain accurate.</span>
    </div>

    <div class="ds-toolbar mt-3">
        <div class="ds-search" role="search">
            <i class="bi bi-search ds-search__icon" aria-hidden="true"></i>
            <input
                type="search"
                class="live-search-input"
                placeholder="Search by ID, head name, address, purok…"
                data-target-table="#householdsTableBody"
                data-empty-state="#householdsEmptyState"
                data-result-count="#householdsResultCount"
                aria-label="Search households"
                autocomplete="off">
            <button class="ds-search__clear clear-search-btn" type="button" aria-label="Clear search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <span class="ds-result-count" id="householdsResultCount" aria-live="polite"></span>
    </div>

    <?php
        $householdSearchFields = [
            [
                'name' => 'search',
                'label' => 'Household ID or Address',
                'type' => 'text',
                'placeholder' => 'e.g., HH-2024-001 or Purok 2'
            ],
            [
                'name' => 'head_name',
                'label' => 'Primary Head',
                'type' => 'text',
                'placeholder' => 'Enter head name'
            ],
            [
                'name' => 'purok_id',
                'label' => 'Purok',
                'type' => 'select',
                'options' => $puroks->pluck('purok_name', 'id')->toArray(),
                'placeholder' => 'All puroks'
            ],
            [
                'name' => 'status',
                'label' => 'Approval Status',
                'type' => 'select',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'pending' => 'Pending Approval'
                ],
                'placeholder' => 'All statuses'
            ],
        ];
    ?>

    <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['route' => route('households.index'),'title' => 'Filter Households','icon' => 'bi-house-fill','fields' => $householdSearchFields,'advanced' => false,'inline' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('households.index')),'title' => 'Filter Households','icon' => 'bi-house-fill','fields' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($householdSearchFields),'advanced' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'inline' => true]); ?>
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

    <?php if($households->count() > 0): ?>
        <div class="ds-table-wrapper" id="householdsTableWrapper">
            <div class="table-responsive">
                <table class="table ds-table" id="householdsTable">
                    <thead>
                        <tr>
                            <th scope="col">Household ID</th>
                            <th scope="col">Primary Head</th>
                            <th scope="col">Address</th>
                            <th scope="col">Purok</th>
                            <th scope="col">Members</th>
                            <th scope="col">Status</th>
                            <?php if(auth()->user()->isSecretary()): ?>
                                <th scope="col" class="text-end">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="householdsTableBody">
                        <?php $__currentLoopData = $households; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $household): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $headName = $household->officialHead ? $household->officialHead->full_name : ($household->head ? $household->head->full_name : 'No Head Assigned');
                                $purokName = $household->purok ? (is_object($household->purok) ? $household->purok->purok_name : $household->purok) : '';
                                $purokName = $purokName ?: '—';
                                $searchText = implode(' ', [
                                    $household->household_id,
                                    $headName,
                                    $household->address,
                                    $purokName,
                                    $household->total_members . ' members',
                                    $household->approval_status
                                ]);
                            ?>
                            <tr
                                class="clickable-row"
                                data-href="<?php echo e(route('households.show', $household)); ?>"
                                data-search-text="<?php echo e($searchText); ?>"
                                title="Click to view household details"
                            >
                                <td data-label="Household ID">
                                    <strong class="text-primary"><?php echo e($household->household_id); ?></strong>
                                </td>
                                <td data-label="Primary Head">
                                    <div class="fw-semibold"><?php echo e($headName); ?></div>
                                </td>
                                <td data-label="Address" class="ds-text-truncate"><?php echo e($household->address ?? '—'); ?></td>
                                <td data-label="Purok"><?php echo e($purokName); ?></td>
                                <td data-label="Members">
                                    <span class="ds-chip ds-chip--neutral">
                                        <i class="bi bi-people-fill"></i>
                                        <?php echo e($household->total_members); ?>

                                    </span>
                                </td>
                                <td data-label="Status">
                                    <?php if($household->approval_status === 'active'): ?>
                                        <span class="ds-badge ds-badge--success">Active</span>
                                    <?php elseif($household->approval_status === 'inactive'): ?>
                                        <span class="ds-badge ds-badge--neutral">Inactive</span>
                                    <?php elseif($household->approval_status === 'pending'): ?>
                                        <span class="ds-badge ds-badge--warning">Pending</span>
                                    <?php else: ?>
                                        <span class="ds-badge ds-badge--neutral"><?php echo e(ucfirst($household->approval_status)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if(auth()->user()->isSecretary()): ?>
                                    <td data-label="Actions" class="text-end" onclick="event.stopPropagation()">
                                        <div class="ds-actions">
                                            <a href="<?php echo e(route('households.edit', $household)); ?>" class="btn btn-icon btn-outline-secondary" title="Edit household">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-outline-success"
                                                title="Archive household"
                                                onclick="event.stopPropagation(); if(confirm('Archive this household? All residents will also be archived.')) { document.getElementById('archive-form-<?php echo e($household->id); ?>').submit(); }">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                        <form id="archive-form-<?php echo e($household->id); ?>" action="<?php echo e(route('households.archive', $household)); ?>" method="POST" class="d-none">
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
                <?php if(method_exists($households, 'firstItem') && $households->count() > 0): ?>
                    <span class="ds-table-foot__meta">
                        Showing <?php echo e(number_format($households->firstItem())); ?>–<?php echo e(number_format($households->lastItem())); ?> of <?php echo e(number_format($householdCount)); ?> households
                    </span>
                <?php endif; ?>
                <?php echo e($households->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="ds-empty-state">
            <div class="ds-empty-state__icon"><i class="bi bi-house"></i></div>
            <p class="ds-empty-state__title">No households yet</p>
            <p class="ds-empty-state__description">Register households to start managing residents and services.</p>
            <?php if(auth()->user()->isSecretary() && Route::has('households.create')): ?>
                <a href="<?php echo e(route('households.create')); ?>" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i>
                    <span>Register first household</span>
                </a>
            <?php elseif(auth()->user()->isStaff() && Route::has('staff.households.create')): ?>
                <a href="<?php echo e(route('staff.households.create')); ?>" class="btn btn-success mt-2">
                    <i class="bi bi-house-add"></i>
                    <span>Register first household</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div id="householdsEmptyState" class="ds-empty-state search-empty-state" hidden>
        <div class="ds-empty-state__icon"><i class="bi bi-search"></i></div>
        <p class="ds-empty-state__title">No results</p>
        <p class="ds-empty-state__description">We couldn’t find households matching your filters.</p>
        <button class="btn btn-secondary clear-search-btn" type="button">Clear search</button>
    </div>

    <?php if(auth()->user()->isSecretary() && Route::has('households.create')): ?>
        <a href="<?php echo e(route('households.create')); ?>" class="fab d-md-none" aria-label="Register household">
            <i class="bi bi-plus-lg"></i>
        </a>
    <?php elseif(auth()->user()->isStaff() && Route::has('staff.households.create')): ?>
        <a href="<?php echo e(route('staff.households.create')); ?>" class="fab d-md-none" aria-label="Register household">
            <i class="bi bi-plus-lg"></i>
        </a>
    <?php endif; ?>
        </div>
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\matina_final\resources\views/households/index.blade.php ENDPATH**/ ?>