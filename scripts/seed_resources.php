<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
  $pdo = Database::pdo();
  $count = $pdo->query('SELECT COUNT(*) as c FROM resources')->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
  if ($count > 0) {
    echo "Resources already present: $count\n";
    exit(0);
  }
  $now = date('Y-m-d H:i:s');
  $examples = [
    ['slug'=>'guia-instalacion-xlerion','title'=>'Guía de instalación Xlerion','description'=>'Guía paso a paso para instalar la plataforma Xlerion.','file_path'=>'/media/docs/guia-instalacion.pdf','url'=>'','created_at'=>$now,'updated_at'=>$now],
    ['slug'=>'api-integracion','title'=>'API de integración','description'=>'Referencia de la API y ejemplos de integración.','file_path'=>'/media/docs/api-integracion.pdf','url'=>'','created_at'=>$now,'updated_at'=>$now],
    ['slug'=>'caso-exito-industria','title'=>'Caso de éxito - Industria','description'=>'Caso de estudio detallado de implementación en industria.','file_path'=>'/media/docs/caso-exito.pdf','url'=>'','created_at'=>$now,'updated_at'=>$now],
  ];
  $ins = $pdo->prepare('INSERT INTO resources (slug,title,description,file_path,url,created_at,updated_at) VALUES (?,?,?,?,?,?,?)');
  foreach ($examples as $e) { $ins->execute([$e['slug'],$e['title'],$e['description'],$e['file_path'],$e['url'],$e['created_at'],$e['updated_at']]); }
  echo "Seeded " . count($examples) . " resources\n";
} catch (Exception $ex){ echo 'Error: ' . $ex->getMessage() . "\n"; exit(1); }
