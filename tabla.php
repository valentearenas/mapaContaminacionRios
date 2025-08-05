<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "bugbusters");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Buscar por nombre de río si se envió el formulario
$busqueda = '';
if (isset($_GET['buscar'])) {
    $busqueda = $conexion->real_escape_string($_GET['buscar']);
} else {
    $busqueda = '';
}

// Filtro de tabla a mostrar
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'con_datos';

// Consulta para ríos con datos
$sql_con_datos = "SELECT id_rio, nombre_rio, zona, ph, nivelContaminacion FROM rios WHERE ph IS NOT NULL AND nivelContaminacion IS NOT NULL";
if ($busqueda !== '') {
    $sql_con_datos .= " AND nombre_rio LIKE '%$busqueda%'";
}

// Consulta para ríos sin datos
$sql_sin_datos = "SELECT id_rio, nombre_rio, zona FROM rios WHERE ph IS NULL AND nivelContaminacion IS NULL";
if ($busqueda !== '') {
    $sql_sin_datos .= " AND nombre_rio LIKE '%$busqueda%'";
}

$resultado_con_datos = $conexion->query($sql_con_datos);
$resultado_sin_datos = $conexion->query($sql_sin_datos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Ríos</title>
    <!-- Bootstrap para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff;
            padding-top: 90px; /* Espacio para el header fijo */
        }
        .container {
            margin-top: 0;
        }
        .table-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .menu-superior {
            width: 100%;
            background: linear-gradient(90deg, #0E51FA 70%, #0096FA 100%);
            color: #fff;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            height: 80px;
        }
        .logo-menu {
            padding: 0 30px;
            display: flex;
            align-items: center;
            height: 80px;
        }
        .logo-menu img {
            width: 60px;
            height: auto;
            display: block;
            border-radius: 50%;
            border: 2px solid #fff;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .regresar-inicio {
            margin-left: 20px;
            margin-right: 10px;
        }
        .regresar-inicio a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            background: #0096FA;
            padding: 10px 22px;
            border-radius: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            border: 2px solid #fff;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }
        .regresar-inicio a:hover {
            background: #fff;
            color: #0E51FA;
            border: 2px solid #0E51FA;
        }
        .tab-selector {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        .tab-selector form {
            display: inline;
        }
        .tab-selector button {
            border: none;
            background: #0096FA;
            color: #fff;
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: bold;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .tab-selector .active {
            background: #fff;
            color: #0E51FA;
            border: 2px solid #0E51FA;
        }
        .tab-selector button:hover {
            background: #fff;
            color: #0E51FA;
            border: 2px solid #0E51FA;
        }
        @media (max-width: 700px) {
            .menu-superior {
                flex-direction: column;
                height: auto;
                padding: 10px 0;
            }
            .logo-menu, .regresar-inicio {
                padding: 0;
                margin: 0;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="menu-superior">
        <div class="logo-menu">
            <img src="logo.png" alt="LOGO" />
        </div>
        <div class="regresar-inicio">
            <a href="index.html">Regresar a inicio</a>
        </div>
    </div>
<div class="container">
    <div class="table-container">
        <h1>Listado de Ríos</h1>
        <!-- Buscador y selector de tabla -->
        <form class="mb-4" method="get" action="">
            <div class="input-group mb-3">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre de río..." value="<?= htmlspecialchars($busqueda) ?>">
                <input type="hidden" name="tabla" value="<?= htmlspecialchars($tabla) ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>
        <div class="tab-selector mb-3">
            <a href="?tabla=con_datos&buscar=<?= urlencode($busqueda) ?>"
               class="btn <?= $tabla == 'con_datos' ? 'active' : 'btn-primary' ?>">Ríos con datos</a>
            <a href="?tabla=sin_datos&buscar=<?= urlencode($busqueda) ?>"
               class="btn <?= $tabla == 'sin_datos' ? 'active' : 'btn-primary' ?>">Ríos sin datos</a>
        </div>
        <?php if ($tabla == 'con_datos'): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Río</th>
                    <th>Nombre del Río</th>
                    <th>Zona</th>
                    <th>pH</th>
                    <th>Nivel de Contaminación</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_con_datos && $resultado_con_datos->num_rows > 0): ?>
                    <?php while($fila = $resultado_con_datos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['id_rio']) ?></td>
                            <td><?= htmlspecialchars($fila['nombre_rio']) ?></td>
                            <td><?= htmlspecialchars($fila['zona']) ?></td>
                            <td><?= htmlspecialchars($fila['ph']) ?></td>
                            <td><?= htmlspecialchars($fila['nivelContaminacion']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Río</th>
                    <th>Nombre del Río</th>
                    <th>Zona</th>
                    <th colspan="2" class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_sin_datos && $resultado_sin_datos->num_rows > 0): ?>
                    <?php while($fila = $resultado_sin_datos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['id_rio']) ?></td>
                            <td><?= htmlspecialchars($fila['nombre_rio']) ?></td>
                            <td><?= htmlspecialchars($fila['zona']) ?></td>
                            <td colspan="2" class="text-center text-warning">Este río aún no ha sido recolectado de datos</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
