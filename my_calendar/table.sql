CREATE TABLE `tasks` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `theme` VARCHAR(255) NOT NULL,
  `type` INT NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  `duration` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `completed` TINYINT(1) UNSIGNED DEFAULT NULL,
  `overdue` TINYINT(1) UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY(`id`),
  INDEX `deleted_at` (`deleted_at`)
);

CREATE EVENT overdue_task
ON SCHEDULE EVERY 10 MINUTE
DO
  UPDATE tasks SET overdue = 1 WHERE date < CURDATE() OR (date = CURDATE() AND time < CURTIME());
