<?php
// archivo: obtener_frase.php
header('Content-Type: application/json');

// Configura tu conexión MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "frasesdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión"]);
  exit();
}

$sql = "SELECT contenido FROM frases ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
  echo json_encode(["frase" => $row['contenido']]);
} else {
  echo json_encode(["frase" => "No hay frases disponibles."]);
}

$conn->close();
?>
