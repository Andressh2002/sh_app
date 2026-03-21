<?php
    function insertar($conn, $festividad, $titulo, $texto, $imagen) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM carruseles WHERE titulo = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $titulo);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El carrusel con el título " . htmlspecialchars($titulo) . " ya existe. Pruebe con otro nombre",
                    'icon' => "error"
                ];
            }
    
            $query = "INSERT INTO carruseles (idFestividad, titulo, texto, imagen, estado, fecha_registro) 
                      VALUES (?, ?, ?, ?, 1, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $festividad, $titulo, $texto, $imagen, $fecha_registro);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "La carta del carrusel se ha guardado correctamente",
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

    function obtener($conn) {
        $query = "SELECT ca.*, fs.fecha_inicial AS fechaInicio, fs.fecha_final AS fechaFinal, fs.nombre AS festividad FROM carruseles ca LEFT JOIN festividades fs ON ca.idFestividad = fs.id AND ca.idFestividad != 0 WHERE ca.estado=1";
        
        $result = $conn->query($query);
        
        $carruseles = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $carruseles[] = $row;
            }
        }
        
        return $carruseles;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT ca.*, fs.nombre AS festividad FROM carruseles ca LEFT JOIN festividades fs ON ca.idFestividad = fs.id AND ca.idFestividad != 0 WHERE ca.id = ? AND ca.estado=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $festividad, $titulo, $texto, $imagen) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM carruseles WHERE titulo = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $titulo, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe una carta del carrusel con ese título",
                    'icon' => "error"
                ];
            }
    
            $queryUpdate = "UPDATE carruseles SET 
                            idFestividad = ?, 
                            titulo = ?,
                            texto = ?,
                            imagen = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssi", $festividad, $titulo, $texto, $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La carta del carrusel se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la carta del carrusel: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE carruseles SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "La carta del carrusel se ha eliminado correctamente";
        } else {
            return "Error al eliminar la carta del carrusel: " . $conn->error;
        }
    }

    function seleccionar($conn, $titulo, $festividad, $limit, $offset) {
        $query = "SELECT ca.*, fs.nombre AS festividad FROM carruseles ca LEFT JOIN festividades fs ON ca.idFestividad = fs.id AND ca.idFestividad != 0 WHERE ca.estado=1";
        
        if ($titulo !== null && $titulo !== '') {
            $query .= " AND ca.titulo LIKE '%" . $conn->real_escape_string($titulo) . "%'";
        }
        if ($festividad !== null && $festividad !== '') {
            $query .= " AND fs.nombre LIKE '%" . $conn->real_escape_string($festividad) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        
        $carruseles = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $carruseles[] = $row;
            }
        }
        
        return $carruseles;
    }
    
    function contar($conn, $titulo, $festividad) {
        $query = "SELECT COUNT(*) as total FROM carruseles ca LEFT JOIN festividades fs ON ca.idFestividad = fs.id AND ca.idFestividad != 0 WHERE ca.estado=1";
        
        if ($titulo !== null && $titulo !== '') {
            $query .= " AND ca.titulo LIKE '%" . $conn->real_escape_string($titulo) . "%'";
        }
        if ($festividad !== null && $festividad !== '') {
            $query .= " AND fs.nombre LIKE '%" . $conn->real_escape_string($festividad) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM carruseles WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }
?>