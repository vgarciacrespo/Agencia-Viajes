<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$id_paquete = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($id_paquete)) {
    header("Location: index.php");
    exit();
}

// Obtener datos del paquete
$query = "SELECT * FROM paquetes_turisticos WHERE id_paquete = '$id_paquete'";
$res = mysqli_query($conex, $query);
$paquete = mysqli_fetch_array($res);

if (!$paquete) {
    echo "<html><head><link rel='stylesheet' href='style.css'></head>
          <body><div class='container'><h2 style='color: var(--danger-color)'>Error: Paquete no encontrado.</h2>
          <a href='index.php'>Volver al listado</a></div></body></html>";
    exit();
}

$error = "";

// --- PROCESAR FORMULARIO (VALIDACIÓN DE DISPONIBILIDAD) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_viaje_post = mysqli_real_escape_string($conex, $_POST['id_viaje']);
    $num_adultos = intval($_POST['num_adultos']);
    $num_ninos = intval($_POST['num_ninos']);
    $total_personas = $num_adultos + $num_ninos;
    $tipo_servicio = htmlspecialchars($_POST['tipo_servicio']);

    // Consultar plazas disponibles reales
    $check_sql = "SELECT plazas_disponibles FROM viajes WHERE id_viaje = '$id_viaje_post'";
    $check_res = mysqli_query($conex, $check_sql);
    $viaje_data = mysqli_fetch_assoc($check_res);

    if ($viaje_data) {
        if ($total_personas > $viaje_data['plazas_disponibles']) {
            $error = "No hay suficientes plazas disponibles. Solicitadas: $total_personas, Disponibles: " . $viaje_data['plazas_disponibles'];
        } else {
      
            ?>
            <html>

            <body onload="document.getElementById('form_pago').submit();">
                <form id="form_pago" action="pago.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id_paquete; ?>">
                    <input type="hidden" name="id_viaje" value="<?php echo $id_viaje_post; ?>">
                    <input type="hidden" name="num_adultos" value="<?php echo $num_adultos; ?>">
                    <input type="hidden" name="num_ninos" value="<?php echo $num_ninos; ?>">
                    <input type="hidden" name="tipo_servicio" value="<?php echo $tipo_servicio; ?>">
                </form>
                <p>Redirigiendo a la pasarela de pago...</p>
            </body>

            </html>
            <?php
            exit();
        }
    } else {
        $error = "El viaje seleccionado no es válido.";
    }
}

// Obtener fechas de viaje disponibles para este paquete
$query_dates = "SELECT * FROM viajes WHERE id_paquete = '$id_paquete' AND fecha_salida >= CURDATE() ORDER BY fecha_salida ASC";
$res_dates = mysqli_query($conex, $query_dates);
$num_fechas = mysqli_num_rows($res_dates);

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reservar - <?php echo $paquete['destino_ciudad']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Reservar Viaje</h1>

        <div class="card">
            <h2><?php echo $paquete['destino_ciudad'] . " (" . $paquete['destino_pais'] . ")"; ?></h2>
            <p><?php echo $paquete['descripcion_detallada']; ?></p>
            <p><strong>Precio Base: <?php echo $paquete['precio_base']; ?>€</strong> / persona</p>
        </div>

        <?php if ($error): ?>
            <div
                style="background-color: #ffcccc; color: #cc0000; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ff9999;">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($num_fechas > 0): ?>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $id_paquete; ?>">

                <h3 style="margin-top: 20px;">Configura tu Reserva</h3>

                <label for="id_viaje">Selecciona una fecha de salida:</label>
                <select name="id_viaje" id="id_viaje" required>
                    <?php while ($fecha = mysqli_fetch_array($res_dates)) {
                        $f_format = date("d/m/Y", strtotime($fecha['fecha_salida']));
                        // Mantener selección si hubo error
                        $selected = (isset($_POST['id_viaje']) && $_POST['id_viaje'] == $fecha['id_viaje']) ? 'selected' : '';
                        echo "<option value='" . $fecha['id_viaje'] . "' $selected>Salida: " . $f_format . " - Plazas: " . $fecha['plazas_disponibles'] . "</option>";
                    } ?>
                </select>

                <label for="tipo_servicio">Tipo de Servicio:</label>
                <select name="tipo_servicio" id="tipo_servicio" required>
                    <option value="Solo Desayuno">Solo Desayuno</option>
                    <option value="Media Pensión">Media Pensión</option>
                    <option value="Pensión Completa">Pensión Completa</option>
                    <option value="Todo Incluido">Todo Incluido</option>
                </select>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label for="num_adultos">Adultos (+12 años):</label>
                        <input type="number" name="num_adultos" id="num_adultos"
                            value="<?php echo isset($_POST['num_adultos']) ? $_POST['num_adultos'] : 1; ?>" min="1" max="10"
                            required>
                    </div>
                    <div style="flex: 1;">
                        <label for="num_ninos">Niños (0-11 años):</label>
                        <input type="number" name="num_ninos" id="num_ninos"
                            value="<?php echo isset($_POST['num_ninos']) ? $_POST['num_ninos'] : 0; ?>" min="0" max="10"
                            required>
                    </div>
                </div>

                <input type="submit" value="Continuar al Pago">
            </form>
        <?php else: ?>
            <div style="background-color: rgba(255,100,100,0.2); padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p><strong>Lo sentimos, no hay fechas disponibles para este viaje en este momento.</strong></p>
            </div>
        <?php endif; ?>

        <br>
        <a href="index.php">Cancelar y volver al listado</a>
    </div>
</body>

</html>