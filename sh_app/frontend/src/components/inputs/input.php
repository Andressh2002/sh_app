<?php

$type       = $input['input']      ?? 'text';
$id         = $input['id']         ?? '';
$label      = $input['label']      ?? '';
$icon       = $input['icon']       ?? 'bi bi-input-cursor';
$onchange   = $input['onchange']   ?? '';
$placeholder= $input['placeholder']?? '';
$value      = $input['value']      ?? '';

$required   = $input['required']   ?? '';
$limit      = $input['limit']      ?? '';

$help       = $input['help']       ?? '';
$options    = $input['options']    ?? [];

$colClass   = $input['col'] ?? 'col-12';

?>
<div class="<?php echo $colClass; ?> mb-4">

    <div class="filter-card admin-input-card px-4 px-sm-5">

        <!-- HEADER -->
        <?php if ($label || $icon): ?>

            <p class="filter-title">

                <?php if ($icon): ?>
                    <i class="<?php echo $icon; ?>"></i>
                <?php endif; ?>

                <?php echo $label; ?>

            </p>

        <?php endif; ?>

        <!-- INPUT TEXT -->
        <?php if ($type == 'text'): ?>

            <input
                type="text"
                class="form-control filter-input"
                id="<?php echo $id; ?>"
                value="<?php echo $value; ?>"
                onchange="<?php echo $onchange; ?>"
                placeholder="<?php echo $placeholder; ?>"
            />

        <!-- INPUT PASSWORD -->
        <?php elseif ($type == 'password'): ?>

            <input
                type="password"
                class="form-control filter-input"
                id="<?php echo $id; ?>"
                onchange="<?php echo $onchange; ?>"
                placeholder="<?php echo $placeholder; ?>"
            />

        <!-- INPUT NUMBER -->
        <?php elseif ($type == 'number'): ?>

            <div class="input-group">

                <?php if (!empty($input['symbol'])): ?>
                    <span class="input-group-text filter-group-text">
                        <?php echo $input['symbol']; ?>
                    </span>
                <?php endif; ?>

                <input
                    type="number"
                    class="form-control filter-input"
                    id="<?php echo $id; ?>"
                    value="<?php echo $value; ?>"
                    onchange="<?php echo $onchange; ?>"
                    placeholder="<?php echo $placeholder; ?>"
                />

            </div>

        <!-- TEXTAREA -->
        <?php elseif ($type == 'textarea'): ?>

            <textarea
                class="form-control filter-input"
                id="<?php echo $id; ?>"
                rows="4"
                placeholder="<?php echo $placeholder; ?>"
            ><?php echo $value; ?></textarea>

        <!-- SELECT -->
        <?php elseif ($type == 'select'): ?>

            <select
                class="form-select filter-input"
                id="<?php echo $id; ?>"
                onchange="<?php echo $onchange; ?>"
            >

                <?php foreach ($options as $optionValue => $optionLabel): ?>

                    <option value="<?php echo $optionValue; ?>">
                        <?php echo $optionLabel; ?>
                    </option>

                <?php endforeach; ?>

            </select>

        <!-- MULTI-SELECT -->
         <?php elseif ($type == 'multiselect'): ?>

            <select
                class="form-select filter-input"
                id="<?php echo $id; ?>"
                multiple
                onchange="<?php echo $onchange; ?>"
            >

                <?php foreach ($options as $optionValue => $optionLabel): ?>

                    <option value="<?php echo $optionValue; ?>">
                        <?php echo $optionLabel; ?>
                    </option>

                <?php endforeach; ?>

            </select>

        <!-- CHECKBOX -->
        <?php elseif ($type == 'checkbox'): ?>

            <div class="filter-checkbox-item">

                <div class="form-check">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="<?php echo $id; ?>"
                        onchange="<?php echo $onchange; ?>"
                    />

                    <label
                        class="form-check-label"
                        for="<?php echo $id; ?>"
                    >
                        <?php echo $placeholder; ?>
                    </label>

                </div>

            </div>

        <!-- DATE -->
        <?php elseif ($type == 'date'): ?>

            <input
                type="date"
                class="form-control filter-input"
                id="<?php echo $id; ?>"
                onchange="<?php echo $onchange; ?>"
            />

        <!-- COLOR -->
        <?php elseif ($type == 'color'): ?>

            <input
                type="color"
                class="form-control form-control-color w-100"
                id="<?php echo $id; ?>"
                value="<?php echo $value ?: '#ffffff'; ?>"
            />

        <!-- PALETTES -->
        <?php elseif ($type == 'palettes'): ?>

            <div class="">

                <button
                    type="button"
                    id="btnColors"
                    class="store-filter-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalColors"
                >
                    <i class="bi bi-plus-circle"></i>
                    Agregar paletas
                </button>

                <small
                    id="labelColorCant"
                    class="d-block mt-2 admin-input-help"
                >
                    Agregados 0 de 20
                </small>

                <div
                    id="colors-selected-data-container"
                    class="row px-0 px-sm-1 px-md-2 px-lg-3 px-xl-4 pt-2"
                >
                </div>

            </div>

        <!-- FILE -->
        <?php elseif ($type == 'file'): ?>

            <div class="admin-image-upload">

                <input
                    type="file"
                    class="form-control filter-input image-preview-input"
                    id="<?php echo $id; ?>"
                    data-preview="vista<?php echo ucfirst($id); ?>"
                    data-hidden="hidden<?php echo ucfirst($id); ?>"
                >

                <div class="admin-image-preview">

                    <img
                        id="vista<?php echo ucfirst($id); ?>"
                        src=""
                        alt=""
                        style="display:none;"
                    >

                </div>

                <input
                    type="hidden"
                    id="hidden<?php echo ucfirst($id); ?>"
                >

            </div>

        <!-- RATING -->
        <?php elseif ($type == 'rating'): ?>

            <div
                class="rating-stars d-flex gap-1"
                data-target="<?php echo $id; ?>"
            >

                <?php for ($i = 1; $i <= 5; $i++): ?>

                    <i
                        class="bi <?php echo ($i <= $value) ? 'bi-star-fill' : 'bi-star'; ?> rating-star"
                        data-value="<?php echo $i; ?>"
                        style="font-size: 1.8rem; cursor: pointer;"
                    ></i>

                <?php endfor; ?>

            </div>

            <input
                type="hidden"
                id="<?php echo $id; ?>"
                name="<?php echo $id; ?>"
                value="<?php echo $value ?: 0; ?>"
                data-onchange="<?php echo htmlspecialchars($onchange); ?>"
            />

        <!-- DISCOUNTS -->
        <?php elseif ($type == 'discounts'): ?>

            <div class="d-flex justify-content-between align-items-center mb-3">

                <span class="admin-input-help" id="labelDiscountCant">
                    0 descuentos seleccionados
                </span>

                <button
                    type="button"
                    class="store-filter-btn"
                    onclick="abrirModal('modalDiscounts')"
                >
                    <i class="bi bi-plus-circle"></i>
                    Agregar
                </button>

            </div>

            <div id="discounts-selected-data-container"></div>

        <!-- DAY (SIN AÑO) -->
        <?php elseif ($type == 'day'): ?>

            <div class="row g-2">

                <div class="col-5">

                    <select
                        class="form-select filter-input"
                        id="Day<?php echo $id; ?>"
                    >

                        <option value="">
                            Día
                        </option>

                        <?php for($i = 1; $i <= 31; $i++): ?>

                            <option value="<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </option>

                        <?php endfor; ?>

                    </select>

                </div>

                <div class="col-7">

                    <select
                        class="form-select filter-input"
                        id="Month<?php echo $id; ?>"
                        onchange="<?php echo $onchange; ?>"
                    >

                        <option value="">
                            Mes
                        </option>

                        <?php foreach($options as $value => $label): ?>

                            <option value="<?php echo $value; ?>">
                                <?php echo $label; ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

        <?php endif; ?>

        <!-- HELP -->
        <?php if ($help): ?>

            <small class="admin-input-help">
                <i class="bi bi-info-circle"></i>
                <?php echo $help; ?>
            </small>

        <?php endif; ?>

        <!-- REQUIRED -->
        <?php if ($required): ?>

            <small class="admin-input-required">
                <?php echo $required; ?>
            </small>

        <?php endif; ?>

        <!-- LIMIT -->
        <?php if ($limit): ?>

            <small class="admin-input-limit">
                <?php echo $limit; ?>
            </small>

        <?php endif; ?>

    </div>

 </div>