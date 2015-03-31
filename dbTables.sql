SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `Types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Types` (
  `typeId` VARCHAR(36) NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`typeId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Users`
-- -----------------------------------------------------
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
CREATE TABLE IF NOT EXISTS `Pages` (
  `idPages` VARCHAR(36) NOT NULL,
  `name` TINYTEXT NULL,
  `owner` VARCHAR(36) NULL,
  `creationDate` DECIMAL(17,4) NULL,
  PRIMARY KEY (`idPages`),
  CONSTRAINT `fk_Pages_1`
    FOREIGN KEY (`owner`)
    REFERENCES `Users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Pages_1_idx` ON `Pages` (`owner` ASC);


-- -----------------------------------------------------
-- Table `PageCards`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PageCards` (
  `idPage` VARCHAR(36) NOT NULL,
  `idCard` VARCHAR(36) NOT NULL,
  `cardTypeId` VARCHAR(36) NULL,
  PRIMARY KEY (`idPage`, `idCard`),
  CONSTRAINT `fk_PagesCards_1`
    FOREIGN KEY (`idPage`)
    REFERENCES `Pages` (`idPages`)
    ON DELETE NO ACTION
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
    ON DELETE NO ACTION
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
CREATE TABLE IF NOT EXISTS `CompletedForms` (
  `formId` VARCHAR(36) NOT NULL,
  `completionDate` DECIMAL(17,4) NULL,
  `sourceIp` VARCHAR(45) NULL,
  `owner` VARCHAR(36) NULL,
  PRIMARY KEY (`formId`),
  CONSTRAINT `fk_CompletedForms_2`
    FOREIGN KEY (`owner`)
    REFERENCES `Users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_CompletedForms_2_idx` ON `CompletedForms` (`owner` ASC);


-- -----------------------------------------------------
-- Table `FormFields`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `FormFields` (
  `fieldId` VARCHAR(36) NOT NULL,
  `formId` VARCHAR(36) NOT NULL,
  `contentType` VARCHAR(36) NULL,
  `content` TEXT NULL,
  PRIMARY KEY (`fieldId`, `formId`),
  CONSTRAINT `fk_FormData_1`
    FOREIGN KEY (`formId`)
    REFERENCES `CompletedForms` (`formId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_FormData_2`
    FOREIGN KEY (`contentType`)
    REFERENCES `Types` (`typeId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_FormData_1_idx` ON `FormFields` (`formId` ASC);

CREATE INDEX `fk_FormData_2_idx` ON `FormFields` (`contentType` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
