<?php
session_start();
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Registro - Agencia de Viajes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php

        function pintar_registro($dni, $nombre, $apellidos, $email, $telefono, $preferencias)
        {
            $form = <<<HTML
                <h1>Registro de Nuevo Cliente</h1>
                <form action="registro.php" method="post">
                    <p>DNI: <input type="text" name="dni" value="$dni"></p>
                    <p>Nombre: <input type="text" name="nombre" value="$nombre"></p>
                    <p>Apellidos: <input type="text" name="apellidos" value="$apellidos"></p>
                    <p>Email: <input type="email" name="email" value="$email"></p>
                    <p>Teléfono: <input type="text" name="telefono" value="$telefono"></p>
                    <p>Preferencias: <textarea name="preferencias">$preferencias</textarea></p>
                    <p>Contraseña: <input type="password" name="pwd"></p>
                    <input type="submit" value="Registrarme">
                </form>
                <p><a href="login.php">Volver al Login</a></p>
HTML;
            print $form;
        }

        function validar(&$dni, &$nombre, &$apellidos, &$email, &$telefono, &$preferencias, &$pwd, &$errores)
        {
            $flag = true;
            // DNI: 8 números + 1 letra
            if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
                $errores .= " DNI inválido (8 números y 1 letra) / ";
                $flag = false;
            }
            // Nombre y Apellidos: solo letras y espacios
            if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                $errores .= " Nombre invalido (solo letras) / ";
                $flag = false;
            }
            if (!preg_match('/^[a-zA-Z\s]+$/', $apellidos)) {
                $errores .= " Apellidos invalidos (solo letras) / ";
                $flag = false;
            }
            // Email
            if ($email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores .= " Email inválido / ";
                $flag = false;
            }
            // Teléfono: 9 dígitos
            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                $errores .= " Teléfono inválido (9 dígitos) / ";
                $flag = false;
            }
            // Password
            if (strlen($pwd) < 4) {
                $errores .= " Contraseña muy corta / ";
                $flag = false;
            }
            return $flag;
        }

        if (empty($_POST)) {
            $dni = "";
            $nombre = "";
            $apellidos = "";
            $email = "";
            $telefono = "";
            $preferencias = "";
            pintar_registro($dni, $nombre, $apellidos, $email, $telefono, $preferencias);
        } else {
            include 'conexion.php';

            
            $dni = isset($_POST["dni"]) ? trim($_POST["dni"]) : "";
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
            $apellidos = isset($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "";
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
            $telefono = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : "";
            $preferencias = isset($_POST["preferencias"]) ? trim($_POST["preferencias"]) : "";
            $pwd = isset($_POST["pwd"]) ? $_POST["pwd"] : "";

            $errores = "";
            if (validar($dni, $nombre, $apellidos, $email, $telefono, $preferencias, $pwd, $errores)) {
              
                $safe_dni = mysqli_real_escape_string($conex, $dni);
                $safe_nombre = mysqli_real_escape_string($conex, $nombre);
                $safe_apellidos = mysqli_real_escape_string($conex, $apellidos);
                $safe_email = mysqli_real_escape_string($conex, $email);
                $safe_telefono = mysqli_real_escape_string($conex, $telefono);
                $safe_pref = mysqli_real_escape_string($conex, $preferencias);
                $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuarios (dni, nombre, apellidos, email, telefono, password, preferencias_usuario, tipo) 
                        VALUES ('$safe_dni', '$safe_nombre', '$safe_apellidos', '$safe_email', '$safe_telefono', '$hash_pwd', '$safe_pref', 'registrado')";

                if (mysqli_query($conex, $sql)) {
                    echo "<script>alert('Registro existoso'); window.location='index.php';</script>";
                } else {
                    echo "Error BD: " . mysqli_error($conex);
                    pintar_registro($dni, $nombre, $apellidos, $email, $telefono, $preferencias);
                }
            } else {
                echo "<p style='color:var(--danger-color)'>$errores</p>";
                pintar_registro($dni, $nombre, $apellidos, $email, $telefono, $preferencias);
            }
        }
        ?>
    </div>
</body>

</html>