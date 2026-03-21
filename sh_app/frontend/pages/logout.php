<?php
    session_start();

    $redireccion = 'store.php';

    // Destruir todas las variables de sesión
    session_unset();

    // Destruir la sesión
    session_destroy();

    // Redirigir al login o a la página según el rol
    header('Location: ' . $redireccion);
    exit();
?>