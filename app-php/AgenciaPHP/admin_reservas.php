<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}
include 'conexion.php';
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Administración de Reservas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Todas las Reservas</h1>

        <?php
        $sql = "SELECT r.*, u.nombre, u.apellidos, u.email, p.destino_ciudad, v.fecha_salida 
                FROM reservas r 
                INNER JOIN usuarios u ON r.dni_usuario = u.dni
                INNER JOIN viajes v ON r.id_viaje = v.id_viaje
                INNER JOIN paquetes_turisticos p ON v.id_paquete = p.id_paquete
                ORDER BY r.id_reserva DESC";

        $res = mysqli_query($conex, $sql);

        if (mysqli_num_rows($res) == 0) {
            echo "<p>No hay reservas registradas.</p>";
        } else {
            echo "<table border='1' width='100%'>";
            echo "<tr>
                    <th>ID Reserva</th>
                    <th>Cliente</th>
                    <th>Destino</th>
                    <th>Fecha Salida</th>
                    <th>Adultos/Niños</th>
                    <th>Precio</th>
                    <th>Estado</th>
                  </tr>";

            while ($fila = mysqli_fetch_array($res)) {
                echo "<tr>";
                echo "<td>" . $fila['id_reserva'] . "</td>";
                echo "<td>" . $fila['nombre'] . " " . $fila['apellidos'] . "<br><small>" . $fila['email'] . "</small></td>";
                echo "<td>" . $fila['destino_ciudad'] . "</td>";
                echo "<td>" . $fila['fecha_salida'] . "</td>";
                echo "<td>" . $fila['num_viajeros_adultos'] . " / " . $fila['num_viajeros_ninos'] . "</td>";
                echo "<td>" . $fila['precio_final'] . " €</td>";
                echo "<td>" . $fila['estado'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>

        <br>
        <a href="admin_dashboard.php">Volver al Panel</a>
    </div>
</body>

</html>