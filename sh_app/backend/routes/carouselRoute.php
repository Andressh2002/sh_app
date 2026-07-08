<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/carouselController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'guardar':

        $slides = json_decode(
            $_POST['slides'] ?? '[]',
            true
        );

        $respuesta = guardar(
            $conn,
            $slides
        );

        echo json_encode($respuesta);

        break;

        case 'buscarImagen':
            $respuesta = buscarImagen($conn, $_POST['id'] ?? '');
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'listarIds':
            $respuesta = listarIds($conn);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
    
        case 'buscarPorId':
            $respuesta = buscarPorId($conn, $_POST['id'] ?? '');
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'listar':
            $respuesta = listar($conn);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
        
        case 'insertarImagen':
            $respuesta = insertarImagen(
                $conn,
                $_POST['id'] ?? 0,
                $_POST['imagen'] ?? ''
            );

            echo json_encode($respuesta);
            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>