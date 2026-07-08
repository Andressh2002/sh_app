<?php
    function guardar($conn, $slides){

        date_default_timezone_set('America/Costa_Rica');

        try{

            $conn->begin_transaction();

            $slidesActualizados = [];

            foreach($slides as $slide){

                $id             = $slide['id'] ?? null;
                $orden          = $slide['orden'];
                $url            = $slide['url'];
                $fecha_limite   = $slide['fecha_limite'];
                $estado         = $slide['estado'];

                if(empty($fecha_limite)){
                    $fecha_limite = null;
                }

                /*
                * Slide nuevo
                */

                if(empty($id)){

                    $fecha_registro = date('Y-m-d H:i:s');

                    $stmt = $conn->prepare("

                        INSERT INTO carruseles(

                            orden,
                            url,
                            fecha_limite,
                            estado,
                            fecha_registro

                        )

                        VALUES(

                            ?,
                            ?,
                            ?,
                            ?,
                            ?

                        )

                    ");

                    $stmt->bind_param(

                        "issis",

                        $orden,
                        $url,
                        $fecha_limite,
                        $estado,
                        $fecha_registro

                    );

                    $stmt->execute();

                    $slide["id"] = $conn->insert_id;
                    $slide["fecha_registro"] = $fecha_registro;

                    $slidesActualizados[] = $slide;

                    continue;

                }

                /*
                * Slide existente
                */

                $stmt = $conn->prepare("

                    UPDATE carruseles

                    SET

                        orden = ?,
                        url = ?,
                        fecha_limite = ?,
                        estado = ?

                    WHERE id = ?

                ");

                $stmt->bind_param(

                    "issii",

                    $orden,
                    $url,
                    $fecha_limite,
                    $estado,
                    $id

                );

                $stmt->execute();

                $slidesActualizados[] = $slide;

            }

            $conn->commit();

            return [
                "title"=>"¡Guardado!",
                "text"=>"El carrusel se actualizó correctamente.",
                "icon"=>"bi bi-check-circle",
                "slides" => $slidesActualizados
            ];

        }

        catch(Exception $e){

            $conn->rollback();

            return [
                "title"=>"Error",
                "text"=>$e->getMessage(),
                "icon"=>"bi bi-x-circle"
            ];

        }

    }
    
    function buscarImagen($conn, $id) {
        $query = "SELECT imagen FROM carruseles WHERE id = " . $conn->real_escape_string($id);
        $result = $conn->query($query);
        
        $imagen = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $imagen[] = $row;
            }
        }
        
        return $imagen;
    }

    function listarIds($conn) {
        $query = "
            SELECT 
                c.id
            FROM carruseles c
            WHERE c.estado = 1
            ORDER BY c.orden";
        
        $result = $conn->query($query);

        $ids = [];

        while($row = $result->fetch_assoc()){

            $ids[] = $row['id'];
        }

        return $ids;
    }

    function buscarPorId($conn, $id) {
        $stmt = $conn->prepare("
            SELECT 
                c.id, 
                c.url, 
                c.fecha_limite, 
                c.fecha_registro
            FROM carruseles c

            WHERE c.estado = 1
            AND c.id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        
        if($result->num_rows <= 0){

            return null;
        }

        $carrusel =
            $result->fetch_assoc();

        return $carrusel;
    }

    function listar($conn){

        $query = "

            SELECT

                id,
                orden,
                url,
                imagen,
                fecha_limite,
                estado,
                fecha_registro

            FROM carruseles

            WHERE estado = 1

            ORDER BY orden ASC

        ";

        $result = $conn->query($query);

        $slides = [];

        while($row = $result->fetch_assoc()){

            $slides[] = $row;

        }

        return $slides;

    }

    function insertarImagen($conn, $id, $imagen){

        try{

            $stmt = $conn->prepare("

                UPDATE carruseles

                SET imagen = ?

                WHERE id = ?

            ");

            $stmt->bind_param(
                "si",
                $imagen,
                $id
            );

            $stmt->execute();

            return[
                "title"=>"¡Guardado!",
                "text"=>"Imagen actualizada correctamente.",
                "icon"=>"bi bi-check-circle"
            ];

        }catch(mysqli_sql_exception $e){

            return[
                "title"=>"¡Error!",
                "text"=>$e->getMessage(),
                "icon"=>"bi bi-x-circle"
            ];

        }

    }
?>