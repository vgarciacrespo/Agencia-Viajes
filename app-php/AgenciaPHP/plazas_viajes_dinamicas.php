<?php


include __DIR__ . '/conexion.php';

echo "Iniciando migración de base de datos...\n";

// 1. Agregar columna plazas_disponibles a la tabla viajes si no existe
$check_col = "SHOW COLUMNS FROM viajes LIKE 'plazas_disponibles'";
$res_check = mysqli_query($conex, $check_col);

if (mysqli_num_rows($res_check) == 0) {
    echo "Agregando columna 'plazas_disponibles' a la tabla 'viajes'...\n";
    $sql_alter = "ALTER TABLE viajes ADD COLUMN plazas_disponibles INT DEFAULT 0";
    if (mysqli_query($conex, $sql_alter)) {
        echo "Columna agregada correctamente.\n";
    } else {
        die("Error al agregar columna: " . mysqli_error($conex) . "\n");
    }
} else {
    echo "La columna 'plazas_disponibles' ya existe.\n";
}

// 2. Inicializar plazas_disponibles con el valor de plazas del paquete asociado
echo "Actualizando plazas disponibles iniciales desde paquetes...\n";

$sql_update = "UPDATE viajes v
               JOIN paquetes_turisticos p ON v.id_paquete = p.id_paquete
               SET v.plazas_disponibles = p.plazas
               WHERE v.plazas_disponibles = 0 OR v.plazas_disponibles IS NULL";

if (mysqli_query($conex, $sql_update)) {
    echo "Actualización completada. Filas afectadas: " . mysqli_affected_rows($conex) . "\n";
} else {
    echo "Error al actualizar plazas: " . mysqli_error($conex) . "\n";
}

echo "Migración finalizada con éxito.\n";
?>