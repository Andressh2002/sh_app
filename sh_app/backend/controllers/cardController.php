<?php
    function contarCategorias($conn, $nombre) {
        $stmt = $conn->prepare("
            SELECT DISTINCT ca.id 
            FROM categorias ca
            JOIN productos pr ON pr.idCategoria = ca.id
            WHERE ca.estado = 1 
            AND pr.idFestividad = 0 
            AND pr.visible = 1
            AND ca.nombre LIKE ?

            UNION

            SELECT DISTINCT ca.id 
            FROM categorias ca
            JOIN productos pr ON pr.idCategoria = ca.id
            JOIN festividades fe ON pr.idFestividad = fe.id
            WHERE ca.estado = 1 
            AND pr.idFestividad != 0 
            AND pr.visible = 1
            AND ca.nombre LIKE ? 
            AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
            AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s');
        ");
        
        $nombre = "%" . $nombre . "%";
        
        $stmt->bind_param("ss", $nombre, $nombre);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        $categorias = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
            return $categorias;
        } else {
            return null;
        }
    }

    function buscarCategoria($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                ca.id, 
                ca.nombre, 
                ca.descripcion, 
                ca.estado, 
                ca.fecha_registro, 
                COUNT(DISTINCT pr.id) AS cantidad, 
                MAX(
                    CASE 
                        WHEN pr.idFestividad != 0 
                        AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                        AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_disponibilidad_limitada,
                MAX(
                    CASE 
                        WHEN FIND_IN_SET(d.id, pr.idDescuentos) > 0 
                        AND NOW() BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW()), '-', d.fecha_inicial), '%Y-%m-%d') 
                            AND STR_TO_DATE(CONCAT(YEAR(NOW()), '-', d.fecha_final), '%Y-%m-%d') 
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_descuentos_activos,
                 MAX(
                    CASE 
                        WHEN pr.existencia != 0 
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_existencias_limitadas
            FROM categorias ca
            JOIN productos pr ON pr.idCategoria = ca.id
            LEFT JOIN festividades fe ON pr.idFestividad = fe.id
            LEFT JOIN descuentos d 
                ON FIND_IN_SET(d.id, pr.idDescuentos) > 0 -- Verifica si el ID del descuento está en la lista
            WHERE ca.estado = 1 
            AND ca.id = ? 
            AND (
                pr.idFestividad = 0 
                OR (
                    pr.idFestividad != 0 
                    AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                    AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                )
            )
            GROUP BY ca.id, ca.nombre;
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

    function buscarImagenCategoria($conn, $id) {
        $stmt = $conn->prepare("SELECT imagen FROM categorias WHERE id = ? AND estado = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function contarUniversos($conn, $nombre) {
        $stmt = $conn->prepare("
            SELECT DISTINCT un.id 
            FROM universos un
            JOIN productos pr ON pr.idUniverso = un.id
            WHERE un.estado = 1 
            AND pr.idFestividad = 0 
            AND pr.visible = 1
            AND un.nombre LIKE ?

            UNION

            SELECT DISTINCT un.id 
            FROM universos un
            JOIN productos pr ON pr.idUniverso = un.id
            JOIN festividades fe ON pr.idFestividad = fe.id
            WHERE un.estado = 1 
            AND pr.idFestividad != 0 
            AND pr.visible = 1
            AND un.nombre LIKE ? 
            AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
            AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s');
        ");
        
        $nombre = "%" . $nombre . "%";
        
        $stmt->bind_param("ss", $nombre, $nombre);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        $universos = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $universos[] = $row;
            }
            return $universos;
        } else {
            return null;
        }
    }

    function buscarUniverso($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                un.id, 
                un.nombre, 
                un.descripcion, 
                un.estado, 
                un.fecha_registro, 
                COUNT(DISTINCT pr.id) AS cantidad, 
                MAX(
                    CASE 
                        WHEN pr.idFestividad != 0 
                        AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                        AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_disponibilidad_limitada,
                MAX(
                    CASE 
                        WHEN FIND_IN_SET(d.id, pr.idDescuentos) > 0 
                        AND NOW() BETWEEN STR_TO_DATE(CONCAT(YEAR(NOW()), '-', d.fecha_inicial), '%Y-%m-%d') 
                            AND STR_TO_DATE(CONCAT(YEAR(NOW()), '-', d.fecha_final), '%Y-%m-%d') 
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_descuentos_activos,
                 MAX(
                    CASE 
                        WHEN pr.existencia != 0 
                        THEN 1 
                        ELSE 0 
                    END
                ) AS tiene_existencias_limitadas
            FROM universos un
            JOIN productos pr ON pr.idUniverso = un.id
            LEFT JOIN festividades fe ON pr.idFestividad = fe.id
            LEFT JOIN descuentos d 
                ON FIND_IN_SET(d.id, pr.idDescuentos) > 0 -- Verifica si el ID del descuento está en la lista
            WHERE un.estado = 1 
            AND un.id = ? 
            AND (
                pr.idFestividad = 0 
                OR (
                    pr.idFestividad != 0 
                    AND NOW() >= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_inicial, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                    AND NOW() <= STR_TO_DATE(CONCAT(YEAR(NOW()), '-', fe.fecha_final, ' 00:00:00'), '%Y-%m-%d %H:%i:%s')
                )
            )
            GROUP BY un.id, un.nombre;
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

    function buscarImagenUniverso($conn, $id) {
        $stmt = $conn->prepare("SELECT imagen FROM universos WHERE id = ? AND estado = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function buscarLogoUniverso($conn, $id) {
        $stmt = $conn->prepare("SELECT logo FROM universos WHERE id = ? AND estado = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function contarProductos($conn, $filtros, $limite) {
        // Extrae los filtros
        list($nombre, $precio, $idCategorias, $idFestividades, $idRarezas, $idUniversos, $idCliente, $modo) = $filtros;
    
        // Base de la consulta SQL
        $sql = "SELECT pr.id AS id, pr.nombre AS nombre, pr.fecha_destacado FROM productos pr";
        
        // Array para almacenar los JOIN dinámicos
        $joins = [];
        $where = ["pr.estado = 1", "pr.visible = 1"];
        $parametros = [];
        $tipos = "";

        if($modo == "favoritos"){
            $joins[] = "
                INNER JOIN favoritos fv
                    ON fv.idProducto = pr.id
                AND fv.idCliente = ?
            ";
            
            $parametros[] = $idCliente;
            $tipos .= "i";
        }
    
        // JOIN y filtro por categorías
        if (!empty($idCategorias)) {
            // Convertir todos los valores de $idCategorias a enteros
            $idCategorias = array_map('intval', $idCategorias);
        
            // Crear los placeholders dinámicos
            $placeholders = implode(",", array_fill(0, count($idCategorias), "?"));
            
            // Agregar el JOIN y la cláusula WHERE
            $joins[] = "JOIN categorias ct ON pr.idCategoria = ct.id";
            $where[] = "ct.id IN ($placeholders)";
            
            // Agregar los valores al array de parámetros
            $parametros = array_merge($parametros, $idCategorias);
            
            // Agregar los tipos correspondientes
            $tipos .= str_repeat("i", count($idCategorias));
        }
    
        // JOIN y filtro por festividades
        if ($idFestividades !== null && count($idFestividades) > 0) {
            $joins[] = "LEFT JOIN festividades ft ON pr.idFestividad = ft.id";
            $placeholders = implode(",", array_fill(0, count($idFestividades), "?"));
            // Aquí cambiamos la lógica para manejar correctamente el caso de "Ninguna"
            if (in_array(0, $idFestividades)) {
                $where[] = "(ft.id IN ($placeholders) OR pr.idFestividad = 0)";
                // Eliminamos el valor 0 de los parámetros ya que lo estamos manejando por separado
                $idFestividades = array_filter($idFestividades, fn($id) => $id !== 0);
            } else {
                $where[] = "ft.id IN ($placeholders)";
            }
            $parametros = array_merge($parametros, $idFestividades); 
            $tipos .= str_repeat("i", count($idFestividades));
        }
    
        // JOIN y filtro por rarezas
        if (!empty($idRarezas)) {
            $joins[] = "JOIN rarezas rr ON pr.idRareza = rr.id";
            $placeholders = implode(",", array_fill(0, count($idRarezas), "?"));
            $where[] = "rr.id IN ($placeholders)";
            $parametros = array_merge($parametros, $idRarezas);
            $tipos .= str_repeat("i", count($idRarezas));
        }
    
        // JOIN y filtro por universos
        if (!empty($idUniversos)) {
            $joins[] = "JOIN universos un ON pr.idUniverso = un.id";
            $placeholders = implode(",", array_fill(0, count($idUniversos), "?"));
            $where[] = "un.id IN ($placeholders)";
            $parametros = array_merge($parametros, $idUniversos);
            $tipos .= str_repeat("i", count($idUniversos));
        }
    
        // Filtro por nombre del producto
        if (!empty($nombre)) {
            $where[] = "pr.nombre LIKE ?";
            $parametros[] = "%" . $nombre . "%";
            $tipos .= "s";
        }
    
        // Filtro por rango de precios
        if (!empty($precio[0])) {
            $where[] = "pr.precio >= ?";
            $parametros[] = $precio[0];
            $tipos .= "d";
        }
        if (!empty($precio[1])) {
            $where[] = "pr.precio <= ?";
            $parametros[] = $precio[1];
            $tipos .= "d";
        }
    
        // Construir consulta final
        $sql .= " " . implode(" ", $joins);
        $sql .= " WHERE " . implode(" AND ", $where);

        // Verificar aleatoridad
        if ($limite != '') {
            $sql .= " ORDER BY RAND() ";
            $sql .= " LIMIT " . intval($limite);
        }
    
        // Preparar la consulta
        $stmt = $conn->prepare($sql);
    
        // Vincular parámetros dinámicos
        if (!empty($parametros)) {
            $stmt->bind_param($tipos, ...$parametros);
        }
    
        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Recoger resultados
        $productos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
    
        return $productos;
    }
    
    function buscarProducto($conn, $id, $idCliente) {
        $stmt = $conn->prepare("SELECT 
                p.id,
                p.nombre,
                p.idCategoria,
                p.idColores,
                p.idFestividad,
                p.precio,
                p.descripcion,
                p.pedidos,
                p.vendidos,
                p.altura,
                p.peso,
                p.especial,
                p.estado,
                p.visible,
                EXISTS(
                    SELECT 1
                    FROM favoritos f
                    WHERE
                        f.idCliente = ?
                    AND
                        f.idProducto = p.id
                ) AS favorito, 
                ROUND(
                    AVG(c.estrellas),
                    1
                ) AS calificacion_estrellas,
                p.fecha_registro,
                p.fecha_destacado,
                p.existencia,
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                fs.fecha_inicial AS festividad_inicio, 
                fs.fecha_final AS festividad_final, 
                -- Subconsulta para descuentos
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            ds.id, ',', ds.fecha_inicial, ',', ds.fecha_final, ',', ds.descuento
                        ) SEPARATOR '|'
                    )
                FROM descuentos ds
                WHERE FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' 
                ) AS descuentos,
                rr.color AS color_rareza,
                -- Subconsulta para colores
                (SELECT 
                    GROUP_CONCAT(
                        CONCAT(
                            cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia
                        ) SEPARATOR '|'
                    )
                FROM colores cl
                WHERE FIND_IN_SET(cl.id, p.idColores)
                ) AS colores
            FROM productos p
            JOIN categorias ct ON p.idCategoria = ct.id
            LEFT JOIN festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            LEFT JOIN descuentos ds ON FIND_IN_SET(ds.id, p.idDescuentos) AND p.idDescuentos != '' || NULL
            LEFT JOIN rarezas rr ON p.idRareza = rr.id AND p.idRareza != 0
            LEFT JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            LEFT JOIN comentarios c ON c.idProducto = p.id AND c.estado = 1
            WHERE p.estado = 1 
            AND p.visible = 1 
            AND p.id=?");
        $stmt->bind_param("ii", $idCliente, $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function buscarImagenProducto($conn, $id) {
        $stmt = $conn->prepare("SELECT 
                p.imagen_portada, 
                ct.nombre AS categoria, 
                fs.nombre AS festividad, 
                fs.fecha_inicial AS festividad_inicio, 
                fs.fecha_final AS festividad_final, 
                GROUP_CONCAT(CONCAT(cl.id, ',', cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM productos p
            JOIN categorias ct ON p.idCategoria = ct.id
            LEFT JOIN festividades fs ON p.idFestividad = fs.id AND p.idFestividad != 0
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado = 1 
            AND p.visible = 1 
            AND p.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function buscarLogoProducto($conn, $id) {
        $stmt = $conn->prepare("SELECT 
                u.logo 
            FROM productos p
            LEFT JOIN universos u ON p.idUniverso = u.id AND p.idUniverso != 0
            WHERE p.estado = 1 
            AND p.visible = 1 
            AND p.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function guardarEstrellas($conn, $idProducto, $dato) {
        try {
            // Buscar la calificación existente para el producto
            $querySelect = "SELECT calificaciones_estrellas FROM productos WHERE id = ?";
            $stmtSelect = $conn->prepare($querySelect);
            $stmtSelect->bind_param("i", $idProducto);
            $stmtSelect->execute();
            $result = $stmtSelect->get_result();
    
            // Obtener el valor actual de calificaciones
            $calificaciones = '';
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $calificaciones = $row['calificaciones_estrellas'];
            }
    
            // Extraer el ID del cliente y las estrellas del dato
            preg_match('/\{(\d+):(\d+)\}/', $dato, $matches);
            if (count($matches) < 3) {
                return [
                    'title' => "¡Error!",
                    'text' => "Formato de dato inválido",
                    'icon' => "error"
                ];
            }
            $clienteId = $matches[1];
            $estrellas = $matches[2];
    
            // Si ya existen calificaciones, actualizar la correspondiente
            if ($calificaciones) {
                // Convertir el string de calificaciones en un array
                $calificacionesArray = explode(',', trim($calificaciones, '{}'));
    
                // Marcar si se actualizó
                $actualizado = false;
    
                foreach ($calificacionesArray as &$calificacion) {
                    // Si encontramos la calificación del cliente, la actualizamos
                    if (strpos($calificacion, "$clienteId:") === 0) {
                        $calificacion = "$clienteId:$estrellas";
                        $actualizado = true;
                        break;
                    }
                }
    
                // Si no se actualizó, significa que es un nuevo cliente
                if (!$actualizado) {
                    $calificacionesArray[] = "$clienteId:$estrellas";
                }
    
                // Unir el array nuevamente y actualizar la base de datos
                $nuevoValor = '{' . implode(',', $calificacionesArray) . '}';
            } else {
                // Si no hay calificaciones, simplemente asignamos el nuevo dato
                $nuevoValor = $dato;
            }
    
            // Actualizar la base de datos con el nuevo valor
            $queryUpdate = "UPDATE productos SET calificaciones_estrellas = ? WHERE id = ?";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bind_param("si", $nuevoValor, $idProducto);
    
            if ($stmtUpdate->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "Tu calificación se ha guardado",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al guardar tu calificación: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }
?>