<?php
// ============================================================
//  NetDTL v3.0 — Découverte réseau (streaming temps réel)
// ============================================================
require_once __DIR__ . '/db.php';
requireAuth();
session_start();
initDB();

$pdo = getDB();
$scanHistory  = $pdo->query("SELECT * FROM scan_history ORDER BY id DESC LIMIT 10")->fetchAll();
$machineCount = (int)$pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
$upCount      = (int)$pdo->query("SELECT COUNT(*) FROM machines WHERE status='up'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NetDTL — Découverte réseau</title>
<?php include __DIR__ . '/style.php'; ?>
<style>
.scan-terminal { background:var(--bg); border:1px solid var(--border); border-radius:8px; overflow:hidden; display:none; }
.scan-terminal.visible { display:block; }
.scan-term-header { padding:10px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; background:var(--bg2); }
.scan-term-body { padding:14px 16px; max-height:320px; overflow-y:auto; font-family:var(--mono); font-size:12px; line-height:1.8; }
.scan-term-body::-webkit-scrollbar { width:4px; }
.scan-term-body::-webkit-scrollbar-thumb { background:var(--border2); border-radius:2px; }
.host-table { width:100%; border-collapse:collapse; }
.host-table th { background:var(--bg3); color:var(--txt2); font-weight:500; padding:8px 12px; text-align:left; border-bottom:1px solid var(--border); font-family:var(--sans); font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
.host-table td { padding:7px 12px; border-bottom:1px solid var(--border); font-size:12px; }
.host-table tr:last-child td { border-bottom:none; }
.host-table tr { animation:fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:none} }
.scan-progress { height:3px; background:var(--bg3); border-radius:2px; overflow:hidden; margin-top:8px; }
.scan-progress-bar { height:100%; background:var(--accent); border-radius:2px; width:0%; }
.scan-progress-bar.running { animation:indeterminate 1.5s ease-in-out infinite; }
.scan-progress-bar.done { animation:none; width:100%; }
@keyframes indeterminate { 0%{transform:translateX(-100%);width:60%} 100%{transform:translateX(200%);width:60%} }
</style>
</head>
<body>
<?php include __DIR__ . '/topbar.php'; ?>
<div class="layout">
<?php include __DIR__ . '/sidebar.php'; ?>
<main class="main">

    <!-- Toolbar -->
    <div class="toolbar">
        <input type="text" id="network-input" placeholder="172.17.7.0/24" value="<?= htmlspecialchars(DEFAULT_NETWORK) ?>">
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--txt2);white-space:nowrap">
            <input type="checkbox" id="opt-ports"> Ports communs
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--txt2);white-space:nowrap">
            <input type="checkbox" id="opt-os"> Détection OS <span style="color:var(--txt3)">(admin requis)</span>
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--txt2);white-space:nowrap">
            <input type="checkbox" id="opt-nbstat"> Identifier NetBIOS
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--txt2);white-space:nowrap">
            <input type="checkbox" id="opt-wmi"> Descriptions WMI <span style="color:var(--txt3)">(PC Windows)</span>
        </label>
        <button class="run-btn" id="scan-btn" onclick="startScan()">▶ Lancer le scan</button>
        <button class="export-btn" id="stop-btn" onclick="stopScan()" style="display:none">■ Arrêter</button>
    </div>

    <div class="content">
        <div class="page-title">Découverte réseau</div>

        <div id="status-box" class="info-box">
            Saisissez une plage réseau (CIDR) et cliquez sur <strong>Lancer le scan</strong>.
            Les machines découvertes sont automatiquement ajoutées à l'inventaire.
        </div>

        <div class="scan-progress" id="progress-wrap" style="display:none">
            <div class="scan-progress-bar" id="progress-bar"></div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Machines connues</div><div class="stat-val" id="stat-total"><?= $machineCount ?></div></div>
            <div class="stat-card"><div class="stat-label">En ligne</div><div class="stat-val green" id="stat-up"><?= $upCount ?></div></div>
            <div class="stat-card"><div class="stat-label">Découvertes (scan)</div><div class="stat-val amber" id="stat-found">—</div></div>
            <div class="stat-card"><div class="stat-label">Durée</div><div class="stat-val" id="stat-duration" style="font-size:14px">—</div></div>
        </div>

        <!-- Terminal nmap -->
        <div class="scan-terminal" id="scan-terminal">
            <div class="scan-term-header">
                <div class="term-dots">
                    <div class="term-dot" style="background:#ff5f57"></div>
                    <div class="term-dot" style="background:#febc2e"></div>
                    <div class="term-dot" style="background:#28c840"></div>
                </div>
                <span style="font-family:var(--sans);font-size:11px;color:var(--txt3);flex:1">nmap output</span>
                <span class="scanning" id="scanning-badge">● scanning…</span>
            </div>
            <div class="scan-term-body" id="scan-output"></div>
        </div>

        <!-- Hôtes découverts -->
        <div class="panel" id="hosts-panel" style="display:none">
            <div class="panel-header">
                Hôtes découverts
                <a href="inventory.php" class="panel-link">Voir l'inventaire →</a>
            </div>
            <table class="host-table">
                <thead><tr>
                    <th>IP</th><th>MAC</th><th>Description</th><th>Statut</th>
                </tr></thead>
                <tbody id="hosts-tbody"></tbody>
            </table>
        </div>

        <!-- Historique -->
        <div class="panel">
            <div class="panel-header">Historique des scans</div>
            <table class="data-table">
                <thead><tr><th>Date</th><th>Réseau</th><th>Hôtes</th><th>Durée</th></tr></thead>
                <tbody id="history-tbody">
                <?php if (!$scanHistory): ?>
                    <tr><td colspan="4" class="empty-cell">Aucun scan effectué.</td></tr>
                <?php endif; ?>
                <?php foreach ($scanHistory as $s): ?>
                <tr>
                    <td class="mono small"><?= $s['scan_date'] ?></td>
                    <td class="mono"><?= htmlspecialchars($s['network']) ?></td>
                    <td><span class="badge badge-up"><?= $s['hosts_up'] ?> up</span></td>
                    <td class="muted small"><?= $s['duration_s'] ?>s</td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>
</div>

<script>
let evtSource = null;
const hostsMap = {};

function startScan() {
    const network = document.getElementById('network-input').value.trim();
    const doports = document.getElementById('opt-ports').checked ? '1' : '0';
    const doOS    = document.getElementById('opt-os').checked    ? '1' : '0';
    if (!network) return;

    // Reset
    document.getElementById('scan-output').innerHTML = '';
    document.getElementById('hosts-tbody').innerHTML = '';
    document.getElementById('hosts-panel').style.display = 'none';
    document.getElementById('scan-terminal').classList.add('visible');
    document.getElementById('progress-wrap').style.display = 'block';
    document.getElementById('progress-bar').className = 'scan-progress-bar running';
    document.getElementById('scan-btn').disabled = true;
    document.getElementById('scan-btn').textContent = '⟳ Scan en cours…';
    document.getElementById('stop-btn').style.display = '';
    document.getElementById('scanning-badge').style.display = '';
    document.getElementById('stat-found').textContent = '0';
    document.getElementById('stat-duration').textContent = '…';
    Object.keys(hostsMap).forEach(k => delete hostsMap[k]);
    setStatus('info', `Scan de <strong>${esc(network)}</strong> en cours…`);

    const doNbstat = document.getElementById('opt-nbstat').checked ? '1' : '0';
    const doWmi    = document.getElementById('opt-wmi').checked    ? '1' : '0';
    evtSource = new EventSource(`scan_stream.php?network=${encodeURIComponent(network)}&ports=${doports}&os=${doOS}&nbstat=${doNbstat}&wmi=${doWmi}`);
    evtSource.onmessage = e => handleEvent(JSON.parse(e.data));
    evtSource.onerror   = () => { evtSource.close(); scanDone(); };
}

function handleEvent(data) {
    const output = document.getElementById('scan-output');
    switch (data.type) {
        case 'line': {
            const d = document.createElement('div');
            d.className = /scan report/i.test(data.msg) ? 'ok'
                        : /MAC Address/i.test(data.msg)  ? 'ok'
                        : /OS details/i.test(data.msg)   ? 'warn'
                        : /warning|error/i.test(data.msg)? 'err' : 'muted';
            d.textContent = data.msg;
            output.appendChild(d);
            output.scrollTop = output.scrollHeight;
            break;
        }
        case 'mac':
            if (hostsMap[data.ip]) {
                hostsMap[data.ip].querySelector('.col-mac').textContent  = data.mac;
                // enrichit la description avec le fabricant
                const descEl = hostsMap[data.ip].querySelector('.col-desc');
                if (descEl && data.vendor && data.vendor !== 'Unknown') {
                    descEl.textContent = descEl.textContent + ' — ' + data.vendor;
                }
            }
            break;
        case 'os':
            if (hostsMap[data.ip]) {
                hostsMap[data.ip].querySelector('.col-os').textContent = data.os;
            }
            break;
        case 'host_saved': {
            document.getElementById('hosts-panel').style.display = 'block';
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="mono">${esc(data.ip)}</td>
                <td class="mono small col-mac">${esc(data.mac || '—')}</td>
                <td class="muted small col-desc">${esc(data.hostname)}</td>
                <td><span class="badge badge-up">up</span></td>
            `;
            document.getElementById('hosts-tbody').appendChild(tr);
            hostsMap[data.ip] = tr;
            document.getElementById('stat-found').textContent = Object.keys(hostsMap).length;
            break;
        }
        case 'done':
            evtSource.close();
            scanDone();
            document.getElementById('progress-bar').className = 'scan-progress-bar done';
            document.getElementById('stat-found').textContent    = data.hosts;
            document.getElementById('stat-duration').textContent = data.duration + 's';
            setStatus('success', `✓ Scan terminé — <strong>${data.hosts}</strong> hôte(s) en ${data.duration}s. Inventaire mis à jour.`);
            addHistoryRow(document.getElementById('network-input').value, data.hosts, data.duration);
            break;
        case 'wmi':
            // Met à jour la description dans le tableau si on a une description WMI
            if (hostsMap[data.ip] && data.desc) {
                const descEl = hostsMap[data.ip].querySelector('.col-desc');
                if (descEl) {
                    const current = descEl.textContent;
                    // Ajoute la description WMI si pas déjà là
                    if (!current.includes(data.desc)) {
                        descEl.textContent = current + (current ? ' — ' : '') + data.desc;
                    }
                }
            }
            break;
        case 'nbstat':
            // Met à jour la description dans le tableau si on a un nom NetBIOS
            if (hostsMap[data.ip] && data.name) {
                const descEl = hostsMap[data.ip].querySelector('.col-desc');
                if (descEl && descEl.textContent === data.ip) {
                    descEl.textContent = data.name;
                }
            }
            break;
        case 'error':
            evtSource.close();
            scanDone();
            setStatus('error', '⚠ ' + esc(data.msg));
            break;
    }
}

function stopScan() {
    if (evtSource) { evtSource.close(); evtSource = null; }
    scanDone();
    setStatus('info', 'Scan interrompu.');
}

function scanDone() {
    document.getElementById('scan-btn').disabled = false;
    document.getElementById('scan-btn').textContent = '▶ Lancer le scan';
    document.getElementById('stop-btn').style.display = 'none';
    document.getElementById('scanning-badge').style.display = 'none';
}

function setStatus(type, html) {
    const box = document.getElementById('status-box');
    box.className = type === 'error' ? 'error-box' : type === 'success' ? 'success-box' : 'info-box';
    box.innerHTML = html;
}

function addHistoryRow(network, hosts, duration) {
    const tbody = document.getElementById('history-tbody');
    const now   = new Date().toISOString().replace('T',' ').substring(0,19);
    const tr    = document.createElement('tr');
    tr.innerHTML = `<td class="mono small">${now}</td><td class="mono">${esc(network)}</td><td><span class="badge badge-up">${hosts} up</span></td><td class="muted small">${duration}s</td>`;
    tbody.insertBefore(tr, tbody.firstChild);
}

function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
</body>
</html>
