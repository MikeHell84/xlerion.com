<?php
// Register template API routes
$router->get('/api/templates', ['TemplateController','index']);
$router->get('/api/templates/:id', ['TemplateController','show']);
$router->post('/api/templates', ['TemplateController','create']);
$router->put('/api/templates/:id', ['TemplateController','update']);
$router->delete('/api/templates/:id', ['TemplateController','delete']);
$router->post('/api/templates/:id/duplicate', ['TemplateController','duplicate']);
