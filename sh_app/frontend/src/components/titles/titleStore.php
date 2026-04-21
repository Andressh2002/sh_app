<?php
$titleText = $title['title'] ?? '';
$text = $title['text'] ?? null;
$icon = $title['icon'] ?? null;
?>

<div class="p-0 my-3 title-element">
    <div class="m-auto row container-sm w-100">
        <div class="d-flex align-items-center gap-2">
            <?php if ($titleText): ?>
                <p class="fw-bold card-category-text-h m-0">
                    <?php echo $titleText; ?>
                </p>
            <?php endif; ?>
            <?php if ($icon): ?>
                <i class="<?php echo $icon; ?> fs-5 d-flex align-self-center m-0"></i>
            <?php endif; ?>
        </div>
        <?php if (!empty($text)): ?>
            <p class="card-category-text-p m-0">
                <?php echo $text; ?>
            </p>
        <?php endif; ?>
    </div>
</div>