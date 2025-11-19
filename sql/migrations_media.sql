-- Migration: create media_files and media_jobs
-- Run this against your MySQL or SQLite database.

-- SQLite
CREATE TABLE IF NOT EXISTS media_files (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  filename TEXT NOT NULL,
  url TEXT NOT NULL,
  -- soft-delete support
  deleted_at TEXT,
  deleted_by INTEGER,
  thumb320 TEXT,
  thumb720 TEXT,
  mime TEXT,
  size INTEGER,
  uploaded_by INTEGER,
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS media_jobs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  media_id INTEGER NOT NULL,
  type TEXT NOT NULL,
  payload TEXT,
  status TEXT DEFAULT 'pending',
  attempts INTEGER DEFAULT 0,
  created_at TEXT DEFAULT (datetime('now'))
);

-- MySQL
-- CREATE TABLE IF NOT EXISTS media_files (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   filename VARCHAR(255) NOT NULL,
--   url VARCHAR(512) NOT NULL,
--   thumb320 VARCHAR(512),
--   thumb720 VARCHAR(512),
--   mime VARCHAR(128),
--   size BIGINT,
--   uploaded_by INT,
--   created_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- CREATE TABLE IF NOT EXISTS media_jobs (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   media_id INT NOT NULL,
--   type VARCHAR(64) NOT NULL,
--   payload TEXT,
--   status VARCHAR(32) DEFAULT 'pending',
--   attempts INT DEFAULT 0,
--   created_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
