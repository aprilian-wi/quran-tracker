<?php
// src/Views/dashboard/parent.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new DashboardController($pdo);
$data = $controller->index();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-person-hearts"></i> My Children</h3>

<?php if (empty($data['children'])): ?>
    <div class="alert alert-info">
        No children registered yet. Contact your teacher.
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($data['children'] as $child): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0"><?= h($child['name']) ?></h5>
                            <?php if ($child['class_name']): ?>
                                <span class="badge bg-success"><?= h($child['class_name']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No Class</span>
                            <?php endif; ?>
                        </div>

                        <!-- Progress Circle -->
                        <div class="text-center my-4">
                            <div class="progress-circle" data-progress="<?= $child['progress']['percentage'] ?>">
                                <svg width="120" height="120">
                                    <circle class="bg" cx="60" cy="60" r="54"></circle>
                                    <circle class="progress" cx="60" cy="60" r="54"></circle>
                                </svg>
                                <div class="text"><?= $child['progress']['percentage'] ?>%</div>
                            </div>
                            <p class="mt-2 text-muted">
                                <?= $child['progress']['total_verses'] ?> / 6236 verses memorized
                            </p>
                        </div>

                        <!-- Latest Update -->
                        <?php if ($child['latest']): ?>
                            <div class="border-top pt-3">
                                <small class="text-muted">
                                    Latest: 
                                    Juz <?= $child['latest']['juz'] ?>, 
                                    Surah <?= $child['latest']['surah_number'] ?>:<?= $child['latest']['verse'] ?>
                                    <span class="badge bg-<?= $child['latest']['status'] === 'memorized' ? 'success' : ($child['latest']['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                                        <?php
                                        if ($child['latest']['status'] === 'memorized') {
                                            echo 'Menghafal';
                                        } elseif ($child['latest']['status'] === 'in_progress') {
                                            echo 'Murajaah';
                                        } else {
                                            echo ucfirst($child['latest']['status']);
                                        }
                                        ?>
                                    </span>
                                    <br>
                                    by <?= h($child['latest']['updated_by_name']) ?> 
                                    on <?= date('M j, Y', strtotime($child['latest']['updated_at'])) ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No progress recorded yet.</p>
                        <?php endif; ?>

                        <div class="mt-3">
                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress&child_id=<?= $child['id'] ?>"
                                   class="btn btn-success flex-fill">
                                    Tahfidz
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_books&child_id=<?= $child['id'] ?>"
                                   class="btn btn-warning flex-fill">
                                    Tahsin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.progress-circle svg { width: 100%; height: auto; }
.progress-circle .bg { stroke: #e9ecef; }
.progress-circle .progress { 
    stroke: #28a745; 
    stroke-dasharray: 377; 
    stroke-dashoffset: 377; 
}
</style>

<script>
// Animate progress circles
document.querySelectorAll('.progress-circle').forEach(el => {
    const progress = el.dataset.progress;
    const offset = 377 - (377 * progress / 100);
    setTimeout(() => {
        el.querySelector('.progress').style.strokeDashoffset = offset;
    }, 300);
});
</script>