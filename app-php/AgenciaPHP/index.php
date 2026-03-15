<?php
session_start();
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Agencia de Viajes - Inicio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php
        include 'conexion.php';

        echo "<div style='text-align:center; margin-bottom: 20px;'>";
        echo "<h1>Agencia de Viajes</h1>";
        echo "<div style='background-color: #f0f0f0; padding: 10px; border-radius: 5px; display: inline-block;'>";
        if (isset($_SESSION['usuario'])) {
            echo "<span style='font-weight:bold; display:block; margin-bottom:5px;'>Hola, " . $_SESSION['usuario'] . "</span>";
            echo "<div style='font-size: 0.9em;'>";
            echo "<a href='mis_reservas.php' style='margin:0 10px;'>Mis Reservas</a>";
            echo "<a href='perfil.php' style='margin:0 10px;'>Mi Perfil</a>";
            if ($_SESSION['tipo'] == 'admin' || $_SESSION['tipo'] == 'empleado') {
                echo "<a href='admin_dashboard.php' style='margin:0 10px;'>Panel Admin</a>";
            }
            echo "<a href='logout.php' style='margin:0 10px; color: #dc3545;'>Salir</a>";
            echo "</div>";
        } else {
            echo "<a href='login.php' class='button' style='margin-right:10px;'>Iniciar Sesión</a> ";
            echo "<a href='registro.php' class='button' style='background-color:#28a745;'>Registrarse</a>";
        }
        echo "</div>";
        echo "</div>";
        echo "<hr>";

   
        $pais = isset($_GET['pais']) ? trim($_GET['pais']) : '';
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
        $precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : '';
        $precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : '';
        $duracion = isset($_GET['duracion']) ? $_GET['duracion'] : '';

        
        $errors = [];

    //Destino no debe contener números
        if ($pais != '' && preg_match('/\d/', $pais)) {
            $errors[] = "El destino no debe contener números, solo letras.";
        }

      //Precio no negativo
        if (($precio_min != '' && $precio_min < 0) || ($precio_max != '' && $precio_max < 0)) {
            $errors[] = "El precio no puede ser negativo.";
        }
        if ($precio_min != '' && $precio_max != '' && $precio_min > $precio_max) {
            $errors[] = "El precio mínimo no puede ser mayor que el máximo.";
        }

        // Duración no negativa
        if ($duracion != '' && $duracion < 0) {
            $errors[] = "La duración no puede ser negativa.";
        }

        //Fecha no puede ser en el pasado
        if ($fecha != '') {
            $current_month = date('Y-m');
            if ($fecha < $current_month) {
                $errors[] = "La fecha del viaje no puede ser en el pasado.";
            }
        }

        //Obtener tipos de viaje
        $types_res = mysqli_query($conex, "SELECT DISTINCT tipo_viaje FROM paquetes_turisticos");
        ?>

        <!-- Mostrar errores si hay -->
        <?php if (!empty($errors)): ?>
            <div
                style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>Por favor, corrija los siguientes errores:</strong>
                <ul style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="filter-box"
            style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <form action="index.php" method="get">
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; align-items: end;">
                    <!-- Destino -->
                    <div style="display: flex; flex-direction: column;">
                        <label style="font-weight: 500; margin-bottom: 8px;">Destino</label>
                        <input type="text" name="pais" value="<?php echo htmlspecialchars($pais); ?>"
                            placeholder="País o Ciudad"
                            style="padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                    </div>

                    <!-- Fecha -->
                    <div style="display: flex; flex-direction: column;">
                        <label style="font-weight: 500; margin-bottom: 8px;">Fecha Viaje</label>
                        <input type="month" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>"
                            min="<?php echo date('Y-m'); ?>"
                            style="padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                    </div>

                    <!-- Tipo -->
                    <div style="display: flex; flex-direction: column;">
                        <label style="font-weight: 500; margin-bottom: 8px;">Tipo de Viaje</label>
                        <select name="tipo"
                            style="padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                            <option value="">Todos</option>
                            <?php while ($row = mysqli_fetch_array($types_res)): ?>
                                <option value="<?php echo $row['tipo_viaje']; ?>" <?php echo ($tipo == $row['tipo_viaje']) ? 'selected' : ''; ?>>
                                    <?php echo $row['tipo_viaje']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div style="display: flex; flex-direction: column;">
                        <label style="font-weight: 500; margin-bottom: 8px;">Rango Precio (€)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="precio_min" min="0" step="0.01"
                                value="<?php echo htmlspecialchars($precio_min); ?>" placeholder="Min"
                                style="width: 100%; padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                            <input type="number" name="precio_max" min="0" step="0.01"
                                value="<?php echo htmlspecialchars($precio_max); ?>" placeholder="Max"
                                style="width: 100%; padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                        </div>
                    </div>

                    <!-- Duración -->
                    <div style="display: flex; flex-direction: column;">
                        <label style="font-weight: 500; margin-bottom: 8px;">Duración Máx.</label>
                        <input type="number" name="duracion" min="1"
                            value="<?php echo htmlspecialchars($duracion); ?>" placeholder="Días"
                            style="width: 100%; padding: 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.2); color: white;">
                    </div>

                    <!-- Botones -->
                    <div style="display: flex; gap: 10px;">
                        <input type="submit" value="Buscar" class="button"
                            style="padding: 10px 25px; cursor: pointer; flex: 2;">
                        <a href="index.php"
                            style="display: flex; align-items: center; justify-content: center; flex: 1; color: #aaa; text-decoration: none; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px;">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <h2>Viajes Disponibles</h2>

        <div class="grid-viajes"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php

            if (empty($errors)) {
                $sql = "SELECT DISTINCT p.* FROM paquetes_turisticos p";
                if ($fecha != '') {
                    $sql .= " INNER JOIN viajes v ON p.id_paquete = v.id_paquete";
                }

                $where = [];
                
                $safe_pais = mysqli_real_escape_string($conex, $pais);
                $safe_tipo = mysqli_real_escape_string($conex, $tipo);
                $safe_fecha = mysqli_real_escape_string($conex, $fecha);
                $safe_min = floatval($precio_min); 
                $safe_max = floatval($precio_max); 
                $safe_dur = intval($duracion);   
            
                if ($pais != '') {
                    $where[] = "(p.destino_pais LIKE '%$safe_pais%' OR p.destino_ciudad LIKE '%$safe_pais%')";
                }
                if ($fecha != '') {
                    $where[] = "v.fecha_salida LIKE '$safe_fecha%'";
                }
                if ($tipo != '') {
                    $where[] = "p.tipo_viaje = '$safe_tipo'";
                }
                if ($precio_min != '') {
                    $where[] = "p.precio_base >= $safe_min";
                }
                if ($precio_max != '') {
                    $where[] = "p.precio_base <= $safe_max";
                }
                if ($duracion != '') {
                    $where[] = "p.duracion <= $safe_dur";
                }
                if (count($where) > 0) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $res = mysqli_query($conex, $sql);
            } else {

                $res = null;
            }
            if ($res === null) {
                echo "<p>No se pueden mostrar resultados debido a errores en los filtros.</p>";
            } else if (!$res || mysqli_num_rows($res) == 0) {
                echo "<p>No se encontraron viajes con esos criterios.</p>";
            } else {
                while ($viaje = mysqli_fetch_array($res)) {
                    echo "<div class='card' style='display:flex; flex-direction:column; justify-content:space-between;'>";
                    echo "<div>";
                    echo "<h3>" . $viaje['destino_ciudad'] . "</h3>";
                    echo "<h4 style='color:#aaa; margin-top:-10px;'>" . $viaje['destino_pais'] . "</h4>";
                    echo "<p><small>" . $viaje['tipo_viaje'] . " | " . $viaje['duracion'] . " días</small></p>";
                    echo "<p>" . substr($viaje['descripcion_detallada'], 0, 100) . "...</p>";
                    echo "</div>";
                    echo "<div>";
                    echo "<p style='font-size:1.2em; font-weight:bold; color: #4CAF50;'>" . $viaje['precio_base'] . " €</p>";
                    echo "<a href='detalles_viaje.php?id=" . $viaje['id_paquete'] . "' class='button' style='display:block; text-align:center;'>Ver Detalles</a>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</body>

</html>