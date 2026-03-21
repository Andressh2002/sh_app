<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/newsController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['titulo']) && isset($_POST['mensaje'])) {
                $titulo = $_POST['titulo'];
                $mensaje = $_POST['mensaje'];
                $imagen = $_POST['imagen'];

                $respuesta = insertar($conn, $titulo, $mensaje, $imagen);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['titulo']) && isset($_POST['mensaje'])) {
                $id = $_POST['id'];
                $titulo = $_POST['titulo'];
                $mensaje = $_POST['mensaje'];
                $imagen = $_POST['imagen'];

                $respuesta = actualizar($conn, $id, $titulo, $mensaje, $imagen);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el aviso";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;
        
        case 'buscar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscar($conn, $id);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'seleccionar':
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $titulo, $limit, $offset);
            $total = contar($conn, $titulo);
        
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
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';

            $orden = isset($_POST['orden']) ? $_POST['orden'] : '';
        
            $respuesta = listarIds($conn, $titulo, $orden);
            $total = contarIds($conn, $titulo);
        
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