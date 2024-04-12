CREATE TABLE IF NOT EXISTS `Drivers` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(40) NOT NULL,
    `abbr` VARCHAR(3) NOT NULL,
    `image` text(500),
    `nationality` VARCHAR(20),
    `country` VARCHAR(30),
    `birthdate` DATE,
    `birthplace` VARCHAR(100),
    `number` INT(3),
    `grands_prix_entered` INT(4),
    `world_championships` INT(2),
    `podiums` INT(3),
    `highest_race_finish` INT,
    `highest_grid_position` INT(2),
    `career_points` DECIMAL(6,2),
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
