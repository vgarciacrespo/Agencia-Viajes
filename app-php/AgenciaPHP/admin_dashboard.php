<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo $_SESSION['usuario']; ?></p>

        <div class="admin-menu" style="display: flex; gap: 20px; justify-content: center; margin-top: 30px;">
            <a href="gestionar_paquetes.php" class="btn"
                style="padding: 20px; background: #f0f0f0; border-radius: 10px; text-decoration: none; color: #333; border: 1px solid #ccc; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="color: #333;">Gestionar Paquetes</h3>
                <p style="color: #555;">Añadir, modificar y eliminar</p>
            </a>

            <a href="admin_reservas.php" class="btn"
                style="padding: 20px; background: #f0f0f0; border-radius: 10px; text-decoration: none; color: #333; border: 1px solid #ccc; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="color: #333;">Mostrar Reservas</h3>
                <p style="color: #555;">Ver reservas de todos los usuarios</p>
            </a>
        </div>

        <br><br>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</body>

</html>