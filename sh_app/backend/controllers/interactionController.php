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

    function listarIds($conn, $filtros = []) {
        try {
            $query = "SELECT id FROM interacciones";
            $where = [];
            $params = [];
            $types = "";

            // --- Filtro por nombre (LIKE %texto%)
            if (!empty($filtros['accion'])) {
                $where[] = "accion LIKE ?";
                $params[] = "%" . $filtros['accion'] . "%";
                $types .= "s";
            }

            // Agregar condiciones dinámicamente
            if (!empty($where)) {
                $query .= " AND " . implode(" AND ", $where);
            }

            $columnasPermitidas = [
                'accion',
                'fecha_registro',
                'id',
            ];

            $ordenarPor = $filtros['ordenarPor'] ?? '';
            $orden = strtoupper($filtros['orden'] ?? 'ASC');

            if (in_array($ordenarPor, $columnasPermitidas)) {
                $orden = ($orden === 'DESC') ? 'DESC' : 'ASC';
                $query .= " ORDER BY $ordenarPor $orden";
            }

            $stmt = $conn->prepare($query);

            // Bind dinámico solo si hay filtros
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $ids = [];

                while ($row = $result->fetch_assoc()) {
                    $ids[] = $row['id'];
                }

                return [
                    'title' => "¡Funcionó!",
                    'text' => "Se obtuvieron los ids",
                    'icon' => "success",
                    'list' => $ids
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

    function obtener($conn, $id) {
        try {
            $query = "SELECT
                id,
                accion,
                url,
                fecha_registro
                FROM interacciones 
                WHERE id = ?;
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    return [
                        'title' => "¡Funcionó!",
                        'text' => "Se obtuvo la interacción",
                        'icon' => "success",
                        'data' => $row
                    ];
                } else {
                    return [
                        'title' => "¡Atención!",
                        'text' => "No se encontró una interacción con ese ID",
                        'icon' => "warning"
                    ];
                }
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }
?>