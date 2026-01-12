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
        <div class="space-y-6 pb-20">
            <?php foreach ($feeds as $feed): ?>
                <div class="bg-surface-light dark:bg-surface-dark rounded-3xl shadow-soft overflow-hidden border border-gray-100 dark:border-gray-800 feed-item"
                    data-id="<?= $feed['id'] ?>">

                    <!-- Header -->
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">

                            <div>
                                <h3 class="font-bold text-sm text-text-main-light dark:text-text-main-dark">
                                    <?= h($feed['user_name']) ?>
                                </h3>
                                <div class="flex items-center text-xs text-text-sub-light dark:text-text-sub-dark space-x-1">
                                    <span>
                                        <?= date('d M Y • H:i', strtotime($feed['created_at'])) ?>
                                    </span>
                                    <span>•</span>
                                    <span class="capitalize">
                                        <?= h($feed['user_role']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Content -->
                    <?php if ($feed['content_type'] === 'image' && $feed['content']): ?>
                        <div class="w-full relative bg-black/5 aspect-[4/3] group cursor-pointer overflow-hidden"
                            onclick="openPreview('image', '<?= BASE_URL ?>public/<?= h($feed['content']) ?>')">
                            <img src="<?= BASE_URL ?>public/<?= h($feed['content']) ?>" alt="Feed Image"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy">
                            <div
                                class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <span class="material-icons-round text-white/80 text-4xl drop-shadow-lg">fullscreen</span>
                            </div>
                        </div>
                    <?php elseif ($feed['content_type'] === 'video' && $feed['content']): ?>
                        <div class="w-full relative bg-black aspect-[4/3] group cursor-pointer"
                            onclick="openPreview('video', '<?= BASE_URL ?>public/<?= h($feed['content']) ?>')">
                            <video class="w-full h-full object-contain pointer-events-none">
                                <source src="<?= BASE_URL ?>public/<?= h($feed['content']) ?>"
                                    type="video/<?= pathinfo($feed['content'], PATHINFO_EXTENSION) ?>">
                            </video>
                            <div
                                class="absolute inset-0 bg-black/20 flex items-center justify-center group-hover:bg-black/30 transition-colors">
                                <div
                                    class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                                    <span class="material-icons-round text-white text-2xl">play_arrow</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Bar -->
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-5">
                            <!-- Like -->
                            <button
                                class="flex items-center space-x-1.5 group like-btn <?= $feed['is_liked'] ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' ?>"
                                onclick="toggleLike(<?= $feed['id'] ?>, this)">
                                <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">
                                    <?= $feed['is_liked'] ? 'favorite' : 'favorite_border' ?>
                                </span>
                                <span class="text-sm font-medium like-count">
                                    <?= $feed['like_count'] > 0 ? $feed['like_count'] : '' ?>
                                </span>
                            </button>

                            <!-- Comment Toggle -->
                            <button
                                class="flex items-center space-x-1.5 text-gray-500 dark:text-gray-400 hover:text-primary group"
                                onclick="toggleComments(<?= $feed['id'] ?>)">
                                <span
                                    class="material-icons-round text-2xl group-active:scale-90 transition-transform">chat_bubble_outline</span>
                                <span class="text-sm font-medium">
                                    <?= $feed['comment_count'] > 0 ? $feed['comment_count'] : '' ?>
                                </span>
                            </button>

                            <!-- Download (Parent Only) -->
                            <?php if ((hasRole('parent') || hasRole('superadmin')) && ($feed['content_type'] === 'image' || $feed['content_type'] === 'video')): ?>
                                <a href="<?= BASE_URL ?>public/<?= h($feed['content']) ?>"
                                    download="<?= basename($feed['content']) ?>"
                                    class="flex items-center space-x-1.5 text-gray-500 dark:text-gray-400 hover:text-primary group">
                                    <span
                                        class="material-icons-round text-2xl group-active:scale-90 transition-transform">download</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Bookmark/Share (Optional) -->
                        <div>
                            <!-- Placeholder for future -->
                        </div>
                    </div>

                    <!-- Caption -->
                    <?php if ($feed['caption']): ?>
                        <div class="px-5 pb-2">
                            <p class="text-sm text-text-main-light dark:text-text-main-dark leading-relaxed">

                                <?= nl2br(h($feed['caption'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>


                <!-- Comments Section (Hidden by Default) -->
                                    <div id="comments-<?= $feed['id'] ?>"
                                        class="hidden border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-black/20">
                                        <!-- Comments will be loaded here via JS or just simple loop if eager loaded -->
                                        <!-- For specific requirement "View comments", let's simplify by using Detail page OR expanding inline. 
                              Since action is simple, let's expand inline. -->
                                        <div class="p-4 space-y-3">
                                            <div class="comments-list-<?= $feed['id'] ?> space-y-2 text-sm">
                                                <!-- Loaded via AJAX or PHP? Model supports getComments but we didn't eager load them.
                                     For now, let's fetch on expand or just render if we change query. 
                                     The requirement is "View interactions". Let's use a "View all comments" link to a dedicated page or load via AJAX.
                                     
                                     For MVP/Prototype: Simple AJAX load.
                                -->
                                                <div class="text-center text-xs text-gray-500 loading-comments">Memuat komentar...</div>
                                            </div>

                                            <!-- Comment Form -->
                                            <form action="<?= BASE_URL ?>public/index.php?page=feed/action/comment" method="POST"
                                                class="flex items-center space-x-2">
                                                <input type="hidden" name="feed_id" value="<?= $feed['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                <input type="text" name="comment" required placeholder="Tulis komentar..."
                                                    class="flex-1 bg-white dark:bg-surface-dark border-none rounded-full py-2 px-4 text-sm focus:ring-1 focus:ring-primary shadow-sm placeholder-gray-400">
                                                <button type="submit"
                                                    class="text-primary disabled:opacity-50 font-medium text-sm">Kirim</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                    <?php endforeach; ?>
                </div>
    <?php endif; ?>
    <!-- Preview Modal -->
    <div id="media-preview-modal" class="fixed inset-0 z-[100] hidden bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closePreview()">
        <button class="absolute top-4 right-4 text-white/70 hover:text-white p-2 rounded-full bg-black/20 hover:bg-black/40 transition-colors z-[110]">
            <span class="material-icons-round text-3xl">close</span>
        </button>
        
        <div class="relative w-full max-w-4xl max-h-[90vh] flex items-center justify-center" onclick="event.stopPropagation()">
            <img id="preview-image" src="" alt="Preview" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl hidden">
            <video id="preview-video" controls class="max-w-full max-h-[85vh] rounded-lg shadow-2xl hidden w-full">
                <source src="" type="video/mp4">
                Browser anda tidak mendukung tag video.
            </video>
        </div>
    </div>
</div>

<script>
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
        const countSpan = btn.querySelector('.like-count');
        const isLiked = icon.innerText === 'favorite';
        let count = parseInt(countSpan.innerText) || 0;

        if (isLiked) {
            icon.innerText = 'favorite_border';
            btn.classList.remove('text-red-500');
            btn.classList.add('text-gray-500', 'dark:text-gray-400');
            count--;
        } else {
            icon.innerText = 'favorite';
            btn.classList.remove('text-gray-500', 'dark:text-gray-400');
            btn.classList.add('text-red-500');
            count++;
        }
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
            }); // TODO: Handle revert if failed
    }

    function toggleComments(feedId) {
        const container = document.getElementById(`comments-${feedId}`);
        container.classList.toggle('hidden');

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
                    <div class="flex space-x-2.5">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                            ${c.user_name.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl rounded-tl-none shadow-sm text-sm flex-1 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">${c.user_name}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full ${c.user_role === 'teacher' ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500'} capitalize">
                                        ${c.user_role}
                                    </span>
                                </div>
                                <span class="text-[10px] text-gray-400">${c.created_at}</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">${c.comment}</p>
                        </div>
                    </div>
                `).join('');
            })
            .catch(err => {
                list.innerHTML = '<p class="text-center text-xs text-red-500">Terjadi kesalahan koneksi</p>';
            });
    }
</script>