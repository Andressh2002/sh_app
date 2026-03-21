<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/filterController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'obtenerLista':
            $tabla = isset($_POST['tabla']) ? $_POST['tabla'] : '';
            $respuesta = obtenerLista($conn, $tabla);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
        
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>