<?php
// ============================================================
//  NetDTL v3.0 — Panneau de brassage
// ============================================================
require_once __DIR__ . '/db.php';
requireAuth();
session_start();
initDB();

$pdo = getDB();
$message = ''; $msgType = '';

// ─── Filtres ─────────────────────────────────────────────────
$filter_switch = $_GET['switch'] ?? '';
$filter_entite = $_GET['entite'] ?? '';
$search        = trim($_GET['q'] ?? '');

$where = []; $params = [];
if ($filter_switch) { $where[] = "p.switch=?"; $params[] = $filter_switch; }
if ($filter_entite) { $where[] = "p.entite=?"; $params[] = $filter_entite; }
if ($search)        { $where[] = "(p.prise LIKE ? OR p.local_name LIKE ? OR p.poste LIKE ? OR pm.hostname LIKE ? OR pm.machine_ip LIKE ?)"; $s="%$search%"; $params=array_merge($params,[$s,$s,$s,$s,$s]); }

$sql = "SELECT p.*, GROUP_CONCAT(pm.machine_ip ORDER BY pm.id SEPARATOR ', ') as ips,
               GROUP_CONCAT(pm.hostname ORDER BY pm.id SEPARATOR ', ') as hostnames
        FROM patch_panel p
        LEFT JOIN patch_machines pm ON p.prise = pm.prise"
    . ($where ? ' WHERE '.implode(' AND ',$where) : '')
    . " GROUP BY p.id ORDER BY LEFT(p.prise,1), CAST(SUBSTRING(p.prise,2) AS UNSIGNED)";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$prises = $stmt->fetchAll();

$total    = (int)$pdo->query("SELECT COUNT(*) FROM patch_panel")->fetchColumn();
$id_count = (int)$pdo->query("SELECT COUNT(DISTINCT prise) FROM patch_machines")->fetchColumn();
$switches = $pdo->query("SELECT DISTINCT switch FROM patch_panel WHERE switch IS NOT NULL ORDER BY switch")->fetchAll(PDO::FETCH_COLUMN);
$entites  = $pdo->query("SELECT DISTINCT entite FROM patch_panel WHERE entite IS NOT NULL ORDER BY entite")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NetDTL — Panneau de brassage</title>
<?php include __DIR__ . '/style.php'; ?>
<style>
.badge-rj45 { background:rgba(88,166,255,.15); color:var(--accent2); }
.badge-rj11 { background:rgba(210,153,34,.15); color:var(--warn); }
.badge-unkn { background:rgba(139,148,158,.15); color:var(--txt2); }
.badge-deleg { background:rgba(63,185,80,.12); color:var(--accent); }
.badge-eqloc { background:rgba(100,200,200,.12); color:var(--teal); }
.multi-ip { font-size:11px; color:var(--accent2); }
</style>
</head>
<body>
<?php include __DIR__ . '/topbar.php'; ?>
<div class="layout">
<?php include __DIR__ . '/sidebar.php'; ?>
<main class="main">

    <!-- Toolbar -->
    <form method="get" class="toolbar">
        <input type="text" name="q" placeholder="Prise, local, poste, IP, hostname…" value="<?= htmlspecialchars($search) ?>">
        <select name="switch" class="ports-input" style="width:100px">
            <option value="">Tous switches</option>
            <?php foreach($switches as $sw): ?>
            <option value="<?= htmlspecialchars($sw) ?>" <?= $filter_switch===$sw?'selected':'' ?>><?= htmlspecialchars($sw) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="entite" class="ports-input" style="width:160px">
            <option value="">Toutes entités</option>
            <?php foreach($entites as $e): ?>
            <option value="<?= htmlspecialchars($e) ?>" <?= $filter_entite===$e?'selected':'' ?>><?= htmlspecialchars($e) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="run-btn">Filtrer</button>
        <a href="patch.php" class="export-btn">Réinitialiser</a>
    </form>

    <div class="content">
        <div class="page-title">Panneau de brassage</div>

        <?php if ($message): ?>
        <div class="<?= $msgType==='error'?'error-box':'success-box' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Total prises</div><div class="stat-val"><?= $total ?></div></div>
            <div class="stat-card"><div class="stat-label">Identifiées</div><div class="stat-val green"><?= $id_count ?></div></div>
            <div class="stat-card"><div class="stat-label">À identifier</div><div class="stat-val amber"><?= $total - $id_count ?></div></div>
            <div class="stat-card"><div class="stat-label">Résultats</div><div class="stat-val"><?= count($prises) ?></div></div>
        </div>

        <!-- Table -->
        <div class="panel">
            <div class="panel-header">
                Liste des prises murales
                <span style="color:var(--txt3)"><?= count($prises) ?> prise(s)</span>
            </div>
            <table class="data-table">
                <thead><tr>
                    <th>Prise</th><th>Type</th><th>Entité</th><th>Local</th><th>Étage</th><th>Poste</th><th>Switch</th><th>Port switch</th><th>IP(s) / Hostname(s)</th><th>Notes</th>
                </tr></thead>
                <tbody>
                <?php if (!$prises): ?>
                    <tr><td colspan="10" class="empty-cell">Aucune prise trouvée.</td></tr>
                <?php endif; ?>
                <?php foreach ($prises as $p):
                    $typCls = $p['type']==='RJ45' ? 'badge-rj45' : ($p['type']==='RJ11' ? 'badge-rj11' : 'badge-unkn');
                    $entCls = str_contains(strtolower($p['entite']??''), 'locale') ? 'badge-eqloc' : 'badge-deleg';
                ?>
                <tr>
                    <td><strong style="color:var(--txt)"><?= htmlspecialchars($p['prise']) ?></strong></td>
                    <td><span class="badge <?= $typCls ?>"><?= htmlspecialchars($p['type'] ?? '?') ?></span></td>
                    <td><?php if($p['entite']): ?><span class="badge <?= $entCls ?>" style="font-size:10px"><?= htmlspecialchars($p['entite']) ?></span><?php else: echo '—'; endif; ?></td>
                    <td class="muted small"><?= htmlspecialchars($p['local_name'] ?? '—') ?></td>
                    <td class="muted small"><?= htmlspecialchars($p['etage'] ?? '—') ?></td>
                    <td class="small"><?= htmlspecialchars($p['poste'] ?? '—') ?></td>
                    <td class="mono small" style="color:var(--warn)"><?= htmlspecialchars($p['switch'] ?? '—') ?></td>
                    <td class="mono small" style="color:var(--accent2)"><?= htmlspecialchars($p['port_switch'] ?? '—') ?></td>
                    <td>
                        <?php if ($p['ips']): ?>
                        <div class="mono small" style="color:var(--accent2)"><?= htmlspecialchars($p['ips']) ?></div>
                        <div class="multi-ip"><?= htmlspecialchars($p['hostnames'] ?? '') ?></div>
                        <?php else: ?>
                        <span class="muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="muted small" style="font-style:italic"><?= htmlspecialchars($p['notes'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</div>
</body>
</html>