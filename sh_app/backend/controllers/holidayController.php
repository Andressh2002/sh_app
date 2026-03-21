<?php
    function insertar($conn, $nombre, $descripcion, $fecha_inicial, $fecha_final) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM festividades WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "La festividad " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "error"
                ];
            }
    
            $query = "INSERT INTO festividades (nombre, descripcion, fecha_registro, estado, fecha_inicial, fecha_final) 
                      VALUES (?, ?, ?, 1, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $nombre, $descripcion, $fecha_registro, $fecha_inicial, $fecha_final);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "La festividad se ha guardado correctamente",
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

    function obtener($conn, $nombre) {
        $query = "SELECT * FROM festividades WHERE estado=1";
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $festividades = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $festividades[] = $row;
            }
        }
    
        return $festividades;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM festividades WHERE id = ? AND estado = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $descripcion, $fecha_inicial, $fecha_final) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM festividades WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe una festividad con ese nombre",
                    'icon' => "error"
                ];
            }
    
            $queryUpdate = "UPDATE festividades SET 
                            nombre = ?, 
                            descripcion = ?,
                            fecha_inicial = ?,
                            fecha_final = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssi", $nombre, $descripcion, $fecha_inicial, $fecha_final, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La festividad se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la festividad: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE festividades SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "La festividad se ha eliminado correctamente";
        } else {
            return "Error al eliminar la festividad: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        $query = "SELECT * FROM festividades WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $festividades = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $festividades[] = $row;
            }
        }
        
        return $festividades;
    }
    
    function contar($conn, $nombre) {
        $query = "SELECT COUNT(*) as total FROM festividades WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM festividades WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $orden) {
        $query = "
            SELECT id FROM festividades
            WHERE estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " GROUP BY id";
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
                COUNT(DISTINCT id) AS total
            FROM festividades 
            WHERE estado = 1";
        
            if ($nombre !== null && $nombre !== '') {
                $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
            }

        $query .= " GROUP BY id";
        
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
            SELECT * FROM festividades
            WHERE estado=1 AND id=?
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