-- ==========================================================
-- DE KIEM TRA GIUA HOC PHAN: CONG NGHE VA LAP TRINH WEB
-- CSDL QLNhanSu, bang Users (4 cot dung theo de bai)
-- Chi can import file nay trong phpMyAdmin / MySQL.
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `QLNhanSu`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `QLNhanSu`;

CREATE TABLE IF NOT EXISTS `Users` (
    `admin_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `admin_password` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `images` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uk_admin_email` (`admin_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4 nguoi dung mau. Mat khau mau: User@12345, da duoc password_hash().
-- INSERT IGNORE de han che loi khi import lai du lieu mau.
INSERT IGNORE INTO `Users` (`admin_id`, `admin_email`, `admin_password`, `images`) VALUES
(1, 'an.nguyen@example.com', '$2y$12$rCx2Pch767JL6be9r79kf.PKPHLAfuFhpmvx2gXJX4BR.8feU96yK', 'assets/img/avatar_1.svg'),
(2, 'binh.ho@example.com', '$2y$12$rCx2Pch767JL6be9r79kf.PKPHLAfuFhpmvx2gXJX4BR.8feU96yK', 'assets/img/avatar_2.svg'),
(3, 'chi.le@example.com', '$2y$12$rCx2Pch767JL6be9r79kf.PKPHLAfuFhpmvx2gXJX4BR.8feU96yK', 'assets/img/avatar_3.svg'),
(4, 'dung.mai@example.com', '$2y$12$rCx2Pch767JL6be9r79kf.PKPHLAfuFhpmvx2gXJX4BR.8feU96yK', 'assets/img/avatar_4.svg');

-- Kiem tra ket qua sau khi import:
SELECT `admin_id`, `admin_email`, `images` FROM `Users`;
