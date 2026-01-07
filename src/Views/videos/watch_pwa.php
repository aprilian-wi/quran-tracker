<?php
// src/Views/videos/watch_pwa.php
require_once '../src/Models/Video.php';
$id = $_GET['id'] ?? 0;
$videoModel = new Video($pdo);
$video = $videoModel->find($id);

if (!$video) {
    setFlash('danger', 'Video tidak ditemukan.');
    redirect('videos/index', ['mode' => 'pwa']);
}

// Increment view count
// View count increment removed

// Get suggested videos
$suggested = $videoModel->getSuggested($id);
?>

<div class="-mx-4 sm:mx-0 -mt-6">
    <!-- Player Wrapper for Sticky Effect or full width -->
    <div id="video-container" class="sticky top-0 z-40 bg-black w-full aspect-video shadow-lg">
        <iframe src="https://www.youtube.com/embed/<?= h($video['youtube_id']) ?>?autoplay=1&rel=0&modestbranding=1&iv_load_policy=3&playsinline=1&controls=1" 
                title="<?= h($video['title']) ?>" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                class="w-full h-full">
        </iframe>
        
        <!-- Custom Fullscreen Button (Overlay) -->
        <button onclick="toggleFullscreen()" class="absolute bottom-4 right-4 bg-black/30 hover:bg-black/50 text-white w-10 h-10 rounded-full backdrop-blur-sm transition-all active:scale-90 flex items-center justify-center z-50 shadow-lg">
            <span id="fullscreen-icon" class="material-icons-round text-2xl">fullscreen</span>
        </button>
    </div>
    
    <div class="px-4 py-4 bg-white dark:bg-surface-dark mb-2">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2"><?= h($video['title']) ?></h1>
        
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
             <div class="flex items-center space-x-3">

                <span class="flex items-center">
                    <span class="material-icons-round text-sm mr-1">schedule</span>
                    <?= h($video['created_at']) // You might want to format this date friendly ?>
                </span>
            </div>
            <button class="text-gray-400 hover:text-red-500 transition-colors">
                <span class="material-icons-round text-xl">favorite_border</span>
            </button>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
            <p><?= nl2br(h($video['description'])) ?></p>
             <div class="mt-3 flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                    <?= h($video['category_name']) ?>
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700">
                    <?= h($video['duration']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Suggested Videos -->
    <div class="px-4 pb-4">
        <div class="flex items-center justify-between mb-3">
             <h3 class="font-bold text-gray-900 dark:text-white">Video Selanjutnya</h3>
             <span class="text-xs text-gray-400">Autoplay on</span>
        </div>
       
        <div class="space-y-3">
            <?php foreach ($suggested as $item): ?>
                <a href="<?= BASE_URL ?>public/index.php?page=videos/watch&id=<?= $item['id'] ?>&mode=pwa" class="flex bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden active:bg-gray-50 dark:active:bg-gray-900 transition-colors">
                    <div class="w-32 flex-shrink-0 relative">
                        <img src="https://img.youtube.com/vi/<?= h($item['youtube_id']) ?>/mqdefault.jpg" alt="<?= h($item['title']) ?>" class="w-full h-full object-cover">
                         <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1 py-0.5 rounded">
                            <?= h($item['duration']) ?>
                        </span>
                    </div>
                    <div class="p-3 flex flex-col justify-center flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm line-clamp-2 leading-snug mb-1">
                            <?= h($item['title']) ?>
                        </h4>
                        <div class="flex items-center text-[10px] text-gray-500 dark:text-gray-400">
                            <span class="truncate"><?= h($item['category_name']) ?></span>
                            <span class="mx-1">•</span>
                            <span><?= h($item['duration']) ?></span>
                        </div>
                         <button class="absolute top-2 right-2 text-gray-400">
                            <span class="material-icons-round text-lg">more_vert</span>
                        </button>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
async function toggleFullscreen() {
    const elem = document.getElementById('video-container');
    const icon = document.getElementById('fullscreen-icon');
    
    if (!document.fullscreenElement) {
        // ENTER Fullscreen & Landscape
        try {
            if (elem.requestFullscreen) {
                await elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { /* Safari */
                await elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE11 */
                await elem.msRequestFullscreen();
            }
            
            // Lock Landscape
            setTimeout(async () => {
                if (screen.orientation && screen.orientation.lock) {
                    try {
                        await screen.orientation.lock('landscape');
                    } catch (e) {
                        console.log('Lock failed:', e);
                    }
                }
            }, 100);

            icon.textContent = 'fullscreen_exit';
            
        } catch (err) {
            console.error("Error entering fullscreen:", err);
        }
    } else {
        // EXIT Fullscreen & Portrait
        try {
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                await document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                await document.msExitFullscreen();
            }
            
            // Unlock / Lock Portrait
            if (screen.orientation && screen.orientation.unlock) {
                try {
                    screen.orientation.unlock();
                } catch(e) {}
            }
            // Optional: Force portrait if unlock isn't enough
            if (screen.orientation && screen.orientation.lock) {
                 try { await screen.orientation.lock('portrait'); } catch(e){}
            }

            icon.textContent = 'fullscreen';
            
        } catch (err) {
            console.error("Error exiting fullscreen:", err);
        }
    }
}

// Sync Icon on System Exit (e.g. Back Button)
document.addEventListener('fullscreenchange', () => {
    const icon = document.getElementById('fullscreen-icon');
    if (!document.fullscreenElement) {
        icon.textContent = 'fullscreen';
        // Ensure orientation is reset
         if (screen.orientation && screen.orientation.unlock) {
            screen.orientation.unlock();
        }
    } else {
        icon.textContent = 'fullscreen_exit';
    }
});
</script>
