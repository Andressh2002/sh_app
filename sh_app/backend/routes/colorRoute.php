<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/colorController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['color1']) && isset($_POST['color2']) && isset($_POST['color3']) && isset($_POST['familia'])) {
                $nombre = $_POST['nombre'];
                $descripcion = $_POST['descripcion'];
                $color1 = $_POST['color1'];
                $color2 = $_POST['color2'];
                $color3 = $_POST['color3'];
                $familia = $_POST['familia'];

                $respuesta = insertar($conn, $nombre, $descripcion, $color1, $color2, $color3, $familia);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['color1']) && isset($_POST['color2']) && isset($_POST['color3']) && isset($_POST['familia'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $descripcion = $_POST['descripcion'];
                $color1 = $_POST['color1'];
                $color2 = $_POST['color2'];
                $color3 = $_POST['color3'];
                $familia = $_POST['familia'];

                $respuesta = actualizar($conn, $id, $nombre, $descripcion, $color1, $color2, $color3, $familia);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el color";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;
        
        case 'obtener':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $familia = isset($_POST['familia']) ? $_POST['familia'] : '';
            $respuesta = obtener($conn, $nombre, $familia);

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
            $familia = isset($_POST['familia']) ? $_POST['familia'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $nombre, $familia, $limit, $offset);
            $total = contar($conn, $nombre, $familia);
        
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
            $familia = isset($_POST['familia']) ? $_POST['familia'] : '';

            $orden = isset($_POST['orden']) ? $_POST['orden'] : '';
        
            $respuesta = listarIds($conn, $nombre, $familia, $orden);
            $total = contarIds($conn, $nombre, $familia);
        
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