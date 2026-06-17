<?php
    function guardar($conn, $idCliente, $accion, $url) {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $query = "INSERT INTO interacciones (idCliente, accion, url, fecha_registro)
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isss", 
                $idCliente, $accion, $url, $fecha_registro
            );
    
            if ($stmt->execute()) {
                $idRareza = $conn->insert_id;
                return [
                    'title' => "¡Guardado!",
                    'text' => "La interacción se guardó",
                    'icon' => "success",
                    'id' => $idRareza
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

    function listarIds(
        $conn,
        $accion = '',
        $orden = [],
        $limite = 10
    ){

        $query = "
            SELECT
                i.id
            FROM interacciones i
            WHERE 1 = 1
        ";

        if(!empty($accion)){

            $query .= "
                AND i.accion LIKE '%" .
                $conn->real_escape_string($accion) .
                "%'
            ";
        }

        $columnasPermitidas = [
            'i.id',
            'i.accion',
            'i.fecha_registro'
        ];

        $formasPermitidas = [
            'ASC',
            'DESC'
        ];

        $campoOrden =
            in_array(
                $orden['orden'] ?? '',
                $columnasPermitidas
            )
            ? $orden['orden']
            : 'i.id';

        $formaOrden =
            in_array(
                strtoupper($orden['forma'] ?? ''),
                $formasPermitidas
            )
            ? strtoupper($orden['forma'])
            : 'DESC';

        $query .= "
            ORDER BY
            $campoOrden
            $formaOrden
        ";

        if($limite !== 'todos'){

            $limite =
                intval(
                    $limite
                );

            if(
                in_array(
                    $limite,
                    [10, 20, 50, 100]
                )
            ){

                $query .= "
                    LIMIT
                    $limite
                ";
            }
        }

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
                    i.id,
                    i.accion,
                    i.url,
                    i.fecha_registro,
                    u.nombre_usuario AS cliente

                FROM interacciones i

                LEFT JOIN usuarios u
                    ON u.nombre_usuario = i.idCliente

                WHERE i.id = ?

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
            $result
                ->fetch_assoc();
    }
?>