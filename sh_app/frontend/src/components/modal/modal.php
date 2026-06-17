<?php

$id         = $modal['id'] ?? 'modal';
$title      = $modal['title'] ?? '';
$icon       = $modal['icon'] ?? '';
$size       = $modal['size'] ?? 'modal-lg';

$body       = $modal['body'] ?? null;
$form       = $modal['form'] ?? [];
$buttons    = $modal['buttons'] ?? [];

$backdrop     = $modal['backdrop'] ?? true;
$keyboard     = $modal['keyboard'] ?? true;
$fullscreen   = $modal['fullscreen'] ?? false;
$btn_close    = $modal['btn_close'] ?? false;

$centered = $modal['centered'] ?? true;

?>

<div
    class="modal fade px-0"
    id="<?php echo $id; ?>"
    tabindex="-1"
    aria-hidden="true"

    data-bs-backdrop="<?php echo $backdrop === 'static' ? 'static' : ($backdrop ? 'true' : 'false'); ?>"
    data-bs-keyboard="<?php echo $keyboard ? 'true' : 'false'; ?>"
>

    <div class="
        modal-dialog
        modal-dialog-scrollable

        <?php echo $size; ?>
        <?php echo $fullscreen ? 'modal-fullscreen' : ''; ?>
        <?php echo $centered ? 'modal-dialog-centered' : ''; ?>
    ">
        <div class="store-modal-shadow">
            <div class="modal-content store-modal">

                <!-- HEADER -->
                <div class="store-modal-header">
                    <div class="d-flex align-items-center gap-2 px-1 px-sm-2">
                        <?php if ($icon): ?>
                            <i id="<?php echo $id; ?>-header-icon" class="<?php echo $icon; ?>"></i>
                        <?php endif; ?>
                        <p id="<?php echo $id; ?>-header-label" class="m-0">
                            <?php echo $title; ?>
                        </p>
                    </div>

                    <button
                        id="<?php echo $id; ?>-close-btn"
                        type="button"
                        class="store-modal-close <?php echo (!empty($btn_close)) ? 'visually-hidden' : ''?>"
                        data-bs-dismiss="modal"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- BODY -->
                <div id="<?php echo $id; ?>-body" class="store-modal-body">
                    <?php if ($body): ?>

                        <?php echo $body; ?>

                    <?php else: ?>

                        <?php foreach ($form as $input): ?>
                            <?php include '../src/components/inputs/input.php'; ?>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>

                <!-- FOOTER -->
                <?php if (!empty($buttons)): ?>
                    <div class="store-modal-footer">
                        <?php foreach ($buttons as $button): ?>
                            <?php

                            $btnText       = $button['text'] ?? 'Botón';
                            $btnIcon       = $button['icon'] ?? '';
                            $btnClass      = $button['class'] ?? 'store-filter-btn';

                            $btnOnclick    = $button['onclick'] ?? '';
                            $btnDismiss    = $button['dismiss'] ?? false;

                            ?>
                            <div class="navbar-btn-shadow">
                                <button
                                    class="<?php echo $btnClass; ?> slide_from_left px-4"
                                    <?php if ($btnOnclick): ?>
                                        onclick="<?php echo $btnOnclick; ?>"
                                    <?php endif; ?>
                                    <?php if ($btnDismiss): ?>
                                        data-bs-dismiss="modal"
                                    <?php endif; ?>
                                >
                                    <?php if ($btnIcon): ?>
                                        <i class="<?php echo $btnIcon; ?>"></i>
                                    <?php endif; ?>
                                    <span>
                                        <?php echo $btnText; ?>
                                    </span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>