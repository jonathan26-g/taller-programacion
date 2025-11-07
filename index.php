<?php
// Validar que sea una petición POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no autorizado");
}

// Configuración de correo
$to = "info@hotmail.com";
$subject = "Mail desde el formulario";

// Captura y validación de datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$comentarios = isset($_POST['comentarios']) ? trim($_POST['comentarios']) : '';

// Validaciones básicas
$errores = array();

if (empty($nombre)) {
    $errores[] = "El nombre es requerido";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Email inválido";
}

if (empty($comentarios)) {
    $errores[] = "Los comentarios son requeridos";
}

// Si hay errores, mostrarlos y detener
if (!empty($errores)) {
    echo "<h3>Errores encontrados:</h3><ul>";
    foreach ($errores as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    exit;
}

// Sanitizar datos para prevenir inyección de headers
$nombre = str_replace(array("\r", "\n"), '', $nombre);
$email = str_replace(array("\r", "\n"), '', $email);

// Escapar HTML para prevenir XSS
$nombre_html = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$email_html = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$comentarios_html = nl2br(htmlspecialchars($comentarios, ENT_QUOTES, 'UTF-8'));

// Encabezados del correo
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Cuerpo del mensaje en formato HTML
$message = "
<html>
<head>
    <title>Información de Contacto</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .info { margin: 10px 0; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Información del formulario de contacto</h1>
    <div class='info'>
        <span class='label'>Nombre:</span> " . $nombre_html . "
    </div>
    <div class='info'>
        <span class='label'>Email:</span> " . $email_html . "
    </div>
    <div class='info'>
        <span class='label'>Comentarios:</span><br>
        " . $comentarios_html . "
    </div>
</body>
</html>";

// Intento de envío del correo
if (mail($to, $subject, $message, $headers)) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Mensaje Enviado</title>
    </head>
    <body>
        <h2>¡Gracias por comunicarse con nosotros!</h2>
        <p>Su mensaje ha sido enviado correctamente.</p>
    </body>
    </html>';
} else {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
    </head>
    <body>
        <h2>Error al enviar el mensaje</h2>
        <p>Por favor, intente nuevamente más tarde.</p>
    </body>
    </html>';
}
?>