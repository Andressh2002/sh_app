<?php if ($menuTable['showCount']): ?>
    <div class="navbar-btn-shadow">
        <p class="order-count-card" id="total-data">Contando...</p>
    </div>
<?php endif; ?>

<?php if ($menuTable['showInfo']): ?>
    <button type="button" onclick="showInfo('<?php echo $menuTable['pageInfo']; ?>')" id="btn-informacion"
        class="btn-details text-white border-0 rounded-pill px-3 py-2 d-flex gap-2 justify-content-center align-items-center" style="height: 40px; width:40px;">
        <i class="d-flex bi bi-info-circle"></i>
    </button>
<?php endif; ?>

<?php if ($menuTable['showAdd']): ?>
    <div class="navbar-btn-shadow">
        <a
            id="btn-agregar"
            href="<?php echo $menuTable['url']; ?>"
            class="store-filter-btn slide_from_left text-decoration-none"
        >
            <i class="bi bi-file-earmark-plus-fill"></i>
            <span>Agregar</span>
        </a>
    </div>
<?php endif; ?>

<?php if ($menuTable['showUpdate']): ?>
    <div class="navbar-btn-shadow">
        <button
            id="btn-refrescar"
            onclick="<?php echo $menuTable['updateMethod']; ?>; <?php echo $menuTable['clearMethod']; ?>"
            class="store-filter-btn slide_from_left"
        >
            <i class="bi bi-arrow-repeat"></i>
            <span>Actualizar</span>
        </button>
    </div>
<?php endif; ?>