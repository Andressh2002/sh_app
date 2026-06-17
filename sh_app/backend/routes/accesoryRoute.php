<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/accesoryController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['colores'])) {
                $nombre = $_POST['nombre'];
                $colores = $_POST['colores'];

                $respuesta = insertar($conn, $nombre, $colores);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['colores'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $colores = $_POST['colores'];

                $respuesta = actualizar($conn, $id, $nombre, $colores);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el accesorio";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;
        
        case 'obtener':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $productos = obtener($conn, $nombre);

            header('Content-Type: application/json');
            echo json_encode($productos);
            break;

        case 'listar':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $productos = listar($conn, $nombre);

            header('Content-Type: application/json');
            echo json_encode($productos);
            break;
        
        case 'buscar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $producto = buscar($conn, $id);

            header('Content-Type: application/json');
            echo json_encode($producto);
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

        case 'buscarImagen':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarImagen($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
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

        case 'insertarImagen':
            if (isset($_POST['id']) && isset($_POST['imagen']) && isset($_POST['idImagen'])) {
                $id = $_POST['id'];
                $imagen = $_POST['imagen'];
                $idImagen = $_POST['idImagen'];

                $respuesta = insertarImagen($conn, $id, $imagen, $idImagen);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'listarIds':
            $nombre = $_POST['nombre'] ?? '';

            $orden = $_POST['orden'] ?? [];
        
            $respuesta = listarIds($conn, $nombre, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
    
        case 'buscarPorId':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscarPorId($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>