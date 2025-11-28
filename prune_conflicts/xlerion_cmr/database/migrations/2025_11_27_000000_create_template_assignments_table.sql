-- Migration: create template_assignments table
CREATE TABLE IF NOT EXISTS template_assignments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  page_id INTEGER NOT NULL,
  section TEXT NOT NULL,
  template_id INTEGER NOT NULL,
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now')),
  UNIQUE(page_id, section)
);
