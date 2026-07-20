<?php

function toggleFavorito($conn, $idCliente, $idProducto){

    if(!$idCliente || !$idProducto){
        return [
            'success' => false,
            'favorito' => false,
            'title' => "Error",
            'text' => "Datos incompletos",
            'icon' => "bi bi-x-circle"
        ];
    }

    // ¿Ya existe?

    $sql = "
        SELECT id
        FROM favoritos
        WHERE
            idCliente = ?
        AND
            idProducto = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idCliente, $idProducto);
    $stmt->execute();

    $resultado = $stmt->get_result();

    //==========================================
    // ELIMINAR FAVORITO
    //==========================================

    if($resultado->num_rows > 0){

        $sql = "
            DELETE
            FROM favoritos
            WHERE
                idCliente = ?
            AND
                idProducto = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idCliente, $idProducto);

        if($stmt->execute()){

            return [
                'success' => true,
                'favorito' => false,
                'title' => "Favorito eliminado",
                'text' => "El producto ya no está en favoritos",
                'icon' => "bi bi-heart"
            ];

        }

    }

    //==========================================
    // INSERTAR FAVORITO
    //==========================================

    $sql = "
        INSERT INTO favoritos(
            idCliente,
            idProducto
        )
        VALUES(
            ?,
            ?
        )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idCliente, $idProducto);

    if($stmt->execute()){

        return [
            'success' => true,
            'favorito' => true,
            'title' => "Favorito agregado",
            'text' => "Producto agregado a favoritos",
            'icon' => "bi bi-heart-fill"
        ];

    }

    //==========================================
    // ERROR
    //==========================================

    return [
        'success' => false,
        'favorito' => false,
        'title' => "Error",
        'text' => $stmt->error,
        'icon' => "bi bi-x-circle"
    ];

}