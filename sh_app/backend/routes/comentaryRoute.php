<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/comentaryController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['idProducto']) && isset($_POST['idCliente']) && isset($_POST['mensaje'])) {
                $idProducto = $_POST['idProducto'];
                $idCliente = $_POST['idCliente'];
                $mensaje = $_POST['mensaje'];
                $estrellas = $_POST['estrellas'];

                $respuesta = insertar($conn, $idProducto, $idCliente, $mensaje, $estrellas);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['idProducto']) && isset($_POST['idCliente']) && isset($_POST['mensaje'])) {
                $id = $_POST['id'];
                $idProducto = $_POST['idProducto'];
                $idCliente = $_POST['idCliente'];
                $mensaje = $_POST['mensaje'];
                $estrellas = $_POST['estrellas'];

                $respuesta = actualizar($conn, $id, $idProducto, $idCliente, $mensaje, $estrellas);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el mensaje";
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
            $producto = isset($_POST['producto']) ? $_POST['producto'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $producto, $limit, $offset);
            $total = contar($conn, $producto);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;

        case 'seleccionarPorIdProducto':
            $idProducto = isset($_POST['idProducto']) ? $_POST['idProducto'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionarPorIdProducto($conn, $idProducto);
        
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

        case 'listarIds':
            $respuesta =
                listarIds($conn, $_POST['nombre'] ?? '', $_POST['orden'] ?? []);
            header(
                'Content-Type: application/json'
            );
            echo json_encode(
                $respuesta
            );
        break;

        case 'buscarPorId':
            $respuesta = buscarPorId($conn, $_POST['id'] ?? '');
            header(
                'Content-Type: application/json'
            );
            echo json_encode(
                $respuesta
            );

        break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>