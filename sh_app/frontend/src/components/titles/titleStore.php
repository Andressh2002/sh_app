<?php
$titleText = $title['title'] ?? '';
$text = $title['text'] ?? null;
$icon = $title['icon'] ?? null;
?>

<div class="p-0 my-3 title-element py-5">
    <div class="m-auto row container-sm w-100">
        <div class="d-flex align-items-center gap-2">
            <?php if ($titleText): ?>
                <h2 class="m-0">
                    <?php echo $titleText; ?>
                </h2>
            <?php endif; ?>
            <?php if ($icon): ?>
                <i class="<?php echo $icon; ?> ms-2 fs-2 d-flex align-self-center m-0"></i>
            <?php endif; ?>
        </div>
        <?php if (!empty($text)): ?>
            <p class="m-0">
                <?php echo $text; ?>
            </p>
        <?php endif; ?>
    </div>
</div>