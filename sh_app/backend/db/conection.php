<?php
    $bdServerName = "sql103.infinityfree.com";
    $bdUsername = "if0_37449711";
    $bdPassword = "G2FsF6VqGhQ";
    $bdDatabase = "if0_37449711_sh_app";

    // Crear conexión
    $conn = new mysqli($bdServerName, $bdUsername, $bdPassword, $bdDatabase);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Habilitar el modo de excepciones en MySQLi
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn->set_charset("utf8mb4"); // Opcional: establecer el juego de caracteres para evitar problemas con caracteres especiales
?>