<?php

include '../db/conection.php';
include '../controllers/favoritesController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'toggle':
        $idCliente = $_POST['idCliente'] ?? null;
        $idProducto = $_POST['idProducto'] ?? null;

        $respuesta = toggleFavorito($conn, $idCliente, $idProducto);
        echo json_encode($respuesta);
        break;

    default:
        echo "Acción no válida";
        break;
}