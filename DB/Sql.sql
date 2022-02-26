
CREATE TABLE `shunbodb`.`doctors` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(40) NOT NULL ,
 `email` VARCHAR(40) NOT NULL , `mobile` VARCHAR(11) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE `shunbodb`.`users` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(40) NOT NULL ,
 `email` VARCHAR(40) NOT NULL , `mobile` VARCHAR(11) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE `shunbodb`.`verificationcodes` (
   `id` INT NOT NULL AUTO_INCREMENT , `email` VARCHAR(40) NOT NULL , `code` INT(6) NOT NULL , PRIMARY KEY (`id`)
   ) ENGINE = InnoDB;

CREATE TABLE `sunboappdb`.`posts` 
   ( `id` INT NOT NULL , `uid` INT NOT NULL , `msg` VARCHAR(5000) NOT NULL , `votes` INT NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `posts` ADD `updated_at` 
  DATETIME NOT NULL AFTER `votes`, ADD `created_at` DATETIME NOT NULL AFTER `updated_at`;

CREATE TABLE `sunboappdb`.`comments` 
    ( `id` INT NOT NULL , `uid` INT NOT NULL , `pid` INT NOT NULL , `msg` VARCHAR(1500) NOT NULL , 
    `updated_at` DATETIME NOT NULL , `created_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
    
ALTER TABLE `comments` ADD CONSTRAINT `fk_post` FOREIGN KEY (`pid`) REFERENCES `posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `comments` 
    ADD CONSTRAINT `fk_user` FOREIGN KEY (`uid`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;