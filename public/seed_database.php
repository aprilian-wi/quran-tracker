<?php
// public/seed_database.php
// Web-accessible database seeder
// Access via: http://localhost/quran-tracker/public/seed_database.php

require_once '../config/database.php';
require_once '../src/Helpers/functions.php';

// Only allow superadmin to run this
requireLogin();
if (!hasRole('superadmin')) {
    die('Access denied: Superadmin only');
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seed'])) {
    // Prevent re-seeding
    $stmt = $pdo->query("SELECT COUNT(*) FROM quran_structure");
    if ($stmt->fetchColumn() > 0) {
        $message = "Quran data already seeded.";
    } else {
        $quran = [
            // Juz 1
            [1,1,'الفاتحة','Al-Fatihah',7,1,7],
            [1,2,'البقرة','Al-Baqarah',286,1,141],
            // Juz 2
            [2,2,'البقرة','Al-Baqarah',286,141,252],
            // Juz 3
            [3,2,'البقرة','Al-Baqarah',286,253,286],
            [3,3,'آل عمران','Aal-E-Imran',200,1,91],
            // Juz 4
            [4,3,'آل عمران','Aal-E-Imran',200,92,200],
            [4,4,'النساء','An-Nisa',176,1,23],
            // Juz 5
            [5,4,'النساء','An-Nisa',176,24,147],
            // Juz 6
            [6,4,'النساء','An-Nisa',176,148,176],
            [6,5,'المائدة','Al-Ma\'idah',120,1,82],
            // Juz 7
            [7,5,'المائدة','Al-Ma\'idah',120,83,120],
            [7,6,'الأنعام','Al-An\'am',165,1,110],
            // Juz 8
            [8,6,'الأنعام','Al-An\'am',165,111,165],
            [8,7,'الأعراف','Al-A\'raf',206,1,87],
            // Juz 9
            [9,7,'الأعراف','Al-A\'raf',206,88,206],
            [9,8,'الأنفال','Al-Anfal',75,1,40],
            // Juz 10
            [10,8,'الأنفال','Al-Anfal',75,41,75],
            [10,9,'التوبة','At-Tawbah',129,1,93],
            // Juz 11
            [11,9,'التوبة','At-Tawbah',129,94,129],
            [11,10,'يونس','Yunus',109,1,109],
            [11,11,'هود','Hud',123,1,5],
            // Juz 12
            [12,11,'هود','Hud',123,6,123],
            [12,12,'يوسف','Yusuf',111,1,52],
            // Juz 13
            [13,12,'يوسف','Yusuf',111,53,111],
            [13,13,'الرعد','Ar-Ra\'d',43,1,43],
            [13,14,'ابراهيم','Ibrahim',52,1,52],
            // Juz 14
            [14,15,'الحجر','Al-Hijr',99,1,99],
            [14,16,'النحل','An-Nahl',128,1,128],
            // Juz 15
            [15,17,'الإسراء','Al-Isra',111,1,111],
            [15,18,'الكهف','Al-Kahf',110,1,74],
            // Juz 16
            [16,18,'الكهف','Al-Kahf',110,75,110],
            [16,19,'مريم','Maryam',98,1,98],
            [16,20,'طه','Taha',135,1,135],
            // Juz 17
            [17,21,'الأنبياء','Al-Anbiya',112,1,112],
            [17,22,'الحج','Al-Hajj',78,1,78],
            // Juz 18
            [18,23,'المؤمنون','Al-Mu\'minun',118,1,118],
            [18,24,'النور','An-Nur',64,1,64],
            [18,25,'الفرقان','Al-Furqan',77,1,20],
            // Juz 19
            [19,25,'الفرقان','Al-Furqan',77,21,77],
            [19,26,'الشعراء','Ash-Shu\'ara',227,1,227],
            [19,27,'النمل','An-Naml',93,1,55],
            // Juz 20
            [20,27,'النمل','An-Naml',93,56,93],
            [20,28,'القصص','Al-Qasas',88,1,88],
            [20,29,'العنكبوت','Al-Ankabut',69,1,45],
            // Juz 21
            [21,29,'العنكبوت','Al-Ankabut',69,46,69],
            [21,30,'الروم','Ar-Rum',60,1,60],
            [21,31,'لقمان','Luqman',34,1,34],
            [21,32,'السجدة','As-Sajdah',30,1,30],
            [21,33,'الأحزاب','Al-Ahzab',73,1,30],
            // Juz 22
            [22,33,'الأحزاب','Al-Ahzab',73,31,73],
            [22,34,'سبإ','Saba',54,1,54],
            [22,35,'فاطر','Fatir',45,1,45],
    [22,36,'يس','Ya-Sin',83,1,27],
            // Juz 23
            [23,36,'يس','Ya-Sin',83,28,83],
            [23,37,'الصافات','As-Saffat',182,1,182],
            [23,38,'ص','Sad',88,1,88],
            [23,39,'الزمر','Az-Zumar',75,1,31],
            // Juz 24
            [24,39,'الزمر','Az-Zumar',75,32,75],
            [24,40,'غافر','Ghafir',85,1,85],
            [24,41,'فصلت','Fussilat',54,1,46],
            // Juz 25
            [25,41,'فصلت','Fussilat',54,47,54],
            [25,42,'الشورى','Ash-Shura',53,1,53],
            [25,43,'الزخرف','Az-Zukhruf',89,1,89],
            [25,44,'الدخان','Ad-Dukhan',59,1,59],
            [25,45,'الجاثية','Al-Jathiyah',37,1,32],
            // Juz 26
            [26,45,'الجاثية','Al-Jathiyah',37,33,37],
            [26,46,'الأحقاف','Al-Ahqaf',35,1,35],
            [26,47,'محمد','Muhammad',38,1,38],
            [26,48,'الفتح','Al-Fath',29,1,29],
            [26,49,'الحجرات','Al-Hujurat',18,1,18],
            [26,50,'ق','Qaf',45,1,45],
            [26,51,'الذاريات','Adh-Dhariyat',60,1,30],
            // Juz 27
            [27,51,'الذاريات','Adh-Dhariyat',60,31,60],
            [27,52,'الطور','At-Tur',49,1,49],
            [27,53,'النجم','An-Najm',62,1,62],
            [27,54,'القمر','Al-Qamar',55,1,55],
            [27,55,'الرحمن','Ar-Rahman',78,1,78],
            [27,56,'الواقعة','Al-Waqi\'ah',96,1,96],
            [27,57,'الحديد','Al-Hadid',29,1,29],
            // Juz 28
            [28,58,'المجادلة','Al-Mujadilah',22,1,22],
            [28,59,'الحشر','Al-Hashr',24,1,24],
            [28,60,'الممتحنة','Al-Mumtahanah',13,1,13],
            [28,61,'الصف','As-Saff',14,1,14],
            [28,62,'الجمعة','Al-Jumu\'ah',11,1,11],
            [28,63,'المنافقون','Al-Munafiqun',11,1,11],
            [28,64,'التغابن','At-Taghabun',18,1,18],
            [28,65,'الطلاق','At-Talaq',12,1,12],
            [28,66,'التحريم','At-Tahrim',12,1,12],
            // Juz 29
            [29,67,'الملك','Al-Mulk',30,1,30],
            [29,68,'القلم','Al-Qalam',52,1,52],
            [29,69,'الحاقة','Al-Haqqah',52,1,52],
            [29,70,'المعارج','Al-Ma\'arij',44,1,44],
            [29,71,'نوح','Nuh',28,1,28],
            [29,72,'الجن','Al-Jinn',28,1,28],
            [29,73,'المزمل','Al-Muzzammil',20,1,20],
            [29,74,'المدثر','Al-Muddathir',56,1,56],
            [29,75,'القيامة','Al-Qiyamah',40,1,40],
            [29,76,'الإنسان','Al-Insan',31,1,31],
            [29,77,'المرسلات','Al-Mursalat',50,1,50],
            // Juz 30
            [30,78,'النبإ','An-Naba',40,1,40],
            [30,79,'النازعات','An-Nazi\'at',46,1,46],
            [30,80,'عبس','Abasa',42,1,42],
            [30,81,'التكوير','At-Takwir',29,1,29],
            [30,82,'الإنفطار','Al-Infitar',19,1,19],
            [30,83,'المطففين','Al-Mutaffifin',36,1,36],
            [30,84,'الإنشقاق','Al-Inshiqaq',25,1,25],
            [30,85,'البروج','Al-Buruj',22,1,22],
            [30,86,'الطارق','At-Tariq',17,1,17],
            [30,87,'الأعلى','Al-A\'la',19,1,19],
            [30,88,'الغاشية','Al-Ghashiyah',26,1,26],
            [30,89,'الفجر','Al-Fajr',30,1,30],
            [30,90,'البلد','Al-Balad',20,1,20],
            [30,91,'الشمس','Ash-Shams',15,1,15],
            [30,92,'الليل','Al-Layl',21,1,21],
            [30,93,'الضحى','Ad-Duha',11,1,11],
            [30,94,'الشرح','Ash-Sharh',8,1,8],
            [30,95,'التين','At-Tin',8,1,8],
            [30,96,'العلق','Al-\'Alaq',19,1,19],
            [30,97,'القدر','Al-Qadr',5,1,5],
            [30,98,'البينة','Al-Bayyinah',8,1,8],
            [30,99,'الزلزلة','Az-Zalzalah',8,1,8],
            [30,100,'العاديات','Al-\'Adiyat',11,1,11],
            [30,101,'القارعة','Al-Qari\'ah',11,1,11],
            [30,102,'التكاثر','At-Takathur',8,1,8],
            [30,103,'العصر','Al-\'Asr',3,1,3],
            [30,104,'الهمزة','Al-Humazah',9,1,9],
            [30,105,'الفيل','Al-Fil',5,1,5],
            [30,106,'قريش','Quraish',4,1,4],
            [30,107,'الماعون','Al-Ma\'un',7,1,7],
            [30,108,'الكوثر','Al-Kawthar',3,1,3],
            [30,109,'الكافرون','Al-Kafirun',6,1,6],
            [30,110,'النصر','An-Nasr',3,1,3],
            [30,111,'المسد','Al-Masad',5,1,5],
            [30,112,'الإخلاص','Al-Ikhlas',4,1,4],
            [30,113,'الفلق','Al-Falaq',5,1,5],
            [30,114,'الناس','An-Nas',6,1,6]
        ];

        $stmt = $pdo->prepare("
            INSERT INTO quran_structure
            (juz, surah_number, surah_name_ar, surah_name_en, full_verses, start_verse, end_verse)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $inserted = 0;
        foreach ($quran as $s) {
            if ($stmt->execute($s)) $inserted++;
        }

        $message = "Successfully seeded $inserted surahs into quran_structure.\nTotal verses in Quran: 6236";
        $success = true;
    }
}

include __DIR__ . '/../src/Views/layouts/main.php';
?>

<h3><i class="bi bi-database"></i> Database Seeder</h3>

<div class="card">
    <div class="card-body">
        <p>This tool will seed the database with Quran structure data including juz-specific verse ranges.</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $success ? 'success' : 'warning' ?> alert-dismissible fade show" role="alert">
                <pre class="mb-0"><?= h($message) ?></pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfInput() ?>
            <button type="submit" name="seed" value="1" class="btn btn-primary">
                <i class="bi bi-play-fill"></i> Run Database Seeder
            </button>
            <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </form>
    </div>
</div>
