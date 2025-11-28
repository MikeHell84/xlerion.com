<?php
require_once __DIR__ . '/Database.php';

class Template {
    public static function slugify($text){
        $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-a-z0-9]+~', '', $text);
        return $text ?: 'template-'.time();
    }

    public static function create(array $attrs){
        $pdo = Database::pdo();
        $check = $pdo->prepare('SELECT id FROM templates WHERE slug = ? LIMIT 1');
        // If client supplied a slug, behave get-or-create (return existing id)
        if (!empty($attrs['slug'])) {
            $slug = $attrs['slug'];
            $check->execute([$slug]);
            $exists = $check->fetch(PDO::FETCH_ASSOC);
            if ($exists) return $exists['id'];
        } else {
            // generate a unique slug based on name, appending numeric suffixes if needed
            $base = self::slugify($attrs['name'] ?? 'template');
            $slug = $base;
            $i = 0;
            while (true) {
                $check->execute([$slug]);
                $exists = $check->fetch(PDO::FETCH_ASSOC);
                if (!$exists) break;
                $i++;
                $slug = $base . '-' . $i;
            }
        }
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO templates (name, slug, description, author_id, data, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        // sanitize HTML inside data if present
        if (isset($attrs['data']) && is_array($attrs['data']) && isset($attrs['data']['html'])){
            require_once __DIR__ . '/../Helpers/HtmlSanitizer.php';
            $attrs['data']['html'] = HtmlSanitizer::sanitize_html($attrs['data']['html']);
        }
        $data = isset($attrs['data']) ? json_encode($attrs['data']) : json_encode(new stdClass());
        $stmt->execute([$attrs['name'], $slug, $attrs['description'] ?? null, $attrs['author_id'] ?? null, $data, $now, $now]);
        return $pdo->lastInsertId();
    }

    public static function find($id){
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM templates WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){
            $row['data'] = $row['data'] ? json_decode($row['data'], true) : [];
            return $row;
        }
        // fallback: try file-based storage (storage/templates.json) for templates saved by the simple PHP API
        try{
            $file = dirname(__DIR__) . '/storage/templates.json';
            if (file_exists($file)){
                $data = json_decode(@file_get_contents($file), true) ?: [];
                foreach ($data as $t){ if (($t['id'] ?? '') == $id){ // normalize to DB-like row
                    return [
                        'id' => $t['id'] ?? null,
                        'name' => $t['name'] ?? '',
                        'slug' => isset($t['name']) ? self::slugify($t['name']) : null,
                        'description' => $t['description'] ?? '',
                        'author_id' => $t['author'] ?? null,
                        'data' => isset($t['blocks']) ? ['blocks'=>$t['blocks']] : ($t['data'] ?? (isset($t['html']) ? ['html'=>$t['html']] : [])),
                        'created_at' => $t['createdAt'] ?? null,
                        'updated_at' => $t['createdAt'] ?? null
                    ]; }
                }
            }
        }catch(Exception $e){}
        return null;
    }

    public static function findAll(){
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT * FROM templates ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as &$r) { $r['data'] = $r['data'] ? json_decode($r['data'], true) : []; }
        // also include templates saved via the lightweight file API (storage/templates.json)
        try{
            $file = dirname(__DIR__) . '/storage/templates.json';
            if (file_exists($file)){
                $fileData = json_decode(@file_get_contents($file), true) ?: [];
                // avoid duplicates by id
                $existingIds = array_map(function($r){ return (string)($r['id'] ?? ''); }, $rows);
                foreach ($fileData as $t){
                    $tid = (string)($t['id'] ?? '');
                    if ($tid === '' || in_array($tid, $existingIds)) continue;
                    $rows[] = [
                        'id' => $t['id'] ?? null,
                        'name' => $t['name'] ?? '',
                        'slug' => isset($t['name']) ? self::slugify($t['name']) : null,
                        'description' => $t['description'] ?? '',
                        'author_id' => $t['author'] ?? null,
                        'data' => isset($t['blocks']) ? ['blocks'=>$t['blocks']] : ($t['data'] ?? (isset($t['html']) ? ['html'=>$t['html']] : [])),
                        'created_at' => $t['createdAt'] ?? null,
                        'updated_at' => $t['createdAt'] ?? null
                    ];
                }
            }
        }catch(Exception $e){}

        return $rows;
    }

    public static function update($id, array $attrs){
        $pdo = Database::pdo();
        $parts = [];
        $values = [];
        if (isset($attrs['name'])) { $parts[] = 'name = ?'; $values[] = $attrs['name']; }
        if (isset($attrs['slug'])) { $parts[] = 'slug = ?'; $values[] = $attrs['slug']; }
        if (isset($attrs['description'])) { $parts[] = 'description = ?'; $values[] = $attrs['description']; }
        if (isset($attrs['data'])) {
            // sanitize HTML inside data before storing
            if (is_array($attrs['data']) && isset($attrs['data']['html'])){
                require_once __DIR__ . '/../Helpers/HtmlSanitizer.php';
                $attrs['data']['html'] = HtmlSanitizer::sanitize_html($attrs['data']['html']);
            }
            $parts[] = 'data = ?'; $values[] = json_encode($attrs['data']); }
    if (empty($parts)) return false;
    $now = date('Y-m-d H:i:s');
    $parts[] = 'updated_at = ?';
    $values[] = $now;
    $values[] = $id;
    $sql = 'UPDATE templates SET ' . implode(', ', $parts) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute($values);
    if ($res){
        // invalidate compiled css cache for this template if exists
        $cacheFile = dirname(__DIR__) . '/storage/cache/templates/tpl-' . intval($id) . '.css';
        if (file_exists($cacheFile)) @unlink($cacheFile);
    }
    return $res;
    }

    public static function delete($id){
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('DELETE FROM templates WHERE id = ?');
    $ok = $stmt->execute([$id]);
    // remove compiled css cache if present
    $cacheFile = dirname(__DIR__) . '/storage/cache/templates/tpl-' . intval($id) . '.css';
    if (file_exists($cacheFile)) @unlink($cacheFile);
    return $ok;
    }

    public static function duplicate($id, $author_id = null){
        $t = self::find($id);
        if (!$t) return null;
        $new = [
            'name' => $t['name'] . ' (copy)',
            'slug' => self::slugify($t['name'] . '-copy-' . time()),
            'description' => $t['description'],
            'author_id' => $author_id ?? $t['author_id'],
            'data' => $t['data']
        ];
        return self::create($new);
    }
}
