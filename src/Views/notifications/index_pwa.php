<?php
// src/Views/notifications/index_pwa.php
require_once __DIR__ . '/../../Models/Notification.php';
require_once __DIR__ . '/../../Models/Child.php';

$childModel = new Child($pdo);
// Only for parents for now (Teachers might not get similar notifications logic yet, assuming Parent View)
// If role is teacher, maybe they don't see this or logic differs. For now focusing on Parents.
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$children = [];
$notifications = [];

if ($role === 'parent') {
    $children = $childModel->getByParent($user_id);
    $child_ids = array_column($children, 'id');
    
    $notificationModel = new Notification($pdo);
    // User requested "Inbox Zero" style: Only show unread.
    $notifications = $notificationModel->getByChildren($child_ids, 50, true);
} else {
    // For teachers...
}

?>

<section class="mb-6 pt-2 px-1 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <a href="<?= BASE_URL ?>public/index.php?page=parent/my_children&mode=pwa" class="text-text-main-light dark:text-white hover:text-primary transition-colors">
            <span class="material-icons-round text-2xl">arrow_back</span>
        </a>
        <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Notifikasi</h2>
    </div>
    <?php if (!empty($notifications)): ?>
    <button id="markAllReadBtn" class="text-xs font-medium text-primary dark:text-green-400 hover:text-primary-dark uppercase tracking-wide">
        Tandai Sudah Baca
    </button>
    <?php endif; ?>
</section>

<section class="space-y-4 pb-20 relative">
    <!-- Empty State (Hidden by default if notifs exist) -->
    <div id="empty-state" class="<?= empty($notifications) ? 'flex' : 'hidden' ?> flex-col items-center justify-center py-20 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
            <span class="material-icons-round text-3xl">notifications_off</span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada notifikasi baru.</p>
    </div>

    <!-- Notification List -->
    <div id="notification-list" class="space-y-4">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): 
            // Since we fetch only unread, these are all unread.
            $bgColor = 'bg-primary/5 dark:bg-primary/10 border-primary/20';
        ?>
        <div class="notification-item <?= $bgColor ?> rounded-2xl p-4 border shadow-sm relative transition-all duration-500" data-id="<?= $notif['id'] ?>">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-primary dark:text-green-400 shadow-sm shrink-0">
                    <span class="material-icons-round text-xl"><?= $notif['icon'] ?? 'notifications' ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <p class="text-sm font-bold text-gray-800 dark:text-white truncate">
                            <?= h($notif['child_name']) ?>
                        </p>
                        <span class="text-[10px] text-gray-400 shrink-0 ml-2">
                            <?= date('d M H:i', strtotime($notif['created_at'])) ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Update oleh <span class="font-medium"><?= h($notif['updated_by_name']) ?></span>
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-2 leading-relaxed">
                        <?= h($notif['message']) ?>
                    </p>
                </div>
                <div class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-1.5 unread-indicator"></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</section>

<!-- CSRF Token -->
<input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<script>
document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
    if (!confirm('Tandai semua sebagai sudah dibaca?')) return;

    fetch('index.php?page=api/mark_all_read', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=mark_all'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const list = document.getElementById('notification-list');
            const items = list.querySelectorAll('.notification-item');
            
            // Animate removal
            items.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
            });

            setTimeout(() => {
                list.innerHTML = ''; // Clear list
                document.getElementById('empty-state').classList.remove('hidden');
                document.getElementById('empty-state').classList.add('flex');
                this.remove(); // Remove "Mark All" button
                
                // Reset Badge
                const badge = document.getElementById('notif-badge');
                if(badge) {
                    badge.classList.remove('scale-100');
                    badge.classList.add('scale-0');
                }
            }, 300);
        }
    });
});
</script>
