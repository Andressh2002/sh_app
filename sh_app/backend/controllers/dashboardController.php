<?php
    function construirFiltros(
        &$query,
        &$params,
        &$types,
        $anioInicial,
        $anioFinal,
        $mesInicial,
        $mesFinal,
        $diaInicial,
        $diaFinal,
        $categoria,
        $rareza,
        $universo,
        $campoFecha = 'pe.fecha_registro'
    ){

        if(
            !empty($anioInicial) &&
            !empty($mesInicial) &&
            !empty($diaInicial)
        ){

            $query .= "
                AND {$campoFecha} >= ?
            ";

            $params[] =
                "$anioInicial-$mesInicial-$diaInicial 00:00:00";

            $types .= 's';
        }

        if(
            !empty($anioFinal) &&
            !empty($mesFinal) &&
            !empty($diaFinal)
        ){

            $query .= "
                AND {$campoFecha} <= ?
            ";

            $params[] =
                "$anioFinal-$mesFinal-$diaFinal 23:59:59";

            $types .= 's';
        }

        $filtros = [

            [
                'valor'=>$categoria,
                'sql'=>'ca.nombre'
            ],

            [
                'valor'=>$rareza,
                'sql'=>'rr.nombre'
            ],

            [
                'valor'=>$universo,
                'sql'=>'un.nombre'
            ]
        ];

        foreach($filtros as $filtro){

            if(
                trim(
                    $filtro['valor'] ?? ''
                ) !== ''
            ){

                $query .= "
                    AND {$filtro['sql']} = ?
                ";

                $params[] =
                    $filtro['valor'];

                $types .= 's';
            }
        }
    }

    function ejecutarConsulta(
        $conn,
        $query,
        $types,
        $params
    ) {

        $stmt = $conn->prepare($query);

        if(!$stmt){

            die(
                $conn->error .
                '<br><pre>' .
                $query .
                '</pre>'
            );
        }

        if (
            !empty($types)
        ) {
            $stmt->bind_param(
                $types,
                ...$params
            );
        }

        $stmt->execute();

        $resultado =
            $stmt
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );

        $stmt->close();

        return $resultado;
    }

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
        $types = '';

        construirFiltros(
            $query,
            $params,
            $types,
            $anioInicial,
            $anioFinal,
            $mesInicial,
            $mesFinal,
            $diaInicial,
            $diaFinal,
            $categoria,
            $rareza,
            $universo,
            'pe.fecha_registro'
        );

        $query .= "
            GROUP BY
                pr.id,
                pr.nombre,
                ca.nombre

            ORDER BY
                pedidos $orden

            LIMIT 10
        ";

        return ejecutarConsulta(
            $conn,
            $query,
            $types,
            $params
        );
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
        $types = '';

        construirFiltros(
            $query,
            $params,
            $types,
            $anioInicial,
            $anioFinal,
            $mesInicial,
            $mesFinal,
            $diaInicial,
            $diaFinal,
            $categoria,
            $rareza,
            $universo,
            'pe.fecha_pago'
        );

        $query .= "
            GROUP BY
                pr.id,
                pr.nombre,
                ca.nombre

            ORDER BY
                ganancias $orden

            LIMIT 10
        ";

        return ejecutarConsulta(
            $conn,
            $query,
            $types,
            $params
        );
    }

    function buscarGananciasPorTiempo(
        $conn,
        $anioInicial,
        $anioFinal,
        $mesInicial,
        $mesFinal,
        $diaInicial,
        $diaFinal,
        $categoria,
        $rareza,
        $universo,
        $orden
    ){

        $query = "
            SELECT

                COALESCE(
                    SUM(
                        CASE
                            WHEN pe.pagado = 1
                            THEN pe.total
                            ELSE 0
                        END
                    ),
                    0
                ) ganancias,

                YEAR(pe.fecha_pago) anio,

                MONTH(pe.fecha_pago) mes,

                MONTHNAME(pe.fecha_pago)
                    mes_nombre

            FROM pedidos pe

            JOIN productos pr
                ON pr.id=pe.idProducto

            JOIN categorias ca
                ON ca.id=pr.idCategoria

            JOIN rarezas rr
                ON rr.id=pr.idRareza

            JOIN universos un
                ON un.id=pr.idUniverso

            WHERE pe.estado=1
        ";

        $params = [];

        $types = '';

        construirFiltros(
            $query,
            $params,
            $types,
            $anioInicial,
            $anioFinal,
            $mesInicial,
            $mesFinal,
            $diaInicial,
            $diaFinal,
            $categoria,
            $rareza,
            $universo,
            'pe.fecha_pago'
        );

        $query .= "
            GROUP BY
                YEAR(pe.fecha_pago),
                MONTH(pe.fecha_pago)

            ORDER BY
                YEAR(pe.fecha_pago),
                MONTH(pe.fecha_pago)
        ";

        return ejecutarConsulta(
            $conn,
            $query,
            $types,
            $params
        );
    }

    function buscarKPIs(
        $conn,
        ...$filtros
    ){

        $query = "
            SELECT

                COUNT(
                    DISTINCT pe.id
                ) pedidos,

                SUM(
                    CASE
                        WHEN pe.pagado=1
                        THEN pe.cantidad
                        ELSE 0
                    END
                ) vendidos,

                SUM(
                    CASE
                        WHEN pe.pagado=1
                        THEN pe.total
                        ELSE 0
                    END
                ) ganancias,

                ROUND(
                    AVG(
                        CASE
                            WHEN pe.pagado=1
                            THEN pe.total
                        END
                    ),
                    0
                ) ticketPromedio,

                COUNT(
                    DISTINCT
                    CASE
                        WHEN pe.pagado=1
                        THEN pe.idProducto
                    END
                ) productosVendidos,

                ROUND(
                    (
                        SUM(
                            CASE
                                WHEN pe.pagado=1
                                THEN 1
                                ELSE 0
                            END
                        )
                        /
                        NULLIF(
                            COUNT(*),
                            0
                        )
                    ) * 100,
                    1
                ) conversion,

                (
                    SELECT
                        pr2.nombre

                    FROM pedidos pe2

                    JOIN productos pr2
                        ON pr2.id=pe2.idProducto

                    WHERE
                        pe2.estado=1
                        AND pe2.pagado=1

                    GROUP BY
                        pr2.id

                    ORDER BY
                        SUM(
                            pe2.cantidad
                        ) DESC

                    LIMIT 1
                ) productoTop,

                (
                    SELECT
                        CASE

                            WHEN anterior.total = 0
                            THEN 100

                            ELSE ROUND(
                                (
                                    (
                                        actual.total
                                        -
                                        anterior.total
                                    )
                                    /
                                    anterior.total
                                ) * 100,
                                1
                            )

                        END

                    FROM (

                        SELECT
                            COALESCE(
                                SUM(pe2.total),
                                0
                            ) total

                        FROM pedidos pe2

                        WHERE
                            pe2.estado = 1
                            AND pe2.pagado = 1
                            AND YEAR(
                                pe2.fecha_pago
                            ) = YEAR(
                                CURRENT_DATE()
                            )
                            AND MONTH(
                                pe2.fecha_pago
                            ) = MONTH(
                                CURRENT_DATE()
                            )

                    ) actual

                    CROSS JOIN (

                        SELECT
                            COALESCE(
                                SUM(pe3.total),
                                0
                            ) total

                        FROM pedidos pe3

                        WHERE
                            pe3.estado = 1
                            AND pe3.pagado = 1
                            AND YEAR(
                                pe3.fecha_pago
                            ) = YEAR(
                                DATE_SUB(
                                    CURRENT_DATE(),
                                    INTERVAL 1 MONTH
                                )
                            )
                            AND MONTH(
                                pe3.fecha_pago
                            ) = MONTH(
                                DATE_SUB(
                                    CURRENT_DATE(),
                                    INTERVAL 1 MONTH
                                )
                            )

                    ) anterior

                ) crecimientoMensual

            FROM pedidos pe

            JOIN productos pr
                ON pr.id=pe.idProducto

            JOIN categorias ca
                ON ca.id=pr.idCategoria

            JOIN rarezas rr
                ON rr.id=pr.idRareza

            JOIN universos un
                ON un.id=pr.idUniverso

            WHERE pe.estado=1
        ";

        $params = [];

        $types = '';

        [
            $anioInicial,
            $anioFinal,
            $mesInicial,
            $mesFinal,
            $diaInicial,
            $diaFinal,
            $categoria,
            $rareza,
            $universo
        ] = $filtros;

        construirFiltros(
            $query,
            $params,
            $types,
            $anioInicial,
            $anioFinal,
            $mesInicial,
            $mesFinal,
            $diaInicial,
            $diaFinal,
            $categoria,
            $rareza,
            $universo,
            'pe.fecha_pago'
        );

        return ejecutarConsulta(
            $conn,
            $query,
            $types,
            $params
        );
    }
?>