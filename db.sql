-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.2.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pramesh
CREATE DATABASE IF NOT EXISTS `pramesh` /*!40100 DEFAULT CHARACTER SET latin1 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pramesh`;

-- Dumping structure for table pramesh.borrowers
CREATE TABLE IF NOT EXISTS `borrowers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `primary_contact_no` varchar(10) NOT NULL,
  `secondary_contact_no` varchar(10) DEFAULT NULL,
  `referenced_by` varchar(50) DEFAULT NULL,
  `referenced_contactno` varchar(10) DEFAULT NULL,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for function pramesh.calculate_interest
DELIMITER //
CREATE FUNCTION `calculate_interest`(
	`req_type` CHAR(1),
	`loan_amount` DECIMAL(10,2),
	`interest_value` DECIMAL(10,2),
	`interest_type` CHAR(1),
	`opening_date` DATE,
	`closing_date` DATE
) RETURNS decimal(10,0)
BEGIN
declare v_closing_date date;
set v_closing_date = case when closing_date is not null then closing_date
								when req_type = 'L' then Last_day(Date_sub(Curdate(), interval 1 MONTH))
								when closing_date is null then Curdate()

							end; 

 return  loan_amount * ( interest_value / 100 ) / 30 *
                 CASE
					  WHEN interest_type = 'P' THEN
                 (Datediff(v_closing_date,opening_date)+ 1 )
                 WHEN interest_type = 'F' THEN
                 CAST((12 * (YEAR(v_closing_date) - YEAR(date_sub(opening_date, INTERVAL 1 MONTH))) 
       + (MONTH(v_closing_date)  - MONTH(date_sub(opening_date, INTERVAL 1 MONTH)))) * 30  AS UNSIGNED) -  (DAY(opening_date) -1) - (30 - if(DAY(v_closing_date)=31,30,DAY(v_closing_date)))
                 ELSE 0
               END;

END//
DELIMITER ;

-- Dumping structure for table pramesh.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.comments_rel
CREATE TABLE IF NOT EXISTS `comments_rel` (
  `commentid` int DEFAULT NULL,
  `tablename` varchar(50) DEFAULT NULL,
  `id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.investments
CREATE TABLE IF NOT EXISTS `investments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lenderid` int NOT NULL DEFAULT '0',
  `txn_date` date NOT NULL,
  `bank_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_type` enum('C','D') NOT NULL,
  `transaction_category` enum('Loan','Interest','Expense') NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_investments_lenders` (`lenderid`),
  CONSTRAINT `FK_investments_lenders` FOREIGN KEY (`lenderid`) REFERENCES `lenders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3208 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.lenders
CREATE TABLE IF NOT EXISTS `lenders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `primary_contact_no` varchar(10) NOT NULL,
  `secondary_contact_no` varchar(10) DEFAULT NULL,
  `net_investment` decimal(10,2) DEFAULT NULL,
  `current_balance` decimal(10,0) DEFAULT NULL,
  `owner` tinyint(1) DEFAULT NULL,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.loans
CREATE TABLE IF NOT EXISTS `loans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lenderid` int NOT NULL DEFAULT '0',
  `borrowerid` int NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `opening_date` date NOT NULL,
  `closing_date` date DEFAULT NULL,
  `agreed_closing_date` date NOT NULL,
  `interest_type` set('F','P') DEFAULT NULL,
  `interest_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` varchar(500) DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL,
  `updated_by` int DEFAULT NULL,
  `parent_loanid` int DEFAULT NULL,
  `loan_opening_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_loans_accounts_2` (`borrowerid`),
  KEY `FK_loans_users` (`created_by`),
  KEY `FK_loans_users_2` (`updated_by`),
  KEY `FK_loans_accounts` (`lenderid`),
  CONSTRAINT `FK_loans_accounts_2` FOREIGN KEY (`borrowerid`) REFERENCES `borrowers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1135 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.recoverables
CREATE TABLE IF NOT EXISTS `recoverables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `loanid` int NOT NULL,
  `recovery_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `collected_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `collected_date` date DEFAULT NULL,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.recovery
CREATE TABLE IF NOT EXISTS `recovery` (
  `recoveryid` int NOT NULL AUTO_INCREMENT,
  `loanid` int NOT NULL,
  `recovery_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `collected_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_id` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recoveryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.settlement
CREATE TABLE IF NOT EXISTS `settlement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `loanid` int NOT NULL,
  `lender_interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `recovery` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  `excess` decimal(10,2) NOT NULL DEFAULT '0.00',
  `settlement_date` date NOT NULL,
  `description` varchar(250) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5964 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `loanid` int NOT NULL,
  `transaction_type` set('B','R','I','E') NOT NULL,
  `transaction_date` date NOT NULL,
  `bank_date` date DEFAULT NULL,
  `behalf_of` tinyint DEFAULT '0',
  `waiver` tinyint DEFAULT '0',
  `flag` tinyint DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL,
  `narration` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_transactions_loans` (`loanid`),
  KEY `FK_transactions_users` (`created_by`),
  CONSTRAINT `FK_transactions_loans` FOREIGN KEY (`loanid`) REFERENCES `loans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8442 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table pramesh.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(40) DEFAULT NULL,
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
