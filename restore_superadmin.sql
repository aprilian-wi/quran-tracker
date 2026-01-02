-- Ensure default school exists
INSERT IGNORE INTO schools (id, name, address) VALUES (1, 'Default School', 'Main System');

-- Restore Superadmin User
-- Password is 'password123'
INSERT INTO users (name, email, password, role, school_id, created_at)
VALUES (
    'Super Admin', 
    'admin@qurantracker.com', 
    '$2a$12$gnJtt6lkarN2jyQQiY02Bu5jKcgBJPRpg34NSyjijiX.SeoLK1COi', -- Hash for 'password123'
    'superadmin', 
    1,
    NOW()
);
