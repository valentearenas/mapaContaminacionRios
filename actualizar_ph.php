<?php
<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$mysqli = new mysqli("localhost", "root", "", "bugbusters");
if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "error" => "Error de conexión"]);
    exit;
}

// Obtener datos del POST
$data = json_decode(file_get_contents("php://input"), true);
$nombre = $mysqli->real_escape_string($data['nombre']);
$ph_manual = intval($data['ph_manual']);
$ph_detectado = intval($data['ph_detectado']);

// Actualiza el registro (ajusta el nombre de la tabla y columnas)
$sql = "UPDATE rios SET ph = $ph_manual = $ph_detectado WHERE nombre_rio = '$nombre'";
if ($mysqli->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $mysqli->error]);
}
$mysqli->close();
?>