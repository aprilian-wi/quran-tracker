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

                        <!-- Child Photo with Edit Icon -->
                        <div class="text-center my-4 position-relative" style="width:120px; height:120px; margin-left:auto; margin-right:auto;">
                            <?php if (!empty($child['photo'])): ?>
                                <img src="<?= BASE_URL ?>public/uploads/children_photos/<?= htmlspecialchars($child['photo']) ?>" alt="Child Photo" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                            <?php else: ?>
                                <div style="width:100%; height:100%; background-color:#eee; border-radius:50%; display:flex; justify-content:center; align-items:center; font-size:80px; color:#ccc;">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-light position-absolute" id="btn-edit-photo-<?= $child['id'] ?>"
                                style="bottom:5px; right:5px; border-radius:50%; padding:5px;">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>

                        <!-- Notifications -->
                        <?php if (!empty($child['notifications'])): ?>
                            <div class="border-top pt-3">
                                <?php foreach ($child['notifications'] as $notification): ?>
                                    <div class="alert alert-info alert-dismissible fade show" role="alert" data-notification-id="<?= $notification['id'] ?>">
                                        <small>
                                            <strong>Update:</strong> <?= h($notification['message']) ?>
                                            <br>
                                            by <?= h($notification['updated_by_name']) ?>
                                            on <?= date('M j, Y', strtotime($notification['created_at'])) ?>
                                        </small>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endforeach; ?>
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
        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_prayers&child_id=<?= $child['id'] ?>"
           class="btn btn-info flex-fill">
            Doa
        </a>
        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_hadiths&child_id=<?= $child['id'] ?>"
           class="btn btn-danger flex-fill">
            Hadith
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

<?php include __DIR__ . '/child_photo_modal.php'; ?>

<script>
document.querySelectorAll('[id^="btn-edit-photo-"]').forEach(button => {
    button.addEventListener('click', event => {
        const childId = event.currentTarget.id.replace('btn-edit-photo-', '');
        const modalChildIdInput = document.getElementById('modalChildId');
        modalChildIdInput.value = childId;

        const modalElement = document.getElementById('childPhotoModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });
});

document.getElementById('childPhotoForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const formData = new FormData(form);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            // Refresh the entire page to ensure image display is updated correctly
            window.location.reload();
            // Hide modal
            const modalElement = document.getElementById('childPhotoModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        } else {
            alert('Error: ' + (data.error || 'Upload failed'));
        }
    } catch (error) {
        alert('Error uploading photo');
    }
});

// Handle notification dismissal
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('btn-close') && event.target.closest('.alert[data-notification-id]')) {
        const alertElement = event.target.closest('.alert');
        const notificationId = alertElement.getAttribute('data-notification-id');

        if (notificationId) {
            // Send AJAX request to mark notification as viewed
            fetch('<?= BASE_URL ?>public/index.php?page=mark_notification_viewed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: 'notification_id=' + encodeURIComponent(notificationId),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to mark notification as viewed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }
});
</script>
