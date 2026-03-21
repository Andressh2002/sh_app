<?php
    function insertar($conn, $nombre, $idColores, $descripcion) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            // Verificar si ya existe un accesorio con el mismo nombre y estado = 1
            $checkQuery = "SELECT COUNT(*) as count FROM accesorios WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El accesorio " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre.",
                    'icon' => "error"
                ];
            }
    
            // Consulta SQL de inserción
            $query = "INSERT INTO accesorios (nombre, idColores, descripcion, fecha_registro, estado) 
                      VALUES (?, ?, ?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            // Aquí, se pasa el número correcto de parámetros y los tipos
            $stmt->bind_param("ssss", $nombre, $idColores, $descripcion, $fecha_registro);
    
            if ($stmt->execute()) {
                // Obtener el ID del accesorio insertado
                $producto_id = $conn->insert_id;
    
                return [
                    'title' => "¡Guardado!",
                    'text' => "El accesorio se ha guardado correctamente.",
                    'icon' => "success",
                    'producto_id' => $producto_id
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function obtener($conn, $nombre) {
        $query = "
            SELECT p.*, 
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) 
                SEPARATOR '|') AS colores
            FROM accesorios p 
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado=1
            GROUP BY p.id";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $accesorios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $accesorios[] = $row;
            }
        }
    
        return $accesorios;
    }

    function listar($conn, $nombre) {
        $query = "
            SELECT p.*, 
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) 
                SEPARATOR '|') AS colores
            FROM accesorios p
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE 1=1
            AND p.estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }

        $query .= ' GROUP BY p.id';
    
        $result = $conn->query($query);
    
        $accesorios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $accesorios[] = $row;
            }
        }
    
        return $accesorios;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("
            SELECT p.*, 
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) 
                SEPARATOR '|') AS colores
            FROM accesorios p
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.id = ?
            GROUP BY p.id
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

    function actualizar($conn, $id, $nombre, $idColores, $descripcion) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM accesorios WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe un accesorio con ese nombre",
                    'icon' => "error"
                ];
            }
    
            $queryUpdate = "UPDATE accesorios SET 
                            nombre = ?, 
                            idColores = ?, 
                            descripcion = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $nombre, $idColores, $descripcion, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El accesorio se ha actualizado correctamente",
                    'icon' => "success",
                    'producto_id' => $id // Agrega el id aquí
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el accesorio: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE accesorios SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El accesorio se ha eliminado correctamente";
        } else {
            return "Error al eliminar el accesorio: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        // Validar y sanitizar limit y offset
        $limit = is_numeric($limit) ? (int)$limit : 10;
        $offset = is_numeric($offset) ? (int)$offset : 0;
    
        // Preparar la consulta
        $query = "
            SELECT 
                p.id,
                p.nombre,
                p.descripcion,
                p.estado,
                p.fecha_registro, 
                p.idColores,
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM accesorios p
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado = 1";
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE ?";
        }
    
        $query .= " GROUP BY p.id LIMIT ? OFFSET ?";
    
        // Preparar la declaración
        $stmt = $conn->prepare($query);
    
        // Vincular parámetros
        if ($nombre !== null && $nombre !== '') {
            $nombre = '%' . $nombre . '%';
            $stmt->bind_param('sii', $nombre, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
    
        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Procesar los resultados
        $accesorios = [];
        while ($row = $result->fetch_assoc()) {
            $accesorios[] = $row;
        }
    
        $stmt->close();
        return $accesorios;
    }

    function buscarImagen($conn, $id) {
        $query = "
            SELECT 
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
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM accesorios p
            JOIN colores cl ON FIND_IN_SET(cl.id, p.idColores)
            WHERE p.estado = 1";
        
            $query .= " AND p.id =" . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        
        $accesorios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $accesorios[] = $row;
            }
        }
        
        return $accesorios;
    }
    
    function contar($conn, $nombre) {
        $query = "SELECT COUNT(*) as total FROM accesorios WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM accesorios WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function insertarImagen($conn, $id, $imagen, $idImagen) {
        try {
            $columnasPermitidas = ['imagen_color1', 'imagen_color2', 'imagen_color3', 'imagen_color4', 'imagen_color5', 'imagen_color6', 'imagen_color7', 'imagen_color8', 'imagen_color9', 'imagen_color10', 'imagen_color11', 'imagen_color12', 'imagen_color13', 'imagen_color14', 'imagen_color15', 'imagen_color16'];
            
            if (!in_array($idImagen, $columnasPermitidas)) {
                return [
                    'title' => "¡Error!",
                    'text' => "Columna no permitida",
                    'icon' => "error",
                    'value' => 0
                ];
            }
    
            $query = "UPDATE accesorios SET " . $idImagen . " = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Insertado!",
                    'text' => "La imagen se ha insertado correctamente",
                    'icon' => "success",
                    'value' => 1
                ];
            } else {
                return [
                    'title' => "¡Error!",
                    'text' => "Error al ejecutar la consulta",
                    'icon' => "error",
                    'value' => 0
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al insertar la imagen: " . $e->getMessage(),
                'icon' => "error",
                'value' => 0
            ];
        }
    }

    function listarIds($conn, $nombre, $orden) {
        $query = "
            SELECT 
                ac.id
            FROM accesorios ac
            WHERE ac.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND ac.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " GROUP BY ac.id";
        $query .= " ORDER BY " . $conn->real_escape_string($orden);
        
        $result = $conn->query($query);
        
        $datas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        
        return $datas;
    }

    function contarIds($conn, $nombre) {
        $query = "
            SELECT 
                COUNT(DISTINCT ac.id) AS total
            FROM accesorios ac
            WHERE ac.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND ac.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }

        $query .= " GROUP BY ac.id";
        
        $result = $conn->query($query);
        
        $datas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $datas[] = $row;
            }
        }
        
        return $datas;
    }

    function buscarPorId($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                ac.id,
                ac.nombre,
                ac.descripcion,
                ac.estado,
                ac.fecha_registro, 
                ac.idColores,
                GROUP_CONCAT(CONCAT(cl.codigo_color_principal, ',', cl.codigo_color_secundario, ',', cl.codigo_color_terciario, ',', cl.color_familia) SEPARATOR '|') AS colores
            FROM accesorios ac
            JOIN colores cl ON FIND_IN_SET(cl.id, ac.idColores)
            WHERE ac.estado = 1 AND ac.id = ?
        ");

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