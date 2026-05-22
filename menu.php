<?php
// ============================================================
//  NetDTL v3.0 — Diagnostics réseau (ancien menu.php adapté)
// ============================================================
require_once __DIR__ . '/db.php';
requireAuth();
session_start();
initDB();

$pdo = getDB();

// ─── Traitement ─────────────────────────────────────────────
$action      = $_POST['action'] ?? ($_GET['tool'] ?? '');
$target      = trim($_POST['target'] ?? '');
$ports       = preg_replace('/[^0-9,\-]/', '', $_POST['ports'] ?? '22,80,443,3389,8080');
$resultLines = [];
$stats       = [];
$error       = '';

$validActions = ['ping','nmap','traceroute','dns','ports','infos','services'];

if ($action && in_array($action, $validActions)) {
    if (!in_array($action, ['infos','services']) && !isValidTarget($target)) {
        $error = 'Cible invalide. Saisissez une IP, un nom d\'hôte ou une plage CIDR (ex: 192.168.1.0/24).';
    } else {
        switch ($action) {
            case 'ping':
                $resultLines = runPing($target);
                $stats = parsePingStats($resultLines);
                break;
            case 'nmap':
                $resultLines = runCommand(NMAP_PATH . ' -sn ' . escapeshellarg($target));
                break;
            case 'traceroute':
                $resultLines = runCommand('tracert ' . escapeshellarg($target));
                break;
            case 'dns':
                $resultLines = runCommand('nslookup ' . escapeshellarg($target));
                break;
            case 'ports':
                $resultLines = runCommand(NMAP_PATH . ' -p ' . $ports . ' ' . escapeshellarg($target));
                break;
            case 'infos':
                $target = 'local';
                $resultLines = runCommand('powershell -ExecutionPolicy Bypass -Command "chcp 65001 | Out-Null; Get-NetIPAddress | Format-Table InterfaceAlias,AddressFamily,IPAddress,PrefixLength -AutoSize"');
                break;
            case 'services':
                $target = 'local';
                $resultLines = runCommand('powershell -ExecutionPolicy Bypass -Command "chcp 65001 | Out-Null; Get-Service | Where-Object {$_.Status -eq \'Running\'} | Select-Object Name,DisplayName,Status | Format-Table -AutoSize"');
                break;
        }
        // Log en BDD
        if (!$error && $resultLines) {
            $pdo->prepare("INSERT INTO diag_history (action, target, result, success) VALUES (?,?,?,1)")
                ->execute([$action, $target, implode("\n", array_slice($resultLines, 0, 20))]);
        }
    }
}

// ─── Export CSV ──────────────────────────────────────────────
if (($_POST['export'] ?? '') === 'csv' && $resultLines) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="diag_' . $action . '_' . date('Ymd_His') . '.csv"');
    echo "\xEF\xBB\xBF";
    foreach ($resultLines as $l) echo '"' . str_replace('"','""',$l) . '"' . "\r\n";
    exit;
}

// ─── Helpers affichage ───────────────────────────────────────
function colorLine(string $line, string $action): string {
    if ($line === '') return '';
    $l = htmlspecialchars($line);
    if ($action === 'ping') {
        if (preg_match('/TTL|Réponse|Reply/i', $line))        return "<span class='ok'>$l</span>";
        if (preg_match('/délai|timeout|unreachable/i', $line)) return "<span class='warn'>$l</span>";
        if (preg_match('/Échec|error|failed/i', $line))        return "<span class='err'>$l</span>";
        return "<span class='muted'>$l</span>";
    }
    if ($action === 'nmap') {
        if (preg_match('/scan report/i', $line)) return "<span class='ok'>$l</span>";
        if (preg_match('/open/i', $line))        return "<span class='ok'>$l</span>";
        if (preg_match('/filtered|closed/i', $line)) return "<span class='warn'>$l</span>";
        return "<span class='muted'>$l</span>";
    }
    if ($action === 'traceroute') {
        if (preg_match('/\*\s+\*\s+\*/', $line)) return "<span class='warn'>$l</span>";
        if (preg_match('/ms/i', $line))            return "<span class='ok'>$l</span>";
        return "<span class='muted'>$l</span>";
    }
    return "<span class='muted'>$l</span>";
}

$labels = [
    'ping'       => ['icon' => '◎', 'label' => 'Ping'],
    'nmap'       => ['icon' => '⬡', 'label' => 'Scan Nmap'],
    'traceroute' => ['icon' => '⤳', 'label' => 'Traceroute'],
    'dns'        => ['icon' => '⊹', 'label' => 'DNS Lookup'],
    'ports'      => ['icon' => '⊞', 'label' => 'Ports ciblés'],
    'infos'      => ['icon' => '⊕', 'label' => 'Infos IP locales'],
    'services'   => ['icon' => '⊗', 'label' => 'Services actifs'],
];

$recentDiag = $pdo->query("SELECT * FROM diag_history ORDER BY id DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NetDTL — Diagnostics</title>
<?php include __DIR__ . '/style.php'; ?>
</head>
<body>
<?php include __DIR__ . '/topbar.php'; ?>
<div class="layout">
<?php include __DIR__ . '/sidebar.php'; ?>

<main class="main">

    <!-- Toolbar -->
    <form method="post" class="toolbar" id="main-form">
        <input type="hidden" name="action" value="<?= htmlspecialchars($action ?: 'ping') ?>">
        <input
            type="text"
            name="target"
            placeholder="192.168.1.1 · 192.168.1.0/24 · hostname"
            value="<?= !in_array($action, ['infos','services']) ? htmlspecialchars($target) : '' ?>"
            <?= in_array($action, ['infos','services']) ? 'disabled' : '' ?>
        >
        <?php if ($action === 'ports'): ?>
        <input type="text" name="ports" class="ports-input" placeholder="22,80,443" value="<?= htmlspecialchars($ports) ?>">
        <?php else: ?>
        <input type="hidden" name="ports" value="<?= htmlspecialchars($ports) ?>">
        <?php endif; ?>
        <button type="submit" class="run-btn">▶ Exécuter</button>
        <?php if ($resultLines): ?>
        <button type="submit" name="export" value="csv" class="export-btn">↓ CSV</button>
        <?php endif; ?>
    </form>

    <div class="content">
        <div class="page-title">Diagnostics réseau</div>

        <?php if ($error): ?>
        <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Stats ping -->
        <?php if ($action === 'ping' && $resultLines && !$error): ?>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Envoyés</div><div class="stat-val"><?= $stats['sent'] ?></div></div>
            <div class="stat-card"><div class="stat-label">Reçus</div><div class="stat-val green"><?= $stats['recv'] ?></div></div>
            <div class="stat-card"><div class="stat-label">Perdus</div><div class="stat-val <?= $stats['lost_pct']>0?($stats['lost_pct']>50?'red':'amber'):'' ?>"><?= $stats['lost_pct'] ?>%</div></div>
            <div class="stat-card"><div class="stat-label">Latence moy.</div><div class="stat-val"><?= $stats['avg_ms'] !== null ? $stats['avg_ms'].' ms' : '—' ?></div></div>
        </div>
        <?php endif; ?>

        <!-- Terminal -->
        <div class="terminal-wrap">
            <div class="terminal-header">
                <div class="term-dots">
                    <div class="term-dot" style="background:#ff5f57"></div>
                    <div class="term-dot" style="background:#febc2e"></div>
                    <div class="term-dot" style="background:#28c840"></div>
                </div>
                <span class="term-title">
                    <?= $action && !$error ? htmlspecialchars($action).' '.htmlspecialchars($target) : 'terminal' ?>
                </span>
                <?php if ($resultLines): ?>
                <button class="term-copy" onclick="copyOutput()">Copier</button>
                <?php endif; ?>
            </div>

            <div class="terminal-body" id="terminal-body">
                <?php if (!$action || $error): ?>
                    <?php if (!$error): ?>
                    <div class="empty-state">
                        <div class="big">⊹</div>
                        Sélectionnez un outil dans la barre latérale,<br>
                        saisissez une cible et cliquez sur <strong>Exécuter</strong>.
                    </div>
                    <?php endif; ?>

                <?php elseif ($action === 'nmap'): ?>
                    <table class="data-table">
                        <thead><tr><th>Hôte / IP</th><th>Statut</th></tr></thead>
                        <tbody>
                        <?php foreach ($resultLines as $line):
                            if (preg_match('/Nmap scan report for (.+)/i', $line, $m)): ?>
                            <tr>
                                <td class="mono"><?= htmlspecialchars($m[1]) ?></td>
                                <td><span class="badge badge-up">actif</span></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php elseif ($action === 'ports'): ?>
                    <table class="data-table">
                        <thead><tr><th>Port</th><th>Protocole</th><th>État</th><th>Service</th></tr></thead>
                        <tbody>
                        <?php foreach ($resultLines as $line):
                            if (preg_match('/^(\d+)\/(tcp|udp)\s+(\S+)\s+(\S+)/i', $line, $m)): ?>
                            <tr>
                                <td class="mono"><?= htmlspecialchars($m[1]) ?></td>
                                <td><?= htmlspecialchars($m[2]) ?></td>
                                <td><span class="badge <?= $m[3]==='open'?'badge-open':'' ?>"><?= htmlspecialchars($m[3]) ?></span></td>
                                <td class="muted"><?= htmlspecialchars($m[4]) ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else: ?>
                    <span class="line"><span class="prompt">$</span> <span class="cmd"><?= htmlspecialchars($action) ?> <?= htmlspecialchars($target) ?></span></span>
                    <?php foreach ($resultLines as $line):
                        if ($line === '') { echo '<span class="line"> </span>'; continue; }
                        echo '<span class="line">' . colorLine($line, $action) . '</span>';
                    endforeach; ?>
                <?php endif; ?>
                <pre id="raw-output"><?php foreach ($resultLines as $l) echo htmlspecialchars($l)."\n"; ?></pre>
            </div>
        </div>

        <!-- Historique diagnostics -->
        <?php if ($recentDiag): ?>
        <div class="panel">
            <div class="panel-header">Historique de session</div>
            <div class="history-list">
            <?php foreach ($recentDiag as $h): ?>
                <div class="history-item">
                    <span class="h-badge h-<?= strtolower($h['action']) ?>"><?= htmlspecialchars($h['action']) ?></span>
                    <span class="h-target"><?= htmlspecialchars($h['target']) ?></span>
                    <span class="h-time"><?= timeAgo($h['created']) ?></span>
                    <span class="<?= $h['success']?'h-ok':'h-fail' ?>"><?= $h['success']?'✓':'✗' ?></span>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>
</div>

<script>
function copyOutput() {
    const raw = document.getElementById('raw-output');
    if (!raw) return;
    navigator.clipboard.writeText(raw.innerText).then(() => {
        const btn = document.querySelector('.term-copy');
        if (btn) { btn.textContent='✓ Copié'; setTimeout(()=>btn.textContent='Copier',1500); }
    });
}
const tb = document.getElementById('terminal-body');
if (tb) tb.scrollTop = tb.scrollHeight;

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        const tool = new URL(this.href).searchParams.get('tool');
        if (tool) {
            e.preventDefault();
            document.querySelector('[name="action"]').value = tool;
            document.getElementById('main-form').submit();
        }
    });
});
</script>
</body>
</html>
