<?php
    function insertar($conn, $nombre, $descripcion, $color1, $color2, $color3, $familia) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM colores WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El color " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "error"
                ];
            }
    
            $query = "INSERT INTO colores (nombre, descripcion, codigo_color_principal, codigo_color_secundario, codigo_color_terciario, color_familia, fecha_registro, estado) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $nombre, $descripcion, $color1, $color2, $color3, $familia, $fecha_registro);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El color se ha guardado correctamente",
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

    function obtener($conn, $nombre, $familia) {
        $query = "SELECT * FROM colores WHERE estado=1";
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($familia !== null && $familia !== '') {
            $query .= " AND color_familia LIKE '%" . $conn->real_escape_string($familia) . "%'";
        }
    
        $result = $conn->query($query);
    
        $colores = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $colores[] = $row;
            }
        }
    
        return $colores;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM colores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $descripcion, $color1, $color2, $color3, $familia) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM colores WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe un color con ese nombre",
                    'icon' => "error"
                ];
            }
    
            $queryUpdate = "UPDATE colores SET 
                            nombre = ?, 
                            descripcion = ?, 
                            codigo_color_principal = ?, 
                            codigo_color_secundario = ?, 
                            codigo_color_terciario = ?, 
                            color_familia = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssssi", $nombre, $descripcion, $color1, $color2, $color3, $familia, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El color se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el color: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE colores SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El color se ha eliminado correctamente";
        } else {
            return "Error al eliminar el color: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $familia, $limit, $offset) {
        $query = "SELECT * FROM colores WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($familia !== null && $familia !== '') {
            $query .= " AND color_familia LIKE '%" . $conn->real_escape_string($familia) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $colores = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $colores[] = $row;
            }
        }
        
        return $colores;
    }
    
    function contar($conn, $nombre, $familia) {
        $query = "SELECT COUNT(*) as total FROM colores WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($familia !== null && $familia !== '') {
            $query .= " AND color_familia LIKE '%" . $conn->real_escape_string($familia) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM colores WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $familia, $orden) {
        $query = "
            SELECT id FROM colores
            WHERE estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
        if ($familia !== null && $familia !== '') {
            $query .= " AND color_familia LIKE '%" . $conn->real_escape_string($familia) . "%'";
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

    function contarIds($conn, $nombre, $familia) {
        $query = "
            SELECT 
                COUNT(DISTINCT id) AS total
            FROM colores 
            WHERE estado = 1";
        
            if ($nombre !== null && $nombre !== '') {
                $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
            }
            if ($familia !== null && $familia !== '') {
                $query .= " AND color_familia LIKE '%" . $conn->real_escape_string($familia) . "%'";
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
            SELECT * FROM colores
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