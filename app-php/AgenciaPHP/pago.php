<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Recoger datos del formulario de reserva
$id_paquete = isset($_POST['id']) ? $_POST['id'] : 0;
$id_viaje = isset($_POST['id_viaje']) ? $_POST['id_viaje'] : "";

// Separar adultos y niños
$num_adultos = isset($_POST['num_adultos']) ? $_POST['num_adultos'] : 1;
$num_ninos = isset($_POST['num_ninos']) ? $_POST['num_ninos'] : 0;
$tipo_servicio = isset($_POST['tipo_servicio']) ? $_POST['tipo_servicio'] : 'Solo Desayuno';
$num_total = $num_adultos + $num_ninos;

// Si no hay datos válidos, volver
if (empty($id_paquete) || $id_viaje == "") {
    header("Location: index.php");
    exit();
}

// Obtener datos para mostrar resumen
$q_paq = "SELECT p.*, v.fecha_salida 
          FROM paquetes_turisticos p 
          JOIN viajes v ON p.id_paquete = v.id_paquete 
          WHERE p.id_paquete = '$id_paquete' AND v.id_viaje = '$id_viaje'";
$r_paq = mysqli_query($conex, $q_paq);
$datos = mysqli_fetch_array($r_paq);

if (!$datos) {
    echo "Error: Viaje no encontrado.";
    exit();
}

$destino = $datos['destino_ciudad'] . ", " . $datos['destino_pais'];
$fecha = date("d/m/Y", strtotime($datos['fecha_salida']));
$precio_base = $datos['precio_base'];


$precio_total = $precio_base * $num_total;

// Variables para el formulario de pago
$err_pago = "";
$num_tarjeta = "";
$titular = "";
$caducidad = "";
$cvv = "";
$pagado = false;
$id_reserva_creada = "";

// PROCESAR PAGO SI SE ENVIA EL FORMULARIO DE PAGO
if (isset($_POST['pagar'])) {
    $num_tarjeta = $_POST['num_tarjeta'];
    $titular = $_POST['titular'];
    $caducidad = $_POST['caducidad'];
    $cvv = $_POST['cvv'];

    // Recuperar valores del post si venimos de 'pagar'
    $num_adultos = $_POST['num_adultos'];
    $num_ninos = $_POST['num_ninos'];
    $tipo_servicio = $_POST['tipo_servicio'];
    $num_total = $num_adultos + $num_ninos;
    $precio_total = $precio_base * $num_total;


    if (!preg_match('/^[0-9]{16}$/', $num_tarjeta)) {
        $err_pago .= "Número de tarjeta inválido (debe tener 16 dígitos numéricos).<br>";
    }

    // Titular: solo letras y espacios
    if (!preg_match('/^[a-zA-Z\s]+$/', $titular)) {
        $err_pago .= "El titular solo puede contener letras y espacios.<br>";
    }

    // Caducidad: MM/YY
    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $caducidad)) {
        $err_pago .= "La fecha de caducidad debe ser válida (MM/YY, mes 01-12).<br>";
    } else {
        // Validar que no esté caducada
        $partes = explode('/', $caducidad);
        $mes_card = intval($partes[0]);
        $anio_card = intval($partes[1]) + 2000;

        $mes_actual = intval(date('m'));
        $anio_actual = intval(date('Y'));

        if ($anio_card < $anio_actual || ($anio_card == $anio_actual && $mes_card < $mes_actual)) {
            $err_pago .= "La tarjeta está caducada (" . $caducidad . "). Por favor use una tarjeta válida.<br>";
        }
    }

    // CVV: 3 dígitos exactos
    if (!preg_match('/^[0-9]{3}$/', $cvv)) {
        $err_pago .= "CVV inválido (debe tener 3 dígitos numéricos).<br>";
    }

    // Si no hay errores, procesar reserva
    if ($err_pago == "") {
        $dni_usuario = $_SESSION['dni'];
        $id_reserva = uniqid('RES');
        $estado = 'Confirmada';
      
        $check_sql = "SELECT plazas_disponibles FROM viajes WHERE id_viaje = '$id_viaje'";
        $check_res = mysqli_query($conex, $check_sql);
        $viaje_data = mysqli_fetch_assoc($check_res);

        if ($viaje_data['plazas_disponibles'] >= $num_total) {
        

            $sql = "INSERT INTO reservas (id_reserva, num_viajeros_adultos, num_viajeros_ninos, estado, precio_final, dni_usuario, id_viaje, tipo_servicio) 
                    VALUES ('$id_reserva', '$num_adultos', '$num_ninos', '$estado', '$precio_total', '$dni_usuario', '$id_viaje', '$tipo_servicio')";

            if (mysqli_query($conex, $sql)) {
                // Actualizar plazas
                $update_sql = "UPDATE viajes SET plazas_disponibles = plazas_disponibles - $num_total WHERE id_viaje = '$id_viaje'";
                mysqli_query($conex, $update_sql);

                $pagado = true;
                $id_reserva_creada = $id_reserva;
            } else {
                $err_pago = "Error al guardar la reserva: " . mysqli_error($conex);
            }
        } else {
            $err_pago = "Lo sentimos, ya no quedan suficientes plazas disponibles para completar su reserva. (Plazas restantes: " . $viaje_data['plazas_disponibles'] . ")";
        }
    }
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pasarela de Pago</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php if ($pagado): ?>
            <h1>¡Pago Realizado con Éxito!</h1>
            <div class="receipt">
                <p><strong>Referencia:</strong> <?php echo $id_reserva_creada; ?></p>
                <p><strong>Destino:</strong> <?php echo $destino; ?></p>
                <p><strong>Fecha Salida:</strong> <?php echo $fecha; ?></p>
                <p><strong>Servicio:</strong> <?php echo $tipo_servicio; ?></p>
                <p><strong>Viajeros:</strong> <?php echo "$num_adultos Adultos, $num_ninos Niños"; ?></p>
                <p><strong>Importe Pagado:</strong> <?php echo number_format($precio_total, 2); ?> €</p>
                <p style="color: green; font-weight: bold;">Transacción Aprobada</p>
            </div>
            <br>
            <a href="index.php">Volver al inicio</a> | <a href="mis_reservas.php">Ver mis reservas</a>

        <?php else: ?>
            <h1>Finalizar Pago</h1>

            <div class="resumen-compra">
                <h3>Resumen de Reserva</h3>
                <div class="card">
                    <p>Viaje a: <strong><?php echo $destino; ?></strong></p>
                    <p>Fecha: <?php echo $fecha; ?></p>
                    <p>Servicio: <strong><?php echo $tipo_servicio; ?></strong></p>
                    <p>Viajeros: <?php echo "$num_adultos Adultos, $num_ninos Niños"; ?></p>
                    <p class="total-price" style="font-size: 1.2rem; margin-top: 10px;">Total a pagar:
                        <strong><?php echo number_format($precio_total, 2); ?> €</strong>
                    </p>
                </div>
            </div>

            <?php if ($err_pago != ""): ?>
                <div style="background-color: #ffcccc; color: #cc0000; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                    <?php echo $err_pago; ?>
                </div>
            <?php endif; ?>

            <form action="pago.php" method="post">
                <!-- Mantener datos de la reserva -->
                <input type="hidden" name="id" value="<?php echo $id_paquete; ?>">
                <input type="hidden" name="id_viaje" value="<?php echo $id_viaje; ?>">
                <input type="hidden" name="num_adultos" value="<?php echo $num_adultos; ?>">
                <input type="hidden" name="num_ninos" value="<?php echo $num_ninos; ?>">
                <input type="hidden" name="tipo_servicio" value="<?php echo $tipo_servicio; ?>">

                <h3>Datos de Tarjeta</h3>
                <p>Número de Tarjeta (16 dígitos): <br>
                    <input type="text" name="num_tarjeta" value="<?php echo $num_tarjeta; ?>" maxlength="16"
                        placeholder="1234567812345678" required>
                </p>
                <p>Titular: <br>
                    <input type="text" name="titular" value="<?php echo $titular; ?>"
                        placeholder="Nombre como aparece en la tarjeta" required>
                </p>
                <p style="display:flex; gap:20px;">
                    <span>
                        Caducidad (MM/YY): <br>
                        <input type="text" name="caducidad" value="<?php echo $caducidad; ?>" placeholder="12/25"
                            style="width: 100px;" required>
                    </span>
                    <span>
                        CVV (3 dígitos): <br>
                        <input type="password" name="cvv" value="<?php echo $cvv; ?>" maxlength="3" style="width: 80px;"
                            required>
                    </span>
                </p>

                <input type="submit" name="pagar" value="Pagar <?php echo number_format($precio_total, 2); ?> €">
            </form>
            <br>
            <a href="reservar.php?id=<?php echo $id_paquete; ?>">Cancelar y Volver</a>
        <?php endif; ?>
    </div>
</body>

</html>