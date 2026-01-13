<?php
// src/Models/Notification.php
class Notification
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new notification (Child)
    public function create($child_id, $type, $progress_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (child_id, type, progress_id, viewed) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$child_id, $type, $progress_id]);
    }

    // Create a new notification (User)
    public function createForUser($user_id, $type, $progress_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, type, progress_id, viewed) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$user_id, $type, $progress_id]);
    }

    // Get unread notifications for a specific child
    public function getUnreadByChild($child_id)
    {
        return $this->getNotifications(['child_ids' => [$child_id]], true);
    }

    // Get notifications for a list of children
    public function getByChildren(array $child_ids, $limit = 20, $onlyUnread = false)
    {
        if (empty($child_ids))
            return [];
        return $this->getNotifications(['child_ids' => $child_ids], $onlyUnread, $limit);
    }

    // Get notifications for a user (Teacher/Parent personal notifs)
    public function getByUser($user_id, $limit = 20, $onlyUnread = false)
    {
        return $this->getNotifications(['user_id' => $user_id], $onlyUnread, $limit);
    }

    // Get unread count for a list of children (for Badge)
    public function getUnreadCount(array $child_ids)
    {
        if (empty($child_ids))
            return 0;

        $placeholders = str_repeat('?,', count($child_ids) - 1) . '?';
        $sql = "SELECT COUNT(*) FROM notifications WHERE child_id IN ($placeholders) AND viewed = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($child_ids);
        return $stmt->fetchColumn();
    }

    // Get unread count for a user
    public function getUnreadCountByUser($user_id)
    {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND viewed = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    // Mark as read
    public function markAsRead($id)
    {
        $stmt = $this->pdo->prepare("UPDATE notifications SET viewed = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Mark all as read for children
    public function markAllAsRead(array $child_ids)
    {
        if (empty($child_ids))
            return false;
        $placeholders = str_repeat('?,', count($child_ids) - 1) . '?';
        $sql = "UPDATE notifications SET viewed = 1 WHERE child_id IN ($placeholders) AND viewed = 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($child_ids);
    }

    // Mark all as read for user
    public function markAllAsReadForUser($user_id)
    {
        $stmt = $this->pdo->prepare("UPDATE notifications SET viewed = 1 WHERE user_id = ? AND viewed = 0");
        return $stmt->execute([$user_id]);
    }

    // Private helper to fetch notifications with details
    // params: array $filters ['child_ids' => [], 'user_id' => int]
    private function getNotifications(array $filters, $onlyUnread = false, $limit = 50)
    {

        $conditions = [];
        $params = [];

        if (!empty($filters['child_ids'])) {
            $placeholders = str_repeat('?,', count($filters['child_ids']) - 1) . '?';
            $conditions[] = "n.child_id IN ($placeholders)";
            $params = array_merge($params, $filters['child_ids']);
        } elseif (!empty($filters['user_id'])) {
            $conditions[] = "n.user_id = ?";
            $params[] = $filters['user_id'];
        } else {
            return []; // No filter provided
        }

        if ($onlyUnread) {
            $conditions[] = "n.viewed = 0";
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "
            SELECT n.*, 
                   c.name as child_name, 
                   COALESCE(u.name, actor.name) as updated_by_name,
                   n.created_at,
                   CASE
                       WHEN n.type = 'tahfidz' THEN CONCAT('Juz ', p.juz, ', Surah ', p.surah_number, ':', p.verse, ' - ', p.status)
                       WHEN n.type = 'tahsin' THEN CONCAT('Page ', pb.page, ' - ', pb.status, ' (', tb.title, ')')
                       WHEN n.type = 'doa' THEN CONCAT(sp.title, ' - ', ps.status)
                       WHEN n.type = 'hadith' THEN CONCAT(h.title, ' - ', ph.status)
                       WHEN n.type = 'feed_comment' THEN CONCAT('mengomentari postingan Anda: \"', LEFT(fc.comment, 50), '...\"')
                   END as message,
                   CASE
                       WHEN n.type = 'tahfidz' THEN 'menu_book'
                       WHEN n.type = 'tahsin' THEN 'auto_stories'
                       WHEN n.type = 'doa' THEN 'volunteer_activism'
                       WHEN n.type = 'hadith' THEN 'format_quote'
                       WHEN n.type = 'feed_comment' THEN 'comment'
                   END as icon,
                   fc.user_id as actor_id,
                   fc.feed_id as target_feed_id -- Useful for linking back to feed
            FROM notifications n
            LEFT JOIN children c ON n.child_id = c.id
            -- Joins for Progress Types
            LEFT JOIN progress_status p ON n.type = 'tahfidz' AND n.progress_id = p.id
            LEFT JOIN progress_books pb ON n.type = 'tahsin' AND n.progress_id = pb.id
            LEFT JOIN teaching_books tb ON n.type = 'tahsin' AND pb.book_id = tb.id
            LEFT JOIN progress_short_prayers ps ON n.type = 'doa' AND n.progress_id = ps.id
            LEFT JOIN short_prayers sp ON ps.prayer_id = sp.id
            LEFT JOIN progress_hadiths ph ON n.type = 'hadith' AND n.progress_id = ph.id
            LEFT JOIN hadiths h ON ph.hadith_id = h.id
            
            -- Join for Feed Comments (progress_id is the comment_id)
            LEFT JOIN feed_comments fc ON n.type = 'feed_comment' AND n.progress_id = fc.id
            LEFT JOIN users actor ON fc.user_id = actor.id

            LEFT JOIN users u ON (
                (n.type = 'tahfidz' AND p.updated_by = u.id) OR
                (n.type = 'tahsin' AND pb.updated_by = u.id) OR
                (n.type = 'doa' AND ps.updated_by = u.id) OR
                (n.type = 'hadith' AND ph.updated_by = u.id)
            )
            WHERE $whereClause
            ORDER BY n.created_at DESC
            LIMIT " . (int) $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
