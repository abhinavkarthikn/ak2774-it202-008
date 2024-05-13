CREATE TABLE IF NOT EXISTS `Teams` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(40) NOT NULL,
    `logo` text(500),
    `base` VARCHAR(50),
    `first_team_entry` INT(4),
    `world_championships` INT(2),
    `highest_race_finish` INT,
    `pole_positions` INT(3),
    `fastest_laps` INT(3),
    `president` VARCHAR(40),
    `director` VARCHAR(40),
    `technical_manager` VARCHAR(40),
    `chassis` VARCHAR(40),
    `engine` VARCHAR(40),
    `tyres` VARCHAR(40),
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
