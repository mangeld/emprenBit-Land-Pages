SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `landingPages` ;
CREATE SCHEMA IF NOT EXISTS `landingPages` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `landingPages` ;

-- -----------------------------------------------------
-- Table `Types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Types` ;

CREATE TABLE IF NOT EXISTS `Types` (
  `typeId` VARCHAR(36) NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`typeId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Users` ;

CREATE TABLE IF NOT EXISTS `Users` (
  `userId` VARCHAR(36) NOT NULL,
  `registrationDate` DECIMAL(17,4) NULL,
  `isAdmin` TINYINT(1) NULL,
  `email` TINYTEXT NULL,
  `passwordHash` VARCHAR(45) NULL,
  PRIMARY KEY (`userId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Pages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Pages` ;

CREATE TABLE IF NOT EXISTS `Pages` (
  `idPages` VARCHAR(36) NOT NULL,
  `name` TINYTEXT NULL,
  `owner` VARCHAR(36) NULL,
  `creationDate` DECIMAL(17,4) NULL,
  `title` TINYTEXT NULL,
  `description` MEDIUMTEXT NULL,
  `logoId` VARCHAR(36) NULL,
  PRIMARY KEY (`idPages`),
  CONSTRAINT `fk_Pages_1`
    FOREIGN KEY (`owner`)
    REFERENCES `Users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Pages_1_idx` ON `Pages` (`owner` ASC);


-- -----------------------------------------------------
-- Table `PageCards`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `PageCards` ;

CREATE TABLE IF NOT EXISTS `PageCards` (
  `idPage` VARCHAR(36) NOT NULL,
  `idCard` VARCHAR(36) NOT NULL,
  `cardTypeId` VARCHAR(36) NULL,
  `index` INT NULL,
  PRIMARY KEY (`idPage`, `idCard`),
  CONSTRAINT `fk_PagesCards_1`
    FOREIGN KEY (`idPage`)
    REFERENCES `Pages` (`idPages`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_PageCards_1`
    FOREIGN KEY (`cardTypeId`)
    REFERENCES `Types` (`typeId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_PagesCards_1_idx` ON `PageCards` (`idPage` ASC);

CREATE INDEX `fk_PageCards_1_idx` ON `PageCards` (`cardTypeId` ASC);

CREATE UNIQUE INDEX `idCard_UNIQUE` ON `PageCards` (`idCard` ASC);


-- -----------------------------------------------------
-- Table `CardContent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CardContent` ;

CREATE TABLE IF NOT EXISTS `CardContent` (
  `idCardContent` VARCHAR(36) NOT NULL,
  `idCard` VARCHAR(36) NOT NULL,
  `typeId` VARCHAR(36) NULL,
  `text` MEDIUMTEXT NULL,
  `index` INT NULL,
  PRIMARY KEY (`idCardContent`, `idCard`),
  CONSTRAINT `fk_CardContent_1`
    FOREIGN KEY (`idCard`)
    REFERENCES `PageCards` (`idCard`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CardContent_2`
    FOREIGN KEY (`typeId`)
    REFERENCES `Types` (`typeId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
PACK_KEYS = DEFAULT;

CREATE INDEX `fk_CardContent_1_idx` ON `CardContent` (`idCard` ASC);

CREATE INDEX `fk_CardContent_2_idx` ON `CardContent` (`typeId` ASC);


-- -----------------------------------------------------
-- Table `Sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Sessions` ;

CREATE TABLE IF NOT EXISTS `Sessions` (
  `sessionId` VARCHAR(36) NOT NULL,
  `userId` VARCHAR(36) NULL,
  `createdDate` DECIMAL(17,4) NULL,
  `expireDate` DECIMAL(17,4) NULL,
  `loggedFromIp` VARCHAR(45) NULL,
  `isExpired` TINYINT(1) NULL,
  PRIMARY KEY (`sessionId`),
  CONSTRAINT `fk_Sessions_1`
    FOREIGN KEY (`userId`)
    REFERENCES `Users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Sessions_1_idx` ON `Sessions` (`userId` ASC);


-- -----------------------------------------------------
-- Table `CompletedForms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CompletedForms` ;

CREATE TABLE IF NOT EXISTS `CompletedForms` (
  `formId` VARCHAR(36) NOT NULL,
  `completionDate` DECIMAL(17,4) NULL,
  `sourceIp` VARCHAR(45) NULL,
  `pageId` VARCHAR(36) NULL,
  `field_name` VARCHAR(45) NULL,
  `field_email` VARCHAR(45) NULL,
  PRIMARY KEY (`formId`),
  CONSTRAINT `fk_CompletedForms_2`
    FOREIGN KEY (`pageId`)
    REFERENCES `Pages` (`idPages`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_CompletedForms_2_idx` ON `CompletedForms` (`pageId` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `Types`
-- -----------------------------------------------------
START TRANSACTION;
USE `landingPages`;
INSERT INTO `Types` (`typeId`, `name`) VALUES ('683b5e06-9ba1-425f-88bd-d3667b4cdc13', 'cardThreeColumns');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('a8620342-f3e2-4b90-9f01-eb3b412db22d', 'cardForm');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('d082a408-e024-4998-8484-3f0b3d4902af', 'fieldEmail');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('48fa0dfc-ecce-4adf-ab22-4dacb307e452', 'fieldText');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('2b870b7c-7d25-4366-a948-49b0b0fb512b', 'fieldTitle');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('c48f1022-60d3-4b51-9290-6605152b8a90', 'fieldImage');
INSERT INTO `Types` (`typeId`, `name`) VALUES ('1cc22fdb-4367-4d17-a1e3-9ddafcc60a97', 'cardCarousel');

COMMIT;

