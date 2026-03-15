<?php
session_start();
include 'conexion.php';

$id_paquete = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($id_paquete)) {
    header("Location: listado_viajes.php");
    exit();
}

// Obtener datos del paquete
$query = "SELECT * FROM paquetes_turisticos WHERE id_paquete = '$id_paquete'";
$res = mysqli_query($conex, $query);
$paquete = mysqli_fetch_array($res);

if (!$paquete) {
    echo "Paquete no encontrado";
    exit();
}

// Obtener fechas
$query_dates = "SELECT * FROM viajes WHERE id_paquete = '$id_paquete' AND fecha_salida >= CURDATE() ORDER BY fecha_salida ASC";
$res_dates = mysqli_query($conex, $query_dates);
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Detalles - <?php echo $paquete['destino_ciudad']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1><?php echo $paquete['destino_ciudad']; ?> <small
                style="color:#aaa;">(<?php echo $paquete['destino_pais']; ?>)</small></h1>

        <div class="details-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 20px;">

            <div class="main-info">
                <div class="card">
                    <h3>Descripción</h3>
                    <p><?php echo $paquete['descripcion_detallada']; ?></p>
                    <hr>
                    <h3>Itinerario</h3>
                    <p style="white-space: pre-line;"><?php echo $paquete['itinerario']; ?></p>
                    <hr>
                    <p><strong>Tipo de Viaje:</strong> <?php echo $paquete['tipo_viaje']; ?></p>
                    <p><strong>Duración:</strong> <?php echo $paquete['duracion']; ?> días</p>
                </div>
            </div>

            <div class="sidebar">
                <div class="card" style="position: sticky; top: 20px;">
                    <h2>Desde <?php echo $paquete['precio_base']; ?>€</h2>
                    <p><small>*Precio por persona, base</small></p>

                    <h3>Próximas Salidas:</h3>
                    <?php if (mysqli_num_rows($res_dates) > 0): ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php
                            // Mostrar solo las primeras 3 fechas en modo lista rápida
                            $count = 0;
                            while ($fecha = mysqli_fetch_array($res_dates)):
                                if ($count >= 3)
                                    break;
                                $f_salida = date("d/m/Y", strtotime($fecha['fecha_salida']));
                                $f_llegada = date("d/m/Y", strtotime($fecha['fecha_llegada']));
                                echo "<li style='margin-bottom: 5px;'>📅 Salida: $f_salida <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;➡️ Llegada: $f_llegada <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🆔 Plazas: " . $fecha['plazas_disponibles'] . "</li>";
                                $count++;
                            endwhile;
                            ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay fechas programadas.</p>
                    <?php endif; ?>

                    <br>
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="reservar.php?id=<?php echo $id_paquete; ?>" class="button"
                            style="display: block; text-align: center; background-color: #28a745;">Reservar Ahora</a>
                    <?php else: ?>
                        <a href="login.php" class="button"
                            style="display: block; text-align: center; background-color: #007bff;">Iniciar Sesión para
                            Reservar</a>
                    <?php endif; ?>
                    <br>
                    <a href="index.php" style="display: block; text-align: center;">Volver al listado</a>
                </div>
            </div>

        </div>
    </div>
</body>

</html>