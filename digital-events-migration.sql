-- sqlite3 migration file, to adapt from drumrum-version code back to master
-- ... while marking all sessions as online_only

UPDATE session_detail SET long_description = long_description || char(10) || char(10) || "Location: " || location;

ALTER TABLE session_detail ADD COLUMN location_lat DOUBLE PRECISION DEFAULT NULL;
ALTER TABLE session_detail ADD COLUMN location_lng DOUBLE PRECISION DEFAULT NULL;
ALTER TABLE session_detail ADD COLUMN online_only BOOLEAN DEFAULT '1' NOT NULL;
ALTER TABLE session_detail ADD COLUMN location_name VARCHAR(255) DEFAULT '' NOT NULL;
ALTER TABLE session_detail ADD COLUMN location_street_no VARCHAR(255) DEFAULT '' NOT NULL;
ALTER TABLE session_detail ADD COLUMN location_zipcode VARCHAR(255) DEFAULT '' NOT NULL;
ALTER TABLE session_detail ADD COLUMN location_city VARCHAR(255) DEFAULT '' NOT NULL;
