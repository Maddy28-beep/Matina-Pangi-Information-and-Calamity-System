
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'route' => '',
    'title' => 'Search & Filter',
    'icon' => 'bi-funnel-fill',
    'fields' => [],
    'advanced' => false,
    'inline' => false,
    'exportRoute' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'route' => '',
    'title' => 'Search & Filter',
    'icon' => 'bi-funnel-fill',
    'fields' => [],
    'advanced' => false,
    'inline' => false,
    'exportRoute' => null,
]); ?>
<?php foreach (array_filter(([
    'route' => '',
    'title' => 'Search & Filter',
    'icon' => 'bi-funnel-fill',
    'fields' => [],
    'advanced' => false,
    'inline' => false,
    'exportRoute' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $advancedToggleId = $advanced ? 'advanced-toggle-' . uniqid() : null;
    $advancedRegionId = $advancedToggleId ? $advancedToggleId . '-panel' : null;
?>

<section class="ds-card ds-card--form" aria-label="<?php echo e($title); ?> filters">
    <header class="ds-card__header">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i class="<?php echo e($icon); ?>"></i>
            <span><?php echo e($title); ?></span>
        </h3>
        <?php if($advanced): ?>
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                data-advanced-toggle
                data-advanced-target="#<?php echo e($advancedRegionId); ?>"
                id="<?php echo e($advancedToggleId); ?>"
                aria-expanded="false"
                aria-controls="<?php echo e($advancedRegionId); ?>">
                <i class="bi bi-gear"></i>
                <span class="ms-1">Advanced</span>
            </button>
        <?php endif; ?>
    </header>

    <div class="ds-card__body">
        <form action="<?php echo e($route); ?>" method="GET" class="ds-filter-form" novalidate>
            <?php if($inline): ?>
                <div class="ds-inline-grid" style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap:12px; align-items:end; width:100%;">
                    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $type = $field['type'] ?? 'text'; ?>
                        <div style="width:100%">
                            <label class="form-label small" for="filter-<?php echo e($field['name']); ?>"><?php echo e($field['label']); ?></label>

                            <?php if($type === 'text'): ?>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
                                    value="<?php echo e(request($field['name'])); ?>">

                            <?php elseif($type === 'select'): ?>
                                <select
                                    class="form-select"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>">
                                    <option value=""><?php echo e($field['placeholder'] ?? 'All'); ?></option>
                                    <?php $__currentLoopData = ($field['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e(request($field['name']) == $value ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                            <?php elseif($type === 'date'): ?>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    value="<?php echo e(request($field['name'])); ?>">

                            <?php elseif($type === 'number'): ?>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
                                    value="<?php echo e(request($field['name'])); ?>"
                                    min="<?php echo e($field['min'] ?? ''); ?>"
                                    max="<?php echo e($field['max'] ?? ''); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if(isset($exportRoute) && $exportRoute): ?>
                        <div class="btn-group ms-auto">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e($exportRoute); ?>?<?php echo e(http_build_query(request()->all())); ?>">
                                        <i class="bi bi-file-excel"></i> Export to Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e($exportRoute); ?>?format=pdf&<?php echo e(http_build_query(request()->all())); ?>">
                                        <i class="bi bi-file-pdf"></i> Export to PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="ds-toolbar__group d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="<?php echo e($route); ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Clear</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="ds-form-grid">
                    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="ds-form-grid__item">
                            <label class="form-label small" for="filter-<?php echo e($field['name']); ?>"><?php echo e($field['label']); ?></label>

                            <?php if(($field['type'] ?? 'text') === 'text'): ?>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
                                    value="<?php echo e(request($field['name'])); ?>">

                            <?php elseif(($field['type'] ?? 'text') === 'select'): ?>
                                <select
                                    class="form-select"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>">
                                    <option value=""><?php echo e($field['placeholder'] ?? 'All'); ?></option>
                                    <?php $__currentLoopData = ($field['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e(request($field['name']) == $value ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                            <?php elseif(($field['type'] ?? 'text') === 'date'): ?>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    value="<?php echo e(request($field['name'])); ?>">

                            <?php elseif(($field['type'] ?? 'text') === 'number'): ?>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="filter-<?php echo e($field['name']); ?>"
                                    name="<?php echo e($field['name']); ?>"
                                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
                                    value="<?php echo e(request($field['name'])); ?>"
                                    min="<?php echo e($field['min'] ?? ''); ?>"
                                    max="<?php echo e($field['max'] ?? ''); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="ds-card__footer">
                    <?php if(isset($exportRoute) && $exportRoute): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e($exportRoute); ?>?<?php echo e(http_build_query(request()->all())); ?>">
                                        <i class="bi bi-file-excel"></i> Export to Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e($exportRoute); ?>?format=pdf&<?php echo e(http_build_query(request()->all())); ?>">
                                        <i class="bi bi-file-pdf"></i> Export to PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="ds-toolbar__group">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="<?php echo e($route); ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Clear</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($advanced): ?>
                <div id="<?php echo e($advancedRegionId); ?>" class="ds-advanced" hidden>
                    <hr>
                    <div class="ds-form-grid">
                        <?php echo e($advancedSlot ?? ''); ?>

                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-advanced-toggle]').forEach(function(toggleBtn) {
        const targetSelector = toggleBtn.getAttribute('data-advanced-target');
        const target = targetSelector ? document.querySelector(targetSelector) : null;
        if (!target) {
            return;
        }

        toggleBtn.addEventListener('click', function() {
            const isHidden = target.hasAttribute('hidden');
            if (isHidden) {
                target.removeAttribute('hidden');
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleBtn.innerHTML = '<i class="bi bi-gear-fill"></i><span class="ms-1">Hide Advanced</span>';
            } else {
                target.setAttribute('hidden', '');
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.innerHTML = '<i class="bi bi-gear"></i><span class="ms-1">Advanced</span>';
            }
        });
    });

    document.querySelectorAll('.auto-submit').forEach(function(select) {
        select.addEventListener('change', function() {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\matina_final\resources\views/components/search-filter.blade.php ENDPATH**/ ?>