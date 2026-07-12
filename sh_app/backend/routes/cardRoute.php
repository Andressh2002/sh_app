<?php
    // Incluir la conexión a la base de datos
    include '../db/conection.php';

    // Incluir el archivo con las operaciones SQL
    include '../controllers/cardController.php';

    // Verificar la acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    switch ($accion) {
        case 'contarCategorias':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        
            $respuesta = contarCategorias($conn, $nombre);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'contarUniversos':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        
            $respuesta = contarUniversos($conn, $nombre);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
        
        case 'contarProductos':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $idCategorias = isset($_POST['idCategorias']) ? $_POST['idCategorias'] : [];
            $precio = isset($_POST['precio']) ? $_POST['precio'] : [null, null];
            $idFestividades = isset($_POST['idFestividades']) ? $_POST['idFestividades'] : [];
            $idRarezas = isset($_POST['idRarezas']) ? $_POST['idRarezas'] : [];
            $idUniversos = isset($_POST['idUniversos']) ? $_POST['idUniversos'] : [];
            $limite = isset($_POST['limite']) ? $_POST['limite'] : '';
            $filtros = [$nombre, $precio, $idCategorias, $idFestividades, $idRarezas, $idUniversos];

            $respuesta = contarProductos($conn, $filtros, $limite);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
    
        case 'buscarCategoria':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarCategoria($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarUniverso':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarUniverso($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarProducto':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarProducto($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'guardarEstrellas':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $dato = isset($_POST['dato']) ? $_POST['dato'] : '';
        
            $respuesta = guardarEstrellas($conn, $id, $dato);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarImagenCategoria':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarImagenCategoria($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarImagenUniverso':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarImagenUniverso($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarLogoUniverso':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarLogoUniverso($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarImagenProducto':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarImagenProducto($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;

        case 'buscarLogoProducto':
            $id = isset($_POST['id']) ? $_POST['id'] : '';
        
            $respuesta = buscarLogoProducto($conn, $id);
        
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            break;
                                
        default:
            echo 'ERROR, falta una acción';
            break;
    }

    $conn->close();
?>