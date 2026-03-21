<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/userController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['primerApellido']) && isset($_POST['nombreUsuario']) && isset($_POST['contrasennia']) && isset($_POST['rol'])) {
                $nombre = $_POST['nombre'];
                $segundoNombre = $_POST['segundoNombre'];
                $primerApellido = $_POST['primerApellido'];
                $segundoApellido = $_POST['segundoApellido'];
                $provincia = $_POST['provincia'];
                $canton = $_POST['canton'];
                $distrito = $_POST['distrito'];
                $telefono = $_POST['telefono'];
                $nombreUsuario = $_POST['nombreUsuario'];
                $contrasennia = $_POST['contrasennia'];
                $rol = $_POST['rol'];

                $respuesta = insertar($conn, $nombre, $nombreUsuario, $contrasennia, $rol, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos";
            }
            break;

        case 'actualizar':
            if (isset($_POST['id']) && isset($_POST['primerApellido']) && isset($_POST['nombre']) && isset($_POST['nombreUsuario']) && isset($_POST['contrasennia']) && isset($_POST['rol'])) {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $segundoNombre = $_POST['segundoNombre'];
                $primerApellido = $_POST['primerApellido'];
                $segundoApellido = $_POST['segundoApellido'];
                $provincia = $_POST['provincia'];
                $canton = $_POST['canton'];
                $distrito = $_POST['distrito'];
                $telefono = $_POST['telefono'];
                $nombreUsuario = $_POST['nombreUsuario'];
                $contrasennia = $_POST['contrasennia'];
                $rol = $_POST['rol'];

                $respuesta = actualizar($conn, $id, $nombre, $nombreUsuario, $contrasennia, $rol, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar el usuario";
            }
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = eliminar($conn, $id);

            echo json_encode($respuesta);
            break;
        
        case 'obtener':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $respuesta = obtener($conn, $nombre);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
        
        case 'buscar':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscar($conn, $id);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'seleccionar':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $rol = isset($_POST['rol']) ? $_POST['rol'] : '';
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
            $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
            $respuesta = seleccionar($conn, $nombre, $rol, $limit, $offset);
            $total = contar($conn, $nombre, $rol);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;

        case 'login':
            if (isset($_POST['nombreUsuario']) && isset($_POST['contrasennia'])) {
                $nombreUsuario = $_POST['nombreUsuario'];
                $contrasennia = $_POST['contrasennia'];
        
                // Verifica las credenciales
                $usuario = login($conn, $nombreUsuario, $contrasennia);
        
                if ($usuario) {
                    // Iniciar sesión
                    ini_set('session.gc_maxlifetime', 3600); // Establecer 1 hora (3600 segundos)
                    session_set_cookie_params(3600);
                    session_start();
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];
        
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false]);
                }
            } else {
                echo "Faltan datos para iniciar sesión.";
            }
            break;

        case 'contar':
            $total = contarTodos($conn);
        
            if ($total !== 0) {
                header('Content-Type: application/json');
                echo json_encode(['total' => $total]);
            } else {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No se pudo obtener el total']);
            }
            break;

        case 'listarIds':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

            $orden = isset($_POST['orden']) ? $_POST['orden'] : '';
        
            $respuesta = listarIds($conn, $nombre, $rol, $orden);
            $total = contarIds($conn, $nombre, $rol);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta, 'total' => $total]);
            break;
    
        case 'buscarPorId':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $respuesta = buscarPorId($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode(['datos' => $respuesta]);
            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>