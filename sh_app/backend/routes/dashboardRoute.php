<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/dashboardController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    // Las variables de filtros
    $anioInicial = isset($_POST['anioInicial']) ? $_POST['anioInicial'] : '';
    $anioFinal = isset($_POST['anioFinal']) ? $_POST['anioFinal'] : '';
    $mesInicial = isset($_POST['mesInicial']) ? $_POST['mesInicial'] : '';
    $mesFinal = isset($_POST['mesFinal']) ? $_POST['mesFinal'] : '';
    $diaInicial = isset($_POST['diaInicial']) ? $_POST['diaInicial'] : '';
    $diaFinal = isset($_POST['diaFinal']) ? $_POST['diaFinal'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $rareza = isset($_POST['rareza']) ? $_POST['rareza'] : '';
    $universo = isset($_POST['universo']) ? $_POST['universo'] : '';
    $orden = isset($_POST['orden']) ? $_POST['orden'] : 'DESC';

    switch ($accion) {
        case 'buscarProductos':
            $respuesta = buscarProductos($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarGananciasProductos':
            $respuesta = buscarGananciasPorProducto($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarGananciasTiempo':
            $respuesta = buscarGananciasPorTiempo($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarTotales':
            $respuesta = buscarTotales($conn);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>