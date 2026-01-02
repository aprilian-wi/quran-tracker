-- database/migrations/003_isolate_content_tables.sql

-- Add school_id to teaching_books
ALTER TABLE teaching_books ADD COLUMN school_id INT NOT NULL DEFAULT 1;
ALTER TABLE teaching_books ADD CONSTRAINT fk_teaching_books_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE teaching_books ADD INDEX idx_book_school (school_id);

-- Add school_id to short_prayers
ALTER TABLE short_prayers ADD COLUMN school_id INT NOT NULL DEFAULT 1;
ALTER TABLE short_prayers ADD CONSTRAINT fk_short_prayers_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE short_prayers ADD INDEX idx_prayer_school (school_id);

-- Add school_id to hadiths
ALTER TABLE hadiths ADD COLUMN school_id INT NOT NULL DEFAULT 1;
ALTER TABLE hadiths ADD CONSTRAINT fk_hadiths_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE;
ALTER TABLE hadiths ADD INDEX idx_hadith_school (school_id);
