-- database/migrations/007_update_notifications_table.sql

-- 1. Make child_id nullable
ALTER TABLE notifications MODIFY child_id INT NULL;

-- 2. Add user_id column
ALTER TABLE notifications ADD COLUMN user_id INT NULL AFTER child_id;
ALTER TABLE notifications ADD CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE notifications ADD INDEX idx_notif_user (user_id);

-- 3. Modify type ENUM to include feed_comment
-- Note: We must redeclare all existing enum values plus the new one.
ALTER TABLE notifications MODIFY type ENUM('tahfidz', 'tahsin', 'doa', 'hadith', 'feed_comment') NOT NULL;
