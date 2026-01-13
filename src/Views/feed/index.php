<?php
// src/Views/feed/index.php
// src/Views/feed/index.php
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Activity Feed</h2>
        <?php if (hasRole('teacher') || hasRole('superadmin') || hasRole('school_admin')): ?>
            <a href="<?= BASE_URL ?>public/index.php?page=feed/create&mode=pwa"
                class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-full text-sm font-medium shadow-md transition-colors flex items-center space-x-1">
                <span class="material-icons-round text-lg">add</span>
                <span>Update</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Feed List -->
    <?php if (empty($feeds)): ?>
        <div class="flex flex-col items-center justify-center py-12 text-center space-y-3">
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full">
                <span class="material-icons-round text-4xl text-gray-400">perm_media</span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada aktivitas terbaru.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4 pb-20 max-w-lg mx-auto">
            <?php foreach ($feeds as $feed): ?>
                <?php
                // Generate initials
                $initials = '';
                $parts = explode(' ', $feed['user_name']);
                foreach ($parts as $part) {
                    if (strlen($initials) < 2)
                        $initials .= strtoupper(substr($part, 0, 1));
                }
                ?>
                <div class="bg-white dark:bg-surface-dark border-b border-gray-200 dark:border-gray-800 md:border md:rounded-xl feed-item"
                    data-id="<?= $feed['id'] ?>">

                    <!-- Header -->
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Avatar Placeholder -->
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-400 to-red-600 p-[2px]">
                                <div class="w-full h-full rounded-full bg-white dark:bg-black p-[2px]">
                                    <div
                                        class="w-full h-full rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[10px] font-bold text-gray-700 dark:text-gray-300">
                                        <?= $initials ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-text-main-light dark:text-text-main-dark leading-none">
                                    <?= h($feed['user_name']) ?>
                                </h3>
                                <?php if ($feed['user_role']): ?>
                                    <span class="text-[10px] text-text-sub-light dark:text-text-sub-dark capitalize block mt-0.5">
                                        <?= h($feed['user_role']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="relative">
                            <?php if ($feed['user_id'] == $_SESSION['user_id']): ?>
                                <button onclick="toggleFeedMenu(<?= $feed['id'] ?>)"
                                    class="text-gray-900 dark:text-white hover:text-gray-600">
                                    <span class="material-icons-round">more_horiz</span>
                                </button>
                                <!-- Dropdown Menu -->
                                <div id="feed-menu-<?= $feed['id'] ?>"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-10 overflow-hidden">
                                    <button onclick="openEditModal(<?= $feed['id'] ?>, `<?= h(addslashes($feed['caption'])) ?>`)"
                                        class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center space-x-2">
                                        <span class="material-icons-round text-lg">edit</span>
                                        <span>Edit Caption</span>
                                    </button>
                                    <form action="<?= BASE_URL ?>public/index.php?page=feed/action/delete" method="POST"
                                        onsubmit="return confirmDelete()">
                                        <?= csrfInput() ?>
                                        <input type="hidden" name="feed_id" value="<?= $feed['id'] ?>">
                                        <button type="submit"
                                            class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2">
                                            <span class="material-icons-round text-lg">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <?php if ($feed['content_type'] === 'image' && $feed['content']): ?>
                        <div class="w-full relative bg-gray-100 dark:bg-gray-900 aspect-square group cursor-pointer overflow-hidden"
                            onclick="openPreview('image', '<?= BASE_URL ?>public/<?= h($feed['content']) ?>')">
                            <img src="<?= BASE_URL ?>public/<?= h($feed['content']) ?>" alt="Feed Image"
                                class="w-full h-full object-cover">
                        </div>
                    <?php elseif ($feed['content_type'] === 'video' && $feed['content']): ?>
                        <div class="w-full relative bg-black aspect-square group cursor-pointer"
                            onclick="openPreview('video', '<?= BASE_URL ?>public/<?= h($feed['content']) ?>')">
                            <video class="w-full h-full object-cover pointer-events-none">
                                <source src="<?= BASE_URL ?>public/<?= h($feed['content']) ?>"
                                    type="video/<?= pathinfo($feed['content'], PATHINFO_EXTENSION) ?>">
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="w-16 h-16 rounded-full bg-black/30 backdrop-blur-sm flex items-center justify-center border border-white/50">
                                    <span class="material-icons-round text-white text-3xl">play_arrow</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Bar -->
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-4">
                                <!-- Like -->
                                <button
                                    class="flex items-center space-x-1.5 group like-btn <?= $feed['is_liked'] ? 'text-red-500' : 'text-gray-900 dark:text-white' ?>"
                                    onclick="toggleLike(<?= $feed['id'] ?>, this)">
                                    <span class="material-icons-round text-[28px] group-active:scale-90 transition-transform">
                                        <?= $feed['is_liked'] ? 'favorite' : 'favorite_border' ?>
                                    </span>
                                    <span
                                        class="font-bold text-base like-count-text"><?= $feed['like_count'] > 0 ? $feed['like_count'] : '' ?></span>
                                </button>

                                <!-- Comment -->
                                <button
                                    class="flex items-center space-x-1.5 text-gray-900 dark:text-white hover:opacity-70 group"
                                    onclick="toggleComments(<?= $feed['id'] ?>)">
                                    <span
                                        class="material-icons-round text-[28px] group-active:scale-90 transition-transform">chat_bubble_outline</span>
                                    <span
                                        class="font-bold text-base"><?= $feed['comment_count'] > 0 ? $feed['comment_count'] : '' ?></span>
                                </button>

                                <!-- Download (Parent Only) -->
                                <?php if ((hasRole('parent') || hasRole('superadmin')) && ($feed['content_type'] === 'image' || $feed['content_type'] === 'video')): ?>
                                    <a href="<?= BASE_URL ?>public/<?= h($feed['content']) ?>"
                                        download="<?= basename($feed['content']) ?>"
                                        class="flex items-center text-gray-900 dark:text-white hover:opacity-70 group">
                                        <span
                                            class="material-icons-round text-[28px] group-active:scale-90 transition-transform">file_download</span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Bookmark (Removed) -->
                        </div>



                        <!-- Caption -->
                        <div class="mb-2">
                            <span
                                class="font-bold text-sm text-gray-900 dark:text-white mr-1"><?= h($feed['user_name']) ?></span>
                            <?php if ($feed['caption']): ?>
                                <span class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                    <?= nl2br(h($feed['caption'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Date -->
                        <div class="mb-2">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wide">
                                <?= date('d F Y', strtotime($feed['created_at'])) ?>
                            </span>
                        </div>



                        <!-- Comment Input (Hidden by Default) -->
                        <div id="comment-form-container-<?= $feed['id'] ?>" class="hidden pb-2 mt-2">
                            <form action="<?= BASE_URL ?>public/index.php?page=feed/action/comment" method="POST"
                                class="flex items-center w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-black/20">
                                <input type="hidden" name="feed_id" value="<?= $feed['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                <input type="text" name="comment" required placeholder="Tambahkan komentar..."
                                    class="flex-1 bg-transparent border-none p-0 text-sm focus:ring-0 placeholder-gray-400 text-gray-900 dark:text-white">
                                <button type="submit"
                                    class="ml-2 focus:outline-none hover:opacity-80 transition-opacity p-1 flex items-center justify-center text-blue-500">
                                    <span
                                        class="material-icons-round text-2xl transform rotate-[-45deg] relative top-[-2px] left-[-2px]">send</span>
                                </button>
                            </form>
                        </div>

                    </div>

                    <!-- Comments Section (Hidden by Default/Expandable) -->
                    <div id="comments-<?= $feed['id'] ?>"
                        class="hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-surface-dark p-4">
                        <div class="comments-list-<?= $feed['id'] ?> space-y-2">
                            <div class="text-center text-xs text-gray-500 loading-comments">Memuat komentar...</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- Preview Modal -->
    <div id="media-preview-modal"
        class="fixed inset-0 z-[100] hidden bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300 opacity-0"
        onclick="closePreview()">
        <button
            class="absolute top-4 right-4 text-white/70 hover:text-white p-2 rounded-full bg-black/20 hover:bg-black/40 transition-colors z-[110]">
            <span class="material-icons-round text-3xl">close</span>
        </button>

        <div class="relative w-full max-w-4xl max-h-[90vh] flex items-center justify-center"
            onclick="event.stopPropagation()">
            <img id="preview-image" src="" alt="Preview"
                class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl hidden">
            <video id="preview-video" controls class="max-w-full max-h-[85vh] rounded-lg shadow-2xl hidden w-full">
                <source src="" type="video/mp4">
                Browser anda tidak mendukung tag video.
            </video>
        </div>
    </div>

    <!-- Edit Caption Modal -->
    <div id="edit-caption-modal"
        class="fixed inset-0 z-[100] hidden bg-black/50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl p-6 relative transform transition-all scale-95"
            id="edit-modal-content">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Edit Caption</h3>
            <form action="<?= BASE_URL ?>public/index.php?page=feed/action/edit" method="POST">
                <?= csrfInput() ?>
                <input type="hidden" name="feed_id" id="edit-feed-id">
                <textarea name="caption" id="edit-caption-text" rows="4"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-black/20 focus:ring-primary focus:border-primary text-sm mb-4"></textarea>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleFeedMenu(id) {
        const menu = document.getElementById(`feed-menu-${id}`);
        // Close all other menus
        document.querySelectorAll('[id^="feed-menu-"]').forEach(el => {
            if (el.id !== `feed-menu-${id}`) el.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('button[onclick^="toggleFeedMenu"]') && !e.target.closest('[id^="feed-menu-"]')) {
            document.querySelectorAll('[id^="feed-menu-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    function openEditModal(id, caption) {
        const modal = document.getElementById('edit-caption-modal');
        const content = document.getElementById('edit-modal-content');
        document.getElementById('edit-feed-id').value = id;
        document.getElementById('edit-caption-text').value = caption;

        // Hide menu
        document.getElementById(`feed-menu-${id}`).classList.add('hidden');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('edit-caption-modal');
        const content = document.getElementById('edit-modal-content');

        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function confirmDelete() {
        return confirm('Apakah anda yakin ingin menghapus postingan ini? Tindakan ini tidak dapat dibatalkan.');
    }

    function openPreview(type, src) {
        const modal = document.getElementById('media-preview-modal');
        const img = document.getElementById('preview-image');
        const vid = document.getElementById('preview-video');

        modal.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        setTimeout(() => {
            modal.classList.remove('opacity-0');
        }, 10);

        if (type === 'image') {
            img.src = src;
            img.classList.remove('hidden');
            vid.classList.add('hidden');
            vid.pause();
        } else {
            vid.querySelector('source').src = src;
            vid.load();
            vid.classList.remove('hidden');
            img.classList.add('hidden');
        }
    }

    function closePreview() {
        const modal = document.getElementById('media-preview-modal');
        const vid = document.getElementById('preview-video');

        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            vid.pause();
            vid.querySelector('source').src = '';
        }, 300);
    }

    function toggleLike(feedId, btn) {
        // Optimistic UI update
        const icon = btn.querySelector('.material-icons-round');
        const countSpan = btn.querySelector('.like-count-text');

        const isLiked = icon.innerText === 'favorite';

        // Parse current count
        let count = parseInt(countSpan.innerText) || 0;

        if (isLiked) {
            icon.innerText = 'favorite_border';
            btn.classList.remove('text-red-500');
            btn.classList.add('text-gray-900', 'dark:text-white');
            count--;
        } else {
            icon.innerText = 'favorite';
            btn.classList.remove('text-gray-900', 'dark:text-white');
            btn.classList.add('text-red-500');
            count++;
        }

        // Update text
        countSpan.innerText = count > 0 ? count : '';

        // API Call
        fetch('<?= BASE_URL ?>public/index.php?page=feed/action/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ feed_id: feedId, csrf_token: '<?= h($_SESSION['csrf_token']) ?>' })
        })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    // Revert on error
                    console.error('Like failed', data);
                }
            });
    }

    function toggleComments(feedId) {
        const container = document.getElementById(`comments-${feedId}`);
        const formContainer = document.getElementById(`comment-form-container-${feedId}`);
        
        container.classList.toggle('hidden');
        formContainer.classList.toggle('hidden');

        if (!container.classList.contains('hidden')) {
            loadComments(feedId);
        }
    }

    function loadComments(feedId) {
        const list = document.querySelector(`.comments-list-${feedId}`);
        list.innerHTML = '<div class="text-center text-xs text-gray-500 loading-comments">Memuat komentar...</div>';

        fetch(`<?= BASE_URL ?>public/index.php?page=feed/action/comment_list&feed_id=${feedId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    list.innerHTML = '<p class="text-center text-xs text-red-500">Gagal memuat komentar</p>';
                    return;
                }

                if (data.comments.length === 0) {
                    list.innerHTML = '<p class="text-center text-xs text-gray-500 italic">Belum ada komentar</p>';
                    return;
                }

                list.innerHTML = data.comments.map(c => `
                    <div class="mb-3">
                        <div class="font-bold text-gray-900 dark:text-white text-sm">${c.user_name}</div>
                        <div class="text-gray-800 dark:text-gray-200 text-sm leading-snug">${c.comment}</div>
                    </div>
                `).join('');
            })
            .catch(err => {
                list.innerHTML = '<p class="text-center text-xs text-red-500">Terjadi kesalahan koneksi</p>';
            });
    }
</script>