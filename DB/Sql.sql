
CREATE TABLE `shunbodb`.`doctors` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(40) NOT NULL ,
 `email` VARCHAR(40) NOT NULL , `mobile` VARCHAR(11) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE `shunbodb`.`users` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(40) NOT NULL ,
 `email` VARCHAR(40) NOT NULL , `mobile` VARCHAR(11) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE `shunbodb`.`verificationcodes` (
   `id` INT NOT NULL AUTO_INCREMENT , `email` VARCHAR(40) NOT NULL , `code` INT(6) NOT NULL , PRIMARY KEY (`id`)
   ) ENGINE = InnoDB;