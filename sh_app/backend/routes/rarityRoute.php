<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/rarityController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['color'])) {
                $nombre = $_POST['nombre'];
                $descripcion = $_POST['descripcion'];
                $color = $_POST['color'];

                $respuesta = insertar($conn, $nombre, $descripcion, $color);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['color'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $descripcion = $_POST['descripcion'];
                $color = $_POST['color'];

                $respuesta = actualizar($conn, $id, $nombre, $descripcion, $color);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar la categoría";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;
        
        case 'obtener':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $respuesta = obtener($conn, $nombre);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
        
        case 'buscar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscar($conn, $id);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'seleccionar':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $nombre, $limit, $offset);
            $total = contar($conn, $nombre);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;

        case 'contar':
            $total = contarTodos($conn);
        
            if ($total !== 0) {
                header('Content-Type: application/json');
                echo json_encode(['total' => $total]);
            } else {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No se pudo obtener el total']);
            }
            break;

        case 'listarIds':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';

            $orden = isset($_POST['orden']) ? $_POST['orden'] : '';
        
            $respuesta = listarIds($conn, $nombre, $orden);
            $total = contarIds($conn, $nombre);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;
    
        case 'buscarPorId':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscarPorId($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta]);
            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>