<?php
    function insertar($conn, $nombre, $idCategoria, $idColores, $precio, $imagen_portada, $imagen_galeria, $descripcion, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            // Verificar si ya existe un producto con el mismo nombre y estado = 1
            $checkQuery = "SELECT COUNT(*) as count FROM productos WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El producto " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre.",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            // Consulta SQL de inserción
            $query = "INSERT INTO productos (nombre, idCategoria, idColores, precio, imagen_portada, imagen_galeria, descripcion, fecha_registro, pedidos, vendidos, altura, idDescuentos, estado, peso, idFestividad, visible, idRareza, idUniverso, idAccesorio, advertencia, tiempo, comida, existencia, fecha_destacado) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?,  ?, 1, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            // Aquí, se pasa el número correcto de parámetros y los tipos
            $stmt->bind_param("ssssssssssssssssssss", $nombre, $idCategoria, $idColores, $precio, $imagen_portada, $imagen_galeria, $descripcion, $fecha_registro, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia, $fecha_registro);
    
            if ($stmt->execute()) {
                // Obtener el ID del producto insertado
                $producto_id = $conn->insert_id;
    
                return [
                    'title' => "¡Guardado!",
                    'text' => "El producto se ha guardado correctamente.",
                    'icon' => "bi bi-check-circle",
                    'producto_id' => $producto_id
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function obtener($conn, $nombre, $idCategoria) {
        $query = "
            SELECT p.*, 
                ct.nombre AS categoria, 
                rr.nombre AS rareza,
                un.nombre AS universo,
                ac.nombre AS accesorio, 
                GROUP_CONCAT(CONCAT(cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) 
                SEPARATOR '|') AS colores
            FROM productos p
            JOIN categorias ct ON p.idCategoria = ct.id 
            LEFT JOIN rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            LEFT JOIN accesorios ac ON p.idAccesorio = ac.id AND p.idAccesorio != 0
            LEFT JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado=1
            GROUP BY p.id";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($idCategoria !== null && $idCategoria !== '') {
            $query .= " AND p.idCategoria = '" . (int)$idCategoria . "'";
        }
    
        $result = $conn->query($query);
    
        $productos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
    
        return $productos;
    }

    function listar($conn, $nombre, $idCategoria) {
        $query = "
            SELECT p.*, 
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                rr.nombre AS rareza,
                un.nombre AS universo,
                ac.nombre AS accesorio, 
                GROUP_CONCAT(CONCAT(cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) 
                SEPARATOR '|') AS colores
            FROM productos p
            JOIN categorias ct ON p.idCategoria = ct.id
            LEFT JOIN rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            LEFT JOIN accesorios ac ON p.idAccesorio = ac.id AND p.idAccesorio != 0
            LEFT JOIN festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.visible=1
            AND p.estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($idCategoria !== null && $idCategoria !== '') {
            $query .= " AND ct.nombre LIKE '%" . $conn->real_escape_string($idCategoria) . "%'";
        }

        $query .= ' GROUP BY p.id';
    
        $result = $conn->query($query);
    
        $productos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
    
        return $productos;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                p.*, 
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                fs.fecha_inicial AS festividad_inicio, 
                fs.fecha_final AS festividad_final, 
                rr.nombre AS rareza,
                un.nombre AS universo,
                ac.nombre AS accesorio, 
                ac.idColores AS idColoresAccesorio,
                ac.imagen_color1 AS imagen_accesorio_color1, 
                ac.imagen_color2 AS imagen_accesorio_color2, 
                ac.imagen_color3 AS imagen_accesorio_color3, 
                ac.imagen_color4 AS imagen_accesorio_color4, 
                ac.imagen_color5 AS imagen_accesorio_color5, 
                ac.imagen_color6 AS imagen_accesorio_color6, 
                ac.imagen_color7 AS imagen_accesorio_color7, 
                ac.imagen_color8 AS imagen_accesorio_color8, 
                ac.imagen_color9 AS imagen_accesorio_color9, 
                ac.imagen_color10 AS imagen_accesorio_color10, 
                ac.imagen_color11 AS imagen_accesorio_color11, 
                ac.imagen_color12 AS imagen_accesorio_color12, 
                ac.imagen_color13 AS imagen_accesorio_color13, 
                ac.imagen_color14 AS imagen_accesorio_color14, 
                ac.imagen_color15 AS imagen_accesorio_color15, 
                ac.imagen_color16 AS imagen_accesorio_color16, 
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            ds.id, ',', ds.fecha_inicial, ',', ds.fecha_final, ',', ds.descuento
                        ) SEPARATOR '|'
                    )
                FROM descuentos ds
                WHERE FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' 
                ) AS descuentos,
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia
                        ) SEPARATOR '|'
                    )
                FROM colores cl
                WHERE FIND_IN_SET(cl.id, p.idColores)
                ) AS colores,
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            cl_accesorio.id, ',', cl_accesorio.codigo_color_principal, ',', cl_accesorio.codigo_color_secundario, ',', cl_accesorio.codigo_color_terciario, ',', cl_accesorio.color_familia
                        ) SEPARATOR '|'
                    )
                FROM 
                    colores cl_accesorio
                WHERE 
                    FIND_IN_SET(cl_accesorio.id, ac.idColores)
                ) AS colores_accesorio
            FROM 
                productos p
            JOIN 
                categorias ct ON p.idCategoria = ct.id
            LEFT JOIN 
                rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN 
                universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            LEFT JOIN 
                accesorios ac ON p.idAccesorio = ac.id AND p.idAccesorio != 0
            LEFT JOIN 
                festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            LEFT JOIN 
                descuentos ds ON FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' || NULL
            LEFT JOIN 
                colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE 
                p.id = ?
            GROUP BY 
                p.id;
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $idCategoria, $idColores, $precio, $imagen1, $imagen2, $descripcion, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM productos WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe un producto con ese nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $queryUpdate = "UPDATE productos SET 
                            nombre = ?, 
                            idCategoria = ?, 
                            idColores = ?, 
                            precio = ?, 
                            imagen_portada = ?, 
                            imagen_galeria = ?, 
                            descripcion = ?, 
                            altura = ?, 
                            idDescuentos = ?,
                            peso = ?, 
                            idFestividad = ?, 
                            idRareza = ?, 
                            idUniverso = ?, 
                            idAccesorio = ?, 
                            advertencia = ?,
                            tiempo = ?,
                            comida = ?, 
                            existencia = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssssssssssssssssi", $nombre, $idCategoria, $idColores, $precio, $imagen1, $imagen2, $descripcion, $altura, $descuentos, $peso, $festividad, $rareza, $universo, $accesorio, $advertencia, $tiempo, $comida, $existencia, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El producto se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle",
                    'producto_id' => $id // Agrega el id aquí
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el producto: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE productos SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El producto se ha eliminado correctamente";
        } else {
            return "Error al eliminar el producto: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $idCategoria, $idRareza, $idUniverso, $limit, $offset) {
        $query = "
            SELECT 
                p.id,
                p.nombre,
                p.idCategoria,
                p.idColores,
                p.idFestividad,
                p.idRareza,
                p.idUniverso,
                p.idAccesorio,
                p.idDescuentos,
                p.precio,
                p.descripcion,
                p.pedidos,
                p.vendidos,
                p.altura,
                p.anchura,
                p.peso,
                p.especial,
                p.estado,
                p.visible,
                p.calificaciones_estrellas,
                p.fecha_registro, 
                p.tiempo,
                p.comida,
                p.existencia,
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                rr.nombre AS rareza,
                un.nombre AS universo,
                ac.nombre AS accesorio, 
                GROUP_CONCAT(CONCAT(cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM productos p
            JOIN categorias ct ON p.idCategoria = ct.id
            LEFT JOIN rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            LEFT JOIN accesorios ac ON p.idAccesorio = ac.id AND p.idAccesorio != 0
            LEFT JOIN festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            LEFT JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($idCategoria !== null && $idCategoria !== '') {
            $query .= " AND ct.nombre LIKE '%" . $conn->real_escape_string($idCategoria) . "%'";
        }
        if ($idRareza !== null && $idRareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($idRareza) . "%'";
        }
        if ($idUniverso !== null && $idUniverso !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($idUniverso) . "%'";
        }
    
        $query .= " GROUP BY p.id LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        
        $productos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        
        return $productos;
    }

    function buscarImagen($conn, $id) {
        $query = "
            SELECT 
                p.imagen_portada, 
                p.imagen_galeria, 
                p.imagen_color1, 
                p.imagen_color2, 
                p.imagen_color3, 
                p.imagen_color4, 
                p.imagen_color5, 
                p.imagen_color6, 
                p.imagen_color7, 
                p.imagen_color8, 
                p.imagen_color9, 
                p.imagen_color10, 
                p.imagen_color11, 
                p.imagen_color12, 
                p.imagen_color13, 
                p.imagen_color14, 
                p.imagen_color15, 
                p.imagen_color16, 
                p.imagen_color17, 
                p.imagen_color18, 
                p.imagen_color19, 
                p.imagen_color20, 
                GROUP_CONCAT(CONCAT(cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM productos p
            LEFT JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado = 1";
        
            $query .= " AND p.id =" . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        
        $productos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        
        return $productos;
    }
    
    function contar($conn, $nombre, $idCategoria, $idRareza, $idUniverso) {
        $query = "SELECT COUNT(*) as total FROM productos p 
            JOIN categorias ct ON p.idCategoria = ct.id
            LEFT JOIN rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            WHERE 1=1 AND p.estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($idCategoria !== null && $idCategoria !== '') {
            $query .= " AND ct.nombre LIKE '%" . $conn->real_escape_string($idCategoria) . "%'";
        }
        if ($idRareza !== null && $idRareza !== '') {
            $query .= " AND rr.nombre LIKE '%" . $conn->real_escape_string($idRareza) . "%'";
        }
        if ($idUniverso !== null && $idUniverso !== '') {
            $query .= " AND un.nombre LIKE '%" . $conn->real_escape_string($idUniverso) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM productos WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function cambiarVisibilidad($conn, $id, $visibilidad) {
        $id = $conn->real_escape_string($id);
        $visibilidad = $conn->real_escape_string($visibilidad);
    
        $query = "UPDATE productos SET visible='$visibilidad' WHERE id='$id'";
    
        if ($conn->query($query)) {
            return [
                'title' => "¡" . ((int)$visibilidad == 1 ? 'Publicado' : 'Ocultado') . "!",
                'text' => "El producto se ha " . ((int)$visibilidad == 1 ? 'publicado' : 'ocultado') . " correctamente",
                'icon' => "bi bi-check-circle"
            ];
        } else {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el producto: " . $conn->error,
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function cambiarDestacacidad($conn, $id, $isDestacacidad) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
        $id = $conn->real_escape_string($id);
        $isDestacacidad = $conn->real_escape_string($isDestacacidad);
        if ($isDestacacidad == 0) {
            $fecha_registro = null;
        }
    
        $query = "UPDATE productos SET fecha_destacado='$fecha_registro' WHERE id='$id'";
    
        if ($conn->query($query)) {
            return [
                'title' => "¡" . ((int)$isDestacacidad == 1 ? 'Destacado' : 'Ya no es un destacado') . "!",
                'text' => "El producto se ha " . ((int)$isDestacacidad == 1 ? 'destacado por siete días' : 'dejado de destacar, pero aún sigue en la tienda como producto ordinario.'),
                'icon' => "bi bi-check-circle"
            ];
        } else {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el producto: " . $conn->error,
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function insertarImagen($conn, $id, $imagen, $idImagen) {
        try {
            $columnasPermitidas = ['imagen_color1', 'imagen_color2', 'imagen_color3', 'imagen_color4', 'imagen_color5', 'imagen_color6', 'imagen_color7', 'imagen_color8', 'imagen_color9', 'imagen_color10', 'imagen_color11', 'imagen_color12', 'imagen_color13', 'imagen_color14', 'imagen_color15', 'imagen_color16', 'imagen_color17', 'imagen_color18', 'imagen_color19', 'imagen_color20'];
            
            if (!in_array($idImagen, $columnasPermitidas)) {
                return [
                    'title' => "¡Error!",
                    'text' => "Columna no permitida",
                    'icon' => "bi bi-x-circle",
                    'value' => 0
                ];
            }
    
            $query = "UPDATE productos SET " . $idImagen . " = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Insertado!",
                    'text' => "La imagen se ha insertado correctamente",
                    'icon' => "bi bi-check-circle",
                    'value' => 1
                ];
            } else {
                return [
                    'title' => "¡Error!",
                    'text' => "Error al ejecutar la consulta",
                    'icon' => "bi bi-x-circle",
                    'value' => 0
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al insertar la imagen: " . $e->getMessage(),
                'icon' => "bi bi-x-circle",
                'value' => 0
            ];
        }
    }

    function listarIds(
        $conn,
        $nombre,
        $categoria,
        $rareza,
        $universo,
        $orden
    ){

        $query = "

            SELECT p.id

            FROM productos p

            JOIN categorias ct
            ON p.idCategoria = ct.id

            LEFT JOIN rarezas rr
            ON p.idRareza = rr.id
            AND p.idRareza != 0

            LEFT JOIN universos un
            ON p.idUniverso = un.id
            AND p.idUniverso != 0

            WHERE p.estado = 1

        ";

        if(!empty($nombre)){

            $query .= "
                AND p.nombre LIKE '%" .
                $conn->real_escape_string($nombre) .
                "%'
            ";
        }

        if(!empty($categoria)){

            $query .= "
                AND ct.nombre LIKE '%" .
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

        $columnasPermitidas = [
            'p.nombre',
            'ct.nombre',
            'rr.nombre',
            'un.nombre',
            'p.id'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden = 'p.id';
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

        $result = $conn->query($query);

        $ids = [];

        while($row = $result->fetch_assoc()){

            $ids[] = $row['id'];
        }

        return $ids;
    }

    function buscarPorId($conn, $id){

        $stmt = $conn->prepare("

            SELECT

            p.id,
            p.nombre,
            p.idCategoria,
            p.idColores,
            p.idFestividad,
            p.idRareza,
            p.idUniverso,
            p.idAccesorio,
            p.idDescuentos,
            p.precio,
            p.descripcion,
            p.pedidos,
            p.vendidos,
            p.altura,
            p.peso,
            p.especial,
            p.estado,
            p.visible,
            ROUND(
                AVG(c.estrellas),
                1
            ) AS calificacion_estrellas,
            p.fecha_registro,
            p.tiempo,
            p.comida,
            p.existencia,
            p.fecha_destacado,

            ct.nombre AS categoria,

            fs.nombre AS festividad,

            rr.nombre AS rareza,

            un.nombre AS universo,

            ac.nombre AS accesorio,

            GROUP_CONCAT(
                CONCAT(
                    cl.id, ',',
                    cl.codigo_color_principal, ',',
                    cl.codigo_color_secundario, ',',
                    cl.codigo_color_terciario, ',',
                    cl.color_familia
                )
                SEPARATOR '|'
            ) AS colores

            FROM productos p

            JOIN categorias ct
            ON p.idCategoria = ct.id

            LEFT JOIN rarezas rr
            ON p.idRareza = rr.id
            AND p.idRareza != 0

            LEFT JOIN universos un
            ON p.idUniverso = un.id
            AND p.idUniverso != 0

            LEFT JOIN accesorios ac
            ON p.idAccesorio = ac.id
            AND p.idAccesorio != 0

            LEFT JOIN festividades fs
            ON p.idFestividad = fs.id
            AND p.idFestividad != 0

            LEFT JOIN colores cl
            ON FIND_IN_SET(cl.id, p.idColores)

            LEFT JOIN comentarios c
            ON c.idProducto = p.id
            AND c.estado = 1

            WHERE
                p.estado = 1
                AND p.id = ?

            GROUP BY p.id

        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows <= 0){

            return null;
        }

        $producto =
            $result->fetch_assoc();

        return $producto;
    }

    function buscarCartaProducto($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                p.nombre, 
                p.idCategoria, 
                p.idFestividad, 
                p.idRareza, 
                p.idUniverso, 
                p.idColores, 
                p.idDescuentos, 
                p.idAccesorio,
                p.precio, 
                p.id, 
                p.altura, 
                p.tiempo, 
                p.peso, 
                p.descripcion, 
                p.advertencia, 
                p.fecha_destacado, 
                p.fecha_registro, 
                p.estado, 
                p.especial, 
                p.existencia, 
                p.comida, 
                ROUND(
                    AVG(c.estrellas),
                    1
                ) AS calificacion_estrellas,
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                fs.fecha_inicial AS festividad_inicio, 
                fs.fecha_final AS festividad_final, 
                rr.nombre AS rareza,
                un.nombre AS universo,
                ac.nombre AS accesorio, 
                ac.idColores AS idColoresAccesorio,
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            ds.id, ',', ds.fecha_inicial, ',', ds.fecha_final, ',', ds.descuento
                        ) SEPARATOR '|'
                    )
                FROM descuentos ds
                WHERE FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' 
                ) AS descuentos,
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia
                        ) SEPARATOR '|'
                    )
                FROM colores cl
                WHERE FIND_IN_SET(cl.id, p.idColores)
                ) AS colores,
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            cl_accesorio.id, ',', cl_accesorio.codigo_color_principal, ',', cl_accesorio.codigo_color_secundario, ',', cl_accesorio.codigo_color_terciario, ',', cl_accesorio.color_familia
                        ) SEPARATOR '|'
                    )
                FROM 
                    colores cl_accesorio
                WHERE 
                    FIND_IN_SET(cl_accesorio.id, ac.idColores)
                ) AS colores_accesorio
            FROM 
                productos p
            JOIN 
                categorias ct ON p.idCategoria = ct.id
            LEFT JOIN 
                rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN 
                universos un ON p.idUniverso = un.id AND p.idUniverso != 0
            LEFT JOIN 
                accesorios ac ON p.idAccesorio = ac.id AND p.idAccesorio != 0
            LEFT JOIN 
                festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            LEFT JOIN 
                descuentos ds ON FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' || NULL
            LEFT JOIN 
                colores cl ON FIND_IN_SET(cl.id, p.idColores)
            LEFT JOIN 
                comentarios c ON c.idProducto = p.id AND c.estado = 1
            WHERE 
                p.id = ?
            GROUP BY 
                p.id;
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
?>