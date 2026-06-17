<?php

include '../db/conection.php';
include '../controllers/colorController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'insertar':

        if (
            isset($_POST['nombre']) &&
            isset($_POST['color1']) &&
            isset($_POST['color2']) &&
            isset($_POST['color3']) &&
            isset($_POST['familia'])
        ) {

            echo json_encode(
                insertar(
                    $conn,
                    $_POST['nombre'],
                    $_POST['color1'],
                    $_POST['color2'],
                    $_POST['color3'],
                    $_POST['familia']
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
            isset($_POST['color1']) &&
            isset($_POST['color2']) &&
            isset($_POST['color3']) &&
            isset($_POST['familia'])
        ) {

            echo json_encode(
                actualizar(
                    $conn,
                    $_POST['id'],
                    $_POST['nombre'],
                    $_POST['color1'],
                    $_POST['color2'],
                    $_POST['color3'],
                    $_POST['familia']
                )
            );

        } else {

            echo "Faltan datos para actualizar el color";
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
            $_POST['familia'] ?? ''
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
            $_POST['familia'] ?? '',
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