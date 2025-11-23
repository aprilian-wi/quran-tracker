<?php
// src/Views/parent/my_children.php
global $pdo;
require_once __DIR__ . '/../../Controllers/ParentController.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new ParentController($pdo);
$children = $controller->myChildren();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-person-hearts"></i> My Children</h3>

<?php if (empty($children)): ?>
    <div class="alert alert-info">
        No children registered yet. Please contact your child's teacher.
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($children as $child): ?>
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

                        <!-- Progress Summary -->
                        <?php
                        $progressModel = new Progress($pdo);
                        $summary = $progressModel->getProgressSummary($child['id']);
                        $latest = $progressModel->getLatest($child['id']);
                        ?>
                        <div class="text-center my-3">
                            <div class="progress-circle" data-progress="<?= $summary['percentage'] ?>">
                                <svg width="100" height="100">
                                    <circle class="bg" cx="50" cy="50" r="44"></circle>
                                    <circle class="progress" cx="50" cy="50" r="44"></circle>
                                </svg>
                                <div class="text"><?= $summary['percentage'] ?>%</div>
                            </div>
                            <p class="mt-2 small text-muted">
                                <?= $summary['memorized'] ?> verses memorized
                            </p>
                        </div>

                        <!-- Latest Update -->
                        <?php if ($latest): ?>
                            <div class="border-top pt-2 small text-muted">
                                Latest: Juz <?= $latest['juz'] ?>, 
                                Surah <?= $latest['surah_number'] ?>:<?= $latest['verse'] ?>
                                <span class="badge bg-<?= $latest['status'] === 'memorized' ? 'success' : ($latest['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                                    <?php
                                    if ($latest['status'] === 'memorized') {
                                        echo 'Menghafal';
                                    } elseif ($latest['status'] === 'in_progress') {
                                        echo 'Murajaah';
                                    } else {
                                        echo ucfirst($latest['status']);
                                    }
                                    ?>
                                </span>
                                <br>
                                by <?= h($latest['updated_by_name']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress&child_id=<?= $child['id'] ?>"
                                   class="btn btn-success flex-fill">
                                    <i class="bi bi-pencil"></i> Tahfidz
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_books&child_id=<?= $child['id'] ?>"
                                   class="btn btn-warning flex-fill">
                                    <i class="bi bi-book"></i> Tahsin
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
    stroke-dasharray: 276; 
    stroke-dashoffset: 276; 
}
.progress-circle .text {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}
</style>

<script>
document.querySelectorAll('.progress-circle').forEach(el => {
    const progress = el.dataset.progress;
    const offset = 276 - (276 * progress / 100);
    setTimeout(() => {
        el.querySelector('.progress').style.strokeDashoffset = offset;
    }, 300);
});
</script>