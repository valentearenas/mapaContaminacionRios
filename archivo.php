<?php
// archivo.php

// Permitir solicitudes desde cualquier origen (CORS) si lo usas con fetch desde JS
header('Content-Type: application/json');

// Conexión a la base de datos (ajusta los datos según tu configuración)
$mysqli = new mysqli("localhost", "root", "", "bugbusters");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'mensaje' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

// Obtener datos del cuerpo de la petición (JSON)
$input = json_decode(file_get_contents('php://input'), true);
$nombre = isset($input['nombre']) ? $input['nombre'] : null;
$ph_manual = isset($input['ph_manual']) ? $input['ph_manual'] : null;
$ph_detectado = isset($input['ph_detectado']) ? $input['ph_detectado'] : null;

// Actualizar la tabla solo si hay datos
$mensaje = "";
$success = false;
if ($nombre && $ph_manual && $ph_detectado) {
    // Determinar el nivel de contaminación según el pH detectado (ejemplo simple)
    if ($ph_detectado < 5) {
        $contaminacion = 'Alta';
    } elseif ($ph_detectado < 7) {
        $contaminacion = 'Media';
    } else {
        $contaminacion = 'Baja';
    }

    // Actualizar la tabla (ajusta el nombre de la tabla y columnas)
    $sql = "UPDATE rios SET ph = ?, nivelContaminacion = ? WHERE nombre_rio = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'mensaje' => "Error en prepare: " . $mysqli->error]);
        exit;
    }
    $stmt->bind_param("sss", $ph_manual, $contaminacion, $nombre);

    if ($stmt->execute()) {
        $mensaje = "Datos actualizados correctamente.";
        $success = true;
    } else {
        $mensaje = "Error al actualizar: " . $stmt->error;
    }
    $stmt->close();
} else {
    $mensaje = "Faltan datos para actualizar la base de datos.";
}

$mysqli->close();

// Respuesta JSON para fetch
echo json_encode([
    'success' => $success,
    'mensaje' => $mensaje,
    'nombre' => $nombre,
    'ph_manual' => $ph_manual,
    'ph_detectado' => $ph_detectado
]);