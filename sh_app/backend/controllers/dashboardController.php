<?php
    function buscarProductos($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden) {
        $orden = strtoupper($orden);
        if ($orden !== 'ASC' && $orden !== 'DESC') {
            $orden = 'DESC';
        }
    
        // Construcción de fechas a partir de los parámetros
        $fechaInicio = !empty($anioInicial) && !empty($mesInicial) && !empty($diaInicial) 
            ? "$anioInicial-$mesInicial-$diaInicial" 
            : null;
        $fechaFinal = !empty($anioFinal) && !empty($mesFinal) && !empty($diaFinal) 
            ? "$anioFinal-$mesFinal-$diaFinal" 
            : null;
    
        // Base query
        $query = "SELECT 
            pr.nombre AS producto, 
            ca.nombre AS categoria, 
            SUM(pe.cantidad) AS pedidos,
            SUM(CASE WHEN pe.pagado = 1 THEN pe.cantidad ELSE 0 END) AS vendidos,
            SUM(CASE WHEN pe.pagado = 1 THEN pe.total ELSE 0 END) AS ganancias,
            RANK() OVER (ORDER BY SUM(pe.cantidad) DESC) AS puesto
        FROM pedidos pe 
        JOIN usuarios cl ON pe.idCliente = cl.id
        JOIN productos pr ON pe.idProducto = pr.id
        JOIN categorias ca ON pr.idCategoria = ca.id
        JOIN rarezas rr ON pr.idRareza = rr.id
        JOIN universos un ON pr.idUniverso = un.id
        WHERE pe.estado = 1";
    
        // Filtros dinámicos usando consultas preparadas
        $params = [];
        $types = "";
    
        if (!empty($fechaInicio) && !empty($fechaFinal)) {
            $query .= " AND pe.fecha_registro BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFinal;
            $types .= "ss";
        }
        if (!empty($categoria)) {
            $query .= " AND ca.nombre LIKE ?";
            $params[] = "%$categoria%";
            $types .= "s";
        }
        if (!empty($rareza)) {
            $query .= " AND rr.nombre LIKE ?";
            $params[] = "%$rareza%";
            $types .= "s";
        }
        if (!empty($universo)) {
            $query .= " AND un.nombre LIKE ?";
            $params[] = "%$universo%";
            $types .= "s";
        }
    
        $query .= " GROUP BY pr.nombre, ca.nombre";
        $query .= " ORDER BY pedidos $orden LIMIT 10";
    
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    
        $stmt->close();
        return $datos;
    }

    function buscarGananciasPorProducto($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden) {
        $orden = strtoupper($orden);
        if ($orden !== 'ASC' && $orden !== 'DESC') {
            $orden = 'DESC';
        }
    
        // Construcción de fechas a partir de los parámetros
        $fechaInicio = !empty($anioInicial) && !empty($mesInicial) && !empty($diaInicial) 
            ? "$anioInicial-$mesInicial-$diaInicial" 
            : null;
        $fechaFinal = !empty($anioFinal) && !empty($mesFinal) && !empty($diaFinal) 
            ? "$anioFinal-$mesFinal-$diaFinal" 
            : null;
    
        // Base query
        $query = "SELECT 
            SUM(CASE WHEN pe.pagado = 1 THEN pe.total ELSE 0 END) AS ganancias,
            pr.nombre AS producto, 
            ca.nombre AS categoria
        FROM pedidos pe 
        JOIN productos pr ON pe.idProducto = pr.id
        JOIN categorias ca ON pr.idCategoria = ca.id
        JOIN rarezas rr ON pr.idRareza = rr.id
        JOIN universos un ON pr.idUniverso = un.id
        WHERE pe.estado = 1";
    
        // Filtros dinámicos
        $params = [];
        $types = "";
    
        if (!empty($fechaInicio) && !empty($fechaFinal)) {
            $query .= " AND pe.fecha_registro BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFinal;
            $types .= "ss";
        }
        if (!empty($categoria)) {
            $query .= " AND ca.nombre LIKE ?";
            $params[] = "%$categoria%";
            $types .= "s";
        }
        if (!empty($rareza)) {
            $query .= " AND rr.nombre LIKE ?";
            $params[] = "%$rareza%";
            $types .= "s";
        }
        if (!empty($universo)) {
            $query .= " AND un.nombre LIKE ?";
            $params[] = "%$universo%";
            $types .= "s";
        }
    
        $query .= " GROUP BY pr.nombre, ca.nombre";
        $query .= " ORDER BY ganancias $orden LIMIT 10";
    
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    
        $stmt->close();
        return $datos;
    }

    function buscarGananciasPorTiempo($conn, $anioInicial, $anioFinal, $mesInicial, $mesFinal, $diaInicial, $diaFinal, $categoria, $rareza, $universo, $orden) {
        $orden = strtoupper($orden);
        if ($orden !== 'ASC' && $orden !== 'DESC') {
            $orden = 'DESC';
        }

        // Construcción de fechas a partir de los parámetros
        $fechaInicio = !empty($anioInicial) && !empty($mesInicial) && !empty($diaInicial) 
            ? "$anioInicial-$mesInicial-$diaInicial" 
            : null;
        $fechaFinal = !empty($anioFinal) && !empty($mesFinal) && !empty($diaFinal) 
            ? "$anioFinal-$mesFinal-$diaFinal" 
            : null;
    
        // Base query
        $query = "SELECT 
            SUM(CASE WHEN pe.pagado = 1 THEN pe.total ELSE 0 END) AS ganancias,
            YEAR(pe.fecha_registro) AS anio,
            MONTH(pe.fecha_registro) AS mes,
            DAY(pe.fecha_registro) AS dia,
            CASE 
                WHEN MONTH(pe.fecha_registro) = 1 THEN 'Enero'
                WHEN MONTH(pe.fecha_registro) = 2 THEN 'Febrero'
                WHEN MONTH(pe.fecha_registro) = 3 THEN 'Marzo'
                WHEN MONTH(pe.fecha_registro) = 4 THEN 'Abril'
                WHEN MONTH(pe.fecha_registro) = 5 THEN 'Mayo'
                WHEN MONTH(pe.fecha_registro) = 6 THEN 'Junio'
                WHEN MONTH(pe.fecha_registro) = 7 THEN 'Julio'
                WHEN MONTH(pe.fecha_registro) = 8 THEN 'Agosto'
                WHEN MONTH(pe.fecha_registro) = 9 THEN 'Septiembre'
                WHEN MONTH(pe.fecha_registro) = 10 THEN 'Octubre'
                WHEN MONTH(pe.fecha_registro) = 11 THEN 'Noviembre'
                WHEN MONTH(pe.fecha_registro) = 12 THEN 'Diciembre'
            END AS mes_nombre
        FROM pedidos pe 
        JOIN productos pr ON pe.idProducto = pr.id
        JOIN categorias ca ON pr.idCategoria = ca.id
        JOIN rarezas rr ON pr.idRareza = rr.id
        JOIN universos un ON pr.idUniverso = un.id
        WHERE pe.estado = 1";
    
        // Filtros dinámicos
        $params = [];
        $types = "";
    
        if (!empty($fechaInicio) && !empty($fechaFinal)) {
            $query .= " AND pe.fecha_registro BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFinal;
            $types .= "ss";
        }
        if (!empty($categoria)) {
            $query .= " AND ca.nombre LIKE ?";
            $params[] = "%$categoria%";
            $types .= "s";
        }
        if (!empty($rareza)) {
            $query .= " AND rr.nombre LIKE ?";
            $params[] = "%$rareza%";
            $types .= "s";
        }
        if (!empty($universo)) {
            $query .= " AND un.nombre LIKE ?";
            $params[] = "%$universo%";
            $types .= "s";
        }
    
        $query .= " GROUP BY anio, mes";
        $query .= " ORDER BY anio, mes $orden";
    
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    
        $datos = [];
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    
        $stmt->close();
        return $datos;
    }

    function buscarTotales($conn) {
        $query = "SELECT 
            SUM(CASE WHEN pe.pagado = 1 THEN pe.total ELSE 0 END) AS ganancias,
            SUM(pe.cantidad) AS pedidos,
            SUM(CASE WHEN pe.pagado = 1 THEN pe.cantidad ELSE 0 END) AS vendidos
        FROM pedidos pe 
        WHERE pe.estado = 1";
    
        $result = $conn->query($query);
    
        $datos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $datos[] = $row;
            }
        }
    
        return $datos;
    }
?>