<?php
    session_start();

    function checkAccess($requiredRole) {
        if (!isset($_SESSION['usuario_id'])) {
            // Si no hay sesión iniciada, redirigir al login
            header('Location: login.php');
            exit();
        } elseif ($_SESSION['usuario_rol'] != $requiredRole) {
            // Si el rol no es el esperado, redirigir a la página correspondiente
            if ($_SESSION['usuario_rol'] == 'Administrador') {
                header('Location: home.php');
            } elseif ($_SESSION['usuario_rol'] == 'Cliente') {
                header('Location: store.php');
            }
            exit();
        }
    }
?>