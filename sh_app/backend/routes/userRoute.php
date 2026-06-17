<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/userController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'insertar':
            if (isset($_POST['nombre']) && isset($_POST['primerApellido']) && isset($_POST['nombreUsuario']) && isset($_POST['rol'])) {
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
            if (isset($_POST['id']) && isset($_POST['primerApellido']) && isset($_POST['nombre']) && isset($_POST['nombreUsuario']) && isset($_POST['rol'])) {
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
                $rol = $_POST['rol'];

                $respuesta = actualizar($conn, $id, $nombre, $nombreUsuario, $rol, $segundoNombre, $primerApellido, $segundoApellido, $provincia, $canton, $distrito, $telefono);
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

        case 'cambiarContrasennia':
            if (isset($_POST['id']) && isset($_POST['contrasenniaActual']) && isset($_POST['contrasenniaNueva'])) {
                $id = $_POST['id'];
                $contrasenniaActual = $_POST['contrasenniaActual'];
                $contrasenniaNueva = $_POST['contrasenniaNueva'];

                $respuesta = cambiarContrasennia($conn, $id, $contrasenniaActual, $contrasenniaNueva);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar la contraseña del usuario";
            }
            break;

        case 'cambiarContrasenniaAdmin':
            if (isset($_POST['id']) && isset($_POST['contrasenniaNueva'])) {
                $id = $_POST['id'];
                $contrasenniaNueva = $_POST['contrasenniaNueva'];

                $respuesta = cambiarContrasenniaAdmin($conn, $id, $contrasenniaNueva);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar la contraseña del usuario";
            }
            break;

        case 'listarIdsAdmin':

            $nombre =
                $_POST['nombre']
                ?? '';

            $rol =
                $_POST['rol']
                ?? '';

            $orden =
                $_POST['orden']
                ?? [];

            $respuesta =
                listarIdsAdmin(
                    $conn,
                    $nombre,
                    $rol,
                    $orden
                );

            header(
                'Content-Type: application/json'
            );

            echo json_encode(
                $respuesta
            );

            break;


        case 'buscarPorIdAdmin':

            $id =
                $_POST['id']
                ?? '';

            $respuesta =
                buscarPorIdAdmin(
                    $conn,
                    $id
                );

            header(
                'Content-Type: application/json'
            );

            echo json_encode(
                $respuesta
            );

            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>