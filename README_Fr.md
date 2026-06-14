<p align="center">
  <img src="logo.png" alt="Logo NetDTL" width="220">
</p>

<h1 align="center">NetDTL</h1>

<p align="center">
  Découverte réseau légère et inventaire d'infrastructure sans agent
</p>

# NetDTL

NetDTL est un outil léger de gestion d'actifs réseau, sans agent à déployer sur les machines cibles.

Il est construit en PHP/MySQL et fonctionne sous XAMPP sous Windows ou dans un environnement LAMP équivalent.

Version 1.0-1 - 23 mai 2026 - (c) Didier DTL MORANDI - didier.morandi@gmail.com

## Fonctionnalités

- Découverte réseau par plage CIDR avec Nmap, diffusion en temps réel via Server-Sent Events.
- Inventaire des machines avec historique de statut, ping, ports ouverts, adresse MAC et fabricant.
- Identification NetBIOS et descriptions WMI lorsque disponibles sur les machines Windows.
- Gestion du panneau de brassage avec association port-machine.
- Diagnostics réseau : ping, traceroute, résolution DNS et scan de ports.
- Fiche machine éditable : OS, port switch, port de brassage, commentaire.
- Export CSV de l'inventaire.
- Interface web sombre sans dépendance JavaScript externe.
- Authentification HTTP Basic intégrée.

## Prérequis

- PHP 8.x avec extensions `pdo_mysql` et `mbstring`.
- MySQL ou MariaDB.
- [Nmap](https://nmap.org/download.html) installé et accessible.
- XAMPP sous Windows ou stack LAMP équivalente.

## Installation

**1. Cloner le dépôt**

```powershell
git clone https://github.com/DidierMorandi/netdtl.git
cd netdtl
```

**2. Copier et configurer**

```powershell
cp db.example.php db.php
```

Modifier `db.php` et renseigner :

- identifiants MySQL (`DB_USER`, `DB_PASS`) ;
- identifiants de l'interface (`AUTH_USER`, `AUTH_PASS`) ;
- chemin de Nmap (`NMAP_PATH`) ;
- plage réseau par défaut (`DEFAULT_NETWORK`).

**3. Créer la base**

Importer `netdtl.sql` via phpMyAdmin ou la ligne de commande MySQL :

```powershell
mysql -u root -p < netdtl.sql
```

Tables créées : `machines`, `scan_history`, `patch_panel`, `patch_machines`, `diag_history`.

La base peut aussi être initialisée automatiquement au premier lancement via `initDB()`.

**4. Accéder à l'interface**

Placer les fichiers dans `htdocs` de XAMPP ou dans la racine du vhost, puis ouvrir :

```text
http://localhost/netdtl/
```

## Structure des fichiers

```text
netdtl/
├── db.example.php      Configuration exemple
├── db.php              Configuration réelle
├── index.php           Tableau de bord
├── inventory.php       Inventaire des machines
├── discovery.php       Découverte réseau
├── machine.php         Fiche machine
├── menu.php            Outils de diagnostic
├── patch_panel.php     Gestion du panneau de brassage
├── scan_stream.php     Endpoint SSE pour scan Nmap
├── sidebar.php         Navigation latérale
├── topbar.php          Barre supérieure
├── style.php           CSS partagé
└── netdtl.sql          Schéma de base de données
```

## Sécurité

- Ne pas exposer NetDTL sur Internet sans protection supplémentaire : VPN, reverse proxy TLS, etc.
- Modifier `AUTH_USER` et `AUTH_PASS` dans `db.php` avant tout déploiement.
- La détection OS de Nmap (`-O`) nécessite des privilèges élevés. Lancer XAMPP en administrateur si cette option est utilisée.

## Licence

MIT - voir le fichier `LICENSE`.

## Mise à jour - 14 juin 2026

Le code courant indique `APP_VERSION = '3.0'` dans `db.example.php` et `db.php`.

Points confirmés :

- Interface PHP modernisée avec tableau de bord, inventaire, fiche machine, découverte réseau et panneau de brassage.
- Scan réseau en temps réel via Server-Sent Events dans `scan_stream.php`.
- Options de découverte : ports, OS, NetBIOS/nbstat et description WMI.
- Enrichissement automatique des machines découvertes : nom, IP, MAC, fabricant, OS, ports ouverts, latence et date de dernière vue.
- Inventaire filtrable, ajout manuel de machine, ping individuel, ping global et export CSV.
- Fiche machine avec diagnostics directs et champs éditables : OS, port switch, port de brassage et commentaire.
- Panneau de brassage avec recherche et filtres par switch ou entité.
- Documentation locale bilingue : guides utilisateur et manuels de référence.
- Attention : `db.php` contient une configuration locale de développement et ne doit pas être publié tel quel en production.
