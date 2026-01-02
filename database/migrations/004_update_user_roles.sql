-- database/migrations/004_update_user_roles.sql

-- Modify role ENUM to include 'school_admin'
ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'school_admin', 'teacher', 'parent') NOT NULL;

-- Update existing tenant admins (anyone with role='superadmin' but school_id != 1) to 'school_admin'
UPDATE users SET role = 'school_admin' WHERE role = 'superadmin' AND school_id != 1;
