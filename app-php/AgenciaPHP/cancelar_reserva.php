<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$id_reserva = isset($_GET['id']) ? $_GET['id'] : "";
$dni_usuario = $_SESSION['dni'];

if ($id_reserva == "") {
    header("Location: mis_reservas.php");
    exit();
}

// Verificar que la reserva pertenece al usuario
$sql = "SELECT r.*, p.destino_ciudad, p.destino_pais, v.fecha_salida, r.num_viajeros_adultos, r.num_viajeros_ninos, r.id_viaje 
        FROM reservas r 
        JOIN viajes v ON r.id_viaje = v.id_viaje 
        JOIN paquetes_turisticos p ON v.id_paquete = p.id_paquete
        WHERE r.id_reserva = '$id_reserva' AND r.dni_usuario = '$dni_usuario'";
$res = mysqli_query($conex, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "Reserva no encontrada o no tienes permiso para anularla.";
    echo "<br><a href='mis_reservas.php'>Volver</a>";
    exit();
}

$datos = mysqli_fetch_array($res);

// Procesar anulación si se confirma
if (isset($_POST['confirmar_anulacion'])) {
    $q_del = "DELETE FROM reservas WHERE id_reserva = '$id_reserva'";
    if (mysqli_query($conex, $q_del)) {
        // Restaurar plazas
        $plazas_a_devolver = $datos['num_viajeros_adultos'] + $datos['num_viajeros_ninos'];
        $id_viaje_reserva = $datos['id_viaje'];
        $q_restaurar = "UPDATE viajes SET plazas_disponibles = plazas_disponibles + $plazas_a_devolver WHERE id_viaje = '$id_viaje_reserva'";
        mysqli_query($conex, $q_restaurar);

        header("Location: mis_reservas.php"); // Redirigir tras borrar
        exit();
    } else {
        $error = "Error al anular: " . mysqli_error($conex);
    }
}

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Anular Reserva</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Anular Reserva</h1>
        <div style="background-color: #ffe6e6; border: 1px solid #ffcccc; padding: 20px; border-radius: 8px;">
            <h3>¿Estás seguro de que quieres cancelar esta reserva?</h3>
            <p><strong>Destino:</strong> <?php echo $datos['destino_ciudad'] . " (" . $datos['destino_pais'] . ")"; ?>
            </p>
            <p><strong>Fecha:</strong> <?php echo $datos['fecha_salida']; ?></p>
            <p><strong>Precio Pagado:</strong> <?php echo $datos['precio_final']; ?> €</p>

            <p style="color: red;">Esta acción no se puede deshacer.</p>

            <form action="cancelar_reserva.php?id=<?php echo $id_reserva; ?>" method="post">
                <input type="submit" name="confirmar_anulacion" value="Sí, Anular Reserva"
                    style="background-color: #dc3545;">
            </form>
            <br>
            <a href="mis_reservas.php" class="button"
                style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">No,
                Volver</a>

            <?php if (isset($error))
                echo "<p style='color:red'>$error</p>"; ?>
        </div>
    </div>
</body>

</html>