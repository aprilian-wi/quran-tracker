<?php
// src/Views/teacher/update_progress_hadiths_pwa.php
// Assumes $child, $child_id, $hadiths are already defined
?>

<section class="pb-20">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-6 px-1">
        <?php if (!empty($class_id)): ?>
            <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class_id ?>&mode=pwa"
                class="text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400 p-1 -ml-1 rounded-full active:bg-gray-100 dark:active:bg-gray-800 transition-colors">
                <span class="material-icons-round text-2xl">arrow_back</span>
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>public/index.php?page=dashboard&mode=pwa"
                class="text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400 p-1 -ml-1 rounded-full active:bg-gray-100 dark:active:bg-gray-800 transition-colors">
                <span class="material-icons-round text-2xl">arrow_back</span>
            </a>
        <?php endif; ?>
        <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Update Hadits</h2>
    </div>

    <!-- Child Info Card -->
    <div
        class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-5 border border-gray-100 dark:border-gray-800 mb-6 flex items-center space-x-4">
        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border-2 border-primary/20">
            <?php if (!empty($child['photo'])): ?>
                <img src="<?= BASE_URL ?>public/uploads/children_photos/<?= htmlspecialchars($child['photo']) ?>"
                    alt="Child" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <span class="material-icons-round text-2xl">person</span>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h3 class="font-bold text-lg text-gray-900 dark:text-white leading-tight"><?= h($child['name']) ?></h3>
            <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-0.5">Hafalan Hadits</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress_hadiths"
        class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 space-y-5 mb-8">
        <?= csrfInput() ?>
        <input type="hidden" name="child_id" value="<?= $child_id ?>">
        <input type="hidden" name="class_id" value="<?= $class_id ?? '' ?>">
        <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

        <!-- Redirect back to PWA view after save -->
        <input type="hidden" name="redirect_to" value="teacher/update_progress_hadiths">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Hadits</label>
            <select name="hadith_id" id="hadith_id"
                class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12"
                required>
                <option value="">Pilih Hadits</option>
                <?php foreach ($hadiths as $hadith): ?>
                    <option value="<?= $hadith['id'] ?>"><?= h($hadith['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select name="status" id="status"
                class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12"
                required>
                <option value="memorized" selected>Menghafal</option>
                <option value="in_progress">Murajaah</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Optional)</label>
            <textarea name="note" id="note"
                class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary p-3"
                rows="2" placeholder="Tulis catatan tambahan..."></textarea>
        </div>

        <button type="submit"
            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-rose-900/10 active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-icons-round text-xl">save</span>
            <span>Simpan Progres Hadits</span>
        </button>
    </form>

    <!-- History Header -->
    <div class="flex flex-col space-y-3 mb-4 px-1">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-display font-bold text-text-main-light dark:text-white">Riwayat</h3>
            <button type="button" id="resetFilters"
                class="text-xs text-primary dark:text-green-400 font-medium hidden">Reset Filter</button>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <!-- Updater Filter -->
            <select id="updatedByFilter"
                class="bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs rounded-lg px-2 py-2 focus:ring-primary focus:border-primary text-gray-600 dark:text-gray-300 w-full">
                <option value="">Semua Pengupdate</option>
                <?php
                $progressModel = new Progress($pdo);
                $history = $progressModel->getHadithHistory($child_id);
                if ($history):
                    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
                    sort($uniqueUpdatedBy);
                    foreach ($uniqueUpdatedBy as $name):
                        ?>
                        <option value="<?= h($name) ?>"><?= h($name) ?></option>
                        <?php
                    endforeach;
                endif;
                ?>
            </select>

            <!-- Date Filter -->
            <div class="relative w-full">
                <input type="text" id="dateFilter"
                    class="bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs rounded-lg pl-8 pr-2 py-2 focus:ring-primary focus:border-primary text-gray-600 dark:text-gray-300 w-full placeholder-gray-400"
                    placeholder="Pilih Tanggal" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                    <span class="material-icons-round text-gray-400 text-sm">event</span>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4" id="historyList">
        <?php
        if ($history):
            foreach ($history as $entry):
                $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                    ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                $statusColor = $entry['status'] === 'memorized' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400' : 'text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400';
                $icon = $entry['status'] === 'memorized' ? 'check_circle' : 'cached';
                $entryDate = date('Y-m-d', strtotime($entry['updated_at']));
                ?>
                <div class="history-item relative overflow-hidden rounded-xl shadow-sm border border-gray-100 dark:border-gray-800"
                    data-updated-by="<?= h($entry['updated_by_name']) ?>" data-date="<?= $entryDate ?>">

                    <!-- Swipe Action (Delete) -->
                    <?php if ($_SESSION['role'] === 'teacher'): ?>
                        <div
                            class="swipe-action absolute inset-y-0 right-0 w-full bg-red-500 flex items-center justify-end px-6 z-0">
                            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_progress_hadiths_action">
                                <?= csrfInput() ?>
                                <input type="hidden" name="progress_id" value="<?= $entry['id'] ?>">
                                <input type="hidden" name="child_id" value="<?= $child_id ?>">
                                <input type="hidden" name="class_id" value="<?= $class_id ?? '' ?>">
                                <input type="hidden" name="mode" value="pwa">
                                <button type="button" onclick="confirmDelete(this)" class="text-white flex flex-col items-center">
                                    <span class="material-icons-round text-2xl">delete</span>
                                    <span class="text-xs font-bold">Hapus</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Swipe Content -->
                    <div
                        class="swipe-content relative z-10 bg-surface-light dark:bg-surface-dark p-4 transition-transform duration-200 ease-out">
                        <!-- Status Stripe -->
                        <div
                            class="absolute left-0 top-0 bottom-0 w-1 <?= $entry['status'] === 'memorized' ? 'bg-emerald-500' : 'bg-amber-500' ?>">
                        </div>

                        <div class="flex justify-between items-center mb-2 pl-2">
                            <h4 class="font-bold text-gray-900 dark:text-white"><?= h($entry['title']) ?></h4>
                            <span class="<?= $statusColor ?> text-xs px-2 py-1 rounded-md font-medium flex items-center gap-1">
                                <span class="material-icons-round text-xs"><?= $icon ?></span>
                                <?= $statusText ?>
                            </span>
                        </div>

                        <?php if (!empty($entry['note'])): ?>
                            <div
                                class="ml-2 mt-2 mb-3 bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-400 italic flex gap-2">
                                <span class="material-icons-round text-xs text-gray-400 mt-0.5">sticky_note_2</span>
                                <?= h($entry['note']) ?>
                            </div>
                        <?php endif; ?>

                        <div
                            class="ml-2 flex justify-between items-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-50 dark:border-gray-800 pt-2 mt-2">
                            <span class="flex items-center gap-1">
                                <span class="material-icons-round text-xs">event</span>
                                <?= date('d M Y, H:i', strtotime($entry['updated_at'])) ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="material-icons-round text-xs">person</span>
                                <?= h($entry['updated_by_name']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
        else:
            ?>
            <div class="text-center py-10 text-gray-400">
                <span class="material-icons-round text-4xl mb-2 opacity-30">history</span>
                <p>Belum ada riwayat Hadits.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination Controls -->
    <div id="paginationContainer" class="flex items-center justify-between mt-4 hidden px-2">
        <button id="prevBtn"
            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-30 disabled:hover:bg-gray-100 dark:disabled:hover:bg-gray-800 transition-colors">
            <span class="material-icons-round text-gray-600 dark:text-gray-300">chevron_left</span>
        </button>
        <span id="pageInfo" class="text-sm font-medium text-gray-600 dark:text-gray-300"></span>
        <button id="nextBtn"
            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-30 disabled:hover:bg-gray-100 dark:disabled:hover:bg-gray-800 transition-colors">
            <span class="material-icons-round text-gray-600 dark:text-gray-300">chevron_right</span>
        </button>
    </div>
</section>

<script>
    // Filter & Pagination Logic
    const filterSelect = document.getElementById('updatedByFilter');
    const dateFilter = document.getElementById('dateFilter');
    const resetBtn = document.getElementById('resetFilters');
    const paginationContainer = document.getElementById('paginationContainer');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageInfo = document.getElementById('pageInfo');

    const itemsPerPage = 5;
    let currentPage = 1;

    function renderList() {
        const selectedUpdater = filterSelect.value;
        const selectedDate = dateFilter.value;
        const items = Array.from(document.querySelectorAll('.history-item'));

        // Filter items
        const filteredItems = items.filter(item => {
            const matchUpdater = !selectedUpdater || item.getAttribute('data-updated-by') === selectedUpdater;
            const matchDate = !selectedDate || item.getAttribute('data-date') === selectedDate;
            return matchUpdater && matchDate;
        });

        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        // Reset/Validation
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        // Show reset button if any filter is active
        if (selectedUpdater || selectedDate) {
            resetBtn.classList.remove('hidden');
        } else {
            resetBtn.classList.add('hidden');
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        // Show/Hide
        items.forEach(item => item.style.display = 'none');
        filteredItems.forEach((item, index) => {
            if (index >= startIndex && index < endIndex) {
                item.style.display = 'block';
            }
        });

        // Pagination UI
        if (filteredItems.length > itemsPerPage) {
            paginationContainer.classList.remove('hidden');
            pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        } else {
            paginationContainer.classList.add('hidden');
        }
    }

    function resetFilters() {
        filterSelect.value = '';
        dateFilter.value = '';
        currentPage = 1;
        renderList();
    }

    filterSelect.addEventListener('change', () => { currentPage = 1; renderList(); });
    dateFilter.addEventListener('change', () => { currentPage = 1; renderList(); });
    resetBtn.addEventListener('click', resetFilters);

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderList();
        }
    });

    nextBtn.addEventListener('click', () => {
        currentPage++; // Validate in render
        renderList();
    });

    // Initial Render
    document.addEventListener('DOMContentLoaded', renderList);
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const historyItems = document.querySelectorAll('.history-item');

        historyItems.forEach(item => {
            const content = item.querySelector('.swipe-content');
            if (!content) return;

            let startX = 0;
            let currentX = 0;
            let isSwiping = false;
            const threshold = 80; // Distance to snap open
            const maxSwipe = 100; // Max swipe distance

            content.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                content.style.transition = 'none';
            }, { passive: true });

            content.addEventListener('touchmove', (e) => {
                const touchX = e.touches[0].clientX;
                const diff = touchX - startX;

                if (diff < 0) {
                    isSwiping = true;
                    currentX = Math.max(diff, -maxSwipe);
                    content.style.transform = `translateX(${currentX}px)`;
                } else if (diff > 0 && item.dataset.open === 'true') {
                    isSwiping = true;
                    currentX = Math.min(diff - 80, 0);
                    content.style.transform = `translateX(${currentX}px)`;
                }
            }, { passive: true });

            content.addEventListener('touchend', (e) => {
                content.style.transition = 'transform 0.2s ease-out';
                isSwiping = false;

                if (currentX < -threshold / 2) {
                    content.style.transform = `translateX(-${threshold}px)`;
                    item.dataset.open = 'true';

                    // Close others
                    historyItems.forEach(other => {
                        if (other !== item) {
                            const otherContent = other.querySelector('.swipe-content');
                            if (otherContent) otherContent.style.transform = 'translateX(0)';
                            other.dataset.open = 'false';
                        }
                    });
                } else {
                    content.style.transform = 'translateX(0)';
                    item.dataset.open = 'false';
                }

                currentX = 0;
            });
        });
    });

    function confirmDelete(btn) {
        if (confirm('Apakah Anda yakin ingin menghapus riwayat ini? Data tidak dapat dikembalikan.')) {
            btn.closest('form').submit();
        }
    }
</script>