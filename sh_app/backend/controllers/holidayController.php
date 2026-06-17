<?php
    function insertar($conn, $nombre, $fecha_inicial, $fecha_final) {
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
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $query = "INSERT INTO festividades (nombre, fecha_registro, estado, fecha_inicial, fecha_final) 
                      VALUES (?, ?, 1, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $nombre, $fecha_registro, $fecha_inicial, $fecha_final);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "La festividad se ha guardado correctamente",
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

    function actualizar($conn, $id, $nombre, $fecha_inicial, $fecha_final) {
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
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $queryUpdate = "UPDATE festividades SET 
                            nombre = ?, 
                            fecha_inicial = ?,
                            fecha_final = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("sssi", $nombre, $fecha_inicial, $fecha_final, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La festividad se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la festividad: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
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

    function listarIds(
        $conn,
        $nombre,
        $orden
    ){

        $query = "
            SELECT
                f.id
            FROM festividades f
            WHERE f.estado = 1
        ";

        if(!empty($nombre)){

            $query .= "
                AND f.nombre LIKE '%" .
                $conn->real_escape_string($nombre) .
                "%'
            ";
        }

        $columnasPermitidas = [
            'f.nombre',
            'f.fecha_registro',
            'f.fecha_inicial',
            'f.fecha_final'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden = 'f.id';
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
                strtoupper(
                    $orden['forma']
                ),
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

        $result =
            $conn->query($query);

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
                f.id,
                f.nombre,
                f.fecha_inicial,
                f.fecha_final,
                f.fecha_registro,
                f.estado,
                COUNT(p.id) AS total_productos
            FROM festividades f

            LEFT JOIN productos p
                ON p.idFestividad = f.id
                AND p.estado = 1

            WHERE f.estado = 1
            AND f.id = ?

            GROUP BY
                f.id,
                f.nombre,
                f.estado,
                f.fecha_registro
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

        return $result->fetch_assoc();
    }
?>