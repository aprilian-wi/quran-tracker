-- 002_add_school_id_to_tables.sql

-- Add school_id to users
ALTER TABLE users ADD COLUMN school_id INT NULL AFTER id;
UPDATE users SET school_id = 1 WHERE school_id IS NULL;
ALTER TABLE users MODIFY COLUMN school_id INT NOT NULL;
ALTER TABLE users ADD CONSTRAINT fk_users_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE users ADD INDEX idx_school (school_id);

-- Add school_id to classes
ALTER TABLE classes ADD COLUMN school_id INT NULL AFTER id;
UPDATE classes SET school_id = 1 WHERE school_id IS NULL;
ALTER TABLE classes MODIFY COLUMN school_id INT NOT NULL;
ALTER TABLE classes ADD CONSTRAINT fk_classes_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE classes ADD INDEX idx_class_school (school_id);

-- Optionally add to children if needed for direct lookups, but parent_id -> user -> school_id is also valid.
-- For performance/filtering complexity, adding it to children is often safer.
ALTER TABLE children ADD COLUMN school_id INT NULL AFTER id;
UPDATE children SET school_id = 1 WHERE school_id IS NULL;
ALTER TABLE children MODIFY COLUMN school_id INT NOT NULL;
ALTER TABLE children ADD CONSTRAINT fk_children_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE children ADD INDEX idx_child_school (school_id);
