-- 008_add_microsite_to_schools.sql
ALTER TABLE schools 
ADD COLUMN slug VARCHAR(255) UNIQUE NULL AFTER name,
ADD COLUMN microsite_html LONGTEXT NULL AFTER address;
