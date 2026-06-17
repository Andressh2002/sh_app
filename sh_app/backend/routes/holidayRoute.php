<?php

include '../db/conection.php';
include '../controllers/holidayController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'insertar':

        if (
            isset($_POST['nombre']) &&
            isset($_POST['fecha_inicial']) &&
            isset($_POST['fecha_final'])
        ) {

            echo json_encode(
                insertar(
                    $conn,
                    $_POST['nombre'],
                    $_POST['fecha_inicial'],
                    $_POST['fecha_final']
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
            isset($_POST['fecha_inicial']) &&
            isset($_POST['fecha_final'])
        ) {

            echo json_encode(
                actualizar(
                    $conn,
                    $_POST['id'],
                    $_POST['nombre'],
                    $_POST['fecha_inicial'],
                    $_POST['fecha_final']
                )
            );

        } else {

            echo "Faltan datos para actualizar la festividad";
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
            $_POST['nombre'] ?? ''
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