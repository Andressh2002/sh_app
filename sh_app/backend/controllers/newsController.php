<?php
    function insertar($conn, $titulo, $mensaje, $imagen) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $query = "INSERT INTO avisos (titulo, mensaje, imagen, fecha_registro, estado) 
                      VALUES (?, ?, ?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $titulo, $mensaje, $imagen, $fecha_registro);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El aviso se ha guardado correctamente",
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

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM avisos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $titulo, $mensaje, $imagen) {
        try {
            $queryUpdate = "UPDATE avisos SET 
                            titulo = ?, 
                            mensaje = ?, 
                            imagen = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $titulo, $mensaje, $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El aviso se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el aviso: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE avisos SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El aviso se ha eliminado correctamente";
        } else {
            return "Error al eliminar el aviso: " . $conn->error;
        }
    }

    function seleccionar($conn, $titulo, $limit, $offset) {
        $query = "SELECT * FROM avisos WHERE 1=1 AND estado=1";
        
        if ($titulo !== null && $titulo !== '') {
            $query .= " AND titulo LIKE '%" . $conn->real_escape_string($titulo) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        
        $avisos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $avisos[] = $row;
            }
        }
        
        return $avisos;
    }
    
    function contar($conn, $titulo) {
        $query = "SELECT COUNT(*) as total FROM avisos WHERE 1=1 AND estado=1";
        
        if ($titulo !== null && $titulo !== '') {
            $query .= " AND titulo LIKE '%" . $conn->real_escape_string($titulo) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM avisos WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $orden) {
        $query = "SELECT id FROM avisos WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND titulo LIKE '%" . $conn->real_escape_string($nombre) . "%'";
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
        $query = "SELECT COUNT(DISTINCT id) AS total FROM avisos WHERE estado=1";
        
            if ($nombre !== null && $nombre !== '') {
                $query .= " AND titulo LIKE '%" . $conn->real_escape_string($nombre) . "%'";
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
        $stmt = $conn->prepare("SELECT * FROM avisos WHERE estado=1 AND id=?");

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