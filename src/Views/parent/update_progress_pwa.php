<?php
// src/Views/parent/update_progress_pwa.php
// Assumes $child, $child_id, $juzList are already defined by the including file

?>

<section class="pb-20">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-6 px-1">
        <a href="<?= BASE_URL ?>public/index.php?page=parent/my_children&mode=pwa" class="text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400 p-1 -ml-1 rounded-full active:bg-gray-100 dark:active:bg-gray-800 transition-colors">
            <span class="material-icons-round text-2xl">arrow_back</span>
        </a>
        <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Update Tahfidz</h2>
    </div>

    <!-- Child Info Card -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-5 border border-gray-100 dark:border-gray-800 mb-6 flex items-center space-x-4">
        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border-2 border-primary/20">
            <?php if (!empty($child['photo'])): ?>
                <img src="<?= BASE_URL ?>public/uploads/children_photos/<?= htmlspecialchars($child['photo']) ?>" alt="Child" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <span class="material-icons-round text-2xl">person</span>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h3 class="font-bold text-lg text-gray-900 dark:text-white leading-tight"><?= h($child['name']) ?></h3>
            <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-0.5">Hafalan Al-Quran</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress" class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 space-y-5 mb-8">
        <?= csrfInput() ?>
        <input type="hidden" name="child_id" value="<?= $child_id ?>">
        <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">
        <!-- Maintain pwa mode on redirect/submission if possible, though action is standard. 
             Ideally the controller redirects back to pwa mode if referrer was pwa. 
             For now we rely on standard action. 
        -->
        
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Juz</label>
            <select name="juz" id="juz" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12" required>
                <option value="">Pilih Juz</option>
                <?php foreach ($juzList as $juz): ?>
                    <option value="<?= $juz ?>">Juz <?= $juz ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Surah</label>
            <select name="surah" id="surah" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12" required disabled>
                <option value="">Pilih Surah</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ayat</label>
                <input type="number" name="verse" id="verse" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12" min="1" required disabled>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary h-12" required>
                    <option value="memorized" selected>Menghafal</option>
                    <option value="in_progress">Murajaah</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Optional)</label>
            <textarea name="note" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary p-3" rows="2" placeholder="Tulis catatan tambahan..."></textarea>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-green-900/20 active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-icons-round text-xl">save</span>
            <span>Simpan Progres</span>
        </button>
    </form>

    <!-- History Header -->
    <div class="flex items-center justify-between mb-4 px-1">
        <h3 class="text-lg font-display font-bold text-text-main-light dark:text-white">Riwayat</h3>
        <select id="updatedByFilter" class="bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs rounded-lg px-2 py-1.5 focus:ring-primary focus:border-primary text-gray-600 dark:text-gray-300">
            <option value="">Semua Pengupdate</option>
            <!-- Options populated via PHP -->
            <?php 
            $progressModel = new Progress($pdo);
            $history = $progressModel->getHistory($child_id);
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
    </div>

    <!-- History List -->
    <div class="space-y-4" id="historyList">
        <?php
        // $history is already fetched above
        if ($history):
            foreach ($history as $entry):
                $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                              ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                $statusColor = $entry['status'] === 'memorized' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400' : 'text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400';
                $icon = $entry['status'] === 'memorized' ? 'check_circle' : 'cached';
        ?>
            <div class="history-item bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 p-4 relative overflow-hidden" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                 <!-- Status Stripe -->
                <div class="absolute left-0 top-0 bottom-0 w-1 <?= $entry['status'] === 'memorized' ? 'bg-emerald-500' : 'bg-amber-500' ?>"></div>
                
                <div class="flex justify-between items-start mb-2 pl-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-gray-900 dark:text-white"><?= h($entry['surah_name_ar']) ?> (<?= h($entry['surah_name_en']) ?>)</h4>
                        </div>
                        <p class="text-sm text-text-sub-light dark:text-text-sub-dark mt-0.5">Ayat <?= $entry['verse'] ?></p>
                    </div>
                    <span class="<?= $statusColor ?> text-xs px-2 py-1 rounded-md font-medium flex items-center gap-1">
                         <span class="material-icons-round text-xs"><?= $icon ?></span>
                        <?= $statusText ?>
                    </span>
                </div>

                <?php if (!empty($entry['note'])): ?>
                    <div class="ml-2 mt-2 mb-3 bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg text-sm text-gray-600 dark:text-gray-400 italic flex gap-2">
                         <span class="material-icons-round text-xs text-gray-400 mt-0.5">sticky_note_2</span>
                        <?= h($entry['note']) ?>
                    </div>
                <?php endif; ?>

                <div class="ml-2 flex justify-between items-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-50 dark:border-gray-800 pt-2 mt-2">
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
        <?php 
            endforeach;
        else:
        ?>
            <div class="text-center py-10 text-gray-400">
                <span class="material-icons-round text-4xl mb-2 opacity-30">history</span>
                <p>Belum ada riwayat.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Filter History Logic
document.getElementById('updatedByFilter').addEventListener('change', function() {
    const selected = this.value;
    const items = document.querySelectorAll('.history-item');
    let hasVisible = false;

    items.forEach(item => {
        if (!selected || item.getAttribute('data-updated-by') === selected) {
            item.style.display = 'block';
            hasVisible = true;
        } else {
            item.style.display = 'none';
        }
    });

    // Handle "No history" message visibility if we wanted to get fancy, 
    // but the PHP "Belum ada riwayat" only shows if generic history is empty.
    // We could add a "No results found" for filter here if needed.
});

// Dynamic loading for Juz → Surah → Verse (Same logic as original, adapted for ID matching)
document.getElementById('juz').addEventListener('change', function() {
    const juz = this.value;
    const surahSelect = document.getElementById('surah');
    const verseInput = document.getElementById('verse');

    surahSelect.innerHTML = '<option>Loading...</option>';
    surahSelect.disabled = true;
    verseInput.disabled = true;

    if (!juz) return;

    fetch(`?page=get_surahs&juz=${juz}&_=${Date.now()}`)
        .then(r => r.json())
        .then(data => {
            surahSelect.innerHTML = '<option value="">Pilih Surah</option>';
            data.forEach(s => {
                surahSelect.add(new Option(`${s.surah_number}. ${s.surah_name_ar} (${s.surah_name_en})`, s.surah_number));
            });
            surahSelect.disabled = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            surahSelect.innerHTML = '<option value="">Error loading</option>';
        });
});

document.getElementById('surah').addEventListener('change', function() {
    const surah = this.value;
    const verseInput = document.getElementById('verse');
    verseInput.disabled = true;

    if (!surah) return;

    fetch(`?page=get_verse_count&surah=${surah}&_=${Date.now()}`)
        .then(r => r.json())
        .then(data => {
            verseInput.max = data.full_verses;
            verseInput.placeholder = `1–${data.full_verses}`;
            verseInput.disabled = false;
        });
});
</script>
