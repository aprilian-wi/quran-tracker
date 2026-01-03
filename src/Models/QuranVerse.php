<?php
// src/Models/QuranVerse.php
class QuranVerse {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getVersesBySurah($surah_number) {
        $stmt = $this->pdo->prepare("
            SELECT verse_number, text_ar, text_latin, text_id, audio_url
            FROM quran_verses
            WHERE surah_number = ?
            ORDER BY verse_number
        ");
        $stmt->execute([$surah_number]);
        return $stmt->fetchAll();
    }

    public function getVerse($surah_number, $verse_number) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quran_verses
            WHERE surah_number = ? AND verse_number = ?
        ");
        $stmt->execute([$surah_number, $verse_number]);
        return $stmt->fetch();
    }

    public function getVersesBySurahPaginated($surah_number, $limit, $offset) {
        $stmt = $this->pdo->prepare("
            SELECT verse_number, text_ar, text_latin, text_id, audio_url
            FROM quran_verses
            WHERE surah_number = ?
            ORDER BY verse_number
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $surah_number, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchVerses($query) {
        $stmt = $this->pdo->prepare("
                 SELECT v.surah_number, v.verse_number, v.text_ar, v.text_latin, v.text_id,
                     s.surah_name_ar, s.surah_name_en, s.juz, s.full_verses
            FROM quran_verses v
            JOIN quran_structure s ON v.surah_number = s.surah_number
            WHERE v.text_ar LIKE ? OR v.text_latin LIKE ? OR v.text_id LIKE ?
                  OR s.surah_name_ar LIKE ? OR s.surah_name_en LIKE ?
            ORDER BY v.surah_number, v.verse_number
            LIMIT 100
        ");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
