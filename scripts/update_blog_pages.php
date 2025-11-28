<?php
// Script to update the blog master page and create three separate CMS pages
// Usage: php scripts/update_blog_pages.php

require_once __DIR__ . '/../src/Model/Database.php';

$pdo = Database::pdo();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

try {
    $pdo->beginTransaction();

    // Update blog page content
    $blogSlug = 'blog';
    $blogContent = <<<'HTML'
<h2>El origen de Total Darkness</h2>
<p>Un recorrido profundo por la génesis de esta obra literaria y su evolución hacia un pelijuego interactivo que combina narrativa inmersiva y filosofía.</p>
<h3>De la novela a la experiencia interactiva</h3>
<p>Exploramos cómo la obra se transforma en una experiencia interactiva que invita a la reflexión y la inmersión profunda: estructura narrativa, decisiones de diseño y la integración de la filosofía como motor de la experiencia.</p>
<h3>Retos técnicos y soluciones</h3>
<p>Se describen los principales retos técnicos afrontados (gestión de assets, sistemas de diálogo, rendimiento en tiempo real) y las soluciones aplicadas para mantener coherencia narrativa sin sacrificar rendimiento.</p>

<hr/>

<h2>Aplicación de la filosofía modular en videojuegos</h2>
<p>Cómo la modularidad impulsa la innovación técnica y creativa en el desarrollo de videojuegos, facilitando escalabilidad y adaptabilidad.</p>
<h3>Principios de modularidad</h3>
<p>Definimos los principios que guían la modularidad: interfaces claras, componentes desacoplados y contratos de datos estables entre sistemas.</p>
<h3>Ejemplos prácticos y beneficios</h3>
<p>Presentamos ejemplos concretos de implementación (sistemas de entidades, plugins para gameplay, pipelines de assets) y discutimos los beneficios para equipos: iteración más rápida, menor deuda técnica y mayor autonomía.</p>

<hr/>

<h2>Diagnóstico técnico como herramienta cultural</h2>
<p>Cómo el diagnóstico técnico se convierte en un medio para entender, preservar y potenciar el patrimonio cultural a través de soluciones tecnológicas.</p>
<h3>Metodologías aplicadas</h3>
<p>Descripción de metodologías prácticas para evaluar sistemas, documentar estados y proponer intervenciones tecnológicas con impacto cultural.</p>
<h3>Casos prácticos</h3>
<p>Ejemplos de proyectos donde el diagnóstico técnico permitió proteger, documentar o revitalizar patrimonio cultural, con indicadores de impacto social y territorial.</p>

<p><em>Conclusión:</em> Estas entradas buscan compartir aprendizajes y metodologías replicables que contribuyan a la innovación cultural y tecnológica.</p>
HTML;

    // Find blog page
    $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$blogSlug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $update = $pdo->prepare('UPDATE cms_pages SET title = ?, content = ?, meta = ? WHERE id = ?');
        $meta = json_encode(['updated_by'=>'script','updated_at'=>date('c')]);
        $update->execute(['Blog', $blogContent, $meta, $row['id']]);
        echo "Updated blog page (id={$row['id']})\n";
    } else {
        $ins = $pdo->prepare('INSERT INTO cms_pages (title, slug, content, meta, is_published, created_at) VALUES (?, ?, ?, ?, 1, ? )');
        $meta = json_encode(['created_by'=>'script','created_at'=>date('c')]);
        $ins->execute(['Blog', $blogSlug, $blogContent, $meta, date('c')]);
        echo "Inserted blog page\n";
    }

    // Create separate pages for the three entries
    $pages = [
        [
            'title' => 'El origen de Total Darkness',
            'slug' => 'el-origen-de-total-darkness',
        'content' => "<h2>El origen de Total Darkness</h2>\n<p>Un recorrido profundo por la génesis de esta obra literaria y su evolución hacia un pelijuego interactivo que combina narrativa inmersiva y filosofía.</p>\n<h3>De la novela a la experiencia interactiva</h3>\n<p>Exploramos cómo la obra se transforma en una experiencia interactiva que invita a la reflexión y la inmersión profunda.</p>\n<h3>Retos técnicos y soluciones</h3>\n<p>Se describen los principales retos técnicos y las soluciones aplicadas para mantener coherencia narrativa sin sacrificar rendimiento.</p>",
            'meta' => json_encode(['section_images'=>[]])
        ],
        [
            'title' => 'Aplicación de la filosofía modular en videojuegos',
            'slug' => 'filosofia-modular-videojuegos',
        'content' => "<h2>Aplicación de la filosofía modular en videojuegos</h2>\n<p>Cómo la modularidad impulsa la innovación técnica y creativa en el desarrollo de videojuegos, facilitando escalabilidad y adaptabilidad.</p>\n<h3>Principios de modularidad</h3>\n<p>Definimos los principios que guían la modularidad: interfaces claras, componentes desacoplados y contratos de datos estables entre sistemas.</p>\n<h3>Ejemplos prácticos y beneficios</h3>\n<p>Presentamos ejemplos concretos de implementación y discutimos los beneficios para equipos: iteración más rápida, menor deuda técnica y mayor autonomía.</p>",
            'meta' => json_encode(['section_images'=>[]])
        ],
        [
            'title' => 'Diagnóstico técnico como herramienta cultural',
            'slug' => 'diagnostico-tecnico-cultural',
        'content' => "<h2>Diagnóstico técnico como herramienta cultural</h2>\n<p>Cómo el diagnóstico técnico se convierte en un medio para entender, preservar y potenciar el patrimonio cultural a través de soluciones tecnológicas.</p>\n<h3>Metodologías aplicadas</h3>\n<p>Descripción de metodologías prácticas para evaluar sistemas, documentar estados y proponer intervenciones tecnológicas con impacto cultural.</p>\n<h3>Casos prácticos</h3>\n<p>Ejemplos de proyectos donde el diagnóstico técnico permitió proteger, documentar o revitalizar patrimonio cultural, con indicadores de impacto social y territorial.</p>\n<p><em>Conclusión:</em> Estas entradas buscan compartir aprendizajes y metodologías replicables que contribuyan a la innovación cultural y tecnológica.</p>",
            'meta' => json_encode(['section_images'=>[]])
        ],
    ];

    foreach ($pages as $p) {
        $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$p['slug']]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($exists) {
            $up = $pdo->prepare('UPDATE cms_pages SET title = ?, content = ?, meta = ?, is_published = 1 WHERE id = ?');
            $up->execute([$p['title'], $p['content'], $p['meta'], $exists['id']]);
            echo "Updated page {$p['slug']} (id={$exists['id']})\n";
        } else {
            $in = $pdo->prepare('INSERT INTO cms_pages (title, slug, content, meta, is_published, created_at) VALUES (?, ?, ?, ?, 1, ?)');
            $in->execute([$p['title'], $p['slug'], $p['content'], $p['meta'], date('c')]);
            echo "Inserted page {$p['slug']}\n";
        }
    }

    $pdo->commit();
    echo "Done.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
