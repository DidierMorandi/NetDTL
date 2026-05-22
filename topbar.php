<?php
// topbar.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="topbar">
    <a href="index.php" class="topbar-logo">
        <img src="logo.png" alt="NetDTL" style="height:28px;vertical-align:middle;margin-right:8px;">
        <span style="font-size:10px;color:var(--txt3);font-weight:400;letter-spacing:.08em">v<?= APP_VERSION ?></span>
        <span style="font-size:9px;color:#ffffff;font-weight:400;display:block;line-height:1.2;margin-top:1px">version 1.0-1 du 23 mai 2026</span>
    </a>
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
