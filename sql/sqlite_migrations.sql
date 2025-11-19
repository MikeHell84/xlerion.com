-- SQLite migrations for local testing
PRAGMA foreign_keys = ON;

CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  totp_secret TEXT,
  two_factor_enabled INTEGER DEFAULT 0,
  is_admin INTEGER DEFAULT 0,
  last_login_at DATETIME,
  remember_token TEXT,
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE cms_pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  excerpt TEXT,
  content TEXT,
  meta TEXT,
  is_published INTEGER DEFAULT 1,
  created_by INTEGER,
  updated_by INTEGER,
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE forms_submissions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  form_name TEXT NOT NULL,
  payload TEXT,
  ip TEXT,
  user_agent TEXT,
  is_read INTEGER DEFAULT 0,
  created_at DATETIME
);

CREATE TABLE contacts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name TEXT,
  last_name TEXT,
  email TEXT,
  phone TEXT,
  phone_alt TEXT,
  source TEXT,
  status TEXT DEFAULT 'active',
  notes TEXT,
  created_by INTEGER,
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE password_resets (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL,
  token TEXT NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at DATETIME
);

-- other tables can be added as needed for local testing
