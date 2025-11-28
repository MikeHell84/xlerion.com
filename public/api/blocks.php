<?php
header('Content-Type: application/json; charset=utf-8');
$blocks = [
  [ 'id'=>'blk-hero-001','name'=>'Hero básico','type'=>'hero','html'=>'<header class="hero"><h1>{{title}}</h1><p>{{subtitle}}</p><a class="btn-cta" href="#">{{cta}}</a></header>' ],
  [ 'id'=>'blk-footer-001','name'=>'Footer simple','type'=>'footer','html'=>'<footer class="site-footer"><div class="container"><p>&copy; <?php echo date("Y"); ?> Mi Sitio</p></div></footer>' ],
  [ 'id'=>'blk-menu-001','name'=>'Menú básico','type'=>'menu','html'=>'<nav class="site-nav"><ul><li><a href="/">Inicio</a></li></ul></nav>' ]
];
echo json_encode($blocks);
