<?php
// idempotent analytics migrations runner
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "Detected driver: $driver\n";
try {
    if ($driver === 'sqlite') {
        echo "Running SQLite analytics migrations...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS search_queries (id INTEGER PRIMARY KEY AUTOINCREMENT, q TEXT NOT NULL, ip TEXT, user_agent TEXT, result_count INTEGER DEFAULT 0, created_at TEXT);");
        $pdo->exec("CREATE TABLE IF NOT EXISTS page_views (id INTEGER PRIMARY KEY AUTOINCREMENT, page_id INTEGER NULL, slug TEXT NULL, ip TEXT NULL, user_agent TEXT NULL, created_at TEXT);");
        // optional interaction_events
        $pdo->exec("CREATE TABLE IF NOT EXISTS interaction_events (id INTEGER PRIMARY KEY AUTOINCREMENT, page_id INTEGER NULL, slug TEXT NULL, event_type TEXT NOT NULL, metadata TEXT NULL, ip TEXT NULL, user_agent TEXT NULL, created_at TEXT);");
        echo "SQLite analytics tables ensured.\n";
    } else {
        echo "Running MySQL analytics migrations...\n";
        // search_queries
        $pdo->exec("CREATE TABLE IF NOT EXISTS search_queries (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            q VARCHAR(191) NOT NULL,
            ip VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            result_count INT UNSIGNED DEFAULT 0,
            created_at DATETIME NULL,
            INDEX (q), INDEX (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        // page_views
        $pdo->exec("CREATE TABLE IF NOT EXISTS page_views (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_id BIGINT UNSIGNED NULL,
            slug VARCHAR(191) NULL,
            ip VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME NULL,
            INDEX (page_id), INDEX (slug), INDEX (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        // interaction_events (optional)
        $pdo->exec("CREATE TABLE IF NOT EXISTS interaction_events (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_id BIGINT UNSIGNED NULL,
            slug VARCHAR(191) NULL,
            event_type VARCHAR(100) NOT NULL,
            metadata JSON NULL,
            ip VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME NULL,
            INDEX (event_type), INDEX (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // try adding FULLTEXT on cms_pages (best-effort)
        try {
            $pdo->exec("ALTER TABLE cms_pages ADD FULLTEXT idx_ft_title_excerpt_content (title, excerpt, content)");
            echo "Added FULLTEXT index on cms_pages (title,excerpt,content).\n";
        } catch (Exception $e) {
            echo "Skipping FULLTEXT index (may already exist or not supported): " . $e->getMessage() . "\n";
        }

        echo "MySQL analytics tables ensured.\n";
    }
    echo "Migrations finished.\n";
} catch (Exception $ex) {
    echo "Migration error: " . $ex->getMessage() . "\n";
    exit(1);
}

return 0;
