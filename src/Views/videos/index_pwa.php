<?php
// src/Views/videos/index_pwa.php
require_once '../src/Models/Video.php';
require_once '../src/Models/VideoCategory.php';

$videoModel = new Video($pdo);
$categoryModel = new VideoCategory($pdo);

$categories = $categoryModel->getAll();

// Filter Logic
$categoryId = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

if ($search) {
    $videos = $videoModel->search($search);
    $activeCategory = null;
} elseif ($categoryId) {
    $videos = $videoModel->getByCategory($categoryId);
    $activeCategory = $categoryId;
} else {
    $videos = $videoModel->getAll();
    $activeCategory = 'all';
}
?>

<div class="space-y-6" x-data="{ showSearch: <?= !empty($search) ? 'true' : 'false' ?> }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Video Edukasi</h2>
        <button @click="showSearch = !showSearch; if(showSearch) $nextTick(() => $refs.searchInput.focus())" class="text-gray-500 p-2 hover:bg-gray-100 rounded-full transition-colors">
            <span class="material-icons-round">search</span>
        </button>
    </div>

    <!-- Search Bar -->
    <form action="<?= BASE_URL ?>public/index.php" method="GET" 
          x-show="showSearch" 
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-y-0"
          x-transition:leave-end="opacity-0 -translate-y-2"
          class="relative">
        <input type="hidden" name="page" value="videos/index">
        <input type="hidden" name="mode" value="pwa">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <span class="material-icons-round text-gray-400">search</span>
        </span>
        <input x-ref="searchInput" type="text" name="search" placeholder="Cari video edukasi..." value="<?= h($search) ?>"
               class="w-full py-2.5 pl-10 pr-4 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm">
    </form>


    <!-- Categories Pills -->
    <div class="flex space-x-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
        <a href="<?= BASE_URL ?>public/index.php?page=videos/index&mode=pwa" 
           class="flex-shrink-0 inline-flex items-center px-4 py-2 rounded-full text-xs font-medium border transition-colors <?= $activeCategory === 'all' ? 'bg-primary border-primary text-white shadow-md' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
            <span class="material-icons-round text-sm mr-1">grid_view</span>
            Semua
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>public/index.php?page=videos/index&category=<?= $cat['id'] ?>&mode=pwa" 
               class="flex-shrink-0 inline-flex items-center px-4 py-2 rounded-full text-xs font-medium border transition-colors <?= $activeCategory == $cat['id'] ? 'bg-primary border-primary text-white shadow-md' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
                <span class="material-icons-round text-sm mr-1"><?= h($cat['icon']) ?></span>
                <?= h($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Hero/Featured Video (Show only on 'All' view and if videos exist) -->
    <?php if ($activeCategory === 'all' && !empty($videos) && empty($search)): ?>
    <?php $featured = $videos[0]; ?>
    <div class="relative rounded-2xl overflow-hidden shadow-card group">
        <img src="https://img.youtube.com/vi/<?= h($featured['youtube_id']) ?>/mqdefault.jpg" alt="<?= h($featured['title']) ?>" class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-4">
             <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-primary/90 text-white mb-2 self-start backdrop-blur-sm">
                REKOMENDASI
            </span>
            <h3 class="text-white font-bold text-lg line-clamp-2 leading-tight"><?= h($featured['title']) ?></h3>
            <p class="text-gray-200 text-xs mt-1 line-clamp-1"><?= h($featured['description']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>public/index.php?page=videos/watch&id=<?= $featured['id'] ?>&mode=pwa" class="absolute inset-0 flex items-center justify-center">
            <span class="material-icons-round text-white text-5xl opacity-80 group-active:scale-90 transition-transform drop-shadow-lg">play_circle</span>
        </a>
    </div>
    
    <h3 class="font-bold text-gray-900 text-lg pt-2">Video Terbaru</h3>
    <?php 
        // Remove first item for list if we showed it as hero
        $listVideos = array_slice($videos, 1);
    ?>
    <?php else: ?>
        <?php $listVideos = $videos; ?>
    <?php endif; ?>

    <!-- Video List -->
    <div class="grid grid-cols-2 gap-4">
        <?php foreach ($listVideos as $video): ?>
            <a href="<?= BASE_URL ?>public/index.php?page=videos/watch&id=<?= $video['id'] ?>&mode=pwa" class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden active:scale-95 transition-transform">
                <div class="relative aspect-w-16 aspect-h-9">
                    <img src="https://img.youtube.com/vi/<?= h($video['youtube_id']) ?>/mqdefault.jpg" alt="<?= h($video['title']) ?>" class="w-full h-full object-cover">
                    <!-- Duration Badge -->
                    <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded backdrop-blur-sm">
                        <?= h($video['duration']) ?>
                    </span>
                </div>
                <div class="p-3">
                    <h4 class="font-semibold text-gray-900 text-sm line-clamp-2 leading-snug mb-1 h-10">
                        <?= h($video['title']) ?>
                    </h4>
                    <div class="flex items-center text-[10px] text-gray-500">
                        <span class="material-icons-round text-[12px] mr-1">category</span>
                        <span class="truncate"><?= h($video['category_name']) ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($videos)): ?>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="bg-gray-100 p-4 rounded-full mb-3">
                <span class="material-icons-round text-gray-400 text-4xl">videocam_off</span>
            </div>
            <p class="text-gray-500 text-sm">Tidak ada video ditemukan.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
