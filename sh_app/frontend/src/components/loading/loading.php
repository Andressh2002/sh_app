<?php
$hiddenClass = $loadingIcon['hiddenClass'] ?? '';
$id = $loadingIcon['id'] ?? null;
$width = $loadingIcon['width'] ?? '40px';
$height = $loadingIcon['height'] ?? '40px';
?>

<div class="spinner-border spinner-color custom-spinner <?php echo $hiddenClass ?>" role="status" id="<?php echo $id ?>" style="width: <?php echo $width ?>; height: <?php echo $height ?>;">
  <span class="visually-hidden"></span>
</div>