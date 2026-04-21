<?php
function guardar($conn, $id, $imagenFile, $colImagen, $tabla)
{
    try {
        $tablasPermitidas = ["productos", "accesorios", "colores", "categorias", "universos"];
        $columnasPermitidas = [
            'imagen',
            'imagen_portada',
            'imagen_galeria',
            'imagen_galeria1',
            'imagen_galeria2',
            'imagen_galeria3',
            'imagen_galeria4',
            'imagen_color1',
            'imagen_color2',
            'imagen_color3',
            'imagen_color4',
            'imagen_color5',
            'imagen_color6',
            'imagen_color7',
            'imagen_color8',
            'imagen_color9',
            'imagen_color10',
            'imagen_color11',
            'imagen_color12',
            'imagen_color13',
            'imagen_color14',
            'imagen_color15',
            'imagen_color16',
            'imagen_color17',
            'imagen_color18',
            'imagen_color19',
            'imagen_color20',
            'imagen_accesorio_color1',
            'imagen_accesorio_color2',
            'imagen_accesorio_color3',
            'imagen_accesorio_color4',
            'imagen_accesorio_color5',
            'imagen_accesorio_color6',
            'imagen_accesorio_color7',
            'imagen_accesorio_color8',
            'imagen_accesorio_color9',
            'imagen_accesorio_color10',
            'imagen_accesorio_color11',
            'imagen_accesorio_color12',
            'imagen_accesorio_color13',
            'imagen_accesorio_color14',
            'imagen_accesorio_color15',
            'imagen_accesorio_color16',
        ];

        if (!in_array($tabla, $tablasPermitidas)) {
            return ['icon' => "error", 'text' => "Tabla no permitida"];
        }

        if (!in_array($colImagen, $columnasPermitidas)) {
            return ['icon' => "error", 'text' => "Columna no permitida"];
        }

        // no viene imagen, entonces guardar NULL
        if (!$imagenFile || $imagenFile['error'] !== UPLOAD_ERR_OK) {

            $query = "UPDATE $tabla SET $colImagen = NULL WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                return [
                    'icon' => "success",
                    'text' => "Imagen eliminada correctamente"
                ];
            }

            return ['icon' => "error", 'text' => "No se pudo eliminar la imagen"];
        }

        // viene imagen, entonces guardar binario
        $fileContent = file_get_contents($imagenFile['tmp_name']);

        $query = "UPDATE $tabla SET $colImagen = ? WHERE id = ?";
        $stmt = $conn->prepare($query);

        $null = NULL;
        $stmt->bind_param("bi", $null, $id);
        $stmt->send_long_data(0, $fileContent);

        if ($stmt->execute()) {
            return [
                'icon' => "success",
                'text' => "Imagen actualizada correctamente"
            ];
        }

        return ['icon' => "error", 'text' => "No se pudo actualizar la imagen"];

    } catch (mysqli_sql_exception $e) {
        return [
            'icon' => "error",
            'text' => "Error SQL: " . $e->getMessage()
        ];
    }
}


function buscar($conn, $id, $colImagen, $tabla, $rowCampo)
{
    try {
        $tablasPermitidas = ["productos", "accesorios", "colores", "categorias", "universos"];
        $columnasPermitidas = [
            'imagen',
            'imagen_portada',
            'imagen_galeria',
            'imagen_galeria1',
            'imagen_galeria2',
            'imagen_galeria3',
            'imagen_galeria4',
            'imagen_color1',
            'imagen_color2',
            'imagen_color3',
            'imagen_color4',
            'imagen_color5',
            'imagen_color6',
            'imagen_color7',
            'imagen_color8',
            'imagen_color9',
            'imagen_color10',
            'imagen_color11',
            'imagen_color12',
            'imagen_color13',
            'imagen_color14',
            'imagen_color15',
            'imagen_color16',
            'imagen_color17',
            'imagen_color18',
            'imagen_color19',
            'imagen_color20',
            'imagen_accesorio_color1',
            'imagen_accesorio_color2',
            'imagen_accesorio_color3',
            'imagen_accesorio_color4',
            'imagen_accesorio_color5',
            'imagen_accesorio_color6',
            'imagen_accesorio_color7',
            'imagen_accesorio_color8',
            'imagen_accesorio_color9',
            'imagen_accesorio_color10',
            'imagen_accesorio_color11',
            'imagen_accesorio_color12',
            'imagen_accesorio_color13',
            'imagen_accesorio_color14',
            'imagen_accesorio_color15',
            'imagen_accesorio_color16',
        ];
        $camposPermitidos = [
            'id',
            'idProducto',
        ];

        if (!in_array($tabla, $tablasPermitidas)) {
            return ['title' => "¡Error!", 'text' => "Tabla no permitida", 'icon' => "error", 'value' => 0];
        }
        if (!in_array($colImagen, $columnasPermitidas)) {
            return ['title' => "¡Error!", 'text' => "Columna no permitida", 'icon' => "error", 'value' => 0];
        }
        if (!in_array($rowCampo, $camposPermitidos)) {
            return ['title' => "¡Error!", 'text' => "Campo no permitido", 'icon' => "error", 'value' => 0];
        }

        $query = "SELECT $colImagen FROM $tabla WHERE estado = 1 AND $rowCampo = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return [
                    'title' => "¡Éxito!",
                    'text' => "Imagen encontrada",
                    'icon' => "success",
                    'value' => $row[$colImagen]
                ];
            } else {
                return ['title' => "¡Error!", 'text' => "No se encontró la imagen", 'icon' => "error", 'value' => 0];
            }
        } else {
            return ['title' => "¡Error!", 'text' => "Error al ejecutar la consulta", 'icon' => "error", 'value' => 0];
        }
    } catch (mysqli_sql_exception $e) {
        return ['title' => "¡Error!", 'text' => "Error al obtener la imagen: " . $e->getMessage(), 'icon' => "error", 'value' => 0];
    }
}

