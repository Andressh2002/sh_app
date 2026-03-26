<?php if ($menuTable['showInfo']): ?>
    <button type="button" onclick="showInfo('<?php echo $menuTable['pageInfo']; ?>')"
        class="btn-details text-white border-0 rounded-pill px-3 py-2 d-flex gap-2 justify-content-center align-items-center" style="height: 40px; width:40px;">
        <i class="d-flex bi bi-info-circle"></i>
    </button>
<?php endif; ?>

<?php if ($menuTable['showAdd']): ?>
    <button type="button" onclick="location.href='<?php echo $menuTable['url']; ?>'"
        class="btn-details text-white border-0 rounded-2 px-3 py-2 d-flex gap-2 justify-content-center align-items-center">
        Agregar<i class="bi bi-file-earmark-plus-fill"></i>
    </button>
<?php endif; ?>

<?php if ($menuTable['showUpdate']): ?>
    <button type="button" onclick="<?php echo $menuTable['updateMethod']; ?>; <?php echo $menuTable['clearMethod']; ?>"
        class="btn-details text-white border-0 rounded-2 px-3 py-2 d-flex gap-2 justify-content-center align-items-center">
        Actualizar<i class="bi bi-arrow-clockwise"></i>
    </button>
<?php endif; ?>