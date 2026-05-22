# NetDTL

Outil léger de gestion de parc réseau, sans agent à déployer sur les machines cibles.
Développé en PHP/MySQL, il fonctionne sur XAMPP (Windows) ou tout environnement LAMP.
Version 1.0-1 du 23 mai 2026 - (c) Didier DTL MORANDI - didier.morandi@gmail.com - Former DECcie (PRSTSC::DTL 1983-1986), VMS Expert and DCL Guru. NetDTL was built with a little help from my Friends: Claude and my cat Gépété. :-)

## Fonctionnalités

- Découverte réseau par plage CIDR via Nmap (streaming temps réel, Server-Sent Events)
- Inventaire des machines avec historique de statut, ping, ports ouverts, MAC, fabricant
- Identification NetBIOS et descriptions WMI si accessible (machines Windows)
- Gestion du brassage réseau (patch panel et association prises/machines)
- Diagnostics réseau : ping, traceroute, DNS lookup, scan de ports
- Fiche machine éditable (OS, port switch, port brassage, commentaire)
- Export CSV de l'inventaire
- Interface web sombre, sans dépendance JavaScript externe
- Authentification HTTP Basic intégrée

## Prérequis

- PHP 8.x avec extensions `pdo_mysql`, `mbstring`
- MySQL / MariaDB
- [Nmap](https://nmap.org/download.html) installé et accessible
- XAMPP (Windows) ou équivalent LAMP

## Installation

**1. Cloner le dépôt**
```
git clone https://github.com/DidierMorandi/netdtl.git
cd netdtl
```

**2. Copier et adapter la configuration**
```
cp db.example.php db.php
```
Éditez `db.php` et renseignez :
- les identifiants MySQL (`DB_USER`, `DB_PASS`)
- les identifiants d'accès à l'interface (`AUTH_USER`, `AUTH_PASS`)
- le chemin vers Nmap (`NMAP_PATH`)
- la plage réseau par défaut (`DEFAULT_NETWORK`)

**3. Créer la base de données**

Importez `netdtl.sql` dans phpMyAdmin ou via MySQL CLI :
```
mysql -u root -p < netdtl.sql
```

Les tables créées sont : `machines`, `scan_history`, `patch_panel`, `patch_machines`, `diag_history`.

Alternatively, la base est aussi initialisée automatiquement au premier lancement via `initDB()`.

**4. Accéder à l'interface**

Placez les fichiers dans le dossier `htdocs` de XAMPP (ou la racine de votre vhost) et ouvrez :
```
http://localhost/netdtl/
```

## Structure des fichiers

```
netdtl/
├── db.example.php      # Configuration type (à copier en db.php)
├── db.php              # Configuration réelle (non versionné)
├── index.php           # Tableau de bord
├── inventory.php       # Inventaire des machines
├── discovery.php       # Découverte réseau
├── machine.php         # Fiche machine
├── menu.php            # Outils de diagnostic
├── patch_panel.php     # Gestion du brassage réseau
├── scan_stream.php     # Endpoint SSE pour le scan Nmap
├── sidebar.php         # Composant navigation latérale
├── topbar.php          # Composant barre supérieure
├── style.php           # CSS partagé
└── netdtl.sql         # Schéma de base de données
```

## Sécurité

- Ne pas exposer NetDTL sur Internet sans protection supplémentaire (VPN, reverse proxy avec TLS).
- Modifier impérativement `AUTH_USER` et `AUTH_PASS` dans `db.php` avant tout déploiement.
- Nmap nécessite des privilèges élevés pour la détection d'OS (`-O`). Lancez XAMPP en tant qu'administrateur si vous utilisez cette option.

## Licence

MIT — voir fichier `LICENSE`.
