<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "frasesdb");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener una frase aleatoria
$fraseAleatoria = "";
$sqlAleatoria = "SELECT contenido FROM frases ORDER BY RAND() LIMIT 1";
$resultAleatoria = $conexion->query($sqlAleatoria);
if ($fila = $resultAleatoria->fetch_assoc()) {
    $fraseAleatoria = $fila['contenido'];
}

// Obtener todas las frases
$sql = "SELECT id_frase, contenido FROM frases";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Frases con Voz</title>
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
        }

        .frase-container {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .frase {
            background: #ffffff;
            border-left: 6px solid #4CAF50;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .frase:hover {
            transform: translateY(-3px);
        }

        .frase p {
            font-size: 18px;
            color: #333;
            margin: 0 0 10px;
        }

        .leer-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .leer-btn:hover {
            background: #388e3c;
        }

        .destacada {
            max-width: 800px;
            margin: 0 auto 30px auto;
            padding: 20px;
            background: #dff0d8;
            border-left: 6px solid #3c763d;
            border-radius: 10px;
        }

        .destacada h2 {
            margin: 0 0 10px 0;
            color: #3c763d;
        }

        .destacada p {
            font-size: 20px;
            color: #2e4e2e;
        }
    </style>
</head>
<body>

    <h1>📢 Frases con Lectura Automática</h1>

    <div class="destacada">
        <h2>Frase seleccionada:</h2>
        <p id="fraseAleatoriaTexto"><?= htmlspecialchars($fraseAleatoria) ?></p>
    </div>

    <div class="frase-container">
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <div class="frase">
                <p><?= htmlspecialchars($fila['contenido']) ?></p>
                <button class="leer-btn" onclick="leerFrase('<?= htmlspecialchars($fila['contenido']) ?>')">Leer en voz alta</button>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        let leido = false;

        function leerFrase(texto) {
            if ('speechSynthesis' in window) {
                const mensaje = new SpeechSynthesisUtterance(texto);
                mensaje.lang = 'es-ES';
                speechSynthesis.cancel(); // Cancelar cualquier lectura previa
                speechSynthesis.speak(mensaje);
            } else {
                alert("Tu navegador no soporta la síntesis de voz.");
            }
        }

        // Reproducir automáticamente al mover el mouse por primera vez
        window.addEventListener('mousemove', function activarLectura() {
            if (!leido) {
                const texto = document.getElementById("fraseAleatoriaTexto").innerText;
                if (texto.trim() !== "") {
                    leerFrase(texto);
                    leido = true;
                    window.removeEventListener('mousemove', activarLectura);
                }
            }
        });
    </script>

</body>
</html>

<?php
$conexion->close();
?>
