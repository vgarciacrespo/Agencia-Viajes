<?php
session_start();
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Agencia de Viajes - Acceso</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php

        function pintar_login()
        {
            $formulario = <<<HTML
            <h1>Agencia de Viajes - Iniciar Sesión</h1>
            <form action="login.php" method="post">
                <p>Email: <input type="text" name="email"></p>
                <p>Contraseña: <input type="password" name="pwd"></p>
                <p>
                    <input type="submit" value="Entrar">
                    <a href="registro.php">¿No tienes cuenta? Regístrate</a>
                </p>
                <p><a href="index.php">Volver al inicio</a></p>
            </form>
HTML;
            print $formulario;
        }

        if (empty($_POST)) {
            pintar_login();
        } else {
            include 'conexion.php';

            $email = isset($_POST['email']) ? mysqli_real_escape_string($conex, $_POST['email']) : "";
            $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";

            $query = "SELECT dni, nombre, apellidos, password, tipo FROM usuarios WHERE email = '$email'";
            $res = mysqli_query($conex, $query) or die(mysqli_error($conex));

            if (mysqli_num_rows($res) > 0) {
                $fila = mysqli_fetch_array($res);
                if (password_verify($pwd, $fila['password'])) {
                    $_SESSION['usuario'] = $fila['nombre'];
                    $_SESSION['dni'] = $fila['dni'];
                    $_SESSION['tipo'] = $fila['tipo'];

                    if ($fila['tipo'] == 'admin') {
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    echo "<p style='color:var(--danger-color)'>Contraseña incorrecta</p>";
                    pintar_login();
                }
            } else {
                echo "<p style='color:var(--danger-color)'>Usuario no encontrado</p>";
                pintar_login();
            }
        }
        ?>
    </div>
</body>

</html>