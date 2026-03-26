<!-- Input text -->
<?php if ($input['input'] == 'text'): ?>
    <div class="p-0 m-0 filterbar-item">
        <h4 class="card-title h6 filterbar-font-size"><?php echo $input['label']; ?></h4>
        <ul class="list-group" id="nombre-filtro">
            <input type="text" class="form-control filterbar-input-bg focus-ring" id="<?php echo $input['id']; ?>" onchange="<?php echo $input['onchange']; ?>" />
        </ul>
    </div>

<!-- Input listCheckbox -->
<?php elseif ($input['input'] == 'listCheckbox'): ?>
    <div class="p-0 m-0 filterbar-item">
        <h4 class="card-title h6 filterbar-font-size"><?php echo $input['label']; ?></h4>
        <ul class="list-group" id="<?php echo $input['id']; ?>">
            <div class="spinner-border spinner-color m-auto mt-2" role="status" style="width: 32px; height: 32px;"></div>
        </ul>
    </div>

<!-- Input checkbox -->
<?php elseif ($input['input'] == 'checkbox'): ?>
    <div class="p-0 m-0 filterbar-item">
        <h4 class="card-title h6 filterbar-font-size"><?php echo $input['label']; ?></h4>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="checkDescount">
            <label class="form-check-label" for="checkDescount">
                Solo en descuento
            </label>
        </div>
    </div>

<!-- Input rangPrice -->
<?php elseif ($input['input'] == 'rangePrice'): ?>
    <div class="p-0 m-0 filterbar-item">
        <h4 class="card-title h6 filterbar-font-size"><?php echo $input['label']; ?></h4>
        <ul class="list-group" id="nombre-filtro">
            <div class="input-group">
                <span class="input-group-text filterbar-span-bg filterbar-font-size">₡</span>
                <input type="number" class="form-control rangePrice-input filterbar-input-bg" id="<?php echo $input['idInicio']; ?>" onchange="<?php echo $input['onchangeInicio']; ?>" />
                <span class="input-group-text filterbar-span-bg filterbar-font-size">hasta</span>
                <span class="input-group-text filterbar-span-bg filterbar-font-size">₡</span>
                <input type="number" class="form-control rangePrice-input filterbar-input-bg" id="<?php echo $input['idFin']; ?>" onchange="<?php echo $input['onchangeFin']; ?>" />
            </div>
        </ul>
    </div>

<?php endif; ?>
