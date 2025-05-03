<?php include __DIR__ . '/header.php'; ?>
<?php 
    $isLoggedIn = $isLoggedIn ?? false;
?>

<?php if ($isLoggedIn): ?>
    <?php include  __DIR__ . '/navbar_user.php'; ?>
<?php else: ?>
    <?php include  __DIR__ . '/navbar_guest.php'; ?>
<?php endif; ?>
<!-- End Navbar -->

<div class="content" id="main">
    <?= $content ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
