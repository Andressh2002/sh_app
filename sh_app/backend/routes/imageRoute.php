<?php
include '../db/conection.php';
include '../controllers/imageController.php';

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'guardarImagen':
        $id       = $_POST['id']       ?? null;
        $columna  = $_POST['columna']  ?? '';
        $tabla    = $_POST['tabla']    ?? '';
        $archivo  = $_FILES['archivo'] ?? null;

        $respuesta = guardar($conn, $id, $archivo, $columna, $tabla);
        echo json_encode($respuesta);
        break;
    
    case 'buscarImagen':
        $id       = $_POST['id']       ?? null;
        $columna  = $_POST['columna']  ?? '';
        $tabla    = $_POST['tabla']    ?? '';
        $campo    = $_POST['campo']    ?? '';

        $respuesta = buscar($conn, $id, $columna, $tabla, $campo);
        echo json_encode($respuesta);
        break;

    default:
        echo json_encode([
            'title' => "¡Error!",
            'text'  => "El servidor no recibió ninguna acción",
            'icon'  => "error"
            ]);
        break;
}

$conn->close();
?>