
CREATE TABLE IF NOT EXISTS `schedule` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `active` TINYINT NOT NULL DEFAULT 1,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(64) NOT NULL,
    `active` TINYINT NOT NULL DEFAULT 1,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `eav_entity` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(16) NOT NULL,
    `active` TINYINT NOT NULL DEFAULT 1,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `eav_entity_ibfk_1_idx` (`name` ASC)
);

CREATE TABLE IF NOT EXISTS `eav_attribute` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity_id` INT UNSIGNED NOT NULL,
    `value` VARCHAR(64) NOT NULL,
    `value_type` VARCHAR(8) NOT NULL DEFAULT "varchar",
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `eav_attribute_idfk_1`
      FOREIGN KEY (`entity_id`)
      REFERENCES `eav_entity` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
);

CREATE TABLE IF NOT EXISTS `eav_value_varchar` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity_id` INT UNSIGNED NOT NULL,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value` VARCHAR(256) NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `eav_value_varchar_ibfk_1_idx` (`value` ASC),
    CONSTRAINT `eav_value_varchar_idfk_1`
      FOREIGN KEY (`attribute_id`)
      REFERENCES `eav_attribute` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
    CONSTRAINT `eav_value_varchar_idfk_2`
      FOREIGN KEY (`entity_id`)
      REFERENCES `eav_entity` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
);

CREATE TABLE IF NOT EXISTS `eav_value_int` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity_id` INT UNSIGNED NOT NULL,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value` INT NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `eav_value_int_ibfk_1_idx` (`value` ASC),
    CONSTRAINT `eav_value_int_idfk_1`
      FOREIGN KEY (`attribute_id`)
      REFERENCES `eav_attribute` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
    CONSTRAINT `eav_value_int_idfk_2`
      FOREIGN KEY (`entity_id`)
      REFERENCES `eav_entity` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
);

CREATE TABLE IF NOT EXISTS `eav_value_decimal` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity_id` INT UNSIGNED NOT NULL,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value` DECIMAL(15,2) NOT NULL,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `eav_value_decimal_ibfk_1_idx` (`value` ASC),
    CONSTRAINT `eav_value_decimal_idfk_1`
      FOREIGN KEY (`attribute_id`)
      REFERENCES `eav_attribute` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
    CONSTRAINT `eav_value_decimal_idfk_2`
      FOREIGN KEY (`entity_id`)
      REFERENCES `eav_entity` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
);
