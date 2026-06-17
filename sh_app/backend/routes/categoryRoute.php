<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/categoryController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['imagen'])) {
                $nombre = $_POST['nombre'];
                $imagen = $_POST['imagen'];

                $respuesta = insertar($conn, $nombre, $imagen);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['imagen'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $imagen = $_POST['imagen'];

                $respuesta = actualizar($conn, $id, $nombre, $imagen);
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
            $isImagen = isset($_POST['isImagen']) ? $_POST['isImagen'] : '';
            $respuesta = obtener($conn, $nombre, $isImagen);

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
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;  // Límite de categorías por página
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0; // Desplazamiento para la paginación
        
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

        case 'listarIds':
            $nombre = $_POST['nombre'] ?? '';

            $orden = $_POST['orden'] ?? [];
        
            $respuesta = listarIds($conn, $nombre, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
    
        case 'buscarPorId':
            $id = $_POST['id'] ?? '';
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