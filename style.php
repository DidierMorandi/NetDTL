<?php
// style.php — CSS partagé NetDTL v3.0
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── Reset & tokens ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:     #0d1117;
    --bg2:    #161b22;
    --bg3:    #1c2230;
    --border: rgba(255,255,255,0.08);
    --border2:rgba(255,255,255,0.14);
    --txt:    #ffffff;
    --txt2:   #cccccc;
    --txt3:   #888888;
    --accent: #3fb950;
    --accent2:#58a6ff;
    --warn:   #d29922;
    --err:    #f85149;
    --purple: #bc8cff;
    --teal:   #64c8c8;
    --mono:   'IBM Plex Mono', monospace;
    --sans:   'IBM Plex Sans', sans-serif;
}
body { background:var(--bg); color:var(--txt); font-family:var(--mono); min-height:100vh; display:flex; flex-direction:column; }

/* ── Topbar ── */
.topbar { background:var(--bg2); border-bottom:1px solid var(--border); padding:0 24px; height:52px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:10; }
.topbar-logo { font-size:13px; font-weight:600; color:var(--accent); letter-spacing:.12em; text-transform:uppercase; display:flex; align-items:center; gap:8px; text-decoration:none; }
.topbar-logo::before { content:''; display:inline-block; width:8px; height:8px; background:var(--accent); border-radius:50%; box-shadow:0 0 8px var(--accent); }
.topbar-sep { flex:1; }
.topbar-nav { display:flex; gap:4px; }
.topbar-nav a { font-size:11px; color:var(--txt2); font-family:var(--sans); text-decoration:none; padding:4px 10px; border-radius:4px; transition:background .15s,color .15s; }
.topbar-nav a:hover { background:rgba(255,255,255,.05); color:var(--txt); }
.topbar-nav a.active { color:var(--accent2); }
.topbar-user { font-size:11px; color:var(--txt2); font-family:var(--sans); }

/* ── Layout ── */
.layout { display:grid; grid-template-columns:220px 1fr; flex:1; min-height:calc(100vh - 52px); }

/* ── Sidebar ── */
.sidebar { background:var(--bg2); border-right:1px solid var(--border); padding:20px 12px; display:flex; flex-direction:column; gap:2px; }
.sidebar-section { font-size:10px; font-family:var(--sans); color:var(--txt3); letter-spacing:.1em; text-transform:uppercase; padding:14px 10px 6px; }
.nav-link { display:flex; align-items:center; gap:9px; padding:8px 10px; border-radius:6px; background:none; border:none; color:var(--txt2); font-family:var(--mono); font-size:12px; cursor:pointer; width:100%; text-align:left; transition:background .15s,color .15s; text-decoration:none; }
.nav-link .icon { font-size:15px; width:18px; text-align:center; opacity:.7; }
.nav-link:hover { background:rgba(255,255,255,.05); color:var(--txt); }
.nav-link.active { background:rgba(63,185,80,.12); color:var(--accent); }
.nav-link.active .icon { opacity:1; }
.nav-btn { display:flex; align-items:center; gap:9px; padding:8px 10px; border-radius:6px; background:none; border:none; color:var(--txt2); font-family:var(--mono); font-size:12px; cursor:pointer; width:100%; text-align:left; transition:background .15s,color .15s; }
.nav-btn .icon { font-size:15px; width:18px; text-align:center; opacity:.7; }
.nav-btn:hover { background:rgba(255,255,255,.05); color:var(--txt); }
.nav-btn.active { background:rgba(63,185,80,.12); color:var(--accent); }
.sidebar-spacer { flex:1; }
.sidebar-version { font-size:10px; color:var(--txt3); padding:8px 10px; font-family:var(--sans); }

/* ── Main ── */
.main { display:flex; flex-direction:column; }
.content { flex:1; padding:20px 24px; display:flex; flex-direction:column; gap:16px; }
.page-title { font-size:16px; font-weight:600; color:var(--txt); letter-spacing:.04em; padding-bottom:4px; border-bottom:1px solid var(--border); }

/* ── Toolbar ── */
.toolbar { padding:16px 24px; border-bottom:1px solid var(--border); display:flex; gap:10px; align-items:center; background:var(--bg); flex-wrap:wrap; }
.toolbar input[type="text"], .toolbar select { flex:1; min-width:160px; background:var(--bg2); border:1px solid var(--border2); border-radius:6px; padding:8px 12px; color:var(--txt); font-family:var(--mono); font-size:13px; outline:none; transition:border-color .2s; }
.toolbar input[type="text"]:focus, .toolbar select:focus { border-color:var(--accent2); }
.toolbar input::placeholder { color:var(--txt3); }
.ports-input { width:180px; background:var(--bg2); border:1px solid var(--border2); border-radius:6px; padding:8px 10px; color:var(--txt); font-family:var(--mono); font-size:12px; outline:none; }
.run-btn { padding:8px 18px; border-radius:6px; background:var(--accent); color:#0d1117; border:none; font-family:var(--mono); font-size:12px; font-weight:600; cursor:pointer; letter-spacing:.05em; transition:opacity .15s,transform .1s; white-space:nowrap; }
.run-btn:hover { opacity:.88; }
.run-btn:active { transform:scale(.97); }
.export-btn { padding:8px 14px; border-radius:6px; background:none; color:var(--txt2); border:1px solid var(--border2); font-family:var(--mono); font-size:12px; cursor:pointer; transition:background .15s,color .15s; white-space:nowrap; }
.export-btn:hover { background:rgba(255,255,255,.05); color:var(--txt); }
.danger-btn { padding:8px 14px; border-radius:6px; background:rgba(248,81,73,.1); color:var(--err); border:1px solid rgba(248,81,73,.3); font-family:var(--mono); font-size:12px; cursor:pointer; transition:background .15s; white-space:nowrap; }
.danger-btn:hover { background:rgba(248,81,73,.2); }

/* ── Stats ── */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; }
.stat-card { background:var(--bg2); border:1px solid var(--border); border-radius:8px; padding:12px 14px; }
.stat-label { font-size:10px; font-family:var(--sans); color:var(--txt3); text-transform:uppercase; letter-spacing:.08em; margin-bottom:6px; }
.stat-val { font-size:22px; font-weight:600; color:var(--txt); line-height:1; }
.stat-val.green { color:var(--accent); }
.stat-val.amber { color:var(--warn); }
.stat-val.red   { color:var(--err); }

/* ── Panel ── */
.panel { background:var(--bg2); border:1px solid var(--border); border-radius:8px; overflow:hidden; }
.panel-header { padding:12px 16px; border-bottom:1px solid var(--border); font-size:11px; color:var(--txt3); font-family:var(--sans); text-transform:uppercase; letter-spacing:.08em; display:flex; align-items:center; justify-content:space-between; }
.panel-link { color:var(--accent2); text-decoration:none; font-size:11px; }
.panel-link:hover { text-decoration:underline; }
.panel-body { padding:16px; }

/* ── Tables ── */
.data-table { width:100%; border-collapse:collapse; font-size:12px; }
.data-table th { background:var(--bg3); color:var(--txt2); font-weight:500; padding:8px 12px; text-align:left; border-bottom:1px solid var(--border); font-family:var(--sans); font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
.data-table td { padding:8px 12px; border-bottom:1px solid var(--border); color:var(--txt); vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tr:hover td { background:rgba(255,255,255,.02); }
.empty-cell { color:var(--txt3); font-family:var(--sans); font-size:13px; text-align:center; padding:32px !important; }

/* ── Badges ── */
.badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-family:var(--sans); font-weight:500; }
.badge-up      { background:rgba(63,185,80,.15);  color:var(--accent); }
.badge-down    { background:rgba(248,81,73,.15);  color:var(--err); }
.badge-unknown { background:rgba(139,148,158,.15);color:var(--txt2); }
.badge-open    { background:rgba(88,166,255,.15); color:var(--accent2); }

/* ── Error / Info ── */
.error-box { background:rgba(248,81,73,.1); border:1px solid rgba(248,81,73,.3); border-radius:8px; padding:12px 16px; color:var(--err); font-size:13px; }
.info-box  { background:rgba(88,166,255,.1); border:1px solid rgba(88,166,255,.3); border-radius:8px; padding:12px 16px; color:var(--accent2); font-size:13px; font-family:var(--sans); }
.success-box { background:rgba(63,185,80,.1); border:1px solid rgba(63,185,80,.3); border-radius:8px; padding:12px 16px; color:var(--accent); font-size:13px; font-family:var(--sans); }

/* ── Terminal ── */
.terminal-wrap { background:var(--bg2); border:1px solid var(--border); border-radius:8px; overflow:hidden; flex:1; }
.terminal-header { padding:10px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.term-dots { display:flex; gap:6px; }
.term-dot { width:10px; height:10px; border-radius:50%; }
.term-title { font-size:11px; color:var(--txt3); font-family:var(--sans); flex:1; }
.term-copy { background:none; border:1px solid var(--border2); color:var(--txt2); border-radius:4px; padding:3px 10px; font-size:11px; font-family:var(--sans); cursor:pointer; }
.term-copy:hover { background:rgba(255,255,255,.05); color:var(--txt); }
.terminal-body { padding:16px; overflow-x:auto; max-height:400px; overflow-y:auto; }
.terminal-body::-webkit-scrollbar { width:4px; height:4px; }
.terminal-body::-webkit-scrollbar-thumb { background:var(--border2); border-radius:2px; }
.line { font-size:12px; line-height:1.9; white-space:pre; display:block; }
.line .prompt { color:var(--accent); }
.line .cmd    { color:var(--accent2); }
.ok   { color:var(--accent); }
.warn { color:var(--warn); }
.err  { color:var(--err); }
.muted { color:var(--txt2); }
.small { font-size:11px; }
.mono  { font-family:var(--mono); }
.empty-state { padding:48px; text-align:center; color:var(--txt3); font-family:var(--sans); font-size:13px; line-height:1.8; }
.empty-state .big { font-size:28px; margin-bottom:8px; opacity:.3; }
#raw-output { display:none; }

/* ── History ── */
.history-list { max-height:240px; overflow-y:auto; }
.history-item { display:flex; align-items:center; gap:10px; padding:7px 16px; border-bottom:1px solid var(--border); font-size:12px; }
.history-item:last-child { border-bottom:none; }
.h-badge { font-size:9px; font-family:var(--sans); font-weight:600; padding:2px 7px; border-radius:3px; letter-spacing:.06em; min-width:72px; text-align:center; }
.h-ping       { background:rgba(88,166,255,.15);  color:var(--accent2); }
.h-nmap       { background:rgba(210,153,34,.15);  color:var(--warn); }
.h-traceroute { background:rgba(63,185,80,.15);   color:var(--accent); }
.h-dns        { background:rgba(188,140,255,.15); color:var(--purple); }
.h-ports      { background:rgba(248,81,73,.15);   color:var(--err); }
.h-infos      { background:rgba(100,200,200,.15); color:var(--teal); }
.h-services   { background:rgba(200,200,100,.15); color:#c8c864; }
.h-discovery  { background:rgba(210,153,34,.15);  color:var(--warn); }
.h-target { flex:1; color:var(--txt2); }
.h-time  { color:var(--txt3); font-size:11px; font-family:var(--sans); }
.h-ok    { color:var(--accent); }
.h-fail  { color:var(--err); }

/* ── Formulaires ── */
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-label { font-size:11px; color:var(--txt2); font-family:var(--sans); text-transform:uppercase; letter-spacing:.06em; }
.form-input, .form-select, .form-textarea { background:var(--bg3); border:1px solid var(--border2); border-radius:6px; padding:8px 12px; color:var(--txt); font-family:var(--mono); font-size:13px; outline:none; transition:border-color .2s; width:100%; }
.form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--accent2); }
.form-textarea { resize:vertical; min-height:80px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

/* ── Machine detail ── */
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.detail-item { }
.detail-key { font-size:10px; color:var(--txt3); font-family:var(--sans); text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; }
.detail-val { font-size:13px; color:var(--txt); }

/* ── Links ── */
a.link { color:var(--accent2); text-decoration:none; }
a.link:hover { text-decoration:underline; }

/* ── Progress bar ── */
.progress-wrap { background:var(--bg3); border-radius:4px; height:6px; overflow:hidden; margin-top:4px; }
.progress-bar  { height:100%; background:var(--accent); border-radius:4px; transition:width .3s; }

/* ── Scanning animation ── */
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
.scanning { animation:pulse 1.2s ease-in-out infinite; color:var(--warn); }
</style>
