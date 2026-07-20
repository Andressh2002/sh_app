<?php
    $appVersion = "v4.3.0";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?></title>

    <?php include '../layouts/head.html'; ?>
</head>

<body class="d-flex flex-column min-dvh-100 overflow-visible px-0">

    <?php if (isset($showHeader) && $showHeader): ?>
        <?php include '../layouts/header.php'; ?>
    <?php endif; ?>

    <?php if (isset($showNavbar) && $showNavbar): ?>
        <?php include '../layouts/navbar.php'; ?>
    <?php endif; ?>

    <?php if (isset($showSidebar) && $showSidebar): ?>
        <?php include '../layouts/sidebar.php'; ?>
    <?php endif; ?>

    <?php if (!$showSidebar): ?>
        <div class="container-fluid flex-fill px-0">
            <?php echo $content; ?>
        </div>
    <?php endif; ?>
    
    <?php include '../layouts/foot.html'; ?>

</body>

<?php if (isset($showFooter) && $showFooter): ?>
    <footer class="text-center text-lg-start text-muted pt-3 mt-4" style="background-color: #DDD;">
        <?php include '../layouts/footer.html'; ?>
    </footer>
<?php endif; ?>


</html>

<?php
    include '../src/components/modal/modals.php';
?>