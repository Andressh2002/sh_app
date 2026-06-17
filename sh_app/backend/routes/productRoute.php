<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/productController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['categoria']) && isset($_POST['precio']) && isset($_POST['imagen1']) && isset($_POST['imagen2']) && isset($_POST['descripcion']) && isset($_POST['altura']) && isset($_POST['descuentos']) && isset($_POST['peso']) && isset($_POST['festividad']) && isset($_POST['rareza']) && isset($_POST['universo']) && isset($_POST['advertencia']) && isset($_POST['comida']) && isset($_POST['existencia'])) {
                $nombre = $_POST['nombre'];
                $categoria = $_POST['categoria'];
                $colores = $_POST['colores'];
                $precio = $_POST['precio'];
                $imagen1 = $_POST['imagen1'];
                $imagen2 = $_POST['imagen2'];
                $descripcion = $_POST['descripcion'];
                $altura = $_POST['altura'];
                $peso = $_POST['peso'];
                $festividad = $_POST['festividad'];
                $descuentos = $_POST['descuentos'];
                $rareza = $_POST['rareza'];
                $universo = $_POST['universo'];
                $accesorio = $_POST['accesorio'];
                $advertencia = $_POST['advertencia'];
                $tiempo = $_POST['tiempo'];
                $comida = $_POST['comida'];
                $existencia = $_POST['existencia'];

                $respuesta = insertar($conn, $nombre, $categoria, $colores, $precio, $imagen1, $imagen2, $descripcion, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['categoria']) && isset($_POST['precio']) && isset($_POST['imagen1']) && isset($_POST['imagen2']) && isset($_POST['descripcion']) && isset($_POST['altura']) && isset($_POST['descuentos']) && isset($_POST['peso']) && isset($_POST['festividad']) && isset($_POST['rareza']) && isset($_POST['universo']) && isset($_POST['advertencia']) && isset($_POST['comida']) && isset($_POST['existencia'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $categoria = $_POST['categoria'];
                $colores = $_POST['colores'];
                $precio = $_POST['precio'];
                $imagen1 = $_POST['imagen1'];
                $imagen2 = $_POST['imagen2'];
                $descripcion = $_POST['descripcion'];
                $altura = $_POST['altura'];
                $peso = $_POST['peso'];
                $festividad = $_POST['festividad'];
                $descuentos = $_POST['descuentos'];
                $rareza = $_POST['rareza'];
                $universo = $_POST['universo'];
                $accesorio = $_POST['accesorio'];
                $advertencia = $_POST['advertencia'];
                $tiempo = $_POST['tiempo'];
                $comida = $_POST['comida'];
                $existencia = $_POST['existencia'];

                $respuesta = actualizar($conn, $id, $nombre, $categoria, $colores, $precio, $imagen1, $imagen2, $descripcion, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el producto";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;
        
        case 'obtener':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $productos = obtener($conn, $nombre, $categoria);

            header('Content-Type: application/json');
            echo json_encode($productos);
            break;

        case 'listar':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $productos = listar($conn, $nombre, $categoria);

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
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $rareza = isset($_POST['rareza']) ? $_POST['rareza'] : '';
            $universo = isset($_POST['universo']) ? $_POST['universo'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $nombre, $categoria, $rareza, $universo, $limit, $offset);
            $total = contar($conn, $nombre, $categoria, $rareza, $universo);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total, 'data' => [$categoria, $rareza, $universo]]);
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

        case 'cambiar':
            if (isset($_POST['id']) && isset($_POST['visible'])) {
                $id = $_POST['id'];
                $visible = $_POST['visible'];

                $respuesta = cambiarVisibilidad($conn, $id, $visible);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el producto";
            }
            break;

        case 'cambiarDestacacidad':
            if (isset($_POST['id']) && isset($_POST['isDestacacidad'])) {
                $id = $_POST['id'];
                $isDestacacidad = $_POST['isDestacacidad'];

                $respuesta = cambiarDestacacidad($conn, $id, $isDestacacidad);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el producto";
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
            $categoria = $_POST['categoria'] ?? '';
            $rareza = $_POST['rareza'] ?? '';
            $universo = $_POST['universo'] ?? '';

            $orden = $_POST['orden'] ?? [];
        
            $respuesta = listarIds($conn, $nombre, $categoria, $rareza, $universo, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarPorId':
            $id = $_POST['id'] ?? '';
            $respuesta = buscarPorId($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarCartaProducto':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $producto = buscarCartaProducto($conn, $id);

            header('Content-Type: application/json');
            echo json_encode($producto);
            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>