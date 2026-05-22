<div align="center">
  <img src="logo.png" alt="NetDTL" width="200">
</div>
# NetDTL v1.0-1

Outil de découverte et d'inventaire réseau — Secours Catholique Bourges  
Version 1.0-1 du 23 mai 2026

## Prérequis

- XAMPP (Apache + MySQL + PHP 8.x)
- Nmap installé : https://nmap.org/download.html
- PHP extension : pdo_mysql

## Installation

1. Copier le dossier `netdtl/` dans `C:\xampp\htdocs\`
2. Copier `db.example.php` en `db.php` et adapter les valeurs
3. Importer `netdtl.sql` dans phpMyAdmin
4. Placer `logo.png` dans le dossier `netdtl/`
5. Accéder à `http://localhost/netdtl/`

## Configuration (db.php)

| Paramètre | Description |
|---|---|
| DB_HOST | Hôte MySQL (localhost) |
| DB_NAME | Nom de la base (netdtl) |
| DB_USER | Utilisateur MySQL (root) |
| DB_PASS | Mot de passe MySQL |
| AUTH_USER | Login HTTP Basic |
| AUTH_PASS | Mot de passe HTTP Basic |
| NMAP_PATH | Chemin vers nmap.exe |
| DEFAULT_NETWORK | Réseau à scanner par défaut |

## Fichiers

| Fichier | Rôle |
|---|---|
| db.php | Configuration et connexion BDD |
| db.example.php | Modèle de configuration |
| index.php | Tableau de bord |
| inventory.php | Inventaire des machines |
| machine.php | Fiche détaillée par machine |
| discovery.php | Découverte réseau (SSE) |
| scan_stream.php | Endpoint streaming nmap |
| patch.php | Panneau de brassage |
| menu.php | Outils de diagnostic |
| sidebar.php | Navigation latérale |
| topbar.php | Barre supérieure |
| style.php | CSS partagé |
| netdtl.sql | Script SQL de création des tables |
