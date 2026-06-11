<p align="center">
  <img src="logo.png" alt="NetDTL logo" width="220">
</p>

<h1 align="center">NetDTL</h1>

<p align="center">
  Lightweight agentless network discovery and infrastructure inventory
</p>

# NetDTL

Q&D lightweight network asset management tool, with no agent to deploy on target machines.
Built with PHP/MySQL, runs on XAMPP (Windows) or any LAMP environment.
Version 1.0-1 23-May-2026 - (c) Didier DTL MORANDI - didier.morandi@gmail.com

## Features

- Network discovery by CIDR range via Nmap (real-time streaming, Server-Sent Events)
- Machine inventory with status history, ping, open ports, MAC address, vendor
- NetBIOS identification and WMI descriptions when available (Windows machines)
- Patch panel management (port-to-machine mapping)
- Network diagnostics: ping, traceroute, DNS lookup, port scan
- Editable machine profile (OS, switch port, patch port, comment)
- CSV export of inventory
- Dark web interface with no external JavaScript dependencies
- Built-in HTTP Basic authentication

## Requirements

- PHP 8.x with extensions `pdo_mysql`, `mbstring`
- MySQL / MariaDB
- [Nmap](https://nmap.org/download.html) installed and accessible
- XAMPP (Windows) or equivalent LAMP stack

## Installation

**1. Clone the repository**
```
git clone https://github.com/DidierMorandi/netdtl.git
cd netdtl
```

**2. Copy and configure**
```
cp db.example.php db.php
```
Edit `db.php` and fill in:
- MySQL credentials (`DB_USER`, `DB_PASS`)
- Interface login credentials (`AUTH_USER`, `AUTH_PASS`)
- Path to Nmap (`NMAP_PATH`)
- Default network range (`DEFAULT_NETWORK`)

**3. Create the database**

Import `netdtl.sql` via phpMyAdmin or MySQL CLI:
```
mysql -u root -p < netdtl.sql
```

Tables created: `machines`, `scan_history`, `patch_panel`, `patch_machines`, `diag_history`.

Alternatively, the database is also initialized automatically on first run via `initDB()`.

**4. Access the interface**

Place the files in XAMPP's `htdocs` folder (or your vhost root) and open:
```
http://localhost/netdtl/
```

## File structure

```
netdtl/
├── db.example.php      # Sample configuration (copy to db.php)
├── db.php              # Actual configuration (not versioned)
├── index.php           # Dashboard
├── inventory.php       # Machine inventory
├── discovery.php       # Network discovery
├── machine.php         # Machine profile
├── menu.php            # Diagnostic tools
├── patch_panel.php     # Patch panel management
├── scan_stream.php     # SSE endpoint for Nmap scan
├── sidebar.php         # Sidebar navigation component
├── topbar.php          # Top bar component
├── style.php           # Shared CSS
└── netdtl.sql         # Database schema
```

## Security

- Do not expose NetDTL on the internet without additional protection (VPN, TLS reverse proxy).
- Change `AUTH_USER` and `AUTH_PASS` in `db.php` before any deployment.
- Nmap requires elevated privileges for OS detection (`-O`). Run XAMPP as administrator if you use this option.

## License

MIT — see `LICENSE` file.
