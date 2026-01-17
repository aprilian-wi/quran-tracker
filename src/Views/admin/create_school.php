<?php
// src/Views/admin/create_school.php
$pageTitle = 'Create New School';
include __DIR__ . '/../layouts/admin.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div
                class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
                <span class="material-icons-round text-2xl">domain_add</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Sekolah Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Buat akun sekolah (Tenant) baru</p>
            </div>
        </div>

        <a href="index.php?page=admin/schools"
            class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
            <span class="material-icons-round text-lg mr-2">arrow_back</span>
            Kembali
        </a>
    </div>

    <div
        class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form action="index.php?page=admin/store_school" method="POST">
            <?= csrfInput() ?>

            <div class="p-6 space-y-8">
                <!-- School Details -->
                <div x-data="{
                    provinsis: [],
                    kabupatens: [],
                    kecamatans: [],
                    selectedProvinsi: '',
                    selectedKabupaten: '',
                    selectedKecamatan: '',
                    
                    init() {
                        this.fetchProvinsis();
                    },

                    fetchProvinsis() {
                        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                            .then(response => response.json())
                            .then(data => {
                                this.provinsis = data;
                            })
                            .catch(err => console.error('Error fetching provinces:', err));
                    },

                    fetchKabupatens(provName) {
                        const prov = this.provinsis.find(p => p.name === provName);
                        if (!prov) return;

                        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${prov.id}.json`)
                            .then(response => response.json())
                            .then(data => {
                                this.kabupatens = data;
                            })
                            .catch(err => console.error('Error fetching regencies:', err));
                    },

                    fetchKecamatans(kabName) {
                        const kab = this.kabupatens.find(k => k.name === kabName);
                        if (!kab) return;

                        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kab.id}.json`)
                            .then(response => response.json())
                            .then(data => {
                                this.kecamatans = data;
                            })
                            .catch(err => console.error('Error fetching districts:', err));
                    }
                }">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-icons-round text-teal-500">school</span>
                        Detail Sekolah
                    </h3>
                    <div class="grid gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                Sekolah <span class="text-red-500">*</span></label>
                            <input type="text" name="school_name" required placeholder="Contoh: SD Islam Al-Azhar"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat
                                Jalan</label>
                            <textarea name="address" rows="2" placeholder="Jl. Raya No. 123"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:borderteal-500 focus:ring-teal-500 sm:text-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Provinsi -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                                <select name="provinsi" x-model="selectedProvinsi"
                                    @change="fetchKabupatens(selectedProvinsi); selectedKabupaten=''; selectedKecamatan=''"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <option value="">Pilih Provinsi</option>
                                    <template x-for="prov in provinsis" :key="prov.id">
                                        <option :value="prov.name" x-text="prov.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Kabupaten/Kota -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kabupaten/Kota</label>
                                <select name="kabupaten" x-model="selectedKabupaten"
                                    @change="fetchKecamatans(selectedKabupaten); selectedKecamatan=''"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    :disabled="!selectedProvinsi">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    <template x-for="kab in kabupatens" :key="kab.id">
                                        <option :value="kab.name" x-text="kab.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                                <select name="kecamatan" x-model="selectedKecamatan"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    :disabled="!selectedKabupaten">
                                    <option value="">Pilih Kecamatan</option>
                                    <template x-for="kec in kecamatans" :key="kec.id">
                                        <option :value="kec.name" x-text="kec.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Kelurahan -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kelurahan/Desa</label>
                                <input type="text" name="kelurahan" placeholder="Nama Kelurahan/Desa"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            </div>

                            <!-- RT/RW -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">RT /
                                    RW</label>
                                <input type="text" name="rt_rw" placeholder="001/002"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            </div>

                            <!-- Kode Pos -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode
                                    Pos</label>
                                <input type="text" name="kode_pos" placeholder="12345"
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700"></div>

                <!-- Admin Details -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-icons-round text-teal-500">admin_panel_settings</span>
                        Administrator Sekolah
                    </h3>
                    <div class="grid gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                                Admin <span class="text-red-500">*</span></label>
                            <input type="text" name="admin_name" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">No. HP
                                Admin <span class="text-red-500">*</span></label>
                            <input type="tel" name="admin_phone" required placeholder="Contoh: 0812345678"
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password
                                Awal <span class="text-red-500">*</span></label>
                            <input type="password" name="admin_password" required
                                class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                <a href="index.php?page=admin/schools"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 gap-2">
                    <span class="material-icons-round text-lg leading-none">add_circle</span>
                    Buat Sekolah
                </button>
            </div>
        </form>
    </div>
</div>