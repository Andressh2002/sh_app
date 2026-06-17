<?php
    include '../security/encrypt.php';

    function insertar($conn, $idProducto, $idCliente, $mensaje, $estrellas) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
        $mensaje = encryptData($mensaje);

        try {
            $query = "INSERT INTO comentarios (idProducto, idCliente, mensaje, estrellas, fecha_registro, estado) 
                      VALUES (?, ?, ?, ?, ?, 1)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $idProducto, $idCliente, $mensaje, $estrellas, $fecha_registro);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El comentario se ha guardado correctamente",
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

    function actualizar($conn, $id, $idProducto, $idCliente, $mensaje, $estrellas) {
        try {
            $queryUpdate = "UPDATE comentarios SET 
                            idProducto = ?, 
                            idCliente = ?, 
                            mensaje = ?, 
                            estrellas = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssi", $idProducto, $idCliente, $mensaje, $estrellas, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El comentario se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el comentario: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
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

    function listarIds(
        $conn,
        $nombre,
        $orden
    ){

        $query = "

            SELECT
                c.id

            FROM comentarios c

            JOIN productos p
            ON c.idProducto = p.id

            WHERE c.estado = 1

        ";

        if(!empty($nombre)){

            $query .= "

                AND p.nombre LIKE '%" .
                $conn->real_escape_string(
                    $nombre
                ) .
                "%'

            ";
        }

        $columnasPermitidas = [

            'c.id',
            'c.fecha_registro',
            'p.nombre'

        ];

        $formasPermitidas = [

            'ASC',
            'DESC'

        ];

        $campoOrden =
            'c.id';

        $formaOrden =
            'DESC';

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
            $conn->query(
                $query
            );

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

        $stmt =
            $conn->prepare("

                SELECT

                    c.id,
                    c.idProducto,
                    c.idCliente,
                    c.estrellas,
                    c.fecha_registro,

                    p.nombre AS producto,
                    u.nombre AS cliente,
                    u.segundo_nombre,
                    u.primer_apellido,
                    u.segundo_apellido,

                    c.mensaje

                FROM comentarios c

                JOIN productos p
                ON c.idProducto = p.id
                JOIN usuarios u
                ON c.idCliente = u.id

                WHERE
                    c.estado = 1
                    AND c.id = ?

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

        $comentario = $result->fetch_assoc();

        $comentario['mensaje'] = decryptData($comentario['mensaje']);
        $comentario['cliente'] = decryptData($comentario['cliente']);
        $comentario['segundo_nombre'] = decryptData($comentario['segundo_nombre']);
        $comentario['primer_apellido'] = decryptData($comentario['primer_apellido']);
        $comentario['segundo_apellido'] = decryptData($comentario['segundo_apellido']);

        return $comentario;
    }
?>