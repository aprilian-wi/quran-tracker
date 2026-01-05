-- database/migrations/005_create_videos_tables.sql

CREATE TABLE video_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'movie', -- Material Icon name
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    youtube_id VARCHAR(50) NOT NULL,
    description TEXT,
    duration VARCHAR(20) DEFAULT '00:00',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES video_categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Seed some categories
INSERT INTO video_categories (name, icon) VALUES 
('Kisah Nabi', 'auto_stories'), 
('Adab & Akhlak', 'mosque'), 
('Sejarah Islam', 'history'), 
('Tutorial Ibadah', 'volunteer_activism');
