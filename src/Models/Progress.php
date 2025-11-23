<?php
// src/Models/Progress.php
class Progress {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function update($child_id, $juz, $surah, $verse, $status, $updated_by, $note = null) {
        $sql = "INSERT INTO progress_status
                (child_id, juz, surah_number, verse, status, updated_by, note)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$child_id, $juz, $surah, $verse, $status, $updated_by, $note]);
    }

    public function getLatest($child_id) {
        $stmt = $this->pdo->prepare("
            SELECT juz, surah_number, verse, status, updated_at,
                   u.name as updated_by_name
            FROM progress_status p
            JOIN users u ON p.updated_by = u.id
            WHERE p.child_id = ?
            ORDER BY p.updated_at DESC
            LIMIT 1
        ");
        $stmt->execute([$child_id]);
        return $stmt->fetch();
    }

    public function getHistory($child_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.name as updated_by_name,
                   qs.surah_name_ar, qs.surah_name_en
            FROM progress_status p
            JOIN users u ON p.updated_by = u.id
            LEFT JOIN quran_structure qs ON p.juz = qs.juz AND p.surah_number = qs.surah_number
            WHERE p.child_id = ?
            ORDER BY p.updated_at DESC
        ");
        $stmt->execute([$child_id]);
        return $stmt->fetchAll();
    }

    public function getProgressSummary($child_id) {
        $stats = [
            'in_progress' => 0,
            'memorized' => 0,
            'total_verses' => 0
        ];

        // Get counts for each status, but only the latest status per verse
        $stmt = $this->pdo->prepare("
            SELECT status, COUNT(*) as count
            FROM (
                SELECT child_id, juz, surah_number, verse, status
                FROM progress_status
                WHERE child_id = ?
                AND (child_id, juz, surah_number, verse, updated_at) IN (
                    SELECT child_id, juz, surah_number, verse, MAX(updated_at)
                    FROM progress_status
                    WHERE child_id = ?
                    GROUP BY child_id, juz, surah_number, verse
                )
            ) latest_statuses
            GROUP BY status
        ");
        $stmt->execute([$child_id, $child_id]);
        foreach ($stmt->fetchAll() as $row) {
            $stats[$row['status']] = (int)$row['count'];
        }

        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT CONCAT(juz, '-', surah_number, '-', verse)) as total
            FROM progress_status
            WHERE child_id = ? AND status = 'memorized'
        ");
        $stmt->execute([$child_id]);
        $row = $stmt->fetch();
        $stats['total_verses'] = $row['total'] ?? 0;

        $stats['percentage'] = 6236 > 0 ? round(($stats['total_verses'] / 6236) * 100, 2) : 0;

        return $stats;
    }

    // Methods for Teaching Books Progress
    public function updateBookProgress($child_id, $book_id, $page, $status, $updated_by, $note = null) {
        $sql = "INSERT INTO progress_books
                (child_id, book_id, page, status, updated_by, note)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$child_id, $book_id, $page, $status, $updated_by, $note]);
    }

    public function getBookLatest($child_id) {
        $stmt = $this->pdo->prepare("
            SELECT pb.page, pb.status, pb.updated_at,
                   u.name as updated_by_name,
                   tb.volume_number, tb.title
            FROM progress_books pb
            JOIN users u ON pb.updated_by = u.id
            JOIN teaching_books tb ON pb.book_id = tb.id
            WHERE pb.child_id = ?
            ORDER BY pb.updated_at DESC
            LIMIT 1
        ");
        $stmt->execute([$child_id]);
        return $stmt->fetch();
    }

    public function getBookHistory($child_id) {
        $stmt = $this->pdo->prepare("
            SELECT pb.*, u.name as updated_by_name,
                   tb.volume_number, tb.title
            FROM progress_books pb
            JOIN users u ON pb.updated_by = u.id
            JOIN teaching_books tb ON pb.book_id = tb.id
            WHERE pb.child_id = ?
            ORDER BY pb.updated_at DESC
        ");
        $stmt->execute([$child_id]);
        return $stmt->fetchAll();
    }

    public function getBookProgressSummary($child_id) {
        $stats = [
            'in_progress' => 0,
            'memorized' => 0,
            'fluent' => 0,
            'repeating' => 0,
            'total_pages' => 0
        ];

        // Get counts for each status, but only the latest status per page per book
        $stmt = $this->pdo->prepare("
            SELECT status, COUNT(*) as count
            FROM (
                SELECT child_id, book_id, page, status
                FROM progress_books
                WHERE child_id = ?
                AND (child_id, book_id, page, updated_at) IN (
                    SELECT child_id, book_id, page, MAX(updated_at)
                    FROM progress_books
                    WHERE child_id = ?
                    GROUP BY child_id, book_id, page
                )
            ) latest_statuses
            GROUP BY status
        ");
        $stmt->execute([$child_id, $child_id]);
        foreach ($stmt->fetchAll() as $row) {
            $stats[$row['status']] = (int)$row['count'];
        }

        // Get total unique pages with 'memorized', 'fluent', or 'repeating' status
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT CONCAT(book_id, '-', page)) as total
            FROM progress_books
            WHERE child_id = ? AND status IN ('memorized', 'fluent', 'repeating')
        ");
        $stmt->execute([$child_id]);
        $row = $stmt->fetch();
        $stats['total_pages'] = $row['total'] ?? 0;

        return $stats;
    }
}