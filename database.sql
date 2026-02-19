CREATE DATABASE IF NOT EXISTS `password_api`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `password_api`;

CREATE TABLE IF NOT EXISTS `generated_passwords` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `password`   VARCHAR(128)     NOT NULL COMMENT 'Contraseña generada en texto plano',
    `length`     TINYINT UNSIGNED NOT NULL,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;