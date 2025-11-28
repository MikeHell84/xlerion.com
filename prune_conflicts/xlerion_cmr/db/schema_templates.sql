-- Minimal DB schema for templates module
CREATE TABLE IF NOT EXISTS templates (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  description TEXT,
  author TEXT,
  data JSON,
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS blocks (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  type TEXT,
  html TEXT,
  css TEXT,
  js TEXT,
  meta JSON
);

CREATE TABLE IF NOT EXISTS style_dictionaries (
  id TEXT PRIMARY KEY,
  name TEXT,
  vars JSON,
  components JSON
);

CREATE TABLE IF NOT EXISTS themes (
  id TEXT PRIMARY KEY,
  name TEXT,
  style_dictionary_id TEXT,
  overrides JSON
);
