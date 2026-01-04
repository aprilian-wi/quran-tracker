<?php
// src/Models/Notification.php
class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new notification
    public function create($child_id, $type, $progress_id) {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (child_id, type, progress_id, viewed) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$child_id, $type, $progress_id]);
    }

    // Get unread notifications for a specific child
    public function getUnreadByChild($child_id) {
        return $this->getNotifications([$child_id], true);
    }

    // Get notifications for a list of children (defaults to Only Unread based on recent user request for PWA?)
    // Actually, let's keep default flexible, but allow passing it.
    public function getByChildren(array $child_ids, $limit = 20, $onlyUnread = false) {
        if (empty($child_ids)) return [];
        return $this->getNotifications($child_ids, $onlyUnread, $limit);
    }

    // Get unread count for a list of children (for Badge)
    public function getUnreadCount(array $child_ids) {
        if (empty($child_ids)) return 0;
        
        $placeholders = str_repeat('?,', count($child_ids) - 1) . '?';
        $sql = "SELECT COUNT(*) FROM notifications WHERE child_id IN ($placeholders) AND viewed = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($child_ids);
        return $stmt->fetchColumn();
    }

    // Mark as read
    public function markAsRead($id) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET viewed = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Mark all as read for children
    public function markAllAsRead(array $child_ids) {
        if (empty($child_ids)) return false;
        $placeholders = str_repeat('?,', count($child_ids) - 1) . '?';
        $sql = "UPDATE notifications SET viewed = 1 WHERE child_id IN ($placeholders) AND viewed = 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($child_ids);
    }

    // Private helper to fetch notifications with details
    private function getNotifications(array $child_ids, $onlyUnread = false, $limit = 50) {
        if (empty($child_ids)) return [];
        
        $placeholders = str_repeat('?,', count($child_ids) - 1) . '?';
        $viewedClause = $onlyUnread ? "AND n.viewed = 0" : "";
        
        $sql = "
            SELECT n.*, c.name as child_name, u.name as updated_by_name,
                   n.created_at,
                   CASE
                       WHEN n.type = 'tahfidz' THEN CONCAT('Juz ', p.juz, ', Surah ', p.surah_number, ':', p.verse, ' - ', p.status)
                       WHEN n.type = 'tahsin' THEN CONCAT('Page ', pb.page, ' - ', pb.status, ' (', tb.title, ')')
                       WHEN n.type = 'doa' THEN CONCAT(sp.title, ' - ', ps.status)
                       WHEN n.type = 'hadith' THEN CONCAT(h.title, ' - ', ph.status)
                   END as message,
                   CASE
                       WHEN n.type = 'tahfidz' THEN 'menu_book'
                       WHEN n.type = 'tahsin' THEN 'auto_stories'
                       WHEN n.type = 'doa' THEN 'volunteer_activism'
                       WHEN n.type = 'hadith' THEN 'format_quote'
                   END as icon
            FROM notifications n
            JOIN children c ON n.child_id = c.id
            LEFT JOIN progress_status p ON n.type = 'tahfidz' AND n.progress_id = p.id
            LEFT JOIN progress_books pb ON n.type = 'tahsin' AND n.progress_id = pb.id
            LEFT JOIN teaching_books tb ON n.type = 'tahsin' AND pb.book_id = tb.id
            LEFT JOIN progress_short_prayers ps ON n.type = 'doa' AND n.progress_id = ps.id
            LEFT JOIN short_prayers sp ON ps.prayer_id = sp.id
            LEFT JOIN progress_hadiths ph ON n.type = 'hadith' AND n.progress_id = ph.id
            LEFT JOIN hadiths h ON ph.hadith_id = h.id
            LEFT JOIN users u ON (
                (n.type = 'tahfidz' AND p.updated_by = u.id) OR
                (n.type = 'tahsin' AND pb.updated_by = u.id) OR
                (n.type = 'doa' AND ps.updated_by = u.id) OR
                (n.type = 'hadith' AND ph.updated_by = u.id)
            )
            WHERE n.child_id IN ($placeholders) $viewedClause
            ORDER BY n.created_at DESC
            LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($child_ids);
        return $stmt->fetchAll();
    }
}
