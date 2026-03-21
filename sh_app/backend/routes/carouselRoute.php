<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/carouselController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['titulo']) && isset($_POST['texto']) && isset($_POST['imagen'])) {
                $festividad = $_POST['idFestividad'];
                $titulo = $_POST['titulo'];
                $texto = $_POST['texto'];
                $imagen = $_POST['imagen'];

                $respuesta = insertar($conn, $festividad, $titulo, $texto, $imagen);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['titulo']) && isset($_POST['texto']) && isset($_POST['imagen'])) {
                $id = $_POST['id'];
                $festividad = $_POST['idFestividad'];
                $titulo = $_POST['titulo'];
                $texto = $_POST['texto'];
                $imagen = $_POST['imagen'];

                $respuesta = actualizar($conn, $id, $festividad, $titulo, $texto, $imagen);
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
            $respuesta = obtener($conn);
        
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
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $festividad = isset($_POST['festividad']) ? $_POST['festividad'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $titulo, $festividad, $limit, $offset);
            $total = contar($conn, $titulo, $festividad,);
        
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
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>