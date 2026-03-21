<?php
    function obtenerLista($conn, $tabla) {
        $query = "SELECT id, nombre FROM " . $conn->real_escape_string($tabla) . " WHERE estado = 1";
    
        $result = $conn->query($query);
    
        $data = [];
        $list = [];
        if ($result->num_rows > 0) {
            while($list = $result->fetch_assoc()) {
                $data[] = $list;
            }
        }
    
        return $data;
    }
?>