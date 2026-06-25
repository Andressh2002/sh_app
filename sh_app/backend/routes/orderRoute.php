<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/orderController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (
                isset($_POST['cliente']) &&
                isset($_POST['producto']) &&
                isset($_POST['color']) &&
                isset($_POST['cantidad']) &&
                isset($_POST['total']) &&
                isset($_POST['fichasUsadas']) &&
                isset($_POST['fichasGanadas'])
            ) {

                $respuesta =
                    insertar(
                        $conn,
                        $_POST['cliente'],
                        $_POST['producto'],
                        $_POST['color'],
                        $_POST['cantidad'],
                        $_POST['total'],
                        $_POST['colorAccesorio'] ?? 0,
                        $_POST['precio'] ?? 0,
                        $_POST['fichasUsadas'],
                        $_POST['fichasGanadas']
                    );

                echo json_encode(
                    $respuesta
                );

            } else {

                echo json_encode([
                    'title'=>'¡Error!',
                    'text'=>'Faltan datos.',
                    'icon'=>'bi bi-x-circle'
                ]);
            }

        break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['cliente']) && isset($_POST['producto']) && isset($_POST['color']) && isset($_POST['cantidad']) && isset($_POST['total'])) {
                $id = $_POST['id'];
                $cliente = $_POST['cliente'];
                $producto = $_POST['producto'];
                $color = $_POST['color'];
                $cantidad = $_POST['cantidad'];
                $total = $_POST['total'];

                $respuesta = actualizar($conn, $id, $cliente, $producto, $color, $cantidad, $total);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el pedido";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo $respuesta;
            break;

        case 'quitar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = quitar($conn, $id);

            echo json_encode($respuesta);
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
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : '';
            $producto = isset($_POST['producto']) ? $_POST['producto'] : '';
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $rareza = isset($_POST['rareza']) ? $_POST['rareza'] : '';
            $universo = isset($_POST['universo']) ? $_POST['universo'] : '';
            $color = isset($_POST['color']) ? $_POST['color'] : '';
            $pagado = isset($_POST['pagado']) ? $_POST['pagado'] : '';
            $ubicacion = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $ubicacion, $telefono, $limit, $offset);
            $total = contar($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $ubicacion, $telefono);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;

        case 'buscarImagen':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $idColor = isset($_POST['idColor']) ? $_POST['idColor'] : '';
            $respuesta = buscarImagen($conn, $id, $idColor);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarImagenAccesorio':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $idColor = isset($_POST['idColor']) ? $_POST['idColor'] : '';
            $respuesta = buscarImagenAccesorio($conn, $id, $idColor);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'seleccionarPedidosCliente':
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : '';
            $producto = isset($_POST['producto']) ? $_POST['producto'] : '';
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $rareza = isset($_POST['rareza']) ? $_POST['rareza'] : '';
            $universo = isset($_POST['universo']) ? $_POST['universo'] : '';
            $color = isset($_POST['color']) ? $_POST['color'] : '';
            $pagado = isset($_POST['pagado']) ? $_POST['pagado'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionarPedidosCliente($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $limit, $offset);
            $total = contarPedidos($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;

        case 'pagar':
            if (isset($_POST['id']) && isset($_POST['producto']) && isset($_POST['cantidad'])) {
                $id = $_POST['id'];
                $producto = $_POST['producto'];
                $cantidad = $_POST['cantidad'];

                $respuesta = pagar($conn, $id, $producto, $cantidad);
                echo $respuesta;
            } else {
                echo "Faltan datos para pagar el pedido";
            }
            break;

        case 'insertarSinUsuario':
            if (isset($_POST['clienteNombre']) && isset($_POST['clienteSegundoNombre']) && isset($_POST['clientePrimerApellido']) && isset($_POST['clienteSegundoApellido']) && isset($_POST['clienteProvincia']) && isset($_POST['clienteCanton']) && isset($_POST['clienteDistrito']) && isset($_POST['clienteTelefono']) && isset($_POST['producto']) && isset($_POST['color']) && isset($_POST['cantidad']) && isset($_POST['total'])) {
                $clienteNombre = $_POST['clienteNombre'];
                $clienteSegundoNombre = $_POST['clienteSegundoNombre'];
                $clientePrimerApellido = $_POST['clientePrimerApellido'];
                $clienteSegundoApellido = $_POST['clienteSegundoApellido'];
                $clienteProvincia = $_POST['clienteProvincia'];
                $clienteCanton = $_POST['clienteCanton'];
                $clienteDistrito = $_POST['clienteDistrito'];
                $clienteTelefono = $_POST['clienteTelefono'];
                
                $producto = $_POST['producto'];
                $color = $_POST['color'];
                $colorAccesorio = $_POST['colorAccesorio'];
                $cantidad = $_POST['cantidad'];
                $total = $_POST['total'];

                $respuesta = insertarSinUsuario($conn, $clienteNombre, $clienteSegundoNombre, $clientePrimerApellido, $clienteSegundoApellido, $clienteProvincia, $clienteCanton, $clienteDistrito, $clienteTelefono, $producto, $color, $cantidad, $total, $colorAccesorio);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;
        
        case 'listarIds':
            $cliente = $_POST['cliente'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $producto = $_POST['producto'] ?? '';
            $categoria = $_POST['categoria'] ?? '';
            $rareza = $_POST['rareza'] ?? '';
            $universo = $_POST['universo'] ?? '';
            $color = $_POST['color'] ?? '';
            $pagado = $_POST['pagado'] ?? '';

            $orden = $_POST['orden'] ?? [];
        
            $respuesta = listarIds($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $ubicacion, $telefono, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
    
        case 'buscarPorId':
            $id = $_POST['id'] ?? '';

            $respuesta = buscarPorId($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'listarIdsCliente':
            $cliente = $_POST['cliente'] ?? '';
            $producto = $_POST['producto'] ?? '';
            $categoria = $_POST['categoria'] ?? '';
            $rareza = $_POST['rareza'] ?? '';
            $universo = $_POST['universo'] ?? '';
            $color = $_POST['color'] ?? '';
            $pagado = $_POST['pagado'] ?? '';
            $orden = $_POST['orden'] ?? 'pe.id';

            $respuesta = listarIdsCliente(
                $conn,
                $cliente,
                $producto,
                $categoria,
                $rareza,
                $universo,
                $color,
                $pagado,
                $orden
            );

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarPorIdCliente':
            $id = $_POST['id'] ?? 0;
            $respuesta = buscarPorIdCliente($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'actualizarProgresoPedido':
            $id = $_POST['id'] ?? '';
            $progreso = $_POST['progreso'] ?? 0;

            $respuesta = actualizarProgresoPedido($conn, $id, $progreso);
            echo $respuesta;
            break;

        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>