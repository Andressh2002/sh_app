<?php

include '../db/conection.php';
include '../controllers/universeController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'insertar':

        if (
            isset($_POST['nombre']) &&
            isset($_POST['imagen'])
        ) {

            echo json_encode(
                insertar(
                    $conn,
                    $_POST['nombre'],
                    $_POST['imagen']
                )
            );

        } else {

            echo "Faltan datos";
        }

    break;

    case 'actualizar':

        if (
            isset($_POST['id']) &&
            isset($_POST['nombre']) &&
            isset($_POST['imagen'])
        ) {

            echo json_encode(
                actualizar(
                    $conn,
                    $_POST['id'],
                    $_POST['nombre'],
                    $_POST['imagen']
                )
            );

        } else {

            echo "Faltan datos para actualizar el universo";
        }

    break;

    case 'eliminar':

        echo eliminar(
            $conn,
            $_POST['id'] ?? ''
        );

    break;

    case 'obtener':

        $respuesta = obtener(
            $conn,
            $_POST['nombre'] ?? '',
            $_POST['isImagen'] ?? ''
        );

        header('Content-Type: application/json');

        echo json_encode($respuesta);

    break;

    case 'buscar':

        $respuesta = buscar(
            $conn,
            $_POST['id'] ?? ''
        );

        header('Content-Type: application/json');

        echo json_encode($respuesta);

    break;

    case 'buscarImagen':

        $respuesta = buscarImagen(
            $conn,
            $_POST['id'] ?? ''
        );

        header('Content-Type: application/json');

        echo json_encode($respuesta);

    break;

    case 'listarIds':

        $respuesta = listarIds(
            $conn,
            $_POST['nombre'] ?? '',
            $_POST['orden'] ?? []
        );

        header('Content-Type: application/json');

        echo json_encode($respuesta);

    break;

    case 'buscarPorId':

        $respuesta = buscarPorId(
            $conn,
            $_POST['id'] ?? ''
        );

        header('Content-Type: application/json');

        echo json_encode($respuesta);

    break;

    default:

        echo 'ERROR, falta una acción';

    break;
}

$conn->close();