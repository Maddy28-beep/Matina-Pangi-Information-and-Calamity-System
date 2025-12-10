<?php $__env->startSection('title', 'Add Affected Household'); ?>

<?php $__env->startSection('content'); ?><div class="ds-page">
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('web.calamity-affected-households.index')); ?>">Affected Households</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>

<div class="card">
  <div class="card-body">
    <?php
      $calamities = \App\Models\Calamity::orderBy('date_occurred','desc')->get();
      $households = \App\Models\Household::approved()->with('head')->orderBy('household_id')->get();
    ?>
    <form method="POST" action="<?php echo e(route('web.calamity-affected-households.store')); ?>">
      <?php echo csrf_field(); ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Calamity</label>
          <select name="calamity_id" class="form-select" required>
            <option value="" disabled <?php echo e(old('calamity_id') ? '' : 'selected'); ?>>Select Calamity</option>
            <?php $__currentLoopData = $calamities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($c->id); ?>" <?php echo e(old('calamity_id')==$c->id ? 'selected' : ''); ?>>
                <?php echo e($c->calamity_name ?? ucfirst($c->calamity_type)); ?> <?php echo e($c->date_occurred ? '• '.$c->date_occurred->format('Y-m-d') : ''); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Household</label>
          <select name="household_id" class="form-select" required>
            <option value="" disabled <?php echo e(old('household_id') ? '' : 'selected'); ?>>Select Household</option>
            <?php $__currentLoopData = $households; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($hh->id); ?>" <?php echo e(old('household_id')==$hh->id ? 'selected' : ''); ?>>
                <?php echo e($hh->household_id); ?> <?php echo e($hh->head?->full_name ? '• '.$hh->head->full_name : ''); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Damage Level</label>
          <select name="damage_level" class="form-select" required>
            <option value="minor">Minor</option>
            <option value="moderate">Moderate</option>
            <option value="severe">Severe</option>
            <option value="total">Total</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Injuries</label>
          <input type="number" name="injured" class="form-control" min="0" value="<?php echo e(old('injured',0)); ?>" placeholder="Enter injured count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Missing</label>
          <input type="number" name="missing" class="form-control" min="0" value="<?php echo e(old('missing',0)); ?>" placeholder="Enter missing count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Deceased</label>
          <input type="number" name="casualties" class="form-control" min="0" value="<?php echo e(old('casualties',0)); ?>" placeholder="Enter deceased count">
        </div>
        <div class="col-md-3">
          <label class="form-label">Evacuation Status</label>
          <select name="evacuation_status" class="form-select">
            <option value="in_home">In Home</option>
            <option value="evacuated">Evacuated</option>
            <option value="returned">Returned</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Damage Cost</label>
          <input type="number" step="0.01" name="house_damage_cost" class="form-control" value="<?php echo e(old('house_damage_cost')); ?>" placeholder="Enter estimated damage cost">
        </div>
        <div class="col-md-4">
          <label class="form-label">Needs Temporary Shelter</label>
          <select name="needs_temporary_shelter" class="form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Relief Received</label>
          <select name="relief_received" class="form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Items</label>
          <input type="text" name="relief_items[]" class="form-control" placeholder="Comma-separated">
        </div>
        <div class="col-md-6">
          <label class="form-label">Relief Date</label>
          <input type="date" name="relief_date" class="form-control" value="<?php echo e(old('relief_date')); ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Needs</label>
          <textarea name="needs" class="form-control" rows="3"><?php echo e(old('needs')); ?></textarea>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="<?php echo e(route('web.calamity-affected-households.index')); ?>" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\matina_final\resources\views/calamity/affected/create.blade.php ENDPATH**/ ?>