<?php
// sidebar.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar">
    <span class="sidebar-section">Navigation</span>
    <a href="index.php"     class="nav-link <?= $currentPage==='index'?'active':'' ?>"><span class="icon">⊹</span> Dashboard</a>
    <a href="inventory.php" class="nav-link <?= $currentPage==='inventory'?'active':'' ?>"><span class="icon">⊞</span> Inventaire</a>
    <a href="discovery.php" class="nav-link <?= $currentPage==='discovery'?'active':'' ?>"><span class="icon">⬡</span> Découverte réseau</a>
    <a href="patch.php" class="nav-link <?= $currentPage==='patch'?'active':'' ?>"><span class="icon">⊟</span> Panneau de brassage</a>

    <span class="sidebar-section">Diagnostics</span>
    <?php
    // Après un POST, $action est défini dans menu.php ; on le récupère ici
    $activeTool = $currentPage === 'menu' ? ($action ?? $_GET['tool'] ?? '') : '';
    ?>
    <a href="menu.php?tool=ping"       class="nav-link <?= $activeTool==='ping'?'active':'' ?>"><span class="icon">◎</span> Ping</a>
    <a href="menu.php?tool=nmap"       class="nav-link <?= $activeTool==='nmap'?'active':'' ?>"><span class="icon">⬡</span> Scan Nmap</a>
    <a href="menu.php?tool=traceroute" class="nav-link <?= $activeTool==='traceroute'?'active':'' ?>"><span class="icon">⤳</span> Traceroute</a>
    <a href="menu.php?tool=dns"        class="nav-link <?= $activeTool==='dns'?'active':'' ?>"><span class="icon">⊹</span> DNS Lookup</a>
    <a href="menu.php?tool=ports"      class="nav-link <?= $activeTool==='ports'?'active':'' ?>"><span class="icon">⊞</span> Ports ciblés</a>
    <a href="menu.php?tool=infos"      class="nav-link <?= $activeTool==='infos'?'active':'' ?>"><span class="icon">⊕</span> Infos IP locales</a>
    <a href="menu.php?tool=services"   class="nav-link <?= $activeTool==='services'?'active':'' ?>"><span class="icon">⊗</span> Services actifs</a>

    <div class="sidebar-spacer"></div>
    <span class="sidebar-version">NetDTL v<?= APP_VERSION ?> · PHP <?= PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION ?></span>
</aside>
