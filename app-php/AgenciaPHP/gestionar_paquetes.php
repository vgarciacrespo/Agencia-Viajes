<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

$msg = "";
$error = "";

// PROCESAR ELIMINACIÓN
if (isset($_GET['borrar'])) {
    $id_borrar = $_GET['borrar'];
    $sql_borrar = "DELETE FROM paquetes_turisticos WHERE id_paquete = '$id_borrar'";
    if (mysqli_query($conex, $sql_borrar)) {
        $msg = "Paquete eliminado correctamente.";
    } else {
        $error = "Error al eliminar: " . mysqli_error($conex);
    }
}



$modo = "lista";
$datos_editar = null;

if (isset($_GET['accion'])) {
    if ($_GET['accion'] == 'nuevo') {
        $modo = "formulario";
    } elseif ($_GET['accion'] == 'editar' && isset($_GET['id'])) {
        $modo = "formulario";
        $id_editar = $_GET['id'];
        $sql_load = "SELECT * FROM paquetes_turisticos WHERE id_paquete='$id_editar'";
        $res_load = mysqli_query($conex, $sql_load);
        $datos_editar = mysqli_fetch_array($res_load);
    }
}

// PROCESAR GUARDADO
if (isset($_POST['guardar'])) {
    $id_paquete = $_POST['id_paquete'];
    $destino_pais = $_POST['destino_pais'];
    $destino_ciudad = $_POST['destino_ciudad'];
    $tipo_viaje = $_POST['tipo_viaje'];
    $duracion = $_POST['duracion'];
    $descripcion = $_POST['descripcion'];
    $itinerario = $_POST['itinerario'];
    $plazas = $_POST['plazas'];
    $precio = $_POST['precio'];
    $id_proveedor = $_POST['id_proveedor'];

    $es_nuevo = $_POST['es_nuevo'] == '1';

    if ($es_nuevo) {
        // Verificar si ID existe
        $check = mysqli_query($conex, "SELECT id_paquete FROM paquetes_turisticos WHERE id_paquete='$id_paquete'");
        if (mysqli_num_rows($check) > 0) {
            $error = "El ID de paquete ya existe.";
            $modo = "formulario"; // volver a mostrar
        } else {
            $sql_insert = "INSERT INTO paquetes_turisticos (id_paquete, destino_pais, destino_ciudad, tipo_viaje, duracion, descripcion_detallada, itinerario, plazas, precio_base, id_proveedor) 
                           VALUES ('$id_paquete', '$destino_pais', '$destino_ciudad', '$tipo_viaje', $duracion, '$descripcion', '$itinerario', $plazas, $precio, '$id_proveedor')";
            if (mysqli_query($conex, $sql_insert)) {
                $msg = "Paquete creado correctamente.";
                $modo = "lista";
            } else {
                $error = "Error al crear: " . mysqli_error($conex);
                $modo = "formulario";
            }
        }
    } else {
        // Actualizar
        $sql_update = "UPDATE paquetes_turisticos SET 
                       destino_pais='$destino_pais', 
                       destino_ciudad='$destino_ciudad', 
                       tipo_viaje='$tipo_viaje', 
                       duracion=$duracion, 
                       descripcion_detallada='$descripcion', 
                       itinerario='$itinerario', 
                       plazas=$plazas, 
                       precio_base=$precio, 
                       id_proveedor='$id_proveedor' 
                       WHERE id_paquete='$id_paquete'";
        if (mysqli_query($conex, $sql_update)) {
            $msg = "Paquete actualizado correctamente.";
            $modo = "lista";
        } else {
            $error = "Error al actualizar: " . mysqli_error($conex);
            $modo = "formulario";
        }
    }
}

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Gestionar Paquetes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Gestionar Paquetes</h1>

        <?php if ($msg)
            echo "<p style='color:green'>$msg</p>"; ?>
        <?php if ($error)
            echo "<p style='color:red'>$error</p>"; ?>

        <?php if ($modo == 'lista'): ?>
            <a href="gestionar_paquetes.php?accion=nuevo" class="btn">Añadir Nuevo Paquete</a>
            <br><br>
            <table border="1" width="100%">
                <tr>
                    <th>ID</th>
                    <th>Ciudad / Pais</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                <?php
                $res = mysqli_query($conex, "SELECT * FROM paquetes_turisticos");
                while ($fila = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>" . $fila['id_paquete'] . "</td>";
                    echo "<td>" . $fila['destino_ciudad'] . " (" . $fila['destino_pais'] . ")</td>";
                    echo "<td>" . $fila['tipo_viaje'] . "</td>";
                    echo "<td>" . $fila['precio_base'] . " €</td>";
                    echo "<td>
                            <a href='gestionar_paquetes.php?accion=editar&id=" . $fila['id_paquete'] . "'>Editar</a> | 
                            <a href='gestionar_paquetes.php?borrar=" . $fila['id_paquete'] . "' onclick='return confirm(\"¿Seguro?\")' style='color:red'>Borrar</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <br>
            <a href="admin_dashboard.php">Volver al Panel</a>

        <?php elseif ($modo == 'formulario'): ?>
            <?php
            // Valores por defecto
            $id = $datos_editar ? $datos_editar['id_paquete'] : "";
            $pais = $datos_editar ? $datos_editar['destino_pais'] : "";
            $ciudad = $datos_editar ? $datos_editar['destino_ciudad'] : "";
            $tipo = $datos_editar ? $datos_editar['tipo_viaje'] : "";
            $duracion = $datos_editar ? $datos_editar['duracion'] : "";
            $desc = $datos_editar ? $datos_editar['descripcion_detallada'] : "";
            $itinerario = $datos_editar ? $datos_editar['itinerario'] : "";
            $plazas = $datos_editar ? $datos_editar['plazas'] : "";
            $precio = $datos_editar ? $datos_editar['precio_base'] : "";
            $prov = $datos_editar ? $datos_editar['id_proveedor'] : "";

            $es_nuevo = ($datos_editar == null);
            ?>

            <h3><?php echo $es_nuevo ? "Nuevo Paquete" : "Editar Paquete"; ?></h3>
            <form action="gestionar_paquetes.php" method="post">
                <input type="hidden" name="es_nuevo" value="<?php echo $es_nuevo ? '1' : '0'; ?>">

                <p>ID Paquete: <input type="text" name="id_paquete" value="<?php echo $id; ?>" <?php echo $es_nuevo ? '' : 'readonly'; ?> required></p>
                <p>País: <input type="text" name="destino_pais" value="<?php echo $pais; ?>" required></p>
                <p>Ciudad: <input type="text" name="destino_ciudad" value="<?php echo $ciudad; ?>" required></p>
                <p>Tipo: <input type="text" name="tipo_viaje" value="<?php echo $tipo; ?>" required></p>
                <p>Duración (días): <input type="number" name="duracion" value="<?php echo $duracion; ?>" required></p>
                <p>Descripción: <textarea name="descripcion" required><?php echo $desc; ?></textarea></p>
                <p>Itinerario: <textarea name="itinerario" required><?php echo $itinerario; ?></textarea></p>
                <p>Plazas: <input type="number" name="plazas" value="<?php echo $plazas; ?>" required></p>
                <p>Precio Base: <input type="number" step="0.01" name="precio" value="<?php echo $precio; ?>" required></p>
                <p>ID Proveedor:
                    <select name="id_proveedor" required>
                        <?php
                        $provs = mysqli_query($conex, "SELECT id_proveedor, nombre_proveedor FROM proveedores");
                        while ($p = mysqli_fetch_array($provs)) {
                            $selected = ($prov == $p['id_proveedor']) ? "selected" : "";
                            echo "<option value='" . $p['id_proveedor'] . "' $selected>" . $p['nombre_proveedor'] . "</option>";
                        }
                        ?>
                    </select>
                </p>

                <input type="submit" name="guardar" value="Guardar">
            </form>
            <br>
            <a href="gestionar_paquetes.php">Volver al listado</a>
        <?php endif; ?>
    </div>
</body>

</html>