-- ============================================================
--  NetDTL — Création de la base de données
--  À exécuter dans phpMyAdmin ou via MySQL CLI :
--    mysql -u root -p < netdtl.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS netdtl
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE netdtl;

CREATE TABLE IF NOT EXISTS machines (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    hostname      VARCHAR(255) NOT NULL,
    ip            VARCHAR(45)  NOT NULL UNIQUE,
    mac           VARCHAR(17)  DEFAULT NULL,
    os            VARCHAR(255) DEFAULT NULL,
    status        ENUM('up','down','unknown') DEFAULT 'unknown',
    open_ports    TEXT         DEFAULT NULL,
    comment       TEXT         DEFAULT NULL,
    first_seen    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    last_seen     DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    vendor        VARCHAR(255) DEFAULT NULL,
    switch_port   VARCHAR(50)  DEFAULT NULL,
    patch_port    VARCHAR(50)  DEFAULT NULL,
    last_ping_ms  INT          DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS scan_history (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    scan_date  DATETIME DEFAULT CURRENT_TIMESTAMP,
    network    VARCHAR(50),
    hosts_up   INT DEFAULT 0,
    hosts_down INT DEFAULT 0,
    duration_s INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS patch_panel (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    prise       VARCHAR(10)  NOT NULL,
    type        VARCHAR(10)  DEFAULT 'RJ45',
    entite      VARCHAR(50)  DEFAULT NULL,
    local_name  VARCHAR(50)  DEFAULT NULL,
    etage       VARCHAR(20)  DEFAULT NULL,
    poste       VARCHAR(100) DEFAULT NULL,
    switch      VARCHAR(10)  DEFAULT NULL,
    port_switch VARCHAR(20)  DEFAULT NULL,
    notes       VARCHAR(255) DEFAULT NULL,
    UNIQUE KEY uk_prise (prise)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS patch_machines (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    prise       VARCHAR(10)  NOT NULL,
    machine_ip  VARCHAR(45)  NOT NULL,
    hostname    VARCHAR(255) DEFAULT NULL,
    notes       VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS diag_history (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    action    VARCHAR(50),
    target    VARCHAR(255),
    result    TEXT,
    success   TINYINT(1) DEFAULT 1,
    created   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
