<?php
namespace Xlerion\Model;

use PDO;

class Resource {
  protected static function pdo(): PDO {
    require_once __DIR__ . '/../../src/Model/Database.php';
    return \Database::pdo();
  }

  public static function findBySlug(string $slug) {
    $pdo = self::pdo();
    $st = $pdo->prepare('SELECT * FROM resources WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  public static function all(int $limit = 50, int $offset = 0) {
    $pdo = self::pdo();
    $st = $pdo->prepare('SELECT * FROM resources ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $st->bindValue(1, $limit, PDO::PARAM_INT);
    $st->bindValue(2, $offset, PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create(array $data) {
    $pdo = self::pdo();
    $st = $pdo->prepare('INSERT INTO resources (slug, title, description, file_path, url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime("now"), datetime("now"))');
    $st->execute([
      $data['slug'], $data['title'], $data['description'] ?? null,
      $data['file_path'] ?? null, $data['url'] ?? null
    ]);
    return $pdo->lastInsertId();
  }

  public static function update(int $id, array $data) {
    $pdo = self::pdo();
    $st = $pdo->prepare('UPDATE resources SET slug = ?, title = ?, description = ?, file_path = ?, url = ?, updated_at = datetime("now") WHERE id = ?');
    return $st->execute([
      $data['slug'], $data['title'], $data['description'] ?? null,
      $data['file_path'] ?? null, $data['url'] ?? null, $id
    ]);
  }

  public static function delete(int $id) {
    $pdo = self::pdo();
    $st = $pdo->prepare('DELETE FROM resources WHERE id = ?');
    return $st->execute([$id]);
  }
}
