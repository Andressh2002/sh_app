<?php
    include '../security/encrypt.php';

    function insertar($conn, $idCliente, $idProducto, $idColor, $cantidad, $total, $colorAccesorio, $precio) {
        if (!$idCliente || trim($idCliente) == '') {
            return [
                'title' => "¡Error!",
                'text' => "El cliente es inválido.",
                'icon' => "error"
            ];
        }
    
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
        $fecha_actual = date('m-d'); // Formato para comparar con los descuentos
    
        try {
            // Obtener el precio del producto y los IDs de descuentos
            $queryProducto = "SELECT precio, idDescuentos FROM productos WHERE id = ?";
            $stmtProd = $conn->prepare($queryProducto);
            $stmtProd->bind_param("s", $idProducto);
            $stmtProd->execute();
            $resultProd = $stmtProd->get_result();
    
            if ($resultProd->num_rows == 0) {
                return [
                    'title' => "¡Error!",
                    'text' => "El producto no existe.",
                    'icon' => "error"
                ];
            }
    
            $producto = $resultProd->fetch_assoc();
            $precioBase = (float)$producto['precio'];
            $idDescuentosStr = $producto['idDescuentos'];
    
            // Si no hay descuentos, calcular el total normal
            if (empty($idDescuentosStr)) {
                $precioFinal = $precioBase;
                $totalCalculado = $precioFinal * (int)$cantidad;
            } else {
                // Obtener los descuentos aplicables
                $idsDescuentos = explode(',', $idDescuentosStr);
                $placeholders = implode(',', array_fill(0, count($idsDescuentos), '?'));
                
                $queryDescuentos = "SELECT id, fecha_inicial, fecha_final, descuento 
                                    FROM descuentos 
                                    WHERE id IN ($placeholders)";
                $stmtDesc = $conn->prepare($queryDescuentos);
                
                $types = str_repeat('i', count($idsDescuentos));
                $stmtDesc->bind_param($types, ...$idsDescuentos);
                $stmtDesc->execute();
                $resultDesc = $stmtDesc->get_result();

                // Verificar si hay resultados
                if ($resultDesc->num_rows == 0) {
                    var_dump("No se encontraron descuentos para los IDs:", $idsDescuentos);
                    exit;
                }
    
                // Encontrar el mejor descuento aplicable
                $mejorDescuento = 0;
                $tieneDescuentos = false;  // Variable para saber si encontró al menos un descuento

                while ($descuento = $resultDesc->fetch_assoc()) {
                    $tieneDescuentos = true; // Se encontró al menos un descuento

                    $anioActual = date('Y');
                    $fechaInicioFormateada = $anioActual . '-' . $descuento['fecha_inicial'];
                    $fechaFinFormateada = $anioActual . '-' . $descuento['fecha_final'];

                    $fechaInicioConvertida = date('m-d', strtotime($fechaInicioFormateada));
                    $fechaFinConvertida = date('m-d', strtotime($fechaFinFormateada));

                    if ($fecha_actual >= $fechaInicioConvertida && $fecha_actual <= $fechaFinConvertida) {
                        $mejorDescuento = max($mejorDescuento, (int)$descuento['descuento']);
                    }
                }

                // Si no se encontró ningún descuento, aseguramos que el valor no sea NULL
                if (!$tieneDescuentos) {
                    $mejorDescuento = 0;
                }
    
                // Aplicar el mejor descuento encontrado
                $precioFinal = $precioBase * (1 - ($mejorDescuento / 100));
                $totalCalculado = $precioFinal * (int)$cantidad;
            }
    
            // Validar si el total enviado es correcto
            if ((float)$total != round($totalCalculado, 2)) {
                return [
                    'title' => "¡Error!",
                    'text' => "El total enviado no es válido.",
                    'icon' => "error"
                ];
            }
    
            // Insertar en la tabla 'pedidos'
            $query1 = "INSERT INTO pedidos (idCliente, idProducto, idColor, cantidad, total, fecha_registro, estado, idColorAccesorio, precio) 
                       VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)";
    
            $stmt = $conn->prepare($query1);
            $stmt->bind_param("ssssssss", $idCliente, $idProducto, $idColor, $cantidad, $total, $fecha_registro, $colorAccesorio, $precioFinal);
    
            if ($stmt->execute()) {
                // Actualizar la cantidad de pedidos en la tabla 'productos'
                $query2 = "UPDATE productos SET pedidos = pedidos + ? WHERE id = ?";
                $stmt2 = $conn->prepare($query2);
                $stmt2->bind_param("is", $cantidad, $idProducto);
    
                if ($stmt2->execute()) {
                    return [
                        'title' => "¡Guardado!",
                        'text' => "El pedido se ha guardado correctamente.",
                        'icon' => "success"
                    ];
                } else {
                    return [
                        'title' => "¡Error!",
                        'text' => "Error al actualizar el producto: " . $conn->error,
                        'icon' => "error"
                    ];
                }
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function obtener($conn) {
        $query = "SELECT * FROM pedidos WHERE 1=1";
    
        $result = $conn->query($query);
    
        $pedidos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
    
        return $pedidos;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $idCliente, $idProducto, $idColor, $cantidad, $total) {
        try {
            $queryUpdate = "UPDATE pedidos SET 
                            idCliente = ?, 
                            idProducto = ?, 
                            idColor = ?, 
                            cantidad = ?, 
                            total = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssssi", $idCliente, $idProducto, $idColor, $cantidad, $total, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El pedido se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el pedido: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE pedidos SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El pedido se ha eliminado";
        } else {
            return "Error al eliminar el pedido: " . $conn->error;
        }
    }

    function seleccionar($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $ubicacion, $telefono, $limit, $offset) {
        $query = "SELECT pe.*, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono,
            pr.nombre AS producto, 
            pr.precio AS precio, 
            ca.nombre AS categoria, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }
    
        $query .= " ORDER BY pe.fecha_registro DESC";
        $query .= " LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar los datos
                $row['cliente'] = decryptData($row['cliente']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                // Concatenar todos los campos de nombre para facilitar la búsqueda
                $nombreCompleto = $row['cliente'] . " " . 
                                  $row['segundo_nombre'] . " " . 
                                  $row['primer_apellido'] . " " . 
                                  $row['segundo_apellido'];

                $ubicacionCompleta = $row['provincia'] . " " . 
                                  $row['canton'] . " " . 
                                  $row['distrito'];
                                  
                // Filtrar resultados por nombre completo del cliente después de desencriptar
                if (!empty($cliente) && stripos($nombreCompleto, $cliente) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }

                if (!empty($ubicacion) && stripos($ubicacionCompleta, $ubicacion) === false) {
                    continue;
                }

                if (!empty($telefono) && stripos($row['telefono'], $telefono) === false) {
                    continue;
                }
                
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function buscarImagen($conn, $id, $idColor) {
        $query = "SELECT 
            pr.imagen_color" . $conn->real_escape_string($idColor) . " AS imagen
        FROM pedidos pe 
        JOIN productos pr ON pe.idProducto = pr.id
        WHERE pe.estado=1";
        
        $query .= " AND pe.id = " . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function buscarImagenAccesorio($conn, $id, $idColor) {
        $query = "SELECT 
            ac.imagen_color" . $conn->real_escape_string($idColor) . " AS imagen
        FROM pedidos pe 
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        WHERE pe.estado=1";
        
        $query .= " AND pe.id = " . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function seleccionarPedidosCliente($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $limit, $offset) {
        $query = "SELECT pe.*, 
            cl.nombre AS cliente, 
            pr.nombre AS producto, 
            pr.precio AS precioProducto, 
            ca.nombre AS categoria, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores,
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        WHERE pe.estado=1";

        if ($cliente !== null && $cliente !== '') {
            $query .= " AND cl.id LIKE '%" . $conn->real_escape_string($cliente) . "%'";
        }
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }

        $query .= " ORDER BY pe.id DESC";
    
        $query .= " LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        
        $pedidos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }
    
    function contar($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $ubicacion, $telefono) {
        // Consulta inicial
        $query = "SELECT pe.*, cl.nombre AS cliente, 
                  pr.nombre AS producto,
                  cl.segundo_nombre AS segundo_nombre,
                  cl.primer_apellido AS primer_apellido,
                  cl.segundo_apellido AS segundo_apellido,
                  cl.provincia AS provincia,
                  cl.canton AS canton,
                  cl.distrito AS distrito,
                  cl.telefono AS telefono
                  FROM pedidos pe
                  JOIN usuarios cl ON pe.idCliente = cl.id
                  JOIN productos pr ON pe.idProducto = pr.id
                  JOIN categorias ca ON pr.idCategoria = ca.id
                  LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
                  LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
                  LEFT JOIN colores co ON pe.idColor = co.id 
                  WHERE pe.estado=1";
    
        // Filtros directos para campos no encriptados
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }
    
        // Ejecutar la consulta
        $result = $conn->query($query);
        $total = 0;
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
    
                // Desencriptar los datos
                $row['cliente'] = decryptData($row['cliente']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                // Concatenar todos los campos de nombre para facilitar la búsqueda
                $nombreCompleto = $row['cliente'] . " " . 
                                  $row['segundo_nombre'] . " " . 
                                  $row['primer_apellido'] . " " . 
                                  $row['segundo_apellido'];
    
                $ubicacionCompleta = $row['provincia'] . " " . 
                                  $row['canton'] . " " . 
                                  $row['distrito'];
                                  
                // Filtrar resultados después de desencriptar
                if (!empty($cliente) && stripos($nombreCompleto, $cliente) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }
                
                if (!empty($ubicacion) && stripos($ubicacionCompleta, $ubicacion) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }
    
                if (!empty($telefono) && stripos($row['telefono'], $telefono) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }
    
                // Incrementar el contador solo si todos los filtros coinciden
                $total++;
            }
        }
    
        return $total;
    }

    function contarPedidos($conn, $cliente, $producto, $categoria, $color, $pagado) {
        $query = "SELECT COUNT(*) as total FROM pedidos pe
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        JOIN categorias ca ON pr.idCategoria = ca.id
        JOIN colores co ON pe.idColor = co.id 
        WHERE 1=1 AND pe.estado=1";

        if ($cliente !== null && $cliente !== '') {
            $query .= " AND cl.id LIKE '%" . $conn->real_escape_string($cliente) . "%'";
        }
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function pagar($conn, $id, $idProducto, $cantidad) {
        $id = $conn->real_escape_string($id);
        $idProducto = $conn->real_escape_string($idProducto);  // Escapamos también el $idProducto
        date_default_timezone_set('America/Costa_Rica');
        $fecha_pago = date('Y-m-d H:i:s');
    
        $query = "UPDATE pedidos SET 
                    pagado = 1, 
                    fecha_pago = '$fecha_pago' 
                  WHERE id = '$id';";
                  
        $query .= "UPDATE productos SET 
                    vendidos = vendidos + $cantidad 
                  WHERE id = '$idProducto';";
    
        // Ejecutar múltiples consultas
        if ($conn->multi_query($query)) {
            return "El pedido se ha pagado";
        } else {
            return "Error al pagar el pedido: " . $conn->error;
        }
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function quitar($conn, $id) {
        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE pedidos SET 
                    estado = 0 
                  WHERE id = '$id';";
    
        if ($conn->query($query)) {
            return [
                'title' => "¡Eliminado!",
                'text' => "El pedido se ha eliminado",
                'icon' => "success"
            ];
        } else {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $conn->error,
                'icon' => "error"
            ];
        }
    }

    function insertarSinUsuario($conn, $clienteNombre, $clienteSegundoNombre, $clientePrimerApellido, $clienteSegundoApellido, $clienteProvincia, $clienteCanton, $clienteDistrito, $clienteTelefono, $idProducto, $idColor, $cantidad, $total, $colorAccesorio) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
        $fecha_actual = date('m-d'); // Formato para comparar con los descuentos

        //Agregar el usuario
        $nombre = encryptData($clienteNombre);
        $segundoNombre = encryptData($clienteSegundoNombre);
        $primerApellido = encryptData($clientePrimerApellido);
        $segundoApellido = encryptData($clienteSegundoApellido);
        $rol = encryptData('Invitado');
        $provincia = encryptData($clienteProvincia);
        $canton = encryptData($clienteCanton);
        $distrito = encryptData($clienteDistrito);
        $telefono = encryptData($clienteTelefono);
        $guestId = null;
        
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $query = "INSERT INTO usuarios (nombre, rol, fecha_registro, estado, segundo_nombre, primer_apellido, segundo_apellido, provincia, canton, distrito, telefono) 
                VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssssss", $nombre, $rol, $fecha_registro, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono);
    
            if ($stmt->execute()) {
                $guestId = $conn->insert_id;
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    
        try {
            // Obtener el precio del producto y los IDs de descuentos
            $queryProducto = "SELECT precio, idDescuentos FROM productos WHERE id = ?";
            $stmtProd = $conn->prepare($queryProducto);
            $stmtProd->bind_param("s", $idProducto);
            $stmtProd->execute();
            $resultProd = $stmtProd->get_result();
    
            if ($resultProd->num_rows == 0) {
                return [
                    'title' => "¡Error!",
                    'text' => "El producto no existe.",
                    'icon' => "error"
                ];
            }
    
            $producto = $resultProd->fetch_assoc();
            $precioBase = (float)$producto['precio'];
            $idDescuentosStr = $producto['idDescuentos'];
    
            // Si no hay descuentos, calcular el total normal
            if (empty($idDescuentosStr)) {
                $precioFinal = $precioBase;
                $totalCalculado = $precioFinal * (int)$cantidad;
            } else {
                // Obtener los descuentos aplicables
                $idsDescuentos = explode(',', $idDescuentosStr);
                $placeholders = implode(',', array_fill(0, count($idsDescuentos), '?'));
                
                $queryDescuentos = "SELECT id, fecha_inicial, fecha_final, descuento 
                                    FROM descuentos 
                                    WHERE id IN ($placeholders)";
                $stmtDesc = $conn->prepare($queryDescuentos);
                
                $types = str_repeat('i', count($idsDescuentos));
                $stmtDesc->bind_param($types, ...$idsDescuentos);
                $stmtDesc->execute();
                $resultDesc = $stmtDesc->get_result();

                // Verificar si hay resultados
                if ($resultDesc->num_rows == 0) {
                    var_dump("No se encontraron descuentos para los IDs:", $idsDescuentos);
                    exit;
                }
    
                // Encontrar el mejor descuento aplicable
                $mejorDescuento = 0;
                $tieneDescuentos = false;  // Variable para saber si encontró al menos un descuento

                while ($descuento = $resultDesc->fetch_assoc()) {
                    $tieneDescuentos = true; // Se encontró al menos un descuento

                    $anioActual = date('Y');
                    $fechaInicioFormateada = $anioActual . '-' . $descuento['fecha_inicial'];
                    $fechaFinFormateada = $anioActual . '-' . $descuento['fecha_final'];

                    $fechaInicioConvertida = date('m-d', strtotime($fechaInicioFormateada));
                    $fechaFinConvertida = date('m-d', strtotime($fechaFinFormateada));

                    if ($fecha_actual >= $fechaInicioConvertida && $fecha_actual <= $fechaFinConvertida) {
                        $mejorDescuento = max($mejorDescuento, (int)$descuento['descuento']);
                    }
                }

                // Si no se encontró ningún descuento, aseguramos que el valor no sea NULL
                if (!$tieneDescuentos) {
                    $mejorDescuento = 0;
                }
    
                // Aplicar el mejor descuento encontrado
                $precioFinal = $precioBase * (1 - ($mejorDescuento / 100));
                $totalCalculado = $precioFinal * (int)$cantidad;
            }
    
            // Validar si el total enviado es correcto
            if ((float)$total != round($totalCalculado, 2)) {
                return [
                    'title' => "¡Error!",
                    'text' => "El total enviado no es válido.",
                    'icon' => "error"
                ];
            }
    
            // Insertar en la tabla 'pedidos'
            $query1 = "INSERT INTO pedidos (idCliente, idProducto, idColor, cantidad, total, fecha_registro, estado, idColorAccesorio, precio) 
                       VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)";
    
            $stmt = $conn->prepare($query1);
            $stmt->bind_param("ssssssss",$guestId, $idProducto, $idColor, $cantidad, $total, $fecha_registro, $colorAccesorio, $precioFinal);
    
            if ($stmt->execute()) {
                // Actualizar la cantidad de pedidos en la tabla 'productos'
                $query2 = "UPDATE productos SET pedidos = pedidos + ? WHERE id = ?";
                $stmt2 = $conn->prepare($query2);
                $stmt2->bind_param("is", $cantidad, $idProducto);
    
                if ($stmt2->execute()) {
                    return [
                        'title' => "¡Guardado!",
                        'text' => "El pedido se ha enviado correctamente.",
                        'icon' => "success"
                    ];
                } else {
                    return [
                        'title' => "¡Error!",
                        'text' => "Error al guardar el pedido: " . $conn->error,
                        'icon' => "error"
                    ];
                }
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function listarIds($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $orden) {
        $query = "SELECT pe.id, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }

        if ($conn->real_escape_string($orden) == "pe.id") {
            $query .= " ORDER BY " . $conn->real_escape_string($orden) . " DESC";
        } else {
            $query .= " ORDER BY " . $conn->real_escape_string($orden);
        }
    
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar los datos
                $row['cliente'] = decryptData($row['cliente']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                // Concatenar todos los campos de nombre para facilitar la búsqueda
                $nombreCompleto = $row['cliente'] . " " . 
                                  $row['segundo_nombre'] . " " . 
                                  $row['primer_apellido'] . " " . 
                                  $row['segundo_apellido'];

                $ubicacionCompleta = $row['provincia'] . " " . 
                                  $row['canton'] . " " . 
                                  $row['distrito'];
                                  
                // Filtrar resultados por nombre completo del cliente después de desencriptar
                if (!empty($cliente) && stripos($nombreCompleto, $cliente) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }

                if (!empty($ubicacion) && stripos($ubicacionCompleta, $ubicacion) === false) {
                    continue;
                }

                if (!empty($telefono) && stripos($row['telefono'], $telefono) === false) {
                    continue;
                }
                
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function contarIds($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado) {
        $query = "SELECT COUNT(DISTINCT pe.id) AS total, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }
    
        $query .= " GROUP BY pe.id";
        
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar los datos
                $row['cliente'] = decryptData($row['cliente']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                // Concatenar todos los campos de nombre para facilitar la búsqueda
                $nombreCompleto = $row['cliente'] . " " . 
                                  $row['segundo_nombre'] . " " . 
                                  $row['primer_apellido'] . " " . 
                                  $row['segundo_apellido'];

                $ubicacionCompleta = $row['provincia'] . " " . 
                                  $row['canton'] . " " . 
                                  $row['distrito'];
                                  
                // Filtrar resultados por nombre completo del cliente después de desencriptar
                if (!empty($cliente) && stripos($nombreCompleto, $cliente) === false) {
                    continue; // Si no coincide, pasar al siguiente
                }

                if (!empty($ubicacion) && stripos($ubicacionCompleta, $ubicacion) === false) {
                    continue;
                }

                if (!empty($telefono) && stripos($row['telefono'], $telefono) === false) {
                    continue;
                }
                
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function buscarPorId($conn, $id) {
        $stmt = $conn->prepare("SELECT pe.*, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono,
            pr.nombre AS producto, 
            pr.precio AS precio, 
            ca.nombre AS categoria, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1 AND pe.id=?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        
        $datas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['cliente'] = decryptData($row['cliente']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                $datas[] = $row;
            }
        }
        
        return $datas;
    }

    function listarIdsCliente($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado, $orden) {
        $query = "SELECT pe.id, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }

        $query .= " AND pe.idCliente = " . $conn->real_escape_string($cliente);

        if ($conn->real_escape_string($orden) == "pe.id") {
            $query .= " ORDER BY " . $conn->real_escape_string($orden) . " DESC";
        } else {
            $query .= " ORDER BY " . $conn->real_escape_string($orden);
        }
    
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function contarIdsCliente($conn, $cliente, $producto, $categoria, $rareza, $universo, $color, $pagado) {
        $query = "SELECT COUNT(DISTINCT pe.id) AS total, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND pr.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
        if ($categoria !== null && $categoria !== '') {
            $query .= " AND ca.nombre LIKE '%" . $conn->real_escape_string($categoria) . "%'";
        }
        if ($rareza !== null && $rareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($rareza) . "%'";
        }
        if ($universo !== null && $universo !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($universo) . "%'";
        }
        if ($color !== null && $color !== '') {
            $query .= " AND co.color_familia LIKE '%" . $conn->real_escape_string($color) . "%'";
        }
        if ($pagado !== null && $pagado !== '') {
            $query .= " AND pe.pagado LIKE '%" . $conn->real_escape_string($pagado) . "%'";
        }

        $query .= " AND pe.idCliente = " . $conn->real_escape_string($cliente);
        $query .= " GROUP BY pe.id";
        
        $result = $conn->query($query);
        $pedidos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        return $pedidos;
    }

    function buscarPorIdCliente($conn, $id) {
        $stmt = $conn->prepare("SELECT pe.*, 
            cl.nombre AS cliente, 
            cl.segundo_nombre AS segundo_nombre,
            cl.primer_apellido AS primer_apellido,
            cl.segundo_apellido AS segundo_apellido,
            cl.provincia AS provincia,
            cl.canton AS canton,
            cl.distrito AS distrito,
            cl.telefono AS telefono,
            pr.nombre AS producto, 
            pr.precio AS precio, 
            ca.nombre AS categoria, 
            co.codigo_color_principal AS colorPrincipal, 
            co.codigo_color_secundario AS colorSecundario, 
            co.color_familia AS color,
            pr.idColores AS colores, 
            ac.idColores AS coloresAccesorio
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        LEFT JOIN accesorios ac ON pr.idAccesorio = ac.id AND pr.idAccesorio != 0
        LEFT JOIN rarezas rr ON pr.idRareza = rr.id AND pr.idRareza != 0
        LEFT JOIN universos un ON pr.idUniverso = un.id AND pr.idUniverso != 0
        LEFT JOIN categorias ca ON pr.idCategoria = ca.id
        LEFT JOIN colores co ON pe.idColor = co.id
        WHERE pe.estado=1 AND pe.id=?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        
        $datas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        
        return $datas;
    }
?>