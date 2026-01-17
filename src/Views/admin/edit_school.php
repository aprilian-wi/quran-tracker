<?php
// src/Views/admin/edit_school.php
$pageTitle = 'Edit School';
include __DIR__ . '/../layouts/admin.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';

$id = $_GET['id'] ?? 0;
$controller = new SystemAdminController($pdo);
$school = $controller->getSchool($id);
$admins = $controller->getSchoolAdmins($id);

if (!$school) {
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800' role='alert'>School not found.</div>";
    exit;
}
?>

<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'details' }">
    <!-- Alpine.js Region Data Logic -->
    <script>
        function addressData() {
            return {
                provinsis: [],
                kabupatens: [],
                kecamatans: [],
                selectedProvinsi: '<?= h($school['provinsi'] ?? '') ?>',
                selectedKabupaten: '<?= h($school['kabupaten'] ?? '') ?>',
                selectedKecamatan: '<?= h($school['kecamatan'] ?? '') ?>',
                
                async init() {
                    await this.fetchProvinsis();
                    if (this.selectedProvinsi) {
                        await this.fetchKabupatens(this.selectedProvinsi);
                    }
                    if (this.selectedKabupaten) {
                        await this.fetchKecamatans(this.selectedKabupaten);
                    }
                },

                fetchProvinsis() {
                    return fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                        .then(response => response.json())
                        .then(data => {
                            this.provinsis = data;
                        })
                        .catch(err => console.error('Error fetching provinces:', err));
                },

                fetchKabupatens(provName) {
                    // Find ID based on name (since we store name)
                    const prov = this.provinsis.find(p => p.name === provName);
                    if (!prov) return Promise.resolve();

                    return fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${prov.id}.json`)
                        .then(response => response.json())
                        .then(data => {
                            this.kabupatens = data;
                        })
                        .catch(err => console.error('Error fetching regencies:', err));
                },

                fetchKecamatans(kabName) {
                    const kab = this.kabupatens.find(k => k.name === kabName);
                    if (!kab) return Promise.resolve();

                    return fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kab.id}.json`)
                        .then(response => response.json())
                        .then(data => {
                            this.kecamatans = data;
                        })
                        .catch(err => console.error('Error fetching districts:', err));
                },

                onProvinsiChange(e) {
                    this.selectedProvinsi = e.target.value;
                    this.selectedKabupaten = '';
                    this.selectedKecamatan = '';
                    this.kabupatens = [];
                    this.kecamatans = [];
                    this.fetchKabupatens(this.selectedProvinsi);
                },

                onKabupatenChange(e) {
                    this.selectedKabupaten = e.target.value;
                    this.selectedKecamatan = '';
                    this.kecamatans = [];
                    this.fetchKecamatans(this.selectedKabupaten);
                }
            }
        }
    </script>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
                <span class="material-icons-round text-2xl">domain</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Sekolah</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400"><?= h($school['name']) ?></p>
            </div>
        </div>
        
        <a href="index.php?page=admin/schools" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
            <span class="material-icons-round text-lg mr-2">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Tabs Header -->
        <div class="border-b border-slate-200 dark:border-slate-700">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button @click="activeTab = 'details'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'details', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'details' }"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">info</span>
                    Detail Sekolah
                </button>
                <button @click="activeTab = 'microsite'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'microsite', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'microsite' }"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">web</span>
                    Microsite
                </button>
                <button @click="activeTab = 'media'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'media', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'media' }"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">perm_media</span>
                    Media
                </button>
                <button @click="activeTab = 'admins'" 
                        :class="{ 'border-teal-500 text-teal-600 dark:text-teal-400': activeTab === 'admins', 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300': activeTab !== 'admins' }"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm flex items-center justify-center gap-2">
                    <span class="material-icons-round text-base">admin_panel_settings</span>
                    Admin Sekolah
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Details Tab -->
            <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="index.php?page=admin/update_school" method="POST" class="space-y-6" x-data="addressData()" x-init="init()">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= $school['id'] ?>">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?= h($school['name']) ?>"
                               class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat Jalan</label>
                        <textarea name="address" rows="2"
                                  class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"><?= h($school['address']) ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                            <select name="provinsi" x-model="selectedProvinsi" @change="onProvinsiChange" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                <option value="">Pilih Provinsi</option>
                                <template x-for="prov in provinsis" :key="prov.id">
                                    <option :value="prov.name" x-text="prov.name" :selected="prov.name === selectedProvinsi"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kabupaten/Kota</label>
                            <select name="kabupaten" x-model="selectedKabupaten" @change="onKabupatenChange" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                <option value="">Pilih Kabupaten/Kota</option>
                                <template x-for="kab in kabupatens" :key="kab.id">
                                    <option :value="kab.name" x-text="kab.name" :selected="kab.name === selectedKabupaten"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                            <select name="kecamatan" x-model="selectedKecamatan" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                <option value="">Pilih Kecamatan</option>
                                <template x-for="kec in kecamatans" :key="kec.id">
                                    <option :value="kec.name" x-text="kec.name" :selected="kec.name === selectedKecamatan"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kelurahan/Desa</label>
                            <input type="text" name="kelurahan" value="<?= h($school['kelurahan'] ?? '') ?>"
                                   class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">RT / RW</label>
                            <input type="text" name="rt_rw" value="<?= h($school['rt_rw'] ?? '') ?>" placeholder="001/002"
                                   class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Pos</label>
                            <input type="text" name="kode_pos" value="<?= h($school['kode_pos'] ?? '') ?>"
                                   class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 gap-2">
                            <span class="material-icons-round text-lg leading-none">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Microsite Tab -->
            <div x-show="activeTab === 'microsite'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="index.php?page=admin/update_school" method="POST" class="space-y-6">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= $school['id'] ?>">

                    <div class="alert bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100">
                        <h4 class="font-bold mb-2">Panduan Microsite</h4>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            <li><strong>Slug URL:</strong> Alamat unik untuk sekolah ini. Contoh: <code>sekolah-islam-amanah</code>. Akan diakses melalui <code><?= BASE_URL ?>[slug]</code>.</li>
                            <li><strong>HTML Code:</strong> Paste kode HTML landing page Anda di sini.</li>
                            <li><strong>Catatan:</strong> Halaman ini bersifat PUBLIK (dapat diakses siapa saja).</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Slug URL (Tanpa Spasi)</label>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 sm:text-sm">
                                <?= BASE_URL ?>
                            </span>
                            <input type="text" name="slug" value="<?= h($school['slug'] ?? '') ?>" placeholder="nama-sekolah-anda"
                                pattern="[a-z0-9-]+" title="Hanya huruf kecil, angka, dan tanda hubung (-)"
                                class="flex-1 block w-full rounded-none rounded-r-md border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white sm:text-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">HTML Code</label>
                        <textarea name="microsite_html" rows="15"
                                  class="block w-full font-mono text-xs rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                  placeholder="<!DOCTYPE html>..."><?= h($school['microsite_html'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <?php if (!empty($school['slug'])): ?>
                            <a href="<?= BASE_URL . h($school['slug']) ?>" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="material-icons-round text-lg mr-2">visibility</span>
                                Lihat Microsite
                            </a>
                        <?php endif; ?>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                            <span class="material-icons-round text-lg mr-2">save</span>
                            Simpan Microsite
                        </button>
                    </div>
                </form>
            </div>

            <!-- Media Tab -->
            <div x-show="activeTab === 'media'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-6">
                    <?php
                    // Calculate Storage Usage
                    $uploadDir = __DIR__ . '/../../../public/uploads/schools/' . $school['id'] . '/';
                    $webPath = BASE_URL . 'public/uploads/schools/' . $school['id'] . '/';
                    $totalSize = 0;
                    $fileCount = 0;
                    
                    if (file_exists($uploadDir)) {
                        foreach (new DirectoryIterator($uploadDir) as $fileInfo) {
                            if ($fileInfo->isFile()) {
                                $totalSize += $fileInfo->getSize();
                                $fileCount++;
                            }
                        }
                    }

                    $maxSize = 256 * 1024 * 1024; // 256MB
                    $usagePercent = min(100, ($totalSize / $maxSize) * 100);
                    $totalSizeMB = number_format($totalSize / 1024 / 1024, 2);
                    
                    // Color state
                    $barColor = 'bg-teal-600 dark:bg-teal-500';
                    if ($usagePercent > 90) $barColor = 'bg-red-600 dark:bg-red-500';
                    elseif ($usagePercent > 70) $barColor = 'bg-amber-500 dark:bg-amber-400';
                    ?>

                    <!-- Storage Indicator -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">Penyimpanan Media</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Total <?= $fileCount ?> file
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-slate-900 dark:text-white"><?= $totalSizeMB ?> MB</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400"> / 256 MB</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                            <div class="<?= $barColor ?> h-2.5 rounded-full transition-all duration-500" style="width: <?= $usagePercent ?>%"></div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700/50">
                        <h4 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-icons-round text-slate-400">cloud_upload</span>
                            Upload Media Baru
                        </h4>
                        <form action="index.php?page=admin/upload_school_media" method="POST" enctype="multipart/form-data" class="flex gap-4 items-end">
                            <?= csrfInput() ?>
                            <input type="hidden" name="id" value="<?= $school['id'] ?>">
                            
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-400 mb-1">Pilih File (Bisa lebih dari satu)</label>
                                <input type="file" name="media[]" multiple required class="block w-full text-sm text-slate-500 dark:text-slate-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-teal-50 file:text-teal-700
                                  hover:file:bg-teal-100 dark:file:bg-teal-900/30 dark:file:text-teal-400
                                "/>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                Upload
                            </button>
                        </form>
                    </div>

                    <!-- File List -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php
                        if (file_exists($uploadDir)) {
                            $files = array_diff(scandir($uploadDir), array('.', '..'));
                            
                            if (empty($files)) {
                                echo '<div class="col-span-full text-center py-8 text-slate-500 italic">Belum ada media yang di-upload.</div>';
                            }

                            foreach ($files as $file) {
                                $fileUrl = $webPath . $file;
                        ?>
                                <div class="relative group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="aspect-video bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                                        <img src="<?= $fileUrl ?>" alt="<?= h($file) ?>" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-3 border-t border-slate-100 dark:border-slate-700">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mb-3" title="<?= h($file) ?>"><?= h($file) ?></p>
                                        <div class="flex gap-2">
                                            <button type="button" 
                                                onclick="navigator.clipboard.writeText('<?= $fileUrl ?>'); alert('Link berhasil disalin!')" 
                                                class="flex-1 inline-flex items-center justify-center px-2 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded hover:bg-slate-200 dark:text-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600">
                                                <span class="material-icons-round text-sm mr-1">link</span>
                                                Salin Link
                                            </button>
                                            
                                            <form action="index.php?page=admin/delete_school_media" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                                <?= csrfInput() ?>
                                                <input type="hidden" name="id" value="<?= $school['id'] ?>">
                                                <input type="hidden" name="filename" value="<?= h($file) ?>">
                                                <button type="submit" class="p-1.5 text-red-600 bg-red-50 rounded hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50" title="Hapus">
                                                    <span class="material-icons-round text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                             echo '<div class="col-span-full text-center py-8 text-slate-500 italic">Belum ada media yang di-upload.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Admins Tab -->
            <div x-show="activeTab === 'admins'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-4 bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 rounded-lg p-4 flex items-start gap-3">
                    <span class="material-icons-round text-sky-600 dark:text-sky-400 mt-0.5">info</span>
                    <p class="text-sm text-sky-700 dark:text-sky-300">
                        Ini adalah daftar administrator yang memiliki akses penuh untuk mengelola data sekolah ini.
                    </p>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. HP</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bergabung</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-card-dark">
                            <?php if (empty($admins)): ?>
                                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada admin ditemukan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($admins as $admin): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                            <?= h($admin['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= h($admin['phone']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <?= date('d M Y', strtotime($admin['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="index.php?page=admin/edit_school_admin&id=<?= $admin['id'] ?>" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors" title="Edit Admin">
                                                    <span class="material-icons-round text-lg">edit_note</span>
                                                </a>
                                                <a href="tel:<?= h($admin['phone']) ?>" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Hubungi">
                                                    <span class="material-icons-round text-lg">phone</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
