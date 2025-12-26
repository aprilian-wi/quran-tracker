-- database/quran_tracker.sql
-- Quran Memorization Tracker Database
-- UTF8MB4 for full Arabic support
-- Created: November 12, 2025

DROP DATABASE IF EXISTS quran_tracker;
CREATE DATABASE quran_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quran_tracker;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'teacher', 'parent') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Classes Table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    teacher_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_teacher (teacher_id)
) ENGINE=InnoDB;

-- Mapping table to allow multiple teachers per class
CREATE TABLE classes_teachers (
    class_id INT NOT NULL,
    teacher_id INT NOT NULL,
    PRIMARY KEY (class_id, teacher_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ct_class (class_id),
    INDEX idx_ct_teacher (teacher_id)
) ENGINE=InnoDB;

-- Backfill mapping from existing classes.teacher_id for compatibility
INSERT INTO classes_teachers (class_id, teacher_id)
SELECT id, teacher_id FROM classes WHERE teacher_id IS NOT NULL;

-- Children Table
CREATE TABLE children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT NOT NULL,
    class_id INT NULL,
    date_of_birth DATE NULL,
    photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
    INDEX idx_parent (parent_id),
    INDEX idx_class (class_id)
) ENGINE=InnoDB;

-- Quran Structure (30 Juz, 114 Surahs)
CREATE TABLE quran_structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juz INT NOT NULL CHECK (juz BETWEEN 1 AND 30),
    surah_number INT NOT NULL CHECK (surah_number BETWEEN 1 AND 114),
    surah_name_ar VARCHAR(100) NOT NULL,
    surah_name_en VARCHAR(100) NOT NULL,
    full_verses INT NOT NULL,
    start_verse INT NOT NULL,
    end_verse INT NOT NULL,
    UNIQUE KEY unique_juz_surah (juz, surah_number),
    INDEX idx_juz (juz),
    INDEX idx_surah (surah_number)
) ENGINE=InnoDB;

-- Quran Verses (Arabic, Latin, Indonesian, Audio)
CREATE TABLE quran_verses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surah_number INT NOT NULL,
    verse_number INT NOT NULL,
    text_ar LONGTEXT NOT NULL,
    text_latin LONGTEXT NOT NULL,
    text_id LONGTEXT NOT NULL,
    audio_url VARCHAR(255) NULL,
    UNIQUE KEY unique_surah_verse (surah_number, verse_number),
    INDEX idx_surah (surah_number),
    INDEX idx_verse (verse_number)
) ENGINE=InnoDB;

-- Bookmarks for Quran Verses
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    surah_number INT NOT NULL,
    verse_number INT NOT NULL,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_verse (user_id, surah_number, verse_number),
    INDEX idx_user (user_id),
    INDEX idx_surah_verse (surah_number, verse_number)
) ENGINE=InnoDB;

-- Progress Status
CREATE TABLE progress_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    juz INT NOT NULL,
    surah_number INT NOT NULL,
    verse INT NOT NULL,
    status ENUM('reached', 'in_progress', 'memorized', 'fluent', 'repeating') NOT NULL,
    note TEXT NULL,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_juz_surah (juz, surah_number),
    INDEX idx_child (child_id),
    INDEX idx_status (status),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB;

-- Teaching Books (Buku Ajar/Pedoman)
CREATE TABLE teaching_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volume_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    total_pages INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_volume (volume_number)
) ENGINE=InnoDB;


-- Progress for Teaching Books
CREATE TABLE progress_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    book_id INT NOT NULL,
    page INT NOT NULL,
    status ENUM('in_progress', 'memorized', 'fluent', 'repeating') NOT NULL,
    note TEXT NULL,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES teaching_books(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_child_book (child_id, book_id),
    INDEX idx_status (status),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB;

-- Tables for Short Prayers (Doa-doa Pendek) and Progress
CREATE TABLE short_prayers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    arabic_text TEXT NOT NULL,
    translation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE progress_short_prayers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    prayer_id INT NOT NULL,
    status ENUM('in_progress', 'memorized') NOT NULL DEFAULT 'in_progress',
    updated_by INT NULL,
    note TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (prayer_id) REFERENCES short_prayers(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_child_prayer (child_id, prayer_id),
    INDEX idx_status (status),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB;

-- Tables for Hadiths and Progress
CREATE TABLE hadiths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    arabic_text TEXT NOT NULL,
    translation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE progress_hadiths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    hadith_id INT NOT NULL,
    status ENUM('in_progress', 'memorized') NOT NULL DEFAULT 'in_progress',
    updated_by INT NULL,
    note TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (hadith_id) REFERENCES hadiths(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_child_hadith (child_id, hadith_id),
    INDEX idx_status (status),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB;

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    type ENUM('tahfidz', 'tahsin', 'doa', 'hadith') NOT NULL,
    progress_id INT NOT NULL,
    viewed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    INDEX idx_child (child_id),
    INDEX idx_viewed (viewed),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert Superadmin (Password: Admin123!)
-- Hash: $2a$12$d4fHw6lc3A.9ZdNZrkSC5.JX6DxMigj.HijNDs3VAPnVexCLUYsle
INSERT INTO users (name, email, password, role) VALUES
('Super Admin', 'admin@qurantracker.com', '$2a$12$d4fHw6lc3A.9ZdNZrkSC5.JX6DxMigj.HijNDs3VAPnVexCLUYsle', 'superadmin');

-- Optional: Sample Data (Uncomment to use)
-- INSERT INTO users (name, email, password, role) VALUES
-- ('Ahmed Khan', 'teacher1@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher'),
-- ('Fatima Ali', 'parent1@home.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent');

-- INSERT INTO classes (name, teacher_id) VALUES ('Morning Class A', 2);

-- INSERT INTO children (name, parent_id, class_id) VALUES
-- ('Aisha Ali', 3, 1);

-- INSERT INTO progress_status (child_id, juz, surah_number, verse, status, updated_by) VALUES
-- (1, 1, 1, 7, 'memorized', 2);
