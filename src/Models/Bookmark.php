<?php
// src/Models/Bookmark.php
class Bookmark {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($user_id, $surah_number, $verse_number, $note = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO bookmarks (user_id, surah_number, verse_number, note)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE note = VALUES(note)
        ");
        return $stmt->execute([$user_id, $surah_number, $verse_number, $note]);
    }

    public function remove($user_id, $surah_number, $verse_number) {
        $stmt = $this->pdo->prepare("
            DELETE FROM bookmarks
            WHERE user_id = ? AND surah_number = ? AND verse_number = ?
        ");
        return $stmt->execute([$user_id, $surah_number, $verse_number]);
    }

    public function getByUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT b.id, b.user_id, b.surah_number, b.verse_number, b.note, b.created_at, 
                   v.text_ar, v.text_latin, v.text_id, s.surah_name_ar, s.surah_name_en
            FROM bookmarks b
            JOIN quran_verses v ON b.surah_number = v.surah_number AND b.verse_number = v.verse_number
            JOIN quran_structure s ON b.surah_number = s.surah_number
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function isBookmarked($user_id, $surah_number, $verse_number) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM bookmarks
            WHERE user_id = ? AND surah_number = ? AND verse_number = ?
        ");
        $stmt->execute([$user_id, $surah_number, $verse_number]);
        return $stmt->fetchColumn() > 0;
    }
}
