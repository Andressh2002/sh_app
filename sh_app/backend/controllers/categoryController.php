<?php
    function insertar($conn, $nombre, $descripcion, $imagen) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM categorias WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "La categoría " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "error"
                ];
            }
    
            $query = "INSERT INTO categorias (nombre, descripcion, fecha_registro, estado, imagen) 
                      VALUES (?, ?, ?, 1, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $nombre, $descripcion, $fecha_registro, $imagen);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "La categoría se ha guardado correctamente",
                    'icon' => "success"
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

    function obtener($conn, $nombre, $isImagen) {
        $query = "";

        if ($isImagen == "false") {
            $query = "SELECT id, nombre, fecha_registro, estado, descripcion FROM categorias WHERE estado=1";
        } else {
            $query = "SELECT * FROM categorias WHERE estado=1";
        }
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $categorias = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
    
        return $categorias;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ? AND estado=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $descripcion, $imagen) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM categorias WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe una categoría con ese nombre",
                    'icon' => "error"
                ];
            }
    
            $queryUpdate = "UPDATE categorias SET 
                            nombre = ?, 
                            descripcion = ?,
                            imagen = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $nombre, $descripcion, $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La categoría se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la categoría: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE categorias SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "La categoría se ha eliminado correctamente";
        } else {
            return "Error al eliminar la categoría: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        $query = "SELECT id, nombre, descripcion, estado, fecha_registro FROM categorias WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $categorias = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        
        return $categorias;
    }

    function buscarImagen($conn, $id) {
        $query = "SELECT * FROM categorias WHERE 1=1";
        
        $query .= " AND id = " . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        
        $categorias = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        
        return $categorias;
    }
    
    function contar($conn, $nombre) {
        $query = "SELECT COUNT(*) as total FROM categorias WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM categorias WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $orden) {
        $query = "
            SELECT 
                c.id
            FROM categorias c
            WHERE c.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND c.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " GROUP BY c.id";
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
                COUNT(DISTINCT c.id) AS total
            FROM categorias c
            WHERE c.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND c.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }

        $query .= " GROUP BY c.id";
        
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
                c.id, 
                c.nombre, 
                c.descripcion, 
                c.estado, 
                c.fecha_registro 
            FROM categorias c
            WHERE c.estado=1 AND c.id=?
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