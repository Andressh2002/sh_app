<?php
    function insertar($conn, $nombre, $color) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM rarezas WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "La rareza " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $query = "INSERT INTO rarezas (nombre, fecha_registro, estado, color) 
                      VALUES (?, ?, 1, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $nombre, $fecha_registro, $color);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "La rareza se ha guardado correctamente",
                    'icon' => "bi bi-check-circle"
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

    function obtener($conn, $nombre) {
        $query = "SELECT * FROM rarezas WHERE estado=1";
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $rarezas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rarezas[] = $row;
            }
        }
    
        return $rarezas;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM rarezas WHERE id = ? AND estado=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $descripcion, $color) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM rarezas WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe una rareza con ese nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $queryUpdate = "UPDATE rarezas SET 
                            nombre = ?, 
                            descripcion = ?,
                            color = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $nombre, $descripcion, $color, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La rareza se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la rareza: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE rarezas SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "La rareza se ha eliminado correctamente";
        } else {
            return "Error al eliminar la rareza: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        $query = "SELECT id, nombre, descripcion, color, estado, fecha_registro FROM rarezas WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $rarezas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rarezas[] = $row;
            }
        }
        
        return $rarezas;
    }
    
    function contar($conn, $nombre) {
        $query = "SELECT COUNT(*) as total FROM rarezas WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM rarezas WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds(
            $conn,
            $nombre,
            $orden
        ){

            $query = "
                SELECT
                    rr.id
                FROM rarezas rr
                WHERE rr.estado = 1
            ";

            if(!empty($nombre)){

                $query .= "
                    AND rr.nombre LIKE '%" .
                    $conn->real_escape_string($nombre) .
                    "%'
                ";
            }

            $columnasPermitidas = [
                'rr.nombre',
                'rr.fecha_registro'
            ];

            $formasPermitidas = [
                'ASC',
                'DESC'
            ];

            $campoOrden = 'rr.id';
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
                $campoOrden =
                    $orden['orden'];
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
                    strtoupper($orden['forma']);
            }

            $query .= "
                ORDER BY
                $campoOrden
                $formaOrden
            ";

            $result = $conn->query($query);

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
                rr.id,
                rr.nombre,
                rr.color,
                rr.estado,
                rr.fecha_registro,
                COUNT(p.id) AS total_productos
            FROM rarezas rr

            LEFT JOIN productos p
                ON p.idRareza = rr.id
                AND p.estado = 1

            WHERE rr.estado = 1
            AND rr.id = ?

            GROUP BY
                rr.id,
                rr.nombre,
                rr.estado,
                rr.fecha_registro
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

        return
            $result->fetch_assoc();
    }
?>