<!-- Input text -->
<?php if ($input['input'] == 'text'): ?>
    <div class="filter-card px-4 px-sm-5">

        <p class="filter-title">
            <i class="bi bi-search"></i>
            <?php echo $input['label']; ?>
        </p>

        <input
            type="text"
            class="form-control filter-input"
            id="<?php echo $input['id']; ?>"
            onchange="<?php echo $input['onchange']; ?>"
            placeholder="Buscar producto..."
        />

    </div>

<!-- Input listCheckbox -->
<?php elseif ($input['input'] == 'listCheckbox'): ?>
    <div class="filter-card px-4 px-sm-5">

        <p class="filter-title">
            <i class="bi bi-ui-checks-grid"></i>
            <?php echo $input['label']; ?>
        </p>

        <ul
            class="list-group filter-list"
            id="<?php echo $input['id']; ?>"
        >
            <div
                class="spinner-border spinner-color m-auto mt-2"
                role="status"
                style="width: 32px; height: 32px;"
            ></div>
        </ul>

    </div>

<!-- Input checkbox -->
<?php elseif ($input['input'] == 'checkbox'): ?>
    <div class="filter-card px-4 px-sm-5">
        <p class="card-title h6 filterbar-font-size"><?php echo $input['label']; ?></p>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="checkDescount">
            <label class="form-check-label" for="checkDescount">
                Solo en descuento
            </label>
        </div>
    </div>

<!-- Input rangPrice -->
<?php elseif ($input['input'] == 'rangePrice'): ?>
    <div class="filter-card px-4 px-sm-5">

        <p class="filter-title">
            <i class="bi bi-cash-stack"></i>
            <?php echo $input['label']; ?>
        </p>

        <div class="input-group">

            <span class="input-group-text filter-group-text">
                ₡
            </span>

            <input
                type="number"
                class="form-control filter-input"
                id="<?php echo $input['idInicio']; ?>"
                onchange="<?php echo $input['onchangeInicio']; ?>"
                placeholder="Mín"
            />

            <span class="input-group-text filter-group-text">
                —
            </span>

            <input
                type="number"
                class="form-control filter-input"
                id="<?php echo $input['idFin']; ?>"
                onchange="<?php echo $input['onchangeFin']; ?>"
                placeholder="Máx"
            />

        </div>

    </div>

<?php endif; ?>
