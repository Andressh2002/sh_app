<?php
//Estas variables php son para los filtros de los modales
$inputsColors = [
    [
        'label' => 'Nombre',
        'id' => 'Nombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'onchange' => 'currentPage = 1; aplicarFiltrosColor()',
    ],
    [
        'label' => 'Familia de color',
        'id' => 'Familia',
        'icon' => 'bi bi-palette-fill',
        'input' => 'text',
        'onchange' => 'currentPage = 1; aplicarFiltrosColor()',
    ]
];
$ordersColors = [
    [
        'label' => 'Ordenar por:',
        'id' => 'Ordenar_por',
        'icon' => '',
        'input' => 'select',
        'options' => [
            'nombre' => 'Nombre',
            'color_familia' => 'Familia de colores',
            'fecha_registro' => 'Fecha de creación'
        ],
        'onchange' => 'aplicarFiltrosColor()',
    ]
];

$inputsHolidays = [
    [
        'label' => 'Nombre',
        'id' => 'Nombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'onchange' => 'currentPage = 1; aplicarFiltrosFestividad()',
    ]
];
$ordersHolidays = [
    [
        'label' => 'Ordenar por:',
        'id' => 'Ordenar_por',
        'icon' => '',
        'input' => 'select',
        'options' => [
            'nombre' => 'Nombre',
            'fecha_registro' => 'Fecha de creación'
        ],
        'onchange' => 'currentPage = 1; aplicarFiltrosFestividad()',
    ]
];
?>

<div class="mb-3 col-auto" id="input-col-<?php echo $input['id']; ?>">
    <div class="input-group">
        <div class="w-100 py-2 d-flex gap-2">
            <label for="" class="form-label m-0"><?php echo $input['label']; ?></label>
            <i class="<?php echo $input['icon']; ?> d-flex align-self-center"></i>
        </div>
    </div>

    <!-- Input select -->
    <?php if ($input['input'] == 'select'): ?>
        <div class="input-group">
            <select class="form-select" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>">
                <?php foreach ($input['options'] as $value => $label): ?>
                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input select for ajax -->
    <?php elseif ($input['input'] == 'selectajax'): ?>
        <div class="input-group">
            <select class="form-select" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>"></select>
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input textarea -->
    <?php elseif ($input['input'] == 'textarea'): ?>
        <div class="input-group">
            <textarea class="form-control bi-textarea-resize" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" aria-describedby="helpId" placeholder="" cols="999%" rows="3"></textarea>
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input text -->
    <?php elseif ($input['input'] == 'text'): ?>
        <div class="input-group">
            <input type="text" class="form-control" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" aria-describedby="helpId" placeholder="" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input password -->
    <?php elseif ($input['input'] == 'password'): ?>
        <div class="input-group">
            <input type="password" class="form-control" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" aria-describedby="helpId" placeholder="" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input color -->
    <?php elseif ($input['input'] == 'color'): ?>
        <div class="input-group">
            <input type="color" class="form-control" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" value="#ffffff" style="width: 50px; height: 38px;" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input number -->
    <?php elseif ($input['input'] == 'number'): ?>
        <div class="input-group">
            <span class="input-group-text"><?php echo $input['symbol']; ?></span>
            <input type="number" class="form-control" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" aria-describedby="helpId" placeholder="" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input long -->
    <?php elseif ($input['input'] == 'long'): ?>
        <div class="input-group">
            <input type="number" class="form-control" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" aria-describedby="helpId" placeholder="" />
            <span class="input-group-text"><?php echo $input['symbol']; ?></span>
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input datepicker -->
    <?php elseif ($input['input'] == 'datepicker'): ?>
    <div class="input-group">
        <input type="date" class="form-control" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" />
        <?php if ($input['btnHelp']): ?>
            <span class="input-group-text p-0 m-0 overflow-hidden">
                <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                    <i class="d-flex bi bi-info-circle"></i>
                </button>
            </span>
        <?php endif; ?>
    </div>
    <?php if ($input['spans'][0]): ?>
        <div class="input-group">
            <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
        </div>
    <?php endif; ?>
    <?php if ($input['spans'][1]): ?>
        <div class="input-group">
            <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
        </div>
    <?php endif; ?>

        <!-- Input check -->
    <?php elseif ($input['input'] == 'check'): ?>
        <div class="input-group">
            <input type="checkbox" class="form-check-input m-0" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" style="width: 34px; height: 34px;" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input image -->
    <?php elseif ($input['input'] == 'image'): ?>
        <div class="input-group">
            <input type="file" class="form-control id-input-image" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" aria-describedby="helpId" placeholder="" />
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <!-- Contenedor para la vista previa de la imagen -->
        <div class="text-center">
            <img class="p-3" id="<?php echo $input['idVista']; ?>" src="" alt="" style="display: none; height: 196px; width: auto;">
        </div>
        <input type="hidden" id="<?php echo $input['idHidden']; ?>" value="<?php echo $input['value']; ?>">
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input day -->
    <?php elseif ($input['input'] == 'day'): ?>
        <div class="input-group">
            <input type="number" class="form-control" name="Day<?php echo $input['id']; ?>" id="Day<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" value="1" max="31" min="1" maxlength="2" />
            <span class="input-group-text">de</span>
            <select class="form-select" name="Month<?php echo $input['id']; ?>" id="Month<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>">
                <?php foreach ($input['options'] as $value => $label): ?>
                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($input['spans'][0]): ?>
            <div class="input-group">
                <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
            </div>
        <?php endif; ?>
        <?php if ($input['spans'][1]): ?>
            <div class="input-group">
                <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
            </div>
        <?php endif; ?>

        <!-- Input info -->
    <?php elseif ($input['input'] == 'infoText'): ?>
        <p class="form-label m-0 text-danger" id="<?php echo $input['id']; ?>"></p>

        <!-- Input holiday -->
    <?php elseif ($input['input'] == 'holiday'): ?>
        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                <div class="input-group">
                    <button type="button" class="btn-details text-white border-0 rounded-2 px-2 py-2 rounded-end-0" data-bs-toggle="modal" data-bs-target="#modalHolidays">
                        Buscar
                    </button>
                    <input type="text" class="form-control" id="text<?php echo $input['id']; ?>" readonly />
                    <span class="input-group-text">
                        <button onclick="$('#text<?php echo $input['id']; ?>').val('Ninguno'); $('#hidden<?php echo $input['id']; ?>').val('0');" type="button" class="btn-delete text-white border-0 rounded-pill d-flex align-items-center" style="width: 22px; height: 22px;">
                            <i class="bi bi-x d-flex align-self-center m-auto"></i>
                        </button>
                    </span>
                    <?php if ($input['btnHelp']): ?>
                        <span class="input-group-text p-0 m-0 overflow-hidden">
                            <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                                <i class="d-flex bi bi-info-circle"></i>
                            </button>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
                <input type="hidden" id="hidden<?php echo $input['id']; ?>" />
            </div>
        </div>

        <div class="modal fade" id="modalHolidays" tabindex="-1" aria-labelledby="modalHolidaysLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHolidaysLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreFestividadModal" onchange="cargarFiltrosParaTablaFestividadesModal('holidays-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="holidays-data-container">
                                    <tr>
                                        <td class="text-center" colspan='4'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input list of colors -->
    <?php elseif ($input['input'] == 'colors'): ?>
        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                <div class="input-group">
                    <div class="w-100 d-flex gap-2 justify-content-between align-items-center">
                        <button type="button" id="btnColors" class="btn-details text-white border-0 rounded-2 px-2 py-2" data-bs-toggle="modal" data-bs-target="#modalColors">
                            Buscar
                        </button>
                        <label id="labelColorCant" for="" class="form-label m-0 text-secondary">Agregados 0 de 16</label>
                        <?php if ($input['btnHelp']): ?>
                            <span class="input-group-text p-0 m-0 overflow-hidden">
                                <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 p-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                                    <i class="d-flex bi bi-info-circle"></i>
                                </button>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="container-fluid overflow-x-auto overflow-y-auto p-0 m-0">
            <div class="row gap-2 overflow-x-auto overflow-y-auto p-0 m-0" id="colors-selected-data-container"></div>
        </div>

        <div class="modal fade" id="modalColors" tabindex="-1" aria-labelledby="modalColorsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalColorsLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4 gap-2">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreColorModal" onchange="cargarFiltrosParaTablaColoresModal('colors-data-container');" />
                            </div>
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Familia</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="FamiliaColorModal" onchange="cargarFiltrosParaTablaColoresModal('colors-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="colors-data-container">
                                    <tr>
                                        <td class="text-center" colspan='5'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input rarity -->
    <?php elseif ($input['input'] == 'rarity'): ?>
        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                <div class="input-group">
                    <button type="button" class="btn-details text-white border-0 rounded-2 px-2 py-2 rounded-end-0" data-bs-toggle="modal" data-bs-target="#modalRarities">
                        Buscar
                    </button>
                    <input type="text" class="form-control" id="text<?php echo $input['id']; ?>" readonly />
                    <span class="input-group-text">
                        <button onclick="$('#text<?php echo $input['id']; ?>').val('Ninguno'); $('#hidden<?php echo $input['id']; ?>').val('0');" type="button" class="btn-delete text-white border-0 rounded-pill d-flex align-items-center" style="width: 22px; height: 22px;">
                            <i class="bi bi-x d-flex align-self-center m-auto"></i>
                        </button>
                    </span>
                    <?php if ($input['btnHelp']): ?>
                        <span class="input-group-text p-0 m-0 overflow-hidden">
                            <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                                <i class="d-flex bi bi-info-circle"></i>
                            </button>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
                <input type="hidden" id="hidden<?php echo $input['id']; ?>" />
            </div>
        </div>

        <div class="modal fade" id="modalRarities" tabindex="-1" aria-labelledby="modalRaritiesLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRaritiesLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreRarezaModal" onchange="cargarFiltrosParaTablaRarezasModal('rarities-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="rarities-data-container">
                                    <tr>
                                        <td class="text-center" colspan='4'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input universe -->
    <?php elseif ($input['input'] == 'universe'): ?>
        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                <div class="input-group">
                    <button type="button" class="btn-details text-white border-0 rounded-2 px-2 py-2 rounded-end-0" data-bs-toggle="modal" data-bs-target="#modalUniverses">
                        Buscar
                    </button>
                    <input type="text" class="form-control" id="text<?php echo $input['id']; ?>" readonly />
                    <span class="input-group-text">
                        <button onclick="$('#text<?php echo $input['id']; ?>').val('Ninguno'); $('#hidden<?php echo $input['id']; ?>').val('0');" type="button" class="btn-delete text-white border-0 rounded-pill d-flex align-items-center" style="width: 22px; height: 22px;">
                            <i class="bi bi-x d-flex align-self-center m-auto"></i>
                        </button>
                    </span>
                    <?php if ($input['btnHelp']): ?>
                        <span class="input-group-text p-0 m-0 overflow-hidden">
                            <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                                <i class="d-flex bi bi-info-circle"></i>
                            </button>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
                <input type="hidden" id="hidden<?php echo $input['id']; ?>" />
            </div>
        </div>

        <div class="modal fade" id="modalUniverses" tabindex="-1" aria-labelledby="modalUniversesLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUniversesLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreUniversoModal" onchange="cargarFiltrosParaTablaUniversosModal('universes-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="universes-data-container">
                                    <tr>
                                        <td class="text-center" colspan='4'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input discount -->
    <?php elseif ($input['input'] == 'discount'): ?>
        <div class="input-group mb-2">
            <button type="button" class="btn-details text-white border-0 rounded-2 px-2 py-2" data-bs-toggle="modal" data-bs-target="#modalDiscounts">
                Buscar
            </button>
        </div>

        <div class="input-group mb-2">
            <div class="table-responsive">
                <table class="table table-hover table-striped m-0">
                    <thead>
                        <tr>
                            <th scope="col" class="align-middle card-text fw-normal">#</th>
                            <th scope="col" class="align-middle card-text fw-normal">Nombre</th>
                            <th scope="col" class="align-middle card-text fw-normal">Tiempo</th>
                            <th scope="col" class="align-middle card-text fw-normal">Descuento</th>
                            <th scope="col" class="align-middle card-text fw-normal">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="discounts-selected-data-container">
                        <tr>
                            <td scope="col" colspan="5" class="align-middle text-center card-text fw-normal">Ninguno seleccionado</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php if ($input['btnHelp']): ?>
                <span class="input-group-text p-0 m-0 overflow-hidden border-0">
                    <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white rounded-2 border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                        <i class="d-flex bi bi-info-circle"></i>
                    </button>
                </span>
            <?php endif; ?>
        </div>

        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
                <input type="hidden" id="hidden<?php echo $input['id']; ?>" />
            </div>
        </div>

        <div class="modal fade" id="modalDiscounts" tabindex="-1" aria-labelledby="modalmodalDiscountsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDiscountsLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreDescuentoModal" onchange="cargarFiltrosParaTablaDescuentosModal('discounts-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="discounts-data-container">
                                    <tr>
                                        <td class="text-center" colspan='5'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input accesory -->
    <?php elseif ($input['input'] == 'accesory'): ?>
        <div class="container-fluid d-flex align-items-center gap-2 p-0 m-0">
            <div class="col-auto p-0 m-0">
                <div class="input-group">
                    <button type="button" class="btn-details text-white border-0 rounded-2 px-2 py-2 rounded-end-0" data-bs-toggle="modal" data-bs-target="#modalAccesories">
                        Buscar
                    </button>
                    <input type="text" class="form-control" id="text<?php echo $input['id']; ?>" readonly />
                    <span class="input-group-text">
                        <button onclick="$('#text<?php echo $input['id']; ?>').val('Ninguno'); $('#hidden<?php echo $input['id']; ?>').val('0');" type="button" class="btn-delete text-white border-0 rounded-pill d-flex align-items-center" style="width: 22px; height: 22px;">
                            <i class="bi bi-x d-flex align-self-center m-auto"></i>
                        </button>
                    </span>
                    <?php if ($input['btnHelp']): ?>
                        <span class="input-group-text p-0 m-0 overflow-hidden">
                            <button type="button" onclick="showInputInfo('<?php echo $input['inputInfo']; ?>')" class="btn-details text-white border-0 px-2 w-100 h-100 d-flex gap-2 justify-content-center align-items-center">
                                <i class="d-flex bi bi-info-circle"></i>
                            </button>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($input['spans'][0]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-required p-0 m-0"><?php echo $input['spans'][0]; ?></label>
                    </div>
                <?php endif; ?>
                <?php if ($input['spans'][1]): ?>
                    <div class="input-group">
                        <label for="" class="form-label label-limit p-0 m-0"><?php echo $input['spans'][1]; ?></label>
                    </div>
                <?php endif; ?>
                <input type="hidden" id="hidden<?php echo $input['id']; ?>" />
            </div>
        </div>

        <div class="modal fade" id="modalAccesories" tabindex="-1" aria-labelledby="modalAccesoriesLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAccesoriesLabel"><?php echo $input['title']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid row mb-4">
                            <div class="col-auto px-0">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label for="" class="form-label m-0">Nombre</label>
                                    <i class="bi bi-card-text d-flex align-self-center"></i>
                                </div>
                                <input type="text" class="form-control" id="NombreAccesorioModal" onchange="cargarFiltrosParaTablaAccesoriosModal('accesories-data-container');" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php foreach ($input['header'] as $header): ?>
                                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="accesories-data-container">
                                    <tr>
                                        <td class="text-center" colspan='4'>
                                            <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: 28px; height: 28px;">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input hidden -->
    <?php else: ?>
        <input type="hidden" name="<?php echo $input['id']; ?>" id="<?php echo $input['id']; ?>" />
    <?php endif; ?>
</div>