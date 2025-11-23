<?php
// database/seed_verses.php
// Seed Quran Verses from Al-Quran Cloud API
// Run once: php database/seed_verses.php

require_once __DIR__ . '/../config/database.php';

// Prevent re-seeding
$stmt = $pdo->query("SELECT COUNT(*) FROM quran_verses");
if ($stmt->fetchColumn() > 0) {
    echo "Quran verses already seeded.\n";
    exit;
}

$baseUrl = 'https://api.alquran.cloud/v1/quran/quran-uthmani';
$translationUrl = 'https://api.alquran.cloud/v1/quran/id.indonesian';
$latinUrl = 'https://api.alquran.cloud/v1/quran/en.transliteration';

echo "Fetching Quran data from API...\n";

// Fetch Arabic text
$arabicData = json_decode(file_get_contents($baseUrl), true);
if (!$arabicData || !isset($arabicData['data']['surahs'])) {
    die("Failed to fetch Arabic text.\n");
}

// Fetch Indonesian translation
$idData = json_decode(file_get_contents($translationUrl), true);
if (!$idData || !isset($idData['data']['surahs'])) {
    die("Failed to fetch Indonesian translation.\n");
}

// Fetch Latin transliteration
$latinData = json_decode(file_get_contents($latinUrl), true);
if (!$latinData || !isset($latinData['data']['surahs'])) {
    die("Failed to fetch Latin transliteration.\n");
}

$arabicSurahs = $arabicData['data']['surahs'];
$idSurahs = $idData['data']['surahs'];
$latinSurahs = $latinData['data']['surahs'];

$stmt = $pdo->prepare("
    INSERT INTO quran_verses
    (surah_number, verse_number, text_ar, text_latin, text_id, audio_url)
    VALUES (?, ?, ?, ?, ?, ?)
");

$inserted = 0;
foreach ($arabicSurahs as $surahIndex => $surah) {
    $surahNumber = $surah['number'];
    $arabicVerses = $surah['ayahs'];
    $idVerses = $idSurahs[$surahIndex]['ayahs'];
    $latinVerses = $latinSurahs[$surahIndex]['ayahs'];

    foreach ($arabicVerses as $verseIndex => $verse) {
        $verseNumber = $verse['numberInSurah'];
        $textAr = $verse['text'];
        $textId = $idVerses[$verseIndex]['text'];
        $textLatin = $latinVerses[$verseIndex]['text'];

        // Audio URL from Qurancdn (Mishary Rashid)
        $audioUrl = "https://cdn.islamic.network/quran/audio/128/ar.alafasy/{$surahNumber}/{$verseNumber}.mp3";

        $stmt->execute([$surahNumber, $verseNumber, $textAr, $textLatin, $textId, $audioUrl]);
        $inserted++;
    }
}

echo "Successfully seeded $inserted verses into quran_verses.\n";
echo "Total verses: 6236\n";
