<?php
session_start();
require_once '../../backend/db/conection.php';

function isMaintenanceMode($conn) {
    $stmt = $conn->prepare("SELECT valor FROM configuracion WHERE clave = 'maintenance_mode'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row && $row['valor'] == '1';
}

// control global de mantenimiento
$maintenance = isMaintenanceMode($conn);

$currentPage = basename($_SERVER['PHP_SELF']);

$allowedPages = [
    'login.php',
    'createLogin.php',
    'mantenimiento.php',
    'logout.php'
];

// verificar si es admin
$isAdmin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'Administrador';

if ($maintenance) {
    if (!$isAdmin && !in_array($currentPage, $allowedPages)) {
        header("Location: mantenimiento.php");
        exit();
    }
}


// control de accesos por roles
function checkAccess($requiredRole) {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    } elseif ($_SESSION['usuario_rol'] != $requiredRole) {
        if ($_SESSION['usuario_rol'] == 'Administrador') {
            header('Location: home.php');
        } elseif ($_SESSION['usuario_rol'] == 'Cliente') {
            header('Location: store.php');
        }
        exit();
    }
}
?>