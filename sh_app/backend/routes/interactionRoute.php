<?php
include '../db/conection.php';
include '../controllers/interactionController.php';

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'guardar':
        $idCliente = $_POST['idCliente'];
        $accionInteraccion = $_POST['accionInteraccion'];
        $url = $_POST['url'];

        $respuesta = guardar($conn, $idCliente, $accionInteraccion, $url);

        echo json_encode($respuesta);
        break;

    case 'listarIds':
        $respuesta = listarIds($conn, $_POST['accionInteraccion'] ?? '', $_POST['orden'] ?? [], $_POST['limite'] ?? '10');
        
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        break;

    case 'obtener':
        $respuesta = buscarPorId($conn, $_POST['id']);
        
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        break;

    default:
        echo json_encode([
                'title' => "¡Error!",
                'text' => "El servidor no recibió ninguna acción",
                'icon' => "error"
            ]);
        break;
}

$conn->close();
?>