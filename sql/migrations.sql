-- SQL migrations for Xlerion Vanilla CMR
-- Charset and engine
SET NAMES utf8mb4;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  totp_secret VARCHAR(255),
  two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  last_login_at DATETIME NULL,
  remember_token VARCHAR(100),
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (email),
  INDEX (last_login_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cms_pages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(191) NOT NULL UNIQUE,
  title VARCHAR(191) NOT NULL,
  excerpt TEXT NULL,
  content LONGTEXT NULL,
  meta JSON NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_by BIGINT UNSIGNED NULL,
  updated_by BIGINT UNSIGNED NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cms_blocks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  key_name VARCHAR(191) NOT NULL UNIQUE,
  title VARCHAR(191) NULL,
  content LONGTEXT NULL,
  page_id BIGINT UNSIGNED NULL,
  `order` INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  meta JSON NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE media_files (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL,
  path VARCHAR(1024) NOT NULL,
  mime VARCHAR(100) NULL,
  size INT UNSIGNED NULL,
  uploaded_by BIGINT UNSIGNED NULL,
  usages JSON NULL,
  created_at DATETIME NULL,
  INDEX (filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contacts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NULL,
  last_name VARCHAR(100) NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(50) NULL,
  phone_alt VARCHAR(50) NULL,
  source VARCHAR(80) NULL,
  status VARCHAR(50) DEFAULT 'active',
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (email),
  INDEX (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE organizations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  website VARCHAR(255) NULL,
  phone VARCHAR(50) NULL,
  address VARCHAR(255) NULL,
  industry VARCHAR(100) NULL,
  size VARCHAR(50) NULL,
  notes TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_org (
  contact_id BIGINT UNSIGNED NOT NULL,
  organization_id BIGINT UNSIGNED NOT NULL,
  role VARCHAR(100) NULL,
  is_primary TINYINT(1) DEFAULT 0,
  added_by BIGINT UNSIGNED NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (contact_id, organization_id),
  INDEX (organization_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE interactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  contact_id BIGINT UNSIGNED NULL,
  organization_id BIGINT UNSIGNED NULL,
  type ENUM('note','call','email','meeting') NOT NULL DEFAULT 'note',
  subject VARCHAR(255) NULL,
  body LONGTEXT NULL,
  result LONGTEXT NULL,
  performed_by BIGINT UNSIGNED NULL,
  performed_at DATETIME NULL,
  attachments JSON NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (contact_id),
  INDEX (organization_id),
  INDEX (type),
  INDEX (performed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE opportunities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  contact_id BIGINT UNSIGNED NULL,
  organization_id BIGINT UNSIGNED NULL,
  value DECIMAL(12,2) NULL,
  currency VARCHAR(10) DEFAULT 'USD',
  stage VARCHAR(50) DEFAULT 'lead',
  probability TINYINT UNSIGNED DEFAULT 0,
  owner_id BIGINT UNSIGNED NULL,
  due_date DATE NULL,
  closed_at DATETIME NULL,
  notes TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (stage),
  INDEX (owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  related_type VARCHAR(100) NULL,
  related_id BIGINT UNSIGNED NULL,
  assigned_to BIGINT UNSIGNED NULL,
  due_date DATE NULL,
  priority TINYINT UNSIGNED DEFAULT 2,
  status VARCHAR(50) DEFAULT 'open',
  reminder_at DATETIME NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (assigned_to),
  INDEX (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE blog_posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(191) NOT NULL UNIQUE,
  excerpt TEXT NULL,
  content LONGTEXT NULL,
  author_id BIGINT UNSIGNED NULL,
  status VARCHAR(50) DEFAULT 'draft',
  published_at DATETIME NULL,
  views INT UNSIGNED DEFAULT 0,
  meta JSON NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX (slug),
  INDEX (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE blog_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  slug VARCHAR(191) NOT NULL UNIQUE,
  parent_id BIGINT UNSIGNED NULL,
  description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE blog_tags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  slug VARCHAR(191) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE post_tag (
  post_id BIGINT UNSIGNED NOT NULL,
  tag_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (post_id, tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE forms_submissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_name VARCHAR(191) NOT NULL,
  payload JSON NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE newsletter_subscribers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  name VARCHAR(150) NULL,
  consent_at DATETIME NULL,
  unsubscribed TINYINT(1) DEFAULT 0,
  token VARCHAR(191) NULL,
  source VARCHAR(100) NULL,
  created_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
  `key` VARCHAR(191) PRIMARY KEY,
  `value` LONGTEXT NULL,
  autoload TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(191) NOT NULL,
  auditable_type VARCHAR(191) NULL,
  auditable_id BIGINT UNSIGNED NULL,
  old_values JSON NULL,
  new_values JSON NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Foreign keys (best-effort; in shared hosting some FK creation may fail if privileges are limited)
ALTER TABLE cms_pages ADD CONSTRAINT fk_cms_pages_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE cms_pages ADD CONSTRAINT fk_cms_pages_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE cms_blocks ADD CONSTRAINT fk_cms_blocks_page FOREIGN KEY (page_id) REFERENCES cms_pages(id) ON DELETE CASCADE;
ALTER TABLE media_files ADD CONSTRAINT fk_media_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE contacts ADD CONSTRAINT fk_contacts_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE contact_org ADD CONSTRAINT fk_contact_org_contact FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE;
ALTER TABLE contact_org ADD CONSTRAINT fk_contact_org_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE;
ALTER TABLE interactions ADD CONSTRAINT fk_interactions_contact FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE;
ALTER TABLE interactions ADD CONSTRAINT fk_interactions_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE;
ALTER TABLE interactions ADD CONSTRAINT fk_interactions_user FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE opportunities ADD CONSTRAINT fk_opps_contact FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL;
ALTER TABLE opportunities ADD CONSTRAINT fk_opps_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL;
ALTER TABLE opportunities ADD CONSTRAINT fk_opps_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE blog_posts ADD CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL;

-- password_resets
CREATE TABLE IF NOT EXISTS password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Search improvements: analytics table and FTS virtual table for SQLite.
-- Note: For MySQL, consider adding FULLTEXT(title, excerpt, content) index.

-- Analytics: record search queries for basic insights
CREATE TABLE IF NOT EXISTS search_queries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  q VARCHAR(191) NOT NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  result_count INT UNSIGNED DEFAULT 0,
  created_at DATETIME NULL,
  INDEX (q),
  INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SQLite FTS5 virtual table for cms_pages content (used when running with sqlite)
-- This will need to be created when using SQLite; note this SQL is for sqlite3.
-- In deployment scripts, detect the DB driver and run the appropriate statements.
-- Example (sqlite):
-- CREATE VIRTUAL TABLE cms_pages_fts USING fts5(title, excerpt, content, page_id UNINDEXED);
-- And triggers to keep it in sync with cms_pages.

-- Page views analytics: record when a page is viewed (basic)
CREATE TABLE IF NOT EXISTS page_views (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_id BIGINT UNSIGNED NULL,
  slug VARCHAR(191) NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NULL,
  INDEX (page_id),
  INDEX (slug),
  INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: record fine-grained interaction events (CTA clicks, downloads, plays)
-- CREATE TABLE IF NOT EXISTS interaction_events (
--   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   page_id BIGINT UNSIGNED NULL,
--   slug VARCHAR(191) NULL,
--   event_type VARCHAR(100) NOT NULL,
--   metadata JSON NULL,
--   ip VARCHAR(45) NULL,
--   user_agent VARCHAR(255) NULL,
--   created_at DATETIME NULL,
--   INDEX (event_type), INDEX (created_at)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

