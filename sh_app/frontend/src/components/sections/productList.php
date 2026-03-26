<?php 
    function generarEstrellas($rating) {
        $fullStars = floor($rating);
        $halfStars = ceil($rating - $fullStars);
        $emptyStars = 5 - $fullStars - $halfStars;

        $starsHTML = '';

        for ($i = 0; $i < $fullStars; $i++) {
            $starsHTML .= '<i class="bi bi-star-fill text-star"></i>';
        }

        for ($i = 0; $i < $halfStars; $i++) {
            $starsHTML .= '<i class="bi bi-star-half text-star"></i>';
        }

        for ($i = 0; $i < $emptyStars; $i++) {
            $starsHTML .= '<i class="bi bi-star text-star"></i>';
        }

        return $starsHTML;
    }

    $productos = [
        [
            'estrellas' => generarEstrellas((75 / 100) * 5),
        
            'nombre' => 'Escultura de Kirby',
            'categoria' => 'Escultura',
            'imagen' => '../src/img/uploads/escultura_kirby.png',
            'precio' => 3000,
            'disponibles' => 0,
            'pedidos' => 0,
        ],
        [
            'estrellas' => generarEstrellas((50 / 100) * 5),
        
            'nombre' => 'Escultura de Kirby',
            'categoria' => 'Escultura',
            'imagen' => '../src/img/uploads/escultura_kirby.png',
            'precio' => 9999,
            'disponibles' => 0,
            'pedidos' => 0,
        ],
        [
            'estrellas' => generarEstrellas((99.99 / 100) * 5),
        
            'nombre' => 'Escultura de Kirby',
            'categoria' => 'Escultura',
            'imagen' => '../src/img/uploads/escultura_kirby.png',
            'precio' => 1000,
            'disponibles' => 0,
            'pedidos' => 0,
        ]
    ];

    foreach ($productos as $producto) {
        include '../src/components/cards/productCard.php';
    }
?>
