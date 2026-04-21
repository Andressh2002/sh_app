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
        $filtros = $_POST['filtros'] ?? [];
        $respuesta = listarIds($conn, $filtros);

        echo json_encode($respuesta);
        break;

    case 'obtener':
        $id = $_POST['id'];
        $respuesta = obtener($conn, $id);

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