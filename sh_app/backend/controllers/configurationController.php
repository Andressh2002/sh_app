<?php
    function buscar($conn, $id) {
        $stmt = $conn->prepare("SELECT valor FROM configuracion WHERE clave = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function actualizar($conn, $clave, $valor) {
        try {
            $queryUpdate = "UPDATE configuracion SET 
                            valor = ? 
                            WHERE clave = ?";
            
            $stmt = $conn->prepare($queryUpdate);
            $stmt->bind_param("ss", $valor, $clave);
    
            if ($stmt->execute()) {
                return [
                    'title' => "¡Actualizado!",
                    'text' => "La configuración se ha actualizado correctamente",
                    'icon' => "bi bi-check-circle"
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            return [
                'title' => "¡Error!",
                'text' => "Error al actualizar la configuración: " . $e->getMessage(),
                'icon' => "bi bi-x-circle"
            ];
        }
    }
?>