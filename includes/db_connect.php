<?php
// Incluir archivo de configuración
require_once 'config.php';

// Crear conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset UTF-8
$conn->set_charset("utf8");

// Función para limpiar datos de entrada y prevenir inyección SQL
function limpiarDato($conn, $dato) {
    if (is_array($dato)) {
        $array_limpio = [];
        foreach ($dato as $key => $value) {
            $array_limpio[$key] = limpiarDato($conn, $value);
        }
        return $array_limpio;
    } else {
        return $conn->real_escape_string(trim($dato));
    }
}

// Función para ejecutar consultas y manejar errores
function ejecutarConsulta($conn, $sql) {
    $resultado = $conn->query($sql);
    if (!$resultado) {
        // Registrar el error en un archivo de log
        error_log("Error SQL: " . $conn->error . " - Query: " . $sql, 0);
        return false;
    }
    return $resultado;
}

// Función para obtener una fila como array asociativo
function obtenerFila($resultado) {
    if ($resultado && $resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    }
    return false;
}

// Función para obtener todas las filas como array asociativo
function obtenerFilas($resultado) {
    $filas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $filas[] = $fila;
        }
    }
    return $filas;
}

// Función para obtener el último ID insertado
function ultimoIdInsertado($conn) {
    return $conn->insert_id;
}

// Función para cerrar la conexión
function cerrarConexion($conn) {
    $conn->close();
}
?>