<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$dni_usuario = $_SESSION['dni'];
$msg = "";
$error = "";

// PROCESAR FORMULARIO DE ACTUALIZACIÓN
if (isset($_POST['actualizar'])) {
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_apellidos = $_POST['apellidos'];
    $nuevo_email = $_POST['email'];
    $nuevo_telefono = $_POST['telefono'];
    $nuevas_preferencias = $_POST['preferencias'];
  

    // Validación básica
    if ($nuevo_nombre == "" || $nuevo_email == "" || $nuevo_apellidos == "") {
        $error = "Nombre, Apellidos y Email son obligatorios.";
    } else {
        $sql_update = "UPDATE usuarios SET 
                        nombre='$nuevo_nombre', 
                        apellidos='$nuevo_apellidos', 
                        email='$nuevo_email', 
                        telefono='$nuevo_telefono', 
                        preferencias_usuario='$nuevas_preferencias' 
                        WHERE dni='$dni_usuario'";
        if (mysqli_query($conex, $sql_update)) {
            $msg = "Datos actualizados correctamente.";
            $_SESSION['usuario'] = $nuevo_nombre;
        } else {
            $error = "Error al actualizar: " . mysqli_error($conex);
        }
    }
}

// OBTENER DATOS ACTUALES
$sql = "SELECT * FROM usuarios WHERE dni = '$dni_usuario'";
$res = mysqli_query($conex, $sql);
$datos = mysqli_fetch_array($res);

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Mi Perfil</h1>

        <?php if ($msg != ""): ?>
            <p style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;"><?php echo $msg; ?></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;"><?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form action="perfil.php" method="post">
            <p>DNI: <strong><?php echo $datos['dni']; ?></strong> (No modificable)</p>

            <p>Nombre: <br>
                <input type="text" name="nombre" value="<?php echo $datos['nombre']; ?>" required>
            </p>

            <p>Apellidos: <br>
                <input type="text" name="apellidos" value="<?php echo $datos['apellidos']; ?>" required>
            </p>

            <p>Email: <br>
                <input type="email" name="email" value="<?php echo $datos['email']; ?>" required>
            </p>

            <p>Teléfono: <br>
                <input type="text" name="telefono" value="<?php echo $datos['telefono']; ?>">
            </p>

            <p>Preferencias: <br>
                <textarea name="preferencias" rows="4"
                    cols="50"><?php echo $datos['preferencias_usuario']; ?></textarea>
            </p>

            <input type="submit" name="actualizar" value="Guardar Cambios">
        </form>

        <a href="index.php">Volver al inicio</a>
    </div>
</body>

</html>