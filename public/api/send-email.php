<?php
// Habilitar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// Obtener los datos del formulario
$input = json_decode(file_get_contents('php://input'), true);

// Validar datos
if (empty($input['name']) || empty($input['email']) || empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos requeridos']);
    exit();
}

$to = 'contactus@xlerion.com';
$from = $input['email'];
$name = htmlspecialchars($input['name']);
$subject = 'Nuevo mensaje de contacto de ' . $name;
$message = htmlspecialchars($input['message']);

// Construir el email
$headers = "From: $from\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$body = "
<html>
<head>
    <title>$subject</title>
</head>
<body>
    <h2>$subject</h2>
    <p><strong>De:</strong> $name ($from)</p>
    <p><strong>Mensaje:</strong></p>
    <p>" . nl2br($message) . "</p>
</body>
</html>
";

// Enviar el email
if (mail($to, $subject, $body, $headers)) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Email enviado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al enviar el email']);
}
