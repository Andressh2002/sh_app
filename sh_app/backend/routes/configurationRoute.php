<?php
    include '../db/conection.php';
    include '../controllers/configurationController.php';

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'cambiarModoMantenimiento':
            if (isset($_POST['clave']) && isset($_POST['valor'])) {
                $clave = $_POST['clave'];
                $valor = $_POST['valor'];

                $respuesta = actualizar($conn, $clave, $valor);
                echo json_encode($respuesta);
            } else {
                echo "Faltan datos para actualizar la configuración";
            }
            break;

        case 'buscarConfiguracionModoMantenimiento':
            $clave = isset($_POST['clave']) ? $_POST['clave'] : '';
            $respuesta = buscar($conn, $clave);

            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>