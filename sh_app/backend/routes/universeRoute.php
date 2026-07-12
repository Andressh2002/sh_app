<?php

include '../db/conection.php';
include '../controllers/universeController.php';

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'insertar':
        echo json_encode(
            insertar(
                $conn,
                $_POST['nombre'] ?? ''
            )
        );
        break;

    case 'actualizar':
        echo json_encode(
            actualizar(
                $conn,
                $_POST['id'] ?? '',
                $_POST['nombre'] ?? ''
            )
        );
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

    case 'buscarLogo':
        $respuesta = buscarLogo(
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

    case 'insertarImagen':

        if(
            isset($_POST['id']) &&
            isset($_POST['imagen']) &&
            isset($_POST['campo'])
        ){

            $respuesta = insertarImagen(
                $conn,
                $_POST['id'],
                $_POST['imagen'],
                $_POST['campo']
            );

            echo json_encode($respuesta);

        }else{

            echo json_encode([
                "title"=>"Error",
                "text"=>"Faltan datos",
                "icon"=>"bi bi-x-circle"
            ]);

        }

        break;

    default:

        echo 'ERROR, falta una acción';

    break;
}

$conn->close();