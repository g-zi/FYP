/* fremo_yellow_pages database structure */

/* Structure for table `Groups` */
DROP TABLE IF EXISTS `Groups`;

CREATE TABLE `Groups` (
  `Group_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Group_Name` varchar(225) NOT NULL,
  PRIMARY KEY (`Group_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `Users` */
DROP TABLE IF EXISTS `Users`;

CREATE TABLE `Users` (
  `User_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(150) NOT NULL,
  `Username_Clean` varchar(150) NOT NULL,
  `Password` varchar(225) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `ActivationToken` varchar(225) NOT NULL,
  `LastActivationRequest` int(11) NOT NULL,
  `LostPasswordRequest` int(1) NOT NULL DEFAULT '0',
  `Active` int(1) NOT NULL,
  `Group_ID` int(11) NOT NULL,
  `SignUpDate` int(11) NOT NULL,
  `LastSignIn` int(11) NOT NULL,
  `Language` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de',
  PRIMARY KEY (`User_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `bahnhof` */
DROP TABLE IF EXISTS `bahnhof`;

CREATE TABLE `bahnhof` (
  `Haltestelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Spur` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Kurzbezeichnung` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Bahnverwaltung` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Besitzer` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Art` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Bhf_Bem` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Zeichnung` varchar(255) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Streckengleise` int(2) unsigned NOT NULL DEFAULT '1',
  `Kreuzung` int(4) unsigned DEFAULT NULL,
  `Einleitung` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Beschreibung` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Personenverkehr` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Frachtverkehr` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Language` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de',
  `LastUser` varchar(150) DEFAULT NULL,
  `LastUser_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `frachtzettel` */
DROP TABLE IF EXISTS `frachtzettel`;

CREATE TABLE `frachtzettel` (
  `Menge` smallint(2) unsigned DEFAULT '1',
  `Eilgut` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Wenden` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Mehrfach` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Stueckgut` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Zielbahnhof` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Empfaenger` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Ecol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Gewicht` smallint(3) unsigned DEFAULT '1',
  `Wagengattung` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Freight` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Ladung` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Versandbahnhof` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Versender` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Vcol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `LadeEmpfang` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Lcol` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ZBV` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `VBV` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Treffen` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `User` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `FUser_ID` bigint(20) NOT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `fyp` */
DROP TABLE IF EXISTS `fyp`;

CREATE TABLE `fyp` (
  `NHM_Code` int(8) NOT NULL DEFAULT '99000000',
  `Wagengattung` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Produktbeschreibung` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Product_Description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Anschliesser` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `FYP_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Gleis_ID` bigint(20) DEFAULT '0',
  `Bhf_ID` bigint(20) unsigned DEFAULT '0',
  `id_fyp` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Ladestelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Wagen_Woche` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_fyp`),
  KEY `NHM_Code` (`NHM_Code`),
  FULLTEXT KEY `Betriebsstelle` (`Betriebsstelle`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `gleise` */
DROP TABLE IF EXISTS `gleise`;

CREATE TABLE `gleise` (
  `Gleisname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Gleislaenge` int(4) unsigned NOT NULL DEFAULT '1',
  `Gleisart` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Bahnsteiglaenge` int(4) unsigned NOT NULL,
  `LadeFarbe` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `Ladestelle` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Gl_Bem` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Bhf_ID` bigint(20) NOT NULL DEFAULT '0',
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `Name` (`Gleisname`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* Structure for table `manage` */
DROP TABLE IF EXISTS `manage`;

CREATE TABLE `manage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `User` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `MUser_ID` bigint(20) DEFAULT NULL,
  `Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Bhf_ID` bigint(20) DEFAULT '0',
  `MainMngr_ID` bigint(20) DEFAULT '0',
  `Mng_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Mitverwalter` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `mngmod` */
DROP TABLE IF EXISTS `mngmod`;

CREATE TABLE `mngmod` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `User` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `MUser_ID` bigint(20) NOT NULL,
  `Md_ID` bigint(20) DEFAULT '0',
  `ModRem` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* Structure for table `module` */
DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `Name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `NR` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Rem` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Spur` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Radius` int(10) DEFAULT NULL,
  `Winkel` int(10) DEFAULT NULL,
  `Laenge` int(6) DEFAULT NULL,
  `Breite` int(6) DEFAULT NULL,
  `Endprofil_1` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Endprofil_2` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Endprofil_3` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Signalschacht` int(1) DEFAULT NULL,
  `Status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Besitzer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Zeichnung` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Bhf_ID` bigint(20) unsigned DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_module` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_module`),
  UNIQUE KEY `id_module` (`id_module`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* Structure for table `treffen` */
DROP TABLE IF EXISTS `treffen`;

CREATE TABLE `treffen` (
  `Treffen` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Telefon` int(3) unsigned DEFAULT '0',
  `Betriebsstelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Bhf_ID` bigint(20) unsigned DEFAULT '0',
  `Anschliesser` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `An_ID` bigint(20) unsigned DEFAULT '0',
  `Trf_Bem` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/* Structure for table `nhm` */
DROP TABLE IF EXISTS `nhm`;

CREATE TABLE `nhm` (
  `NHM_Code` int(8) NOT NULL,
  `UIC_Wagengattung` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `English` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Nederlands` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Deutsch` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Francais` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Italiano` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Dansk` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Espanol` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Polski` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Bulgarian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Greek` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Czech` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Rumanian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Hungarian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Russian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Portuges` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Slovak` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Slovene` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Svenska` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Estonian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Suomeksi` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Latvian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Lithuanian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`NHM_Code`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
