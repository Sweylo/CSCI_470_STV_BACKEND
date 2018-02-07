-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema chess_champions
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema chess_champions
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `chess_champions` DEFAULT CHARACTER SET utf8 ;
USE `chess_champions` ;

-- -----------------------------------------------------
-- Table `chess_champions`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(45) NOT NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `user_password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  UNIQUE INDEX `user_name_UNIQUE` (`user_name` ASC),
  UNIQUE INDEX `user_email_UNIQUE` (`user_email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`boards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`boards` (
  `board_id` INT NOT NULL AUTO_INCREMENT,
  `board_name` VARCHAR(45) NOT NULL,
  `board_data` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`board_id`),
  UNIQUE INDEX `board_id_UNIQUE` (`board_id` ASC),
  UNIQUE INDEX `board_name_UNIQUE` (`board_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`matches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`matches` (
  `match_id` INT NOT NULL AUTO_INCREMENT,
  `match_name` VARCHAR(45) NOT NULL,
  `board_id` INT NOT NULL,
  PRIMARY KEY (`match_id`),
  UNIQUE INDEX `match_id_UNIQUE` (`match_id` ASC),
  INDEX `match_board_id_idx` (`board_id` ASC),
  CONSTRAINT `match_board_id`
    FOREIGN KEY (`board_id`)
    REFERENCES `chess_champions`.`boards` (`board_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`account_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`account_types` (
  `account_type_id` INT NOT NULL AUTO_INCREMENT,
  `account_type_name` VARCHAR(45) NOT NULL,
  `account_perm_level` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_type_id`),
  UNIQUE INDEX `type_id_UNIQUE` (`account_type_id` ASC),
  UNIQUE INDEX `type_name_UNIQUE` (`account_type_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`user_account_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`user_account_types` (
  `user_type_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `account_type_id` INT NOT NULL,
  PRIMARY KEY (`user_type_id`),
  UNIQUE INDEX `user_type_id_UNIQUE` (`user_type_id` ASC),
  INDEX `user_id_idx` (`user_id` ASC),
  INDEX `type_id_idx` (`account_type_id` ASC),
  CONSTRAINT `user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `type_id`
    FOREIGN KEY (`account_type_id`)
    REFERENCES `chess_champions`.`account_types` (`account_type_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`classes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`classes` (
  `class_id` INT NOT NULL AUTO_INCREMENT,
  `class_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE INDEX `class_id_UNIQUE` (`class_id` ASC),
  UNIQUE INDEX `class_name_UNIQUE` (`class_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`moves`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`moves` (
  `move_id` INT NOT NULL AUTO_INCREMENT,
  `move_data` VARCHAR(45) NOT NULL,
  `class_id` INT NOT NULL,
  `move_level` INT NOT NULL,
  PRIMARY KEY (`move_id`),
  UNIQUE INDEX `movement_id_UNIQUE` (`move_id` ASC),
  INDEX `class_id_idx` (`class_id` ASC),
  CONSTRAINT `move_class_id`
    FOREIGN KEY (`class_id`)
    REFERENCES `chess_champions`.`classes` (`class_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`cards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`cards` (
  `card_id` INT NOT NULL AUTO_INCREMENT,
  `card_rarity` INT NOT NULL,
  PRIMARY KEY (`card_id`),
  UNIQUE INDEX `card_id_UNIQUE` (`card_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`user_cards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`user_cards` (
  `user_card_id` INT NOT NULL AUTO_INCREMENT,
  `card_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`user_card_id`),
  UNIQUE INDEX `user_card_id_UNIQUE` (`user_card_id` ASC),
  INDEX `card_id_idx` (`card_id` ASC),
  INDEX `user_id_idx` (`user_id` ASC),
  CONSTRAINT `card_id`
    FOREIGN KEY (`card_id`)
    REFERENCES `chess_champions`.`cards` (`card_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `card_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`tarot_card`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`tarot_card` (
  `tarot_card_id` INT NOT NULL AUTO_INCREMENT,
  `tarot_card_name` VARCHAR(45) NOT NULL,
  `tarot_card_description` VARCHAR(45) NOT NULL,
  `card_id` INT NOT NULL,
  `move_id` INT NOT NULL,
  PRIMARY KEY (`tarot_card_id`),
  UNIQUE INDEX `tarrot_card_id_UNIQUE` (`tarot_card_id` ASC),
  UNIQUE INDEX `card_id_UNIQUE` (`card_id` ASC),
  INDEX `movement_id_idx` (`move_id` ASC),
  CONSTRAINT `tarot_card_id`
    FOREIGN KEY (`card_id`)
    REFERENCES `chess_champions`.`cards` (`card_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `tarot_card_movement_id`
    FOREIGN KEY (`move_id`)
    REFERENCES `chess_champions`.`moves` (`move_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`traps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`traps` (
  `trap_id` INT NOT NULL AUTO_INCREMENT,
  `trap_data` VARCHAR(45) NOT NULL,
  `trap_level` INT NOT NULL,
  PRIMARY KEY (`trap_id`),
  UNIQUE INDEX `trap_id_UNIQUE` (`trap_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`trap_card`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`trap_card` (
  `trap_card_id` INT NOT NULL AUTO_INCREMENT,
  `trap_card_name` VARCHAR(45) NOT NULL,
  `trap_card_description` VARCHAR(45) NOT NULL,
  `card_id` INT NOT NULL,
  `trap_id` INT NOT NULL,
  PRIMARY KEY (`trap_card_id`),
  UNIQUE INDEX `trap_card_id_UNIQUE` (`trap_card_id` ASC),
  UNIQUE INDEX `trap_card_name_UNIQUE` (`trap_card_name` ASC),
  UNIQUE INDEX `card_id_UNIQUE` (`card_id` ASC),
  INDEX `trap_card_trap_id_idx` (`trap_id` ASC),
  CONSTRAINT `trap_card_id`
    FOREIGN KEY (`card_id`)
    REFERENCES `chess_champions`.`cards` (`card_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `trap_card_trap_id`
    FOREIGN KEY (`trap_id`)
    REFERENCES `chess_champions`.`traps` (`trap_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`spaces`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`spaces` (
  `space_id` INT NOT NULL AUTO_INCREMENT,
  `match_id` INT NOT NULL,
  `space_coord` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`space_id`),
  UNIQUE INDEX `board_space_id_UNIQUE` (`space_id` ASC),
  INDEX `match_space_id_idx` (`match_id` ASC),
  CONSTRAINT `match_space_id`
    FOREIGN KEY (`match_id`)
    REFERENCES `chess_champions`.`matches` (`match_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`pieces`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`pieces` (
  `piece_id` INT NOT NULL AUTO_INCREMENT,
  `space_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `move_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`piece_id`),
  UNIQUE INDEX `piece_space_id_UNIQUE` (`piece_id` ASC),
  INDEX `piece_class_id_idx` (`class_id` ASC),
  INDEX `piece_move_id_idx` (`move_id` ASC),
  INDEX `piece_space_id_idx` (`space_id` ASC),
  INDEX `piece_user_id_idx` (`user_id` ASC),
  CONSTRAINT `piece_class_id`
    FOREIGN KEY (`class_id`)
    REFERENCES `chess_champions`.`classes` (`class_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `piece_move_id`
    FOREIGN KEY (`move_id`)
    REFERENCES `chess_champions`.`moves` (`move_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `piece_space_id`
    FOREIGN KEY (`space_id`)
    REFERENCES `chess_champions`.`spaces` (`space_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `piece_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`match_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`match_users` (
  `match_user_id` INT NOT NULL AUTO_INCREMENT,
  `match_white_user_id` INT NOT NULL,
  `match_black_user_id` INT NOT NULL,
  `match_id` INT NOT NULL,
  PRIMARY KEY (`match_user_id`),
  UNIQUE INDEX `match_user_id_UNIQUE` (`match_user_id` ASC),
  INDEX `match_user_white_user_id_idx` (`match_white_user_id` ASC),
  INDEX `match_user_black_user_id_idx` (`match_black_user_id` ASC),
  INDEX `match_user_match_id_idx` (`match_id` ASC),
  CONSTRAINT `match_user_white_user_id`
    FOREIGN KEY (`match_white_user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `match_user_black_user_id`
    FOREIGN KEY (`match_black_user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `match_user_match_id`
    FOREIGN KEY (`match_id`)
    REFERENCES `chess_champions`.`matches` (`match_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chess_champions`.`laid_traps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chess_champions`.`laid_traps` (
  `space_trap_id` INT NOT NULL AUTO_INCREMENT,
  `space_id` INT NOT NULL,
  `trap_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`space_trap_id`),
  UNIQUE INDEX `space_trap_id_UNIQUE` (`space_trap_id` ASC),
  INDEX `space_trap_space_id_idx` (`space_id` ASC),
  INDEX `space_trap_trap_id_idx` (`trap_id` ASC),
  INDEX `laid_trap_user_id_idx` (`user_id` ASC),
  CONSTRAINT `laid_trap_space_id`
    FOREIGN KEY (`space_id`)
    REFERENCES `chess_champions`.`spaces` (`space_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `laid_trap_trap_id`
    FOREIGN KEY (`trap_id`)
    REFERENCES `chess_champions`.`traps` (`trap_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `laid_trap_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `chess_champions`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `chess_champions`.`classes`
-- -----------------------------------------------------
START TRANSACTION;
USE `chess_champions`;
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (1, 'king');
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (2, 'queen');
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (3, 'bishop');
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (4, 'rook');
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (5, 'knight');
INSERT INTO `chess_champions`.`classes` (`class_id`, `class_name`) VALUES (6, 'pawn');

COMMIT;

