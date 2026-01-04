<?php
// src/Models/Quran.php
class Quran {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }



    public function getVerseCount($surah_number) {
        $stmt = $this->pdo->prepare("SELECT full_verses FROM quran_structure WHERE surah_number = ?");
        $stmt->execute([$surah_number]);
        $row = $stmt->fetch();
        return $row['full_verses'] ?? 0;
    }

    public function getAllJuz() {
        $stmt = $this->pdo->query("SELECT DISTINCT juz FROM quran_structure ORDER BY juz");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getSurahInfo($surah_number) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quran_structure
            WHERE surah_number = ?
        ");
        $stmt->execute([$surah_number]);
        return $stmt->fetch();
    }

    public function getAllSurahs() {
        $stmt = $this->pdo->query("
            SELECT surah_number, surah_name_ar, surah_name_en, MIN(juz) as juz, full_verses
            FROM quran_structure
            GROUP BY surah_number, surah_name_ar, surah_name_en, full_verses
            ORDER BY surah_number
        ");
        return $stmt->fetchAll();
    }

    public function getSurahsByJuz($juz = null) {
        if ($juz === null) {
            return $this->getAllSurahs();
        }
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT surah_number, surah_name_ar, surah_name_en, juz, full_verses
            FROM quran_structure
            WHERE juz = ?
            ORDER BY surah_number
        ");
        $stmt->execute([$juz]);
        return $stmt->fetchAll();
    }

    public function getSurah($surah_number) {
        $stmt = $this->pdo->prepare("
            SELECT surah_number, surah_name_ar, surah_name_en, juz, full_verses
            FROM quran_structure
            WHERE surah_number = ?
        ");
        $stmt->execute([$surah_number]);
        return $stmt->fetch();
    }

    public function searchSurahs($query) {
        $query = "%$query%";
        $stmt = $this->pdo->prepare("
            SELECT surah_number, surah_name_ar, surah_name_en, MIN(juz) as juz, full_verses
            FROM quran_structure
            WHERE surah_name_ar LIKE ? OR surah_name_en LIKE ? OR surah_number LIKE ?
            GROUP BY surah_number, surah_name_ar, surah_name_en, full_verses
            ORDER BY surah_number
        ");
        $stmt->execute([$query, $query, $query]);
        return $stmt->fetchAll();
    }
}