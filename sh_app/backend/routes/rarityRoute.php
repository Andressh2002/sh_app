<?php

include '../db/conection.php';
include '../controllers/rarityController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'insertar':

        if (
            isset($_POST['nombre']) &&
            isset($_POST['descripcion']) &&
            isset($_POST['color'])
        ) {

            echo json_encode(
                insertar(
                    $conn,
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $_POST['color']
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
            isset($_POST['descripcion']) &&
            isset($_POST['color'])
        ) {

            echo json_encode(
                actualizar(
                    $conn,
                    $_POST['id'],
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $_POST['color']
                )
            );

        } else {

            echo "Faltan datos para actualizar la rareza";
        }

    break;

    case 'eliminar':

        echo eliminar(
            $conn,
            $_POST['id'] ?? ''
        );

    break;

    case 'obtener':

        header('Content-Type: application/json');

        echo json_encode(
            obtener(
                $conn,
                $_POST['nombre'] ?? ''
            )
        );

    break;

    case 'buscar':

        header('Content-Type: application/json');

        echo json_encode(
            buscar(
                $conn,
                $_POST['id'] ?? ''
            )
        );

    break;

    case 'listarIds':

        header('Content-Type: application/json');

        echo json_encode(
            listarIds(
                $conn,
                $_POST['nombre'] ?? '',
                $_POST['orden'] ?? []
            )
        );

    break;

    case 'buscarPorId':

        header('Content-Type: application/json');

        echo json_encode(
            buscarPorId(
                $conn,
                $_POST['id'] ?? ''
            )
        );

    break;

    default:

        echo 'ERROR, falta una acción';

    break;
}

$conn->close();