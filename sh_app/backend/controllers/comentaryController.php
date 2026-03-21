<?php
    include '../security/encrypt.php';

    function insertar($conn, $idProducto, $idCliente, $mensaje) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
        $mensaje = encryptData($mensaje);

        try {
            $query = "INSERT INTO comentarios (idProducto, idCliente, mensaje, fecha_registro, estado) 
                      VALUES (?, ?, ?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $idProducto, $idCliente, $mensaje, $fecha_registro);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El comentario se ha guardado correctamente",
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
        $stmt = $conn->prepare("SELECT * FROM comentarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $idProducto, $idCliente, $mensaje) {
        try {
            $queryUpdate = "UPDATE comentarios SET 
                            idProducto = ?, 
                            idCliente = ?, 
                            mensaje = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $idProducto, $idCliente, $mensaje, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El comentario se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el comentario: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE comentarios SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El comentario se ha eliminado correctamente";
        } else {
            return "Error al eliminar el comentario: " . $conn->error;
        }
    }

    function seleccionar($conn, $producto, $limit, $offset) {
        $query = "SELECT c.*, p.nombre AS producto
              FROM comentarios c
              JOIN productos p ON c.idProducto = p.id
              WHERE c.estado = 1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $comentarios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['mensaje'] = decryptData($row['mensaje']);
                $comentarios[] = $row;
            }
        }
        
        return $comentarios;
    }

    function seleccionarPorIdProducto($conn, $idProducto, $limit, $offset) {
        $query = "SELECT * FROM comentarios WHERE 1=1 AND estado=1";
        
        if ($idProducto !== null && $idProducto !== '') {
            $query .= " AND idProducto = " . $conn->real_escape_string($idProducto);
        }
    
        $query .= " LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($query);
        
        $comentarios = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['mensaje'] = decryptData($row['mensaje']);
                $comentarios[] = $row;
            }
        }
        
        return $comentarios;
    }
    
    function contar($conn, $producto) {
        $query = "SELECT COUNT(*) as total FROM comentarios c
              JOIN productos p ON c.idProducto = p.id
              WHERE c.estado = 1";
        
        if ($producto !== null && $producto !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($producto) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarPorIdProducto($conn, $idProducto) {
        $query = "SELECT COUNT(*) as total FROM comentarios WHERE 1=1 AND estado=1";

        if ($idProducto !== null && $idProducto !== '') {
            $query .= " AND idProducto = " . $conn->real_escape_string($idProducto);
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM comentarios WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $orden) {
        $query = "
            SELECT c.id
            FROM comentarios c
            JOIN productos p ON c.idProducto = p.id
            WHERE c.estado = 1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " GROUP BY c.id";
        if ($conn->real_escape_string($orden) == "pe.id") {
            $query .= " ORDER BY " . $conn->real_escape_string($orden) . " DESC";
        } else {
            $query .= " ORDER BY " . $conn->real_escape_string($orden);
        }
        
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
            FROM comentarios c
            JOIN productos p ON c.idProducto = p.id
            WHERE c.estado = 1";
        
            if ($nombre !== null && $nombre !== '') {
                $query .= " AND p.nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
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
            SELECT c.*, p.nombre AS producto
            FROM comentarios c
            JOIN productos p ON c.idProducto = p.id
            WHERE c.estado = 1 AND c.id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        
        $datas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['mensaje'] = decryptData($row['mensaje']);
                $datas[] = $row;
            }
        }
        
        return $datas;
    }
?>