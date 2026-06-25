<?php
    include '../security/encrypt.php';

    function insertar(
        $conn,
        $idCliente,
        $idProducto,
        $idColor,
        $cantidad,
        $total,
        $colorAccesorio,
        $precio,
        $fichasUsadas,
        $fichasGanadasFront
    ) {
        date_default_timezone_set('America/Costa_Rica');

        $fechaRegistro = date('Y-m-d H:i:s');

        $conn->begin_transaction();

        try {

            // =====================
            // VALIDACIONES
            // =====================

            $cantidad = max(1, (int)$cantidad);
            $fichasUsadas = max(0, (int)$fichasUsadas);
            $fichasGanadasFront = max(0, (int)$fichasGanadasFront);

            // =====================
            // PRODUCTO
            // =====================

            $query = "
                SELECT
                    precio,
                    fichas,
                    idDescuentos
                FROM productos
                WHERE id = ?
            ";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $idProducto);
            $stmt->execute();

            $producto = $stmt->get_result()->fetch_assoc();

            if (!$producto) {
                throw new Exception('Producto inválido.');
            }

            $precioBase = (float)$producto['precio'];
            $fichasBase = (int)$producto['fichas'];

            $precioFinal = $precioBase;
            $rebaja = 0;

            // =====================
            // DESCUENTOS
            // =====================

            if (!empty($producto['idDescuentos'])) {

                $ids = array_filter(
                    array_map(
                        'intval',
                        explode(',', $producto['idDescuentos'])
                    )
                );

                if (!empty($ids)) {

                    $placeholders = implode(
                        ',',
                        array_fill(0, count($ids), '?')
                    );

                    $query = "
                        SELECT
                            fecha_inicial,
                            fecha_final,
                            descuento
                        FROM descuentos
                        WHERE id IN ($placeholders)
                    ";

                    $stmt = $conn->prepare($query);

                    $stmt->bind_param(
                        str_repeat('i', count($ids)),
                        ...$ids
                    );

                    $stmt->execute();

                    $result = $stmt->get_result();

                    $hoy = new DateTime();

                    while ($row = $result->fetch_assoc()) {

                        $inicio = DateTime::createFromFormat(
                            'm-d',
                            $row['fecha_inicial']
                        );

                        $fin = DateTime::createFromFormat(
                            'm-d',
                            $row['fecha_final']
                        );

                        if (!$inicio || !$fin) {
                            continue;
                        }

                        $inicio->setDate(
                            $hoy->format('Y'),
                            $inicio->format('m'),
                            $inicio->format('d')
                        );

                        $fin->setDate(
                            $inicio->format('Y'),
                            $fin->format('m'),
                            $fin->format('d')
                        );

                        if ($fin < $inicio) {
                            $fin->modify('+1 year');
                        }

                        if ($hoy >= $inicio && $hoy <= $fin) {
                            $rebaja = max(
                                $rebaja,
                                (float)$row['descuento']
                            );
                        }
                    }

                    $precioFinal = round(
                        $precioBase * (1 - ($rebaja / 100)),
                        2
                    );
                }
            }

            // =====================
            // TOTAL
            // =====================

            $subtotal = $precioFinal * $cantidad;

            $descuento = $fichasUsadas * 10;

            $totalCalculado = max(
                0,
                $subtotal - $descuento
            );

            if (
                abs(
                    $totalCalculado - (float)$total
                ) > 0.01
            ) {
                throw new Exception(
                    "Total inválido. Front: {$total} | Back: {$totalCalculado}"
                );
            }

            // =====================
            // FICHAS
            // =====================

            $recompensaUnitaria =
                $rebaja > 0
                    ? (int)(
                        $fichasBase *
                        (
                            1 -
                            (
                                $rebaja / 100
                            )
                        )
                    )
                    : $fichasBase;

            $recompensaBase =
                $recompensaUnitaria *
                $cantidad;

            $porcentajePagado =
                $subtotal > 0
                    ? (
                        $totalCalculado /
                        $subtotal
                    )
                    : 0;

            // igual que JS
            $fichasGanadas =
                (int)floor(
                    max(
                        0,
                        $recompensaBase *
                        $porcentajePagado
                    )
                );

            if (
                abs(
                    $fichasGanadas -
                    $fichasGanadasFront
                ) > 0
            ) {

                throw new Exception(
                    "Fichas inválidas. " .
                    "Front: {$fichasGanadasFront} | " .
                    "Back: {$fichasGanadas}" .
                    " | Unit: {$recompensaUnitaria}" .
                    " | Base: {$recompensaBase}" .
                    " | %: {$porcentajePagado}"
                );
            }

            // =====================
            // DESCONTAR FICHAS
            // =====================

            if ($fichasUsadas > 0) {

                $query = "
                    UPDATE usuarios
                    SET fichas = fichas - ?
                    WHERE id = ?
                    AND fichas >= ?
                ";

                $stmt = $conn->prepare($query);

                $stmt->bind_param(
                    "iii",
                    $fichasUsadas,
                    $idCliente,
                    $fichasUsadas
                );

                $stmt->execute();

                if ($stmt->affected_rows == 0) {
                    throw new Exception(
                        'No tienes suficientes fichas.'
                    );
                }
            }

            // =====================
            // INSERTAR PEDIDO
            // =====================

            $query = "
                INSERT INTO pedidos (
                    idCliente,
                    idProducto,
                    idColor,
                    cantidad,
                    total,
                    fecha_registro,
                    estado,
                    idColorAccesorio,
                    precio,
                    fichas_usadas,
                    fichas_ganadas
                )
                VALUES (
                    ?, ?, ?, ?,
                    ?, ?,
                    1,
                    ?, ?, ?, ?
                )
            ";

            $stmt = $conn->prepare($query);

            $stmt->bind_param(
                "iiiidsdiii",
                $idCliente,
                $idProducto,
                $idColor,
                $cantidad,
                $totalCalculado,
                $fechaRegistro,
                $colorAccesorio,
                $precioFinal,
                $fichasUsadas,
                $fichasGanadas
            );

            $stmt->execute();

            // =====================
            // ACTUALIZAR CONTADOR
            // =====================

            $query = "
                UPDATE productos
                SET pedidos = pedidos + ?
                WHERE id = ?
            ";

            $stmt = $conn->prepare($query);

            $stmt->bind_param(
                "ii",
                $cantidad,
                $idProducto
            );

            $stmt->execute();

            $conn->commit();

            return [
                'title' => '¡Guardado!',
                'text'  => 'Pedido realizado correctamente.',
                'icon'  => 'bi bi-check-circle'
            ];

        } catch (Exception $e) {

            $conn->rollback();

            return [
                'title' => '¡Error!',
                'text'  => $e->getMessage(),
                'icon'  => 'bi bi-x-circle'
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
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el pedido: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
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

    function pagar(
        $conn,
        $id,
        $idProducto,
        $cantidad
    ) {

        date_default_timezone_set(
            'America/Costa_Rica'
        );

        $fechaPago =
            date(
                'Y-m-d H:i:s'
            );

        $conn->begin_transaction();

        try {

            // =====================
            // PEDIDO
            // =====================

            $query = "
                SELECT
                    idCliente,
                    fichas_ganadas,
                    pagado
                FROM pedidos
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "i",
                $id
            );

            $stmt->execute();

            $pedido =
                $stmt
                    ->get_result()
                    ->fetch_assoc();

            if (
                !$pedido
            ) {

                throw new Exception(
                    'Pedido inválido.'
                );
            }

            if (
                $pedido['pagado']
            ) {

                throw new Exception(
                    'Este pedido ya fue pagado.'
                );
            }

            $idCliente =
                (int)
                $pedido[
                    'idCliente'
                ];

            $fichasGanadas =
                (int)
                $pedido[
                    'fichas_ganadas'
                ];

            // =====================
            // PAGAR PEDIDO
            // =====================

            $query = "
                UPDATE pedidos
                SET
                    pagado = 1,
                    fecha_pago = ?
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "si",
                $fechaPago,
                $id
            );

            $stmt->execute();

            if (
                $stmt->affected_rows == 0
            ) {

                throw new Exception(
                    'No se pudo actualizar el pedido.'
                );
            }

            // =====================
            // SUMAR VENDIDOS
            // =====================

            $query = "
                UPDATE productos
                SET vendidos =
                    vendidos + ?
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "ii",
                $cantidad,
                $idProducto
            );

            $stmt->execute();

            // =====================
            // DAR FICHAS
            // =====================

            if (
                $fichasGanadas > 0
            ) {

                $query = "
                    UPDATE usuarios
                    SET fichas =
                        fichas + ?
                    WHERE id = ?
                ";

                $stmt =
                    $conn->prepare(
                        $query
                    );

                $stmt->bind_param(
                    "ii",
                    $fichasGanadas,
                    $idCliente
                );

                $stmt->execute();

                if (
                    $stmt->affected_rows == 0
                ) {

                    throw new Exception(
                        'No se pudieron asignar las fichas.'
                    );
                }
            }

            $conn->commit();

            return [
                'title' =>
                    '¡Pagado!',
                'text' =>
                    'Pedido pagado correctamente.',
                'icon' =>
                    'bi bi-check-circle'
            ];

        } catch (
            Exception $e
        ) {

            $conn->rollback();

            return [
                'title' =>
                    '¡Error!',
                'text' =>
                    $e->getMessage(),
                'icon' =>
                    'bi bi-x-circle'
            ];
        }
    }

    function quitar(
        $conn,
        $id
    ) {

        $conn->begin_transaction();

        try {

            $query = "
                SELECT
                    idCliente,
                    fichas_usadas,
                    fichas_ganadas,
                    pagado
                FROM pedidos
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "i",
                $id
            );

            $stmt->execute();

            $pedido =
                $stmt
                    ->get_result()
                    ->fetch_assoc();

            $fichas =
                (int)
                $pedido[
                    'fichas_usadas'
                ];

            if (
                $pedido[
                    'pagado'
                ]
            ) {

                $fichas -=
                    (int)
                    $pedido[
                        'fichas_ganadas'
                    ];
            }

            $fichas =
                max(
                    0,
                    $fichas
                );

            $query = "
                UPDATE usuarios
                SET fichas =
                    fichas + ?
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "ii",
                $fichas,
                $pedido['idCliente']
            );

            $stmt->execute();

            $query = "
                UPDATE pedidos
                SET estado = 0
                WHERE id = ?
            ";

            $stmt =
                $conn->prepare(
                    $query
                );

            $stmt->bind_param(
                "i",
                $id
            );

            $stmt->execute();

            $conn->commit();

            return [
                'title' => '¡Eliminado!',
                'text' => 'Pedido eliminado.',
                'icon' => 'bi bi-check-circle'
            ];

        } catch (
            Exception $e
        ) {

            $conn->rollback();

            return [
                'title' => '¡Error!',
                'text' => $e->getMessage(),
                'icon' => 'bi bi-x-circle'
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
                'icon' => "bi bi-x-circle"
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
                    'icon' => "bi bi-x-circle"
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
                    'icon' => "bi bi-x-circle"
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
                        'icon' => "bi bi-check-circle"
                    ];
                } else {
                    return [
                        'title' => "¡Error!",
                        'text' => "Error al guardar el pedido: " . $conn->error,
                        'icon' => "bi bi-x-circle"
                    ];
                }
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function listarIds(
        $conn,
        $cliente,
        $producto,
        $categoria,
        $rareza,
        $universo,
        $color,
        $pagado,
        $ubicacion,
        $telefono,
        $orden
    ){

        $query = "

            SELECT pe.id

            FROM pedidos pe

            JOIN usuarios cl
            ON pe.idCliente = cl.id

            JOIN productos pr
            ON pe.idProducto = pr.id

            LEFT JOIN accesorios ac
            ON pr.idAccesorio = ac.id
            AND pr.idAccesorio != 0

            LEFT JOIN rarezas rr
            ON pr.idRareza = rr.id
            AND pr.idRareza != 0

            LEFT JOIN universos un
            ON pr.idUniverso = un.id
            AND pr.idUniverso != 0

            LEFT JOIN categorias ca
            ON pr.idCategoria = ca.id

            LEFT JOIN colores co
            ON pe.idColor = co.id

            WHERE pe.estado = 1

        ";

        if(!empty($producto)){

            $query .= "
                AND pr.nombre LIKE '%" .
                $conn->real_escape_string($producto) .
                "%'
            ";
        }

        if(!empty($categoria)){

            $query .= "
                AND ca.nombre LIKE '%" .
                $conn->real_escape_string($categoria) .
                "%'
            ";
        }

        if(!empty($rareza)){

            $query .= "
                AND rr.nombre LIKE '%" .
                $conn->real_escape_string($rareza) .
                "%'
            ";
        }

        if(!empty($universo)){

            $query .= "
                AND un.nombre LIKE '%" .
                $conn->real_escape_string($universo) .
                "%'
            ";
        }

        if(!empty($color)){

            $query .= "
                AND co.color_familia LIKE '%" .
                $conn->real_escape_string($color) .
                "%'
            ";
        }

        if($pagado !== ''){

            $query .= "
                AND pe.pagado = '" .
                $conn->real_escape_string($pagado) .
                "'
            ";
        }

        $columnasPermitidas = [
            'pe.id',
            'cl.nombre',
            'pr.nombre',
            'ca.nombre',
            'rr.nombre',
            'un.nombre',
            'co.color_familia'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden = 'pe.id';
        $formaOrden = 'DESC';

        if(
            is_array($orden)
            &&
            isset($orden['orden'])
            &&
            in_array(
                $orden['orden'],
                $columnasPermitidas
            )
        ){
            $campoOrden = $orden['orden'];
        }

        if(
            is_array($orden)
            &&
            isset($orden['forma'])
            &&
            in_array(
                strtoupper($orden['forma']),
                $formasPermitidas
            )
        ){
            $formaOrden =
                strtoupper(
                    $orden['forma']
                );
        }

        $query .= "
            ORDER BY
            $campoOrden
            $formaOrden
        ";

        $result =
            $conn->query($query);

        $ids = [];

        while(
            $row =
            $result->fetch_assoc()
        ){

            $ids[] =
                $row['id'];
        }

        return $ids;
    }

    function buscarPorId(
        $conn,
        $id
    ){

        $stmt = $conn->prepare("

            SELECT

                pe.*,

                cl.nombre AS cliente,
                cl.segundo_nombre,
                cl.primer_apellido,
                cl.segundo_apellido,
                cl.provincia,
                cl.canton,
                cl.distrito,
                cl.telefono,
                cl.nombre_usuario,

                pr.nombre AS producto,
                pr.precio,

                ca.nombre AS categoria,

                rr.nombre AS rareza,

                un.nombre AS universo,

                co.codigo_color_principal AS colorPrincipal,
                co.codigo_color_secundario AS colorSecundario,
                co.color_familia AS color,

                pr.idColores AS colores,

                ac.idColores AS coloresAccesorio

            FROM pedidos pe

            JOIN usuarios cl
            ON pe.idCliente = cl.id

            JOIN productos pr
            ON pe.idProducto = pr.id

            LEFT JOIN accesorios ac
            ON pr.idAccesorio = ac.id
            AND pr.idAccesorio != 0

            LEFT JOIN rarezas rr
            ON pr.idRareza = rr.id
            AND pr.idRareza != 0

            LEFT JOIN universos un
            ON pr.idUniverso = un.id
            AND pr.idUniverso != 0

            LEFT JOIN categorias ca
            ON pr.idCategoria = ca.id

            LEFT JOIN colores co
            ON pe.idColor = co.id

            WHERE
                pe.estado = 1
                AND pe.id = ?

        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        if(
            $result->num_rows <= 0
        ){
            return null;
        }

        $pedido =
            $result->fetch_assoc();

        // Desencriptar datos cliente

        $pedido['cliente'] =
            decryptData(
                $pedido['cliente']
            );

        $pedido['segundo_nombre'] =
            decryptData(
                $pedido['segundo_nombre']
            );

        $pedido['primer_apellido'] =
            decryptData(
                $pedido['primer_apellido']
            );

        $pedido['segundo_apellido'] =
            decryptData(
                $pedido['segundo_apellido']
            );

        $pedido['provincia'] =
            decryptData(
                $pedido['provincia']
            );

        $pedido['canton'] =
            decryptData(
                $pedido['canton']
            );

        $pedido['distrito'] =
            decryptData(
                $pedido['distrito']
            );

        $pedido['telefono'] =
            decryptData(
                $pedido['telefono']
            );

        return $pedido;
    }

    function listarIdsCliente(
        $conn,
        $cliente,
        $producto,
        $categoria,
        $rareza,
        $universo,
        $color,
        $pagado,
        $orden
    ) {

        $query = "
            SELECT pe.id

            FROM pedidos pe

            JOIN usuarios cl
                ON pe.idCliente = cl.id

            JOIN productos pr
                ON pe.idProducto = pr.id

            LEFT JOIN categorias ca
                ON pr.idCategoria = ca.id

            LEFT JOIN rarezas rr
                ON pr.idRareza = rr.id

            LEFT JOIN universos un
                ON pr.idUniverso = un.id

            LEFT JOIN colores co
                ON pe.idColor = co.id

            WHERE pe.estado = 1
            AND pe.idCliente = ?
        ";

        if (!empty($producto)) {
            $query .= " AND pr.nombre LIKE '%" .
                $conn->real_escape_string($producto) . "%'";
        }

        if (!empty($categoria)) {
            $query .= " AND ca.nombre LIKE '%" .
                $conn->real_escape_string($categoria) . "%'";
        }

        if (!empty($rareza)) {
            $query .= " AND rr.nombre LIKE '%" .
                $conn->real_escape_string($rareza) . "%'";
        }

        if (!empty($universo)) {
            $query .= " AND un.nombre LIKE '%" .
                $conn->real_escape_string($universo) . "%'";
        }

        if (!empty($color)) {
            $query .= " AND co.color_familia LIKE '%" .
                $conn->real_escape_string($color) . "%'";
        }

        if ($pagado !== '') {
            $query .= " AND pe.pagado = '" .
                $conn->real_escape_string($pagado) . "'";
        }

        // CAMPOS PERMITIDOS
        $columnasPermitidas = [
            'pe.id',
            'pr.nombre',
            'ca.nombre',
            'co.nombre',
            'pe.progreso'
        ];

        // DIRECCIONES PERMITIDAS
        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        // VALORES POR DEFECTO
        $campoOrden = 'pe.id';
        $formaOrden = 'DESC';

        // VALIDAR CAMPO
        if (
            is_array($orden) &&
            isset($orden['orden']) &&
            in_array($orden['orden'], $columnasPermitidas)
        ) {
            $campoOrden = $orden['orden'];
        }

        // VALIDAR FORMA
        if (
            is_array($orden) &&
            isset($orden['forma']) &&
            in_array(
                strtoupper($orden['forma']),
                $formasPermitidas
            )
        ) {
            $formaOrden = strtoupper($orden['forma']);
        }

        // ORDER BY FINAL
        $query .= " ORDER BY $campoOrden $formaOrden";

        // PREPARE

        $stmt = $conn->prepare($query);

        // BIND

        $stmt->bind_param("i", $cliente);

        // EXECUTE

        $stmt->execute();

        $result = $stmt->get_result();

        $ids = [];

        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id'];
        }

        return $ids;
    }

    function buscarPorIdCliente($conn, $id) {
        $stmt = $conn->prepare("

            SELECT

            pe.*,

            cl.nombre AS cliente,
            cl.segundo_nombre,
            cl.primer_apellido,
            cl.segundo_apellido,
            cl.telefono,

            pr.nombre AS producto,
            pr.precio AS precioProducto,
            pr.imagen_color1,
            pr.imagen_color2,
            pr.imagen_color3,
            pr.imagen_color4,
            pr.imagen_color5,
            pr.imagen_color6,
            pr.imagen_color7,
            pr.imagen_color8,
            pr.imagen_color9,
            pr.imagen_color10,
            pr.imagen_color11,
            pr.imagen_color12,
            pr.imagen_color13,
            pr.imagen_color14,
            pr.imagen_color15,
            pr.imagen_color16,
            pr.imagen_color17,
            pr.imagen_color18,
            pr.imagen_color19,
            pr.imagen_color20,
            pr.idColores,

            ca.nombre AS categoria,

            un.nombre AS universo,

            co.codigo_color_principal,
            co.codigo_color_secundario,
            co.color_familia,

            ac.nombre AS accesorio,
            ac.imagen_color1 AS accesorio_imagen1,
            ac.imagen_color2 AS accesorio_imagen2,
            ac.imagen_color3 AS accesorio_imagen3,
            ac.imagen_color4 AS accesorio_imagen4,
            ac.imagen_color5 AS accesorio_imagen5,
            ac.imagen_color6 AS accesorio_imagen6,
            ac.imagen_color7 AS accesorio_imagen7,
            ac.imagen_color8 AS accesorio_imagen8,
            ac.imagen_color9 AS accesorio_imagen9,
            ac.imagen_color10 AS accesorio_imagen10,
            ac.imagen_color11 AS accesorio_imagen11,
            ac.imagen_color12 AS accesorio_imagen12,
            ac.imagen_color13 AS accesorio_imagen13,
            ac.imagen_color14 AS accesorio_imagen14,
            ac.imagen_color15 AS accesorio_imagen15,
            ac.imagen_color16 AS accesorio_imagen16,
            ac.idColores AS coloresAccesorio

            FROM pedidos pe

            JOIN usuarios cl
            ON pe.idCliente = cl.id

            JOIN productos pr
            ON pe.idProducto = pr.id

            LEFT JOIN categorias ca
            ON pr.idCategoria = ca.id

            LEFT JOIN universos un
            ON pr.idUniverso = un.id

            LEFT JOIN colores co
            ON pe.idColor = co.id

            LEFT JOIN accesorios ac
            ON pr.idAccesorio = ac.id

            WHERE pe.id = ?
            AND pe.estado = 1

        ");

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return null;
        }

        $pedido = $result->fetch_assoc();

        // ==========================================
        // OBTENER IMAGEN SEGÚN COLOR SELECCIONADO
        // ==========================================

        // convertir lista de colores en array
        $coloresProducto = explode(',', $pedido['idColores']);

        // buscar posición del color seleccionado
        $indexColor = array_search(
            (string)$pedido['idColor'],
            $coloresProducto
        );

        // imagen final
        $pedido['imagen_producto'] = null;

        // ==========================================
        // IMAGEN ACCESORIO
        // ==========================================

        $pedido['imagen_accesorio'] = null;

        if (
            !empty($pedido['accesorio']) &&
            !empty($pedido['coloresAccesorio']) &&
            !empty($pedido['idColorAccesorio'])
        ) {

            $coloresAccesorio = explode(
                ',',
                $pedido['coloresAccesorio']
            );

            $indexAccesorio = array_search(
                (string)$pedido['idColorAccesorio'],
                $coloresAccesorio
            );

            if ($indexAccesorio !== false) {

                $numeroImagenAccesorio =
                    $indexAccesorio + 1;

                $campoAccesorio =
                    'accesorio_imagen' .
                    $numeroImagenAccesorio;

                if (!empty($pedido[$campoAccesorio])) {

                    $pedido['imagen_accesorio'] =
                        $pedido[$campoAccesorio];
                }
            }
        }

        // si encontró el color
        if ($indexColor !== false) {

            // array_search empieza en 0
            // pero las imágenes empiezan en 1
            $numeroImagen = $indexColor + 1;

            $campoImagen = 'imagen_color' . $numeroImagen;

            // validar que exista
            if (!empty($pedido[$campoImagen])) {

                $pedido['imagen_producto'] =
                    $pedido[$campoImagen];
            }
        }

        // desencriptar
        $pedido['cliente'] = decryptData($pedido['cliente']);
        $pedido['segundo_nombre'] = decryptData($pedido['segundo_nombre']);
        $pedido['primer_apellido'] = decryptData($pedido['primer_apellido']);
        $pedido['segundo_apellido'] = decryptData($pedido['segundo_apellido']);
        $pedido['telefono'] = decryptData($pedido['telefono']);

        return $pedido;
    }

    function actualizarProgresoPedido($conn, $id, $progreso) {
        $id = $conn->real_escape_string($id);
        $progreso = $conn->real_escape_string($progreso);
    
        $query = "UPDATE pedidos SET 
                    progreso = '$progreso' 
                  WHERE id = '$id';";
    
        // Ejecutar múltiples consultas
        if ($conn->multi_query($query)) {
            return "Se ha actualizado el progreso del pedido";
        } else {
            return "Error al actualizar el progreso del pedido: " . $conn->error;
        }
    }

?>