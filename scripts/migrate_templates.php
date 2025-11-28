<?php
// Run SQL migration file against the project's database using Database::pdo()
require_once __DIR__ . '/../src/Model/Database.php';

try {
        $pdo = Database::pdo();
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
                // sqlite-compatible statements
                $stmts = [
                        "CREATE TABLE IF NOT EXISTS templates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT NULL,
    author_id INTEGER NULL,
    data TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);",
                        "CREATE TABLE IF NOT EXISTS template_usage (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    template_id INTEGER NOT NULL,
    context_type TEXT NOT NULL,
    context_id TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE
);",
                        // trigger to keep updated_at current
                        "CREATE TRIGGER IF NOT EXISTS trg_templates_updated_at AFTER UPDATE ON templates
BEGIN
    UPDATE templates SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;"
                ];
        } else {
                // default to mysql-compatible statements
                $stmts = [
                        "CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    slug VARCHAR(191) NOT NULL UNIQUE,
    description TEXT NULL,
    author_id INT NULL,
    data JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);",
                        "CREATE TABLE IF NOT EXISTS template_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    context_type VARCHAR(50) NOT NULL,
    context_id VARCHAR(191) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE
);"
                ];
        }

        foreach ($stmts as $s) {
                $pdo->exec($s);
        }
        echo "Migrations applied successfully\n";
} catch (PDOException $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        exit(1);
}
