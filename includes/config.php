<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', '6921');
define('DB_PASS', 'rata.cedro.dados');
define('DB_NAME', '6921');

// Configuración del sitio
define('SITE_NAME', 'Panaderia');
define('SITE_URL', 'https://mattprofe.com.ar/alumno/6921/');
define('ADMIN_EMAIL', 'admin@barcafe.com');

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City'); // Cambiar según tu ubicación

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // 0 en producción, 1 en desarrollo
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Directorio raíz
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDE_DIR', ROOT_DIR . '/includes');
define('UPLOAD_DIR', ROOT_DIR . '/uploads');

// Función para generar URL
function url($path = '') {
    return SITE_URL . '/' . $path;
}

// Función para redireccionar
function redirect($location) {
    header('Location: ' . url($location));
    exit;
}

// Función para verificar si el usuario está autenticado
function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Función para verificar si es administrador
function esAdmin() {
    return estaAutenticado() && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Función para generar un token CSRF
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar token CSRF
function verificarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Función para mostrar mensajes de alerta
function mostrarAlerta($tipo, $mensaje) {
    $_SESSION['alerta'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

// Función para obtener y limpiar mensajes de alerta
function obtenerAlerta() {
    if (isset($_SESSION['alerta'])) {
        $alerta = $_SESSION['alerta'];
        unset($_SESSION['alerta']);
        return $alerta;
    }
    return null;
}
?>