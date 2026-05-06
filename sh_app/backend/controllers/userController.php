<?php
    include '../security/encrypt.php';

    function insertar($conn, $nombre, $nombreUsuario, $contrasennia, $rol, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono) {
        $nombre = encryptData($nombre);
        $segundoNombre = encryptData($segundoNombre);
        $primerApellido = encryptData($primerApellido);
        $segundoApellido = encryptData($segundoApellido);
        $contrasennia = password_hash($contrasennia, PASSWORD_DEFAULT);
        $provincia = encryptData($provincia);
        $canton = encryptData($canton);
        $distrito = encryptData($distrito);
        $telefono = encryptData($telefono);
        
        date_default_timezone_set('America/Costa_Rica');
        $fecha_registro = date('Y-m-d H:i:s');
    
        try {
            $checkQuery = "SELECT COUNT(*) as count FROM usuarios WHERE nombre_usuario = ? AND estado = 1";
            $stmtCheck = $conn->prepare($checkQuery);
            $stmtCheck->bind_param("s", $nombreUsuario);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
    
            if ($row['count'] > 0) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "El usuario " . htmlspecialchars($nombreUsuario) . " ya existe. Pruebe con otro nombre de usuario",
                    'icon' => "error"
                ];
            }
            
            $query = "INSERT INTO usuarios (nombre, nombre_usuario, contrasennia, rol, fecha_registro, estado, segundo_nombre, primer_apellido, segundo_apellido, provincia, canton, distrito, telefono) 
                      VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssssssss", $nombre, $nombreUsuario, $contrasennia, $rol, $fecha_registro, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Guardado!",
                    'text' => "El usuario se ha guardado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                return [
                    'title' => "¡No se guardó!",
                    'text' => "Este usuario ya existe. Pruebe con otro nombre de usuario",
                    'icon' => "error"
                ];
            }
            return [
                'title' => "¡Error!",
                'text' => "Ha ocurrido un error: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function obtener($conn, $nombre) {
        $query = "SELECT * FROM usuarios WHERE estado=1 AND nombre LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        $result = $conn->query($query);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['nombre'] = decryptData($row['nombre']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                $usuarios[] = $row;
            }
        }
        
        return $usuarios;
    }

    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND estado=1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $row['nombre'] = decryptData($row['nombre']);
            $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
            $row['primer_apellido'] = decryptData($row['primer_apellido']);
            $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
            $row['provincia'] = decryptData($row['provincia']);
            $row['canton'] = decryptData($row['canton']);
            $row['distrito'] = decryptData($row['distrito']);
            $row['telefono'] = decryptData($row['telefono']);
            return $row;
        } else {
            return null;
        }
    }

    function actualizar($conn, $id, $nombre, $nombreUsuario, $rol, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono) {
        $nombre = encryptData($nombre);
        $segundoNombre = encryptData($segundoNombre);
        $primerApellido = encryptData($primerApellido);
        $segundoApellido = encryptData($segundoApellido);
        $provincia = encryptData($provincia);
        $canton = encryptData($canton);
        $distrito = encryptData($distrito);
        $telefono = encryptData($telefono);

        try {
            $queryUpdate = "UPDATE usuarios SET 
                            nombre = ?, 
                            segundo_nombre = ?, 
                            primer_apellido = ?, 
                            segundo_apellido = ?, 
                            provincia = ?, 
                            canton = ?, 
                            distrito = ?, 
                            telefono = ?, 
                            nombre_usuario = ?, 
                            rol = ? 
                            WHERE id = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ssssssssssi", $nombre, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono, $nombreUsuario, $rol, $id);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "El usuario se ha actualizado correctamente",
                    'icon' => "success"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar el usuario: " . $e->getMessage(),
                'icon' => "error"
            ];
        }
    }

    function eliminar($conn, $id) {
        $id = $conn->real_escape_string($id);
        $rol = encryptData('Administrador');

        // Consulta para verificar si el usuario tiene el rol de 'Administrador'
        $checkQuery = "SELECT COUNT(*) AS total FROM usuarios WHERE rol = '" . $rol . "' AND estado = 1";
        $result = $conn->query($checkQuery);
    
        if ($result) {
            $row = $result->fetch_assoc();
            
            // Si solo hay un administrador, verificar si es el que se desea eliminar
            if ($row['total'] == 1) {
                $adminQuery = "SELECT rol FROM usuarios WHERE id='$id' AND rol = ". $rol ." AND estado = 1";
                $adminResult = $conn->query($adminQuery);
                
                if ($adminResult && $adminResult->num_rows > 0) {
                    return [
                        'title' => "¡Proceso evitado!",
                        'text' => "No se puede eliminar el único usuario con rol de Administrador",
                        'icon' => "error"
                    ];
                }
            }
        }
    
        // Proceder con la eliminación si no es el único Administrador
        $query = "UPDATE usuarios SET estado=0 WHERE id='$id'";
    
        if ($conn->query($query)) {
            return [
                'title' => "¡Eliminado!",
                'text' => "El usuario se ha eliminado correctamente",
                'icon' => "success"
            ];
        } else {
            return [
                'title' => "¡ERROR!",
                'text' => "Error al actualizar el usuario: " . $conn->error,
                'icon' => "error"
            ];
        }
    }

    function seleccionar($conn, $nombre, $rol, $limit, $offset) {
        // Realiza la consulta inicial solo con filtros no encriptados
        $query = "SELECT * FROM usuarios WHERE estado=1 LIMIT $limit OFFSET $offset";
        $result = $conn->query($query);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencripta todos los campos necesarios
                $row['nombre'] = decryptData($row['nombre']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);
                
                // Filtra los resultados en PHP después de desencriptar
                // Filtrar por nombre si se proporcionó
                if (!empty($nombre) && stripos($row['nombre'], $nombre) === false) {
                    continue; // Saltar si no coincide
                }
                
                // Filtrar por rol si se proporcionó
                if (!empty($rol) && stripos($row['rol'], $rol) === false) {
                    continue; // Saltar si no coincide
                }

                if ($row['rol'] == "Invitado") {
                    continue;
                }
                
                $usuarios[] = $row;
            }
        }
        
        return $usuarios;
    }
    
    function contar($conn, $nombre, $rol) {
        // Ejecuta una consulta solo con filtros no encriptados
        $query = "SELECT * FROM usuarios WHERE estado=1";
        $result = $conn->query($query);
    
        $total = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencripta solo los campos necesarios para el filtrado
                $nombreDesencriptado = decryptData($row['nombre']);
                
                // Filtrar por nombre si se proporcionó
                if (!empty($nombre) && stripos($nombreDesencriptado, $nombre) === false) {
                    continue; // Saltar si no coincide
                }
                
                // Filtrar por rol si se proporcionó
                if (!empty($rol) === false) {
                    continue; // Saltar si no coincide
                }

                if ($row['rol'] == "Invitado") {
                    continue;
                }
                
                // Incrementar el contador solo si pasan todos los filtros
                $total++;
            }
        }
        
        return $total;
    }

    function login($conn, $nombreUsuario, $contrasennia) {
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND estado = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nombreUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            // Verificar contraseña (HASH)
            if (password_verify($contrasennia, $row['contrasennia'])) {

                // Desencriptar datos necesarios
                $row['nombre'] = decryptData($row['nombre']);
                $row['segundo_nombre'] = decryptData($row['segundo_nombre']);
                $row['primer_apellido'] = decryptData($row['primer_apellido']);
                $row['segundo_apellido'] = decryptData($row['segundo_apellido']);
                $row['provincia'] = decryptData($row['provincia']);
                $row['canton'] = decryptData($row['canton']);
                $row['distrito'] = decryptData($row['distrito']);
                $row['telefono'] = decryptData($row['telefono']);

                // Validar rol
                if ($row['rol'] == "Cliente" || $row['rol'] == "Administrador") {
                    return $row;
                }
            }
        }

        return null;
    }

    function contarTodos($conn) {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE estado=1";
    
        if ($result = $conn->query($query)) {
            if ($row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }

        return 0;
    }

    function listarIds($conn, $nombre, $rol, $orden) {
        $query = "SELECT id, nombre, rol, nombre_usuario FROM usuarios WHERE estado=1 GROUP BY id";
        $result = $conn->query($query);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar campos
                $row['nombre'] = decryptData($row['nombre']);
    
                // Filtros
                if (!empty($nombre) && stripos($row['nombre'], $nombre) === false) {
                    continue;
                }
    
                if (!empty($rol) && stripos($row['rol'], $rol) === false) {
                    continue;
                }
    
                if ($row['rol'] === "Invitado") {
                    continue;
                }
    
                $usuarios[] = $row;
            }
        }

        $ordenValido = ['nombre', 'rol', 'nombre_usuario', 'primer_apellido', 'telefono', 'id'];
        if (!empty($orden) && in_array($orden, $ordenValido)) {
            usort($usuarios, function($a, $b) use ($orden) {
                return strcasecmp($a[$orden], $b[$orden]);
            });
        }
    
        return $usuarios;
    }

    function contarIds($conn, $nombre, $rol) {
        $query = "SELECT COUNT(DISTINCT id) AS total, nombre, rol, nombre_usuario FROM usuarios WHERE estado=1 GROUP BY id";
        $result = $conn->query($query);
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar campos
                $row['nombre'] = decryptData($row['nombre']);
    
                // Filtros
                if (!empty($nombre) && stripos($row['nombre'], $nombre) === false) {
                    continue;
                }
    
                if (!empty($rol) && stripos($row['rol'], $rol) === false) {
                    continue;
                }
    
                if ($row['rol'] === "Invitado") {
                    continue;
                }
    
                $usuarios[] = $row;
            }
        }
    
        return $usuarios;
    }

    function buscarPorId($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE estado=1 AND id=?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Desencriptar campos
                $row['nombre'] = decryptData($row['nombre']);
    
                // Filtros
                if (!empty($nombre) && stripos($row['nombre'], $nombre) === false) {
                    continue;
                }
    
                if (!empty($rol) && stripos($row['rol'], $rol) === false) {
                    continue;
                }
    
                if ($row['rol'] === "Invitado") {
                    continue;
                }
    
                $usuarios[] = $row;
            }
        }
    
        return $usuarios;
    }
?>