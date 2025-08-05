<?php
// Conexión a MySQL
$mysqli = new mysqli("localhost", "root", "", "bugbusters");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Cargar el archivo JSON
$json = file_get_contents("rios_veracruz1.json");
$data = json_decode($json, true);

foreach ($data['features'] as $item) {
    $nombre = $mysqli->real_escape_string($item['properties']['NOMBRE']);
    $zona = 'Veracruz';  // Zona fija para todos

    // Insertar en la base con ph y nivelContaminacion como NULL
    $sql = "INSERT INTO rios (nombre_rio, zona, ph, nivelContaminacion)
            VALUES ('$nombre', '$zona', NULL, NULL)";

    if (!$mysqli->query($sql)) {
        echo "Error insertando $nombre: " . $mysqli->error . "<br>";
    }
}

echo "Importación completada.";
?>
