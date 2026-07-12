<?php
    function insertar($conn, $nombre) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM universos WHERE nombre = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombre);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El universo " . htmlspecialchars($nombre) . " ya existe. Pruebe con otro nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $query = "INSERT INTO universos (nombre, fecha_registro, estado) 
                      VALUES (?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $nombre, $fecha_registro);
    
            if ($stmt->execute()) {
                $universo_id = $conn->insert_id;
                return [
                    'title' => "¡Guardado!",
                    'text' => "El universo se ha guardado correctamente",
                    'icon' => "bi bi-check-circle",
                    'universo_id' => $universo_id
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

    function obtener($conn, $nombre, $isImagen) {
        $query = "";

        if ($isImagen == "false") {
            $query = "SELECT id, nombre, fecha_registro, estado, descripcion FROM categorias WHERE estado=1";
        } else {
            $query = "SELECT * FROM universos WHERE estado=1";
        }
    
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
    
        $universos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $universos[] = $row;
            }
        }
    
        return $universos;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM universos WHERE id = ? AND estado=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre) {
        try {
            $queryCheck = "SELECT COUNT(*) AS total FROM universos WHERE nombre = ? AND estado = 1 AND id != ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("si", $nombre, $id);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
    
            if ($rowCheck['total'] > 0) {
                return [
                    'title' => "¡No se actualizó!",
                    'text' => "Ya existe un universo con ese nombre",
                    'icon' => "bi bi-x-circle"
                ];
            }
    
            $queryUpdate = "UPDATE universos SET 
                            nombre = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("si", $nombre, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El universo se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle",
                    'universo_id' => $id
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el universo: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }

    function eliminar($conn, $id) {

        $id = $conn->real_escape_string($id);
    
        $query = "UPDATE universos SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return "El universo se ha eliminado correctamente";
        } else {
            return "Error al eliminar el universo: " . $conn->error;
        }
    }

    function seleccionar($conn, $nombre, $limit, $offset) {
        $query = "SELECT id, nombre, descripcion, estado, fecha_registro FROM universos WHERE estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $query .= " LIMIT $limit OFFSET $offset"; // Añadido para la paginación
        
        $result = $conn->query($query);
        
        $universos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $universos[] = $row;
            }
        }
        
        return $universos;
    }

    function buscarImagen($conn, $id) {
        $query = "SELECT imagen FROM universos WHERE id = " . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        
        $universos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $universos[] = $row;
            }
        }
        
        return $universos;
    }

    function buscarLogo($conn, $id) {
        $query = "SELECT logo FROM universos WHERE id = " . $conn->real_escape_string($id);
        
        $result = $conn->query($query);
        
        $universos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $universos[] = $row;
            }
        }
        
        return $universos;
    }
    
    function contar($conn, $nombre) {
        $query = "SELECT COUNT(*) as total FROM universos WHERE 1=1 AND estado=1";
        
        if ($nombre !== null && $nombre !== '') {
            $query .= " AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        }
    
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM universos WHERE estado=1";
    
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
                un.id
            FROM universos un
            WHERE un.estado = 1
        ";

        if(!empty($nombre)){

            $query .= "
                AND un.nombre LIKE '%" .
                $conn->real_escape_string($nombre) .
                "%'
            ";
        }

        $columnasPermitidas = [
            'un.nombre',
            'un.fecha_registro'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden = 'un.id';
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

    function buscarPorId($conn, $id) {

        $stmt = $conn->prepare("
            SELECT
                un.id,
                un.nombre,
                un.estado,
                un.fecha_registro,
                COUNT(p.id) AS total_productos
            FROM universos un

            LEFT JOIN productos p
                ON p.idUniverso = un.id
                AND p.estado = 1

            WHERE un.estado = 1
            AND un.id = ?

            GROUP BY
                un.id,
                un.nombre,
                un.estado,
                un.fecha_registro
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    function insertarImagen($conn, $id, $imagen, $campo) {
        try {
            $columnasPermitidas = ['imagen', 'logo'];
            
            if (!in_array($campo, $columnasPermitidas)) {
                return [
                    'title' => "¡Error!",
                    'text' => "Columna no permitida",
                    'icon' => "bi bi-x-circle",
                    'value' => 0
                ];
            }
    
            $query = "UPDATE universos SET " . $campo . " = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $imagen, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Insertado!",
                    'text' => "La imagen se ha insertado correctamente",
                    'icon' => "bi bi-check-circle",
                    'value' => 1
                ];
            } else {
                return [
                    'title' => "¡Error!",
                    'text' => "Error al ejecutar la consulta",
                    'icon' => "bi bi-x-circle",
                    'value' => 0
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al insertar la imagen: " . $e->getMessage(),
                'icon' => "bi bi-x-circle",
                'value' => 0
            ];
        }
    }
?>