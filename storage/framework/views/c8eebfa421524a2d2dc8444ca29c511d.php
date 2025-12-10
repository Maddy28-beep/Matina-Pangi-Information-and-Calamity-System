<?php $__env->startSection('title', 'Calamity Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-content-wrapper { display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
    .dashboard-content { max-width: 1200px; width: 100%; margin: 0 auto; padding: 0; padding-top: 0; margin-left: 120px; }
    .page-header { margin-bottom: 8px !important; padding-bottom: 0; border-bottom: none; }
    .stats-grid { display: grid; gap: 1.25rem; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 1.25rem; align-items: stretch; }
    .stat-card { cursor: pointer; transition: background 0.15s ease, transform 0.15s ease; height: 100%; }
    .stat-card:hover { background: #f9fafb; transform: translateY(-2px); }
    .stat-card .card-body { padding: 1.5rem; height: 100%; display: flex; flex-direction: column; justify-content: center; min-height: 120px; }
    .stat-card h3 { font-size: 2.25rem; margin-bottom: 0.25rem; line-height: 1; }
    .analytics-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .chart-wrapper { display: block; }
    .chart-wrapper canvas { width: 100% !important; max-width: 100% !important; min-height: 220px; }
    .quick-actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: stretch; }
    .quick-actions-grid > a.btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 48px; padding: 12px 16px !important; box-shadow: none !important; margin: 0 !important; white-space: nowrap; box-sizing: border-box; }
    @media (max-width: 1200px) { .quick-actions-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    @media (max-width: 992px) { .quick-actions-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 576px) { .quick-actions-grid { grid-template-columns: 1fr; } }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 992px) { .analytics-grid { grid-template-columns: 1fr; } }
    @media (max-width: 576px) {
        .dashboard-content { padding: 0; }
        .stats-grid { grid-template-columns: 1fr; }
        .analytics-grid { grid-template-columns: 1fr; }
    }
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background: #f9fafb; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-content-wrapper" style="background: #f6f8f7; min-height: 100vh;">
    <div class="dashboard-content" style="background: #f6f8f7; border-radius: 24px; box-shadow: 0 4px 32px 0 rgba(44, 111, 82, 0.08); padding: 40px 32px; max-width: 1400px; margin: 0 auto;">
        <div class="page-header">
            <h2 class="mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Calamity Dashboard</h2>
            <p class="text-muted mb-0">Monitoring, coordination, and response overview.</p>
        </div>

        <div class="stats-grid">
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('calamities.index')); ?>?status=ongoing'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1 text-danger"><?php echo e(number_format($ongoing)); ?></h3>
                        <p class="text-muted mb-0">Ongoing Incidents</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('calamities.index')); ?>?status=resolved'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1 text-success"><?php echo e(number_format($resolved)); ?></h3>
                        <p class="text-muted mb-0">Resolved Incidents</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('web.calamity-affected-households.index')); ?>'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($totalAffectedHouseholds)); ?></h3>
                        <p class="text-muted mb-0">Affected Households</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('web.calamity-affected-households.index')); ?>?needing_relief=1'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($needingRelief)); ?></h3>
                        <p class="text-muted mb-0">Needing Relief</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('web.response-team-members.index')); ?>'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($rescuesToday)); ?></h3>
                        <p class="text-muted mb-0">Rescues Today</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('web.notifications.index')); ?>'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($notificationsPending)); ?></h3>
                        <p class="text-muted mb-0">Pending Notices</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('calamities.index')); ?>'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($incidents->total())); ?></h3>
                        <p class="text-muted mb-0">Listed Incidents</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="card stat-card h-100" onclick="window.location.href='<?php echo e(route('calamities.index')); ?>'">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1"><?php echo e(number_format($recentCalamities->count())); ?></h3>
                        <p class="text-muted mb-0">Recent Incidents</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-grid mb-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Monthly Incidents</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="monthlyChart" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>Incidents by Type</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="typeChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-info"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="quick-actions-grid">
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('calamities.index')); ?>">Manage Incidents</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.calamity-affected-households.index')); ?>">Affected Households</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.evacuation-centers.index')); ?>">Evacuation Centers</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.relief-distributions.index')); ?>">Relief Distributions</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.damage-assessments.index')); ?>">Damage Assessments</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.notifications.index')); ?>">Notifications</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.response-team-members.index')); ?>">Response Team</a>
                    <a class="btn btn-outline-primary w-100" href="<?php echo e(route('web.calamity-reports.index')); ?>">Reports</a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-2 text-primary"></i>Recently Added Incidents</h5>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-sm btn-outline-light text-white" href="<?php echo e(route('calamities.index')); ?>">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Severity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $recentCalamities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="clickable-row" data-href="<?php echo e(route('calamities.show', $c)); ?>">
                                    <td class="fw-semibold"><?php echo e($c->calamity_name ?? ucfirst($c->calamity_type)); ?></td>
                                    <td><?php echo e(ucfirst($c->calamity_type)); ?></td>
                                    <td><small class="text-muted"><i class="bi bi-clock"></i> <?php echo e(optional($c->created_at)->diffForHumans()); ?></small></td>
                                    <td><?php echo e(ucfirst($c->status)); ?></td>
                                    <td><?php echo e(ucfirst($c->severity_level)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    const primary = '#1E3A8A';
    const neutral = '#E5E7EB';
    const monthly = <?php echo json_encode($monthlyCounts ?? [], 15, 512) ?>;
    const byType = <?php echo json_encode($byType ?? [], 15, 512) ?>;
    const monthlyCtx = document.getElementById('monthlyChart');
    const typeCtx = document.getElementById('typeChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: monthly.map(m => m[0]),
                datasets: [{ label: 'Incidents / month', data: monthly.map(m => m[1]), borderColor: primary, backgroundColor: 'rgba(30,58,138,.15)', tension: .3 }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, grid: { color: neutral } }, x: { grid: { color: neutral } } } }
        });
    }
    if (typeCtx) {
        new Chart(typeCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: byType.map(t => t[0]),
                datasets: [{ label: 'Incidents by type', data: byType.map(t => t[1]), backgroundColor: primary, borderRadius: 6 }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, grid: { color: neutral } }, x: { grid: { display: false } } } }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.clickable-row').forEach(function(row) {
            row.addEventListener('click', function() { window.location = this.getAttribute('data-href'); });
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\matina_final\resources\views/calamity/dashboard.blade.php ENDPATH**/ ?>