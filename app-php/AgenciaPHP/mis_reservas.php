<?php
session_start();
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php

        function pintar_tabla_reservas($res, $titulo, $es_historial)
        {
            echo "<h1>$titulo</h1>";
            
            echo "<div style='margin-bottom: 20px;'>";
            echo "<a href='mis_reservas.php' style='margin-right: 15px;'>Ver Activas</a>";
            echo "<a href='mis_reservas.php?view=historial'>Ver Historial</a>";
            echo "</div>";

            if (mysqli_num_rows($res) == 0) {
                echo "<p>No hay reservas en esta sección.</p>";
            } else {
                echo "<table border='1'><tr><th>Destino</th><th>Fecha</th><th>Viajeros</th><th>Total</th><th>Acciones</th></tr>";
                while ($fila = mysqli_fetch_array($res)) {
                    $id_reserva = $fila['id_reserva'];
                    
                    $accion = ($es_historial) 
                        ? "<span style='color:gray;'>Finalizada</span>" 
                        : "<a href='cancelar_reserva.php?id=$id_reserva' style='color:red;'>Cancelar</a>";

                    printf(
                        "<tr>
                        <td>%s (%s)</td>
                        <td>%s</td>
                        <td>%d</td>
                        <td>%.2f €</td>
                        <td>%s</td>
                    </tr>",
                        $fila['destino_ciudad'],
                        $fila['destino_pais'],
                        $fila['fecha_salida'],
                        $fila['num_viajeros_adultos'] + $fila['num_viajeros_ninos'],
                        $fila['precio_final'],
                        $accion
                    );
                }
                echo "</table>";
            }
            echo "<br><a href='index.php'>Volver al inicio</a>";
        }

        if (!isset($_SESSION['dni'])) {
            header("Location: index.php");
            exit();
        } else {
            include 'conexion.php';
            $dni = $_SESSION['dni'];
            $hoy = date('Y-m-d');

            $view = isset($_GET['view']) ? $_GET['view'] : 'activas';

            if ($view === 'historial') {
                $condicion_fecha = "v.fecha_salida < '$hoy'";
                $titulo = "Historial de Reservas (Pasadas)";
                $es_historial = true;
            } else {
                $condicion_fecha = "v.fecha_salida >= '$hoy'";
                $titulo = "Reservas Activas";
                $es_historial = false;
            }

            $sql = "SELECT r.*, p.destino_ciudad, p.destino_pais, v.fecha_salida 
                         FROM reservas r 
                         INNER JOIN viajes v ON r.id_viaje = v.id_viaje
                         INNER JOIN paquetes_turisticos p ON v.id_paquete = p.id_paquete 
                         WHERE r.dni_usuario = '$dni' AND $condicion_fecha
                         ORDER BY v.fecha_salida DESC";

            $res = mysqli_query($conex, $sql);

            pintar_tabla_reservas($res, $titulo, $es_historial);
        }
        ?>
    </div>
</body>

</html>