<?php
    function insertar($conn, $nombre, $descuento, $fecha_inicial, $fecha_final) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM descuentos WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El descuento " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $query = "INSERT INTO descuentos (nombre, descuento, fecha_registro, estado, fecha_inicial, fecha_final) 
                      VALUES (?, ?, ?, 1, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $nombre, $descuento, $fecha_registro, $fecha_inicial, $fecha_final);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El descuento se ha guardado correctamente",
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
        $query = "SELECT * FROM descuentos WHERE estado=1";
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $descuentos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $descuentos[] = $row;
            }
        }
    
        return $descuentos;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM descuentos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $descuento, $fecha_inicial, $fecha_final) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM descuentos WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe un descuento con ese nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $queryUpdate = "UPDATE descuentos SET 
                            nombre = ?, 
                            descuento = ?, 
                            fecha_inicial = ?,
                            fecha_final = ?
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssi", $nombre, $descuento, $fecha_inicial, $fecha_final, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El descuento se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el descuento: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE descuentos SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El descuento se ha eliminado correctamente";
        } else {
            return "Error al eliminar el descuento: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        $query = "SELECT * FROM descuentos WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $descuentos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $descuentos[] = $row;
            }
        }
        
        return $descuentos;
    }

    function listarIds(
        $conn,
        $nombre,
        $orden
    ){

        $query = "
            SELECT
                d.id
            FROM descuentos d
            WHERE d.estado = 1
        ";

        if(!empty($nombre)){

            $query .= "
                AND d.nombre LIKE '%" .
                $conn->real_escape_string($nombre) .
                "%'
            ";
        }

        $columnasPermitidas = [
            'id',
            'nombre',
            'descuento'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden = 'id';
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
            d.$campoOrden
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
                id,
                nombre,
                descuento,
                descripcion,
                fecha_inicial,
                fecha_final,
                fecha_registro
            FROM descuentos
            WHERE estado = 1
            AND id = ?
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