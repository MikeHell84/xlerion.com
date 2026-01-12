<?php
// Habilitar registro de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/email-errors.log');

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
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// Log de debugging
error_log("Datos recibidos: " . print_r($input, true));

// Validar datos
if (empty($input['name']) || empty($input['email']) || empty($input['message'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Faltan campos requeridos',
        'received' => array_keys($input ?: [])
    ]);
    exit();
}

// Validar formato de email
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email inválido']);
    exit();
}

$to = 'contactus@xlerion.com';
$from = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
$name = htmlspecialchars(strip_tags($input['name']), ENT_QUOTES, 'UTF-8');
$subject = 'Nuevo mensaje de contacto de ' . $name;
$message = htmlspecialchars(strip_tags($input['message']), ENT_QUOTES, 'UTF-8');

// Construir el email con headers más compatibles
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "From: Formulario XLERION <noreply@xlerion.com>";
$headers[] = "Reply-To: $name <$from>";
$headers[] = "X-Mailer: PHP/" . phpversion();
$headers[] = "X-Priority: 3";

$body = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>$subject</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;'>
        <h2 style='color: #00e9fa; border-bottom: 2px solid #00e9fa; padding-bottom: 10px;'>
            $subject
        </h2>
        <div style='background-color: white; padding: 20px; border-radius: 5px; margin-top: 20px;'>
            <p><strong>De:</strong> $name</p>
            <p><strong>Email:</strong> <a href='mailto:$from'>$from</a></p>
            <p><strong>Mensaje:</strong></p>
            <div style='padding: 15px; background-color: #f9f9f9; border-left: 4px solid #00e9fa;'>
                <p>" . nl2br($message) . "</p>
            </div>
        </div>
        <p style='margin-top: 20px; font-size: 12px; color: #666;'>
            Este mensaje fue enviado desde el formulario de contacto de xlerion.com
        </p>
    </div>
</body>
</html>";

// Intentar enviar el email usando mail() nativo
$mailSent = @mail($to, $subject, $body, implode("\r\n", $headers));

if ($mailSent) {
    error_log("Email enviado exitosamente a $to");
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Email enviado correctamente'
    ]);
} else {
    $error = error_get_last();
    error_log("Error al enviar email: " . print_r($error, true));
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al enviar el email',
        'details' => 'Por favor verifica la configuración SMTP del servidor'
    ]);
}
