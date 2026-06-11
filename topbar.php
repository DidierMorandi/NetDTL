<?php
// topbar.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="topbar">
    <a href="index.php" class="topbar-logo">NetDTL</a>
    <div class="topbar-sep"></div>
    <nav class="topbar-nav">
        <a href="index.php"     class="<?= $currentPage==='index'?'active':'' ?>">Dashboard</a>
        <a href="inventory.php" class="<?= $currentPage==='inventory'||$currentPage==='machine'?'active':'' ?>">Inventaire</a>
        <a href="discovery.php" class="<?= $currentPage==='discovery'?'active':'' ?>">Découverte</a>
        <a href="patch.php"     class="<?= $currentPage==='patch'?'active':'' ?>">Brassage</a>
                <a href="menu.php"      class="<?= $currentPage==='menu'?'active':'' ?>">Diagnostics</a>
    </nav>
    <span class="topbar-user">● <?= htmlspecialchars(AUTH_USER) ?></span>
</div>
