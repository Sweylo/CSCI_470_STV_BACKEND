-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: chess_n_conquer
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abilities`
--

DROP TABLE IF EXISTS `abilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abilities` (
  `ability_id` int(11) NOT NULL AUTO_INCREMENT,
  `ability_data` longtext NOT NULL,
  `ability_class_id` int(11) NOT NULL,
  `ability_level` int(11) DEFAULT '0',
  PRIMARY KEY (`ability_id`),
  UNIQUE KEY `ability_id_UNIQUE` (`ability_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `account_types`
--

DROP TABLE IF EXISTS `account_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_types` (
  `account_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_type_name` varchar(45) NOT NULL,
  `account_perm_level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_type_id`),
  UNIQUE KEY `type_id_UNIQUE` (`account_type_id`),
  UNIQUE KEY `type_name_UNIQUE` (`account_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_init_spaces`
--

DROP TABLE IF EXISTS `board_init_spaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_init_spaces` (
  `board_init_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_init_board_id` int(11) NOT NULL,
  `board_init_coord_x` int(11) NOT NULL,
  `board_init_coord_y` int(11) NOT NULL,
  `board_init_class_id` int(2) DEFAULT NULL,
  `board_init_piece_color` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`board_init_id`),
  UNIQUE KEY `board_coord_id_UNIQUE` (`board_init_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `boards`
--

DROP TABLE IF EXISTS `boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boards` (
  `board_id` int(11) NOT NULL AUTO_INCREMENT,
  `board_name` varchar(45) DEFAULT NULL,
  `board_row_count` int(11) NOT NULL DEFAULT '8',
  `board_col_count` int(11) NOT NULL DEFAULT '8',
  `board_home_col` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`board_id`),
  UNIQUE KEY `board_id_UNIQUE` (`board_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cards` (
  `card_id` int(11) NOT NULL AUTO_INCREMENT,
  `card_rarity` int(11) NOT NULL DEFAULT '1',
  `card_play_opportunity` int(1) NOT NULL DEFAULT '0' COMMENT '0 represents that a card can only be played before the game begins. 1 represents a that a card can only be played during the game (1 per turn)',
  PRIMARY KEY (`card_id`),
  UNIQUE KEY `card_id_UNIQUE` (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(45) NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_id_UNIQUE` (`class_id`),
  UNIQUE KEY `class_name_UNIQUE` (`class_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friends` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `friend_user_1_id` int(11) NOT NULL,
  `friend_user_2_id` int(11) NOT NULL,
  `friend_accepted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`friend_id`),
  UNIQUE KEY `friend_id_UNIQUE` (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laid_traps`
--

DROP TABLE IF EXISTS `laid_traps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laid_traps` (
  `space_trap_id` int(11) NOT NULL AUTO_INCREMENT,
  `space_id` int(11) NOT NULL,
  `trap_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`space_trap_id`),
  UNIQUE KEY `space_trap_id_UNIQUE` (`space_trap_id`),
  KEY `space_trap_space_id_idx` (`space_id`),
  KEY `space_trap_trap_id_idx` (`trap_id`),
  KEY `laid_trap_user_id_idx` (`user_id`),
  CONSTRAINT `laid_trap_space_id` FOREIGN KEY (`space_id`) REFERENCES `spaces` (`space_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `laid_trap_trap_id` FOREIGN KEY (`trap_id`) REFERENCES `traps` (`trap_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `laid_trap_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `match_move_log`
--

DROP TABLE IF EXISTS `match_move_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match_move_log` (
  `match_move_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_move_match_id` int(11) NOT NULL,
  `match_move_move_id` int(11) NOT NULL,
  `match_move_piece_id` int(11) NOT NULL,
  `match_move_new_space_id` int(11) NOT NULL,
  `match_move_timestamp` varchar(20) NOT NULL,
  PRIMARY KEY (`match_move_log_id`),
  UNIQUE KEY `match_move_log_id_UNIQUE` (`match_move_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `match_users`
--

DROP TABLE IF EXISTS `match_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match_users` (
  `match_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_user_user_id` int(11) DEFAULT NULL,
  `match_user_match_id` int(11) NOT NULL,
  `match_user_color` varchar(45) DEFAULT NULL,
  `match_user_is_ready` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_user_id`),
  UNIQUE KEY `match_user_id_UNIQUE` (`match_user_id`),
  KEY `match_user_white_user_id_idx` (`match_user_user_id`),
  KEY `match_user_match_id_idx` (`match_user_match_id`),
  CONSTRAINT `match_user_match_id` FOREIGN KEY (`match_user_match_id`) REFERENCES `matches` (`match_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `match_user_white_user_id` FOREIGN KEY (`match_user_user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_name` varchar(45) DEFAULT NULL,
  `match_board_id` int(11) NOT NULL,
  `match_turn_count` int(11) NOT NULL DEFAULT '1',
  `match_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_id`),
  UNIQUE KEY `match_id_UNIQUE` (`match_id`),
  KEY `match_board_id_idx` (`match_board_id`),
  CONSTRAINT `match_board_id` FOREIGN KEY (`match_board_id`) REFERENCES `boards` (`board_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pieces`
--

DROP TABLE IF EXISTS `pieces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pieces` (
  `piece_id` int(11) NOT NULL AUTO_INCREMENT,
  `piece_space_id` int(11) DEFAULT NULL,
  `piece_class_id` int(11) NOT NULL,
  `piece_ability_id` int(11) DEFAULT NULL,
  `piece_user_id` int(11) NOT NULL,
  `piece_kill_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`piece_id`),
  UNIQUE KEY `piece_space_id_UNIQUE` (`piece_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `power_cards`
--

DROP TABLE IF EXISTS `power_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `power_cards` (
  `power_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `power_card_name` varchar(100) NOT NULL,
  `power_card_description` mediumtext NOT NULL,
  `power_card_card_id` int(11) NOT NULL,
  `power_card_ability_id` int(11) NOT NULL,
  PRIMARY KEY (`power_card_id`),
  UNIQUE KEY `tarrot_card_id_UNIQUE` (`power_card_id`),
  UNIQUE KEY `card_id_UNIQUE` (`power_card_card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `space_types`
--

DROP TABLE IF EXISTS `space_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `space_types` (
  `space_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `space_type_name` varchar(45) NOT NULL,
  PRIMARY KEY (`space_type_id`),
  UNIQUE KEY `space_type_id_UNIQUE` (`space_type_id`),
  UNIQUE KEY `space_type_name_UNIQUE` (`space_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spaces`
--

DROP TABLE IF EXISTS `spaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spaces` (
  `space_id` int(11) NOT NULL AUTO_INCREMENT,
  `space_match_id` int(11) NOT NULL,
  `space_coord_x` int(11) NOT NULL,
  `space_coord_y` int(11) NOT NULL,
  `space_type_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`space_id`),
  UNIQUE KEY `board_space_id_UNIQUE` (`space_id`),
  KEY `match_space_id_idx` (`space_match_id`),
  KEY `match_space_type_id_idx` (`space_type_id`),
  CONSTRAINT `match_space_id` FOREIGN KEY (`space_match_id`) REFERENCES `matches` (`match_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trap_cards`
--

DROP TABLE IF EXISTS `trap_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trap_cards` (
  `trap_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `trap_card_name` varchar(45) NOT NULL,
  `trap_card_description` varchar(45) NOT NULL,
  `trap_card_card_id` int(11) NOT NULL,
  `trap_card_trap_id` int(11) NOT NULL,
  PRIMARY KEY (`trap_card_id`),
  UNIQUE KEY `trap_card_id_UNIQUE` (`trap_card_id`),
  UNIQUE KEY `trap_card_name_UNIQUE` (`trap_card_name`),
  UNIQUE KEY `card_id_UNIQUE` (`trap_card_card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traps`
--

DROP TABLE IF EXISTS `traps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traps` (
  `trap_id` int(11) NOT NULL AUTO_INCREMENT,
  `trap_data` varchar(45) NOT NULL,
  `trap_level` int(11) NOT NULL,
  PRIMARY KEY (`trap_id`),
  UNIQUE KEY `trap_id_UNIQUE` (`trap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cards`
--

DROP TABLE IF EXISTS `user_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_cards` (
  `user_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_card_card_id` int(11) NOT NULL,
  `user_card_user_id` int(11) NOT NULL,
  `user_card_match_id` int(11) DEFAULT NULL,
  `user_card_is_used` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_card_id`),
  UNIQUE KEY `user_card_id_UNIQUE` (`user_card_id`),
  KEY `card_id_idx` (`user_card_card_id`),
  KEY `user_id_idx` (`user_card_user_id`),
  CONSTRAINT `card_id` FOREIGN KEY (`user_card_card_id`) REFERENCES `cards` (`card_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `card_user_id` FOREIGN KEY (`user_card_user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(45) NOT NULL,
  `user_email` varchar(45) NOT NULL,
  `user_password` varchar(128) NOT NULL,
  `user_account_type_id` int(11) NOT NULL DEFAULT '2',
  `user_token` varchar(128) DEFAULT NULL,
  `user_win_count` int(11) NOT NULL DEFAULT '0',
  `user_loss_count` int(11) NOT NULL DEFAULT '0',
  `user_score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  UNIQUE KEY `user_name_UNIQUE` (`user_name`),
  UNIQUE KEY `user_email_UNIQUE` (`user_email`),
  UNIQUE KEY `user_token_UNIQUE` (`user_token`),
  KEY `user_account_type_id_idx` (`user_account_type_id`),
  CONSTRAINT `user_account_type_id` FOREIGN KEY (`user_account_type_id`) REFERENCES `account_types` (`account_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-30 19:17:44

-- -----------------------------------------------------
-- Data for table `chess_n_conquer`.`account_types`
-- -----------------------------------------------------
START TRANSACTION;
USE `chess_n_conquer`;
INSERT INTO `chess_n_conquer`.`account_types` (`account_type_id`, `account_type_name`, `account_perm_level`) VALUES (1, 'guest', 1);
INSERT INTO `chess_n_conquer`.`account_types` (`account_type_id`, `account_type_name`, `account_perm_level`) VALUES (2, 'user', 2);
INSERT INTO `chess_n_conquer`.`account_types` (`account_type_id`, `account_type_name`, `account_perm_level`) VALUES (3, 'mod', 3);
INSERT INTO `chess_n_conquer`.`account_types` (`account_type_id`, `account_type_name`, `account_perm_level`) VALUES (4, 'admin', 4);

COMMIT;


-- -----------------------------------------------------
-- Data for table `chess_n_conquer`.`classes`
-- -----------------------------------------------------
START TRANSACTION;
USE `chess_n_conquer`;
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (1, 'king');
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (2, 'queen');
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (3, 'rook');
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (4, 'bishop');
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (5, 'knight');
INSERT INTO `chess_n_conquer`.`classes` (`class_id`, `class_name`) VALUES (6, 'pawn');

COMMIT;


-- -----------------------------------------------------
-- Data for table `chess_n_conquer`.`space_types`
-- -----------------------------------------------------
START TRANSACTION;
USE `chess_n_conquer`;
INSERT INTO `chess_n_conquer`.`space_types` (`space_type_id`, `space_type_name`) VALUES (1, 'normal');
INSERT INTO `chess_n_conquer`.`space_types` (`space_type_id`, `space_type_name`) VALUES (2, 'void');
INSERT INTO `chess_n_conquer`.`space_types` (`space_type_id`, `space_type_name`) VALUES (3, 'water');
INSERT INTO `chess_n_conquer`.`space_types` (`space_type_id`, `space_type_name`) VALUES (4, 'mountain');

COMMIT;
