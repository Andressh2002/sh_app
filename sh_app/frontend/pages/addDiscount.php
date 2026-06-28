<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateDiscount = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $discountId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateDiscount ? 'Actualizar descuento' : 'Agregar descuento';
    $pageIcon = 'bi-percent';
    
    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre del descuento.",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
        ],
        [
            'label' => 'Descuento',
            'id' => 'Descuento',
            'icon' => 'bi bi-percent',
            'input' => 'number',
            'symbol' => '%',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el descuento de la oferta.",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
        ],
    ];
    $datesInputs = [
        [
            'label' => 'Fecha de inicio',
            'id' => 'StartDate',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'day',
            'btnHelp' => true,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6',
            'options' => [
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
            'onchange' => "actualizarDias('StartDate')"
        ],

        [
            'label' => 'Fecha de finalización',
            'id' => 'EndDate',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'day',
            'btnHelp' => true,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6',
                'options' => [
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
            'onchange' => "actualizarDias('EndDate')"
        ]
    ];
    $menuTable = [
        'url' => 'discounts.php',
        'addMethod' => 'guardarDescuento()',
    ];

    $type = 'descuento';
?>

<div class="w-100 <?php echo $updateDiscount ? 'product-loading' : ''; ?>" id="discount-form">

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center gap-2">

                <p class="card-title p-0 m-0">
                    Información básica
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach($informationInputs as $input){
                include '../src/components/inputs/input.php';
            }
            ?>

        </div>

    </div>

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center gap-2">

                <p class="card-title p-0 m-0">
                    Rango de fechas
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach($datesInputs as $input){
                include '../src/components/inputs/input.php';
            }
            ?>

        </div>

    </div>

    <div class="container-fluid mb-3">

        <?php include '../src/components/forms/dialogButtons.php'; ?>

    </div>

    <input
        type="hidden"
        id="Id"
        value="<?php echo $discountId; ?>"
    >

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

    function setDiscountLoading(isLoading){
        $('#discount-form').toggleClass(
            'product-loading',
            isLoading
        );
    }

    $(document).ready(function(){
        const isUpdate = <?php echo $updateDiscount ? 'true' : 'false'; ?>;

        if(isUpdate){
            setDiscountLoading(true);
            buscarDescuento(<?php echo $discountId; ?>);
        }
    });

    function actualizarDias(id){

        const mes =
            parseInt(
                $('#Month' + id).val()
            );

        const diasSelect =
            $('#Day' + id);

        const diaActual =
            diasSelect.val();

        let dias = 31;

        if([4,6,9,11].includes(mes)){
            dias = 30;
        }

        if(mes === 2){
            dias = 29;
        }

        diasSelect.empty();

        diasSelect.append(
            '<option value="">Día</option>'
        );

        for(let i = 1; i <= dias; i++){

            diasSelect.append(`
                <option value="${i}">
                    ${i}
                </option>
            `);
        }

        if(diaActual <= dias){
            diasSelect.val(diaActual);
        }
    }
</script>