-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 03-06-2013 a las 00:26:05
-- Versión del servidor: 5.5.24-log
-- Versión de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dolimich331`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_accountingaccount`
--

CREATE TABLE IF NOT EXISTS `llx_accountingaccount` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_pcg_version` varchar(12) NOT NULL,
  `pcg_type` varchar(20) NOT NULL,
  `pcg_subtype` varchar(20) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `account_parent` varchar(20) DEFAULT NULL,
  `label` varchar(128) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  KEY `idx_accountingaccount_fk_pcg_version` (`fk_pcg_version`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=439 ;

--
-- Volcado de datos para la tabla `llx_accountingaccount`
--

INSERT INTO `llx_accountingaccount` (`rowid`, `fk_pcg_version`, `pcg_type`, `pcg_subtype`, `account_number`, `account_parent`, `label`, `active`) VALUES
(1, 'PCG99-ABREGE', 'CAPIT', 'CAPITAL', '101', '1', 'Capital', 1),
(2, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '105', '1', 'Ecarts de réévaluation', 1),
(3, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '1061', '1', 'Réserve légale', 1),
(4, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '1063', '1', 'Réserves statutaires ou contractuelles', 1),
(5, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '1064', '1', 'Réserves réglementées', 1),
(6, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '1068', '1', 'Autres réserves', 1),
(7, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '108', '1', 'Compte de l''exploitant', 1),
(8, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '12', '1', 'Résultat de l''exercice', 1),
(9, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '145', '1', 'Amortissements dérogatoires', 1),
(10, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '146', '1', 'Provision spéciale de réévaluation', 1),
(11, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '147', '1', 'Plus-values réinvesties', 1),
(12, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '148', '1', 'Autres provisions réglementées', 1),
(13, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '15', '1', 'Provisions pour risques et charges', 1),
(14, 'PCG99-ABREGE', 'CAPIT', 'XXXXXX', '16', '1', 'Emprunts et dettes assimilees', 1),
(15, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '20', '2', 'Immobilisations incorporelles', 1),
(16, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '201', '20', 'Frais d''établissement', 1),
(17, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '206', '20', 'Droit au bail', 1),
(18, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '207', '20', 'Fonds commercial', 1),
(19, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '208', '20', 'Autres immobilisations incorporelles', 1),
(20, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '21', '2', 'Immobilisations corporelles', 1),
(21, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '23', '2', 'Immobilisations en cours', 1),
(22, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '27', '2', 'Autres immobilisations financieres', 1),
(23, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '280', '2', 'Amortissements des immobilisations incorporelles', 1),
(24, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '281', '2', 'Amortissements des immobilisations corporelles', 1),
(25, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '290', '2', 'Provisions pour dépréciation des immobilisations incorporelles', 1),
(26, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '291', '2', 'Provisions pour dépréciation des immobilisations corporelles', 1),
(27, 'PCG99-ABREGE', 'IMMO', 'XXXXXX', '297', '2', 'Provisions pour dépréciation des autres immobilisations financières', 1),
(28, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '31', '3', 'Matieres premières', 1),
(29, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '32', '3', 'Autres approvisionnements', 1),
(30, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '33', '3', 'En-cours de production de biens', 1),
(31, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '34', '3', 'En-cours de production de services', 1),
(32, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '35', '3', 'Stocks de produits', 1),
(33, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '37', '3', 'Stocks de marchandises', 1),
(34, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '391', '3', 'Provisions pour dépréciation des matières premières', 1),
(35, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '392', '3', 'Provisions pour dépréciation des autres approvisionnements', 1),
(36, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '393', '3', 'Provisions pour dépréciation des en-cours de production de biens', 1),
(37, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '394', '3', 'Provisions pour dépréciation des en-cours de production de services', 1),
(38, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '395', '3', 'Provisions pour dépréciation des stocks de produits', 1),
(39, 'PCG99-ABREGE', 'STOCK', 'XXXXXX', '397', '3', 'Provisions pour dépréciation des stocks de marchandises', 1),
(40, 'PCG99-ABREGE', 'TIERS', 'SUPPLIER', '400', '4', 'Fournisseurs et Comptes rattachés', 1),
(41, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '409', '4', 'Fournisseurs débiteurs', 1),
(42, 'PCG99-ABREGE', 'TIERS', 'CUSTOMER', '410', '4', 'Clients et Comptes rattachés', 1),
(43, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '419', '4', 'Clients créditeurs', 1),
(44, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '421', '4', 'Personnel', 1),
(45, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '428', '4', 'Personnel', 1),
(46, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '43', '4', 'Sécurité sociale et autres organismes sociaux', 1),
(47, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '444', '4', 'Etat - impôts sur bénéfice', 1),
(48, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '445', '4', 'Etat - Taxes sur chiffre affaires', 1),
(49, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '447', '4', 'Autres impôts, taxes et versements assimilés', 1),
(50, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '45', '4', 'Groupe et associes', 1),
(51, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '455', '45', 'Associés', 1),
(52, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '46', '4', 'Débiteurs divers et créditeurs divers', 1),
(53, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '47', '4', 'Comptes transitoires ou d''attente', 1),
(54, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '481', '4', 'Charges à répartir sur plusieurs exercices', 1),
(55, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '486', '4', 'Charges constatées d''avance', 1),
(56, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '487', '4', 'Produits constatés d''avance', 1),
(57, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '491', '4', 'Provisions pour dépréciation des comptes de clients', 1),
(58, 'PCG99-ABREGE', 'TIERS', 'XXXXXX', '496', '4', 'Provisions pour dépréciation des comptes de débiteurs divers', 1),
(59, 'PCG99-ABREGE', 'FINAN', 'XXXXXX', '50', '5', 'Valeurs mobilières de placement', 1),
(60, 'PCG99-ABREGE', 'FINAN', 'BANK', '51', '5', 'Banques, établissements financiers et assimilés', 1),
(61, 'PCG99-ABREGE', 'FINAN', 'CASH', '53', '5', 'Caisse', 1),
(62, 'PCG99-ABREGE', 'FINAN', 'XXXXXX', '54', '5', 'Régies d''avance et accréditifs', 1),
(63, 'PCG99-ABREGE', 'FINAN', 'XXXXXX', '58', '5', 'Virements internes', 1),
(64, 'PCG99-ABREGE', 'FINAN', 'XXXXXX', '590', '5', 'Provisions pour dépréciation des valeurs mobilières de placement', 1),
(65, 'PCG99-ABREGE', 'CHARGE', 'PRODUCT', '60', '6', 'Achats', 1),
(66, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '603', '60', 'Variations des stocks', 1),
(67, 'PCG99-ABREGE', 'CHARGE', 'SERVICE', '61', '6', 'Services extérieurs', 1),
(68, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '62', '6', 'Autres services extérieurs', 1),
(69, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '63', '6', 'Impôts, taxes et versements assimiles', 1),
(70, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '641', '6', 'Rémunérations du personnel', 1),
(71, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '644', '6', 'Rémunération du travail de l''exploitant', 1),
(72, 'PCG99-ABREGE', 'CHARGE', 'SOCIAL', '645', '6', 'Charges de sécurité sociale et de prévoyance', 1),
(73, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '646', '6', 'Cotisations sociales personnelles de l''exploitant', 1),
(74, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '65', '6', 'Autres charges de gestion courante', 1),
(75, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '66', '6', 'Charges financières', 1),
(76, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '67', '6', 'Charges exceptionnelles', 1),
(77, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '681', '6', 'Dotations aux amortissements et aux provisions', 1),
(78, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '686', '6', 'Dotations aux amortissements et aux provisions', 1),
(79, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '687', '6', 'Dotations aux amortissements et aux provisions', 1),
(80, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '691', '6', 'Participation des salariés aux résultats', 1),
(81, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '695', '6', 'Impôts sur les bénéfices', 1),
(82, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '697', '6', 'Imposition forfaitaire annuelle des sociétés', 1),
(83, 'PCG99-ABREGE', 'CHARGE', 'XXXXXX', '699', '6', 'Produits', 1),
(84, 'PCG99-ABREGE', 'PROD', 'PRODUCT', '701', '7', 'Ventes de produits finis', 1),
(85, 'PCG99-ABREGE', 'PROD', 'SERVICE', '706', '7', 'Prestations de services', 1),
(86, 'PCG99-ABREGE', 'PROD', 'PRODUCT', '707', '7', 'Ventes de marchandises', 1),
(87, 'PCG99-ABREGE', 'PROD', 'PRODUCT', '708', '7', 'Produits des activités annexes', 1),
(88, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '709', '7', 'Rabais, remises et ristournes accordés par l''entreprise', 1),
(89, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '713', '7', 'Variation des stocks', 1),
(90, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '72', '7', 'Production immobilisée', 1),
(91, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '73', '7', 'Produits nets partiels sur opérations à long terme', 1),
(92, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '74', '7', 'Subventions d''exploitation', 1),
(93, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '75', '7', 'Autres produits de gestion courante', 1),
(94, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '753', '75', 'Jetons de présence et rémunérations d''administrateurs, gérants,...', 1),
(95, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '754', '75', 'Ristournes perçues des coopératives', 1),
(96, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '755', '75', 'Quotes-parts de résultat sur opérations faites en commun', 1),
(97, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '76', '7', 'Produits financiers', 1),
(98, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '77', '7', 'Produits exceptionnels', 1),
(99, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '781', '7', 'Reprises sur amortissements et provisions', 1),
(100, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '786', '7', 'Reprises sur provisions pour risques', 1),
(101, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '787', '7', 'Reprises sur provisions', 1),
(102, 'PCG99-ABREGE', 'PROD', 'XXXXXX', '79', '7', 'Transferts de charges', 1),
(103, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '10', '1', 'Capital  et réserves', 1),
(104, 'PCG99-BASE', 'CAPIT', 'CAPITAL', '101', '10', 'Capital', 1),
(105, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '104', '10', 'Primes liées au capital social', 1),
(106, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '105', '10', 'Ecarts de réévaluation', 1),
(107, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '106', '10', 'Réserves', 1),
(108, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '107', '10', 'Ecart d''equivalence', 1),
(109, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '108', '10', 'Compte de l''exploitant', 1),
(110, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '109', '10', 'Actionnaires : capital souscrit - non appelé', 1),
(111, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '11', '1', 'Report à nouveau (solde créditeur ou débiteur)', 1),
(112, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '110', '11', 'Report à nouveau (solde créditeur)', 1),
(113, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '119', '11', 'Report à nouveau (solde débiteur)', 1),
(114, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '12', '1', 'Résultat de l''exercice (bénéfice ou perte)', 1),
(115, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '120', '12', 'Résultat de l''exercice (bénéfice)', 1),
(116, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '129', '12', 'Résultat de l''exercice (perte)', 1),
(117, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '13', '1', 'Subventions d''investissement', 1),
(118, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '131', '13', 'Subventions d''équipement', 1),
(119, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '138', '13', 'Autres subventions d''investissement', 1),
(120, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '139', '13', 'Subventions d''investissement inscrites au compte de résultat', 1),
(121, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '14', '1', 'Provisions réglementées', 1),
(122, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '142', '14', 'Provisions réglementées relatives aux immobilisations', 1),
(123, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '143', '14', 'Provisions réglementées relatives aux stocks', 1),
(124, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '144', '14', 'Provisions réglementées relatives aux autres éléments de l''actif', 1),
(125, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '145', '14', 'Amortissements dérogatoires', 1),
(126, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '146', '14', 'Provision spéciale de réévaluation', 1),
(127, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '147', '14', 'Plus-values réinvesties', 1),
(128, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '148', '14', 'Autres provisions réglementées', 1),
(129, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '15', '1', 'Provisions pour risques et charges', 1),
(130, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '151', '15', 'Provisions pour risques', 1),
(131, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '153', '15', 'Provisions pour pensions et obligations similaires', 1),
(132, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '154', '15', 'Provisions pour restructurations', 1),
(133, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '155', '15', 'Provisions pour impôts', 1),
(134, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '156', '15', 'Provisions pour renouvellement des immobilisations (entreprises concessionnaires)', 1),
(135, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '157', '15', 'Provisions pour charges à répartir sur plusieurs exercices', 1),
(136, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '158', '15', 'Autres provisions pour charges', 1),
(137, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '16', '1', 'Emprunts et dettes assimilees', 1),
(138, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '161', '16', 'Emprunts obligataires convertibles', 1),
(139, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '163', '16', 'Autres emprunts obligataires', 1),
(140, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '164', '16', 'Emprunts auprès des établissements de crédit', 1),
(141, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '165', '16', 'Dépôts et cautionnements reçus', 1),
(142, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '166', '16', 'Participation des salariés aux résultats', 1),
(143, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '167', '16', 'Emprunts et dettes assortis de conditions particulières', 1),
(144, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '168', '16', 'Autres emprunts et dettes assimilées', 1),
(145, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '169', '16', 'Primes de remboursement des obligations', 1),
(146, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '17', '1', 'Dettes rattachées à des participations', 1),
(147, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '171', '17', 'Dettes rattachées à des participations (groupe)', 1),
(148, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '174', '17', 'Dettes rattachées à des participations (hors groupe)', 1),
(149, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '178', '17', 'Dettes rattachées à des sociétés en participation', 1),
(150, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '18', '1', 'Comptes de liaison des établissements et sociétés en participation', 1),
(151, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '181', '18', 'Comptes de liaison des établissements', 1),
(152, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '186', '18', 'Biens et prestations de services échangés entre établissements (charges)', 1),
(153, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '187', '18', 'Biens et prestations de services échangés entre établissements (produits)', 1),
(154, 'PCG99-BASE', 'CAPIT', 'XXXXXX', '188', '18', 'Comptes de liaison des sociétés en participation', 1),
(155, 'PCG99-BASE', 'IMMO', 'XXXXXX', '20', '2', 'Immobilisations incorporelles', 1),
(156, 'PCG99-BASE', 'IMMO', 'XXXXXX', '201', '20', 'Frais d''établissement', 1),
(157, 'PCG99-BASE', 'IMMO', 'XXXXXX', '203', '20', 'Frais de recherche et de développement', 1),
(158, 'PCG99-BASE', 'IMMO', 'XXXXXX', '205', '20', 'Concessions et droits similaires, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires', 1),
(159, 'PCG99-BASE', 'IMMO', 'XXXXXX', '206', '20', 'Droit au bail', 1),
(160, 'PCG99-BASE', 'IMMO', 'XXXXXX', '207', '20', 'Fonds commercial', 1),
(161, 'PCG99-BASE', 'IMMO', 'XXXXXX', '208', '20', 'Autres immobilisations incorporelles', 1),
(162, 'PCG99-BASE', 'IMMO', 'XXXXXX', '21', '2', 'Immobilisations corporelles', 1),
(163, 'PCG99-BASE', 'IMMO', 'XXXXXX', '211', '21', 'Terrains', 1),
(164, 'PCG99-BASE', 'IMMO', 'XXXXXX', '212', '21', 'Agencements et aménagements de terrains', 1),
(165, 'PCG99-BASE', 'IMMO', 'XXXXXX', '213', '21', 'Constructions', 1),
(166, 'PCG99-BASE', 'IMMO', 'XXXXXX', '214', '21', 'Constructions sur sol d''autrui', 1),
(167, 'PCG99-BASE', 'IMMO', 'XXXXXX', '215', '21', 'Installations techniques, matériels et outillage industriels', 1),
(168, 'PCG99-BASE', 'IMMO', 'XXXXXX', '218', '21', 'Autres immobilisations corporelles', 1),
(169, 'PCG99-BASE', 'IMMO', 'XXXXXX', '22', '2', 'Immobilisations mises en concession', 1),
(170, 'PCG99-BASE', 'IMMO', 'XXXXXX', '23', '2', 'Immobilisations en cours', 1),
(171, 'PCG99-BASE', 'IMMO', 'XXXXXX', '231', '23', 'Immobilisations corporelles en cours', 1),
(172, 'PCG99-BASE', 'IMMO', 'XXXXXX', '232', '23', 'Immobilisations incorporelles en cours', 1),
(173, 'PCG99-BASE', 'IMMO', 'XXXXXX', '237', '23', 'Avances et acomptes versés sur immobilisations incorporelles', 1),
(174, 'PCG99-BASE', 'IMMO', 'XXXXXX', '238', '23', 'Avances et acomptes versés sur commandes d''immobilisations corporelles', 1),
(175, 'PCG99-BASE', 'IMMO', 'XXXXXX', '25', '2', 'Parts dans des entreprises liées et créances sur des entreprises liées', 1),
(176, 'PCG99-BASE', 'IMMO', 'XXXXXX', '26', '2', 'Participations et créances rattachées à des participations', 1),
(177, 'PCG99-BASE', 'IMMO', 'XXXXXX', '261', '26', 'Titres de participation', 1),
(178, 'PCG99-BASE', 'IMMO', 'XXXXXX', '266', '26', 'Autres formes de participation', 1),
(179, 'PCG99-BASE', 'IMMO', 'XXXXXX', '267', '26', 'Créances rattachées à des participations', 1),
(180, 'PCG99-BASE', 'IMMO', 'XXXXXX', '268', '26', 'Créances rattachées à des sociétés en participation', 1),
(181, 'PCG99-BASE', 'IMMO', 'XXXXXX', '269', '26', 'Versements restant à effectuer sur titres de participation non libérés', 1),
(182, 'PCG99-BASE', 'IMMO', 'XXXXXX', '27', '2', 'Autres immobilisations financieres', 1),
(183, 'PCG99-BASE', 'IMMO', 'XXXXXX', '271', '27', 'Titres immobilisés autres que les titres immobilisés de l''activité de portefeuille (droit de propriété)', 1),
(184, 'PCG99-BASE', 'IMMO', 'XXXXXX', '272', '27', 'Titres immobilisés (droit de créance)', 1),
(185, 'PCG99-BASE', 'IMMO', 'XXXXXX', '273', '27', 'Titres immobilisés de l''activité de portefeuille', 1),
(186, 'PCG99-BASE', 'IMMO', 'XXXXXX', '274', '27', 'Prêts', 1),
(187, 'PCG99-BASE', 'IMMO', 'XXXXXX', '275', '27', 'Dépôts et cautionnements versés', 1),
(188, 'PCG99-BASE', 'IMMO', 'XXXXXX', '276', '27', 'Autres créances immobilisées', 1),
(189, 'PCG99-BASE', 'IMMO', 'XXXXXX', '277', '27', '(Actions propres ou parts propres)', 1),
(190, 'PCG99-BASE', 'IMMO', 'XXXXXX', '279', '27', 'Versements restant à effectuer sur titres immobilisés non libérés', 1),
(191, 'PCG99-BASE', 'IMMO', 'XXXXXX', '28', '2', 'Amortissements des immobilisations', 1),
(192, 'PCG99-BASE', 'IMMO', 'XXXXXX', '280', '28', 'Amortissements des immobilisations incorporelles', 1),
(193, 'PCG99-BASE', 'IMMO', 'XXXXXX', '281', '28', 'Amortissements des immobilisations corporelles', 1),
(194, 'PCG99-BASE', 'IMMO', 'XXXXXX', '282', '28', 'Amortissements des immobilisations mises en concession', 1),
(195, 'PCG99-BASE', 'IMMO', 'XXXXXX', '29', '2', 'Dépréciations des immobilisations', 1),
(196, 'PCG99-BASE', 'IMMO', 'XXXXXX', '290', '29', 'Dépréciations des immobilisations incorporelles', 1),
(197, 'PCG99-BASE', 'IMMO', 'XXXXXX', '291', '29', 'Dépréciations des immobilisations corporelles', 1),
(198, 'PCG99-BASE', 'IMMO', 'XXXXXX', '292', '29', 'Dépréciations des immobilisations mises en concession', 1),
(199, 'PCG99-BASE', 'IMMO', 'XXXXXX', '293', '29', 'Dépréciations des immobilisations en cours', 1),
(200, 'PCG99-BASE', 'IMMO', 'XXXXXX', '296', '29', 'Provisions pour dépréciation des participations et créances rattachées à des participations', 1),
(201, 'PCG99-BASE', 'IMMO', 'XXXXXX', '297', '29', 'Provisions pour dépréciation des autres immobilisations financières', 1),
(202, 'PCG99-BASE', 'STOCK', 'XXXXXX', '31', '3', 'Matières premières (et fournitures)', 1),
(203, 'PCG99-BASE', 'STOCK', 'XXXXXX', '311', '31', 'Matières (ou groupe) A', 1),
(204, 'PCG99-BASE', 'STOCK', 'XXXXXX', '312', '31', 'Matières (ou groupe) B', 1),
(205, 'PCG99-BASE', 'STOCK', 'XXXXXX', '317', '31', 'Fournitures A, B, C,', 1),
(206, 'PCG99-BASE', 'STOCK', 'XXXXXX', '32', '3', 'Autres approvisionnements', 1),
(207, 'PCG99-BASE', 'STOCK', 'XXXXXX', '321', '32', 'Matières consommables', 1),
(208, 'PCG99-BASE', 'STOCK', 'XXXXXX', '322', '32', 'Fournitures consommables', 1),
(209, 'PCG99-BASE', 'STOCK', 'XXXXXX', '326', '32', 'Emballages', 1),
(210, 'PCG99-BASE', 'STOCK', 'XXXXXX', '33', '3', 'En-cours de production de biens', 1),
(211, 'PCG99-BASE', 'STOCK', 'XXXXXX', '331', '33', 'Produits en cours', 1),
(212, 'PCG99-BASE', 'STOCK', 'XXXXXX', '335', '33', 'Travaux en cours', 1),
(213, 'PCG99-BASE', 'STOCK', 'XXXXXX', '34', '3', 'En-cours de production de services', 1),
(214, 'PCG99-BASE', 'STOCK', 'XXXXXX', '341', '34', 'Etudes en cours', 1),
(215, 'PCG99-BASE', 'STOCK', 'XXXXXX', '345', '34', 'Prestations de services en cours', 1),
(216, 'PCG99-BASE', 'STOCK', 'XXXXXX', '35', '3', 'Stocks de produits', 1),
(217, 'PCG99-BASE', 'STOCK', 'XXXXXX', '351', '35', 'Produits intermédiaires', 1),
(218, 'PCG99-BASE', 'STOCK', 'XXXXXX', '355', '35', 'Produits finis', 1),
(219, 'PCG99-BASE', 'STOCK', 'XXXXXX', '358', '35', 'Produits résiduels (ou matières de récupération)', 1),
(220, 'PCG99-BASE', 'STOCK', 'XXXXXX', '37', '3', 'Stocks de marchandises', 1),
(221, 'PCG99-BASE', 'STOCK', 'XXXXXX', '371', '37', 'Marchandises (ou groupe) A', 1),
(222, 'PCG99-BASE', 'STOCK', 'XXXXXX', '372', '37', 'Marchandises (ou groupe) B', 1),
(223, 'PCG99-BASE', 'STOCK', 'XXXXXX', '39', '3', 'Provisions pour dépréciation des stocks et en-cours', 1),
(224, 'PCG99-BASE', 'STOCK', 'XXXXXX', '391', '39', 'Provisions pour dépréciation des matières premières', 1),
(225, 'PCG99-BASE', 'STOCK', 'XXXXXX', '392', '39', 'Provisions pour dépréciation des autres approvisionnements', 1),
(226, 'PCG99-BASE', 'STOCK', 'XXXXXX', '393', '39', 'Provisions pour dépréciation des en-cours de production de biens', 1),
(227, 'PCG99-BASE', 'STOCK', 'XXXXXX', '394', '39', 'Provisions pour dépréciation des en-cours de production de services', 1),
(228, 'PCG99-BASE', 'STOCK', 'XXXXXX', '395', '39', 'Provisions pour dépréciation des stocks de produits', 1),
(229, 'PCG99-BASE', 'STOCK', 'XXXXXX', '397', '39', 'Provisions pour dépréciation des stocks de marchandises', 1),
(230, 'PCG99-BASE', 'TIERS', 'XXXXXX', '40', '4', 'Fournisseurs et Comptes rattachés', 1),
(231, 'PCG99-BASE', 'TIERS', 'XXXXXX', '400', '40', 'Fournisseurs et Comptes rattachés', 1),
(232, 'PCG99-BASE', 'TIERS', 'SUPPLIER', '401', '40', 'Fournisseurs', 1),
(233, 'PCG99-BASE', 'TIERS', 'XXXXXX', '403', '40', 'Fournisseurs - Effets à payer', 1),
(234, 'PCG99-BASE', 'TIERS', 'XXXXXX', '404', '40', 'Fournisseurs d''immobilisations', 1),
(235, 'PCG99-BASE', 'TIERS', 'XXXXXX', '405', '40', 'Fournisseurs d''immobilisations - Effets à payer', 1),
(236, 'PCG99-BASE', 'TIERS', 'XXXXXX', '408', '40', 'Fournisseurs - Factures non parvenues', 1),
(237, 'PCG99-BASE', 'TIERS', 'XXXXXX', '409', '40', 'Fournisseurs débiteurs', 1),
(238, 'PCG99-BASE', 'TIERS', 'XXXXXX', '41', '4', 'Clients et comptes rattachés', 1),
(239, 'PCG99-BASE', 'TIERS', 'XXXXXX', '410', '41', 'Clients et Comptes rattachés', 1),
(240, 'PCG99-BASE', 'TIERS', 'CUSTOMER', '411', '41', 'Clients', 1),
(241, 'PCG99-BASE', 'TIERS', 'XXXXXX', '413', '41', 'Clients - Effets à recevoir', 1),
(242, 'PCG99-BASE', 'TIERS', 'XXXXXX', '416', '41', 'Clients douteux ou litigieux', 1),
(243, 'PCG99-BASE', 'TIERS', 'XXXXXX', '418', '41', 'Clients - Produits non encore facturés', 1),
(244, 'PCG99-BASE', 'TIERS', 'XXXXXX', '419', '41', 'Clients créditeurs', 1),
(245, 'PCG99-BASE', 'TIERS', 'XXXXXX', '42', '4', 'Personnel et comptes rattachés', 1),
(246, 'PCG99-BASE', 'TIERS', 'XXXXXX', '421', '42', 'Personnel - Rémunérations dues', 1),
(247, 'PCG99-BASE', 'TIERS', 'XXXXXX', '422', '42', 'Comités d''entreprises, d''établissement, ...', 1),
(248, 'PCG99-BASE', 'TIERS', 'XXXXXX', '424', '42', 'Participation des salariés aux résultats', 1),
(249, 'PCG99-BASE', 'TIERS', 'XXXXXX', '425', '42', 'Personnel - Avances et acomptes', 1),
(250, 'PCG99-BASE', 'TIERS', 'XXXXXX', '426', '42', 'Personnel - Dépôts', 1),
(251, 'PCG99-BASE', 'TIERS', 'XXXXXX', '427', '42', 'Personnel - Oppositions', 1),
(252, 'PCG99-BASE', 'TIERS', 'XXXXXX', '428', '42', 'Personnel - Charges à payer et produits à recevoir', 1),
(253, 'PCG99-BASE', 'TIERS', 'XXXXXX', '43', '4', 'Sécurité sociale et autres organismes sociaux', 1),
(254, 'PCG99-BASE', 'TIERS', 'XXXXXX', '431', '43', 'Sécurité sociale', 1),
(255, 'PCG99-BASE', 'TIERS', 'XXXXXX', '437', '43', 'Autres organismes sociaux', 1),
(256, 'PCG99-BASE', 'TIERS', 'XXXXXX', '438', '43', 'Organismes sociaux - Charges à payer et produits à recevoir', 1),
(257, 'PCG99-BASE', 'TIERS', 'XXXXXX', '44', '4', 'État et autres collectivités publiques', 1),
(258, 'PCG99-BASE', 'TIERS', 'XXXXXX', '441', '44', 'État - Subventions à recevoir', 1),
(259, 'PCG99-BASE', 'TIERS', 'XXXXXX', '442', '44', 'Etat - Impôts et taxes recouvrables sur des tiers', 1),
(260, 'PCG99-BASE', 'TIERS', 'XXXXXX', '443', '44', 'Opérations particulières avec l''Etat, les collectivités publiques, les organismes internationaux', 1),
(261, 'PCG99-BASE', 'TIERS', 'XXXXXX', '444', '44', 'Etat - Impôts sur les bénéfices', 1),
(262, 'PCG99-BASE', 'TIERS', 'XXXXXX', '445', '44', 'Etat - Taxes sur le chiffre d''affaires', 1),
(263, 'PCG99-BASE', 'TIERS', 'XXXXXX', '446', '44', 'Obligations cautionnées', 1),
(264, 'PCG99-BASE', 'TIERS', 'XXXXXX', '447', '44', 'Autres impôts, taxes et versements assimilés', 1),
(265, 'PCG99-BASE', 'TIERS', 'XXXXXX', '448', '44', 'Etat - Charges à payer et produits à recevoir', 1),
(266, 'PCG99-BASE', 'TIERS', 'XXXXXX', '449', '44', 'Quotas d''émission à restituer à l''Etat', 1),
(267, 'PCG99-BASE', 'TIERS', 'XXXXXX', '45', '4', 'Groupe et associes', 1),
(268, 'PCG99-BASE', 'TIERS', 'XXXXXX', '451', '45', 'Groupe', 1),
(269, 'PCG99-BASE', 'TIERS', 'XXXXXX', '455', '45', 'Associés - Comptes courants', 1),
(270, 'PCG99-BASE', 'TIERS', 'XXXXXX', '456', '45', 'Associés - Opérations sur le capital', 1),
(271, 'PCG99-BASE', 'TIERS', 'XXXXXX', '457', '45', 'Associés - Dividendes à payer', 1),
(272, 'PCG99-BASE', 'TIERS', 'XXXXXX', '458', '45', 'Associés - Opérations faites en commun et en G.I.E.', 1),
(273, 'PCG99-BASE', 'TIERS', 'XXXXXX', '46', '4', 'Débiteurs divers et créditeurs divers', 1),
(274, 'PCG99-BASE', 'TIERS', 'XXXXXX', '462', '46', 'Créances sur cessions d''immobilisations', 1),
(275, 'PCG99-BASE', 'TIERS', 'XXXXXX', '464', '46', 'Dettes sur acquisitions de valeurs mobilières de placement', 1),
(276, 'PCG99-BASE', 'TIERS', 'XXXXXX', '465', '46', 'Créances sur cessions de valeurs mobilières de placement', 1),
(277, 'PCG99-BASE', 'TIERS', 'XXXXXX', '467', '46', 'Autres comptes débiteurs ou créditeurs', 1),
(278, 'PCG99-BASE', 'TIERS', 'XXXXXX', '468', '46', 'Divers - Charges à payer et produits à recevoir', 1),
(279, 'PCG99-BASE', 'TIERS', 'XXXXXX', '47', '4', 'Comptes transitoires ou d''attente', 1),
(280, 'PCG99-BASE', 'TIERS', 'XXXXXX', '471', '47', 'Comptes d''attente', 1),
(281, 'PCG99-BASE', 'TIERS', 'XXXXXX', '476', '47', 'Différence de conversion - Actif', 1),
(282, 'PCG99-BASE', 'TIERS', 'XXXXXX', '477', '47', 'Différences de conversion - Passif', 1),
(283, 'PCG99-BASE', 'TIERS', 'XXXXXX', '478', '47', 'Autres comptes transitoires', 1),
(284, 'PCG99-BASE', 'TIERS', 'XXXXXX', '48', '4', 'Comptes de régularisation', 1),
(285, 'PCG99-BASE', 'TIERS', 'XXXXXX', '481', '48', 'Charges à répartir sur plusieurs exercices', 1),
(286, 'PCG99-BASE', 'TIERS', 'XXXXXX', '486', '48', 'Charges constatées d''avance', 1),
(287, 'PCG99-BASE', 'TIERS', 'XXXXXX', '487', '48', 'Produits constatés d''avance', 1),
(288, 'PCG99-BASE', 'TIERS', 'XXXXXX', '488', '48', 'Comptes de répartition périodique des charges et des produits', 1),
(289, 'PCG99-BASE', 'TIERS', 'XXXXXX', '489', '48', 'Quotas d''émission alloués par l''Etat', 1),
(290, 'PCG99-BASE', 'TIERS', 'XXXXXX', '49', '4', 'Provisions pour dépréciation des comptes de tiers', 1),
(291, 'PCG99-BASE', 'TIERS', 'XXXXXX', '491', '49', 'Provisions pour dépréciation des comptes de clients', 1),
(292, 'PCG99-BASE', 'TIERS', 'XXXXXX', '495', '49', 'Provisions pour dépréciation des comptes du groupe et des associés', 1),
(293, 'PCG99-BASE', 'TIERS', 'XXXXXX', '496', '49', 'Provisions pour dépréciation des comptes de débiteurs divers', 1),
(294, 'PCG99-BASE', 'FINAN', 'XXXXXX', '50', '5', 'Valeurs mobilières de placement', 1),
(295, 'PCG99-BASE', 'FINAN', 'XXXXXX', '501', '50', 'Parts dans des entreprises liées', 1),
(296, 'PCG99-BASE', 'FINAN', 'XXXXXX', '502', '50', 'Actions propres', 1),
(297, 'PCG99-BASE', 'FINAN', 'XXXXXX', '503', '50', 'Actions', 1),
(298, 'PCG99-BASE', 'FINAN', 'XXXXXX', '504', '50', 'Autres titres conférant un droit de propriété', 1),
(299, 'PCG99-BASE', 'FINAN', 'XXXXXX', '505', '50', 'Obligations et bons émis par la société et rachetés par elle', 1),
(300, 'PCG99-BASE', 'FINAN', 'XXXXXX', '506', '50', 'Obligations', 1),
(301, 'PCG99-BASE', 'FINAN', 'XXXXXX', '507', '50', 'Bons du Trésor et bons de caisse à court terme', 1),
(302, 'PCG99-BASE', 'FINAN', 'XXXXXX', '508', '50', 'Autres valeurs mobilières de placement et autres créances assimilées', 1),
(303, 'PCG99-BASE', 'FINAN', 'XXXXXX', '509', '50', 'Versements restant à effectuer sur valeurs mobilières de placement non libérées', 1),
(304, 'PCG99-BASE', 'FINAN', 'XXXXXX', '51', '5', 'Banques, établissements financiers et assimilés', 1),
(305, 'PCG99-BASE', 'FINAN', 'XXXXXX', '511', '51', 'Valeurs à l''encaissement', 1),
(306, 'PCG99-BASE', 'FINAN', 'BANK', '512', '51', 'Banques', 1),
(307, 'PCG99-BASE', 'FINAN', 'XXXXXX', '514', '51', 'Chèques postaux', 1),
(308, 'PCG99-BASE', 'FINAN', 'XXXXXX', '515', '51', '"Caisses" du Trésor et des établissements publics', 1),
(309, 'PCG99-BASE', 'FINAN', 'XXXXXX', '516', '51', 'Sociétés de bourse', 1),
(310, 'PCG99-BASE', 'FINAN', 'XXXXXX', '517', '51', 'Autres organismes financiers', 1),
(311, 'PCG99-BASE', 'FINAN', 'XXXXXX', '518', '51', 'Intérêts courus', 1),
(312, 'PCG99-BASE', 'FINAN', 'XXXXXX', '519', '51', 'Concours bancaires courants', 1),
(313, 'PCG99-BASE', 'FINAN', 'XXXXXX', '52', '5', 'Instruments de trésorerie', 1),
(314, 'PCG99-BASE', 'FINAN', 'CASH', '53', '5', 'Caisse', 1),
(315, 'PCG99-BASE', 'FINAN', 'XXXXXX', '531', '53', 'Caisse siège social', 1),
(316, 'PCG99-BASE', 'FINAN', 'XXXXXX', '532', '53', 'Caisse succursale (ou usine) A', 1),
(317, 'PCG99-BASE', 'FINAN', 'XXXXXX', '533', '53', 'Caisse succursale (ou usine) B', 1),
(318, 'PCG99-BASE', 'FINAN', 'XXXXXX', '54', '5', 'Régies d''avance et accréditifs', 1),
(319, 'PCG99-BASE', 'FINAN', 'XXXXXX', '58', '5', 'Virements internes', 1),
(320, 'PCG99-BASE', 'FINAN', 'XXXXXX', '59', '5', 'Provisions pour dépréciation des comptes financiers', 1),
(321, 'PCG99-BASE', 'FINAN', 'XXXXXX', '590', '59', 'Provisions pour dépréciation des valeurs mobilières de placement', 1),
(322, 'PCG99-BASE', 'CHARGE', 'PRODUCT', '60', '6', 'Achats', 1),
(323, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '601', '60', 'Achats stockés - Matières premières (et fournitures)', 1),
(324, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '602', '60', 'Achats stockés - Autres approvisionnements', 1),
(325, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '603', '60', 'Variations des stocks (approvisionnements et marchandises)', 1),
(326, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '604', '60', 'Achats stockés - Matières premières (et fournitures)', 1),
(327, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '605', '60', 'Achats de matériel, équipements et travaux', 1),
(328, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '606', '60', 'Achats non stockés de matière et fournitures', 1),
(329, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '607', '60', 'Achats de marchandises', 1),
(330, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '608', '60', '(Compte réservé, le cas échéant, à la récapitulation des frais accessoires incorporés aux achats)', 1),
(331, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '609', '60', 'Rabais, remises et ristournes obtenus sur achats', 1),
(332, 'PCG99-BASE', 'CHARGE', 'SERVICE', '61', '6', 'Services extérieurs', 1),
(333, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '611', '61', 'Sous-traitance générale', 1),
(334, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '612', '61', 'Redevances de crédit-bail', 1),
(335, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '613', '61', 'Locations', 1),
(336, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '614', '61', 'Charges locatives et de copropriété', 1),
(337, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '615', '61', 'Entretien et réparations', 1),
(338, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '616', '61', 'Primes d''assurances', 1),
(339, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '617', '61', 'Etudes et recherches', 1),
(340, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '618', '61', 'Divers', 1),
(341, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '619', '61', 'Rabais, remises et ristournes obtenus sur services extérieurs', 1),
(342, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '62', '6', 'Autres services extérieurs', 1),
(343, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '621', '62', 'Personnel extérieur à l''entreprise', 1),
(344, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '622', '62', 'Rémunérations d''intermédiaires et honoraires', 1),
(345, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '623', '62', 'Publicité, publications, relations publiques', 1),
(346, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '624', '62', 'Transports de biens et transports collectifs du personnel', 1),
(347, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '625', '62', 'Déplacements, missions et réceptions', 1),
(348, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '626', '62', 'Frais postaux et de télécommunications', 1),
(349, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '627', '62', 'Services bancaires et assimilés', 1),
(350, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '628', '62', 'Divers', 1),
(351, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '629', '62', 'Rabais, remises et ristournes obtenus sur autres services extérieurs', 1),
(352, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '63', '6', 'Impôts, taxes et versements assimilés', 1),
(353, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '631', '63', 'Impôts, taxes et versements assimilés sur rémunérations (administrations des impôts)', 1),
(354, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '633', '63', 'Impôts, taxes et versements assimilés sur rémunérations (autres organismes)', 1),
(355, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '635', '63', 'Autres impôts, taxes et versements assimilés (administrations des impôts)', 1),
(356, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '637', '63', 'Autres impôts, taxes et versements assimilés (autres organismes)', 1),
(357, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '64', '6', 'Charges de personnel', 1),
(358, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '641', '64', 'Rémunérations du personnel', 1),
(359, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '644', '64', 'Rémunération du travail de l''exploitant', 1),
(360, 'PCG99-BASE', 'CHARGE', 'SOCIAL', '645', '64', 'Charges de sécurité sociale et de prévoyance', 1),
(361, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '646', '64', 'Cotisations sociales personnelles de l''exploitant', 1),
(362, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '647', '64', 'Autres charges sociales', 1),
(363, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '648', '64', 'Autres charges de personnel', 1),
(364, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '65', '6', 'Autres charges de gestion courante', 1),
(365, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '651', '65', 'Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires', 1),
(366, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '653', '65', 'Jetons de présence', 1),
(367, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '654', '65', 'Pertes sur créances irrécouvrables', 1),
(368, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '655', '65', 'Quote-part de résultat sur opérations faites en commun', 1),
(369, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '658', '65', 'Charges diverses de gestion courante', 1),
(370, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '66', '6', 'Charges financières', 1),
(371, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '661', '66', 'Charges d''intérêts', 1),
(372, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '664', '66', 'Pertes sur créances liées à des participations', 1),
(373, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '665', '66', 'Escomptes accordés', 1),
(374, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '666', '66', 'Pertes de change', 1),
(375, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '667', '66', 'Charges nettes sur cessions de valeurs mobilières de placement', 1),
(376, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '668', '66', 'Autres charges financières', 1),
(377, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '67', '6', 'Charges exceptionnelles', 1),
(378, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '671', '67', 'Charges exceptionnelles sur opérations de gestion', 1),
(379, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '672', '67', '(Compte à la disposition des entités pour enregistrer, en cours d''exercice, les charges sur exercices antérieurs)', 1),
(380, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '675', '67', 'Valeurs comptables des éléments d''actif cédés', 1),
(381, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '678', '67', 'Autres charges exceptionnelles', 1),
(382, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '68', '6', 'Dotations aux amortissements et aux provisions', 1),
(383, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '681', '68', 'Dotations aux amortissements et aux provisions - Charges d''exploitation', 1),
(384, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '686', '68', 'Dotations aux amortissements et aux provisions - Charges financières', 1),
(385, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '687', '68', 'Dotations aux amortissements et aux provisions - Charges exceptionnelles', 1),
(386, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '69', '6', 'Participation des salariés - impôts sur les bénéfices et assimiles', 1),
(387, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '691', '69', 'Participation des salariés aux résultats', 1),
(388, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '695', '69', 'Impôts sur les bénéfices', 1),
(389, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '696', '69', 'Suppléments d''impôt sur les sociétés liés aux distributions', 1),
(390, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '697', '69', 'Imposition forfaitaire annuelle des sociétés', 1),
(391, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '698', '69', 'Intégration fiscale', 1),
(392, 'PCG99-BASE', 'CHARGE', 'XXXXXX', '699', '69', 'Produits - Reports en arrière des déficits', 1),
(393, 'PCG99-BASE', 'PROD', 'XXXXXX', '70', '7', 'Ventes de produits fabriqués, prestations de services, marchandises', 1),
(394, 'PCG99-BASE', 'PROD', 'PRODUCT', '701', '70', 'Ventes de produits finis', 1),
(395, 'PCG99-BASE', 'PROD', 'XXXXXX', '702', '70', 'Ventes de produits intermédiaires', 1),
(396, 'PCG99-BASE', 'PROD', 'XXXXXX', '703', '70', 'Ventes de produits résiduels', 1),
(397, 'PCG99-BASE', 'PROD', 'XXXXXX', '704', '70', 'Travaux', 1),
(398, 'PCG99-BASE', 'PROD', 'XXXXXX', '705', '70', 'Etudes', 1),
(399, 'PCG99-BASE', 'PROD', 'SERVICE', '706', '70', 'Prestations de services', 1),
(400, 'PCG99-BASE', 'PROD', 'PRODUCT', '707', '70', 'Ventes de marchandises', 1),
(401, 'PCG99-BASE', 'PROD', 'PRODUCT', '708', '70', 'Produits des activités annexes', 1),
(402, 'PCG99-BASE', 'PROD', 'XXXXXX', '709', '70', 'Rabais, remises et ristournes accordés par l''entreprise', 1),
(403, 'PCG99-BASE', 'PROD', 'XXXXXX', '71', '7', 'Production stockée (ou déstockage)', 1),
(404, 'PCG99-BASE', 'PROD', 'XXXXXX', '713', '71', 'Variation des stocks (en-cours de production, produits)', 1),
(405, 'PCG99-BASE', 'PROD', 'XXXXXX', '72', '7', 'Production immobilisée', 1),
(406, 'PCG99-BASE', 'PROD', 'XXXXXX', '721', '72', 'Immobilisations incorporelles', 1),
(407, 'PCG99-BASE', 'PROD', 'XXXXXX', '722', '72', 'Immobilisations corporelles', 1),
(408, 'PCG99-BASE', 'PROD', 'XXXXXX', '74', '7', 'Subventions d''exploitation', 1),
(409, 'PCG99-BASE', 'PROD', 'XXXXXX', '75', '7', 'Autres produits de gestion courante', 1),
(410, 'PCG99-BASE', 'PROD', 'XXXXXX', '751', '75', 'Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires', 1),
(411, 'PCG99-BASE', 'PROD', 'XXXXXX', '752', '75', 'Revenus des immeubles non affectés à des activités professionnelles', 1),
(412, 'PCG99-BASE', 'PROD', 'XXXXXX', '753', '75', 'Jetons de présence et rémunérations d''administrateurs, gérants,...', 1),
(413, 'PCG99-BASE', 'PROD', 'XXXXXX', '754', '75', 'Ristournes perçues des coopératives (provenant des excédents)', 1),
(414, 'PCG99-BASE', 'PROD', 'XXXXXX', '755', '75', 'Quotes-parts de résultat sur opérations faites en commun', 1),
(415, 'PCG99-BASE', 'PROD', 'XXXXXX', '758', '75', 'Produits divers de gestion courante', 1),
(416, 'PCG99-BASE', 'PROD', 'XXXXXX', '76', '7', 'Produits financiers', 1),
(417, 'PCG99-BASE', 'PROD', 'XXXXXX', '761', '76', 'Produits de participations', 1),
(418, 'PCG99-BASE', 'PROD', 'XXXXXX', '762', '76', 'Produits des autres immobilisations financières', 1),
(419, 'PCG99-BASE', 'PROD', 'XXXXXX', '763', '76', 'Revenus des autres créances', 1),
(420, 'PCG99-BASE', 'PROD', 'XXXXXX', '764', '76', 'Revenus des valeurs mobilières de placement', 1),
(421, 'PCG99-BASE', 'PROD', 'XXXXXX', '765', '76', 'Escomptes obtenus', 1),
(422, 'PCG99-BASE', 'PROD', 'XXXXXX', '766', '76', 'Gains de change', 1),
(423, 'PCG99-BASE', 'PROD', 'XXXXXX', '767', '76', 'Produits nets sur cessions de valeurs mobilières de placement', 1),
(424, 'PCG99-BASE', 'PROD', 'XXXXXX', '768', '76', 'Autres produits financiers', 1),
(425, 'PCG99-BASE', 'PROD', 'XXXXXX', '77', '7', 'Produits exceptionnels', 1),
(426, 'PCG99-BASE', 'PROD', 'XXXXXX', '771', '77', 'Produits exceptionnels sur opérations de gestion', 1),
(427, 'PCG99-BASE', 'PROD', 'XXXXXX', '772', '77', '(Compte à la disposition des entités pour enregistrer, en cours d''exercice, les produits sur exercices antérieurs)', 1),
(428, 'PCG99-BASE', 'PROD', 'XXXXXX', '775', '77', 'Produits des cessions d''éléments d''actif', 1),
(429, 'PCG99-BASE', 'PROD', 'XXXXXX', '777', '77', 'Quote-part des subventions d''investissement virée au résultat de l''exercice', 1),
(430, 'PCG99-BASE', 'PROD', 'XXXXXX', '778', '77', 'Autres produits exceptionnels', 1),
(431, 'PCG99-BASE', 'PROD', 'XXXXXX', '78', '7', 'Reprises sur amortissements et provisions', 1),
(432, 'PCG99-BASE', 'PROD', 'XXXXXX', '781', '78', 'Reprises sur amortissements et provisions (à inscrire dans les produits d''exploitation)', 1),
(433, 'PCG99-BASE', 'PROD', 'XXXXXX', '786', '78', 'Reprises sur provisions pour risques (à inscrire dans les produits financiers)', 1),
(434, 'PCG99-BASE', 'PROD', 'XXXXXX', '787', '78', 'Reprises sur provisions (à inscrire dans les produits exceptionnels)', 1),
(435, 'PCG99-BASE', 'PROD', 'XXXXXX', '79', '7', 'Transferts de charges', 1),
(436, 'PCG99-BASE', 'PROD', 'XXXXXX', '791', '79', 'Transferts de charges d''exploitation ', 1),
(437, 'PCG99-BASE', 'PROD', 'XXXXXX', '796', '79', 'Transferts de charges financières', 1),
(438, 'PCG99-BASE', 'PROD', 'XXXXXX', '797', '79', 'Transferts de charges exceptionnelles', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_accountingdebcred`
--

CREATE TABLE IF NOT EXISTS `llx_accountingdebcred` (
  `fk_transaction` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `amount` double NOT NULL,
  `direction` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_accountingtransaction`
--

CREATE TABLE IF NOT EXISTS `llx_accountingtransaction` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(128) NOT NULL,
  `datec` date NOT NULL,
  `fk_author` varchar(20) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_source` int(11) NOT NULL,
  `sourcetype` varchar(16) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_accounting_system`
--

CREATE TABLE IF NOT EXISTS `llx_accounting_system` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `pcg_version` varchar(12) NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `label` varchar(128) NOT NULL,
  `active` smallint(6) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_accounting_system_pcg_version` (`pcg_version`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `llx_accounting_system`
--

INSERT INTO `llx_accounting_system` (`rowid`, `pcg_version`, `fk_pays`, `label`, `active`) VALUES
(1, 'PCG99-ABREGE', 1, 'The simple accountancy french plan', 1),
(2, 'PCG99-BASE', 1, 'The base accountancy french plan', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_actioncomm`
--

CREATE TABLE IF NOT EXISTS `llx_actioncomm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_ext` varchar(128) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datep` datetime DEFAULT NULL,
  `datep2` datetime DEFAULT NULL,
  `datea` datetime DEFAULT NULL,
  `datea2` datetime DEFAULT NULL,
  `fk_action` int(11) DEFAULT NULL,
  `label` varchar(128) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_mod` int(11) DEFAULT NULL,
  `fk_project` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_parent` int(11) NOT NULL DEFAULT '0',
  `fk_user_action` int(11) DEFAULT NULL,
  `fk_user_done` int(11) DEFAULT NULL,
  `priority` smallint(6) DEFAULT NULL,
  `fulldayevent` smallint(6) NOT NULL DEFAULT '0',
  `punctual` smallint(6) NOT NULL DEFAULT '1',
  `percent` smallint(6) NOT NULL DEFAULT '0',
  `location` varchar(128) DEFAULT NULL,
  `durationp` double DEFAULT NULL,
  `durationa` double DEFAULT NULL,
  `note` text,
  `fk_element` int(11) DEFAULT NULL,
  `elementtype` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_actioncomm_datea` (`datea`),
  KEY `idx_actioncomm_fk_soc` (`fk_soc`),
  KEY `idx_actioncomm_fk_contact` (`fk_contact`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=165 ;

--
-- Volcado de datos para la tabla `llx_actioncomm`
--

INSERT INTO `llx_actioncomm` (`id`, `ref_ext`, `entity`, `datep`, `datep2`, `datea`, `datea2`, `fk_action`, `label`, `datec`, `tms`, `fk_user_author`, `fk_user_mod`, `fk_project`, `fk_soc`, `fk_contact`, `fk_parent`, `fk_user_action`, `fk_user_done`, `priority`, `fulldayevent`, `punctual`, `percent`, `location`, `durationp`, `durationa`, `note`, `fk_element`, `elementtype`) VALUES
(1, NULL, 1, '2013-02-25 19:40:57', '2013-02-25 19:40:57', NULL, NULL, 40, 'Empresa VentasxMenor insertada en Dolibarr', '2013-02-25 19:40:57', '2013-02-25 23:40:57', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa VentasxMenor insertada en Dolibarr\nAutor: admindb', 1, 'societe'),
(2, NULL, 1, '2013-02-28 20:03:56', '2013-02-28 20:03:56', NULL, NULL, 40, 'Factura FA1302-0001 validada en Dolibarr', '2013-02-28 20:03:56', '2013-03-01 00:03:56', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1302-0001 validada en Dolibarr\nAutor: admindb', 1, 'invoice'),
(3, NULL, 1, '2013-02-28 20:03:56', '2013-02-28 20:03:56', NULL, NULL, 40, 'Factura FA1302-0001 pasada a pagada en Dolibarr', '2013-02-28 20:03:56', '2013-03-01 00:03:56', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1302-0001 pasada a pagada en Dolibarr\nAutor: admindb', 1, 'invoice'),
(4, NULL, 1, '2013-02-28 20:25:50', '2013-02-28 20:25:50', NULL, NULL, 40, 'Pedido CO1302-0001 validado', '2013-02-28 20:25:50', '2013-03-01 00:25:50', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1302-0001 validado\nAutor: admindb', 1, 'order'),
(5, NULL, 1, '2013-03-02 12:23:22', '2013-03-02 12:23:22', NULL, NULL, 40, 'Factura FA1303-0002 validada en Dolibarr', '2013-03-02 12:23:22', '2013-03-02 16:23:22', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0002 validada en Dolibarr\nAutor: admindb', 2, 'invoice'),
(6, NULL, 1, '2013-03-02 12:23:22', '2013-03-02 12:23:22', NULL, NULL, 40, 'Factura FA1303-0002 pasada a pagada en Dolibarr', '2013-03-02 12:23:22', '2013-03-02 16:23:22', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0002 pasada a pagada en Dolibarr\nAutor: admindb', 2, 'invoice'),
(7, NULL, 1, '2013-03-02 14:07:52', '2013-03-02 14:07:52', NULL, NULL, 40, 'Factura FA1303-0003 validada en Dolibarr', '2013-03-02 14:07:52', '2013-03-02 18:07:52', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0003 validada en Dolibarr\nAutor: admindb', 3, 'invoice'),
(8, NULL, 1, '2013-03-02 14:07:52', '2013-03-02 14:07:52', NULL, NULL, 40, 'Factura FA1303-0003 pasada a pagada en Dolibarr', '2013-03-02 14:07:52', '2013-03-02 18:07:52', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0003 pasada a pagada en Dolibarr\nAutor: admindb', 3, 'invoice'),
(9, NULL, 1, '2013-03-02 15:48:49', '2013-03-02 15:48:49', NULL, NULL, 40, 'Factura FA1303-0004 validada', '2013-03-02 15:48:49', '2013-03-02 19:48:49', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0004 validada\nAutor: admindb', 4, 'invoice'),
(10, NULL, 1, '2013-03-02 15:48:49', '2013-03-02 15:48:49', NULL, NULL, 40, 'Factura FA1303-0004 pasada a pagada en Dolibarr', '2013-03-02 15:48:49', '2013-03-02 19:48:49', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0004 pasada a pagada en Dolibarr\nAutor: admindb', 4, 'invoice'),
(11, NULL, 1, '2013-03-02 16:51:44', '2013-03-02 16:51:44', NULL, NULL, 40, 'Factura FA1303-0005 validada', '2013-03-02 16:51:44', '2013-03-02 20:51:44', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0005 validada\nAutor: admindb', 5, 'invoice'),
(12, NULL, 1, '2013-03-02 16:51:44', '2013-03-02 16:51:44', NULL, NULL, 40, 'Factura FA1303-0005 pasada a pagada en Dolibarr', '2013-03-02 16:51:44', '2013-03-02 20:51:44', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0005 pasada a pagada en Dolibarr\nAutor: admindb', 5, 'invoice'),
(13, NULL, 1, '2013-03-02 16:58:25', '2013-03-02 16:58:25', NULL, NULL, 40, 'Factura FA1303-0006 validada', '2013-03-02 16:58:25', '2013-03-02 20:58:25', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0006 validada\nAutor: admindb', 6, 'invoice'),
(14, NULL, 1, '2013-03-02 16:58:25', '2013-03-02 16:58:25', NULL, NULL, 40, 'Factura FA1303-0006 pasada a pagada en Dolibarr', '2013-03-02 16:58:25', '2013-03-02 20:58:25', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0006 pasada a pagada en Dolibarr\nAutor: admindb', 6, 'invoice'),
(15, NULL, 1, '2013-03-02 16:58:59', '2013-03-02 16:58:59', NULL, NULL, 40, 'Factura FA1303-0006 pasada a pagada en Dolibarr', '2013-03-02 16:58:59', '2013-03-02 20:58:59', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0006 pasada a pagada en Dolibarr\nAutor: admindb', 6, 'invoice'),
(16, NULL, 1, '2013-03-02 16:59:44', '2013-03-02 16:59:44', NULL, NULL, 40, 'Factura FA1303-0007 validada', '2013-03-02 16:59:44', '2013-03-02 20:59:44', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0007 validada\nAutor: admindb', 7, 'invoice'),
(17, NULL, 1, '2013-03-02 16:59:44', '2013-03-02 16:59:44', NULL, NULL, 40, 'Factura FA1303-0007 pasada a pagada en Dolibarr', '2013-03-02 16:59:44', '2013-03-02 20:59:44', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0007 pasada a pagada en Dolibarr\nAutor: admindb', 7, 'invoice'),
(18, NULL, 1, '2013-03-02 17:01:38', '2013-03-02 17:01:38', NULL, NULL, 40, 'Factura FA1303-0008 validada', '2013-03-02 17:01:38', '2013-03-02 21:01:38', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0008 validada\nAutor: admindb', 8, 'invoice'),
(19, NULL, 1, '2013-03-02 17:01:38', '2013-03-02 17:01:38', NULL, NULL, 40, 'Factura FA1303-0008 pasada a pagada en Dolibarr', '2013-03-02 17:01:38', '2013-03-02 21:01:38', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0008 pasada a pagada en Dolibarr\nAutor: admindb', 8, 'invoice'),
(20, NULL, 1, '2013-03-02 17:10:14', '2013-03-02 17:10:14', NULL, NULL, 40, 'Factura FA1303-0009 validada', '2013-03-02 17:10:14', '2013-03-02 21:10:14', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0009 validada\nAutor: admindb', 9, 'invoice'),
(21, NULL, 1, '2013-03-02 17:10:14', '2013-03-02 17:10:14', NULL, NULL, 40, 'Factura FA1303-0009 pasada a pagada en Dolibarr', '2013-03-02 17:10:14', '2013-03-02 21:10:14', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0009 pasada a pagada en Dolibarr\nAutor: admindb', 9, 'invoice'),
(22, NULL, 1, '2013-03-02 17:14:56', '2013-03-02 17:14:56', NULL, NULL, 40, 'Factura FA1303-0010 validada', '2013-03-02 17:14:56', '2013-03-02 21:14:56', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0010 validada\nAutor: admindb', 10, 'invoice'),
(23, NULL, 1, '2013-03-02 17:14:56', '2013-03-02 17:14:56', NULL, NULL, 40, 'Factura FA1303-0010 pasada a pagada en Dolibarr', '2013-03-02 17:14:56', '2013-03-02 21:14:56', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0010 pasada a pagada en Dolibarr\nAutor: admindb', 10, 'invoice'),
(24, NULL, 1, '2013-03-04 16:25:49', '2013-03-04 16:25:49', NULL, NULL, 40, 'Factura FA1303-0011 validada', '2013-03-04 16:25:49', '2013-03-04 20:25:49', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0011 validada\nAutor: admindb', 11, 'invoice'),
(25, NULL, 1, '2013-03-04 16:25:49', '2013-03-04 16:25:49', NULL, NULL, 40, 'Factura FA1303-0011 pasada a pagada en Dolibarr', '2013-03-04 16:25:49', '2013-03-04 20:25:49', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0011 pasada a pagada en Dolibarr\nAutor: admindb', 11, 'invoice'),
(26, NULL, 1, '2013-03-05 09:19:13', '2013-03-05 09:19:13', NULL, NULL, 40, 'Empresa Trans America insertada en Dolibarr', '2013-03-05 09:19:13', '2013-03-05 13:19:13', 1, NULL, NULL, 2, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Trans America insertada en Dolibarr\nAutor: admindb', 2, 'societe'),
(27, NULL, 1, '2013-03-05 09:43:18', '2013-03-05 09:43:18', NULL, NULL, 40, 'Empresa Jose Luis Mariaca insertada en Dolibarr', '2013-03-05 09:43:18', '2013-03-05 13:43:18', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Jose Luis Mariaca insertada en Dolibarr\nAutor: admindb', 3, 'societe'),
(28, NULL, 1, '2013-03-05 22:27:23', '2013-03-05 22:27:23', NULL, NULL, 40, 'Factura FA1303-0012 validada', '2013-03-05 22:27:23', '2013-03-06 02:27:23', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0012 validada\nAutor: admindb', 12, 'invoice'),
(29, NULL, 1, '2013-03-05 22:27:23', '2013-03-05 22:27:23', NULL, NULL, 40, 'Factura FA1303-0012 pasada a pagada en Dolibarr', '2013-03-05 22:27:23', '2013-03-06 02:27:23', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0012 pasada a pagada en Dolibarr\nAutor: admindb', 12, 'invoice'),
(30, NULL, 1, '2013-03-05 22:45:14', '2013-03-05 22:45:14', NULL, NULL, 40, 'Pedido CO1302-0001 validado', '2013-03-05 22:45:14', '2013-03-06 02:45:14', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1302-0001 validado\nAutor: admindb', 1, 'order'),
(31, NULL, 1, '2013-03-05 23:16:24', '2013-03-05 23:16:24', NULL, NULL, 40, 'Factura FA1303-0013 validada', '2013-03-05 23:16:24', '2013-03-06 03:16:24', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0013 validada\nAutor: admindb', 13, 'invoice'),
(32, NULL, 1, '2013-03-09 12:00:52', '2013-03-09 12:00:52', NULL, NULL, 40, 'Factura FA1303-0014 validada', '2013-03-09 12:00:52', '2013-03-09 16:00:52', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0014 validada\nAutor: admindb', 14, 'invoice'),
(33, NULL, 1, '2013-03-09 12:00:52', '2013-03-09 12:00:52', NULL, NULL, 40, 'Factura FA1303-0014 pasada a pagada en Dolibarr', '2013-03-09 12:00:52', '2013-03-09 16:00:52', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1303-0014 pasada a pagada en Dolibarr\nAutor: admindb', 14, 'invoice'),
(34, NULL, 1, '2013-03-16 09:56:19', '2013-03-16 09:56:19', NULL, NULL, 40, 'Empresa Pedro Mendez insertada en Dolibarr', '2013-03-16 09:56:19', '2013-03-16 13:56:19', 1, NULL, NULL, 4, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Pedro Mendez insertada en Dolibarr\nAutor: admindb', 4, 'societe'),
(35, NULL, 1, '2013-03-16 09:57:31', '2013-03-16 09:57:31', NULL, NULL, 40, 'Pedido CO1303-0002 validado', '2013-03-16 09:57:31', '2013-03-16 13:57:31', 1, NULL, NULL, 4, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1303-0002 validado\nAutor: admindb', 6, 'order'),
(36, NULL, 1, '2013-03-16 11:44:27', '2013-03-16 11:44:27', NULL, NULL, 40, 'Pedido CO1303-0003 validado', '2013-03-16 11:44:27', '2013-03-16 15:44:27', 1, NULL, NULL, 4, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1303-0003 validado\nAutor: admindb', 7, 'order'),
(37, NULL, 1, '2013-03-16 17:01:23', '2013-03-16 17:01:23', NULL, NULL, 40, 'Pedido CO1303-0004 validado', '2013-03-16 17:01:23', '2013-03-16 21:01:23', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1303-0004 validado\nAutor: admindb', 8, 'order'),
(38, NULL, 1, '2013-03-23 09:56:49', '2013-03-23 09:56:49', NULL, NULL, 40, 'Presupuesto PR1303-0001 validado', '2013-03-23 09:56:49', '2013-03-23 13:56:49', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Presupuesto PR1303-0001 validado\nAutor: admindb', 1, 'propal'),
(39, NULL, 1, '2013-04-06 10:03:55', '2013-04-06 10:03:55', NULL, NULL, 40, 'Pedido CO1304-0005 validado', '2013-04-06 10:03:55', '2013-04-06 14:03:55', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1304-0005 validado\nAutor: admindb', 9, 'order'),
(40, NULL, 1, '2013-04-06 12:43:22', '2013-04-06 12:43:22', NULL, NULL, 40, 'Empresa quimica montes insertada en Dolibarr', '2013-04-06 12:43:22', '2013-04-06 16:43:22', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa quimica montes insertada en Dolibarr\nAutor: admindb', 5, 'societe'),
(41, NULL, 1, '2013-04-06 12:44:27', '2013-04-06 12:44:27', NULL, NULL, 40, 'Pedido CF1304-0001 validado', '2013-04-06 12:44:27', '2013-04-06 16:44:27', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0001 validado\nAutor: admindb', 1, 'order_supplier'),
(42, NULL, 1, '2013-04-06 12:48:48', '2013-04-06 12:48:48', NULL, NULL, 40, 'Pedido CF1304-0002 validado', '2013-04-06 12:48:48', '2013-04-06 16:48:48', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0002 validado\nAutor: admindb', 2, 'order_supplier'),
(43, NULL, 1, '2013-04-06 12:50:10', '2013-04-06 12:50:10', NULL, NULL, 40, 'Factura 1 validada en Dolibarr', '2013-04-06 12:50:10', '2013-04-06 16:50:10', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 1 validada en Dolibarr\nAutor: admindb', 1, 'invoice_supplier'),
(44, NULL, 1, '2013-04-06 12:53:20', '2013-04-06 12:53:20', NULL, NULL, 40, 'Factura 2 validada en Dolibarr', '2013-04-06 12:53:20', '2013-04-06 16:53:20', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 2 validada en Dolibarr\nAutor: admindb', 2, 'invoice_supplier'),
(45, NULL, 1, '2013-04-06 12:58:07', '2013-04-06 12:58:07', NULL, NULL, 40, 'Pedido CF1304-0003 validado', '2013-04-06 12:58:07', '2013-04-06 16:58:07', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0003 validado\nAutor: admindb', 4, 'order_supplier'),
(46, NULL, 1, '2013-04-06 12:59:11', '2013-04-06 12:59:11', NULL, NULL, 40, 'Factura 3 validada en Dolibarr', '2013-04-06 12:59:11', '2013-04-06 16:59:11', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 3 validada en Dolibarr\nAutor: admindb', 3, 'invoice_supplier'),
(47, NULL, 1, '2013-04-06 12:59:41', '2013-04-06 12:59:41', NULL, NULL, 40, 'Factura 4 validada en Dolibarr', '2013-04-06 12:59:41', '2013-04-06 16:59:41', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 4 validada en Dolibarr\nAutor: admindb', 4, 'invoice_supplier'),
(48, NULL, 1, '2013-04-06 13:04:39', '2013-04-06 13:04:39', NULL, NULL, 40, 'Factura 5 validada', '2013-04-06 13:04:39', '2013-04-06 17:04:39', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 5 validada\nAutor: admindb', 5, 'invoice_supplier'),
(49, NULL, 1, '2013-04-10 19:53:51', '2013-04-10 19:53:51', NULL, NULL, 40, 'Pedido CO1304-0006 validado', '2013-04-10 19:53:51', '2013-04-10 23:53:51', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1304-0006 validado\nAutor: admindb', 10, 'order'),
(50, NULL, 1, '2013-04-10 21:10:55', '2013-04-10 21:10:55', NULL, NULL, 40, 'Pedido CO1304-0007 validado', '2013-04-10 21:10:55', '2013-04-11 01:10:55', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1304-0007 validado\nAutor: admindb', 11, 'order'),
(51, NULL, 1, '2013-04-10 21:29:21', '2013-04-10 21:29:21', NULL, NULL, 40, 'Factura FA1304-0015 validada', '2013-04-10 21:29:21', '2013-04-11 01:29:21', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0015 validada\nAutor: admindb', 15, 'invoice'),
(52, NULL, 1, '2013-04-10 21:29:21', '2013-04-10 21:29:21', NULL, NULL, 40, 'Factura FA1304-0015 pasada a pagada en Dolibarr', '2013-04-10 21:29:21', '2013-04-11 01:29:21', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0015 pasada a pagada en Dolibarr\nAutor: admindb', 15, 'invoice'),
(53, NULL, 1, '2013-04-11 19:05:17', '2013-04-11 19:05:17', NULL, NULL, 40, 'Factura (PROV16) pasada a pagada en Dolibarr', '2013-04-11 19:05:17', '2013-04-11 23:05:17', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura (PROV16) pasada a pagada en Dolibarr\nAutor: monicamamani', 16, 'invoice'),
(54, NULL, 1, '2013-04-13 10:17:47', '2013-04-13 10:17:47', NULL, NULL, 40, 'Factura FA1304-0016 validada', '2013-04-13 10:17:47', '2013-04-13 14:17:47', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0016 validada\nAutor: admindb', 17, 'invoice'),
(55, NULL, 1, '2013-04-13 10:17:47', '2013-04-13 10:17:47', NULL, NULL, 40, 'Factura FA1304-0016 pasada a pagada en Dolibarr', '2013-04-13 10:17:47', '2013-04-13 14:17:47', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0016 pasada a pagada en Dolibarr\nAutor: admindb', 17, 'invoice'),
(56, NULL, 1, '2013-04-13 10:20:12', '2013-04-13 10:20:12', NULL, NULL, 40, 'Factura FA1304-0017 validada', '2013-04-13 10:20:12', '2013-04-13 14:20:12', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0017 validada\nAutor: admindb', 18, 'invoice'),
(57, NULL, 1, '2013-04-13 10:20:12', '2013-04-13 10:20:12', NULL, NULL, 40, 'Factura FA1304-0017 pasada a pagada en Dolibarr', '2013-04-13 10:20:12', '2013-04-13 14:20:12', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0017 pasada a pagada en Dolibarr\nAutor: admindb', 18, 'invoice'),
(58, NULL, 1, '2013-04-13 10:54:13', '2013-04-13 10:54:13', NULL, NULL, 40, 'Factura FA1304-0018 validada', '2013-04-13 10:54:13', '2013-04-13 14:54:13', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0018 validada\nAutor: monicamamani', 19, 'invoice'),
(59, NULL, 1, '2013-04-13 10:54:13', '2013-04-13 10:54:13', NULL, NULL, 40, 'Factura FA1304-0018 pasada a pagada en Dolibarr', '2013-04-13 10:54:13', '2013-04-13 14:54:13', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0018 pasada a pagada en Dolibarr\nAutor: monicamamani', 19, 'invoice'),
(60, NULL, 1, '2013-04-13 10:57:11', '2013-04-13 10:57:11', NULL, NULL, 40, 'Factura FA1304-0019 validada', '2013-04-13 10:57:11', '2013-04-13 14:57:11', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0019 validada\nAutor: monicamamani', 20, 'invoice'),
(61, NULL, 1, '2013-04-13 10:57:11', '2013-04-13 10:57:11', NULL, NULL, 40, 'Factura FA1304-0019 pasada a pagada en Dolibarr', '2013-04-13 10:57:11', '2013-04-13 14:57:11', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0019 pasada a pagada en Dolibarr\nAutor: monicamamani', 20, 'invoice'),
(62, NULL, 1, '2013-04-13 10:57:30', '2013-04-13 10:57:30', NULL, NULL, 40, 'Factura FA1304-0020 validada', '2013-04-13 10:57:30', '2013-04-13 14:57:30', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0020 validada\nAutor: monicamamani', 21, 'invoice'),
(63, NULL, 1, '2013-04-13 10:57:30', '2013-04-13 10:57:30', NULL, NULL, 40, 'Factura FA1304-0020 pasada a pagada en Dolibarr', '2013-04-13 10:57:30', '2013-04-13 14:57:30', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0020 pasada a pagada en Dolibarr\nAutor: monicamamani', 21, 'invoice'),
(64, NULL, 1, '2013-04-13 12:08:29', '2013-04-13 12:08:29', NULL, NULL, 40, 'Factura FA1304-0021 validada', '2013-04-13 12:08:29', '2013-04-13 16:08:29', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0021 validada\nAutor: monicamamani', 22, 'invoice'),
(65, NULL, 1, '2013-04-13 12:08:29', '2013-04-13 12:08:29', NULL, NULL, 40, 'Factura FA1304-0021 pasada a pagada en Dolibarr', '2013-04-13 12:08:29', '2013-04-13 16:08:29', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0021 pasada a pagada en Dolibarr\nAutor: monicamamani', 22, 'invoice'),
(66, NULL, 1, '2013-04-13 12:09:16', '2013-04-13 12:09:16', NULL, NULL, 40, 'Factura FA1304-0022 validada', '2013-04-13 12:09:16', '2013-04-13 16:09:16', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0022 validada\nAutor: monicamamani', 23, 'invoice'),
(67, NULL, 1, '2013-04-13 12:09:16', '2013-04-13 12:09:16', NULL, NULL, 40, 'Factura FA1304-0022 pasada a pagada en Dolibarr', '2013-04-13 12:09:16', '2013-04-13 16:09:16', 12, NULL, NULL, 1, NULL, 0, NULL, 12, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0022 pasada a pagada en Dolibarr\nAutor: monicamamani', 23, 'invoice'),
(68, NULL, 1, '2013-04-13 14:11:07', '2013-04-13 14:11:07', NULL, NULL, 40, 'Factura FA1304-0023 validada', '2013-04-13 14:11:07', '2013-04-13 18:11:07', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0023 validada\nAutor: admindb', 24, 'invoice'),
(69, NULL, 1, '2013-04-13 14:11:07', '2013-04-13 14:11:07', NULL, NULL, 40, 'Factura FA1304-0023 pasada a pagada en Dolibarr', '2013-04-13 14:11:07', '2013-04-13 18:11:07', 1, NULL, NULL, 3, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0023 pasada a pagada en Dolibarr\nAutor: admindb', 24, 'invoice'),
(70, NULL, 1, '2013-04-15 19:13:24', '2013-04-15 19:13:24', NULL, NULL, 40, 'Empresa don joseluis insertada en Dolibarr', '2013-04-15 19:13:24', '2013-04-15 23:13:24', 3, NULL, NULL, 6, NULL, 0, NULL, 3, 0, 0, 1, -1, '', NULL, NULL, 'Empresa don joseluis insertada en Dolibarr\nAutor: alemercado', 6, 'societe'),
(71, NULL, 1, '2013-04-15 19:14:40', '2013-04-15 19:14:40', NULL, NULL, 40, 'Pedido CO1304-0008 validado', '2013-04-15 19:14:40', '2013-04-15 23:14:40', 3, NULL, NULL, 6, NULL, 0, NULL, 3, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1304-0008 validado\nAutor: alemercado', 12, 'order'),
(72, NULL, 1, '2013-04-15 19:26:59', '2013-04-15 19:26:59', NULL, NULL, 40, 'Factura FA1304-0024 validada', '2013-04-15 19:26:59', '2013-04-15 23:26:59', 3, NULL, NULL, 3, NULL, 0, NULL, 3, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0024 validada\nAutor: alemercado', 25, 'invoice'),
(73, NULL, 1, '2013-04-15 19:26:59', '2013-04-15 19:26:59', NULL, NULL, 40, 'Factura FA1304-0024 pasada a pagada en Dolibarr', '2013-04-15 19:26:59', '2013-04-15 23:26:59', 3, NULL, NULL, 3, NULL, 0, NULL, 3, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1304-0024 pasada a pagada en Dolibarr\nAutor: alemercado', 25, 'invoice'),
(74, NULL, 1, '2013-04-18 12:18:21', '2013-04-18 12:18:21', NULL, NULL, 40, 'Pedido CF1304-0004 validado', '2013-04-18 12:18:21', '2013-04-18 16:18:21', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0004 validado\nAutor: admindb', 7, 'order_supplier'),
(75, NULL, 1, '2013-04-18 20:00:14', '2013-04-18 20:00:14', NULL, NULL, 40, 'Empresa proveedor generico insertada en Dolibarr', '2013-04-18 20:00:14', '2013-04-19 00:00:14', 1, NULL, NULL, 7, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa proveedor generico insertada en Dolibarr\nAutor: admindb', 7, 'societe'),
(76, NULL, 1, '2013-04-19 14:12:17', '2013-04-19 14:12:17', NULL, NULL, 40, 'Pedido CF1304-0005 validado', '2013-04-19 14:12:17', '2013-04-19 18:12:17', 1, NULL, NULL, 7, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0005 validado\nAutor: admindb', 11, 'order_supplier'),
(77, NULL, 1, '2013-04-19 14:52:03', '2013-04-19 14:52:03', NULL, NULL, 40, 'Pedido CF1304-0006 validado', '2013-04-19 14:52:03', '2013-04-19 18:52:03', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0006 validado\nAutor: admindb', 12, 'order_supplier'),
(78, NULL, 1, '2013-04-19 14:58:34', '2013-04-19 14:58:34', NULL, NULL, 40, 'Pedido CF1304-0007 validado', '2013-04-19 14:58:34', '2013-04-19 18:58:34', 1, NULL, NULL, 7, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0007 validado\nAutor: admindb', 13, 'order_supplier'),
(79, NULL, 1, '2013-05-01 12:05:00', '2013-05-01 12:05:00', NULL, NULL, 40, 'Empresa cliente generico insertada en Dolibarr', '2013-05-01 12:05:00', '2013-05-01 16:05:00', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa cliente generico insertada en Dolibarr\nAutor: admindb', 8, 'societe'),
(80, NULL, 1, '2013-05-04 09:29:58', '2013-05-04 09:29:58', NULL, NULL, 40, 'Factura (PROV26) pasada a pagada en Dolibarr', '2013-05-04 09:29:58', '2013-05-04 13:29:58', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura (PROV26) pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 26, 'invoice'),
(81, NULL, 1, '2013-05-04 09:36:20', '2013-05-04 09:36:20', NULL, NULL, 40, 'Factura FA1305-0025 validada', '2013-05-04 09:36:20', '2013-05-04 13:36:20', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0025 validada\nAutor: admindb', 27, 'invoice'),
(82, NULL, 1, '2013-05-04 09:36:20', '2013-05-04 09:36:20', NULL, NULL, 40, 'Factura FA1305-0025 pasada a pagada en Dolibarr', '2013-05-04 09:36:20', '2013-05-04 13:36:20', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0025 pasada a pagada en Dolibarr\nAutor: admindb', 27, 'invoice'),
(83, NULL, 1, '2013-05-04 09:41:14', '2013-05-04 09:41:14', NULL, NULL, 40, 'Factura FA1305-0026 validada', '2013-05-04 09:41:14', '2013-05-04 13:41:14', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0026 validada\nAutor: admindb', 28, 'invoice'),
(84, NULL, 1, '2013-05-04 09:41:14', '2013-05-04 09:41:14', NULL, NULL, 40, 'Factura FA1305-0026 pasada a pagada en Dolibarr', '2013-05-04 09:41:14', '2013-05-04 13:41:14', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0026 pasada a pagada en Dolibarr\nAutor: admindb', 28, 'invoice'),
(85, NULL, 1, '2013-05-04 09:49:22', '2013-05-04 09:49:22', NULL, NULL, 40, 'Factura FA1305-0027 validada', '2013-05-04 09:49:22', '2013-05-04 13:49:22', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0027 validada\nAutor: cajeraaspiazu', 29, 'invoice'),
(86, NULL, 1, '2013-05-04 09:49:22', '2013-05-04 09:49:22', NULL, NULL, 40, 'Factura FA1305-0027 pasada a pagada en Dolibarr', '2013-05-04 09:49:22', '2013-05-04 13:49:22', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0027 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 29, 'invoice'),
(87, NULL, 1, '2013-05-04 09:51:31', '2013-05-04 09:51:31', NULL, NULL, 40, 'Factura FA1305-0028 validada', '2013-05-04 09:51:31', '2013-05-04 13:51:31', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0028 validada\nAutor: cajeraaspiazu', 30, 'invoice'),
(88, NULL, 1, '2013-05-04 09:51:31', '2013-05-04 09:51:31', NULL, NULL, 40, 'Factura FA1305-0028 pasada a pagada en Dolibarr', '2013-05-04 09:51:31', '2013-05-04 13:51:31', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0028 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 30, 'invoice'),
(89, NULL, 1, '2013-05-04 09:52:37', '2013-05-04 09:52:37', NULL, NULL, 40, 'Factura FA1305-0029 validada', '2013-05-04 09:52:37', '2013-05-04 13:52:37', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0029 validada\nAutor: cajeraaspiazu', 31, 'invoice'),
(90, NULL, 1, '2013-05-04 09:52:37', '2013-05-04 09:52:37', NULL, NULL, 40, 'Factura FA1305-0029 pasada a pagada en Dolibarr', '2013-05-04 09:52:37', '2013-05-04 13:52:37', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0029 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 31, 'invoice'),
(91, NULL, 1, '2013-05-04 09:59:01', '2013-05-04 09:59:01', NULL, NULL, 40, 'Factura (PROV26) pasada a pagada en Dolibarr', '2013-05-04 09:59:01', '2013-05-04 13:59:01', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura (PROV26) pasada a pagada en Dolibarr\nAutor: admindb', 26, 'invoice'),
(92, NULL, 1, '2013-05-04 11:18:09', '2013-05-04 11:18:09', NULL, NULL, 40, 'Factura FA1305-0030 validada', '2013-05-04 11:18:09', '2013-05-04 15:18:09', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0030 validada\nAutor: admindb', 32, 'invoice'),
(93, NULL, 1, '2013-05-04 11:18:09', '2013-05-04 11:18:09', NULL, NULL, 40, 'Factura FA1305-0030 pasada a pagada en Dolibarr', '2013-05-04 11:18:09', '2013-05-04 15:18:09', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0030 pasada a pagada en Dolibarr\nAutor: admindb', 32, 'invoice'),
(94, NULL, 1, '2013-05-04 11:21:39', '2013-05-04 11:21:39', NULL, NULL, 40, 'Factura FA1305-0031 validada', '2013-05-04 11:21:39', '2013-05-04 15:21:39', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0031 validada\nAutor: cajeraanexo', 33, 'invoice'),
(95, NULL, 1, '2013-05-04 11:21:39', '2013-05-04 11:21:39', NULL, NULL, 40, 'Factura FA1305-0031 pasada a pagada en Dolibarr', '2013-05-04 11:21:39', '2013-05-04 15:21:39', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0031 pasada a pagada en Dolibarr\nAutor: cajeraanexo', 33, 'invoice'),
(96, NULL, 1, '2013-05-04 11:22:07', '2013-05-04 11:22:07', NULL, NULL, 40, 'Factura FA1305-0032 validada', '2013-05-04 11:22:07', '2013-05-04 15:22:07', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0032 validada\nAutor: cajeraanexo', 34, 'invoice'),
(97, NULL, 1, '2013-05-04 11:22:07', '2013-05-04 11:22:07', NULL, NULL, 40, 'Factura FA1305-0032 pasada a pagada en Dolibarr', '2013-05-04 11:22:07', '2013-05-04 15:22:07', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0032 pasada a pagada en Dolibarr\nAutor: cajeraanexo', 34, 'invoice'),
(98, NULL, 1, '2013-05-04 11:57:41', '2013-05-04 11:57:41', NULL, NULL, 40, 'Factura FA1305-0033 validada', '2013-05-04 11:57:41', '2013-05-04 15:57:41', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0033 validada\nAutor: cajeraanexo', 35, 'invoice'),
(99, NULL, 1, '2013-05-04 11:57:41', '2013-05-04 11:57:41', NULL, NULL, 40, 'Factura FA1305-0033 pasada a pagada en Dolibarr', '2013-05-04 11:57:41', '2013-05-04 15:57:41', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0033 pasada a pagada en Dolibarr\nAutor: cajeraanexo', 35, 'invoice'),
(100, NULL, 1, '2013-05-04 12:08:38', '2013-05-04 12:08:38', NULL, NULL, 40, 'Factura FA1305-0034 validada', '2013-05-04 12:08:38', '2013-05-04 16:08:38', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0034 validada\nAutor: cajeraanexo', 36, 'invoice'),
(101, NULL, 1, '2013-05-04 12:08:39', '2013-05-04 12:08:39', NULL, NULL, 40, 'Factura FA1305-0034 pasada a pagada en Dolibarr', '2013-05-04 12:08:39', '2013-05-04 16:08:39', 6, NULL, NULL, 1, NULL, 0, NULL, 6, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0034 pasada a pagada en Dolibarr\nAutor: cajeraanexo', 36, 'invoice'),
(102, NULL, 1, '2013-05-04 20:25:28', '2013-05-04 20:25:28', NULL, NULL, 40, 'Factura FA1305-0035 validada', '2013-05-04 20:25:28', '2013-05-05 00:25:28', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0035 validada\nAutor: admindb', 37, 'invoice'),
(103, NULL, 1, '2013-05-04 20:25:28', '2013-05-04 20:25:28', NULL, NULL, 40, 'Factura FA1305-0035 pasada a pagada en Dolibarr', '2013-05-04 20:25:28', '2013-05-05 00:25:28', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0035 pasada a pagada en Dolibarr\nAutor: admindb', 37, 'invoice'),
(104, NULL, 1, '2013-05-04 20:28:11', '2013-05-04 20:28:11', NULL, NULL, 40, 'Factura FA1305-0036 validada', '2013-05-04 20:28:11', '2013-05-05 00:28:11', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0036 validada\nAutor: admindb', 38, 'invoice'),
(105, NULL, 1, '2013-05-04 20:28:11', '2013-05-04 20:28:11', NULL, NULL, 40, 'Factura FA1305-0036 pasada a pagada en Dolibarr', '2013-05-04 20:28:11', '2013-05-05 00:28:11', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0036 pasada a pagada en Dolibarr\nAutor: admindb', 38, 'invoice'),
(106, NULL, 1, '2013-05-04 20:38:03', '2013-05-04 20:38:03', NULL, NULL, 40, 'Factura FA1305-0037 validada', '2013-05-04 20:38:03', '2013-05-05 00:38:03', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0037 validada\nAutor: admindb', 39, 'invoice'),
(107, NULL, 1, '2013-05-04 20:38:03', '2013-05-04 20:38:03', NULL, NULL, 40, 'Factura FA1305-0037 pasada a pagada en Dolibarr', '2013-05-04 20:38:03', '2013-05-05 00:38:03', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0037 pasada a pagada en Dolibarr\nAutor: admindb', 39, 'invoice'),
(108, NULL, 1, '2013-05-14 14:39:05', '2013-05-14 14:39:05', NULL, NULL, 40, 'Empresa Jorge Rada insertada en Dolibarr', '2013-05-14 14:39:05', '2013-05-14 18:39:05', 1, NULL, NULL, 71, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Jorge Rada insertada en Dolibarr\nAutor: admindb', 71, 'societe'),
(109, NULL, 1, '2013-05-14 14:53:11', '2013-05-14 14:53:11', NULL, NULL, 40, 'Pedido  validado', '2013-05-14 14:53:11', '2013-05-14 18:53:11', 1, NULL, NULL, 71, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido  validado\nAutor: admindb', 14, 'order'),
(110, NULL, 1, '2013-05-14 15:11:59', '2013-05-14 15:11:59', NULL, NULL, 40, 'Pedido CO1305-0009 validado', '2013-05-14 15:11:59', '2013-05-14 19:11:59', 1, NULL, NULL, 71, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0009 validado\nAutor: admindb', 15, 'order'),
(111, NULL, 1, '2013-05-15 12:04:48', '2013-05-15 12:04:48', NULL, NULL, 40, 'Empresa Carlos Mendizabal insertada en Dolibarr', '2013-05-15 12:04:48', '2013-05-15 16:04:48', 1, NULL, NULL, 72, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Carlos Mendizabal insertada en Dolibarr\nAutor: admindb', 72, 'societe'),
(112, NULL, 1, '2013-05-15 12:06:00', '2013-05-15 12:06:00', NULL, NULL, 40, 'Pedido CO1305-0010 validado', '2013-05-15 12:06:00', '2013-05-15 16:06:00', 1, NULL, NULL, 72, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0010 validado\nAutor: admindb', 16, 'order'),
(113, NULL, 1, '2013-05-15 14:11:25', '2013-05-15 14:11:25', NULL, NULL, 40, 'Pedido CO1305-0011 validado', '2013-05-15 14:11:25', '2013-05-15 18:11:25', 1, NULL, NULL, 47, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0011 validado\nAutor: admindb', 17, 'order'),
(114, NULL, 1, '2013-05-16 12:08:36', '2013-05-16 12:08:36', NULL, NULL, 40, 'Pedido CO1305-0012 validado', '2013-05-16 12:08:36', '2013-05-16 16:08:36', 1, NULL, NULL, 40, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0012 validado\nAutor: admindb', 18, 'order'),
(115, NULL, 1, '2013-05-18 13:21:52', '2013-05-18 13:21:52', NULL, NULL, 40, 'Factura (PROV40) pasada a pagada en Dolibarr', '2013-05-18 13:21:52', '2013-05-18 17:21:53', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura (PROV40) pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 40, 'invoice'),
(116, NULL, 1, '2013-05-18 13:22:16', '2013-05-18 13:22:16', NULL, NULL, 40, 'Factura (PROV41) pasada a pagada en Dolibarr', '2013-05-18 13:22:16', '2013-05-18 17:22:16', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura (PROV41) pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 41, 'invoice'),
(117, NULL, 1, '2013-05-18 13:23:25', '2013-05-18 13:23:25', NULL, NULL, 40, 'Factura FA1305-0038 validada', '2013-05-18 13:23:25', '2013-05-18 17:23:25', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0038 validada\nAutor: admindb', 42, 'invoice'),
(118, NULL, 1, '2013-05-18 13:23:25', '2013-05-18 13:23:25', NULL, NULL, 40, 'Factura FA1305-0038 pasada a pagada en Dolibarr', '2013-05-18 13:23:25', '2013-05-18 17:23:25', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0038 pasada a pagada en Dolibarr\nAutor: admindb', 42, 'invoice'),
(119, NULL, 1, '2013-05-18 13:24:23', '2013-05-18 13:24:23', NULL, NULL, 40, 'Factura FA1305-0039 validada', '2013-05-18 13:24:23', '2013-05-18 17:24:23', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0039 validada\nAutor: admindb', 43, 'invoice'),
(120, NULL, 1, '2013-05-18 13:24:23', '2013-05-18 13:24:23', NULL, NULL, 40, 'Factura FA1305-0039 pasada a pagada en Dolibarr', '2013-05-18 13:24:23', '2013-05-18 17:24:23', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0039 pasada a pagada en Dolibarr\nAutor: admindb', 43, 'invoice'),
(121, NULL, 1, '2013-05-18 13:25:25', '2013-05-18 13:25:25', NULL, NULL, 40, 'Factura FA1305-0040 validada', '2013-05-18 13:25:25', '2013-05-18 17:25:25', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0040 validada\nAutor: admindb', 44, 'invoice'),
(122, NULL, 1, '2013-05-18 13:25:25', '2013-05-18 13:25:25', NULL, NULL, 40, 'Factura FA1305-0040 pasada a pagada en Dolibarr', '2013-05-18 13:25:25', '2013-05-18 17:25:25', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0040 pasada a pagada en Dolibarr\nAutor: admindb', 44, 'invoice'),
(123, NULL, 1, '2013-05-18 13:27:21', '2013-05-18 13:27:21', NULL, NULL, 40, 'Factura FA1305-0041 validada', '2013-05-18 13:27:21', '2013-05-18 17:27:21', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0041 validada\nAutor: cajeraaspiazu', 45, 'invoice'),
(124, NULL, 1, '2013-05-18 13:27:21', '2013-05-18 13:27:21', NULL, NULL, 40, 'Factura FA1305-0041 pasada a pagada en Dolibarr', '2013-05-18 13:27:21', '2013-05-18 17:27:21', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0041 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 45, 'invoice'),
(125, NULL, 1, '2013-05-18 13:27:38', '2013-05-18 13:27:38', NULL, NULL, 40, 'Factura FA1305-0042 validada', '2013-05-18 13:27:38', '2013-05-18 17:27:38', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0042 validada\nAutor: cajeraaspiazu', 46, 'invoice'),
(126, NULL, 1, '2013-05-18 13:27:38', '2013-05-18 13:27:38', NULL, NULL, 40, 'Factura FA1305-0042 pasada a pagada en Dolibarr', '2013-05-18 13:27:38', '2013-05-18 17:27:38', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0042 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 46, 'invoice'),
(127, NULL, 1, '2013-05-18 13:48:57', '2013-05-18 13:48:57', NULL, NULL, 40, 'Pedido CO1305-0013 validado', '2013-05-18 13:48:57', '2013-05-18 17:48:57', 1, NULL, NULL, 40, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0013 validado\nAutor: admindb', 20, 'order'),
(128, NULL, 1, '2013-05-18 14:41:59', '2013-05-18 14:41:59', NULL, NULL, 40, 'Factura FA1305-0043 validada', '2013-05-18 14:41:59', '2013-05-18 18:41:59', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0043 validada\nAutor: admindb', 47, 'invoice'),
(129, NULL, 1, '2013-05-18 14:41:59', '2013-05-18 14:41:59', NULL, NULL, 40, 'Factura FA1305-0043 pasada a pagada en Dolibarr', '2013-05-18 14:41:59', '2013-05-18 18:41:59', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0043 pasada a pagada en Dolibarr\nAutor: admindb', 47, 'invoice'),
(130, NULL, 1, '2013-05-18 14:50:29', '2013-05-18 14:50:29', NULL, NULL, 40, 'Empresa sra. martha gutierrez insertada en Dolibarr', '2013-05-18 14:50:29', '2013-05-18 18:50:29', 1, NULL, NULL, 73, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa sra. martha gutierrez insertada en Dolibarr\nAutor: admindb', 73, 'societe'),
(131, NULL, 1, '2013-05-18 14:52:38', '2013-05-18 14:52:38', NULL, NULL, 40, 'Pedido CO1305-0014 validado', '2013-05-18 14:52:38', '2013-05-18 18:52:38', 1, NULL, NULL, 73, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0014 validado\nAutor: admindb', 21, 'order'),
(132, NULL, 1, '2013-05-25 09:00:21', '2013-05-25 09:00:21', NULL, NULL, 40, 'Factura FA1305-0044 validada', '2013-05-25 09:00:21', '2013-05-25 13:00:21', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0044 validada\nAutor: admindb', 48, 'invoice'),
(133, NULL, 1, '2013-05-25 09:00:21', '2013-05-25 09:00:21', NULL, NULL, 40, 'Factura FA1305-0044 pasada a pagada en Dolibarr', '2013-05-25 09:00:21', '2013-05-25 13:00:21', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0044 pasada a pagada en Dolibarr\nAutor: admindb', 48, 'invoice'),
(134, NULL, 1, '2013-05-25 09:00:53', '2013-05-25 09:00:53', NULL, NULL, 40, 'Factura FA1305-0045 validada', '2013-05-25 09:00:53', '2013-05-25 13:00:53', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0045 validada\nAutor: admindb', 49, 'invoice'),
(135, NULL, 1, '2013-05-25 09:00:53', '2013-05-25 09:00:53', NULL, NULL, 40, 'Factura FA1305-0045 pasada a pagada en Dolibarr', '2013-05-25 09:00:53', '2013-05-25 13:00:53', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0045 pasada a pagada en Dolibarr\nAutor: admindb', 49, 'invoice'),
(136, NULL, 1, '2013-05-25 09:07:30', '2013-05-25 09:07:30', NULL, NULL, 40, 'Factura FA1305-0046 validada', '2013-05-25 09:07:30', '2013-05-25 13:07:30', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0046 validada\nAutor: cajeraaspiazu', 50, 'invoice'),
(137, NULL, 1, '2013-05-25 09:07:30', '2013-05-25 09:07:30', NULL, NULL, 40, 'Factura FA1305-0046 pasada a pagada en Dolibarr', '2013-05-25 09:07:30', '2013-05-25 13:07:30', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0046 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 50, 'invoice'),
(138, NULL, 1, '2013-05-25 09:16:14', '2013-05-25 09:16:14', NULL, NULL, 40, 'Factura FA1305-0047 validada', '2013-05-25 09:16:14', '2013-05-25 13:16:14', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0047 validada\nAutor: cajeraaspiazu', 51, 'invoice'),
(139, NULL, 1, '2013-05-25 09:16:14', '2013-05-25 09:16:14', NULL, NULL, 40, 'Factura FA1305-0047 pasada a pagada en Dolibarr', '2013-05-25 09:16:14', '2013-05-25 13:16:14', 9, NULL, NULL, 1, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0047 pasada a pagada en Dolibarr\nAutor: cajeraaspiazu', 51, 'invoice'),
(140, NULL, 1, '2013-05-25 09:20:38', '2013-05-25 09:20:38', NULL, NULL, 40, 'Factura FA1305-0045 pasada a pagada en Dolibarr', '2013-05-25 09:20:38', '2013-05-25 13:20:38', 1, NULL, NULL, 1, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0045 pasada a pagada en Dolibarr\nAutor: admindb', 49, 'invoice'),
(141, NULL, 1, '2013-05-25 09:41:27', '2013-05-25 09:41:27', NULL, NULL, 40, 'Factura FA1305-0048 validada', '2013-05-25 09:41:27', '2013-05-25 13:41:27', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0048 validada\nAutor: admindb', 52, 'invoice'),
(142, NULL, 1, '2013-05-25 09:41:27', '2013-05-25 09:41:27', NULL, NULL, 40, 'Factura FA1305-0048 pasada a pagada en Dolibarr', '2013-05-25 09:41:27', '2013-05-25 13:41:27', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0048 pasada a pagada en Dolibarr\nAutor: admindb', 52, 'invoice'),
(143, NULL, 1, '2013-05-25 09:42:11', '2013-05-25 09:42:11', NULL, NULL, 40, 'Empresa ssss insertada en Dolibarr', '2013-05-25 09:42:11', '2013-05-25 13:42:11', 1, NULL, NULL, 74, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Empresa ssss insertada en Dolibarr\nAutor: admindb', 74, 'societe'),
(144, NULL, 1, '2013-05-25 09:42:30', '2013-05-25 09:42:30', NULL, NULL, 40, 'Pedido CO1305-0015 validado', '2013-05-25 09:42:30', '2013-05-25 13:42:30', 1, NULL, NULL, 74, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0015 validado\nAutor: admindb', 22, 'order'),
(145, NULL, 1, '2013-05-25 09:42:57', '2013-05-25 09:42:57', NULL, NULL, 40, 'Empresa Rita Mamani insertada en Dolibarr', '2013-05-25 09:42:57', '2013-05-25 13:42:57', 9, NULL, NULL, 75, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Empresa Rita Mamani insertada en Dolibarr\nAutor: cajeraaspiazu', 75, 'societe'),
(146, NULL, 1, '2013-05-25 09:43:35', '2013-05-25 09:43:35', NULL, NULL, 40, 'Pedido CO1305-0016 validado', '2013-05-25 09:43:35', '2013-05-25 13:43:35', 9, NULL, NULL, 75, NULL, 0, NULL, 9, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0016 validado\nAutor: cajeraaspiazu', 23, 'order'),
(147, NULL, 1, '2013-05-25 11:21:08', '2013-05-25 11:21:08', NULL, NULL, 40, 'Factura FA1305-0049 validada', '2013-05-25 11:21:08', '2013-05-25 15:21:08', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0049 validada\nAutor: admindb', 53, 'invoice'),
(148, NULL, 1, '2013-05-25 11:21:08', '2013-05-25 11:21:08', NULL, NULL, 40, 'Factura FA1305-0049 pasada a pagada en Dolibarr', '2013-05-25 11:21:08', '2013-05-25 15:21:08', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0049 pasada a pagada en Dolibarr\nAutor: admindb', 53, 'invoice'),
(149, NULL, 1, '2013-05-25 11:23:50', '2013-05-25 11:23:50', NULL, NULL, 40, 'Factura FA1305-0050 validada', '2013-05-25 11:23:50', '2013-05-25 15:23:50', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0050 validada\nAutor: admindb', 54, 'invoice'),
(150, NULL, 1, '2013-05-25 11:23:50', '2013-05-25 11:23:50', NULL, NULL, 40, 'Factura FA1305-0050 pasada a pagada en Dolibarr', '2013-05-25 11:23:50', '2013-05-25 15:23:50', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1305-0050 pasada a pagada en Dolibarr\nAutor: admindb', 54, 'invoice'),
(151, NULL, 1, '2013-05-25 12:32:26', '2013-05-25 12:32:26', NULL, NULL, 40, 'Pedido CO1305-0017 validado', '2013-05-25 12:32:26', '2013-05-25 16:32:26', 1, NULL, NULL, 40, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CO1305-0017 validado\nAutor: admindb', 24, 'order'),
(152, NULL, 1, '2013-05-25 13:27:20', '2013-05-25 13:27:20', NULL, NULL, 40, 'Factura 7 validada en Dolibarr', '2013-05-25 13:27:20', '2013-05-25 17:27:20', 1, NULL, NULL, 59, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 7 validada en Dolibarr\nAutor: admindb', 7, 'invoice_supplier'),
(153, NULL, 1, '2013-05-25 13:45:34', '2013-05-25 13:45:34', NULL, NULL, 40, 'Factura 9 validada en Dolibarr', '2013-05-25 13:45:34', '2013-05-25 17:45:34', 1, NULL, NULL, 40, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 9 validada en Dolibarr\nAutor: admindb', 9, 'invoice_supplier'),
(154, NULL, 1, '2013-06-01 11:49:01', '2013-06-01 11:49:01', NULL, NULL, 40, 'Pedido CF1304-0001 validado', '2013-06-01 11:49:01', '2013-06-01 15:49:01', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0001 validado\nAutor: admindb', 1, 'order_supplier'),
(155, NULL, 1, '2013-06-01 11:55:54', '2013-06-01 11:55:54', NULL, NULL, 40, 'Pedido CF1304-0001 validado', '2013-06-01 11:55:54', '2013-06-01 15:55:54', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1304-0001 validado\nAutor: admindb', 1, 'order_supplier'),
(156, NULL, 1, '2013-06-01 12:02:12', '2013-06-01 12:02:12', NULL, NULL, 40, 'Factura 10 validada en Dolibarr', '2013-06-01 12:02:12', '2013-06-01 16:02:12', 1, NULL, NULL, 5, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura 10 validada en Dolibarr\nAutor: admindb', 10, 'invoice_supplier'),
(157, NULL, 1, '2013-06-01 12:18:07', '2013-06-01 12:18:07', NULL, NULL, 40, 'Pedido CF1306-0008 validado', '2013-06-01 12:18:07', '2013-06-01 16:18:07', 1, NULL, NULL, 47, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1306-0008 validado\nAutor: admindb', 16, 'order_supplier'),
(158, NULL, 1, '2013-06-01 12:21:50', '2013-06-01 12:21:50', NULL, NULL, 40, 'Pedido CF1306-0009 validado', '2013-06-01 12:21:50', '2013-06-01 16:21:50', 1, NULL, NULL, 47, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Pedido CF1306-0009 validado\nAutor: admindb', 17, 'order_supplier'),
(159, NULL, 1, '2013-06-01 16:05:10', '2013-06-01 16:05:10', NULL, NULL, 40, 'Factura FA1306-0051 validada', '2013-06-01 16:05:10', '2013-06-01 20:05:10', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0051 validada\nAutor: admindb', 55, 'invoice'),
(160, NULL, 1, '2013-06-01 16:05:10', '2013-06-01 16:05:10', NULL, NULL, 40, 'Factura FA1306-0051 pasada a pagada en Dolibarr', '2013-06-01 16:05:10', '2013-06-01 20:05:10', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0051 pasada a pagada en Dolibarr\nAutor: admindb', 55, 'invoice'),
(161, NULL, 1, '2013-06-01 16:05:55', '2013-06-01 16:05:55', NULL, NULL, 40, 'Factura FA1306-0052 validada', '2013-06-01 16:05:55', '2013-06-01 20:05:55', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0052 validada\nAutor: admindb', 56, 'invoice'),
(162, NULL, 1, '2013-06-01 16:05:55', '2013-06-01 16:05:55', NULL, NULL, 40, 'Factura FA1306-0052 pasada a pagada en Dolibarr', '2013-06-01 16:05:55', '2013-06-01 20:05:55', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0052 pasada a pagada en Dolibarr\nAutor: admindb', 56, 'invoice'),
(163, NULL, 1, '2013-06-01 16:17:48', '2013-06-01 16:17:48', NULL, NULL, 40, 'Factura FA1306-0053 validada', '2013-06-01 16:17:48', '2013-06-01 20:17:48', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0053 validada\nAutor: admindb', 57, 'invoice'),
(164, NULL, 1, '2013-06-01 16:17:48', '2013-06-01 16:17:48', NULL, NULL, 40, 'Factura FA1306-0053 pasada a pagada en Dolibarr', '2013-06-01 16:17:48', '2013-06-01 20:17:48', 1, NULL, NULL, 8, NULL, 0, NULL, 1, 0, 0, 1, -1, '', NULL, NULL, 'Factura FA1306-0053 pasada a pagada en Dolibarr\nAutor: admindb', 57, 'invoice');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_actioncomm_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_actioncomm_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_actioncomm_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_adherent`
--

CREATE TABLE IF NOT EXISTS `llx_adherent` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) DEFAULT NULL,
  `civilite` varchar(6) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `pass` varchar(50) DEFAULT NULL,
  `fk_adherent_type` int(11) NOT NULL,
  `morphy` varchar(3) NOT NULL,
  `societe` varchar(50) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `adresse` text,
  `cp` varchar(30) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `pays` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `phone_perso` varchar(30) DEFAULT NULL,
  `phone_mobile` varchar(30) DEFAULT NULL,
  `naiss` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `public` smallint(6) NOT NULL DEFAULT '0',
  `datefin` datetime DEFAULT NULL,
  `note` text,
  `datevalid` datetime DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_mod` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `canvas` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_adherent_login` (`login`,`entity`),
  UNIQUE KEY `uk_adherent_fk_soc` (`fk_soc`),
  KEY `idx_adherent_fk_adherent_type` (`fk_adherent_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_adherent_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_adherent_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_adherent_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_adherent_type`
--

CREATE TABLE IF NOT EXISTS `llx_adherent_type` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `libelle` varchar(50) NOT NULL,
  `cotisation` varchar(3) NOT NULL DEFAULT 'yes',
  `vote` varchar(3) NOT NULL DEFAULT 'yes',
  `note` text,
  `mail_valid` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_adherent_type_libelle` (`libelle`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank`
--

CREATE TABLE IF NOT EXISTS `llx_bank` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datev` date DEFAULT NULL,
  `dateo` date DEFAULT NULL,
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `label` varchar(255) DEFAULT NULL,
  `fk_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_rappro` int(11) DEFAULT NULL,
  `fk_type` varchar(6) DEFAULT NULL,
  `num_releve` varchar(50) DEFAULT NULL,
  `num_chq` varchar(50) DEFAULT NULL,
  `rappro` tinyint(4) DEFAULT '0',
  `note` text,
  `fk_bordereau` int(11) DEFAULT '0',
  `banque` varchar(255) DEFAULT NULL,
  `emetteur` varchar(255) DEFAULT NULL,
  `author` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_bank_datev` (`datev`),
  KEY `idx_bank_dateo` (`dateo`),
  KEY `idx_bank_fk_account` (`fk_account`),
  KEY `idx_bank_rappro` (`rappro`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157 ;

--
-- Volcado de datos para la tabla `llx_bank`
--

INSERT INTO `llx_bank` (`rowid`, `datec`, `tms`, `datev`, `dateo`, `amount`, `label`, `fk_account`, `fk_user_author`, `fk_user_rappro`, `fk_type`, `num_releve`, `num_chq`, `rappro`, `note`, `fk_bordereau`, `banque`, `emetteur`, `author`) VALUES
(1, '2013-02-25 19:26:51', '2013-02-25 23:26:51', '2013-02-25', '2013-02-25', 1000.00000000, '(Saldo inicial)', 1, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(2, '2013-02-25 19:47:35', '2013-02-25 23:47:35', '2013-02-25', '2013-02-25', 1000.00000000, '(Saldo inicial)', 2, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(3, '2013-02-25 19:49:15', '2013-02-25 23:49:15', '2013-02-25', '2013-02-25', 0.00000000, '(Saldo inicial)', 3, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(4, '2013-02-28 19:54:08', '2013-02-28 23:54:08', '2013-02-28', '2013-02-28', 800.00000000, '(Saldo inicial)', 4, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(5, '2013-02-28 20:03:56', '2013-03-01 00:03:56', '2013-02-28', '2013-02-28', 80.00000000, '(CustomerInvoicePayment)', 4, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(6, '2013-02-28 20:04:45', '2013-03-01 00:04:45', '2013-02-28', '2013-02-28', -10.00000000, 'PASAJES', 4, 1, NULL, 'LIQ', NULL, '132', 0, NULL, 0, NULL, NULL, NULL),
(7, '2013-02-28 20:34:31', '2013-03-01 00:34:31', '2013-02-28', '2013-02-28', -870.00000000, 'Cierre de Caja admindb', 4, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(8, '2013-02-28 20:34:31', '2013-03-01 00:34:31', '2013-02-28', '2013-02-28', 870.00000000, 'Cierre de Caja admindb', 2, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(9, '2013-03-02 12:23:22', '2013-03-02 16:23:22', '2013-03-02', '2013-03-02', 120.00000000, '(CustomerInvoicePayment)', 4, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(10, '2013-03-02 14:07:52', '2013-03-02 18:07:52', '2013-03-02', '2013-03-02', 180.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(11, '2013-03-02 14:19:04', '2013-03-02 18:19:04', '2013-03-02', '2013-03-02', 0.00000000, '(Saldo inicial)', 5, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(12, '2013-03-02 14:19:20', '2013-03-02 18:19:20', '2013-03-02', '2013-03-02', 0.00000000, '(Saldo inicial)', 6, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(13, '2013-03-02 14:21:42', '2013-03-02 18:21:42', '2013-03-02', '2013-03-02', 0.00000000, '(Saldo inicial)', 8, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(14, '2013-03-02 14:22:26', '2013-03-02 18:22:26', '2013-03-02', '2013-03-02', 0.00000000, '(Saldo inicial)', 9, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(15, '2013-03-02 14:22:49', '2013-03-02 18:22:49', '2013-03-02', '2013-03-02', 0.00000000, '(Saldo inicial)', 10, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(16, '2013-03-02 15:48:49', '2013-03-02 19:48:49', '2013-03-02', '2013-03-02', 229.00000000, '(CustomerInvoicePayment)', 4, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(17, '2013-03-02 16:40:42', '2013-03-02 20:40:42', '2013-03-02', '2013-03-02', -200.00000000, 'Cierre de Caja admindb', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(19, '2013-03-02 16:41:38', '2013-03-02 20:41:38', '2013-03-02', '2013-03-02', -1000.00000000, '', 8, 1, NULL, 'LIQ', NULL, '65645', 0, NULL, 0, NULL, NULL, NULL),
(20, '2013-03-02 16:41:51', '2013-03-02 20:41:51', '2013-03-02', '2013-03-02', 2000.00000000, '', 8, 1, NULL, 'LIQ', NULL, '123', 0, NULL, 0, NULL, NULL, NULL),
(21, '2013-03-02 16:42:48', '2013-03-02 20:42:48', '2013-03-02', '2013-03-02', 1000.00000000, 'prueba', 10, 1, NULL, 'LIQ', NULL, '99', 0, NULL, 0, NULL, NULL, NULL),
(22, '2013-03-02 16:44:53', '2013-03-02 20:44:53', '2013-03-02', '2013-03-02', 5000.00000000, 'saldo prueba', 5, 1, NULL, 'LIQ', NULL, '888', 0, NULL, 0, NULL, NULL, NULL),
(23, '2013-03-02 16:48:07', '2013-03-02 20:48:07', '2013-03-02', '2013-03-02', -100.00000000, '546465645', 1, 1, NULL, 'LIQ', NULL, '312321', 0, NULL, 0, NULL, NULL, NULL),
(24, '2013-03-02 16:48:22', '2013-03-02 20:48:22', '2013-03-02', '2013-03-02', -100.00000000, '897', 1, 1, NULL, 'LIQ', NULL, '564', 0, NULL, 0, NULL, NULL, NULL),
(25, '2013-03-02 16:51:44', '2013-03-02 20:51:44', '2013-03-02', '2013-03-02', 158.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(26, '2013-03-02 16:58:25', '2013-03-02 20:58:25', '2013-03-02', '2013-03-02', 392.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(27, '2013-03-02 16:59:44', '2013-03-02 20:59:44', '2013-03-02', '2013-03-02', 482.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(28, '2013-03-02 17:00:38', '2013-03-02 21:00:38', '2013-03-02', '2013-03-02', -100.00000000, 'COMIDA PERSONAL', 1, 1, NULL, 'LIQ', NULL, '001', 0, NULL, 0, NULL, NULL, NULL),
(29, '2013-03-02 17:01:38', '2013-03-02 21:01:38', '2013-03-02', '2013-03-02', 1025.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(30, '2013-03-02 17:10:14', '2013-03-02 21:10:14', '2013-03-02', '2013-03-02', 766.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(31, '2013-03-02 17:14:56', '2013-03-02 21:14:56', '2013-03-02', '2013-03-02', 777.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(32, '2013-03-02 17:15:40', '2013-03-02 21:15:40', '2013-03-02', '2013-03-02', -20000.00000000, 'Cierre de Caja admindb admindb', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(33, '2013-03-02 17:15:40', '2013-03-02 21:15:40', '2013-03-02', '2013-03-02', 20000.00000000, 'Cierre de Caja admindb admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(34, '2013-03-04 16:25:49', '2013-03-04 20:25:49', '2013-03-04', '2013-03-04', 455.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(35, '2013-03-05 22:27:23', '2013-03-06 02:27:23', '2013-03-05', '2013-03-05', 4526.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(36, '2013-03-05 23:24:44', '2013-03-06 03:24:44', '2013-03-05', '2013-03-05', 3000.00000000, '(CustomerInvoicePayment)', 2, 1, NULL, 'TIP', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(37, '2013-03-09 12:00:52', '2013-03-09 16:00:52', '2013-03-09', '2013-03-09', 2617.00000000, '(CustomerInvoicePayment)', 4, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(38, '2013-04-06 12:50:43', '2013-04-06 16:50:43', '2013-04-06', '2013-04-06', -100000.00000000, '(SupplierInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(39, '2013-04-06 12:53:51', '2013-04-06 16:53:51', '2013-04-06', '2013-04-06', -100000.00000000, '(SupplierInvoicePayment)', 2, 1, NULL, 'TIP', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(40, '2013-04-06 12:54:33', '2013-04-06 16:54:33', '2013-04-06', '2013-04-06', -90000.00000000, '(SupplierInvoicePayment)', 3, 1, NULL, 'TIP', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(41, '2013-04-10 21:29:21', '2013-04-11 01:29:21', '2013-04-10', '2013-04-10', 600.00000000, '(CustomerInvoicePayment)', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(42, '2013-04-10 21:31:00', '2013-04-11 01:31:00', '2013-04-10', '2013-04-10', -8000.00000000, 'Cierre de caja admindb', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(43, '2013-04-10 21:31:00', '2013-04-11 01:31:00', '2013-04-10', '2013-04-10', 8000.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(44, '2013-04-11 19:05:17', '2013-04-11 23:05:17', '2013-04-11', '2013-04-11', 1346.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(45, '2013-04-11 19:12:09', '2013-04-11 23:12:09', '2013-04-11', '2013-04-11', 1000.00000000, '(Saldo inicial)', 11, NULL, NULL, 'SOLD', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(46, '2013-04-11 19:12:46', '2013-04-11 23:12:46', '2013-04-11', '2013-04-11', -1000.00000000, '', 11, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(47, '2013-04-13 09:43:48', '2013-04-13 13:47:33', '2013-04-13', '2013-04-13', -1346.00000000, 'Cierre de caja monicamamani', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(48, '2013-04-13 09:43:48', '2013-04-13 14:36:10', '2013-04-13', '2013-04-13', -1346.00000000, 'Cierre de caja monicamamani', 10, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(50, '2013-04-13 10:13:56', '2013-04-13 14:13:56', '2013-04-13', '2013-04-13', -110000.00000000, 'Cierre de caja admindb admindb', 1, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(54, '2013-04-13 10:18:59', '2013-04-13 14:18:59', '2013-04-13', '2013-04-13', 150000.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(56, '2013-04-13 10:30:14', '2013-04-13 14:44:20', '2013-04-13', '2013-04-13', -1346.00000000, 'Cierre de caja monicamamani', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(57, '2013-04-13 10:30:14', '2013-04-13 14:44:23', '2013-04-13', '2013-04-13', 1346.00000000, 'Cierre de caja monicamamani', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(58, '2013-04-13 10:54:13', '2013-04-13 14:54:13', '2013-04-13', '2013-04-13', 57.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(59, '2013-04-13 10:54:41', '2013-04-13 14:54:41', '2013-04-13', '2013-04-13', -57.00000000, 'Cierre de caja monicamamani', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(60, '2013-04-13 10:54:41', '2013-04-13 14:54:41', '2013-04-13', '2013-04-13', 57.00000000, 'Cierre de caja monicamamani', 10, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(61, '2013-04-13 10:57:11', '2013-04-13 14:57:11', '2013-04-13', '2013-04-13', 20.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(62, '2013-04-13 10:57:30', '2013-04-13 14:57:30', '2013-04-13', '2013-04-13', 16.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(63, '2013-04-13 10:57:52', '2013-04-13 14:57:52', '2013-04-13', '2013-04-13', -20.00000000, 'Cierre de caja monicamamani', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(64, '2013-04-13 10:57:52', '2013-04-13 14:57:52', '2013-04-13', '2013-04-13', 20.00000000, 'Cierre de caja monicamamani', 10, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(65, '2013-04-13 10:58:15', '2013-04-13 14:58:15', '2013-04-13', '2013-04-13', -16.00000000, 'Cierre de caja monicamamani', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(66, '2013-04-13 10:58:15', '2013-04-13 14:58:15', '2013-04-13', '2013-04-13', 16.00000000, 'Cierre de caja monicamamani', 10, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(67, '2013-04-13 12:08:29', '2013-04-13 16:08:29', '2013-04-13', '2013-04-13', 45.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(68, '2013-04-13 12:09:16', '2013-04-13 16:09:16', '2013-04-13', '2013-04-13', 150.00000000, '(CustomerInvoicePayment)', 1, 12, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(70, '2013-04-15 19:26:59', '2013-04-15 23:26:59', '2013-04-15', '2013-04-15', 93.00000000, '(CustomerInvoicePayment)', 6, 3, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(71, '2013-05-04 09:28:54', '2013-05-04 13:28:54', '2013-05-04', '2013-05-04', -150.00000000, 'Entrega de efectivo para ventas', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(80, '2013-05-04 09:54:41', '2013-05-04 13:54:41', '2013-05-04', '2013-05-04', 1000.00000000, 'Cierre de caja cajeraaspiazu', 10, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(81, '2013-05-04 11:18:09', '2013-05-04 15:18:09', '2013-05-04', '2013-05-04', 291.00000000, '(CustomerInvoicePayment)', 5, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(82, '2013-05-04 11:18:26', '2013-05-04 15:18:26', '2013-05-04', '2013-05-04', -2000.00000000, 'Cierre de caja admindb admindb', 5, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(83, '2013-05-04 11:18:26', '2013-05-04 15:18:26', '2013-05-04', '2013-05-04', 2000.00000000, 'Cierre de caja admindb admindb', 10, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(84, '2013-05-04 11:21:39', '2013-05-04 15:21:39', '2013-05-04', '2013-05-04', 8329.00000000, '(CustomerInvoicePayment)', 5, 6, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(85, '2013-05-04 11:22:07', '2013-05-04 15:22:07', '2013-05-04', '2013-05-04', 8329.00000000, '(CustomerInvoicePayment)', 5, 6, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(86, '2013-05-04 11:26:33', '2013-05-04 15:26:33', '2013-05-04', '2013-05-04', -1000.00000000, 'Gastos en Almuerzo', 5, 6, NULL, 'LIQ', NULL, '132123', 0, NULL, 0, NULL, NULL, NULL),
(87, '2013-05-04 11:57:41', '2013-05-04 15:57:41', '2013-05-04', '2013-05-04', 775.00000000, '(CustomerInvoicePayment)', 5, 6, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(88, '2013-05-04 12:08:38', '2013-05-04 16:08:38', '2013-05-04', '2013-05-04', 1529.00000000, '(CustomerInvoicePayment)', 5, 6, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(89, '2013-05-04 20:25:28', '2013-05-05 00:25:28', '2013-05-04', '2013-05-04', 2433.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(90, '2013-05-04 20:27:43', '2013-05-05 00:27:43', '2013-05-04', '2013-05-04', -20.00000000, 'RADIOTAXI ENVIO A CENTRAL', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(91, '2013-05-04 20:28:00', '2013-05-05 00:28:00', '2013-05-04', '2013-05-04', -15.00000000, 'COMPRA DE PAPAYA', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(92, '2013-05-04 20:28:11', '2013-05-05 00:28:11', '2013-05-04', '2013-05-04', 70.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(93, '2013-05-04 20:37:33', '2013-05-05 00:37:33', '2013-05-04', '2013-05-04', -100.00000000, 'GASTOS VARIOS', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(94, '2013-05-04 20:38:03', '2013-05-05 00:38:03', '2013-05-04', '2013-05-04', 138.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(97, '2013-05-18 13:23:25', '2013-05-18 17:23:25', '2013-05-18', '2013-05-18', 139.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(98, '2013-05-18 13:24:23', '2013-05-18 17:24:23', '2013-05-18', '2013-05-18', 134.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(99, '2013-05-18 13:25:25', '2013-05-18 17:25:25', '2013-05-18', '2013-05-18', 294.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(102, '2013-05-18 14:38:05', '2013-05-18 18:38:05', '2013-05-18', '2013-05-18', -2000.00000000, 'Cierre de caja alemercado', 10, 3, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(103, '2013-05-18 14:38:05', '2013-05-18 18:38:05', '2013-05-18', '2013-05-18', 2000.00000000, 'Cierre de caja alemercado', 8, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(104, '2013-05-18 14:41:59', '2013-05-18 18:41:59', '2013-05-18', '2013-05-18', 229.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(105, '2013-05-18 14:53:42', '2013-05-18 18:53:42', '2013-05-18', '2013-05-18', -10.00000000, 'compras para cocina', 10, 1, NULL, 'LIQ', NULL, '123456', 0, NULL, 0, NULL, NULL, NULL),
(106, '2013-05-18 15:02:30', '2013-05-18 19:02:30', '2013-05-18', '2013-05-18', -100.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(107, '2013-05-18 15:02:30', '2013-05-18 19:02:30', '2013-05-18', '2013-05-18', 100.00000000, 'Cierre de caja admindb', 2, 0, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(112, '2013-05-25 09:03:52', '2013-05-25 13:03:52', '2013-05-25', '2013-05-25', 1162.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(115, '2013-05-25 09:07:30', '2013-05-25 13:07:30', '2013-05-25', '2013-05-25', 59.00000000, '(CustomerInvoicePayment)', 9, 9, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(116, '2013-05-25 09:16:14', '2013-05-25 13:16:14', '2013-05-25', '2013-05-25', 60.00000000, '(CustomerInvoicePayment)', 9, 9, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(117, '2013-05-25 09:41:27', '2013-05-25 13:41:27', '2013-05-25', '2013-05-25', 224.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(118, '2013-05-25 11:21:08', '2013-05-25 15:21:08', '2013-05-25', '2013-05-25', 1130.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(119, '2013-05-25 11:22:52', '2013-05-25 15:22:52', '2013-05-25', '2013-05-25', -90000.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(120, '2013-05-25 11:22:52', '2013-05-25 15:22:52', '2013-05-25', '2013-05-25', 90000.00000000, 'Cierre de caja admindb', 2, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(121, '2013-05-25 11:23:50', '2013-05-25 15:23:50', '2013-05-25', '2013-05-25', 4096.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(122, '2013-05-25 13:46:08', '2013-05-25 17:46:08', '2013-05-25', '2013-05-25', -50.00000000, '(SupplierInvoicePayment)', 3, 1, NULL, 'TIP', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(123, '2013-06-01 16:05:10', '2013-06-01 20:05:10', '2013-06-01', '2013-06-01', 30.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(124, '2013-06-01 16:05:55', '2013-06-01 20:05:55', '2013-06-01', '2013-06-01', 24.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(126, '2013-06-01 16:13:35', '2013-06-01 20:13:35', '2013-06-01', '2013-06-01', -54.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(127, '2013-06-01 16:13:35', '2013-06-01 20:13:35', '2013-06-01', '2013-06-01', 54.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(128, '2013-06-01 16:14:14', '2013-06-01 20:14:14', '2013-06-01', '2013-06-01', -54.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(129, '2013-06-01 16:14:14', '2013-06-01 20:14:14', '2013-06-01', '2013-06-01', 54.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(130, '2013-06-01 16:14:58', '2013-06-01 20:14:58', '2013-06-01', '2013-06-01', -54.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(131, '2013-06-01 16:14:58', '2013-06-01 20:14:58', '2013-06-01', '2013-06-01', 54.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(132, '2013-06-01 16:17:48', '2013-06-01 20:17:48', '2013-06-01', '2013-06-01', 115.00000000, '(CustomerInvoicePayment)', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(133, '2013-06-01 16:18:10', '2013-06-01 20:18:10', '2013-06-01', '2013-06-01', -7.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(134, '2013-06-01 16:18:10', '2013-06-01 20:18:10', '2013-06-01', '2013-06-01', 7.00000000, 'Cierre de caja admindb', 8, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(135, '2013-06-01 16:21:28', '2013-06-01 20:21:28', '2013-06-01', '2013-06-01', -257.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(136, '2013-06-01 16:21:28', '2013-06-01 20:21:28', '2013-06-01', '2013-06-01', 257.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(137, '2013-06-01 16:23:06', '2013-06-01 20:23:06', '2013-06-01', '2013-06-01', -510.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(138, '2013-06-01 16:23:06', '2013-06-01 20:23:06', '2013-06-01', '2013-06-01', 510.00000000, 'Cierre de caja admindb', 2, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(139, '2013-06-01 16:24:19', '2013-06-01 20:24:19', '2013-06-01', '2013-06-01', -12.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(140, '2013-06-01 16:24:19', '2013-06-01 20:24:19', '2013-06-01', '2013-06-01', 12.00000000, 'Cierre de caja admindb', 9, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(141, '2013-06-01 16:25:13', '2013-06-01 20:25:13', '2013-06-01', '2013-06-01', -33.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(142, '2013-06-01 16:25:13', '2013-06-01 20:25:13', '2013-06-01', '2013-06-01', 33.00000000, 'Cierre de caja admindb', 9, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(143, '2013-06-01 16:29:01', '2013-06-01 20:29:01', '2013-06-01', '2013-06-01', -33.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(144, '2013-06-01 16:29:01', '2013-06-01 20:29:01', '2013-06-01', '2013-06-01', 33.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(145, '2013-06-01 16:33:03', '2013-06-01 20:33:03', '2013-06-01', '2013-06-01', -45.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(146, '2013-06-01 16:33:03', '2013-06-01 20:33:03', '2013-06-01', '2013-06-01', 45.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(147, '2013-06-01 16:34:04', '2013-06-01 20:34:04', '2013-06-01', '2013-06-01', -3.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(148, '2013-06-01 16:34:04', '2013-06-01 20:34:04', '2013-06-01', '2013-06-01', 3.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(149, '2013-06-01 16:34:43', '2013-06-01 20:34:43', '2013-06-01', '2013-06-01', -3.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(150, '2013-06-01 16:34:43', '2013-06-01 20:34:43', '2013-06-01', '2013-06-01', 3.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(151, '2013-06-01 16:35:06', '2013-06-01 20:35:06', '2013-06-01', '2013-06-01', -3.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(152, '2013-06-01 16:35:06', '2013-06-01 20:35:06', '2013-06-01', '2013-06-01', 3.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(153, '2013-06-01 16:36:43', '2013-06-01 20:36:43', '2013-06-01', '2013-06-01', -33.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(154, '2013-06-01 16:36:43', '2013-06-01 20:36:43', '2013-06-01', '2013-06-01', 33.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(155, '2013-06-01 16:38:38', '2013-06-01 20:38:38', '2013-06-01', '2013-06-01', -33.00000000, 'Cierre de caja admindb', 10, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(156, '2013-06-01 16:38:38', '2013-06-01 20:38:38', '2013-06-01', '2013-06-01', 33.00000000, 'Cierre de caja admindb', 3, 1, NULL, 'LIQ', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank_account`
--

CREATE TABLE IF NOT EXISTS `llx_bank_account` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(12) NOT NULL,
  `label` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `bank` varchar(60) DEFAULT NULL,
  `code_banque` varchar(8) DEFAULT NULL,
  `code_guichet` varchar(6) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `cle_rib` varchar(5) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  `iban_prefix` varchar(34) DEFAULT NULL,
  `country_iban` varchar(2) DEFAULT NULL,
  `cle_iban` varchar(2) DEFAULT NULL,
  `domiciliation` varchar(255) DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL,
  `proprio` varchar(60) DEFAULT NULL,
  `adresse_proprio` varchar(255) DEFAULT NULL,
  `courant` smallint(6) NOT NULL DEFAULT '0',
  `clos` smallint(6) NOT NULL DEFAULT '0',
  `rappro` smallint(6) DEFAULT '1',
  `url` varchar(128) DEFAULT NULL,
  `account_number` varchar(8) DEFAULT NULL,
  `currency_code` varchar(3) NOT NULL,
  `min_allowed` int(11) DEFAULT '0',
  `min_desired` int(11) DEFAULT '0',
  `comment` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bank_account_label` (`label`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `llx_bank_account`
--

INSERT INTO `llx_bank_account` (`rowid`, `datec`, `tms`, `ref`, `label`, `entity`, `bank`, `code_banque`, `code_guichet`, `number`, `cle_rib`, `bic`, `iban_prefix`, `country_iban`, `cle_iban`, `domiciliation`, `fk_departement`, `fk_pays`, `proprio`, `adresse_proprio`, `courant`, `clos`, `rappro`, `url`, `account_number`, `currency_code`, `min_allowed`, `min_desired`, `comment`) VALUES
(1, '2013-02-25 19:26:51', '2013-02-25 23:26:51', 'CJMAX', 'CAJA MAX PAREDES', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'BOB', 0, 0, ''),
(2, '2013-02-25 19:47:35', '2013-02-25 23:47:35', 'banco001', 'banco 1', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 1, 0, 1, NULL, '', 'BOB', 0, 0, ''),
(3, '2013-02-25 19:49:15', '2013-02-25 23:49:15', 'B001', 'BANCO NACIONAL', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 1, 0, 1, NULL, '', 'BOB', 0, 0, ''),
(4, '2013-02-28 19:54:08', '2013-03-02 18:21:02', 'YUNGAS', 'CAJA YUNGAS', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'AFN', 0, 0, ''),
(5, '2013-03-02 14:19:04', '2013-03-02 18:22:06', 'ANEXO', 'CAJA MAX ANEXO', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, ''),
(6, '2013-03-02 14:19:20', '2013-03-02 18:20:49', 'SANPEDRO', 'CAJA SAN PEDRO', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, ''),
(8, '2013-03-02 14:21:42', '2013-03-02 18:21:42', 'ECUADOR', 'CAJA ECUADOR', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, ''),
(9, '2013-03-02 14:22:26', '2013-03-02 18:22:26', 'ASPIAZU', 'CAJA ASPIAZU', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, ''),
(10, '2013-03-02 14:22:49', '2013-03-02 18:23:10', 'CENTRAL', '.  OFICINA CENTRAL', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, ''),
(11, '2013-04-11 19:12:09', '2013-04-11 23:12:09', 'CAJAMIRAFLOR', 'CAJA MIRAFLORES', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 52, NULL, NULL, 2, 0, 1, NULL, '', 'Bs.', 0, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank_categ`
--

CREATE TABLE IF NOT EXISTS `llx_bank_categ` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank_class`
--

CREATE TABLE IF NOT EXISTS `llx_bank_class` (
  `lineid` int(11) NOT NULL,
  `fk_categ` int(11) NOT NULL,
  UNIQUE KEY `uk_bank_class_lineid` (`lineid`,`fk_categ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank_spending`
--

CREATE TABLE IF NOT EXISTS `llx_bank_spending` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_bank_id` int(11) NOT NULL,
  `fk_accounting_spending` int(11) NOT NULL,
  `date_creator` date NOT NULL,
  `amount` double(24,8) NOT NULL,
  `date_seat` date DEFAULT '0000-00-00',
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bank_url`
--

CREATE TABLE IF NOT EXISTS `llx_bank_url` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_bank` int(11) DEFAULT NULL,
  `url_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bank_url` (`fk_bank`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=191 ;

--
-- Volcado de datos para la tabla `llx_bank_url`
--

INSERT INTO `llx_bank_url` (`rowid`, `fk_bank`, `url_id`, `url`, `label`, `type`) VALUES
(1, 5, 1, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(2, 5, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(3, 7, 8, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(4, 8, 7, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(5, 9, 2, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(6, 9, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(7, 10, 3, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(8, 10, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(9, 16, 4, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(10, 16, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(11, 17, 18, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(13, 25, 5, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(14, 25, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(15, 26, 6, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(16, 26, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(17, 27, 7, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(18, 27, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(19, 29, 8, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(20, 29, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(21, 30, 9, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(22, 30, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(23, 31, 10, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(24, 31, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(25, 32, 33, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(26, 33, 32, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(27, 34, 11, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(28, 34, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(29, 35, 12, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(30, 35, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(31, 36, 14, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(32, 36, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(33, 37, 15, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(34, 37, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(35, 38, 1, '/michelline/htdocs/fourn/paiement/fiche.php?id=', '(paiement)', 'payment_supplier'),
(36, 38, 5, '/michelline/htdocs/fourn/fiche.php?socid=', 'quimica montes', 'company'),
(37, 39, 3, '/michelline/htdocs/fourn/paiement/fiche.php?id=', '(paiement)', 'payment_supplier'),
(38, 39, 5, '/michelline/htdocs/fourn/fiche.php?socid=', 'quimica montes', 'company'),
(39, 40, 4, '/michelline/htdocs/fourn/paiement/fiche.php?id=', '(paiement)', 'payment_supplier'),
(40, 40, 5, '/michelline/htdocs/fourn/fiche.php?socid=', 'quimica montes', 'company'),
(41, 41, 16, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(42, 41, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(43, 42, 43, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(44, 43, 42, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(45, 44, 17, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(46, 44, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(47, 47, 48, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(48, 48, 47, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(49, 50, 51, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(54, 54, 53, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(57, 56, 57, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(58, 57, 56, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(59, 58, 20, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(60, 58, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(61, 59, 60, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(62, 60, 59, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(63, 61, 21, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(64, 61, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(65, 62, 22, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(66, 62, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(67, 63, 64, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(68, 64, 63, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(69, 65, 66, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(70, 66, 65, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(71, 67, 23, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(72, 67, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(73, 68, 24, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(74, 68, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(77, 70, 26, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(78, 70, 3, '/michelline/htdocs/comm/fiche.php?socid=', 'Jose Luis Mariaca', 'company'),
(79, 71, 72, '/michelline/htdocs/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(94, 80, 79, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(95, 81, 33, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(96, 81, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(97, 82, 83, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(98, 83, 82, '/michelline/htdocs/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(99, 84, 34, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(100, 84, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(101, 85, 35, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(102, 85, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(103, 87, 36, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(104, 87, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(105, 88, 37, '/michelline/htdocs/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(106, 88, 1, '/michelline/htdocs/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(107, 89, 38, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(108, 89, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(109, 92, 39, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(110, 92, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(111, 94, 40, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(112, 94, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(117, 97, 43, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(118, 97, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(119, 98, 44, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(120, 98, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(121, 99, 45, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(122, 99, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(127, 102, 103, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(128, 103, 102, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(129, 104, 48, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(130, 104, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(131, 106, 107, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(132, 107, 106, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(138, 112, 111, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(141, 115, 51, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(142, 115, 1, '/michelline/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(143, 116, 52, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(144, 116, 1, '/michelline/comm/fiche.php?socid=', 'VentasxMenor', 'company'),
(145, 117, 53, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(146, 117, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(147, 118, 54, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(148, 118, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(149, 119, 120, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(150, 120, 119, '/michelline/comptaped/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(151, 121, 55, '/michelline/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(152, 121, 8, '/michelline/comm/fiche.php?socid=', 'cliente generico', 'company'),
(153, 122, 6, '/michelline/fourn/paiement/fiche.php?id=', '(paiement)', 'payment_supplier'),
(154, 122, 40, '/michelline/fourn/fiche.php?socid=', 'Abarrotes en General', 'company'),
(155, 123, 56, '/dolimich331/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(156, 123, 8, '/dolimich331/comm/fiche.php?socid=', 'cliente generico', 'company'),
(157, 124, 57, '/dolimich331/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(158, 124, 8, '/dolimich331/comm/fiche.php?socid=', 'cliente generico', 'company'),
(159, 126, 127, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(160, 127, 126, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(161, 128, 129, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(162, 129, 128, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(163, 130, 131, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(164, 131, 130, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(165, 132, 58, '/dolimich331/compta/paiement/fiche.php?id=', '(paiement)', 'payment'),
(166, 132, 8, '/dolimich331/comm/fiche.php?socid=', 'cliente generico', 'company'),
(167, 133, 134, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(168, 134, 133, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(169, 135, 136, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(170, 136, 135, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(171, 137, 138, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(172, 138, 137, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(173, 139, 140, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(174, 140, 139, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(175, 141, 142, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(176, 142, 141, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(177, 143, 144, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(178, 144, 143, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(179, 145, 146, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(180, 146, 145, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(181, 147, 148, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(182, 148, 147, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(183, 149, 150, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(184, 150, 149, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(185, 151, 152, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(186, 152, 151, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(187, 153, 154, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(188, 154, 153, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(189, 155, 156, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert'),
(190, 156, 155, '/dolimich331/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bookmark`
--

CREATE TABLE IF NOT EXISTS `llx_bookmark` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `dateb` datetime DEFAULT NULL,
  `url` varchar(128) NOT NULL,
  `target` varchar(16) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `favicon` varchar(24) DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bookmark_url` (`fk_user`,`url`),
  UNIQUE KEY `uk_bookmark_title` (`fk_user`,`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `llx_bookmark`
--

INSERT INTO `llx_bookmark` (`rowid`, `fk_soc`, `fk_user`, `dateb`, `url`, `target`, `title`, `favicon`, `position`) VALUES
(1, NULL, 1, '2013-04-27 16:21:42', 'http://192.168.2.101/michelline/htdocs/almacen/local/liste.php?idmenu=248', '1', 'solicitudes a entregar', 'none', 0),
(2, NULL, 1, '2013-05-18 15:26:12', 'http://192.168.2.11/michelline/product/stock/mouvement.php', '1', 'MOVIMIENTOS DE STOCK', 'none', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_bordereau_cheque`
--

CREATE TABLE IF NOT EXISTS `llx_bordereau_cheque` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime NOT NULL,
  `date_bordereau` date DEFAULT NULL,
  `number` varchar(16) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `amount` double(24,8) NOT NULL,
  `nbcheque` smallint(6) NOT NULL,
  `fk_bank_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bordereau_cheque` (`number`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_boxes`
--

CREATE TABLE IF NOT EXISTS `llx_boxes` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `box_id` int(11) NOT NULL,
  `position` smallint(6) NOT NULL,
  `box_order` varchar(3) NOT NULL,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `maxline` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_boxes` (`entity`,`box_id`,`position`,`fk_user`),
  KEY `idx_boxes_boxid` (`box_id`),
  KEY `idx_boxes_fk_user` (`fk_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=212 ;

--
-- Volcado de datos para la tabla `llx_boxes`
--

INSERT INTO `llx_boxes` (`rowid`, `entity`, `box_id`, `position`, `box_order`, `fk_user`, `maxline`) VALUES
(1, 1, 1, 0, 'B18', 0, NULL),
(2, 1, 2, 0, 'A09', 0, NULL),
(3, 1, 3, 0, 'B12', 0, NULL),
(7, 1, 7, 0, 'A11', 0, NULL),
(8, 1, 8, 0, 'B16', 0, NULL),
(9, 1, 9, 0, 'B08', 0, NULL),
(18, 1, 18, 0, 'A15', 0, NULL),
(19, 1, 19, 0, 'A19', 0, NULL),
(24, 1, 24, 0, 'B10', 0, NULL),
(114, 1, 55, 0, 'B14', 0, NULL),
(118, 1, 59, 0, 'A07', 0, NULL),
(137, 1, 78, 0, 'A13', 0, NULL),
(155, 1, 96, 0, 'B06', 0, NULL),
(200, 1, 96, 0, 'A01', 1, NULL),
(201, 1, 1, 0, 'A02', 1, NULL),
(202, 1, 8, 0, 'A03', 1, NULL),
(204, 1, 19, 0, 'B02', 1, NULL),
(205, 1, 9, 0, 'B03', 1, NULL),
(206, 1, 97, 0, 'B02', 0, NULL),
(207, 1, 98, 0, 'A01', 0, NULL),
(208, 1, 99, 0, 'B04', 0, NULL),
(209, 1, 100, 0, 'A03', 0, NULL),
(210, 1, 101, 0, 'A05', 0, NULL),
(211, 1, 102, 0, '0', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_boxes_def`
--

CREATE TABLE IF NOT EXISTS `llx_boxes_def` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(200) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` varchar(130) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_boxes_def` (`file`,`entity`,`note`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=103 ;

--
-- Volcado de datos para la tabla `llx_boxes_def`
--

INSERT INTO `llx_boxes_def` (`rowid`, `file`, `entity`, `tms`, `note`) VALUES
(1, 'box_clients.php', 1, '2013-02-19 13:38:43', NULL),
(2, 'box_prospect.php', 1, '2013-02-19 13:38:43', NULL),
(3, 'box_contacts.php', 1, '2013-02-19 13:38:43', NULL),
(7, 'box_fournisseurs.php', 1, '2013-02-19 13:39:22', NULL),
(8, 'box_factures_fourn_imp.php', 1, '2013-02-19 13:39:22', NULL),
(9, 'box_factures_fourn.php', 1, '2013-02-19 13:39:22', NULL),
(18, 'box_services_contracts.php', 1, '2013-02-19 14:03:33', NULL),
(19, 'box_actions.php', 1, '2013-02-19 14:05:01', NULL),
(24, 'box_activity.php', 1, '2013-03-02 15:43:06', NULL),
(55, 'box_propales.php', 1, '2013-04-13 14:28:54', NULL),
(59, 'box_bookmarks.php', 1, '2013-04-27 20:20:44', NULL),
(78, 'box_members.php', 1, '2013-05-04 23:59:37', NULL),
(96, 'box_productmax.php', 1, '2013-05-25 12:40:13', NULL),
(97, 'box_comptes.php', 1, '2013-06-01 13:18:53', NULL),
(98, 'box_factures_imp.php', 1, '2013-06-01 13:18:53', NULL),
(99, 'box_factures.php', 1, '2013-06-01 13:18:53', NULL),
(100, 'box_produits.php', 1, '2013-06-01 13:18:53', NULL),
(101, 'box_produits_alerte_stock.php', 1, '2013-06-01 13:18:53', NULL),
(102, 'box_commandes.php', 1, '2013-06-01 16:19:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_cajachica`
--

CREATE TABLE IF NOT EXISTS `llx_cajachica` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `cod_caja` varchar(3) COLLATE latin1_bin NOT NULL,
  `nombre_caja` varchar(30) COLLATE latin1_bin NOT NULL,
  `fk_bank` int(11) NOT NULL,
  `status` varchar(1) COLLATE latin1_bin NOT NULL,
  `saldo` double DEFAULT NULL,
  `ultima_apertura` date DEFAULT NULL,
  `secuencia_cierre` varchar(6) COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_bin COMMENT='Registra Caja Chica' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_cajachica`
--

INSERT INTO `llx_cajachica` (`rowid`, `entity`, `cod_caja`, `nombre_caja`, `fk_bank`, `status`, `saldo`, `ultima_apertura`, `secuencia_cierre`) VALUES
(1, 1, 'max', 'max paredes', 1, 'P', NULL, NULL, NULL),
(2, 1, '192', 'Anexo', 5, 'P', NULL, NULL, NULL),
(3, 1, '192', 'max paredes', 1, 'P', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_cajachicadet`
--

CREATE TABLE IF NOT EXISTS `llx_cajachicadet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_cajachica` int(11) NOT NULL,
  `numero` varchar(10) COLLATE latin1_bin NOT NULL,
  `tipo_movimiento` varchar(1) COLLATE latin1_bin NOT NULL,
  `valor` double NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin COMMENT='Movimiento Caja Chica' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_cajachicaentrepot`
--

CREATE TABLE IF NOT EXISTS `llx_cajachicaentrepot` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `numero_ip` varchar(15) NOT NULL,
  `fk_entrepotid` int(11) NOT NULL,
  `fk_socid` int(11) NOT NULL,
  `fk_cajaid` int(11) NOT NULL,
  `fk_bankid` int(11) NOT NULL,
  `fk_banktcid` int(11) NOT NULL,
  `status` varchar(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Registra permisos de Almacenes Terceros por IP' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_cajachicaentrepot`
--

INSERT INTO `llx_cajachicaentrepot` (`rowid`, `entity`, `numero_ip`, `fk_entrepotid`, `fk_socid`, `fk_cajaid`, `fk_bankid`, `fk_banktcid`, `status`) VALUES
(1, 1, '192.168.2.101', 1, 1, 1, 0, 0, '1'),
(2, 1, '192.168.2.109', 1, 1, 4, 0, 0, '1'),
(3, 1, '192.168.2.112', 1, 1, 1, 0, 0, '1'),
(4, 1, '192.168.2.110', 7, 3, 8, 0, 0, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_cajachicauser`
--

CREATE TABLE IF NOT EXISTS `llx_cajachicauser` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_cajachica` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Relacion Caja Chica y Users' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_cajachicauser`
--

INSERT INTO `llx_cajachicauser` (`rowid`, `fk_cajachica`, `fk_user`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie`
--

CREATE TABLE IF NOT EXISTS `llx_categorie` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_parent` int(11) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `entity` int(11) NOT NULL DEFAULT '1',
  `description` text,
  `fk_soc` int(11) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_categorie_ref` (`entity`,`fk_parent`,`label`,`type`),
  KEY `idx_categorie_type` (`type`),
  KEY `idx_categorie_label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_categorie`
--

INSERT INTO `llx_categorie` (`rowid`, `fk_parent`, `label`, `type`, `entity`, `description`, `fk_soc`, `visible`, `import_key`) VALUES
(1, 9, 'Pasteleria', 0, 1, 'Productos de pasteleria', NULL, 0, NULL),
(2, 9, 'Pastelitos', 0, 1, 'Pasteles pequenos', NULL, 0, NULL),
(3, 9, 'Productos Eventuales', 0, 1, 'Productos por temporada', NULL, 0, NULL),
(4, 9, 'Productos Snack', 0, 1, 'Productos para consumo en tiendas, salados, productos externos', NULL, 0, NULL),
(5, 9, 'Servicios', 0, 1, 'Servicios de PDV', NULL, 0, NULL),
(6, 9, 'Otros', 0, 1, 'Otros Productos de venta', NULL, 0, NULL),
(7, 9, 'TORTAS', 0, 1, 'Tortas', NULL, 0, NULL),
(8, 0, 'INSUMOS', 0, 1, 'USO INTERNO', NULL, 0, NULL),
(9, 0, 'VENTA', 0, 1, 'PRODUCTOS DE VENTA', NULL, 0, NULL),
(10, 9, 'HELADOS', 0, 1, 'HELADOS', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie_association`
--

CREATE TABLE IF NOT EXISTS `llx_categorie_association` (
  `fk_categorie_mere` int(11) NOT NULL,
  `fk_categorie_fille` int(11) NOT NULL,
  UNIQUE KEY `uk_categorie_association` (`fk_categorie_mere`,`fk_categorie_fille`),
  UNIQUE KEY `uk_categorie_association_fk_categorie_fille` (`fk_categorie_fille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie_fournisseur`
--

CREATE TABLE IF NOT EXISTS `llx_categorie_fournisseur` (
  `fk_categorie` int(11) NOT NULL,
  `fk_societe` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_societe`),
  KEY `idx_categorie_fournisseur_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_fournisseur_fk_societe` (`fk_societe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie_member`
--

CREATE TABLE IF NOT EXISTS `llx_categorie_member` (
  `fk_categorie` int(11) NOT NULL,
  `fk_member` int(11) NOT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_member`),
  KEY `idx_categorie_member_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_member_fk_member` (`fk_member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie_product`
--

CREATE TABLE IF NOT EXISTS `llx_categorie_product` (
  `fk_categorie` int(11) NOT NULL,
  `fk_product` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_product`),
  KEY `idx_categorie_product_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_product_fk_product` (`fk_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_categorie_product`
--

INSERT INTO `llx_categorie_product` (`fk_categorie`, `fk_product`, `import_key`) VALUES
(1, 29, NULL),
(1, 33, NULL),
(1, 37, NULL),
(1, 39, NULL),
(1, 47, NULL),
(2, 1, NULL),
(2, 38, NULL),
(2, 50, NULL),
(3, 164, NULL),
(4, 184, NULL),
(5, 31, NULL),
(6, 207, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_categorie_societe`
--

CREATE TABLE IF NOT EXISTS `llx_categorie_societe` (
  `fk_categorie` int(11) NOT NULL,
  `fk_societe` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_societe`),
  KEY `idx_categorie_societe_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_societe_fk_societe` (`fk_societe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_chargesociales`
--

CREATE TABLE IF NOT EXISTS `llx_chargesociales` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_ech` datetime NOT NULL,
  `libelle` varchar(80) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_type` int(11) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `periode` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande`
--

CREATE TABLE IF NOT EXISTS `llx_commande` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) DEFAULT NULL,
  `ref_int` varchar(255) DEFAULT NULL,
  `ref_client` varchar(255) DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `source` smallint(6) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `amount_ht` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `facture` tinyint(4) DEFAULT '0',
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `fk_input_reason` int(11) DEFAULT NULL,
  `fk_adresse_livraison` int(11) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_commande_ref` (`ref`,`entity`),
  KEY `idx_commande_fk_soc` (`fk_soc`),
  KEY `idx_commande_fk_user_author` (`fk_user_author`),
  KEY `idx_commande_fk_user_valid` (`fk_user_valid`),
  KEY `idx_commande_fk_user_cloture` (`fk_user_cloture`),
  KEY `idx_commande_fk_projet` (`fk_projet`),
  KEY `idx_commande_fk_account` (`fk_account`),
  KEY `idx_commande_fk_currency` (`fk_currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Volcado de datos para la tabla `llx_commande`
--

INSERT INTO `llx_commande` (`rowid`, `ref`, `entity`, `ref_ext`, `ref_int`, `ref_client`, `fk_soc`, `fk_projet`, `tms`, `date_creation`, `date_valid`, `date_cloture`, `date_commande`, `fk_user_author`, `fk_user_valid`, `fk_user_cloture`, `source`, `fk_statut`, `amount_ht`, `remise_percent`, `remise_absolue`, `remise`, `tva`, `localtax1`, `localtax2`, `total_ht`, `total_ttc`, `note`, `note_public`, `model_pdf`, `facture`, `fk_account`, `fk_currency`, `fk_cond_reglement`, `fk_mode_reglement`, `date_livraison`, `fk_availability`, `fk_input_reason`, `fk_adresse_livraison`, `import_key`, `extraparams`) VALUES
(1, 'CO1302-0001', 1, NULL, NULL, '2204193', 1, NULL, '2013-03-06 02:52:59', '2013-02-28 20:22:50', '2013-03-05 22:45:14', '2013-03-05 22:52:59', '2013-02-28', 1, 1, 1, NULL, 3, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 2440.00000000, 2440.00000000, '', 'sr. mariaca', 'einstein', 0, NULL, NULL, 1, NULL, '2013-02-02', NULL, NULL, NULL, NULL, NULL),
(2, '(PROV2)', 1, NULL, NULL, '', 1, NULL, '2013-03-02 17:01:41', '2013-03-02 13:01:41', NULL, NULL, '2013-03-02', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '(PROV3)', 1, NULL, NULL, '', 3, NULL, '2013-03-09 14:49:16', '2013-03-09 10:44:17', NULL, NULL, '2013-03-09', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 1.30000000, 0.00000000, 0.00000000, 10.00000000, 11.30000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-03-09', NULL, NULL, NULL, NULL, NULL),
(4, '(PROV4)', 1, NULL, NULL, 'as', 3, NULL, '2013-03-09 15:23:57', '2013-03-09 11:22:53', NULL, NULL, '2013-03-09', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 260.00000000, 0.00000000, 0.00000000, 3000.00000000, 3260.00000000, '', '', 'einstein', 0, NULL, NULL, 1, NULL, '2013-03-09', NULL, NULL, NULL, NULL, NULL),
(5, '(PROV5)', 1, NULL, NULL, 'ads', 3, NULL, '2013-03-09 16:20:26', '2013-03-09 12:20:26', NULL, NULL, '2013-03-09', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-03-09', NULL, NULL, NULL, NULL, NULL),
(6, 'CO1303-0002', 1, NULL, NULL, '6666262', 4, NULL, '2013-03-16 13:57:31', '2013-03-16 09:56:36', '2013-03-16 09:57:31', NULL, '2013-03-16', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 130.00000000, 0.00000000, 0.00000000, 1000.00000000, 1130.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-03-22', NULL, NULL, NULL, NULL, NULL),
(7, 'CO1303-0003', 1, NULL, NULL, '12312', 4, NULL, '2013-03-16 15:44:27', '2013-03-16 11:42:45', '2013-03-16 11:44:27', NULL, '2013-03-16', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 2600.00000000, 0.00000000, 0.00000000, 20000.00000000, 22600.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-03-29', NULL, NULL, NULL, NULL, NULL),
(8, 'CO1303-0004', 1, NULL, NULL, 'joselo', 3, NULL, '2013-03-16 21:01:23', '2013-03-16 17:01:06', '2013-03-16 17:01:23', NULL, '2013-03-16', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'CO1304-0005', 1, NULL, NULL, 'jose', 3, NULL, '2013-04-06 14:03:55', '2013-04-06 10:02:42', '2013-04-06 10:03:55', NULL, '2013-04-06', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 1200.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-04-06', NULL, NULL, NULL, NULL, NULL),
(10, 'CO1304-0006', 1, NULL, NULL, '123', 1, NULL, '2013-04-10 23:54:42', '2013-04-10 19:53:32', '2013-04-10 19:53:51', NULL, '2013-04-10', 1, 1, NULL, NULL, 2, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-04-11', NULL, NULL, NULL, NULL, NULL),
(11, 'CO1304-0007', 1, NULL, NULL, '132213213', 1, NULL, '2013-04-11 01:11:13', '2013-04-10 21:10:32', '2013-04-10 21:10:55', NULL, '2013-04-10', 1, 1, NULL, NULL, 2, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-04-10', NULL, NULL, NULL, NULL, NULL),
(12, 'CO1304-0008', 1, NULL, NULL, '', 6, NULL, '2013-04-15 23:14:40', '2013-04-15 19:14:03', '2013-04-15 19:14:40', NULL, '2013-04-15', 3, 3, NULL, NULL, 1, 0, 0, NULL, 0, 1560.00000000, 0.00000000, 0.00000000, 12000.00000000, 13560.00000000, '', '', 'einstein', 0, NULL, NULL, 1, 4, '2013-04-20', 2, 7, NULL, NULL, NULL),
(13, '(PROV13)', 1, NULL, NULL, '', 6, NULL, '2013-04-27 16:59:54', '2013-04-27 12:59:54', NULL, NULL, '2013-04-27', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, '', 1, NULL, NULL, '465465465', 71, NULL, '2013-05-14 18:53:11', '2013-05-14 14:51:44', '2013-05-14 14:53:11', NULL, '2013-05-14', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, 1, 4, '2013-05-14', 1, 10, NULL, NULL, NULL),
(15, 'CO1305-0009', 1, NULL, NULL, '60606516', 71, NULL, '2013-05-14 19:24:53', '2013-05-14 15:10:25', '2013-05-14 15:11:59', '2013-05-14 15:24:41', '2013-05-14', 1, 1, 1, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-14', NULL, NULL, NULL, NULL, NULL),
(16, 'CO1305-0010', 1, NULL, NULL, '70623232', 72, NULL, '2013-05-15 16:06:00', '2013-05-15 12:05:08', '2013-05-15 12:06:00', NULL, '2013-05-15', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'CO1305-0011', 1, NULL, NULL, '444444', 47, NULL, '2013-05-15 18:11:25', '2013-05-15 14:09:05', '2013-05-15 14:11:25', NULL, '2013-05-15', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 433.29000000, 0.00000000, 0.00000000, 3333.00000000, 3766.29000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'CO1305-0012', 1, NULL, NULL, '4444444', 40, NULL, '2013-05-16 16:08:36', '2013-05-16 12:07:58', '2013-05-16 12:08:36', NULL, '2013-05-16', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 370.50000000, 0.00000000, 0.00000000, 2850.00000000, 3220.50000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-16', NULL, NULL, NULL, NULL, NULL),
(19, '(PROV19)', 1, NULL, NULL, '123123123', 55, NULL, '2013-05-18 14:38:36', '2013-05-18 10:38:10', NULL, NULL, '2013-05-18', 1, NULL, NULL, NULL, 0, 0, 0, NULL, 0, 130.00000000, 0.00000000, 0.00000000, 1000.00000000, 1130.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-18', NULL, NULL, NULL, NULL, NULL),
(20, 'CO1305-0013', 1, NULL, NULL, '56564645', 40, NULL, '2013-05-18 17:48:57', '2013-05-18 13:48:44', '2013-05-18 13:48:57', NULL, '2013-05-18', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-18', NULL, NULL, NULL, NULL, NULL),
(21, 'CO1305-0014', 1, NULL, NULL, '70520612', 73, NULL, '2013-05-18 18:52:38', '2013-05-18 14:50:48', '2013-05-18 14:52:38', NULL, '2013-05-18', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-25', NULL, NULL, NULL, NULL, NULL),
(22, 'CO1305-0015', 1, NULL, NULL, '', 74, NULL, '2013-05-25 13:42:30', '2013-05-25 09:42:17', '2013-05-25 09:42:30', NULL, '2013-05-25', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-25', NULL, NULL, NULL, NULL, NULL),
(23, 'CO1305-0016', 1, NULL, NULL, '123', 75, NULL, '2013-05-25 13:43:35', '2013-05-25 09:43:17', '2013-05-25 09:43:35', NULL, '2013-05-25', 9, 9, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-25', NULL, NULL, NULL, NULL, NULL),
(24, 'CO1305-0017', 1, NULL, NULL, '243234234423', 40, NULL, '2013-05-25 16:32:26', '2013-05-25 12:31:52', '2013-05-25 12:32:26', NULL, '2013-05-25', 1, 1, NULL, NULL, 1, 0, 0, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', '', 'einstein', 0, NULL, NULL, NULL, NULL, '2013-05-25', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commandedet`
--

CREATE TABLE IF NOT EXISTS `llx_commandedet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT NULL,
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT NULL,
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `fk_remise_except` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `fk_product_fournisseur_price` int(11) DEFAULT NULL,
  `buy_price_ht` double(24,8) DEFAULT '0.00000000',
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_commandedet_fk_commande` (`fk_commande`),
  KEY `idx_commandedet_fk_product` (`fk_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Volcado de datos para la tabla `llx_commandedet`
--

INSERT INTO `llx_commandedet` (`rowid`, `fk_commande`, `fk_parent_line`, `fk_product`, `label`, `description`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `qty`, `remise_percent`, `remise`, `fk_remise_except`, `price`, `subprice`, `total_ht`, `total_tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `info_bits`, `fk_product_fournisseur_price`, `buy_price_ht`, `special_code`, `rang`, `import_key`) VALUES
(2, 1, NULL, NULL, NULL, 'torta de moca y sandia', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 12, 12.00000000, 1200.00000000, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 2, NULL),
(3, 1, NULL, 1, NULL, '(País de origen: Bolivia)', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, 4, 4.00000000, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 3, NULL),
(4, 1, NULL, NULL, NULL, 'torta de pisos, 1er piso es con puca, 2do es con nubes, 4 redondas que sean con su carita, buscar de internet , mandara por correo la foto al correo de la empresa.', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 12, 12.00000000, 1200.00000000, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 4, NULL),
(5, 3, NULL, NULL, NULL, 'Prueba de registro de un producto nuevo', 13.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 10, 10.00000000, 10.00000000, 1.30000000, 0.00000000, 0.00000000, 11.30000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(6, 4, NULL, NULL, NULL, 'cupcakes', 13.000, 0.000, NULL, 0.000, NULL, 200, 0, 0, NULL, 10, 10.00000000, 2000.00000000, 260.00000000, 0.00000000, 0.00000000, 2260.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(7, 4, NULL, 29, NULL, 'Torta mediana Especial\nprueba', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, 100, 100.00000000, 1000.00000000, 0.00000000, 0.00000000, 0.00000000, 1000.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 2, NULL),
(8, 6, NULL, 219, NULL, 'cupcakes', 13.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 10, 10.00000000, 1000.00000000, 130.00000000, 0.00000000, 0.00000000, 1130.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(9, 6, NULL, 46, NULL, 'Torta Grande Especial', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 2, NULL),
(10, 7, NULL, 220, NULL, 'torta de 15 anhos fondant', 13.000, 0.000, NULL, 0.000, NULL, 200, 0, 0, NULL, 100, 100.00000000, 20000.00000000, 2600.00000000, 0.00000000, 0.00000000, 22600.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(11, 8, NULL, 188, NULL, 'Lomito', 15.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(12, 9, NULL, 1560, NULL, 'Galletas\nes una prueba con el ramiro', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 12, 12.00000000, 1200.00000000, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(13, 10, NULL, 207, NULL, 'Torta mediana', 65.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(14, 11, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(15, 12, NULL, 1560, NULL, 'Galletas', 13.000, 0.000, NULL, 0.000, NULL, 1000, 0, 0, NULL, 12, 12.00000000, 12000.00000000, 1560.00000000, 0.00000000, 0.00000000, 13560.00000000, 0, NULL, NULL, 0, NULL, 1000.00000000, 0, 1, NULL),
(16, 14, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 100.00000000, 0, 1, NULL),
(17, 15, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(18, 16, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(19, 17, NULL, NULL, NULL, 'torta conrato', 13.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3333, 3333.00000000, 3333.00000000, 433.29000000, 0.00000000, 0.00000000, 3766.29000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(20, 18, NULL, 1726, NULL, 'torta de cumpleanios', 13.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 1425, 1425.00000000, 2850.00000000, 370.50000000, 0.00000000, 0.00000000, 3220.50000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(21, 19, NULL, NULL, NULL, 'torta', 13.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 1000, 1000.00000000, 1000.00000000, 130.00000000, 0.00000000, 0.00000000, 1130.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(22, 20, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(23, 21, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(24, 21, NULL, 38, NULL, 'Empanadas\ncon harto quesito, entregar en planta', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 2, NULL),
(25, 22, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(26, 23, NULL, 1500, NULL, 'Torta Helada de Leche', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL),
(27, 24, NULL, 43, NULL, 'Torta Mediana', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande_fournisseur`
--

CREATE TABLE IF NOT EXISTS `llx_commande_fournisseur` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) DEFAULT NULL,
  `ref_supplier` varchar(30) DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_approve` datetime DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_approve` int(11) DEFAULT NULL,
  `source` smallint(6) NOT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `amount_ht` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `fk_input_method` int(11) DEFAULT '0',
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_commande_fournisseur_ref` (`ref`,`fk_soc`,`entity`),
  KEY `idx_commande_fournisseur_fk_soc` (`fk_soc`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `llx_commande_fournisseur`
--

INSERT INTO `llx_commande_fournisseur` (`rowid`, `ref`, `entity`, `ref_ext`, `ref_supplier`, `fk_soc`, `fk_projet`, `tms`, `date_creation`, `date_valid`, `date_approve`, `date_commande`, `fk_user_author`, `fk_user_valid`, `fk_user_approve`, `source`, `fk_statut`, `amount_ht`, `remise_percent`, `remise`, `tva`, `localtax1`, `localtax2`, `total_ht`, `total_ttc`, `note`, `note_public`, `model_pdf`, `fk_cond_reglement`, `fk_mode_reglement`, `fk_input_method`, `import_key`, `extraparams`, `date_livraison`) VALUES
(1, 'CF1304-0001', 1, NULL, NULL, 5, 0, '2013-06-01 16:12:01', '2013-04-06 12:43:25', '2013-06-01 11:55:54', '2013-06-01 11:56:10', '2013-06-01', 1, 1, 1, 0, 4, 0, 0, 0, 1300.00000000, 0.00000000, 0.00000000, 10000.00000000, 11300.00000000, NULL, NULL, 'muscadet', NULL, NULL, 4, NULL, NULL, NULL),
(2, 'CF1304-0002', 1, NULL, NULL, 5, 0, '2013-04-18 16:14:45', '2013-04-06 12:45:54', '2013-04-06 12:48:48', '2013-04-06 12:48:54', '2013-04-06', 1, 1, 1, 0, 4, 0, 0, 0, 1150.44000000, 0.00000000, 0.00000000, 8849.56000000, 10000.00000000, NULL, NULL, 'muscadet', NULL, NULL, 1, NULL, NULL, NULL),
(3, '(PROV3)', 1, NULL, NULL, 5, 0, '2013-04-06 16:52:14', '2013-04-06 12:52:14', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(4, 'CF1304-0003', 1, NULL, NULL, 5, 0, '2013-04-06 16:58:33', '2013-04-06 12:57:07', '2013-04-06 12:58:07', '2013-04-06 12:58:19', '2013-04-06', 1, 1, 1, 0, 3, 0, 0, 0, 11504.43000000, 0.00000000, 0.00000000, 88495.58000000, 100000.01000000, NULL, NULL, 'muscadet', NULL, NULL, 2, NULL, NULL, NULL),
(5, '(PROV5)', 1, NULL, NULL, 5, 0, '2013-04-11 00:18:11', '2013-04-10 20:18:11', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(6, '(PROV6)', 1, NULL, NULL, 5, 0, '2013-04-11 22:39:13', '2013-04-11 18:39:13', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(7, 'CF1304-0004', 1, NULL, NULL, 5, 0, '2013-04-18 16:19:45', '2013-04-18 12:16:43', '2013-04-18 12:18:21', '2013-04-18 12:18:49', '2013-04-18', 1, 1, 1, 0, 4, 0, 0, 0, 39.00000000, 0.00000000, 0.00000000, 300.00000000, 339.00000000, NULL, NULL, 'muscadet', NULL, NULL, -1, NULL, NULL, NULL),
(8, '(PROV8)', 1, NULL, NULL, 7, 0, '2013-04-19 00:00:26', '2013-04-18 20:00:26', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(10, '(PROV10)', 1, NULL, NULL, 5, 0, '2013-04-19 00:02:58', '2013-04-18 20:02:58', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(11, 'CF1304-0005', 1, NULL, NULL, 7, 0, '2013-04-19 18:16:26', '2013-04-19 14:10:43', '2013-04-19 14:12:17', '2013-04-19 14:15:46', '2013-04-19', 1, 1, 1, 0, 4, 0, 0, 0, 19.50000000, 0.00000000, 0.00000000, 150.00000000, 169.50000000, NULL, NULL, 'muscadet', NULL, NULL, -1, NULL, NULL, NULL),
(12, 'CF1304-0006', 1, NULL, NULL, 5, 0, '2013-04-19 18:53:02', '2013-04-19 14:51:15', '2013-04-19 14:52:03', '2013-04-19 14:52:26', '2013-04-19', 1, 1, 1, 0, 3, 0, 0, 0, 2392.00000000, 0.00000000, 0.00000000, 18400.00000000, 20792.00000000, NULL, NULL, 'muscadet', NULL, NULL, 4, NULL, NULL, NULL),
(13, 'CF1304-0007', 1, NULL, NULL, 7, 0, '2013-04-19 18:59:54', '2013-04-19 14:57:20', '2013-04-19 14:58:34', '2013-04-19 14:58:58', '2013-04-19', 1, 1, 1, 0, 3, 0, 0, 0, 19500.00000000, 0.00000000, 0.00000000, 150000.00000000, 169500.00000000, NULL, NULL, 'muscadet', NULL, NULL, 4, NULL, NULL, NULL),
(14, '(PROV14)', 1, NULL, NULL, 47, 0, '2013-05-15 16:02:48', '2013-05-15 12:02:48', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(15, '(PROV15)', 1, NULL, NULL, 59, 0, '2013-05-25 17:25:17', '2013-05-25 13:25:17', NULL, NULL, NULL, 1, NULL, NULL, 0, 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 'muscadet', NULL, NULL, 0, NULL, NULL, NULL),
(16, 'CF1306-0008', 1, NULL, NULL, 47, 0, '2013-06-01 16:18:47', '2013-06-01 12:16:51', '2013-06-01 12:18:07', '2013-06-01 12:18:12', '2013-06-01', 1, 1, 1, 0, 4, 0, 0, 0, 65.00000000, 0.00000000, 0.00000000, 500.00000000, 565.00000000, NULL, NULL, 'muscadet', NULL, NULL, 4, NULL, NULL, NULL),
(17, 'CF1306-0009', 1, NULL, NULL, 47, 0, '2013-06-01 16:22:42', '2013-06-01 12:21:31', '2013-06-01 12:21:50', '2013-06-01 12:21:54', '2013-06-01', 1, 1, 1, 0, 5, 0, 0, 0, 97.50000000, 0.00000000, 0.00000000, 750.00000000, 847.50000000, NULL, NULL, 'muscadet', NULL, NULL, 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande_fournisseurdet`
--

CREATE TABLE IF NOT EXISTS `llx_commande_fournisseurdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `ref` varchar(50) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `llx_commande_fournisseurdet`
--

INSERT INTO `llx_commande_fournisseurdet` (`rowid`, `fk_commande`, `fk_product`, `ref`, `label`, `description`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `qty`, `remise_percent`, `remise`, `subprice`, `total_ht`, `total_tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `info_bits`, `import_key`) VALUES
(1, 1, NULL, '', '', 'jalea', 13.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, 100.00000000, 10000.00000000, 1300.00000000, 0.00000000, 0.00000000, 11300.00000000, 0, NULL, NULL, 0, NULL),
(2, 2, 1253, '897', 'colorante rosado electrico 383 gr/unidad', 'colorante rosado electrico 383 gr/unidad', 13.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, 88.49558000, 8849.56000000, 1150.44000000, 0.00000000, 0.00000000, 10000.00000000, 0, NULL, NULL, 0, NULL),
(3, 4, 1253, '897', 'colorante rosado electrico 383 gr/unidad', 'colorante rosado electrico 383 gr/unidad', 13.000, 0.000, NULL, 0.000, NULL, 1000, 0, 0, 88.49558000, 88495.58000000, 11504.43000000, 0.00000000, 0.00000000, 100000.01000000, 0, NULL, NULL, 0, NULL),
(4, 7, 1271, 'adsa', 'Coca cola 350 cc', 'Coca cola 350 cc', 13.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, 3.00000000, 300.00000000, 39.00000000, 0.00000000, 0.00000000, 339.00000000, 0, NULL, NULL, 0, NULL),
(5, 11, 1114, 'har321', 'HARINA Trigal', 'Harina  TRIGAL qq', 13.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, 150.00000000, 150.00000000, 19.50000000, 0.00000000, 0.00000000, 169.50000000, 0, NULL, NULL, 0, NULL),
(6, 12, 1114, '35', 'HARINA Trigal', 'Harina  TRIGAL qq', 13.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, 184.00000000, 18400.00000000, 2392.00000000, 0.00000000, 0.00000000, 20792.00000000, 0, NULL, NULL, 0, NULL),
(7, 13, 1114, 'har321', 'HARINA Trigal', 'Harina  TRIGAL qq', 13.000, 0.000, NULL, 0.000, NULL, 1000, 0, 0, 150.00000000, 150000.00000000, 19500.00000000, 0.00000000, 0.00000000, 169500.00000000, 0, NULL, NULL, 0, NULL),
(8, 16, NULL, '', '', 'harina especial Kg25', 13.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, 25.00000000, 500.00000000, 65.00000000, 0.00000000, 0.00000000, 565.00000000, 0, NULL, NULL, 0, NULL),
(9, 17, 1115, 'harina', 'Har Tri 5', 'Harina  TRIGAL 5 kgs', 13.000, 0.000, NULL, 0.000, NULL, 30, 0, 0, 25.00000000, 750.00000000, 97.50000000, 0.00000000, 0.00000000, 847.50000000, 0, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande_fournisseur_dispatch`
--

CREATE TABLE IF NOT EXISTS `llx_commande_fournisseur_dispatch` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `qty` float DEFAULT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_commande_fournisseur_dispatch_fk_commande` (`fk_commande`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_commande_fournisseur_dispatch`
--

INSERT INTO `llx_commande_fournisseur_dispatch` (`rowid`, `fk_commande`, `fk_product`, `qty`, `fk_entrepot`, `fk_user`, `datec`) VALUES
(1, 12, 1114, 100, 13, 1, '2013-04-19 14:53:37'),
(2, 13, 1114, 800, 13, 1, '2013-04-19 15:00:48'),
(3, 13, 1114, 200, 2, 1, '2013-04-19 15:00:59'),
(4, 17, 1115, 30, 13, 1, '2013-06-01 12:22:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande_fournisseur_log`
--

CREATE TABLE IF NOT EXISTS `llx_commande_fournisseur_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datelog` datetime NOT NULL,
  `fk_commande` int(11) NOT NULL,
  `fk_statut` smallint(6) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

--
-- Volcado de datos para la tabla `llx_commande_fournisseur_log`
--

INSERT INTO `llx_commande_fournisseur_log` (`rowid`, `tms`, `datelog`, `fk_commande`, `fk_statut`, `fk_user`, `comment`) VALUES
(1, '2013-04-06 16:43:25', '2013-04-06 12:43:25', 1, 0, 1, NULL),
(2, '2013-04-06 16:44:27', '2013-04-06 12:44:27', 1, 1, 1, NULL),
(3, '2013-04-06 16:45:54', '2013-04-06 12:45:54', 2, 0, 1, NULL),
(4, '2013-04-06 16:48:48', '2013-04-06 12:48:48', 2, 1, 1, NULL),
(5, '2013-04-06 16:48:54', '2013-04-06 12:48:54', 2, 2, 1, NULL),
(6, '2013-04-06 16:49:07', '2013-04-06 00:00:00', 2, 3, 1, NULL),
(7, '2013-04-06 16:52:14', '2013-04-06 12:52:14', 3, 0, 1, NULL),
(8, '2013-04-06 16:57:07', '2013-04-06 12:57:07', 4, 0, 1, NULL),
(9, '2013-04-06 16:58:07', '2013-04-06 12:58:07', 4, 1, 1, NULL),
(10, '2013-04-06 16:58:19', '2013-04-06 12:58:19', 4, 2, 1, NULL),
(11, '2013-04-06 16:58:33', '2013-04-06 00:00:00', 4, 3, 1, 'none'),
(12, '2013-04-11 00:18:11', '2013-04-10 20:18:11', 5, 0, 1, NULL),
(13, '2013-04-11 22:39:13', '2013-04-11 18:39:13', 6, 0, 1, NULL),
(14, '2013-04-18 16:13:56', '2013-04-18 00:00:00', 2, 4, 1, NULL),
(15, '2013-04-18 16:14:38', '2013-04-18 00:00:00', 2, 5, 1, NULL),
(16, '2013-04-18 16:16:43', '2013-04-18 12:16:43', 7, 0, 1, NULL),
(17, '2013-04-18 16:18:21', '2013-04-18 12:18:21', 7, 1, 1, NULL),
(18, '2013-04-18 16:18:49', '2013-04-18 12:18:49', 7, 2, 1, NULL),
(19, '2013-04-18 16:19:14', '2013-04-18 00:00:00', 7, 3, 1, NULL),
(20, '2013-04-18 16:19:45', '2013-04-18 00:00:00', 7, 4, 1, NULL),
(21, '2013-04-19 00:00:26', '2013-04-18 20:00:26', 8, 0, 1, NULL),
(22, '2013-04-19 00:02:13', '2013-04-18 20:02:13', 9, 0, 1, NULL),
(23, '2013-04-19 00:02:58', '2013-04-18 20:02:58', 10, 0, 1, NULL),
(24, '2013-04-19 18:10:43', '2013-04-19 14:10:43', 11, 0, 1, NULL),
(25, '2013-04-19 18:12:17', '2013-04-19 14:12:17', 11, 1, 1, NULL),
(26, '2013-04-19 18:15:46', '2013-04-19 14:15:46', 11, 2, 1, NULL),
(27, '2013-04-19 18:16:08', '2013-04-19 00:00:00', 11, 3, 1, NULL),
(28, '2013-04-19 18:16:26', '2013-04-19 00:00:00', 11, 4, 1, NULL),
(29, '2013-04-19 18:51:15', '2013-04-19 14:51:15', 12, 0, 1, NULL),
(30, '2013-04-19 18:52:03', '2013-04-19 14:52:03', 12, 1, 1, NULL),
(31, '2013-04-19 18:52:26', '2013-04-19 14:52:26', 12, 2, 1, NULL),
(32, '2013-04-19 18:53:02', '2013-04-19 00:00:00', 12, 3, 1, 'se pido en elvio por este medio jeje'),
(33, '2013-04-19 18:57:20', '2013-04-19 14:57:20', 13, 0, 1, NULL),
(34, '2013-04-19 18:58:34', '2013-04-19 14:58:34', 13, 1, 1, NULL),
(35, '2013-04-19 18:58:58', '2013-04-19 14:58:58', 13, 2, 1, NULL),
(36, '2013-04-19 18:59:54', '2013-04-19 00:00:00', 13, 3, 1, 'bla bla bla'),
(37, '2013-05-15 16:02:48', '2013-05-15 12:02:48', 14, 0, 1, NULL),
(38, '2013-05-25 17:25:17', '2013-05-25 13:25:17', 15, 0, 1, NULL),
(39, '2013-06-01 15:49:01', '2013-06-01 11:49:01', 1, 1, 1, NULL),
(40, '2013-06-01 15:55:54', '2013-06-01 11:55:54', 1, 1, 1, NULL),
(41, '2013-06-01 15:56:10', '2013-06-01 11:56:10', 1, 2, 1, NULL),
(42, '2013-06-01 15:58:43', '2013-06-01 00:00:00', 1, 3, 1, 'pedido nro 1'),
(43, '2013-06-01 16:05:02', '2013-06-01 00:00:00', 1, 5, 1, 'asdfasdf'),
(44, '2013-06-01 16:16:51', '2013-06-01 12:16:51', 16, 0, 1, NULL),
(45, '2013-06-01 16:18:07', '2013-06-01 12:18:07', 16, 1, 1, NULL),
(46, '2013-06-01 16:18:12', '2013-06-01 12:18:12', 16, 2, 1, NULL),
(47, '2013-06-01 16:18:23', '2013-06-01 00:00:00', 16, 3, 1, 'sdfasdf'),
(48, '2013-06-01 16:18:47', '2013-06-01 00:00:00', 16, 4, 1, 'dasdf'),
(49, '2013-06-01 16:21:31', '2013-06-01 12:21:31', 17, 0, 1, NULL),
(50, '2013-06-01 16:21:50', '2013-06-01 12:21:50', 17, 1, 1, NULL),
(51, '2013-06-01 16:21:54', '2013-06-01 12:21:54', 17, 2, 1, NULL),
(52, '2013-06-01 16:22:03', '2013-06-01 00:00:00', 17, 3, 1, 'ddddddddddddd'),
(53, '2013-06-01 16:22:42', '2013-06-01 00:00:00', 17, 5, 1, 'asdfasdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_commande_venta`
--

CREATE TABLE IF NOT EXISTS `llx_commande_venta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_commande` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabla de fabricacion' AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `llx_commande_venta`
--

INSERT INTO `llx_commande_venta` (`rowid`, `entity`, `fk_commande`, `fk_entrepot`) VALUES
(1, 1, 12, 5),
(2, 1, 14, 3),
(3, 1, 15, 3),
(4, 1, 16, 11),
(5, 1, 17, 11),
(6, 1, 18, 11),
(8, 1, 19, 11),
(9, 1, 21, 11),
(10, 1, 22, 11),
(11, 1, 23, 6),
(12, 1, 24, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_compta`
--

CREATE TABLE IF NOT EXISTS `llx_compta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `fk_compta_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `valid` tinyint(4) DEFAULT '0',
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_compta_account`
--

CREATE TABLE IF NOT EXISTS `llx_compta_account` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `number` varchar(12) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_compta_compte_generaux`
--

CREATE TABLE IF NOT EXISTS `llx_compta_compte_generaux` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_creation` datetime DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `numero` (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_const`
--

CREATE TABLE IF NOT EXISTS `llx_const` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `value` text NOT NULL,
  `type` varchar(6) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `note` text,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_const` (`name`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=540 ;

--
-- Volcado de datos para la tabla `llx_const`
--

INSERT INTO `llx_const` (`rowid`, `name`, `entity`, `value`, `type`, `visible`, `note`, `tms`) VALUES
(2, 'MAIN_FEATURES_LEVEL', 0, '0', 'chaine', 1, 'Level of features to show (0=stable only, 1=stable+experimental, 2=stable+experimental+development', '2013-02-19 13:21:21'),
(3, 'SYSLOG_FILE_ON', 0, '1', 'chaine', 0, 'Log to file Directory where to write log file', '2013-02-19 13:21:21'),
(4, 'SYSLOG_FILE', 0, 'DOL_DATA_ROOT/dolibarr.log', 'chaine', 0, 'Directory where to write log file', '2013-02-19 13:21:21'),
(5, 'SYSLOG_LEVEL', 0, '7', 'chaine', 0, 'Level of debug info to show', '2013-02-19 13:21:21'),
(6, 'MAIN_MAIL_SMTP_SERVER', 0, '', 'chaine', 0, 'Host or ip address for SMTP server', '2013-02-19 13:21:21'),
(7, 'MAIN_MAIL_SMTP_PORT', 0, '', 'chaine', 0, 'Port for SMTP server', '2013-02-19 13:21:21'),
(8, 'MAIN_UPLOAD_DOC', 0, '2048', 'chaine', 0, 'Max size for file upload (0 means no upload allowed)', '2013-02-19 13:21:21'),
(10, 'MAIN_MAIL_EMAIL_FROM', 1, 'dolibarr-robot@domain.com', 'chaine', 0, 'EMail emetteur pour les emails automatiques Dolibarr', '2013-02-19 13:21:21'),
(11, 'MAIN_SIZE_LISTE_LIMIT', 0, '25', 'chaine', 0, 'Longueur maximum des listes', '2013-02-19 13:21:21'),
(12, 'MAIN_SHOW_WORKBOARD', 0, '1', 'yesno', 0, 'Affichage tableau de bord de travail Dolibarr', '2013-02-19 13:21:21'),
(13, 'MAIN_MENU_STANDARD', 1, 'eldy_backoffice.php', 'chaine', 0, 'Module de gestion de la barre de menu pour utilisateurs internes', '2013-02-19 13:21:21'),
(14, 'MAIN_MENUFRONT_STANDARD', 1, 'eldy_frontoffice.php', 'chaine', 0, 'Module de gestion de la barre de menu pour utilisateurs externes', '2013-02-19 13:21:21'),
(15, 'MAIN_MENU_SMARTPHONE', 1, 'eldy_backoffice.php', 'chaine', 0, 'Module de gestion de la barre de menu smartphone pour utilisateurs internes', '2013-02-19 13:21:21'),
(16, 'MAIN_MENUFRONT_SMARTPHONE', 1, 'eldy_frontoffice.php', 'chaine', 0, 'Module de gestion de la barre de menu smartphone pour utilisateurs externes', '2013-02-19 13:21:21'),
(24, 'MAIN_DELAY_NOT_ACTIVATED_SERVICES', 1, '0', 'chaine', 0, 'Tolérance de retard avant alerte (en jours) sur services à activer', '2013-02-19 13:21:21'),
(25, 'MAIN_DELAY_RUNNING_SERVICES', 1, '0', 'chaine', 0, 'Tolérance de retard avant alerte (en jours) sur services expirés', '2013-02-19 13:21:21'),
(28, 'SOCIETE_NOLIST_COURRIER', 0, '1', 'yesno', 0, 'Liste les fichiers du repertoire courrier', '2013-02-19 13:21:21'),
(30, 'SOCIETE_CODECOMPTA_ADDON', 1, 'mod_codecompta_panicum', 'yesno', 0, 'Module to control third parties codes', '2013-02-19 13:21:21'),
(31, 'MAILING_EMAIL_FROM', 1, 'dolibarr@domain.com', 'chaine', 0, 'EMail emmetteur pour les envois d emailings', '2013-02-19 13:21:21'),
(33, 'MAIN_VERSION_LAST_INSTALL', 0, '3.2.3', 'chaine', 0, 'Dolibarr version when install', '2013-02-19 13:28:28'),
(45, 'MAIN_INFO_SOCIETE_LOGO', 1, 'michelline.jpg', 'chaine', 0, '', '2013-02-19 13:38:13'),
(46, 'MAIN_INFO_SOCIETE_LOGO_SMALL', 1, 'michelline_small.jpg', 'chaine', 0, '', '2013-02-19 13:38:13'),
(47, 'MAIN_INFO_SOCIETE_LOGO_MINI', 1, 'michelline_mini.jpg', 'chaine', 0, '', '2013-02-19 13:38:13'),
(54, 'COMPANY_ADDON_PDF_ODT_PATH', 1, 'DOL_DATA_ROOT/doctemplates/thirdparties', 'chaine', 0, NULL, '2013-02-19 13:38:43'),
(56, 'COMMANDE_ADDON_PDF', 1, 'einstein', 'chaine', 0, 'Nom du gestionnaire de generation des commandes en PDF', '2013-02-19 13:38:52'),
(58, 'COMMANDE_ADDON_PDF_ODT_PATH', 1, 'DOL_DATA_ROOT/doctemplates/orders', 'chaine', 0, NULL, '2013-02-19 13:38:52'),
(61, 'FACTURE_ADDON_PDF', 1, 'crabe', 'chaine', 0, NULL, '2013-02-19 13:39:07'),
(62, 'FACTURE_ADDON', 1, 'terre', 'chaine', 0, NULL, '2013-02-19 13:39:07'),
(63, 'FACTURE_ADDON_PDF_ODT_PATH', 1, 'DOL_DATA_ROOT/doctemplates/invoices', 'chaine', 0, NULL, '2013-02-19 13:39:07'),
(66, 'COMMANDE_SUPPLIER_ADDON_PDF', 1, 'muscadet', 'chaine', 0, NULL, '2013-02-19 13:39:22'),
(67, 'COMMANDE_SUPPLIER_ADDON', 1, 'mod_commande_fournisseur_muguet', 'chaine', 0, NULL, '2013-02-19 13:39:22'),
(68, 'INVOICE_SUPPLIER_ADDON_PDF', 1, 'canelle', 'chaine', 0, NULL, '2013-02-19 13:39:22'),
(74, 'MAIN_MODULE_CATEGORIE', 1, '1', NULL, 0, NULL, '2013-02-19 13:40:14'),
(76, 'MAIN_MODULE_WORKFLOW', 1, '1', NULL, 0, NULL, '2013-02-19 13:58:22'),
(78, 'PROPALE_ADDON_PDF', 1, 'azur', 'chaine', 0, 'Nom du gestionnaire de generation des propales en PDF', '2013-02-19 14:01:35'),
(79, 'PROPALE_ADDON', 1, 'mod_propale_marbre', 'chaine', 0, 'Nom du gestionnaire de numerotation des propales', '2013-02-19 14:01:35'),
(80, 'PROPALE_VALIDITY_DURATION', 1, '15', 'chaine', 0, 'Duration of validity of business proposals', '2013-02-19 14:01:35'),
(81, 'PROPALE_ADDON_PDF_ODT_PATH', 1, 'DOL_DATA_ROOT/doctemplates/proposals', 'chaine', 0, NULL, '2013-02-19 14:01:35'),
(86, 'EXPEDITION_ADDON_PDF', 1, 'rouget', 'chaine', 0, 'Nom du gestionnaire de generation des bons expeditions en PDF', '2013-02-19 14:01:50'),
(87, 'EXPEDITION_ADDON', 1, 'elevement', 'chaine', 0, 'Nom du gestionnaire du type d''expedition', '2013-02-19 14:01:50'),
(88, 'LIVRAISON_ADDON_PDF', 1, 'typhon', 'chaine', 0, 'Nom du gestionnaire de generation des bons de reception en PDF', '2013-02-19 14:01:50'),
(89, 'LIVRAISON_ADDON', 1, 'mod_livraison_jade', 'chaine', 0, 'Nom du gestionnaire de numerotation des bons de reception', '2013-02-19 14:01:50'),
(90, 'EXPEDITION_ADDON_NUMBER', 1, 'mod_expedition_safor', 'chaine', 0, 'Nom du gestionnaire de numerotation des expeditions', '2013-02-19 14:01:50'),
(93, 'MAIN_MODULE_COMPTABILITE', 1, '1', NULL, 0, NULL, '2013-02-19 14:02:00'),
(98, 'MAIN_MODULE_TAX', 1, '1', NULL, 0, NULL, '2013-02-19 14:03:02'),
(100, 'MAIN_MODULE_EXPORT', 1, '1', NULL, 0, NULL, '2013-02-19 14:03:57'),
(101, 'MAIN_MODULE_IMPORT', 1, '1', NULL, 0, NULL, '2013-02-19 14:04:15'),
(103, 'MAIN_AGENDA_ACTIONAUTO_COMPANY_CREATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(104, 'MAIN_AGENDA_ACTIONAUTO_CONTRACT_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(105, 'MAIN_AGENDA_ACTIONAUTO_PROPAL_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(106, 'MAIN_AGENDA_ACTIONAUTO_PROPAL_SENTBYMAIL', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(107, 'MAIN_AGENDA_ACTIONAUTO_ORDER_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(108, 'MAIN_AGENDA_ACTIONAUTO_ORDER_SENTBYMAIL', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(109, 'MAIN_AGENDA_ACTIONAUTO_BILL_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(110, 'MAIN_AGENDA_ACTIONAUTO_BILL_PAYED', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(111, 'MAIN_AGENDA_ACTIONAUTO_BILL_CANCEL', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(112, 'MAIN_AGENDA_ACTIONAUTO_BILL_SENTBYMAIL', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(113, 'MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(114, 'MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(115, 'MAIN_AGENDA_ACTIONAUTO_SHIPPING_VALIDATE', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(116, 'MAIN_AGENDA_ACTIONAUTO_SHIPPING_SENTBYMAIL', 1, '1', 'chaine', 0, NULL, '2013-02-19 14:05:01'),
(122, 'PRODUCT_CODEPRODUCT_ADDON', 1, 'mod_codeproduct_leopard', 'yesno', 0, 'Module to control product codes', '2013-02-25 14:43:03'),
(123, 'SYSLOG_HANDLERS', 0, '["mod_syslog_file"]', NULL, 0, NULL, '2013-02-25 14:43:35'),
(141, 'CASHDESK_ID_THIRDPARTY', 1, '1', 'chaine', 0, '', '2013-03-01 23:51:03'),
(142, 'CASHDESK_ID_BANKACCOUNT_CASH', 1, '1', 'chaine', 0, '', '2013-03-01 23:51:03'),
(143, 'CASHDESK_ID_BANKACCOUNT_CHEQUE', 1, '3', 'chaine', 0, '', '2013-03-01 23:51:03'),
(144, 'CASHDESK_ID_BANKACCOUNT_CB', 1, '3', 'chaine', 0, '', '2013-03-01 23:51:03'),
(145, 'CASHDESK_ID_WAREHOUSE', 1, '1', 'chaine', 0, '', '2013-03-01 23:51:03'),
(146, 'CASHDESK_SERVICES', 1, '0', 'chaine', 0, '', '2013-03-01 23:51:03'),
(151, 'PRODUIT_DESC_IN_FORM', 1, '1', 'chaine', 0, '', '2013-03-02 16:57:10'),
(152, 'PRODUIT_USE_SEARCH_TO_SELECT', 1, '2', 'chaine', 0, '', '2013-03-02 16:58:26'),
(153, 'PRODUIT_MULTIPRICES', 1, '1', 'chaine', 0, '', '2013-03-02 16:58:46'),
(165, 'FICHEINTER_ADDON_PDF', 1, 'soleil', 'chaine', 0, NULL, '2013-03-06 21:39:50'),
(166, 'FICHEINTER_ADDON', 1, 'pacific', 'chaine', 0, NULL, '2013-03-06 21:39:50'),
(170, 'MAIN_MODULE_STOCK', 1, '1', NULL, 0, NULL, '2013-03-06 22:04:28'),
(172, 'MAIN_REMOVE_INSTALL_WARNING', 1, '1', 'chaine', 1, '', '2013-03-06 22:08:22'),
(204, 'MAIN_SUBMODULE_EXPEDITION', 1, '1', 'chaine', 0, '', '2013-04-10 23:52:14'),
(205, 'MAIN_SUBMODULE_LIVRAISON', 1, '1', 'chaine', 0, '', '2013-04-10 23:52:14'),
(243, 'MAIN_MODULE_MARGIN', 1, '1', NULL, 0, NULL, '2013-04-13 14:28:54'),
(244, 'MAIN_MODULE_MARGIN_TABS_0', 1, 'product:+margin:Margins:margins:$conf->margin->enabled:/margin/tabs/productMargins.php?id=__ID__', 'chaine', 0, NULL, '2013-04-13 14:28:54'),
(245, 'MAIN_MODULE_MARGIN_TABS_1', 1, 'thirdparty:+margin:Margins:margins:$conf->margin->enabled:/margin/tabs/thirdpartyMargins.php?socid=__ID__', 'chaine', 0, NULL, '2013-04-13 14:28:54'),
(246, 'MAIN_MODULE_PROPALE', 1, '1', NULL, 0, NULL, '2013-04-13 14:28:54'),
(249, 'MAIN_MODULE_SYSLOG', 0, '1', NULL, 0, NULL, '2013-04-13 14:29:08'),
(271, 'MAIN_MODULE_BOOKMARK', 1, '1', NULL, 0, NULL, '2013-04-27 20:20:44'),
(295, 'COMPANY_USE_SEARCH_TO_SELECT', 1, '3', 'chaine', 0, '', '2013-05-04 20:13:45'),
(297, 'COMPANY_ELEPHANT_MASK_CUSTOMER', 1, 'CLI{00000}', 'chaine', 0, '', '2013-05-04 20:26:11'),
(298, 'COMPANY_ELEPHANT_MASK_SUPPLIER', 1, 'PROV{00000}', 'chaine', 0, '', '2013-05-04 20:26:11'),
(299, 'SOCIETE_CODECLIENT_ADDON', 1, 'mod_codeclient_monkey', 'chaine', 0, '', '2013-05-04 20:33:18'),
(300, 'MAIN_MODULE_HOLIDAY', 1, '1', NULL, 0, NULL, '2013-05-04 23:59:34'),
(301, 'MAIN_MODULE_HOLIDAY_TABS_0', 1, 'user:+paidholidays:CPTitreMenu:holiday:$user->rights->holiday->write:/holiday/index.php?mainmenu=holiday&id=__ID__', 'chaine', 0, NULL, '2013-05-04 23:59:34'),
(302, 'MAIN_MODULE_ADHERENT', 1, '1', NULL, 0, NULL, '2013-05-04 23:59:37'),
(303, 'ADHERENT_MAIL_RESIL', 1, 'Votre adhésion vient d''être résiliée.\r\nNous espérons vous revoir très bientôt', 'texte', 0, 'Mail de résiliation', '2013-05-04 23:59:37'),
(304, 'ADHERENT_MAIL_VALID', 1, 'Votre adhésion vient d''être validée. \r\nVoici le rappel de vos coordonnées (toute information erronée entrainera la non validation de votre inscription) :\r\n\r\n%INFOS%\r\n\r\n', 'texte', 0, 'Mail de validation', '2013-05-04 23:59:37'),
(305, 'ADHERENT_MAIL_VALID_SUBJECT', 1, 'Votre adhésion a été validée', 'chaine', 0, 'Sujet du mail de validation', '2013-05-04 23:59:37'),
(306, 'ADHERENT_MAIL_RESIL_SUBJECT', 1, 'Résiliation de votre adhésion', 'chaine', 0, 'Sujet du mail de résiliation', '2013-05-04 23:59:37'),
(307, 'ADHERENT_MAIL_FROM', 1, '', 'chaine', 0, 'From des mails', '2013-05-04 23:59:37'),
(308, 'ADHERENT_MAIL_COTIS', 1, 'Bonjour %PRENOM%,\r\nCet email confirme que votre cotisation a été reçue\r\net enregistrée', 'texte', 0, 'Mail de validation de cotisation', '2013-05-04 23:59:37'),
(309, 'ADHERENT_MAIL_COTIS_SUBJECT', 1, 'Reçu de votre cotisation', 'chaine', 0, 'Sujet du mail de validation de cotisation', '2013-05-04 23:59:37'),
(310, 'ADHERENT_CARD_HEADER_TEXT', 1, '%ANNEE%', 'chaine', 0, 'Texte imprimé sur le haut de la carte adhérent', '2013-05-04 23:59:37'),
(311, 'ADHERENT_CARD_FOOTER_TEXT', 1, 'Association AZERTY', 'chaine', 0, 'Texte imprimé sur le bas de la carte adhérent', '2013-05-04 23:59:37'),
(312, 'ADHERENT_CARD_TEXT', 1, '%FULLNAME%\r\nID: %ID%\r\n%EMAIL%\r\n%ADDRESS%\r\n%ZIP% %TOWN%\r\n%COUNTRY%', 'texte', 0, 'Text to print on member cards', '2013-05-04 23:59:37'),
(313, 'ADHERENT_MAILMAN_ADMINPW', 1, '', 'chaine', 0, 'Mot de passe Admin des liste mailman', '2013-05-04 23:59:37'),
(314, 'ADHERENT_BANK_USE_AUTO', 1, '', 'yesno', 0, 'Insertion automatique des cotisations dans le compte banquaire', '2013-05-04 23:59:37'),
(315, 'ADHERENT_BANK_ACCOUNT', 1, '', 'chaine', 0, 'ID du Compte banquaire utilise', '2013-05-04 23:59:37'),
(316, 'ADHERENT_BANK_CATEGORIE', 1, '', 'chaine', 0, 'ID de la catégorie banquaire des cotisations', '2013-05-04 23:59:37'),
(318, 'ADHERENT_ETIQUETTE_TEXT', 1, '%FULLNAME%\n%ADDRESS%\n%ZIP% %TOWN%\n%COUNTRY%', 'texte', 0, 'Text to print on member address sheets', '2013-05-04 23:59:37'),
(320, 'COMMANDE_ADDON', 1, 'mod_commande_marbre', 'chaine', 0, '', '2013-05-14 19:09:10'),
(325, 'ALMACEN_ADDON', 1, 'mod_almacen_ubuntubo', 'chaine', 0, '', '2013-05-15 17:10:13'),
(326, 'FABRICATION_ADDON', 1, 'mod_fabrication_ubuntubo', 'chaine', 0, '', '2013-05-15 17:10:30'),
(336, 'PRODUIT_SOUSPRODUITS', 1, '0', 'chaine', 0, '', '2013-05-18 17:46:55'),
(337, 'MAIN_MODULE_PROJET', 1, '1', NULL, 0, NULL, '2013-05-18 18:07:43'),
(338, 'PROJECT_ADDON_PDF', 1, 'baleine', 'chaine', 0, 'Nom du gestionnaire de generation des projets en PDF', '2013-05-18 18:07:43'),
(339, 'PROJECT_ADDON', 1, 'mod_project_simple', 'chaine', 0, 'Nom du gestionnaire de numerotation des projets', '2013-05-18 18:07:43'),
(355, 'ADHERENT_ETIQUETTE_TYPE', 1, '5160', '', 0, 'Type of address sheets', '2013-05-25 15:26:02'),
(356, 'ADHERENT_CARD_TYPE', 1, 'CARD', '', 0, '', '2013-05-25 15:26:09'),
(439, 'MAIN_DELAY_ACTIONS_TODO', 1, '7', 'chaine', 0, '', '2013-05-25 16:13:03'),
(440, 'MAIN_DELAY_PROPALS_TO_CLOSE', 1, '31', 'chaine', 0, '', '2013-05-25 16:13:03'),
(441, 'MAIN_DELAY_PROPALS_TO_BILL', 1, '7', 'chaine', 0, '', '2013-05-25 16:13:03'),
(442, 'MAIN_DELAY_ORDERS_TO_PROCESS', 1, '2', 'chaine', 0, '', '2013-05-25 16:13:03'),
(443, 'MAIN_DELAY_CUSTOMER_BILLS_UNPAYED', 1, '31', 'chaine', 0, '', '2013-05-25 16:13:03'),
(444, 'MAIN_DELAY_SUPPLIER_ORDERS_TO_PROCESS', 1, '7', 'chaine', 0, '', '2013-05-25 16:13:03'),
(445, 'MAIN_DELAY_SUPPLIER_BILLS_TO_PAY', 1, '2', 'chaine', 0, '', '2013-05-25 16:13:03'),
(446, 'MAIN_DELAY_TRANSACTIONS_TO_CONCILIATE', 1, '62', 'chaine', 0, '', '2013-05-25 16:13:03'),
(447, 'MAIN_DELAY_MEMBERS', 1, '31', 'chaine', 0, '', '2013-05-25 16:13:03'),
(448, 'MAIN_DISABLE_METEO', 1, '0', 'chaine', 0, '', '2013-05-25 16:13:03'),
(450, 'MAIN_USE_ADVANCED_PERMS', 1, '1', 'chaine', 0, '', '2013-05-25 16:14:21'),
(451, 'MAIN_SESSION_TIMEOUT', 1, '1440', 'chaine', 0, '', '2013-05-25 16:14:48'),
(452, 'PRODUIT_MULTIPRICES_LIMIT', 1, '3', 'chaine', 0, '', '2013-05-25 16:15:36'),
(453, 'MAIN_LOGEVENTS_USER_LOGIN', 1, '1', 'chaine', 0, '', '2013-05-25 16:19:33'),
(454, 'MAIN_LOGEVENTS_USER_LOGOUT', 1, '1', 'chaine', 0, '', '2013-05-25 16:19:33'),
(455, 'MAIN_PDF_FORMAT', 1, 'USLetter', 'chaine', 0, '', '2013-05-25 16:21:35'),
(456, 'MAIN_PROFID1_IN_ADDRESS', 1, '0', 'chaine', 0, '', '2013-05-25 16:21:35'),
(457, 'MAIN_PROFID2_IN_ADDRESS', 1, '0', 'chaine', 0, '', '2013-05-25 16:21:35'),
(458, 'MAIN_PROFID3_IN_ADDRESS', 1, '0', 'chaine', 0, '', '2013-05-25 16:21:35'),
(459, 'MAIN_PROFID4_IN_ADDRESS', 1, '0', 'chaine', 0, '', '2013-05-25 16:21:35'),
(460, 'MAIN_GENERATE_DOCUMENTS_WITHOUT_VAT', 1, '1', 'chaine', 0, '', '2013-05-25 16:21:35'),
(463, 'MAIN_MODULE_AGENDA', 1, '1', NULL, 0, NULL, '2013-06-01 13:13:13'),
(465, 'MAIN_MODULE_SERVICE', 1, '1', NULL, 0, NULL, '2013-06-01 13:13:14'),
(468, 'MAIN_MODULE_FOURNISSEUR', 1, '1', NULL, 0, NULL, '2013-06-01 13:13:14'),
(469, 'MAIN_MODULE_USER', 0, '1', NULL, 0, NULL, '2013-06-01 13:13:14'),
(470, 'MAIN_MODULE_DEPLACEMENT', 1, '1', NULL, 0, NULL, '2013-06-01 13:13:14'),
(471, 'MAIN_MODULE_ECM', 1, '1', NULL, 0, NULL, '2013-06-01 13:13:14'),
(472, 'MAIN_VERSION_LAST_UPGRADE', 0, '3.3.1', 'chaine', 0, 'Dolibarr version for last upgrade', '2013-06-01 13:13:22'),
(474, 'MAIN_MODULE_FABRICATION', 1, '1', NULL, 0, NULL, '2013-06-01 13:18:11'),
(475, 'MAIN_MODULE_FABRICATION_TABS_0', 1, 'product:+product:Material:@fabrication:$user->rights->fabrication->crearlistproduct:/fabrication/productlist/fiche.php?id=__ID__', 'chaine', 0, NULL, '2013-06-01 13:18:11'),
(478, 'MAIN_MODULE_VENTAS', 1, '1', NULL, 0, NULL, '2013-06-01 13:18:53'),
(479, 'MAIN_MODULE_BANQUE', 1, '1', NULL, 0, NULL, '2013-06-01 13:18:53'),
(480, 'MAIN_MODULE_FACTURE', 1, '1', NULL, 0, NULL, '2013-06-01 13:18:53'),
(482, 'MAIN_MODULE_PRODUCT', 1, '1', NULL, 0, NULL, '2013-06-01 13:18:53'),
(497, 'MAIN_INFO_SOCIETE_PAYS', 1, '52:BO:Bolivia', 'chaine', 0, '', '2013-06-01 15:55:30'),
(498, 'MAIN_INFO_SOCIETE_NOM', 1, 'Pasteleria Michelline', 'chaine', 0, '', '2013-06-01 15:55:31'),
(499, 'MAIN_INFO_SOCIETE_DEPARTEMENT', 1, '0', 'chaine', 0, '', '2013-06-01 15:55:31'),
(500, 'MAIN_MONNAIE', 1, 'Bs.', 'chaine', 0, '', '2013-06-01 15:55:31'),
(501, 'MAIN_INFO_SOCIETE_FORME_JURIDIQUE', 1, '0', 'chaine', 0, '', '2013-06-01 15:55:31'),
(502, 'SOCIETE_FISCAL_MONTH_START', 1, '1', 'chaine', 0, '', '2013-06-01 15:55:31'),
(503, 'FACTURE_TVAOPTION', 1, 'franchise', 'chaine', 0, '', '2013-06-01 15:55:31'),
(504, 'MAIN_MODULE_FICHEINTER', 1, '1', NULL, 0, NULL, '2013-06-01 16:19:11'),
(506, 'MAIN_MODULE_EXPEDITION', 1, '1', NULL, 0, NULL, '2013-06-01 16:19:14'),
(507, 'MAIN_MODULE_COMMANDE', 1, '1', NULL, 0, NULL, '2013-06-01 16:19:14'),
(508, 'MAIN_MODULE_SOCIETE', 1, '1', NULL, 0, NULL, '2013-06-01 16:19:14'),
(509, 'STOCK_CALCULATE_ON_BILL', 1, '1', 'chaine', 0, '', '2013-06-01 16:19:59'),
(510, 'STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER', 1, '1', 'chaine', 0, '', '2013-06-01 16:20:02'),
(511, 'MAIN_LANG_DEFAULT', 1, 'es_ES', 'chaine', 0, '', '2013-06-01 21:40:41'),
(512, 'MAIN_MULTILANGS', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:41'),
(513, 'MAIN_SIZE_LISTE_LIMIT', 1, '25', 'chaine', 0, '', '2013-06-01 21:40:41'),
(514, 'MAIN_DISABLE_JAVASCRIPT', 1, '0', 'chaine', 0, '', '2013-06-01 21:40:41'),
(515, 'MAIN_BUTTON_HIDE_UNAUTHORIZED', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:41'),
(516, 'MAIN_START_WEEK', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:41'),
(517, 'MAIN_SHOW_LOGO', 1, '0', 'chaine', 0, '', '2013-06-01 21:40:41'),
(518, 'MAIN_FIRSTNAME_NAME_POSITION', 1, '0', 'chaine', 0, '', '2013-06-01 21:40:42'),
(519, 'MAIN_THEME', 1, 'eldy', 'chaine', 0, '', '2013-06-01 21:40:42'),
(520, 'MAIN_SEARCHFORM_CONTACT', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:42'),
(521, 'MAIN_SEARCHFORM_SOCIETE', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:42'),
(522, 'MAIN_SEARCHFORM_PRODUITSERVICE', 1, '1', 'chaine', 0, '', '2013-06-01 21:40:42'),
(523, 'MAIN_SEARCHFORM_ADHERENT', 1, '0', 'chaine', 0, '', '2013-06-01 21:40:42'),
(524, 'MAIN_HELPCENTER_DISABLELINK', 0, '1', 'chaine', 0, '', '2013-06-01 21:40:42'),
(525, 'MAIN_MOTD', 1, 'Buenos días, recuerde que mientras mas actualizada este la información del sistema mas provecho obtendrá del mismo.', 'chaine', 0, '', '2013-06-01 21:40:42'),
(526, 'MAIN_HOME', 1, 'Introduzca su Usuario y Contraseña (su contraseña podría ser el numero de su C.I)', 'chaine', 0, '', '2013-06-01 21:40:42'),
(527, 'MAIN_HELP_DISABLELINK', 0, '1', 'chaine', 0, '', '2013-06-01 21:40:42'),
(528, 'COMPTA_PRODUCT_BUY_ACCOUNT', 1, '1501', 'string', 0, '', '2013-06-01 21:54:09'),
(529, 'COMPTA_PRODUCT_SOLD_ACCOUNT', 1, '4101', 'string', 0, '', '2013-06-01 21:54:43'),
(530, 'COMPTA_ACCOUNT_SUPPLIER', 1, '2101', 'string', 0, '', '2013-06-01 21:55:34'),
(531, 'COMPTA_ACCOUNT_CUSTOMER', 1, '1301', 'string', 0, '', '2013-06-01 21:56:05'),
(532, 'MAIN_MODULE_ALMACEN', 1, '1', NULL, 0, NULL, '2013-06-01 22:05:07'),
(539, 'MAIN_MODULE_CONTAB', 1, '1', NULL, 0, NULL, '2013-06-03 00:20:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_accounting`
--

CREATE TABLE IF NOT EXISTS `llx_contab_accounting` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(40) NOT NULL,
  `entity` int(11) NOT NULL,
  `cta_class` int(1) NOT NULL,
  `cta_normal` int(1) NOT NULL,
  `cta_top` int(11) DEFAULT '0',
  `cta_name` varchar(80) NOT NULL,
  `statut` int(1) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_ref` (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

--
-- Volcado de datos para la tabla `llx_contab_accounting`
--

INSERT INTO `llx_contab_accounting` (`rowid`, `ref`, `entity`, `cta_class`, `cta_normal`, `cta_top`, `cta_name`, `statut`) VALUES
(1, '1000', 1, 1, 1, -1, 'ACTIVO', 0),
(2, '1100', 1, 1, 1, 1, 'Cajas y Bancos', 0),
(3, '1101', 1, 2, 1, 2, 'Caja', 0),
(4, '1102', 1, 2, 1, 2, 'Caja - U$S', 0),
(5, '1103', 1, 2, 1, 2, 'Moneda Extranjera', 0),
(6, '1104', 1, 2, 1, 2, 'Caja Chica', 0),
(7, '1105', 1, 2, 1, 2, 'Fondo Fijo', 0),
(8, '1106', 1, 2, 1, 2, 'Valores a depositar', 0),
(9, '1107', 1, 2, 1, 2, 'Valores al cobro', 0),
(10, '1108', 1, 2, 1, 2, 'Cheques comunes', 0),
(11, '1109', 1, 2, 1, 2, 'Cheques diferidos/Valores diferidos a depositar', 0),
(12, '1110', 1, 2, 1, 2, 'Banco Nación Arg. c/c', 0),
(13, '1111', 1, 2, 1, 2, 'Banco Pcia. Bs. As. c/c', 0),
(14, '1112', 1, 2, 1, 2, 'Banco Nación Arg. - Caja de Ahorro', 0),
(15, '1113', 1, 2, 1, 2, 'Banco Pcia. Bs. As. - Caja de Ahorro', 0),
(16, '1200', 1, 1, 1, 1, 'Inversiones', 0),
(17, '1201', 1, 2, 1, 16, 'Depósitos a plazo fijo', 0),
(18, '1202', 1, 2, 1, 16, 'Depósitos a plazo fijo - U$S', 0),
(19, '1203', 1, 2, 1, 16, 'Depósitos a plazo fijo – Euros (€)', 0),
(20, '1204', 1, 2, 1, 16, 'Títulos Públicos', 0),
(21, '1205', 1, 2, 1, 16, 'Acciones', 0),
(22, '1206', 1, 2, 1, 16, 'Cuotas sociales cooperativas', 0),
(23, '1207', 1, 2, 1, 16, 'Previsión para Desvalorizaciones y Fluctuaciones', 0),
(24, '1300', 1, 1, 1, 1, 'Créditos por Ventas', 0),
(25, '1301', 1, 2, 1, 24, 'Deudores por Ventas', 0),
(26, '1302', 1, 2, 1, 24, 'Deudores por Ventas - U$S', 0),
(27, '1303', 1, 2, 1, 24, 'Documentos a Cobrar', 0),
(28, '1304', 1, 2, 1, 24, 'Cheques diferidos', 0),
(29, '1305', 1, 2, 1, 24, 'Deudores Morosos', 0),
(30, '1307', 1, 2, 1, 24, 'Deudores en gestión judicial', 0),
(31, '1308', 1, 2, 1, 24, 'Deudores hipotecarios', 0),
(32, '1309', 1, 2, 1, 24, 'Deudores prendarios', 0),
(33, '1310', 1, 2, 1, 24, 'Letras a cobrar', 0),
(34, '1311', 1, 2, 1, 24, 'Letras a cobrar - U$S', 0),
(35, '1312', 1, 2, 1, 24, 'Deudores Morosos', 0),
(36, '1313', 1, 2, 1, 24, 'Previsión para Deudores Incobrables.', 0),
(37, '1314', 1, 2, 1, 24, 'Documentos descontados', 0),
(38, '1400', 1, 1, 1, 1, 'Otros Créditos', 0),
(39, '1401', 1, 2, 1, 38, 'Anticipo al Personal.', 0),
(40, '1402', 1, 2, 1, 38, 'Deudores Varios.', 0),
(41, '1403', 1, 2, 1, 38, 'Depósitos en garantía', 0),
(42, '1404', 1, 2, 1, 38, 'J. Roca Cta. Aporte', 0),
(43, '1405', 1, 2, 1, 38, 'A. Ratti Cta. Particular (por retiro de efvo.)', 0),
(44, '1406', 1, 2, 1, 38, 'Asociados', 0),
(45, '1407', 1, 2, 1, 38, 'Importaciones en tramite', 0),
(46, '1408', 1, 2, 1, 38, 'Adelantos por compra bienes de uso', 0),
(47, '1409', 1, 2, 1, 38, 'I.V.A. Crédito Fiscal', 0),
(48, '1410', 1, 2, 1, 38, 'Anticipos de IIBB/Convenio Multilateral', 0),
(49, '1411', 1, 2, 1, 38, 'Anticipos de Ganancias', 0),
(50, '1412', 1, 2, 1, 38, 'I.V.A. a nuestro favor', 0),
(51, '1413', 1, 2, 1, 38, 'Alquileres pagados por adelantado', 0),
(52, '1414', 1, 2, 1, 38, 'Seguros pagados por adelantado', 0),
(53, '1500', 1, 1, 1, 1, 'Bienes de Cambio', 0),
(54, '1501', 1, 2, 1, 53, 'Mercaderías', 0),
(55, '1502', 1, 2, 1, 53, 'Mercaderías gravadas', 0),
(56, '1503', 1, 2, 1, 53, 'Mercaderías exentas', 0),
(57, '1504', 1, 2, 1, 53, 'Mercaderías en transito', 0),
(58, '1505', 1, 2, 1, 53, 'Mercaderías en aduana', 0),
(59, '1506', 1, 2, 1, 53, 'Mercaderías en consignación', 0),
(60, '1507', 1, 2, 1, 53, 'Materias Primas', 0),
(61, '1508', 1, 2, 1, 53, 'Productos en proceso de elaboración', 0),
(62, '1509', 1, 2, 1, 53, 'Productos Elaborados', 0),
(63, '1510', 1, 2, 1, 53, 'Productos Terminados en depósito', 0),
(64, '1511', 1, 2, 1, 53, 'Previsión para Desvalorizaciones', 0),
(65, '1600', 1, 1, 1, 1, 'Bienes de uso', 0),
(66, '1601', 1, 2, 1, 65, 'Terrenos', 0),
(67, '1602', 1, 2, 1, 65, 'Edificios', 0),
(68, '1603', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Edificios', 0),
(69, '1604', 1, 2, 1, 65, 'Rodados', 0),
(70, '1605', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Rodados', 0),
(71, '1606', 1, 2, 1, 65, 'Muebles y Útiles', 0),
(72, '1607', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Muebles y Útiles', 0),
(73, '1608', 1, 2, 1, 65, 'Maquinarias', 0),
(74, '1609', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Maquinarias', 0),
(75, '1610', 1, 2, 1, 65, 'Instalaciones', 0),
(76, '1611', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Instalaciones', 0),
(77, '1612', 1, 2, 1, 65, 'Equipos de procesamiento de datos', 0),
(78, '1613', 1, 2, 1, 65, 'Amortizaciones Acumuladas de Equipos pr. datos', 0),
(79, '1700', 1, 1, 1, 1, 'Bienes intangibles', 0),
(80, '1701', 1, 2, 1, 79, 'Llave de Negocio', 0),
(81, '1702', 1, 2, 1, 79, 'Amortizaciones Acumuladas de Llave de Negocio', 0),
(82, '1703', 1, 2, 1, 79, 'Marca de Fábrica', 0),
(83, '1704', 1, 2, 1, 79, 'Patentes', 0),
(84, '1705', 1, 2, 1, 79, 'Derechos intelectuales', 0),
(85, '1800', 1, 1, 1, 1, 'Otros activos', 0),
(86, '1801', 1, 2, 1, 85, 'Gastos adelantados', 0),
(87, '1802', 1, 2, 1, 85, 'Bienes de uso desafectados', 0),
(88, '1803', 1, 2, 1, 85, 'Seguros adelantados', 0),
(89, '2000', 1, 1, 2, -1, 'PASIVO', 0),
(90, '2100', 1, 1, 2, 89, 'Cuentas por Pagar/Deudas comerciales', 0),
(91, '2101', 1, 2, 2, 90, 'Proveedores', 0),
(92, '2102', 1, 2, 2, 90, 'Proveedores en U$S', 0),
(93, '2103', 1, 2, 2, 90, 'Documentos a Pagar', 0),
(94, '2104', 1, 2, 2, 90, 'Anticipo de Clientes', 0),
(95, '2200', 1, 1, 2, 90, 'Prestamos', 0),
(96, '2201', 1, 2, 2, 90, 'Adelantos en Cta. Cte.', 0),
(97, '2202', 1, 2, 2, 90, 'Acreedores Varios', 0),
(98, '2300', 1, 1, 2, 89, 'Remuneraciones y Cargas Sociales', 0),
(99, '2301', 1, 2, 2, 98, 'Sueldos y Jornales a Pagar', 0),
(100, '2302', 1, 2, 2, 98, 'Contribuciones Patrimoniales a Pagar', 0),
(101, '2303', 1, 2, 2, 98, 'Administración Nacional del Seguro de Salud (A.N.S.Sal.)', 0),
(102, '2304', 1, 2, 2, 98, 'Sueldo Anual Complementario (S.A.C.) a pagar', 0),
(103, '2305', 1, 2, 2, 98, 'Indemnizaciones a Pagar', 0),
(104, '2306', 1, 2, 2, 98, 'Obra social a pagar', 0),
(105, '2307', 1, 2, 2, 98, 'Seguro obligatorio a pagar', 0),
(106, '2308', 1, 2, 2, 98, 'Sindicato a pagar', 0),
(107, '2309', 1, 2, 2, 98, 'FAECYS a pagar', 0),
(108, '2400', 1, 1, 2, 89, 'Cargas Fiscales', 0),
(109, '2401', 1, 2, 2, 108, 'I.V.A. Débito Fiscal', 0),
(110, '2402', 1, 2, 2, 108, 'I.V.A. a pagar', 0),
(111, '2403', 1, 2, 2, 108, 'Impuesto a las Ganancias a pagar', 0),
(112, '2404', 1, 2, 2, 108, 'IIBB a pagar', 0),
(113, '2405', 1, 2, 2, 108, 'Tasas Municipales a pagar', 0),
(114, '2500', 1, 1, 2, 89, 'Otros Pasivos', 0),
(115, '2501', 1, 2, 2, 114, 'Intereses Ganados por Adelantado', 0),
(116, '2502', 1, 2, 2, 114, 'A. Ratti cuenta particular (exceso en aporte)', 0),
(117, '2600', 1, 1, 2, 89, 'Provisiones.', 0),
(118, '2601', 1, 2, 2, 117, 'Provisión para Despidos', 0),
(119, '2602', 1, 2, 2, 117, 'Provisión para Accidentes y Enfermedades', 0),
(120, '3000', 1, 1, 2, -1, 'PATRIMONIO NETO', 0),
(121, '3100', 1, 1, 2, 120, 'Capital/Aporte de los propietarios', 0),
(122, '3101', 1, 2, 2, 121, 'Capital Suscripto/Capital social', 0),
(123, '3102', 1, 2, 2, 121, 'Ajuste de Capital', 0),
(124, '3200', 1, 1, 2, 120, 'Ganancias Reservadas', 0),
(125, '3201', 1, 2, 2, 124, 'Reserva Legal', 0),
(126, '3202', 1, 2, 2, 124, 'Reserva Facultativa/Voluntaria', 0),
(127, '3203', 1, 2, 2, 124, 'Reserva Estatutaria', 0),
(128, '3300', 1, 1, 2, 120, 'Resultados No Asignados', 0),
(129, '3301', 1, 2, 2, 128, 'Resultados de Ejercicios Anteriores', 0),
(130, '3302', 1, 2, 2, 128, 'Resultado del Ejercicio', 0),
(131, '4000', 1, 1, 2, -1, 'INGRESOS', 0),
(132, '4100', 1, 1, 2, 131, 'Ingresos Ordinarios', 0),
(133, '4101', 1, 2, 2, 132, 'Ventas', 0),
(134, '4102', 1, 2, 2, 132, 'Descuentos Obtenidos', 0),
(135, '4103', 1, 2, 2, 132, 'Intereses Gravados', 0),
(136, '4104', 1, 2, 2, 132, 'Rentas Ganadas', 0),
(137, '4200', 1, 1, 2, 131, 'Ingresos Extraordinarios', 0),
(138, '4201', 1, 2, 2, 137, 'Sobrante de Caja', 0),
(139, '4202', 1, 2, 2, 137, 'Sobrante de Mercaderías', 0),
(140, '4203', 1, 2, 2, 137, 'Intereses Punitorios', 0),
(141, '4204', 1, 2, 2, 137, 'Recupero de Créditos Incobrables', 0),
(142, '4205', 1, 2, 2, 137, 'Resultado Positivo de Ventas Bienes de Uso', 0),
(143, '4206', 1, 2, 2, 137, 'Diferencia de Cotización Positiva', 0),
(144, '5000', 1, 1, 1, -1, 'GASTOS', 0),
(145, '5100', 1, 1, 1, 144, 'Gastos Ordinarios', 0),
(146, '5101', 1, 2, 1, 145, 'Costo de Ventas', 0),
(147, '5102', 1, 2, 1, 145, 'Intereses Perdidos', 0),
(148, '5103', 1, 2, 1, 145, 'Comisiones Perdidas', 0),
(149, '5104', 1, 2, 1, 145, 'Seguros Perdidos', 0),
(150, '5105', 1, 2, 1, 145, 'Sueldos y Jornales', 0),
(151, '5106', 1, 2, 1, 145, 'Sueldo Anual Complementario (S.A.C.)', 0),
(152, '5107', 1, 2, 1, 145, 'Gastos de energía, luz, teléfono agua e Internet', 0),
(153, '5108', 1, 2, 1, 145, 'Gastos generales', 0),
(154, '5109', 1, 2, 1, 145, 'Amortizaciones de …', 0),
(155, '5110', 1, 2, 1, 145, 'Contribuciones Patronales', 0),
(156, '5111', 1, 2, 1, 145, 'Descuentos Cedidos', 0),
(157, '5112', 1, 2, 1, 145, 'Alquileres Perdidos/Cedidos', 0),
(158, '5113', 1, 2, 1, 145, 'Impuesto a las Ganancias', 0),
(159, '5114', 1, 2, 1, 145, 'Ingresos Brutos (IIBB)', 0),
(160, '5115', 1, 2, 1, 145, 'Tasas Municipales', 0),
(161, '5116', 1, 2, 1, 145, 'Fletes y acarreos', 0),
(162, '5200', 1, 1, 1, 144, 'Gastos Extraordinarios', 0),
(163, '5201', 1, 2, 1, 162, 'Faltante de Caja', 0),
(164, '5202', 1, 2, 1, 162, 'Faltante de Inventario', 0),
(165, '5203', 1, 2, 1, 162, 'Resultado Negativo Ventas de Bienes de Uso', 0),
(166, '5204', 1, 2, 1, 162, 'Diferencia de Cotización Negativa', 0),
(167, '5205', 1, 2, 1, 162, 'Indemnizaciones por Despidos', 0),
(168, '5206', 1, 2, 1, 162, 'Indemnizaciones por Accidentes y Enfermedades', 0),
(169, '6000', 1, 1, 1, -1, 'CUENTAS DE MOVIMIENTO', 0),
(170, '6101', 1, 2, 1, 169, 'Compras.', 0),
(171, '6102', 1, 2, 1, 169, 'Devoluciones de Compras.', 0),
(172, '6103', 1, 2, 1, 169, 'Devoluciones de Ventas', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_accountingspending`
--

CREATE TABLE IF NOT EXISTS `llx_contab_accountingspending` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(20) NOT NULL,
  `fk_contab_accounting` int(11) NOT NULL,
  `detail` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_accounting_spending`
--

CREATE TABLE IF NOT EXISTS `llx_contab_accounting_spending` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(20) NOT NULL,
  `fk_contab_accounting` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_ref_fk_contab_accounting` (`ref`,`fk_contab_accounting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_periodo`
--

CREATE TABLE IF NOT EXISTS `llx_contab_periodo` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `period_month` int(2) NOT NULL,
  `period_year` year(4) NOT NULL,
  `date_ini` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` int(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_contab_periodo`
--

INSERT INTO `llx_contab_periodo` (`rowid`, `entity`, `period_month`, `period_year`, `date_ini`, `date_fin`, `statut`) VALUES
(1, 1, 1, 2013, '2013-01-01', '2013-01-31', 1),
(2, 1, 2, 2013, '2013-02-01', '2013-02-28', 1),
(3, 1, 3, 2013, '2013-03-01', '2013-03-31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_point_entry`
--

CREATE TABLE IF NOT EXISTS `llx_contab_point_entry` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `ref` varchar(3) NOT NULL,
  `description` varchar(120) NOT NULL,
  `cfglan` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_point_entry` (`entity`,`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=337 ;

--
-- Volcado de datos para la tabla `llx_contab_point_entry`
--

INSERT INTO `llx_contab_point_entry` (`rowid`, `entity`, `ref`, `description`, `cfglan`) VALUES
(1, 1, '500', 'Cuentas por Cobrar-Inclusion de Titulos', 2),
(2, 1, '501', 'Cuentas por Cobrar-Inclusion de Titulos de Cobranza Anticipada(RA)', 2),
(3, 1, '502', 'Cuentas por Cobrar-Borrado de Titulos de Cobranza Anticipada(RA)', 2),
(4, 1, '503', 'Cuentas por Cobrar-Sustitucion de Titulos Provisorios', 2),
(5, 1, '504', 'Cuentas por Cobrar-Generacion de Titulos por Desdoblamiento', 2),
(6, 1, '505', 'Cuentas por Cobrar-Borrado de Titulos', 2),
(7, 1, '506', 'Cuentas por Cobrar-Inclusion Prorrateo Centro de Costo Modalidad Multiple', 2),
(8, 1, '507', 'Cuentas por Cobrar-Anulacion Prorrateo Centro de Costo Modalidad Multiple', 2),
(9, 1, '508', 'Cuentas por Pagar - Prorrateo Centro de Costo Modalidad Multiple', 2),
(10, 1, '509', 'Cuentas por Pagar - Anulacion Prorrateo Centro de Costo Modalidad Multiple', 2),
(11, 1, '51A', 'Cuentas por Cobrar - Provision para cuentas de cobranza dudosa', 2),
(12, 1, '510', 'Cuentas por Pagar - Inclusion de Titulos', 2),
(13, 1, '511', 'Cuentas por Pagar - Inclusion de Titulos con Prorrateo Simple', 2),
(14, 1, '512', 'Cuentas por Pagar - Borrado de Titulos con Prorrateo Simple', 2),
(15, 1, '513', 'Cuentas por Pagar - Inclusion de Titulos Pago Anticipado (PA)', 2),
(16, 1, '514', 'Cuentas por Pagar - Borrado Titulos Pago Anticipado (PA)', 2),
(17, 1, '515', 'Cuentas por Pagar - Borrado de Titulos', 2),
(18, 1, '516', 'Movimiento Bancario - Inclusion Movimiento por Pagar con Prorrateo', 2),
(19, 1, '517', 'Movimiento Bancario - Inclusion Movimiento por Cobrar con Prorrateo', 2),
(20, 1, '518', 'Cuentas por Pagar - Baja de Titulos por Vendor', 2),
(21, 1, '519', 'Cuentas por Pagar - Anulacion de Baja de Titulos por Vendor', 2),
(22, 1, '520', 'Cuentas por Cobrar - Baja de Titulos en Cartera', 2),
(23, 1, '521', 'Cuentas por Cobrar - Baja de Titulos Cobranza Simple', 2),
(24, 1, '522', 'Cuentas por cobrar - Bajas de Titulo Cobranza Descontada', 2),
(25, 1, '523', 'Cuentas por cobrar - Baja de Titulos Cobranza Garantizada', 2),
(26, 1, '524', 'Cuentas por cobrar - Bajas de Titulos Cobranza Vinculada', 2),
(27, 1, '525', 'Cuentas por cobrar - Baja de Titulos Cobranza Abogado', 2),
(28, 1, '526', 'Cuentas por cobrar - Baja de Titulos Cobranza Judicial', 2),
(29, 1, '527', 'Cuentas por cobrar - Anulacion de Bajas de Titulos', 2),
(30, 1, '528', 'Cuentas por cobrar - Baja de Titulos Cobranza Garantizada Descontada', 2),
(31, 1, '529', 'Cuentas por cobrar - Anulacion de Titulos generados por Desdoblamiento', 2),
(32, 1, '530', 'Cuentas por Pagar - Baja de Titulos', 2),
(33, 1, '531', 'Cuentas por Pagar - Anulacion de Bajas de Titulos', 2),
(34, 1, '532', 'Cuentas por Pagar - Baja de Titulos por Bordero de Pago', 2),
(35, 1, '533', 'Cuentas por Pagar - Sustitucion de Titulos Provisorios', 2),
(36, 1, '535', 'Anulacion de la compensacion de titulos por Pagar / Cobrar', 2),
(37, 1, '536', 'Bajas de titulos a por cobrar c/distrib.en multiples modalidades p/CC', 2),
(38, 1, '537', 'Bajas de titulos por pagar c/distr.en mult.modalidades p/CC', 2),
(39, 1, '538', 'Anulacion bajas de titulos por cobrar c/distr.em mult.modalidades p/CC', 2),
(40, 1, '539', 'Anulacion bajas de titulos por pagar c/distr.em mult.modalidades p/CC', 2),
(41, 1, '540', 'Cuentas por cobrar - Transferencia de Titulos para Cartera', 2),
(42, 1, '541', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza Simple', 2),
(43, 1, '542', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza Descontada', 2),
(44, 1, '543', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza Garantizada', 2),
(45, 1, '544', 'Cuentas por cobrar - Transferencia de Titulos Cobranza Vinculada', 2),
(46, 1, '545', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza Abogado', 2),
(47, 1, '546', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza Judicial', 2),
(48, 1, '547', 'Cuentas por cobrar - Generacion de Bordero Cuentas por cobrar para Cartera', 2),
(49, 1, '548', 'Cuentas por cobrar - Generacion de Bordero Cuentas por cobrar para Cobranza Simple', 2),
(50, 1, '549', 'Cuentas por cobrar - Generacion de bordero Cuentas por cobrar para cobranza descontada', 2),
(51, 1, '550', 'Cuentas por cobrar - Generacion de Bordero Cuentas por cobrar para cobranza garantizada.', 2),
(52, 1, '551', 'Cuentas por cobrar - Generacion de bordero Cuentas por cobrar para cobranza vinculada', 2),
(53, 1, '552', 'Cuentas por cobrar - Generacion de bordero Cuentas por cobrar para cobranza Abogado', 2),
(54, 1, '553', 'Cuentas por cobrar - Generacion de bordero Cuentas por cobrar para cobranza judicial', 2),
(55, 1, '554', 'Cuentas por cobrar - Anulacion de bordero Cuentas por cobrar.', 2),
(56, 1, '555', 'Cuentas por cobrar - Transferencia de Titulos para Cobranza garantizada descontada', 2),
(57, 1, '556', 'Cuentas por cobrar - Generacion de bordero Cuentas por cobrar para cobranza garantizada descontada', 2),
(58, 1, '557', 'Movimiento Bancario - Anulacion de Movimiento por Pagar con Prorrateo', 2),
(59, 1, '558', 'Movimiento Bancario - Anulacion de Movimiento por cobrar con prorrateo', 2),
(60, 1, '559', 'Compensacion de Cheques Recibidos', 2),
(61, 1, '560', 'Movimiento Bancario - Transferencia Salida Banco Origen', 2),
(62, 1, '56A', 'Movimiento Bancario - Asiento de ITF', 2),
(63, 1, '56B', 'Movimiento Bancario - Asiento de ITF (reversion)', 2),
(64, 1, '561', 'Movimiento Bancario - Transferencia Entr. Banco Destino', 2),
(65, 1, '562', 'Movimiento Bancario - Inclusion de Movimiento por pagar', 2),
(66, 1, '563', 'Movimiento Bancario - Inclusion de Movimiento por cobrar/Anulacion de la Orden de Pago', 2),
(67, 1, '564', 'Movimiento Bancario - Anulacion Movimiento por pagar', 2),
(68, 1, '565', 'Movimiento Bancario - Anulacion Movimiento por cobrar/Reversion de la Orden de Pago', 2),
(69, 1, '566', 'Cuentas por Pagar - Generacion de Cheques sobre Titulos pendientes', 2),
(70, 1, '567', 'Cuentas por Pagar - Generacion de Cheques Sueltos', 2),
(71, 1, '568', 'Cuentas por Pagar - Anulacion de Cheque Suelto', 2),
(72, 1, '569', 'Cuentas por Pagar -  Segundo deposito de Cheque sobre Titulos Pendientes', 2),
(73, 1, '570', 'Orden de Pago - Inclusion de Orden de Pago', 2),
(74, 1, '571', 'Cuentas por Pagar - Anulacion de Cheques sobre Titulos Pendientes / Borrado de Orden de Pago', 2),
(75, 1, '572', 'Movimiento Cajero Interno - Movimientos de Cajero Interno', 2),
(76, 1, '573', 'Movimiento Cajero Interno - Movimientos de Reposicion', 2),
(77, 1, '574', 'Rechazar Cheques - Chile', 2),
(78, 1, '575', 'Recibos Diversos - Inclusion de Recibo', 2),
(79, 1, '576', 'Recibos Diversos - Inclusion de Recibo', 2),
(80, 1, '577', 'Cuentas por Pagar - Generacion de Titulo por desdoblamiento', 2),
(81, 1, '578', 'Cuentas por Pagar - Borrado de Titulo generado por desdoblamiento', 2),
(82, 1, '579', 'Movimiento Caja Interno - Borrado de un Movimiento de Anticipo / Gasto en la Caja Chica', 2),
(83, 1, '57A', 'Cuentas por Cobrar - Diferencia de Cambio por Cobrar', 2),
(84, 1, '57B', 'Cuentas por Cobrar - Reversion de Diferencia de Cambio por Cobrar', 2),
(85, 1, '57C', 'Cuentas por Pagar - Diferencia de Cambio por Pagar', 2),
(86, 1, '57D', 'Cuentas por Pagar - Reversion de Diferencia de Cambio por Pagar', 2),
(87, 1, '580', 'Inversiones - Inclusion de Inversion Financiera', 2),
(88, 1, '581', 'Inversiones - Borrado Inversion Financiera', 2),
(89, 1, '582', 'Inversiones - Atribucion / Rescate de Prestamo', 2),
(90, 1, '583', 'Reversion de compensacion de cheques recibidos', 2),
(91, 1, '584', 'Inversiones - Reversion Atribucion Inversion Financiera', 2),
(92, 1, '585', 'Inversiones - Rescate Inversion Financiera', 2),
(93, 1, '586', 'Inversiones - Reversion Prestamo / Reversion del Rescate', 2),
(94, 1, '587', 'Cuentas por Pagar - Generacion de Facturas por Pagar', 2),
(95, 1, '588', 'Cuentas por Cobrar - Anulacion Compensacion de Titulos', 2),
(96, 1, '589', 'Cuentas por Pagar - Anulacion Compensacion de Titulos', 2),
(97, 1, '590', 'Cuentas por Pagar - Generacion de Cheques sobre Titulos dados de baja', 2),
(98, 1, '591', 'Cuentas por Pagar - Anulacion de Cheques sobre Titulos dados de baja', 2),
(99, 1, '592', 'Cuentas por Cobrar - Anulacion Facturas por Cobrar', 2),
(100, 1, '593', 'Cuentas por Pagar - Anulacion Facturas por Pagar', 2),
(101, 1, '594', 'Cuentas por Pagar/Cobrar - Compensacion entre Carteras', 2),
(102, 1, '595', 'Cuentas por Cobrar - Generacion de Facturas por Cobrar', 2),
(103, 1, '596', 'Cuentas por Cobrar - Compensacion Cuentas por Cobrar', 2),
(104, 1, '597', 'Cuentas por Pagar - Compensacion Cuentas por Pagar', 2),
(105, 1, '598', 'Variacion Monetaria - Cuentas por Cobrar', 2),
(106, 1, '599', 'Variacion Monetaria - Cuentas por Pagar', 2),
(107, 1, '5B1', 'Ajuste a Valor Presente - Cuentas por Cobrar', 2),
(108, 1, '5B2', 'Reversion del Ajuste a Valor Presente - Cuentas por Cobrar', 2),
(109, 1, '5B3', 'Ajuste a Valor Presente - Cuentas por Pagar', 2),
(110, 1, '5B4', 'Reversion del Ajuste a Valor Presente - Cuentas por Pagar', 2),
(111, 1, '5B5', 'Reversion de AVP en la Anulacion de Inclusion - Cuentas por Cobrar', 2),
(112, 1, '5B6', 'Reversion de AVP en la Anulacion de Inclusion - Cuentas por Pagar', 2),
(113, 1, '5B7', 'Reversion de AVP en la Anulacion de Baja - Cuentas por Cobrar', 2),
(114, 1, '5B8', 'Reversion de AVP en la Anulacion de Baja - Cuentas por Pagar', 2),
(115, 1, '605', 'Calculo de PIS - Inclusion', 2),
(116, 1, '606', 'Calculo de COFINS - Inclusion', 2),
(117, 1, '607', 'Calculo de PIS - Reversion', 2),
(118, 1, '608', 'Calculo de COFINS - Reversion', 2),
(119, 1, '610', 'Documento de Salida - Inclusion de Documento Items', 2),
(120, 1, '620', 'Documento de Salida - Inclusion de Documento Total', 2),
(121, 1, '630', 'Documento de Salida - Borrado de Documento Items', 2),
(122, 1, '635', 'Documento de Salida - Borrado de Documento Total', 2),
(123, 1, '640', 'Documento de Entrada - Inclusion de Documento Devolucion/Mejora Items', 2),
(124, 1, '641', 'Documento de Entrada - Inclusion de Documento Devolucion/Mejora Items Prorrateo', 2),
(125, 1, '642', 'Documento de Entrada - Inclusion de Documento Devolucion/Mejora Total', 2),
(126, 1, '650', 'Documento de Entrada - Inclusion de Documento Entrada Items', 2),
(127, 1, '651', 'Documento de Entrada - Inclusion de Documento Entrada Items Prorrateo', 2),
(128, 1, '652', 'Pedido de Compras - Inclusion de Pedidos Items', 2),
(129, 1, '655', 'Documento de Entrada - Borrado de Documento Entrada Items', 2),
(130, 1, '656', 'Documento de Entrada - Borrado de Documento Entrada Items Prorrateo', 2),
(131, 1, '657', 'Pedido de Compras - Borrado de Pedidos Items', 2),
(132, 1, '658', 'Pedido de Compras - Borrado de Residuos - Items', 2),
(133, 1, '659', 'Pedido de Compras - Prorrateo del Pedido de Compra', 2),
(134, 1, '65A', 'Pedido de Compras - Borrado/Reversion del Prorrateo del Pedido de Compra', 2),
(135, 1, '65B', 'Pedido de Compras - Pedido de Compra por Convocacion', 2),
(136, 1, '660', 'Documento de Entrada - Inclusion de Documento Entrada Total', 2),
(137, 1, '661', 'Documento de Entrada - Inclusion de Titulos por Pagar', 2),
(138, 1, '665', 'Documento de Entrada - Borrado de Documento Entrada Total', 2),
(139, 1, '666', 'Movimiento de Stock - Salida de Productos', 2),
(140, 1, '667', 'Movimiento de Stock - Salida de Productos ( Antes de Actualizar el Costo )', 2),
(141, 1, '668', 'Movimiento de Stock - Entrada de Productos', 2),
(142, 1, '669', 'Movimiento de Stock - Entrada de Productos ( Antes de Actualizar el Costo)', 2),
(143, 1, '670', 'Movimiento de Stock - Transferencia Origen', 2),
(144, 1, '672', 'Movimiento de Stock - Transferencia Destino', 2),
(145, 1, '674', 'Movimiento de Stock - Inventario Salida', 2),
(146, 1, '676', 'Movimiento de Stock - Inventario Entrada', 2),
(147, 1, '678', 'Documento de Salida - Costo de Mercaderia Vendida', 2),
(148, 1, '679', 'Movimiento de Stock - Entrada de Productos ( Atribucion Indirecta )', 2),
(149, 1, '680', 'Movimiento de stock - Salida de Productos ( Atribucion Indirecta )', 2),
(150, 1, '681', 'Documento de Entrada - Compra / Envio de Terceros Items', 2),
(151, 1, '682', 'Documento de Entrada - Retorno de Poder Terceros Items', 2),
(152, 1, '690', 'Garantia - Inclusion de garantias', 2),
(153, 1, '691', 'Garantias - Borrado de garantias', 2),
(154, 1, '692', 'Garantia - Garantias dadas de baja', 2),
(155, 1, '693', 'Gesti?n de Contratos - Cronograma Contable - Imputaci?n de las Cuotas', 2),
(156, 1, '694', 'Gesti?n de Contratos - Inclusi?n de Contrato', 2),
(157, 1, '695', 'Gesti?n de Contratos - Retenci?n del Contrato', 2),
(158, 1, '696', 'Gestion de Contratos - Borrado del Contrato', 2),
(159, 1, '697', 'Gestion de Contratos -  Finalizacion de la Medicion', 2),
(160, 1, '698', 'Gestion de Contratos - Finalizacion de la Medicion/Automatico', 2),
(161, 1, '699', 'Gestion de Contratos - Reversion de la Medicion', 2),
(162, 1, '69A', 'Gestion de Contratos - Finalizacion de la Medicion: Items del Contrato', 2),
(163, 1, '69B', 'Gestion de Contratos - Reversion de la Medicion: Items del Contrato', 2),
(164, 1, '69C', 'Gestion de Contratos - Prorrateo Items Contrato s/Planilla en la Finalizacion de la Medicion', 2),
(165, 1, '69D', 'Gestion de Contratos - Prorrateo Items Contrato s/Planilla en la Reversion de la Medicion', 2),
(166, 1, '69', 'Gestion de Contratos - Prorrateo Items Contrato c/Planilla en la Finalizacion de la Medicion', 2),
(167, 1, '69F', 'Gestion de Contratos - Prorrateo Items Contrato c/Planilla en la Reversion de la Medicion', 2),
(168, 1, '710', 'Calculo de ICMS - Inclusion', 2),
(169, 1, '711', 'Calculo de ICMS - Reversion', 2),
(170, 1, '712', 'Calculo de ICMS Compensacion de Saldo - Inclusion', 2),
(171, 1, '713', 'Calculo de ICMS Compensacion de Saldo - Devolucion', 2),
(172, 1, '720', 'Calculo de IPI - Inclusion', 2),
(173, 1, '721', 'Calculo de IPI - Reversion', 2),
(174, 1, '750', 'Calculo de ISS - Inclusion', 2),
(175, 1, '751', 'Calculo de ISS - Reversion', 2),
(176, 1, '755', 'Atribucion CIAP', 2),
(177, 1, '756', 'Reversion CIAP', 2),
(178, 1, '801', 'Activo Fijo - Adquisicion de Bien', 2),
(179, 1, '802', 'Activo Fijo - Adquisicion - Revaluacion', 2),
(180, 1, '803', 'Activo Fijo - Adquisicion - Anticipo', 2),
(181, 1, '804', 'Activo Fijo - Adquisicion - Ley 8.200', 2),
(182, 1, '805', 'Activo Fijo - Borrado Adquisicion de Bien', 2),
(183, 1, '806', 'Activo Fijo - Borrado - Revaluacion', 2),
(184, 1, '807', 'Activo Fijo - Borrado - Anticipo', 2),
(185, 1, '808', 'Activo Fijo - Borrado - Ley 8.200', 2),
(186, 1, '810', 'Activo Fijo - Baja Adquisicion', 2),
(187, 1, '811', 'Activo Fijo - Baja Revaluacion', 2),
(188, 1, '812', 'Activo Fijo - Baja - Anticipos', 2),
(189, 1, '813', 'Activo Fijo - Baja Ley 8.200', 2),
(190, 1, '814', 'Activo Fjo - Anulacion Baja Adquisicion', 2),
(191, 1, '815', 'Activo Fijo - Anulacion Baja Revaluacion', 2),
(192, 1, '816', 'Activo Fijo - Anulacion Baja Anticipo', 2),
(193, 1, '817', 'Activo Fijo - Anulacion Baja Ley 8.200', 2),
(194, 1, '820', 'Activo Fijo - Calculo de la Depreciacion', 2),
(195, 1, '821', 'Activo Fijo - Ampliacion', 2),
(196, 1, '822', 'Activo Fjo - Anulacion de Ampliacion', 2),
(197, 1, '823', 'Activo fijo - C?lculo de depreciaci?n: Prorrateo de gastos', 2),
(198, 1, '825', 'Activo Fijo - Reversion de calculo', 2),
(199, 1, '827', 'Activo Fijo - Ajuste Inventario', 2),
(200, 1, '828', 'Activo fijo - Reversi?n del c?lculo de depreciaci?n: Prorrateo de gastos', 2),
(201, 1, '850', 'Activo Fijo - Ajuste por inflacion - Calculo del Ajuste (Argentina)', 2),
(202, 1, '851', 'Activo fijo - Ajuste por inflacion - Anulacion de calculo del Ajuste (Argentina)', 2),
(203, 1, '870', 'Activo Fijo - Apunte de estimativa de produccion', 2),
(204, 1, '871', 'Activo Fijo - Apunte de revision de la estimativa de produccion', 2),
(205, 1, '872', 'Activo Fijo - Apunte de produccion', 2),
(206, 1, '873', 'Activo Fijo - Apunte de finalizacion de produccion', 2),
(207, 1, '874', 'Activo Fijo - Apunte de complemento de produccion', 2),
(208, 1, '875', 'Activo Fijo - Reversion de apunte de estimativa de produccion', 2),
(209, 1, '876', 'Activo Fijo - Reversion de apunte de revision de estimativa de produccion', 2),
(210, 1, '877', 'Activo Fijo - Reversion de apunte de produccion', 2),
(211, 1, '878', 'Activo Fijo - Reversion de apunte de finalizacion de produccion', 2),
(212, 1, '879', 'Activo Fijo - Reversion de apunte de complemento de produccion', 2),
(213, 1, '87A', 'Activo Fijo - Apunte de produccion con prorrateo', 2),
(214, 1, '87B', 'Activo Fijo - Devolucion de apunte de produccion con prorrateo', 2),
(215, 1, '845', 'Activo Fijo - Adquisicion - Ley 8.200 (Tipo del Activo - 0', 2),
(216, 1, '846', 'Activo Fijo - Borrado - Ley 8.200 (Tipo del Activo - 06', 2),
(217, 1, '847', 'Activo Fijo - Baja Ley 8.200 (Tipo del Activo - a06)', 2),
(218, 1, '848', 'Activo Fijo - Anulacion de Baja (Tipo del Activo - 0', 2),
(219, 1, '80A', 'Activo Fijo - Inclusion de otros tipos de Activo', 2),
(220, 1, '80B', 'Activo Fijo - Exclusion de otros tipos de Activo', 2),
(221, 1, '80C', 'Activo Fijo - Adquisicion de Bien por conversion', 2),
(222, 1, '80D', 'Activo Fijo - Borrado Adquisicion del Bien por conversion', 2),
(223, 1, '81A', 'Activo Fijo - Baja de otros tipos de Activo', 2),
(224, 1, '81B', 'Activo Fijo - Anulacion de la baja de otros tipos de Activo', 2),
(225, 1, '81C', 'Activo Fijo - Baja por conversion', 2),
(226, 1, '81D', 'Activo Fijo - Anulacion Baja Adquisicion por conversion', 2),
(227, 1, '81', 'Activo fijo - Baja de activo: Prorrateo de gastos', 2),
(228, 1, '81F', 'Activo fijo - Cancelacion de baja de activo: Prorrateo de gastos', 2),
(229, 1, '830', 'Activo Fijo - Transferencia contable', 2),
(230, 1, '831', 'Activo Fijo - Transferencia contable entre sucursales (origen)', 2),
(231, 1, '832', 'Activo Fijo - Transferencia contable entre sucursales (destino)', 2),
(232, 1, '835', 'Activo Fijo - Adquisicion por transferencia', 2),
(233, 1, '836', 'Activo Fijo - Anulacion de la Adquisicion por transferencia', 2),
(234, 1, '901', 'TMS - Inclusion de movimiento de costo de transporte por fecha de baja', 2),
(235, 1, '902', 'TMS - Reversion de movimiento de costo de transporte por fecha de baja', 2),
(236, 1, '903', 'TMS - Inclusion de movimiento de costo de transporte por fecha de emision', 2),
(237, 1, '904', 'TMS - Reversion de movimiento de costo de transporte por fecha de emision', 2),
(238, 1, '950', 'Documento de Entrada EIC - Inclusion de Items', 2),
(239, 1, '955', 'Documento de Entrada EIC - Borrado de Items', 2),
(240, 1, '965', 'Derechos del Autor - Apropiacion de derechos del autor', 2),
(241, 1, '970', 'Gest. de Abogados  - Generac.de Honorarios - Encab. de Factura', 2),
(242, 1, '971', 'Gest. de Abogados  - Generac.de Honorarios - Items Factura', 2),
(243, 1, '972', 'Gest. de Abogados  - Generac. de Gastos  - Items Factura', 2),
(244, 1, '973', 'Gest. de Abogados  - Generac.de Eventos - Items Factura''''', 2),
(245, 1, '974', 'Gest. de Abogados  - Revers. de Honorarios- Encabez. de Factura', 2),
(246, 1, '975', 'Gest. de Abogados  - Revers. de Honorarios - Items Factura', 2),
(247, 1, '976', 'Gest. de abogados  - Revers. de Gastos   - Items Factura', 2),
(248, 1, '977', 'Gest. de Abogados  - Revers. de Eventos - Items Factura', 2),
(249, 1, '980', 'Gest. de Abogados  - Genereracion Gastos, Cuen. p/ pagar - Encabez. de Factura', 2),
(250, 1, '981', 'Gest. de Abogados  - Generacion Gastos, Cuentas p/ Pagar - Items Factura', 2),
(251, 1, '984', 'Gest. de Abogados  - Reversion de Gastos, Cuen. p/ Pagar - Encabez. Factura', 2),
(252, 1, '985', 'Gest. de Abogados  - Reversion de Gastos, Cuen. p/ Pagar - Items Factura', 2),
(253, 1, 'A01', 'Planilla de Haberes - Sueldos por Pagar', 2),
(254, 1, 'A02', 'Planilla de Haberes - Vacaciones', 2),
(255, 1, 'A03', 'Planilla de Haberes - Asignacion Familiar', 2),
(256, 1, 'A04', 'Planilla de Haberes - Asignacion por Maternidad', 2),
(257, 1, 'A05', 'Planilla de Haberes - Remuneracion de Socios', 2),
(258, 1, 'B01', 'Planilla de Haberes - INSS Empleados', 2),
(259, 1, 'B02', 'Planilla de Haberes - IRRF', 2),
(260, 1, 'B03', 'Planilla de haberes - Asistencia Medica', 2),
(261, 1, 'B04', 'Planilla de Haberes - Aporte Sindical', 2),
(262, 1, 'B05', 'Planilla de Haberes - Faltas', 2),
(263, 1, 'B06', 'Planilla de Haberes - Aviso Previo (Descuento)', 2),
(264, 1, 'B07', 'Planilla de Haberes - Otros Descuentos', 2),
(265, 1, 'B08', 'Planilla de Haberes - Pension Alimenticia', 2),
(266, 1, 'B09', 'Planilla de Haberes - Ticket-Transporte', 2),
(267, 1, 'B10', 'Planilla de Haberes - Desc. Insuficiencia de Saldo', 2),
(268, 1, 'C01', 'Planilla de Haberes - INSS Empresa', 2),
(269, 1, 'C02', 'Planilla de Haberes - FGTS', 2),
(270, 1, 'C03', 'Planilla de Haberes - Provision Vacaciones', 2),
(271, 1, 'C04', 'Planilla de haberes - Cargas Provision de Vacaciones', 2),
(272, 1, 'C05', 'Planilla de Haberes - Provision de Aguinaldo', 2),
(273, 1, 'C06', 'Planilla de Haberes - Cargas de Provision de Aguinaldo', 2),
(274, 1, '890', 'Activo fijo - Simulacion depreciacion - Bienes reales', 2),
(275, 1, '892', 'Activo fijo - Reversion Simulacion depreciacion - Bienes reales', 2),
(276, 1, '891', 'Activo fijo - Simulacion depreciacion - Bienes modificadores', 2),
(277, 1, '893', 'Activo fijo - Reversion Simulacion depreciacion - Bienes modificadores', 2),
(278, 1, '700', 'Tienda - Venta', 2),
(279, 1, '701', 'Tienda - Anulacion de Venta', 2),
(280, 1, '702', 'Tienda - Factura Global', 2),
(281, 1, '703', 'Tienda - Factura sobre Comprobante', 2),
(282, 1, '704', 'Tienda - Cambio y Devolucion', 2),
(283, 1, '612', 'Pedido de Venta - Inclusion de Documento Items', 2),
(284, 1, '621', 'Pedido de Venta - Inclusion de Documento Total', 2),
(285, 1, '632', 'Pedido de Venta - Exclusion de Documento Items', 2),
(286, 1, '636', 'Pedido de Venta - Exclusion de Documento Total', 2),
(287, 1, '9A0', 'PLS - Inclusion cuentas a cobrar-antes BM1', 2),
(288, 1, '9A1', 'PLS - Inclusion cuentas a cobrrar- cada BM1', 2),
(289, 1, '9A8', 'PLS - Inclusion cuentas a cobrar-despues BM1', 2),
(290, 1, '9AB', 'PLS - BORRADO CTAS A COBRAR- CADA BMN', 2),
(291, 1, '9AG', 'PLS - INCL.CTAS PAGAR-ANTES BMS', 2),
(292, 1, '9AH', 'PLS - INCL.CTAS PAGAR- CADA BMS DE BD7', 2),
(293, 1, '9AI', 'PLS - INCL.CTAS PAGAR- CADA BMS DE BBC', 2),
(294, 1, '9AJ', 'PLS - INCL.CTAS PAGAR- CADA BMS DE BGQ', 2),
(295, 1, '9AK', 'PLS - INCL.CTAS PAGAR- CADA BMS DE BCE', 2),
(296, 1, '9AL', 'PLS - INCL.CTAS PAGAR- CADA BMR', 2),
(297, 1, '9AM', 'PLS - INCL.CTAS PAGAR- CADA BMS OTROS', 2),
(298, 1, '9AN', 'PLS - INCL.CTAS PAGAR-DESPUES BMS', 2),
(299, 1, '9AW', 'PLS - BAJA.CTAS PAGAR-ANTES BM1', 2),
(300, 1, '9AX', 'PLS - BAJA CTAS A COBRAR- CADA BM1', 2),
(301, 1, '9B4', 'PLS - BAJA.CTAS A COBRAR-DESPUES BM1', 2),
(302, 1, '9B5', 'PLS - CANCEL.BJ CTAS COBRAR -ANTES BM1', 2),
(303, 1, '9B6', 'PLS - CANCEL.BJ CTAS COBRAR-CADA BM1', 2),
(304, 1, '9BC', 'PLS - CANCEL.BJ CTAS COBRAR -DESPUES BM1', 2),
(305, 1, '9BD', 'PLS - BJ.CTAS PAGAR- ANTES DE BMS', 2),
(306, 1, '9BE', 'PLS - BJ.CTAS PAGAR- CADA BMS DE BD7', 2),
(307, 1, '9BF', 'PLS - BJ.CTAS PAGAR- CADA BMS DE BBC', 2),
(308, 1, '9BG', 'PLS - BJ.CTAS PAGAR- CADA BMS DE BGQ', 2),
(309, 1, '9BH', 'PLS - BJ.CTAS PAGAR- CADA BMS DE BCE', 2),
(310, 1, '9BI', 'PLS - BJ.CTAS PAGAR- CADA BMR', 2),
(311, 1, '9BJ', 'PLS - BJ.CTAS PAGAR- CADA BMS OTROS', 2),
(312, 1, '9BK', 'PLS - BJ.CTAS PAGAR-DESPUES BMS', 2),
(313, 1, '9BL', 'PLS - CANCEL.BJ CTAS PAGAR -ANTES BMS', 2),
(314, 1, '9BM', 'PLS - CANCEL.BJ CTAS PAGAR -CADA BMS/BD7', 2),
(315, 1, '9BN', 'PLS - CANCEL.BJ CTAS PAGAR -CADA BMS/BBC', 2),
(316, 1, '9BO', 'PLS - CANCEL.BJ CTAS PAGAR -CADA BMS/BGQ', 2),
(317, 1, '9BP', 'PLS - CANCEL.BJ CTAS PAGAR -CADA BMS/BCE', 2),
(318, 1, '9BQ', 'PLS - CANCEL.BJ CTAS PAGAR-CADA BMR', 2),
(319, 1, '9BR', 'PLS - CANCEL.BJ CTAS PAGAR -CADA BMS/OUT', 2),
(320, 1, '9BS', 'PLS - CANCEL.BJ CTAS PAGAR -DESPUES BMS', 2),
(321, 1, '9BZ', 'PLS - CONTABILIZACION DE FORMULARIOS - BD5/BE4', 2),
(322, 1, '9C0', 'PLS - CONTABILIZACION DE FORMULARIOS - BD6', 2),
(323, 1, '9C1', 'PLS - CONTABILIZACION DE FORMULARIOS - BD7', 2),
(324, 1, '59A', 'Reversion de la Variacion Monetaria - Cuentas por cobrar', 2),
(325, 1, '59B', 'Reversion de la Variacion Monetaria - Cuentas por pagar', 2),
(326, 1, '752', 'SPED PIS/COFINS - Inclus?o', 2),
(327, 1, '753', 'SPED PIS/COFINS - Exclus?o', 2),
(328, 1, '860', 'Activo Fijo - Ajuste a Valor Presente - Constitucion', 2),
(329, 1, '861', 'Activo Fijo - Ajuste a Valor Presente - Apropriacion', 2),
(330, 1, '862', 'Activo Fijo - Ajuste a Valor Presente - Baja de apropiacion', 2),
(331, 1, '863', 'Activo Fijo - Ajuste a Valor Presente - Baja', 2),
(332, 1, '864', 'Activo Fijo - Ajuste a Valor Presente - Realizacion', 2),
(333, 1, '865', 'Activo Fijo - Ajuste a Valor Presente - Baja de realizacion', 2),
(334, 1, '866', 'Activo Fijo - Ajuste a Valor Presente - Diferencia por revision', 2),
(335, 1, '867', 'Activo Fijo - Ajuste a Valor Presente - Anulacion de constitucion', 2),
(336, 1, '868', 'Activo Fijo - Ajuste a Valor Presente - Anulacion de AVP - Baja', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_seat`
--

CREATE TABLE IF NOT EXISTS `llx_contab_seat` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `date_seat` date NOT NULL,
  `lote` varchar(6) NOT NULL,
  `sblote` varchar(3) NOT NULL,
  `doc` varchar(6) NOT NULL,
  `currency` int(1) NOT NULL,
  `type_seat` int(1) NOT NULL,
  `debit_total` double(20,5) DEFAULT '0.00000',
  `credit_total` double(20,5) DEFAULT '0.00000',
  `history` varchar(40) DEFAULT NULL,
  `manual` int(1) NOT NULL,
  `fk_user_creator` int(11) NOT NULL,
  `fk_date_creator` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` int(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_seat_det`
--

CREATE TABLE IF NOT EXISTS `llx_contab_seat_det` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_contab_seat` int(11) NOT NULL,
  `debit_account` varchar(20) DEFAULT NULL,
  `debit_detail` varchar(40) DEFAULT NULL,
  `credit_account` varchar(20) DEFAULT NULL,
  `credit_detail` varchar(40) DEFAULT NULL,
  `dcd` int(1) DEFAULT NULL,
  `dcc` int(1) DEFAULT NULL,
  `amount` double(20,5) NOT NULL,
  `history` varchar(40) DEFAULT NULL,
  `sequence` varchar(10) NOT NULL,
  `fk_standard_seat` int(11) NOT NULL,
  `routines` varchar(10) NOT NULL,
  `value02` double(20,5) NOT NULL DEFAULT '0.00000',
  `value03` double(20,5) NOT NULL DEFAULT '0.00000',
  `value04` double(20,5) NOT NULL DEFAULT '0.00000',
  `date_rate` date NOT NULL,
  `rate` double(8,4) NOT NULL,
  `fk_user_creator` int(11) NOT NULL,
  `fk_date_creator` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` int(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contab_standard_seat`
--

CREATE TABLE IF NOT EXISTS `llx_contab_standard_seat` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_point_entry` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `description` varchar(40) NOT NULL,
  `type_seat` int(1) NOT NULL,
  `type_balance` int(1) NOT NULL,
  `debit_account` tinytext,
  `credit_account` tinytext,
  `currency` varchar(5) NOT NULL DEFAULT '11111' COMMENT 'Informa las monedas para los cuales se debe hacer los asientos contables. 1 hace el asiento en la moneda, 2 no efectua el asiento',
  `currency_value1` tinytext,
  `currency_value2` tinytext,
  `history` varchar(150) DEFAULT NULL,
  `history_group` varchar(150) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_entity_point_entry` (`entity`,`fk_point_entry`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_contab_standard_seat`
--

INSERT INTO `llx_contab_standard_seat` (`rowid`, `entity`, `fk_point_entry`, `sequence`, `status`, `description`, `type_seat`, `type_balance`, `debit_account`, `credit_account`, `currency`, `currency_value1`, `currency_value2`, `history`, `history_group`, `origin`) VALUES
(2, 1, 1, 1, 1, 'prueba', 1, 2, NULL, NULL, '11111', NULL, NULL, NULL, NULL, NULL),
(3, 1, 12, 1, 1, 'CUENTAS POR PAGAR MANUAL', 3, 3, 'llx_product.accountancy_code_buy', 'llx_societe.code_compta', '11222', 'llx_commande_fornisseurdet.total_ht', 'llx_commande_fornisseurdet.total_ht', '''PEDIDO ''.llx_commande_fornisseur.ref.'' DE FECHA ''.date_commande', '''PEDIDO ''.llx_commande_fornisseur.ref.'' DE FECHA ''.date_commande', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contrat`
--

CREATE TABLE IF NOT EXISTS `llx_contrat` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `date_contrat` datetime DEFAULT NULL,
  `statut` smallint(6) DEFAULT '0',
  `mise_en_service` datetime DEFAULT NULL,
  `fin_validite` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_commercial_signature` int(11) NOT NULL,
  `fk_commercial_suivi` int(11) NOT NULL,
  `fk_user_author` int(11) NOT NULL DEFAULT '0',
  `fk_user_mise_en_service` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `note` text,
  `note_public` text,
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_contrat_ref` (`ref`,`entity`),
  KEY `idx_contrat_fk_soc` (`fk_soc`),
  KEY `idx_contrat_fk_user_author` (`fk_user_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contratdet`
--

CREATE TABLE IF NOT EXISTS `llx_contratdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_contrat` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `statut` smallint(6) DEFAULT '0',
  `label` text,
  `description` text,
  `fk_remise_except` int(11) DEFAULT NULL,
  `date_commande` datetime DEFAULT NULL,
  `date_ouverture_prevue` datetime DEFAULT NULL,
  `date_ouverture` datetime DEFAULT NULL,
  `date_fin_validite` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double NOT NULL,
  `remise_percent` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT '0.00000000',
  `price_ht` double DEFAULT NULL,
  `remise` double DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `info_bits` int(11) DEFAULT '0',
  `fk_user_author` int(11) NOT NULL DEFAULT '0',
  `fk_user_ouverture` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `commentaire` text,
  PRIMARY KEY (`rowid`),
  KEY `idx_contratdet_fk_contrat` (`fk_contrat`),
  KEY `idx_contratdet_fk_product` (`fk_product`),
  KEY `idx_contratdet_date_ouverture_prevue` (`date_ouverture_prevue`),
  KEY `idx_contratdet_date_ouverture` (`date_ouverture`),
  KEY `idx_contratdet_date_fin_validite` (`date_fin_validite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_contratdet_log`
--

CREATE TABLE IF NOT EXISTS `llx_contratdet_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_contratdet` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `statut` smallint(6) NOT NULL,
  `fk_user_author` int(11) NOT NULL,
  `commentaire` text,
  PRIMARY KEY (`rowid`),
  KEY `idx_contratdet_log_fk_contratdet` (`fk_contratdet`),
  KEY `idx_contratdet_log_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_cotisation`
--

CREATE TABLE IF NOT EXISTS `llx_cotisation` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_adherent` int(11) DEFAULT NULL,
  `dateadh` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `cotisation` double DEFAULT NULL,
  `fk_bank` int(11) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_cotisation` (`fk_adherent`,`dateadh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_actioncomm`
--

CREATE TABLE IF NOT EXISTS `llx_c_actioncomm` (
  `id` int(11) NOT NULL,
  `code` varchar(12) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'system',
  `libelle` varchar(48) NOT NULL,
  `module` varchar(16) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `todo` tinyint(4) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_actioncomm` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_actioncomm`
--

INSERT INTO `llx_c_actioncomm` (`id`, `code`, `type`, `libelle`, `module`, `active`, `todo`, `position`) VALUES
(1, 'AC_TEL', 'system', 'Phone call', NULL, 1, NULL, 2),
(2, 'AC_FAX', 'system', 'Send Fax', NULL, 1, NULL, 3),
(3, 'AC_PROP', 'systemauto', 'Send commercial proposal by email', 'propal', 0, NULL, 10),
(4, 'AC_EMAIL', 'system', 'Send Email', NULL, 1, NULL, 4),
(5, 'AC_RDV', 'system', 'Rendez-vous', NULL, 1, NULL, 1),
(8, 'AC_COM', 'systemauto', 'Send customer order by email', 'order', 0, NULL, 8),
(9, 'AC_FAC', 'systemauto', 'Send customer invoice by email', 'invoice', 0, NULL, 6),
(10, 'AC_SHIP', 'systemauto', 'Send shipping by email', 'shipping', 0, NULL, 11),
(30, 'AC_SUP_ORD', 'systemauto', 'Send supplier order by email', 'order_supplier', 0, NULL, 9),
(31, 'AC_SUP_INV', 'systemauto', 'Send supplier invoice by email', 'invoice_supplier', 0, NULL, 7),
(40, 'AC_OTH_AUTO', 'systemauto', 'Other (automatically inserted events)', NULL, 1, NULL, 20),
(50, 'AC_OTH', 'system', 'Other (manually inserted events)', NULL, 1, NULL, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_action_trigger`
--

CREATE TABLE IF NOT EXISTS `llx_c_action_trigger` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `label` varchar(128) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `elementtype` varchar(16) NOT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_action_trigger_code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Volcado de datos para la tabla `llx_c_action_trigger`
--

INSERT INTO `llx_c_action_trigger` (`rowid`, `code`, `label`, `description`, `elementtype`, `rang`) VALUES
(1, 'FICHINTER_VALIDATE', 'Intervention validated', 'Executed when a intervention is validated', 'ficheinter', 18),
(2, 'BILL_VALIDATE', 'Customer invoice validated', 'Executed when a customer invoice is approved', 'facture', 6),
(3, 'ORDER_SUPPLIER_APPROVE', 'Supplier order request approved', 'Executed when a supplier order is approved', 'order_supplier', 11),
(4, 'ORDER_SUPPLIER_REFUSE', 'Supplier order request refused', 'Executed when a supplier order is refused', 'order_supplier', 12),
(5, 'ORDER_VALIDATE', 'Customer order validate', 'Executed when a customer order is validated', 'commande', 4),
(6, 'PROPAL_VALIDATE', 'Customer proposal validated', 'Executed when a commercial proposal is validated', 'propal', 2),
(10, 'COMPANY_CREATE', 'Third party created', 'Executed when a third party is created', 'societe', 1),
(11, 'CONTRACT_VALIDATE', 'Contract validated', 'Executed when a contract is validated', 'contrat', 17),
(12, 'PROPAL_SENTBYMAIL', 'Commercial proposal sent by mail', 'Executed when a commercial proposal is sent by mail', 'propal', 3),
(13, 'ORDER_SENTBYMAIL', 'Customer order sent by mail', 'Executed when a customer order is sent by mail ', 'commande', 5),
(14, 'BILL_PAYED', 'Customer invoice payed', 'Executed when a customer invoice is payed', 'facture', 7),
(15, 'BILL_CANCEL', 'Customer invoice canceled', 'Executed when a customer invoice is conceled', 'facture', 8),
(16, 'BILL_SENTBYMAIL', 'Customer invoice sent by mail', 'Executed when a customer invoice is sent by mail', 'facture', 9),
(17, 'ORDER_SUPPLIER_VALIDATE', 'Supplier order validated', 'Executed when a supplier order is validated', 'order_supplier', 10),
(18, 'ORDER_SUPPLIER_SENTBYMAIL', 'Supplier order sent by mail', 'Executed when a supplier order is sent by mail', 'order_supplier', 13),
(19, 'BILL_SUPPLIER_VALIDATE', 'Supplier invoice validated', 'Executed when a supplier invoice is validated', 'invoice_supplier', 14),
(20, 'BILL_SUPPLIER_PAYED', 'Supplier invoice payed', 'Executed when a supplier invoice is payed', 'invoice_supplier', 15),
(21, 'BILL_SUPPLIER_SENTBYMAIL', 'Supplier invoice sent by mail', 'Executed when a supplier invoice is sent by mail', 'invoice_supplier', 16),
(22, 'SHIPPING_VALIDATE', 'Shipping validated', 'Executed when a shipping is validated', 'shipping', 19),
(23, 'SHIPPING_SENTBYMAIL', 'Shipping sent by mail', 'Executed when a shipping is sent by mail', 'shipping', 20),
(24, 'MEMBER_VALIDATE', 'Member validated', 'Executed when a member is validated', 'member', 21),
(25, 'MEMBER_SUBSCRIPTION', 'Member subscribed', 'Executed when a member is subscribed', 'member', 22),
(26, 'MEMBER_RESILIATE', 'Member resiliated', 'Executed when a member is resiliated', 'member', 23),
(27, 'MEMBER_DELETE', 'Member deleted', 'Executed when a member is deleted', 'member', 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_availability`
--

CREATE TABLE IF NOT EXISTS `llx_c_availability` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `label` varchar(60) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_availability` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_c_availability`
--

INSERT INTO `llx_c_availability` (`rowid`, `code`, `label`, `active`) VALUES
(1, 'AV_NOW', 'Immediate', 1),
(2, 'AV_1W', '1 week', 1),
(3, 'AV_2W', '2 weeks', 1),
(4, 'AV_3W', '3 weeks', 1),
(5, 'BV_1D', '1 dia', 1),
(6, 'BV_2D', '2 dias', 1),
(7, 'BV_3D', '3 dias', 1),
(8, 'BV_4D', '4 dias', 1),
(9, 'BV_5D', '5 dias', 1),
(10, 'BV_6D', '6 dias', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_barcode_type`
--

CREATE TABLE IF NOT EXISTS `llx_c_barcode_type` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `libelle` varchar(50) NOT NULL,
  `coder` varchar(16) NOT NULL,
  `example` varchar(16) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_barcode_type` (`code`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `llx_c_barcode_type`
--

INSERT INTO `llx_c_barcode_type` (`rowid`, `code`, `entity`, `libelle`, `coder`, `example`) VALUES
(1, 'EAN8', 1, 'EAN8', '0', '1234567'),
(2, 'EAN13', 1, 'EAN13', '0', '123456789012'),
(3, 'UPC', 1, 'UPC', '0', '123456789012'),
(4, 'ISBN', 1, 'ISBN', '0', '123456789'),
(5, 'C39', 1, 'Code 39', '0', '1234567890'),
(6, 'C128', 1, 'Code 128', '0', 'ABCD1234567890');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_chargesociales`
--

CREATE TABLE IF NOT EXISTS `llx_c_chargesociales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) DEFAULT NULL,
  `deductible` smallint(6) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `code` varchar(12) NOT NULL,
  `accountancy_code` varchar(15) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=231 ;

--
-- Volcado de datos para la tabla `llx_c_chargesociales`
--

INSERT INTO `llx_c_chargesociales` (`id`, `libelle`, `deductible`, `active`, `code`, `accountancy_code`, `fk_pays`, `module`) VALUES
(1, 'Allocations familiales', 1, 1, 'TAXFAM', NULL, 1, NULL),
(2, 'CSG Deductible', 1, 1, 'TAXCSGD', NULL, 1, NULL),
(3, 'CSG/CRDS NON Deductible', 0, 1, 'TAXCSGND', NULL, 1, NULL),
(10, 'Taxe apprentissage', 0, 1, 'TAXAPP', NULL, 1, NULL),
(11, 'Taxe professionnelle', 0, 1, 'TAXPRO', NULL, 1, NULL),
(12, 'Cotisation fonciere des entreprises', 0, 1, 'TAXCFE', NULL, 1, NULL),
(13, 'Cotisation sur la valeur ajoutee des entreprises', 0, 1, 'TAXCVAE', NULL, 1, NULL),
(20, 'Impots locaux/fonciers', 0, 1, 'TAXFON', NULL, 1, NULL),
(25, 'Impots revenus', 0, 1, 'TAXREV', NULL, 1, NULL),
(30, 'Assurance Sante', 0, 1, 'TAXSECU', NULL, 1, NULL),
(40, 'Mutuelle', 0, 1, 'TAXMUT', NULL, 1, NULL),
(50, 'Assurance vieillesse', 0, 1, 'TAXRET', NULL, 1, NULL),
(60, 'Assurance Chomage', 0, 1, 'TAXCHOM', NULL, 1, NULL),
(201, 'ONSS', 1, 1, 'TAXBEONSS', NULL, 2, NULL),
(210, 'Precompte professionnel', 1, 1, 'TAXBEPREPRO', NULL, 2, NULL),
(220, 'Prime existence', 1, 1, 'TAXBEPRIEXI', NULL, 2, NULL),
(230, 'Precompte immobilier', 1, 1, 'TAXBEPREIMMO', NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_civilite`
--

CREATE TABLE IF NOT EXISTS `llx_c_civilite` (
  `rowid` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `civilite` varchar(50) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_civilite` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_civilite`
--

INSERT INTO `llx_c_civilite` (`rowid`, `code`, `civilite`, `active`, `module`) VALUES
(1, 'MME', 'Madame', 1, NULL),
(3, 'MR', 'Monsieur', 1, NULL),
(5, 'MLE', 'Mademoiselle', 1, NULL),
(7, 'MTRE', 'Maître', 1, NULL),
(8, 'DR', 'Docteur', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_currencies`
--

CREATE TABLE IF NOT EXISTS `llx_c_currencies` (
  `code_iso` varchar(3) NOT NULL,
  `label` varchar(64) NOT NULL,
  `unicode` varchar(32) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`code_iso`),
  UNIQUE KEY `uk_c_currencies_code_iso` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_currencies`
--

INSERT INTO `llx_c_currencies` (`code_iso`, `label`, `unicode`, `active`) VALUES
('AED', 'United Arab Emirates Dirham', NULL, 1),
('AFN', 'Afghanistan Afghani', '[1547]', 0),
('ALL', 'Albania Lek', '[76,101,107]', 0),
('ANG', 'Netherlands Antilles Guilder', '[402]', 1),
('ARP', 'Pesos argentins', NULL, 0),
('ARS', 'Argentino Peso', '[36]', 0),
('ATS', 'Shiliing autrichiens', NULL, 0),
('AUD', 'Australia Dollar', '[36]', 0),
('AWG', 'Aruba Guilder', '[402]', 0),
('AZN', 'Azerbaijan New Manat', '[1084,1072,1085]', 0),
('BAM', 'Bosnia and Herzegovina Convertible Marka', '[75,77]', 0),
('BBD', 'Barbados Dollar', '[36]', 0),
('BEF', 'Francs belges', NULL, 0),
('BGN', 'Bulgaria Lev', '[1083,1074]', 1),
('BMD', 'Bermuda Dollar', '[36]', 0),
('BND', 'Brunei Darussalam Dollar', '[36]', 1),
('BRL', 'Brazil Real', '[82,36]', 1),
('Bs.', 'Bolivia Boliviano', 'B$.', 1),
('BSD', 'Bahamas Dollar', '[36]', 0),
('BWP', 'Botswana Pula', '[80]', 0),
('BYR', 'Belarus Ruble', '[112,46]', 0),
('BZD', 'Belize Dollar', '[66,90,36]', 0),
('CAD', 'Canada Dollar', '[36]', 1),
('CHF', 'Switzerland Franc', '[67,72,70]', 1),
('CLP', 'Chile Peso', '[36]', 1),
('CNY', 'China Yuan Renminbi', '[165]', 1),
('COP', 'Colombia Peso', '[36]', 1),
('CRC', 'Costa Rica Colon', '[8353]', 1),
('CUP', 'Cuba Peso', '[8369]', 1),
('CZK', 'Czech Republic Koruna', '[75,269]', 1),
('DEM', 'Deutsch mark', NULL, 0),
('DKK', 'Denmark Krone', '[107,114]', 1),
('DOP', 'Dominican Republic Peso', '[82,68,36]', 1),
('DZD', 'Algeria Dinar', NULL, 0),
('EEK', 'Estonia Kroon', '[107,114]', 1),
('EGP', 'Egypt Pound', '[163]', 1),
('ESP', 'Pesete', NULL, 0),
('EUR', 'Euro Member Countries', '[8364]', 1),
('FIM', 'Mark finlandais', NULL, 0),
('FJD', 'Fiji Dollar', '[36]', 1),
('FKP', 'Falkland Islands (Malvinas) Pound', '[163]', 1),
('FRF', 'Francs francais', NULL, 0),
('GBP', 'United Kingdom Pound', '[163]', 1),
('GGP', 'Guernsey Pound', '[163]', 1),
('GHC', 'Ghana Cedis', '[162]', 1),
('GIP', 'Gibraltar Pound', '[163]', 1),
('GRD', 'Drachme (grece)', NULL, 0),
('GTQ', 'Guatemala Quetzal', '[81]', 1),
('GYD', 'Guyana Dollar', '[36]', 1),
('HKD', 'Hong Kong Dollar', '[36]', 1),
('HNL', 'Honduras Lempira', '[76]', 1),
('HRK', 'Croatia Kuna', '[107,110]', 1),
('HUF', 'Hungary Forint', '[70,116]', 1),
('IDR', 'Indonesia Rupiah', '[82,112]', 1),
('IEP', 'Livres irlandaises', NULL, 0),
('ILS', 'Israel Shekel', '[8362]', 1),
('IMP', 'Isle of Man Pound', '[163]', 1),
('INR', 'India Rupee', NULL, 1),
('IRR', 'Iran Rial', '[65020]', 1),
('ISK', 'Iceland Krona', '[107,114]', 1),
('ITL', 'Lires', NULL, 0),
('JEP', 'Jersey Pound', '[163]', 1),
('JMD', 'Jamaica Dollar', '[74,36]', 1),
('JPY', 'Japan Yen', '[165]', 1),
('KGS', 'Kyrgyzstan Som', '[1083,1074]', 1),
('KHR', 'Cambodia Riel', '[6107]', 1),
('KPW', 'Korea (North) Won', '[8361]', 1),
('KRW', 'Korea (South) Won', '[8361]', 1),
('KYD', 'Cayman Islands Dollar', '[36]', 1),
('KZT', 'Kazakhstan Tenge', '[1083,1074]', 1),
('LAK', 'Laos Kip', '[8365]', 1),
('LBP', 'Lebanon Pound', '[163]', 1),
('LKR', 'Sri Lanka Rupee', '[8360]', 1),
('LRD', 'Liberia Dollar', '[36]', 1),
('LTL', 'Lithuania Litas', '[76,116]', 1),
('LUF', 'Francs luxembourgeois', NULL, 0),
('LVL', 'Latvia Lat', '[76,115]', 1),
('MAD', 'Morocco Dirham', NULL, 1),
('MKD', 'Macedonia Denar', '[1076,1077,1085]', 1),
('MNT', 'Mongolia Tughrik', '[8366]', 1),
('MRO', 'Mauritania Ouguiya', NULL, 1),
('MUR', 'Mauritius Rupee', '[8360]', 1),
('MXN', 'Mexico Peso', '[36]', 1),
('MXP', 'Pesos Mexicans', NULL, 0),
('MYR', 'Malaysia Ringgit', '[82,77]', 1),
('MZN', 'Mozambique Metical', '[77,84]', 1),
('NAD', 'Namibia Dollar', '[36]', 1),
('NGN', 'Nigeria Naira', '[8358]', 1),
('NIO', 'Nicaragua Cordoba', '[67,36]', 1),
('NLG', 'Florins', NULL, 0),
('NOK', 'Norway Krone', '[107,114]', 1),
('NPR', 'Nepal Rupee', '[8360]', 1),
('NZD', 'New Zealand Dollar', '[36]', 1),
('OMR', 'Oman Rial', '[65020]', 1),
('PAB', 'Panama Balboa', '[66,47,46]', 1),
('PEN', 'Peru Nuevo Sol', '[83,47,46]', 1),
('PHP', 'Philippines Peso', '[8369]', 1),
('PKR', 'Pakistan Rupee', '[8360]', 1),
('PLN', 'Poland Zloty', '[122,322]', 1),
('PTE', 'Escudos', NULL, 0),
('PYG', 'Paraguay Guarani', '[71,115]', 1),
('QAR', 'Qatar Riyal', '[65020]', 1),
('RON', 'Romania New Leu', '[108,101,105]', 1),
('RSD', 'Serbia Dinar', '[1044,1080,1085,46]', 1),
('RUB', 'Russia Ruble', '[1088,1091,1073]', 1),
('SAR', 'Saudi Arabia Riyal', '[65020]', 1),
('SBD', 'Solomon Islands Dollar', '[36]', 1),
('SCR', 'Seychelles Rupee', '[8360]', 1),
('SEK', 'Sweden Krona', '[107,114]', 1),
('SGD', 'Singapore Dollar', '[36]', 1),
('SHP', 'Saint Helena Pound', '[163]', 1),
('SKK', 'Couronnes slovaques', NULL, 0),
('SOS', 'Somalia Shilling', '[83]', 1),
('SRD', 'Suriname Dollar', '[36]', 1),
('SUR', 'Rouble', NULL, 0),
('SVC', 'El Salvador Colon', '[36]', 1),
('SYP', 'Syria Pound', '[163]', 1),
('THB', 'Thailand Baht', '[3647]', 1),
('TND', 'Tunisia Dinar', NULL, 1),
('TRL', 'Turkey Lira', '[84,76]', 1),
('TRY', 'Turkey Lira', '[8356]', 1),
('TTD', 'Trinidad and Tobago Dollar', '[84,84,36]', 1),
('TVD', 'Tuvalu Dollar', '[36]', 1),
('TWD', 'Taiwan New Dollar', '[78,84,36]', 1),
('UAH', 'Ukraine Hryvna', '[8372]', 1),
('USD', 'United States Dollar', '[36]', 1),
('UYU', 'Uruguay Peso', '[36,85]', 1),
('UZS', 'Uzbekistan Som', '[1083,1074]', 1),
('VEF', 'Venezuela Bolivar Fuerte', '[66,115]', 1),
('VND', 'Viet Nam Dong', '[8363]', 1),
('XAF', 'Communaute Financiere Africaine (BEAC) CFA Franc', NULL, 1),
('XCD', 'East Caribbean Dollar', '[36]', 1),
('XEU', 'Ecus', NULL, 0),
('XOF', 'Communaute Financiere Africaine (BCEAO) Franc', NULL, 1),
('YER', 'Yemen Rial', '[65020]', 1),
('ZAR', 'South Africa Rand', '[82]', 1),
('ZWD', 'Zimbabwe Dollar', '[90,36]', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_departements`
--

CREATE TABLE IF NOT EXISTS `llx_c_departements` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code_departement` varchar(6) NOT NULL,
  `fk_region` int(11) DEFAULT NULL,
  `cheflieu` varchar(50) DEFAULT NULL,
  `tncc` int(11) DEFAULT NULL,
  `ncc` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_departements` (`code_departement`,`fk_region`),
  KEY `idx_departements_fk_region` (`fk_region`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=651 ;

--
-- Volcado de datos para la tabla `llx_c_departements`
--

INSERT INTO `llx_c_departements` (`rowid`, `code_departement`, `fk_region`, `cheflieu`, `tncc`, `ncc`, `nom`, `active`) VALUES
(1, '0', 0, '0', 0, '-', '-', 1),
(2, '01', 82, '01053', 5, 'AIN', 'Ain', 1),
(3, '02', 22, '02408', 5, 'AISNE', 'Aisne', 1),
(4, '03', 83, '03190', 5, 'ALLIER', 'Allier', 1),
(5, '04', 93, '04070', 4, 'ALPES-DE-HAUTE-PROVENCE', 'Alpes-de-Haute-Provence', 1),
(6, '05', 93, '05061', 4, 'HAUTES-ALPES', 'Hautes-Alpes', 1),
(7, '06', 93, '06088', 4, 'ALPES-MARITIMES', 'Alpes-Maritimes', 1),
(8, '07', 82, '07186', 5, 'ARDECHE', 'Ardèche', 1),
(9, '08', 21, '08105', 4, 'ARDENNES', 'Ardennes', 1),
(10, '09', 73, '09122', 5, 'ARIEGE', 'Ariège', 1),
(11, '10', 21, '10387', 5, 'AUBE', 'Aube', 1),
(12, '11', 91, '11069', 5, 'AUDE', 'Aude', 1),
(13, '12', 73, '12202', 5, 'AVEYRON', 'Aveyron', 1),
(14, '13', 93, '13055', 4, 'BOUCHES-DU-RHONE', 'Bouches-du-Rhône', 1),
(15, '14', 25, '14118', 2, 'CALVADOS', 'Calvados', 1),
(16, '15', 83, '15014', 2, 'CANTAL', 'Cantal', 1),
(17, '16', 54, '16015', 3, 'CHARENTE', 'Charente', 1),
(18, '17', 54, '17300', 3, 'CHARENTE-MARITIME', 'Charente-Maritime', 1),
(19, '18', 24, '18033', 2, 'CHER', 'Cher', 1),
(20, '19', 74, '19272', 3, 'CORREZE', 'Corrèze', 1),
(21, '2A', 94, '2A004', 3, 'CORSE-DU-SUD', 'Corse-du-Sud', 1),
(22, '2B', 94, '2B033', 3, 'HAUTE-CORSE', 'Haute-Corse', 1),
(23, '21', 26, '21231', 3, 'COTE-D OR', 'Côte-d Or', 1),
(24, '22', 53, '22278', 4, 'COTES-D ARMOR', 'Côtes-d Armor', 1),
(25, '23', 74, '23096', 3, 'CREUSE', 'Creuse', 1),
(26, '24', 72, '24322', 3, 'DORDOGNE', 'Dordogne', 1),
(27, '25', 43, '25056', 2, 'DOUBS', 'Doubs', 1),
(28, '26', 82, '26362', 3, 'DROME', 'Drôme', 1),
(29, '27', 23, '27229', 5, 'EURE', 'Eure', 1),
(30, '28', 24, '28085', 1, 'EURE-ET-LOIR', 'Eure-et-Loir', 1),
(31, '29', 53, '29232', 2, 'FINISTERE', 'Finistère', 1),
(32, '30', 91, '30189', 2, 'GARD', 'Gard', 1),
(33, '31', 73, '31555', 3, 'HAUTE-GARONNE', 'Haute-Garonne', 1),
(34, '32', 73, '32013', 2, 'GERS', 'Gers', 1),
(35, '33', 72, '33063', 3, 'GIRONDE', 'Gironde', 1),
(36, '34', 91, '34172', 5, 'HERAULT', 'Hérault', 1),
(37, '35', 53, '35238', 1, 'ILLE-ET-VILAINE', 'Ille-et-Vilaine', 1),
(38, '36', 24, '36044', 5, 'INDRE', 'Indre', 1),
(39, '37', 24, '37261', 1, 'INDRE-ET-LOIRE', 'Indre-et-Loire', 1),
(40, '38', 82, '38185', 5, 'ISERE', 'Isère', 1),
(41, '39', 43, '39300', 2, 'JURA', 'Jura', 1),
(42, '40', 72, '40192', 4, 'LANDES', 'Landes', 1),
(43, '41', 24, '41018', 0, 'LOIR-ET-CHER', 'Loir-et-Cher', 1),
(44, '42', 82, '42218', 3, 'LOIRE', 'Loire', 1),
(45, '43', 83, '43157', 3, 'HAUTE-LOIRE', 'Haute-Loire', 1),
(46, '44', 52, '44109', 3, 'LOIRE-ATLANTIQUE', 'Loire-Atlantique', 1),
(47, '45', 24, '45234', 2, 'LOIRET', 'Loiret', 1),
(48, '46', 73, '46042', 2, 'LOT', 'Lot', 1),
(49, '47', 72, '47001', 0, 'LOT-ET-GARONNE', 'Lot-et-Garonne', 1),
(50, '48', 91, '48095', 3, 'LOZERE', 'Lozère', 1),
(51, '49', 52, '49007', 0, 'MAINE-ET-LOIRE', 'Maine-et-Loire', 1),
(52, '50', 25, '50502', 3, 'MANCHE', 'Manche', 1),
(53, '51', 21, '51108', 3, 'MARNE', 'Marne', 1),
(54, '52', 21, '52121', 3, 'HAUTE-MARNE', 'Haute-Marne', 1),
(55, '53', 52, '53130', 3, 'MAYENNE', 'Mayenne', 1),
(56, '54', 41, '54395', 0, 'MEURTHE-ET-MOSELLE', 'Meurthe-et-Moselle', 1),
(57, '55', 41, '55029', 3, 'MEUSE', 'Meuse', 1),
(58, '56', 53, '56260', 2, 'MORBIHAN', 'Morbihan', 1),
(59, '57', 41, '57463', 3, 'MOSELLE', 'Moselle', 1),
(60, '58', 26, '58194', 3, 'NIEVRE', 'Nièvre', 1),
(61, '59', 31, '59350', 2, 'NORD', 'Nord', 1),
(62, '60', 22, '60057', 5, 'OISE', 'Oise', 1),
(63, '61', 25, '61001', 5, 'ORNE', 'Orne', 1),
(64, '62', 31, '62041', 2, 'PAS-DE-CALAIS', 'Pas-de-Calais', 1),
(65, '63', 83, '63113', 2, 'PUY-DE-DOME', 'Puy-de-Dôme', 1),
(66, '64', 72, '64445', 4, 'PYRENEES-ATLANTIQUES', 'Pyrénées-Atlantiques', 1),
(67, '65', 73, '65440', 4, 'HAUTES-PYRENEES', 'Hautes-Pyrénées', 1),
(68, '66', 91, '66136', 4, 'PYRENEES-ORIENTALES', 'Pyrénées-Orientales', 1),
(69, '67', 42, '67482', 2, 'BAS-RHIN', 'Bas-Rhin', 1),
(70, '68', 42, '68066', 2, 'HAUT-RHIN', 'Haut-Rhin', 1),
(71, '69', 82, '69123', 2, 'RHONE', 'Rhône', 1),
(72, '70', 43, '70550', 3, 'HAUTE-SAONE', 'Haute-Saône', 1),
(73, '71', 26, '71270', 0, 'SAONE-ET-LOIRE', 'Saône-et-Loire', 1),
(74, '72', 52, '72181', 3, 'SARTHE', 'Sarthe', 1),
(75, '73', 82, '73065', 3, 'SAVOIE', 'Savoie', 1),
(76, '74', 82, '74010', 3, 'HAUTE-SAVOIE', 'Haute-Savoie', 1),
(77, '75', 11, '75056', 0, 'PARIS', 'Paris', 1),
(78, '76', 23, '76540', 3, 'SEINE-MARITIME', 'Seine-Maritime', 1),
(79, '77', 11, '77288', 0, 'SEINE-ET-MARNE', 'Seine-et-Marne', 1),
(80, '78', 11, '78646', 4, 'YVELINES', 'Yvelines', 1),
(81, '79', 54, '79191', 4, 'DEUX-SEVRES', 'Deux-Sèvres', 1),
(82, '80', 22, '80021', 3, 'SOMME', 'Somme', 1),
(83, '81', 73, '81004', 2, 'TARN', 'Tarn', 1),
(84, '82', 73, '82121', 0, 'TARN-ET-GARONNE', 'Tarn-et-Garonne', 1),
(85, '83', 93, '83137', 2, 'VAR', 'Var', 1),
(86, '84', 93, '84007', 0, 'VAUCLUSE', 'Vaucluse', 1),
(87, '85', 52, '85191', 3, 'VENDEE', 'Vendée', 1),
(88, '86', 54, '86194', 3, 'VIENNE', 'Vienne', 1),
(89, '87', 74, '87085', 3, 'HAUTE-VIENNE', 'Haute-Vienne', 1),
(90, '88', 41, '88160', 4, 'VOSGES', 'Vosges', 1),
(91, '89', 26, '89024', 5, 'YONNE', 'Yonne', 1),
(92, '90', 43, '90010', 0, 'TERRITOIRE DE BELFORT', 'Territoire de Belfort', 1),
(93, '91', 11, '91228', 5, 'ESSONNE', 'Essonne', 1),
(94, '92', 11, '92050', 4, 'HAUTS-DE-SEINE', 'Hauts-de-Seine', 1),
(95, '93', 11, '93008', 3, 'SEINE-SAINT-DENIS', 'Seine-Saint-Denis', 1),
(96, '94', 11, '94028', 2, 'VAL-DE-MARNE', 'Val-de-Marne', 1),
(97, '95', 11, '95500', 2, 'VAL-D OISE', 'Val-d Oise', 1),
(98, '971', 1, '97105', 3, 'GUADELOUPE', 'Guadeloupe', 1),
(99, '972', 2, '97209', 3, 'MARTINIQUE', 'Martinique', 1),
(100, '973', 3, '97302', 3, 'GUYANE', 'Guyane', 1),
(101, '974', 4, '97411', 3, 'REUNION', 'Réunion', 1),
(102, '01', 201, '', 1, 'ANVERS', 'Anvers', 1),
(103, '02', 203, '', 3, 'BRUXELLES-CAPITALE', 'Bruxelles-Capitale', 1),
(104, '03', 202, '', 2, 'BRABANT-WALLON', 'Brabant-Wallon', 1),
(105, '04', 201, '', 1, 'BRABANT-FLAMAND', 'Brabant-Flamand', 1),
(106, '05', 201, '', 1, 'FLANDRE-OCCIDENTALE', 'Flandre-Occidentale', 1),
(107, '06', 201, '', 1, 'FLANDRE-ORIENTALE', 'Flandre-Orientale', 1),
(108, '07', 202, '', 2, 'HAINAUT', 'Hainaut', 1),
(109, '08', 201, '', 2, 'LIEGE', 'Liège', 1),
(110, '09', 202, '', 1, 'LIMBOURG', 'Limbourg', 1),
(111, '10', 202, '', 2, 'LUXEMBOURG', 'Luxembourg', 1),
(112, '11', 201, '', 2, 'NAMUR', 'Namur', 1),
(113, 'AG', 315, NULL, NULL, NULL, 'AGRIGENTO', 1),
(114, 'AL', 312, NULL, NULL, NULL, 'ALESSANDRIA', 1),
(115, 'AN', 310, NULL, NULL, NULL, 'ANCONA', 1),
(116, 'AO', 319, NULL, NULL, NULL, 'AOSTA', 1),
(117, 'AR', 316, NULL, NULL, NULL, 'AREZZO', 1),
(118, 'AP', 310, NULL, NULL, NULL, 'ASCOLI PICENO', 1),
(119, 'AT', 312, NULL, NULL, NULL, 'ASTI', 1),
(120, 'AV', 304, NULL, NULL, NULL, 'AVELLINO', 1),
(121, 'BA', 313, NULL, NULL, NULL, 'BARI', 1),
(122, 'BT', 313, NULL, NULL, NULL, 'BARLETTA-ANDRIA-TRANI', 1),
(123, 'BL', 320, NULL, NULL, NULL, 'BELLUNO', 1),
(124, 'BN', 304, NULL, NULL, NULL, 'BENEVENTO', 1),
(125, 'BG', 309, NULL, NULL, NULL, 'BERGAMO', 1),
(126, 'BI', 312, NULL, NULL, NULL, 'BIELLA', 1),
(127, 'BO', 305, NULL, NULL, NULL, 'BOLOGNA', 1),
(128, 'BZ', 317, NULL, NULL, NULL, 'BOLZANO', 1),
(129, 'BS', 309, NULL, NULL, NULL, 'BRESCIA', 1),
(130, 'BR', 313, NULL, NULL, NULL, 'BRINDISI', 1),
(131, 'CA', 314, NULL, NULL, NULL, 'CAGLIARI', 1),
(132, 'CL', 315, NULL, NULL, NULL, 'CALTANISSETTA', 1),
(133, 'CB', 311, NULL, NULL, NULL, 'CAMPOBASSO', 1),
(134, 'CI', 314, NULL, NULL, NULL, 'CARBONIA-IGLESIAS', 1),
(135, 'CE', 304, NULL, NULL, NULL, 'CASERTA', 1),
(136, 'CT', 315, NULL, NULL, NULL, 'CATANIA', 1),
(137, 'CZ', 303, NULL, NULL, NULL, 'CATANZARO', 1),
(138, 'CH', 301, NULL, NULL, NULL, 'CHIETI', 1),
(139, 'CO', 309, NULL, NULL, NULL, 'COMO', 1),
(140, 'CS', 303, NULL, NULL, NULL, 'COSENZA', 1),
(141, 'CR', 309, NULL, NULL, NULL, 'CREMONA', 1),
(142, 'KR', 303, NULL, NULL, NULL, 'CROTONE', 1),
(143, 'CN', 312, NULL, NULL, NULL, 'CUNEO', 1),
(144, 'EN', 315, NULL, NULL, NULL, 'ENNA', 1),
(145, 'FM', 310, NULL, NULL, NULL, 'FERMO', 1),
(146, 'FE', 305, NULL, NULL, NULL, 'FERRARA', 1),
(147, 'FI', 316, NULL, NULL, NULL, 'FIRENZE', 1),
(148, 'FG', 313, NULL, NULL, NULL, 'FOGGIA', 1),
(149, 'FC', 305, NULL, NULL, NULL, 'FORLI-CESENA', 1),
(150, 'FR', 307, NULL, NULL, NULL, 'FROSINONE', 1),
(151, 'GE', 308, NULL, NULL, NULL, 'GENOVA', 1),
(152, 'GO', 306, NULL, NULL, NULL, 'GORIZIA', 1),
(153, 'GR', 316, NULL, NULL, NULL, 'GROSSETO', 1),
(154, 'IM', 308, NULL, NULL, NULL, 'IMPERIA', 1),
(155, 'IS', 311, NULL, NULL, NULL, 'ISERNIA', 1),
(156, 'SP', 308, NULL, NULL, NULL, 'LA SPEZIA', 1),
(157, 'AQ', 301, NULL, NULL, NULL, 'L AQUILA', 1),
(158, 'LT', 307, NULL, NULL, NULL, 'LATINA', 1),
(159, 'LE', 313, NULL, NULL, NULL, 'LECCE', 1),
(160, 'LC', 309, NULL, NULL, NULL, 'LECCO', 1),
(161, 'LI', 314, NULL, NULL, NULL, 'LIVORNO', 1),
(162, 'LO', 309, NULL, NULL, NULL, 'LODI', 1),
(163, 'LU', 316, NULL, NULL, NULL, 'LUCCA', 1),
(164, 'MC', 310, NULL, NULL, NULL, 'MACERATA', 1),
(165, 'MN', 309, NULL, NULL, NULL, 'MANTOVA', 1),
(166, 'MS', 316, NULL, NULL, NULL, 'MASSA-CARRARA', 1),
(167, 'MT', 302, NULL, NULL, NULL, 'MATERA', 1),
(168, 'VS', 314, NULL, NULL, NULL, 'MEDIO CAMPIDANO', 1),
(169, 'ME', 315, NULL, NULL, NULL, 'MESSINA', 1),
(170, 'MI', 309, NULL, NULL, NULL, 'MILANO', 1),
(171, 'MB', 309, NULL, NULL, NULL, 'MONZA e BRIANZA', 1),
(172, 'MO', 305, NULL, NULL, NULL, 'MODENA', 1),
(173, 'NA', 304, NULL, NULL, NULL, 'NAPOLI', 1),
(174, 'NO', 312, NULL, NULL, NULL, 'NOVARA', 1),
(175, 'NU', 314, NULL, NULL, NULL, 'NUORO', 1),
(176, 'OG', 314, NULL, NULL, NULL, 'OGLIASTRA', 1),
(177, 'OT', 314, NULL, NULL, NULL, 'OLBIA-TEMPIO', 1),
(178, 'OR', 314, NULL, NULL, NULL, 'ORISTANO', 1),
(179, 'PD', 320, NULL, NULL, NULL, 'PADOVA', 1),
(180, 'PA', 315, NULL, NULL, NULL, 'PALERMO', 1),
(181, 'PR', 305, NULL, NULL, NULL, 'PARMA', 1),
(182, 'PV', 309, NULL, NULL, NULL, 'PAVIA', 1),
(183, 'PG', 318, NULL, NULL, NULL, 'PERUGIA', 1),
(184, 'PU', 310, NULL, NULL, NULL, 'PESARO e URBINO', 1),
(185, 'PE', 301, NULL, NULL, NULL, 'PESCARA', 1),
(186, 'PC', 305, NULL, NULL, NULL, 'PIACENZA', 1),
(187, 'PI', 316, NULL, NULL, NULL, 'PISA', 1),
(188, 'PT', 316, NULL, NULL, NULL, 'PISTOIA', 1),
(189, 'PN', 306, NULL, NULL, NULL, 'PORDENONE', 1),
(190, 'PZ', 302, NULL, NULL, NULL, 'POTENZA', 1),
(191, 'PO', 316, NULL, NULL, NULL, 'PRATO', 1),
(192, 'RG', 315, NULL, NULL, NULL, 'RAGUSA', 1),
(193, 'RA', 305, NULL, NULL, NULL, 'RAVENNA', 1),
(194, 'RC', 303, NULL, NULL, NULL, 'REGGIO CALABRIA', 1),
(195, 'RE', 305, NULL, NULL, NULL, 'REGGIO NELL EMILIA', 1),
(196, 'RI', 307, NULL, NULL, NULL, 'RIETI', 1),
(197, 'RN', 305, NULL, NULL, NULL, 'RIMINI', 1),
(198, 'RM', 307, NULL, NULL, NULL, 'ROMA', 1),
(199, 'RO', 320, NULL, NULL, NULL, 'ROVIGO', 1),
(200, 'SA', 304, NULL, NULL, NULL, 'SALERNO', 1),
(201, 'SS', 314, NULL, NULL, NULL, 'SASSARI', 1),
(202, 'SV', 308, NULL, NULL, NULL, 'SAVONA', 1),
(203, 'SI', 316, NULL, NULL, NULL, 'SIENA', 1),
(204, 'SR', 315, NULL, NULL, NULL, 'SIRACUSA', 1),
(205, 'SO', 309, NULL, NULL, NULL, 'SONDRIO', 1),
(206, 'TA', 313, NULL, NULL, NULL, 'TARANTO', 1),
(207, 'TE', 301, NULL, NULL, NULL, 'TERAMO', 1),
(208, 'TR', 318, NULL, NULL, NULL, 'TERNI', 1),
(209, 'TO', 312, NULL, NULL, NULL, 'TORINO', 1),
(210, 'TP', 315, NULL, NULL, NULL, 'TRAPANI', 1),
(211, 'TN', 317, NULL, NULL, NULL, 'TRENTO', 1),
(212, 'TV', 320, NULL, NULL, NULL, 'TREVISO', 1),
(213, 'TS', 306, NULL, NULL, NULL, 'TRIESTE', 1),
(214, 'UD', 306, NULL, NULL, NULL, 'UDINE', 1),
(215, 'VA', 309, NULL, NULL, NULL, 'VARESE', 1),
(216, 'VE', 320, NULL, NULL, NULL, 'VENEZIA', 1),
(217, 'VB', 312, NULL, NULL, NULL, 'VERBANO-CUSIO-OSSOLA', 1),
(218, 'VC', 312, NULL, NULL, NULL, 'VERCELLI', 1),
(219, 'VR', 320, NULL, NULL, NULL, 'VERONA', 1),
(220, 'VV', 303, NULL, NULL, NULL, 'VIBO VALENTIA', 1),
(221, 'VI', 320, NULL, NULL, NULL, 'VICENZA', 1),
(222, 'VT', 307, NULL, NULL, NULL, 'VITERBO', 1),
(223, 'NSW', 2801, '', 1, '', 'New South Wales', 1),
(224, 'VIC', 2801, '', 1, '', 'Victoria', 1),
(225, 'QLD', 2801, '', 1, '', 'Queensland', 1),
(226, 'SA', 2801, '', 1, '', 'South Australia', 1),
(227, 'ACT', 2801, '', 1, '', 'Australia Capital Territory', 1),
(228, 'TAS', 2801, '', 1, '', 'Tasmania', 1),
(229, 'WA', 2801, '', 1, '', 'Western Australia', 1),
(230, 'NT', 2801, '', 1, '', 'Northern Territory', 1),
(231, '01', 419, '', 19, 'ALAVA', 'Álava', 1),
(232, '02', 404, '', 4, 'ALBACETE', 'Albacete', 1),
(233, '03', 411, '', 11, 'ALICANTE', 'Alicante', 1),
(234, '04', 401, '', 1, 'ALMERIA', 'Almería', 1),
(235, '05', 403, '', 3, 'AVILA', 'Avila', 1),
(236, '06', 412, '', 12, 'BADAJOZ', 'Badajoz', 1),
(237, '07', 414, '', 14, 'ISLAS BALEARES', 'Islas Baleares', 1),
(238, '08', 406, '', 6, 'BARCELONA', 'Barcelona', 1),
(239, '09', 403, '', 8, 'BURGOS', 'Burgos', 1),
(240, '10', 412, '', 12, 'CACERES', 'Cáceres', 1),
(241, '11', 401, '', 1, 'CADIz', 'Cádiz', 1),
(242, '12', 411, '', 11, 'CASTELLON', 'Castellón', 1),
(243, '13', 404, '', 4, 'CIUDAD REAL', 'Ciudad Real', 1),
(244, '14', 401, '', 1, 'CORDOBA', 'Córdoba', 1),
(245, '15', 413, '', 13, 'LA CORUÑA', 'La Coruña', 1),
(246, '16', 404, '', 4, 'CUENCA', 'Cuenca', 1),
(247, '17', 406, '', 6, 'GERONA', 'Gerona', 1),
(248, '18', 401, '', 1, 'GRANADA', 'Granada', 1),
(249, '19', 404, '', 4, 'GUADALAJARA', 'Guadalajara', 1),
(250, '20', 419, '', 19, 'GUIPUZCOA', 'Guipúzcoa', 1),
(251, '21', 401, '', 1, 'HUELVA', 'Huelva', 1),
(252, '22', 402, '', 2, 'HUESCA', 'Huesca', 1),
(253, '23', 401, '', 1, 'JAEN', 'Jaén', 1),
(254, '24', 403, '', 3, 'LEON', 'León', 1),
(255, '25', 406, '', 6, 'LERIDA', 'Lérida', 1),
(256, '26', 415, '', 15, 'LA RIOJA', 'La Rioja', 1),
(257, '27', 413, '', 13, 'LUGO', 'Lugo', 1),
(258, '28', 416, '', 16, 'MADRID', 'Madrid', 1),
(259, '29', 401, '', 1, 'MALAGA', 'Málaga', 1),
(260, '30', 417, '', 17, 'MURCIA', 'Murcia', 1),
(261, '31', 408, '', 8, 'NAVARRA', 'Navarra', 1),
(262, '32', 413, '', 13, 'ORENSE', 'Orense', 1),
(263, '33', 418, '', 18, 'ASTURIAS', 'Asturias', 1),
(264, '34', 403, '', 3, 'PALENCIA', 'Palencia', 1),
(265, '35', 405, '', 5, 'LAS PALMAS', 'Las Palmas', 1),
(266, '36', 413, '', 13, 'PONTEVEDRA', 'Pontevedra', 1),
(267, '37', 403, '', 3, 'SALAMANCA', 'Salamanca', 1),
(268, '38', 405, '', 5, 'STA. CRUZ DE TENERIFE', 'Sta. Cruz de Tenerife', 1),
(269, '39', 410, '', 10, 'CANTABRIA', 'Cantabria', 1),
(270, '40', 403, '', 3, 'SEGOVIA', 'Segovia', 1),
(271, '41', 401, '', 1, 'SEVILLA', 'Sevilla', 1),
(272, '42', 403, '', 3, 'SORIA', 'Soria', 1),
(273, '43', 406, '', 6, 'TARRAGONA', 'Tarragona', 1),
(274, '44', 402, '', 2, 'TERUEL', 'Teruel', 1),
(275, '45', 404, '', 5, 'TOLEDO', 'Toledo', 1),
(276, '46', 411, '', 11, 'VALENCIA', 'Valencia', 1),
(277, '47', 403, '', 3, 'VALLADOLID', 'Valladolid', 1),
(278, '48', 419, '', 19, 'VIZCAYA', 'Vizcaya', 1),
(279, '49', 403, '', 3, 'ZAMORA', 'Zamora', 1),
(280, '50', 402, '', 1, 'ZARAGOZA', 'Zaragoza', 1),
(281, '51', 407, '', 7, 'CEUTA', 'Ceuta', 1),
(282, '52', 409, '', 9, 'MELILLA', 'Melilla', 1),
(283, '53', 420, '', 20, 'OTROS', 'Otros', 1),
(284, 'BW', 501, NULL, NULL, 'BADEN-WÜRTTEMBERG', 'Baden-Württemberg', 1),
(285, 'BY', 501, NULL, NULL, 'BAYERN', 'Bayern', 1),
(286, 'BE', 501, NULL, NULL, 'BERLIN', 'Berlin', 1),
(287, 'BB', 501, NULL, NULL, 'BRANDENBURG', 'Brandenburg', 1),
(288, 'HB', 501, NULL, NULL, 'BREMEN', 'Bremen', 1),
(289, 'HH', 501, NULL, NULL, 'HAMBURG', 'Hamburg', 1),
(290, 'HE', 501, NULL, NULL, 'HESSEN', 'Hessen', 1),
(291, 'MV', 501, NULL, NULL, 'MECKLENBURG-VORPOMMERN', 'Mecklenburg-Vorpommern', 1),
(292, 'NI', 501, NULL, NULL, 'NIEDERSACHSEN', 'Niedersachsen', 1),
(293, 'NW', 501, NULL, NULL, 'NORDRHEIN-WESTFALEN', 'Nordrhein-Westfalen', 1),
(294, 'RP', 501, NULL, NULL, 'RHEINLAND-PFALZ', 'Rheinland-Pfalz', 1),
(295, 'SL', 501, NULL, NULL, 'SAARLAND', 'Saarland', 1),
(296, 'SN', 501, NULL, NULL, 'SACHSEN', 'Sachsen', 1),
(297, 'ST', 501, NULL, NULL, 'SACHSEN-ANHALT', 'Sachsen-Anhalt', 1),
(298, 'SH', 501, NULL, NULL, 'SCHLESWIG-HOLSTEIN', 'Schleswig-Holstein', 1),
(299, 'TH', 501, NULL, NULL, 'THÜRINGEN', 'Thüringen', 1),
(300, 'AG', 601, NULL, NULL, 'ARGOVIE', 'Argovie', 1),
(301, 'AI', 601, NULL, NULL, 'APPENZELL RHODES INTERIEURES', 'Appenzell Rhodes intérieures', 1),
(302, 'AR', 601, NULL, NULL, 'APPENZELL RHODES EXTERIEURES', 'Appenzell Rhodes extérieures', 1),
(303, 'BE', 601, NULL, NULL, 'BERNE', 'Berne', 1),
(304, 'BL', 601, NULL, NULL, 'BALE CAMPAGNE', 'Bâle Campagne', 1),
(305, 'BS', 601, NULL, NULL, 'BALE VILLE', 'Bâle Ville', 1),
(306, 'FR', 601, NULL, NULL, 'FRIBOURG', 'Fribourg', 1),
(307, 'GE', 601, NULL, NULL, 'GENEVE', 'Genève', 1),
(308, 'GL', 601, NULL, NULL, 'GLARIS', 'Glaris', 1),
(309, 'GR', 601, NULL, NULL, 'GRISONS', 'Grisons', 1),
(310, 'JU', 601, NULL, NULL, 'JURA', 'Jura', 1),
(311, 'LU', 601, NULL, NULL, 'LUCERNE', 'Lucerne', 1),
(312, 'NE', 601, NULL, NULL, 'NEUCHATEL', 'Neuchâtel', 1),
(313, 'NW', 601, NULL, NULL, 'NIDWALD', 'Nidwald', 1),
(314, 'OW', 601, NULL, NULL, 'OBWALD', 'Obwald', 1),
(315, 'SG', 601, NULL, NULL, 'SAINT-GALL', 'Saint-Gall', 1),
(316, 'SH', 601, NULL, NULL, 'SCHAFFHOUSE', 'Schaffhouse', 1),
(317, 'SO', 601, NULL, NULL, 'SOLEURE', 'Soleure', 1),
(318, 'SZ', 601, NULL, NULL, 'SCHWYZ', 'Schwyz', 1),
(319, 'TG', 601, NULL, NULL, 'THURGOVIE', 'Thurgovie', 1),
(320, 'TI', 601, NULL, NULL, 'TESSIN', 'Tessin', 1),
(321, 'UR', 601, NULL, NULL, 'URI', 'Uri', 1),
(322, 'VD', 601, NULL, NULL, 'VAUD', 'Vaud', 1),
(323, 'VS', 601, NULL, NULL, 'VALAIS', 'Valais', 1),
(324, 'ZG', 601, NULL, NULL, 'ZUG', 'Zug', 1),
(325, 'ZH', 601, NULL, NULL, 'ZURICH', 'Zürich', 1),
(326, 'AL', 1101, '', 0, 'ALABAMA', 'Alabama', 1),
(327, 'AK', 1101, '', 0, 'ALASKA', 'Alaska', 1),
(328, 'AZ', 1101, '', 0, 'ARIZONA', 'Arizona', 1),
(329, 'AR', 1101, '', 0, 'ARKANSAS', 'Arkansas', 1),
(330, 'CA', 1101, '', 0, 'CALIFORNIA', 'California', 1),
(331, 'CO', 1101, '', 0, 'COLORADO', 'Colorado', 1),
(332, 'CT', 1101, '', 0, 'CONNECTICUT', 'Connecticut', 1),
(333, 'DE', 1101, '', 0, 'DELAWARE', 'Delaware', 1),
(334, 'FL', 1101, '', 0, 'FLORIDA', 'Florida', 1),
(335, 'GA', 1101, '', 0, 'GEORGIA', 'Georgia', 1),
(336, 'HI', 1101, '', 0, 'HAWAII', 'Hawaii', 1),
(337, 'ID', 1101, '', 0, 'IDAHO', 'Idaho', 1),
(338, 'IL', 1101, '', 0, 'ILLINOIS', 'Illinois', 1),
(339, 'IN', 1101, '', 0, 'INDIANA', 'Indiana', 1),
(340, 'IA', 1101, '', 0, 'IOWA', 'Iowa', 1),
(341, 'KS', 1101, '', 0, 'KANSAS', 'Kansas', 1),
(342, 'KY', 1101, '', 0, 'KENTUCKY', 'Kentucky', 1),
(343, 'LA', 1101, '', 0, 'LOUISIANA', 'Louisiana', 1),
(344, 'ME', 1101, '', 0, 'MAINE', 'Maine', 1),
(345, 'MD', 1101, '', 0, 'MARYLAND', 'Maryland', 1),
(346, 'MA', 1101, '', 0, 'MASSACHUSSETTS', 'Massachusetts', 1),
(347, 'MI', 1101, '', 0, 'MICHIGAN', 'Michigan', 1),
(348, 'MN', 1101, '', 0, 'MINNESOTA', 'Minnesota', 1),
(349, 'MS', 1101, '', 0, 'MISSISSIPPI', 'Mississippi', 1),
(350, 'MO', 1101, '', 0, 'MISSOURI', 'Missouri', 1),
(351, 'MT', 1101, '', 0, 'MONTANA', 'Montana', 1),
(352, 'NE', 1101, '', 0, 'NEBRASKA', 'Nebraska', 1),
(353, 'NV', 1101, '', 0, 'NEVADA', 'Nevada', 1),
(354, 'NH', 1101, '', 0, 'NEW HAMPSHIRE', 'New Hampshire', 1),
(355, 'NJ', 1101, '', 0, 'NEW JERSEY', 'New Jersey', 1),
(356, 'NM', 1101, '', 0, 'NEW MEXICO', 'New Mexico', 1),
(357, 'NY', 1101, '', 0, 'NEW YORK', 'New York', 1),
(358, 'NC', 1101, '', 0, 'NORTH CAROLINA', 'North Carolina', 1),
(359, 'ND', 1101, '', 0, 'NORTH DAKOTA', 'North Dakota', 1),
(360, 'OH', 1101, '', 0, 'OHIO', 'Ohio', 1),
(361, 'OK', 1101, '', 0, 'OKLAHOMA', 'Oklahoma', 1),
(362, 'OR', 1101, '', 0, 'OREGON', 'Oregon', 1),
(363, 'PA', 1101, '', 0, 'PENNSYLVANIA', 'Pennsylvania', 1),
(364, 'RI', 1101, '', 0, 'RHODE ISLAND', 'Rhode Island', 1),
(365, 'SC', 1101, '', 0, 'SOUTH CAROLINA', 'South Carolina', 1),
(366, 'SD', 1101, '', 0, 'SOUTH DAKOTA', 'South Dakota', 1),
(367, 'TN', 1101, '', 0, 'TENNESSEE', 'Tennessee', 1),
(368, 'TX', 1101, '', 0, 'TEXAS', 'Texas', 1),
(369, 'UT', 1101, '', 0, 'UTAH', 'Utah', 1),
(370, 'VT', 1101, '', 0, 'VERMONT', 'Vermont', 1),
(371, 'VA', 1101, '', 0, 'VIRGINIA', 'Virginia', 1),
(372, 'WA', 1101, '', 0, 'WASHINGTON', 'Washington', 1),
(373, 'WV', 1101, '', 0, 'WEST VIRGINIA', 'West Virginia', 1),
(374, 'WI', 1101, '', 0, 'WISCONSIN', 'Wisconsin', 1),
(375, 'WY', 1101, '', 0, 'WYOMING', 'Wyoming', 1),
(376, 'SS', 8601, NULL, NULL, NULL, 'San Salvador', 1),
(377, 'SA', 8603, NULL, NULL, NULL, 'Santa Ana', 1),
(378, 'AH', 8603, NULL, NULL, NULL, 'Ahuachapan', 1),
(379, 'SO', 8603, NULL, NULL, NULL, 'Sonsonate', 1),
(380, 'US', 8602, NULL, NULL, NULL, 'Usulutan', 1),
(381, 'SM', 8602, NULL, NULL, NULL, 'San Miguel', 1),
(382, 'MO', 8602, NULL, NULL, NULL, 'Morazan', 1),
(383, 'LU', 8602, NULL, NULL, NULL, 'La Union', 1),
(384, 'LL', 8601, NULL, NULL, NULL, 'La Libertad', 1),
(385, 'CH', 8601, NULL, NULL, NULL, 'Chalatenango', 1),
(386, 'CA', 8601, NULL, NULL, NULL, 'Cabañas', 1),
(387, 'LP', 8601, NULL, NULL, NULL, 'La Paz', 1),
(388, 'SV', 8601, NULL, NULL, NULL, 'San Vicente', 1),
(389, 'CU', 8601, NULL, NULL, NULL, 'Cuscatlan', 1),
(390, '2301', 2301, '', 0, 'CATAMARCA', 'Catamarca', 1),
(391, '2302', 2301, '', 0, 'JUJUY', 'Jujuy', 1),
(392, '2303', 2301, '', 0, 'TUCAMAN', 'Tucamán', 1),
(393, '2304', 2301, '', 0, 'SANTIAGO DEL ESTERO', 'Santiago del Estero', 1),
(394, '2305', 2301, '', 0, 'SALTA', 'Salta', 1),
(395, '2306', 2302, '', 0, 'CHACO', 'Chaco', 1),
(396, '2307', 2302, '', 0, 'CORRIENTES', 'Corrientes', 1),
(397, '2308', 2302, '', 0, 'ENTRE RIOS', 'Entre Ríos', 1),
(398, '2309', 2302, '', 0, 'FORMOSA MISIONES', 'Formosa Misiones', 1),
(399, '2310', 2302, '', 0, 'SANTA FE', 'Santa Fe', 1),
(400, '2311', 2303, '', 0, 'LA RIOJA', 'La Rioja', 1),
(401, '2312', 2303, '', 0, 'MENDOZA', 'Mendoza', 1),
(402, '2313', 2303, '', 0, 'SAN JUAN', 'San Juan', 1),
(403, '2314', 2303, '', 0, 'SAN LUIS', 'San Luis', 1),
(404, '2315', 2304, '', 0, 'CORDOBA', 'Córdoba', 1),
(405, '2316', 2304, '', 0, 'BUENOS AIRES', 'Buenos Aires', 1),
(406, '2317', 2304, '', 0, 'CABA', 'Caba', 1),
(407, '2318', 2305, '', 0, 'LA PAMPA', 'La Pampa', 1),
(408, '2319', 2305, '', 0, 'NEUQUEN', 'Neuquén', 1),
(409, '2320', 2305, '', 0, 'RIO NEGRO', 'Río Negro', 1),
(410, '2321', 2305, '', 0, 'CHUBUT', 'Chubut', 1),
(411, '2322', 2305, '', 0, 'SANTA CRUZ', 'Santa Cruz', 1),
(412, '2323', 2305, '', 0, 'TIERRA DEL FUEGO', 'Tierra del Fuego', 1),
(413, '2324', 2305, '', 0, 'ISLAS MALVINAS', 'Islas Malvinas', 1),
(414, '2325', 2305, '', 0, 'ANTARTIDA', 'Antártida', 1),
(415, 'AC', 5601, 'ACRE', 0, 'AC', 'Acre', 1),
(416, 'AL', 5601, 'ALAGOAS', 0, 'AL', 'Alagoas', 1),
(417, 'AP', 5601, 'AMAPA', 0, 'AP', 'Amapá', 1),
(418, 'AM', 5601, 'AMAZONAS', 0, 'AM', 'Amazonas', 1),
(419, 'BA', 5601, 'BAHIA', 0, 'BA', 'Bahia', 1),
(420, 'CE', 5601, 'CEARA', 0, 'CE', 'Ceará', 1),
(421, 'ES', 5601, 'ESPIRITO SANTO', 0, 'ES', 'Espirito Santo', 1),
(422, 'GO', 5601, 'GOIAS', 0, 'GO', 'Goiás', 1),
(423, 'MA', 5601, 'MARANHAO', 0, 'MA', 'Maranhão', 1),
(424, 'MT', 5601, 'MATO GROSSO', 0, 'MT', 'Mato Grosso', 1),
(425, 'MS', 5601, 'MATO GROSSO DO SUL', 0, 'MS', 'Mato Grosso do Sul', 1),
(426, 'MG', 5601, 'MINAS GERAIS', 0, 'MG', 'Minas Gerais', 1),
(427, 'PA', 5601, 'PARA', 0, 'PA', 'Pará', 1),
(428, 'PB', 5601, 'PARAIBA', 0, 'PB', 'Paraiba', 1),
(429, 'PR', 5601, 'PARANA', 0, 'PR', 'Paraná', 1),
(430, 'PE', 5601, 'PERNAMBUCO', 0, 'PE', 'Pernambuco', 1),
(431, 'PI', 5601, 'PIAUI', 0, 'PI', 'Piauí', 1),
(432, 'RJ', 5601, 'RIO DE JANEIRO', 0, 'RJ', 'Rio de Janeiro', 1),
(433, 'RN', 5601, 'RIO GRANDE DO NORTE', 0, 'RN', 'Rio Grande do Norte', 1),
(434, 'RS', 5601, 'RIO GRANDE DO SUL', 0, 'RS', 'Rio Grande do Sul', 1),
(435, 'RO', 5601, 'RONDONIA', 0, 'RO', 'Rondônia', 1),
(436, 'RR', 5601, 'RORAIMA', 0, 'RR', 'Roraima', 1),
(437, 'SC', 5601, 'SANTA CATARINA', 0, 'SC', 'Santa Catarina', 1),
(438, 'SE', 5601, 'SERGIPE', 0, 'SE', 'Sergipe', 1),
(439, 'SP', 5601, 'SAO PAULO', 0, 'SP', 'Sao Paulo', 1),
(440, 'TO', 5601, 'TOCANTINS', 0, 'TO', 'Tocantins', 1),
(441, 'DF', 5601, 'DISTRITO FEDERAL', 0, 'DF', 'Distrito Federal', 1),
(442, '151', 6715, '', 0, '151', 'Arica', 1),
(443, '152', 6715, '', 0, '152', 'Parinacota', 1),
(444, '011', 6701, '', 0, '011', 'Iquique', 1),
(445, '014', 6701, '', 0, '014', 'Tamarugal', 1),
(446, '021', 6702, '', 0, '021', 'Antofagasa', 1),
(447, '022', 6702, '', 0, '022', 'El Loa', 1),
(448, '023', 6702, '', 0, '023', 'Tocopilla', 1),
(449, '031', 6703, '', 0, '031', 'Copiapó', 1),
(450, '032', 6703, '', 0, '032', 'Chañaral', 1),
(451, '033', 6703, '', 0, '033', 'Huasco', 1),
(452, '041', 6704, '', 0, '041', 'Elqui', 1),
(453, '042', 6704, '', 0, '042', 'Choapa', 1),
(454, '043', 6704, '', 0, '043', 'Limarí', 1),
(455, '051', 6705, '', 0, '051', 'Valparaíso', 1),
(456, '052', 6705, '', 0, '052', 'Isla de Pascua', 1),
(457, '053', 6705, '', 0, '053', 'Los Andes', 1),
(458, '054', 6705, '', 0, '054', 'Petorca', 1),
(459, '055', 6705, '', 0, '055', 'Quillota', 1),
(460, '056', 6705, '', 0, '056', 'San Antonio', 1),
(461, '057', 6705, '', 0, '057', 'San Felipe de Aconcagua', 1),
(462, '058', 6705, '', 0, '058', 'Marga Marga', 1),
(463, '061', 6706, '', 0, '061', 'Cachapoal', 1),
(464, '062', 6706, '', 0, '062', 'Cardenal Caro', 1),
(465, '063', 6706, '', 0, '063', 'Colchagua', 1),
(466, '071', 6707, '', 0, '071', 'Talca', 1),
(467, '072', 6707, '', 0, '072', 'Cauquenes', 1),
(468, '073', 6707, '', 0, '073', 'Curicó', 1),
(469, '074', 6707, '', 0, '074', 'Linares', 1),
(470, '081', 6708, '', 0, '081', 'Concepción', 1),
(471, '082', 6708, '', 0, '082', 'Arauco', 1),
(472, '083', 6708, '', 0, '083', 'Biobío', 1),
(473, '084', 6708, '', 0, '084', 'Ñuble', 1),
(474, '091', 6709, '', 0, '091', 'Cautín', 1),
(475, '092', 6709, '', 0, '092', 'Malleco', 1),
(476, '141', 6714, '', 0, '141', 'Valdivia', 1),
(477, '142', 6714, '', 0, '142', 'Ranco', 1),
(478, '101', 6710, '', 0, '101', 'Llanquihue', 1),
(479, '102', 6710, '', 0, '102', 'Chiloé', 1),
(480, '103', 6710, '', 0, '103', 'Osorno', 1),
(481, '104', 6710, '', 0, '104', 'Palena', 1),
(482, '111', 6711, '', 0, '111', 'Coihaique', 1),
(483, '112', 6711, '', 0, '112', 'Aisén', 1),
(484, '113', 6711, '', 0, '113', 'Capitán Prat', 1),
(485, '114', 6711, '', 0, '114', 'General Carrera', 1),
(486, '121', 6712, '', 0, '121', 'Magallanes', 1),
(487, '122', 6712, '', 0, '122', 'Antártica Chilena', 1),
(488, '123', 6712, '', 0, '123', 'Tierra del Fuego', 1),
(489, '124', 6712, '', 0, '124', 'Última Esperanza', 1),
(490, '131', 6713, '', 0, '131', 'Santiago', 1),
(491, '132', 6713, '', 0, '132', 'Cordillera', 1),
(492, '133', 6713, '', 0, '133', 'Chacabuco', 1),
(493, '134', 6713, '', 0, '134', 'Maipo', 1),
(494, '135', 6713, '', 0, '135', 'Melipilla', 1),
(495, '136', 6713, '', 0, '136', 'Talagante', 1),
(496, 'AN', 11701, NULL, 0, 'AN', 'Andaman & Nicobar', 1),
(497, 'AP', 11701, NULL, 0, 'AP', 'Andhra Pradesh', 1),
(498, 'AR', 11701, NULL, 0, 'AR', 'Arunachal Pradesh', 1),
(499, 'AS', 11701, NULL, 0, 'AS', 'Assam', 1),
(500, 'BR', 11701, NULL, 0, 'BR', 'Bihar', 1),
(501, 'CG', 11701, NULL, 0, 'CG', 'Chattisgarh', 1),
(502, 'CH', 11701, NULL, 0, 'CH', 'Chandigarh', 1),
(503, 'DD', 11701, NULL, 0, 'DD', 'Daman & Diu', 1),
(504, 'DL', 11701, NULL, 0, 'DL', 'Delhi', 1),
(505, 'DN', 11701, NULL, 0, 'DN', 'Dadra and Nagar Haveli', 1),
(506, 'GA', 11701, NULL, 0, 'GA', 'Goa', 1),
(507, 'GJ', 11701, NULL, 0, 'GJ', 'Gujarat', 1),
(508, 'HP', 11701, NULL, 0, 'HP', 'Himachal Pradesh', 1),
(509, 'HR', 11701, NULL, 0, 'HR', 'Haryana', 1),
(510, 'JH', 11701, NULL, 0, 'JH', 'Jharkhand', 1),
(511, 'JK', 11701, NULL, 0, 'JK', 'Jammu & Kashmir', 1),
(512, 'KA', 11701, NULL, 0, 'KA', 'Karnataka', 1),
(513, 'KL', 11701, NULL, 0, 'KL', 'Kerala', 1),
(514, 'LD', 11701, NULL, 0, 'LD', 'Lakshadweep', 1),
(515, 'MH', 11701, NULL, 0, 'MH', 'Maharashtra', 1),
(516, 'ML', 11701, NULL, 0, 'ML', 'Meghalaya', 1),
(517, 'MN', 11701, NULL, 0, 'MN', 'Manipur', 1),
(518, 'MP', 11701, NULL, 0, 'MP', 'Madhya Pradesh', 1),
(519, 'MZ', 11701, NULL, 0, 'MZ', 'Mizoram', 1),
(520, 'NL', 11701, NULL, 0, 'NL', 'Nagaland', 1),
(521, 'OR', 11701, NULL, 0, 'OR', 'Orissa', 1),
(522, 'PB', 11701, NULL, 0, 'PB', 'Punjab', 1),
(523, 'PY', 11701, NULL, 0, 'PY', 'Puducherry', 1),
(524, 'RJ', 11701, NULL, 0, 'RJ', 'Rajasthan', 1),
(525, 'SK', 11701, NULL, 0, 'SK', 'Sikkim', 1),
(526, 'TN', 11701, NULL, 0, 'TN', 'Tamil Nadu', 1),
(527, 'TR', 11701, NULL, 0, 'TR', 'Tripura', 1),
(528, 'UL', 11701, NULL, 0, 'UL', 'Uttarakhand', 1),
(529, 'UP', 11701, NULL, 0, 'UP', 'Uttar Pradesh', 1),
(530, 'WB', 11701, NULL, 0, 'WB', 'West Bengal', 1),
(531, 'DIF', 15401, '', 0, 'DIF', 'Distrito Federal', 1),
(532, 'AGS', 15401, '', 0, 'AGS', 'Aguascalientes', 1),
(533, 'BCN', 15401, '', 0, 'BCN', 'Baja California Norte', 1),
(534, 'BCS', 15401, '', 0, 'BCS', 'Baja California Sur', 1),
(535, 'CAM', 15401, '', 0, 'CAM', 'Campeche', 1),
(536, 'CHP', 15401, '', 0, 'CHP', 'Chiapas', 1),
(537, 'CHI', 15401, '', 0, 'CHI', 'Chihuahua', 1),
(538, 'COA', 15401, '', 0, 'COA', 'Coahuila', 1),
(539, 'COL', 15401, '', 0, 'COL', 'Colima', 1),
(540, 'DUR', 15401, '', 0, 'DUR', 'Durango', 1),
(541, 'GTO', 15401, '', 0, 'GTO', 'Guanajuato', 1),
(542, 'GRO', 15401, '', 0, 'GRO', 'Guerrero', 1),
(543, 'HGO', 15401, '', 0, 'HGO', 'Hidalgo', 1),
(544, 'JAL', 15401, '', 0, 'JAL', 'Jalisco', 1),
(545, 'MEX', 15401, '', 0, 'MEX', 'México', 1),
(546, 'MIC', 15401, '', 0, 'MIC', 'Michoacán de Ocampo', 1),
(547, 'MOR', 15401, '', 0, 'MOR', 'Morelos', 1),
(548, 'NAY', 15401, '', 0, 'NAY', 'Nayarit', 1),
(549, 'NLE', 15401, '', 0, 'NLE', 'Nuevo León', 1),
(550, 'OAX', 15401, '', 0, 'OAX', 'Oaxaca', 1),
(551, 'PUE', 15401, '', 0, 'PUE', 'Puebla', 1),
(552, 'QRO', 15401, '', 0, 'QRO', 'Querétaro', 1),
(553, 'ROO', 15401, '', 0, 'ROO', 'Quintana Roo', 1),
(554, 'SLP', 15401, '', 0, 'SLP', 'San Luis Potosí', 1),
(555, 'SIN', 15401, '', 0, 'SIN', 'Sinaloa', 1),
(556, 'SON', 15401, '', 0, 'SON', 'Sonora', 1),
(557, 'TAB', 15401, '', 0, 'TAB', 'Tabasco', 1),
(558, 'TAM', 15401, '', 0, 'TAM', 'Tamaulipas', 1),
(559, 'TLX', 15401, '', 0, 'TLX', 'Tlaxcala', 1),
(560, 'VER', 15401, '', 0, 'VER', 'Veracruz', 1),
(561, 'YUC', 15401, '', 0, 'YUC', 'Yucatán', 1),
(562, 'ZAC', 15401, '', 0, 'ZAC', 'Zacatecas', 1),
(563, 'ANT', 7001, '', 0, 'ANT', 'Antioquia', 1),
(564, 'BOL', 7001, '', 0, 'BOL', 'Bolívar', 1),
(565, 'BOY', 7001, '', 0, 'BOY', 'Boyacá', 1),
(566, 'CAL', 7001, '', 0, 'CAL', 'Caldas', 1),
(567, 'CAU', 7001, '', 0, 'CAU', 'Cauca', 1),
(568, 'CUN', 7001, '', 0, 'CUN', 'Cundinamarca', 1),
(569, 'HUI', 7001, '', 0, 'HUI', 'Huila', 1),
(570, 'LAG', 7001, '', 0, 'LAG', 'La Guajira', 1),
(571, 'MET', 7001, '', 0, 'MET', 'Meta', 1),
(572, 'NAR', 7001, '', 0, 'NAR', 'Nariño', 1),
(573, 'NDS', 7001, '', 0, 'NDS', 'Norte de Santander', 1),
(574, 'SAN', 7001, '', 0, 'SAN', 'Santander', 1),
(575, 'SUC', 7001, '', 0, 'SUC', 'Sucre', 1),
(576, 'TOL', 7001, '', 0, 'TOL', 'Tolima', 1),
(577, 'VAC', 7001, '', 0, 'VAC', 'Valle del Cauca', 1),
(578, 'RIS', 7001, '', 0, 'RIS', 'Risalda', 1),
(579, 'ATL', 7001, '', 0, 'ATL', 'Atlántico', 1),
(580, 'COR', 7001, '', 0, 'COR', 'Córdoba', 1),
(581, 'SAP', 7001, '', 0, 'SAP', 'San Andrés, Providencia y Santa Catalina', 1),
(582, 'ARA', 7001, '', 0, 'ARA', 'Arauca', 1),
(583, 'CAS', 7001, '', 0, 'CAS', 'Casanare', 1),
(584, 'AMA', 7001, '', 0, 'AMA', 'Amazonas', 1),
(585, 'CAQ', 7001, '', 0, 'CAQ', 'Caquetá', 1),
(586, 'CHO', 7001, '', 0, 'CHO', 'Chocó', 1),
(587, 'GUA', 7001, '', 0, 'GUA', 'Guainía', 1),
(588, 'GUV', 7001, '', 0, 'GUV', 'Guaviare', 1),
(589, 'PUT', 7001, '', 0, 'PUT', 'Putumayo', 1),
(590, 'QUI', 7001, '', 0, 'QUI', 'Quindío', 1),
(591, 'VAU', 7001, '', 0, 'VAU', 'Vaupés', 1),
(592, 'BOG', 7001, '', 0, 'BOG', 'Bogotá', 1),
(593, 'VID', 7001, '', 0, 'VID', 'Vichada', 1),
(594, 'CES', 7001, '', 0, 'CES', 'Cesar', 1),
(595, 'MAG', 7001, '', 0, 'MAG', 'Magdalena', 1),
(596, 'AT', 11401, '', 0, 'AT', 'Atlántida', 1),
(597, 'CH', 11401, '', 0, 'CH', 'Choluteca', 1),
(598, 'CL', 11401, '', 0, 'CL', 'Colón', 1),
(599, 'CM', 11401, '', 0, 'CM', 'Comayagua', 1),
(600, 'CO', 11401, '', 0, 'CO', 'Copán', 1),
(601, 'CR', 11401, '', 0, 'CR', 'Cortés', 1),
(602, 'EP', 11401, '', 0, 'EP', 'El Paraíso', 1),
(603, 'FM', 11401, '', 0, 'FM', 'Francisco Morazán', 1),
(604, 'GD', 11401, '', 0, 'GD', 'Gracias a Dios', 1),
(605, 'IN', 11401, '', 0, 'IN', 'Intibucá', 1),
(606, 'IB', 11401, '', 0, 'IB', 'Islas de la Bahía', 1),
(607, 'LP', 11401, '', 0, 'LP', 'La Paz', 1),
(608, 'LM', 11401, '', 0, 'LM', 'Lempira', 1),
(609, 'OC', 11401, '', 0, 'OC', 'Ocotepeque', 1),
(610, 'OL', 11401, '', 0, 'OL', 'Olancho', 1),
(611, 'SB', 11401, '', 0, 'SB', 'Santa Bárbara', 1),
(612, 'VL', 11401, '', 0, 'VL', 'Valle', 1),
(613, 'YO', 11401, '', 0, 'YO', 'Yoro', 1),
(614, 'DC', 11401, '', 0, 'DC', 'Distrito Central', 1),
(615, 'CC', 4601, 'Oistins', 0, 'CC', 'Christ Church', 1),
(616, 'SA', 4601, 'Greenland', 0, 'SA', 'Saint Andrew', 1),
(617, 'SG', 4601, 'Bulkeley', 0, 'SG', 'Saint George', 1),
(618, 'JA', 4601, 'Holetown', 0, 'JA', 'Saint James', 1),
(619, 'SJ', 4601, 'Four Roads', 0, 'SJ', 'Saint John', 1),
(620, 'SB', 4601, 'Bathsheba', 0, 'SB', 'Saint Joseph', 1),
(621, 'SL', 4601, 'Crab Hill', 0, 'SL', 'Saint Lucy', 1),
(622, 'SM', 4601, 'Bridgetown', 0, 'SM', 'Saint Michael', 1),
(623, 'SP', 4601, 'Speightstown', 0, 'SP', 'Saint Peter', 1),
(624, 'SC', 4601, 'Crane', 0, 'SC', 'Saint Philip', 1),
(625, 'ST', 4601, 'Hillaby', 0, 'ST', 'Saint Thomas', 1),
(626, 'VE-L', 23201, '', 0, 'VE-L', 'Mérida', 1),
(627, 'VE-T', 23201, '', 0, 'VE-T', 'Trujillo', 1),
(628, 'VE-E', 23201, '', 0, 'VE-E', 'Barinas', 1),
(629, 'VE-M', 23202, '', 0, 'VE-M', 'Miranda', 1),
(630, 'VE-W', 23202, '', 0, 'VE-W', 'Vargas', 1),
(631, 'VE-A', 23202, '', 0, 'VE-A', 'Distrito Capital', 1),
(632, 'VE-D', 23203, '', 0, 'VE-D', 'Aragua', 1),
(633, 'VE-G', 23203, '', 0, 'VE-G', 'Carabobo', 1),
(634, 'VE-I', 23204, '', 0, 'VE-I', 'Falcón', 1),
(635, 'VE-K', 23204, '', 0, 'VE-K', 'Lara', 1),
(636, 'VE-U', 23204, '', 0, 'VE-U', 'Yaracuy', 1),
(637, 'VE-F', 23205, '', 0, 'VE-F', 'Bolívar', 1),
(638, 'VE-X', 23205, '', 0, 'VE-X', 'Amazonas', 1),
(639, 'VE-Y', 23205, '', 0, 'VE-Y', 'Delta Amacuro', 1),
(640, 'VE-O', 23206, '', 0, 'VE-O', 'Nueva Esparta', 1),
(641, 'VE-Z', 23206, '', 0, 'VE-Z', 'Dependencias Federales', 1),
(642, 'VE-C', 23207, '', 0, 'VE-C', 'Apure', 1),
(643, 'VE-J', 23207, '', 0, 'VE-J', 'Guárico', 1),
(644, 'VE-H', 23207, '', 0, 'VE-H', 'Cojedes', 1),
(645, 'VE-P', 23207, '', 0, 'VE-P', 'Portuguesa', 1),
(646, 'VE-B', 23208, '', 0, 'VE-B', 'Anzoátegui', 1),
(647, 'VE-N', 23208, '', 0, 'VE-N', 'Monagas', 1),
(648, 'VE-R', 23208, '', 0, 'VE-R', 'Sucre', 1),
(649, 'VE-V', 23209, '', 0, 'VE-V', 'Zulia', 1),
(650, 'VE-S', 23209, '', 0, 'VE-S', 'Táchira', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_ecotaxe`
--

CREATE TABLE IF NOT EXISTS `llx_c_ecotaxe` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_ecotaxe` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Volcado de datos para la tabla `llx_c_ecotaxe`
--

INSERT INTO `llx_c_ecotaxe` (`rowid`, `code`, `libelle`, `price`, `organization`, `fk_pays`, `active`) VALUES
(1, 'ER-A-A', 'Materiels electriques < 0,2kg', 0.01000000, 'ERP', 1, 1),
(2, 'ER-A-B', 'Materiels electriques >= 0,2 kg et < 0,5 kg', 0.03000000, 'ERP', 1, 1),
(3, 'ER-A-C', 'Materiels electriques >= 0,5 kg et < 1 kg', 0.04000000, 'ERP', 1, 1),
(4, 'ER-A-D', 'Materiels electriques >= 1 kg et < 2 kg', 0.13000000, 'ERP', 1, 1),
(5, 'ER-A-E', 'Materiels electriques >= 2 kg et < 4kg', 0.21000000, 'ERP', 1, 1),
(6, 'ER-A-F', 'Materiels electriques >= 4 kg et < 8 kg', 0.42000000, 'ERP', 1, 1),
(7, 'ER-A-G', 'Materiels electriques >= 8 kg et < 15 kg', 0.84000000, 'ERP', 1, 1),
(8, 'ER-A-H', 'Materiels electriques >= 15 kg et < 20 kg', 1.25000000, 'ERP', 1, 1),
(9, 'ER-A-I', 'Materiels electriques >= 20 kg et < 30 kg', 1.88000000, 'ERP', 1, 1),
(10, 'ER-A-J', 'Materiels electriques >= 30 kg', 3.34000000, 'ERP', 1, 1),
(11, 'ER-M-1', 'TV, Moniteurs < 9kg', 0.84000000, 'ERP', 1, 1),
(12, 'ER-M-2', 'TV, Moniteurs >= 9kg et < 15kg', 1.67000000, 'ERP', 1, 1),
(13, 'ER-M-3', 'TV, Moniteurs >= 15kg et < 30kg', 3.34000000, 'ERP', 1, 1),
(14, 'ER-M-4', 'TV, Moniteurs >= 30 kg', 6.69000000, 'ERP', 1, 1),
(15, 'EC-A-A', 'Materiels electriques  0,2 kg max', 0.00840000, 'Ecologic', 1, 1),
(16, 'EC-A-B', 'Materiels electriques 0,21 kg min - 0,50 kg max', 0.02500000, 'Ecologic', 1, 1),
(17, 'EC-A-C', 'Materiels electriques  0,51 kg min - 1 kg max', 0.04000000, 'Ecologic', 1, 1),
(18, 'EC-A-D', 'Materiels electriques  1,01 kg min - 2,5 kg max', 0.13000000, 'Ecologic', 1, 1),
(19, 'EC-A-E', 'Materiels electriques  2,51 kg min - 4 kg max', 0.21000000, 'Ecologic', 1, 1),
(20, 'EC-A-F', 'Materiels electriques 4,01 kg min - 8 kg max', 0.42000000, 'Ecologic', 1, 1),
(21, 'EC-A-G', 'Materiels electriques  8,01 kg min - 12 kg max', 0.63000000, 'Ecologic', 1, 1),
(22, 'EC-A-H', 'Materiels electriques 12,01 kg min - 20 kg max', 1.05000000, 'Ecologic', 1, 1),
(23, 'EC-A-I', 'Materiels electriques  20,01 kg min', 1.88000000, 'Ecologic', 1, 1),
(24, 'EC-M-1', 'TV, Moniteurs 9 kg max', 0.84000000, 'Ecologic', 1, 1),
(25, 'EC-M-2', 'TV, Moniteurs 9,01 kg min - 18 kg max', 1.67000000, 'Ecologic', 1, 1),
(26, 'EC-M-3', 'TV, Moniteurs 18,01 kg min - 36 kg max', 3.34000000, 'Ecologic', 1, 1),
(27, 'EC-M-4', 'TV, Moniteurs 36,01 kg min', 6.69000000, 'Ecologic', 1, 1),
(28, 'ES-M-1', 'TV, Moniteurs <= 20 pouces', 0.84000000, 'Eco-systemes', 1, 1),
(29, 'ES-M-2', 'TV, Moniteurs > 20 pouces et <= 32 pouces', 3.34000000, 'Eco-systemes', 1, 1),
(30, 'ES-M-3', 'TV, Moniteurs > 32 pouces et autres grands ecrans', 6.69000000, 'Eco-systemes', 1, 1),
(31, 'ES-A-A', 'Ordinateur fixe, Audio home systems (HIFI), elements hifi separes', 0.84000000, 'Eco-systemes', 1, 1),
(32, 'ES-A-B', 'Ordinateur portable, CD-RCR, VCR, lecteurs et enregistreurs DVD, instruments de musique et caisses de resonance, haut parleurs...', 0.25000000, 'Eco-systemes', 1, 1),
(33, 'ES-A-C', 'Imprimante, photocopieur, telecopieur', 0.42000000, 'Eco-systemes', 1, 1),
(34, 'ES-A-D', 'Accessoires, clavier, souris, PDA, imprimante photo, appareil photo, gps, telephone, repondeur, telephone sans fil, modem, telecommande, casque, camescope, baladeur mp3, radio portable, radio K7 et CD portable, radio reveil', 0.08400000, 'Eco-systemes', 1, 1),
(35, 'ES-A-E', 'GSM', 0.00840000, 'Eco-systemes', 1, 1),
(36, 'ES-A-F', 'Jouets et equipements de loisirs et de sports < 0,5 kg', 0.04200000, 'Eco-systemes', 1, 1),
(37, 'ES-A-G', 'Jouets et equipements de loisirs et de sports > 0,5 kg', 0.17000000, 'Eco-systemes', 1, 1),
(38, 'ES-A-H', 'Jouets et equipements de loisirs et de sports > 10 kg', 1.25000000, 'Eco-systemes', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_effectif`
--

CREATE TABLE IF NOT EXISTS `llx_c_effectif` (
  `id` int(11) NOT NULL,
  `code` varchar(12) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_effectif` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_effectif`
--

INSERT INTO `llx_c_effectif` (`id`, `code`, `libelle`, `active`, `module`) VALUES
(0, 'EF0', '-', 1, NULL),
(1, 'EF1-5', '1 - 5', 1, NULL),
(2, 'EF6-10', '6 - 10', 1, NULL),
(3, 'EF11-50', '11 - 50', 1, NULL),
(4, 'EF51-100', '51 - 100', 1, NULL),
(5, 'EF100-500', '100 - 500', 1, NULL),
(6, 'EF500-', '> 500', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_field_list`
--

CREATE TABLE IF NOT EXISTS `llx_c_field_list` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `element` varchar(64) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `name` varchar(32) NOT NULL,
  `alias` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  `align` varchar(6) DEFAULT 'left',
  `sort` tinyint(4) NOT NULL DEFAULT '1',
  `search` tinyint(4) NOT NULL DEFAULT '0',
  `enabled` varchar(255) DEFAULT '1',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_forme_juridique`
--

CREATE TABLE IF NOT EXISTS `llx_c_forme_juridique` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `isvatexempted` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_forme_juridique` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

--
-- Volcado de datos para la tabla `llx_c_forme_juridique`
--

INSERT INTO `llx_c_forme_juridique` (`rowid`, `code`, `fk_pays`, `libelle`, `isvatexempted`, `active`, `module`) VALUES
(1, 0, 0, '-', 0, 1, NULL),
(2, 2301, 23, 'Monotributista', 0, 1, NULL),
(3, 2302, 23, 'Sociedad Civil', 0, 1, NULL),
(4, 2303, 23, 'Sociedades Comerciales', 0, 1, NULL),
(5, 2304, 23, 'Sociedades de Hecho', 0, 1, NULL),
(6, 2305, 23, 'Sociedades Irregulares', 0, 1, NULL),
(7, 2306, 23, 'Sociedad Colectiva', 0, 1, NULL),
(8, 2307, 23, 'Sociedad en Comandita Simple', 0, 1, NULL),
(9, 2308, 23, 'Sociedad de Capital e Industria', 0, 1, NULL),
(10, 2309, 23, 'Sociedad Accidental o en participación', 0, 1, NULL),
(11, 2310, 23, 'Sociedad de Responsabilidad Limitada', 0, 1, NULL),
(12, 2311, 23, 'Sociedad Anónima', 0, 1, NULL),
(13, 2312, 23, 'Sociedad Anónima con Participación Estatal Mayoritaria', 0, 1, NULL),
(14, 2313, 23, 'Sociedad en Comandita por Acciones (arts. 315 a 324, LSC)', 0, 1, NULL),
(15, 11, 1, 'Artisan Commerçant (EI)', 0, 1, NULL),
(16, 12, 1, 'Commerçant (EI)', 0, 1, NULL),
(17, 13, 1, 'Artisan (EI)', 0, 1, NULL),
(18, 14, 1, 'Officier public ou ministériel', 0, 1, NULL),
(19, 15, 1, 'Profession libérale (EI)', 0, 1, NULL),
(20, 16, 1, 'Exploitant agricole', 0, 1, NULL),
(21, 17, 1, 'Agent commercial', 0, 1, NULL),
(22, 18, 1, 'Associé Gérant de société', 0, 1, NULL),
(23, 19, 1, 'Personne physique', 0, 1, NULL),
(24, 21, 1, 'Indivision', 0, 1, NULL),
(25, 22, 1, 'Société créée de fait', 0, 1, NULL),
(26, 23, 1, 'Société en participation', 0, 1, NULL),
(27, 27, 1, 'Paroisse hors zone concordataire', 0, 1, NULL),
(28, 29, 1, 'Groupement de droit privé non doté de la personnalité morale', 0, 1, NULL),
(29, 31, 1, 'Personne morale de droit étranger, immatriculée au RCS', 0, 1, NULL),
(30, 32, 1, 'Personne morale de droit étranger, non immatriculée au RCS', 0, 1, NULL),
(31, 35, 1, 'Régime auto-entrepreneur', 0, 1, NULL),
(32, 41, 1, 'Établissement public ou régie à caractère industriel ou commercial', 0, 1, NULL),
(33, 51, 1, 'Société coopérative commerciale particulière', 0, 1, NULL),
(34, 52, 1, 'Société en nom collectif', 0, 1, NULL),
(35, 53, 1, 'Société en commandite', 0, 1, NULL),
(36, 54, 1, 'Société à responsabilité limitée (SARL)', 0, 1, NULL),
(37, 55, 1, 'Société anonyme à conseil d administration', 0, 1, NULL),
(38, 56, 1, 'Société anonyme à directoire', 0, 1, NULL),
(39, 57, 1, 'Société par actions simplifiée', 0, 1, NULL),
(40, 58, 1, 'Entreprise Unipersonnelle à Responsabilité Limitée (EURL)', 0, 1, NULL),
(41, 61, 1, 'Caisse d''épargne et de prévoyance', 0, 1, NULL),
(42, 62, 1, 'Groupement d''intérêt économique (GIE)', 0, 1, NULL),
(43, 63, 1, 'Société coopérative agricole', 0, 1, NULL),
(44, 64, 1, 'Société non commerciale d assurances', 0, 1, NULL),
(45, 65, 1, 'Société civile', 0, 1, NULL),
(46, 69, 1, 'Personnes de droit privé inscrites au RCS', 0, 1, NULL),
(47, 71, 1, 'Administration de l état', 0, 1, NULL),
(48, 72, 1, 'Collectivité territoriale', 0, 1, NULL),
(49, 73, 1, 'Établissement public administratif', 0, 1, NULL),
(50, 74, 1, 'Personne morale de droit public administratif', 0, 1, NULL),
(51, 81, 1, 'Organisme gérant régime de protection social à adhésion obligatoire', 0, 1, NULL),
(52, 82, 1, 'Organisme mutualiste', 0, 1, NULL),
(53, 83, 1, 'Comité d entreprise', 0, 1, NULL),
(54, 84, 1, 'Organisme professionnel', 0, 1, NULL),
(55, 85, 1, 'Organisme de retraite à adhésion non obligatoire', 0, 1, NULL),
(56, 91, 1, 'Syndicat de propriétaires', 0, 1, NULL),
(57, 92, 1, 'Association loi 1901 ou assimilé', 0, 1, NULL),
(58, 93, 1, 'Fondation', 0, 1, NULL),
(59, 99, 1, 'Personne morale de droit privé', 0, 1, NULL),
(60, 200, 2, 'Indépendant', 0, 1, NULL),
(61, 201, 2, 'SPRL - Société à responsabilité limitée', 0, 1, NULL),
(62, 202, 2, 'SA   - Société Anonyme', 0, 1, NULL),
(63, 203, 2, 'SCRL - Société coopérative à responsabilité limitée', 0, 1, NULL),
(64, 204, 2, 'ASBL - Association sans but Lucratif', 0, 1, NULL),
(65, 205, 2, 'SCRI - Société coopérative à responsabilité illimitée', 0, 1, NULL),
(66, 206, 2, 'SCS  - Société en commandite simple', 0, 1, NULL),
(67, 207, 2, 'SCA  - Société en commandite par action', 0, 1, NULL),
(68, 208, 2, 'SNC  - Société en nom collectif', 0, 1, NULL),
(69, 209, 2, 'GIE  - Groupement d intérêt économique', 0, 1, NULL),
(70, 210, 2, 'GEIE - Groupement européen d intérêt économique', 0, 1, NULL),
(71, 500, 5, 'GmbH - Gesellschaft mit beschränkter Haftung', 0, 1, NULL),
(72, 501, 5, 'AG - Aktiengesellschaft ', 0, 1, NULL),
(73, 502, 5, 'GmbH&Co. KG - Gesellschaft mit beschränkter Haftung & Compagnie Kommanditgesellschaft', 0, 1, NULL),
(74, 503, 5, 'Gewerbe - Personengesellschaft', 0, 1, NULL),
(75, 504, 5, 'UG - Unternehmergesellschaft -haftungsbeschränkt-', 0, 1, NULL),
(76, 505, 5, 'GbR - Gesellschaft des bürgerlichen Rechts', 0, 1, NULL),
(77, 506, 5, 'KG - Kommanditgesellschaft', 0, 1, NULL),
(78, 507, 5, 'Ltd. - Limited Company', 0, 1, NULL),
(79, 508, 5, 'OHG - Offene Handelsgesellschaft', 0, 1, NULL),
(80, 301, 3, 'Società semplice', 0, 1, NULL),
(81, 302, 3, 'Società in nome collettivo s.n.c.', 0, 1, NULL),
(82, 303, 3, 'Società in accomandita semplice s.a.s.', 0, 1, NULL),
(83, 304, 3, 'Società per azioni s.p.a.', 0, 1, NULL),
(84, 305, 3, 'Società a responsabilità limitata s.r.l.', 0, 1, NULL),
(85, 306, 3, 'Società in accomandita per azioni s.a.p.a.', 0, 1, NULL),
(86, 307, 3, 'Società cooperativa', 0, 1, NULL),
(87, 308, 3, 'Società consortile', 0, 1, NULL),
(88, 309, 3, 'Società europea', 0, 1, NULL),
(89, 310, 3, 'Società cooperativa europea', 0, 1, NULL),
(90, 311, 3, 'Società unipersonale', 0, 1, NULL),
(91, 312, 3, 'Società di professionisti', 0, 1, NULL),
(92, 313, 3, 'Società di fatto', 0, 1, NULL),
(93, 314, 3, 'Società occulta', 0, 1, NULL),
(94, 315, 3, 'Società apparente', 0, 1, NULL),
(95, 316, 3, 'Impresa individuale ', 0, 1, NULL),
(96, 317, 3, 'Impresa coniugale', 0, 1, NULL),
(97, 318, 3, 'Impresa familiare', 0, 1, NULL),
(98, 600, 6, 'Raison Individuelle', 0, 1, NULL),
(99, 601, 6, 'Société Simple', 0, 1, NULL),
(100, 602, 6, 'Société en nom collectif', 0, 1, NULL),
(101, 603, 6, 'Société en commandite', 0, 1, NULL),
(102, 604, 6, 'Société anonyme (SA)', 0, 1, NULL),
(103, 605, 6, 'Société en commandite par actions', 0, 1, NULL),
(104, 606, 6, 'Société à responsabilité limitée (SARL)', 0, 1, NULL),
(105, 607, 6, 'Société coopérative', 0, 1, NULL),
(106, 608, 6, 'Association', 0, 1, NULL),
(107, 609, 6, 'Fondation', 0, 1, NULL),
(108, 700, 7, 'Sole Trader', 0, 1, NULL),
(109, 701, 7, 'Partnership', 0, 1, NULL),
(110, 702, 7, 'Private Limited Company by shares (LTD)', 0, 1, NULL),
(111, 703, 7, 'Public Limited Company', 0, 1, NULL),
(112, 704, 7, 'Workers Cooperative', 0, 1, NULL),
(113, 705, 7, 'Limited Liability Partnership', 0, 1, NULL),
(114, 706, 7, 'Franchise', 0, 1, NULL),
(115, 1000, 10, 'Société à responsabilité limitée (SARL)', 0, 1, NULL),
(116, 1001, 10, 'Société en Nom Collectif (SNC)', 0, 1, NULL),
(117, 1002, 10, 'Société en Commandite Simple (SCS)', 0, 1, NULL),
(118, 1003, 10, 'société en participation', 0, 1, NULL),
(119, 1004, 10, 'Société Anonyme (SA)', 0, 1, NULL),
(120, 1005, 10, 'Société Unipersonnelle à Responsabilité Limitée (SUARL)', 0, 1, NULL),
(121, 1006, 10, 'Groupement d''intérêt économique (GEI)', 0, 1, NULL),
(122, 1007, 10, 'Groupe de sociétés', 0, 1, NULL),
(123, 401, 4, 'Empresario Individual', 0, 1, NULL),
(124, 402, 4, 'Comunidad de Bienes', 0, 1, NULL),
(125, 403, 4, 'Sociedad Civil', 0, 1, NULL),
(126, 404, 4, 'Sociedad Colectiva', 0, 1, NULL),
(127, 405, 4, 'Sociedad Limitada', 0, 1, NULL),
(128, 406, 4, 'Sociedad Anónima', 0, 1, NULL),
(129, 407, 4, 'Sociedad Comandataria por Acciones', 0, 1, NULL),
(130, 408, 4, 'Sociedad Comandataria Simple', 0, 1, NULL),
(131, 409, 4, 'Sociedad Laboral', 0, 1, NULL),
(132, 410, 4, 'Sociedad Cooperativa', 0, 1, NULL),
(133, 411, 4, 'Sociedad de Garantía Recíproca', 0, 1, NULL),
(134, 412, 4, 'Entidad de Capital-Riesgo', 0, 1, NULL),
(135, 413, 4, 'Agrupación de Interés Económico', 0, 1, NULL),
(136, 414, 4, 'Sociedad de Inversión Mobiliaria', 0, 1, NULL),
(137, 415, 4, 'Agrupación sin Ánimo de Lucro', 0, 1, NULL),
(138, 15201, 152, 'Mauritius Private Company Limited By Shares', 0, 1, NULL),
(139, 15202, 152, 'Mauritius Company Limited By Guarantee', 0, 1, NULL),
(140, 15203, 152, 'Mauritius Public Company Limited By Shares', 0, 1, NULL),
(141, 15204, 152, 'Mauritius Foreign Company', 0, 1, NULL),
(142, 15205, 152, 'Mauritius GBC1 (Offshore Company)', 0, 1, NULL),
(143, 15206, 152, 'Mauritius GBC2 (International Company)', 0, 1, NULL),
(144, 15207, 152, 'Mauritius General Partnership', 0, 1, NULL),
(145, 15208, 152, 'Mauritius Limited Partnership', 0, 1, NULL),
(146, 15209, 152, 'Mauritius Sole Proprietorship', 0, 1, NULL),
(147, 15210, 152, 'Mauritius Trusts', 0, 1, NULL),
(148, 15401, 154, 'Sociedad en nombre colectivo', 0, 1, NULL),
(149, 15402, 154, 'Sociedad en comandita simple', 0, 1, NULL),
(150, 15403, 154, 'Sociedad de responsabilidad limitada', 0, 1, NULL),
(151, 15404, 154, 'Sociedad anónima', 0, 1, NULL),
(152, 15405, 154, 'Sociedad en comandita por acciones', 0, 1, NULL),
(153, 15406, 154, 'Sociedad cooperativa', 0, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_input_method`
--

CREATE TABLE IF NOT EXISTS `llx_c_input_method` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) DEFAULT NULL,
  `libelle` varchar(60) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_input_method` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `llx_c_input_method`
--

INSERT INTO `llx_c_input_method` (`rowid`, `code`, `libelle`, `active`, `module`) VALUES
(1, 'OrderByMail', 'Courrier', 1, NULL),
(2, 'OrderByFax', 'Fax', 1, NULL),
(3, 'OrderByEMail', 'EMail', 1, NULL),
(4, 'OrderByPhone', 'Téléphone', 1, NULL),
(5, 'OrderByWWW', 'En ligne', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_input_reason`
--

CREATE TABLE IF NOT EXISTS `llx_c_input_reason` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) DEFAULT NULL,
  `label` varchar(60) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_input_reason` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `llx_c_input_reason`
--

INSERT INTO `llx_c_input_reason` (`rowid`, `code`, `label`, `active`, `module`) VALUES
(1, 'SRC_INTE', 'Web site', 1, NULL),
(2, 'SRC_CAMP_MAIL', 'Mailing campaign', 1, NULL),
(3, 'SRC_CAMP_PHO', 'Phone campaign', 1, NULL),
(4, 'SRC_CAMP_FAX', 'Fax campaign', 1, NULL),
(5, 'SRC_COMM', 'Commercial contact', 1, NULL),
(6, 'SRC_SHOP', 'Shop contact', 1, NULL),
(7, 'SRC_CAMP_EMAIL', 'EMailing campaign', 1, NULL),
(8, 'SRC_WOM', 'Word of mouth', 1, NULL),
(9, 'SRC_PARTNER', 'Partner', 1, NULL),
(10, 'SRC_EMPLOYEE', 'Employee', 1, NULL),
(11, 'SRC_SPONSORING', 'Sponsoring', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_paiement`
--

CREATE TABLE IF NOT EXISTS `llx_c_paiement` (
  `id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_paiement` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_paiement`
--

INSERT INTO `llx_c_paiement` (`id`, `code`, `libelle`, `type`, `active`, `module`) VALUES
(0, '', '-', 3, 1, NULL),
(1, 'TIP', 'TIP', 2, 1, NULL),
(2, 'VIR', 'Virement', 2, 1, NULL),
(3, 'PRE', 'Prélèvement', 2, 0, NULL),
(4, 'LIQ', 'Espèces', 2, 1, NULL),
(6, 'CB', 'Carte Bancaire', 2, 1, NULL),
(7, 'CHQ', 'Chèque', 2, 1, NULL),
(50, 'VAD', 'Paiement en ligne', 2, 0, NULL),
(51, 'TRA', 'Traite', 2, 0, NULL),
(52, 'LCR', 'LCR', 2, 0, NULL),
(53, 'FAC', 'Factor', 2, 0, NULL),
(54, 'PRO', 'Proforma', 2, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_paper_format`
--

CREATE TABLE IF NOT EXISTS `llx_c_paper_format` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `label` varchar(50) NOT NULL,
  `width` float(6,2) DEFAULT '0.00',
  `height` float(6,2) DEFAULT '0.00',
  `unit` varchar(5) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=226 ;

--
-- Volcado de datos para la tabla `llx_c_paper_format`
--

INSERT INTO `llx_c_paper_format` (`rowid`, `code`, `label`, `width`, `height`, `unit`, `active`, `module`) VALUES
(1, 'EU4A0', 'Format 4A0', 1682.00, 2378.00, 'mm', 1, NULL),
(2, 'EU2A0', 'Format 2A0', 1189.00, 1682.00, 'mm', 1, NULL),
(3, 'EUA0', 'Format A0', 840.00, 1189.00, 'mm', 1, NULL),
(4, 'EUA1', 'Format A1', 594.00, 840.00, 'mm', 1, NULL),
(5, 'EUA2', 'Format A2', 420.00, 594.00, 'mm', 1, NULL),
(6, 'EUA3', 'Format A3', 297.00, 420.00, 'mm', 1, NULL),
(7, 'EUA4', 'Format A4', 210.00, 297.00, 'mm', 1, NULL),
(8, 'EUA5', 'Format A5', 148.00, 210.00, 'mm', 1, NULL),
(9, 'EUA6', 'Format A6', 105.00, 148.00, 'mm', 1, NULL),
(100, 'USLetter', 'Format Letter (A)', 216.00, 279.00, 'mm', 1, NULL),
(105, 'USLegal', 'Format Legal', 216.00, 356.00, 'mm', 1, NULL),
(110, 'USExecutive', 'Format Executive', 190.00, 254.00, 'mm', 1, NULL),
(115, 'USLedger', 'Format Ledger/Tabloid (B)', 279.00, 432.00, 'mm', 1, NULL),
(200, 'CAP1', 'Format Canadian P1', 560.00, 860.00, 'mm', 1, NULL),
(205, 'CAP2', 'Format Canadian P2', 430.00, 560.00, 'mm', 1, NULL),
(210, 'CAP3', 'Format Canadian P3', 280.00, 430.00, 'mm', 1, NULL),
(215, 'CAP4', 'Format Canadian P4', 215.00, 280.00, 'mm', 1, NULL),
(220, 'CAP5', 'Format Canadian P5', 140.00, 215.00, 'mm', 1, NULL),
(225, 'CAP6', 'Format Canadian P6', 107.00, 140.00, 'mm', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_payment_term`
--

CREATE TABLE IF NOT EXISTS `llx_c_payment_term` (
  `rowid` int(11) NOT NULL,
  `code` varchar(16) DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `libelle` varchar(255) DEFAULT NULL,
  `libelle_facture` text,
  `fdm` tinyint(4) DEFAULT NULL,
  `nbjour` smallint(6) DEFAULT NULL,
  `decalage` smallint(6) DEFAULT NULL,
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_payment_term`
--

INSERT INTO `llx_c_payment_term` (`rowid`, `code`, `sortorder`, `active`, `libelle`, `libelle_facture`, `fdm`, `nbjour`, `decalage`, `module`) VALUES
(1, 'RECEP', 1, 1, 'A réception', 'Réception de facture', 0, 0, NULL, NULL),
(2, '30D', 2, 1, '30 jours', 'Réglement à 30 jours', 0, 30, NULL, NULL),
(3, '30DENDMONTH', 3, 1, '30 jours fin de mois', 'Réglement à 30 jours fin de mois', 1, 30, NULL, NULL),
(4, '60D', 4, 1, '60 jours', 'Réglement à 60 jours', 0, 60, NULL, NULL),
(5, '60DENDMONTH', 5, 1, '60 jours fin de mois', 'Réglement à 60 jours fin de mois', 1, 60, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_pays`
--

CREATE TABLE IF NOT EXISTS `llx_c_pays` (
  `rowid` int(11) NOT NULL,
  `code` varchar(2) NOT NULL,
  `code_iso` varchar(3) DEFAULT NULL,
  `libelle` varchar(50) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_c_pays_code` (`code`),
  UNIQUE KEY `idx_c_pays_libelle` (`libelle`),
  UNIQUE KEY `idx_c_pays_code_iso` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_pays`
--

INSERT INTO `llx_c_pays` (`rowid`, `code`, `code_iso`, `libelle`, `active`) VALUES
(0, '', NULL, '-', 1),
(1, 'FR', NULL, 'France', 1),
(2, 'BE', NULL, 'Belgium', 1),
(3, 'IT', NULL, 'Italy', 1),
(4, 'ES', NULL, 'Spain', 1),
(5, 'DE', NULL, 'Germany', 1),
(6, 'CH', NULL, 'Suisse', 1),
(7, 'GB', NULL, 'United Kingdom', 1),
(8, 'IE', NULL, 'Irland', 1),
(9, 'CN', NULL, 'China', 1),
(10, 'TN', NULL, 'Tunisie', 1),
(11, 'US', NULL, 'United States', 1),
(12, 'MA', NULL, 'Maroc', 1),
(13, 'DZ', NULL, 'Algérie', 1),
(14, 'CA', NULL, 'Canada', 1),
(15, 'TG', NULL, 'Togo', 1),
(16, 'GA', NULL, 'Gabon', 1),
(17, 'NL', NULL, 'Nerderland', 1),
(18, 'HU', NULL, 'Hongrie', 1),
(19, 'RU', NULL, 'Russia', 1),
(20, 'SE', NULL, 'Sweden', 1),
(21, 'CI', NULL, 'Côte d''Ivoire', 1),
(22, 'SN', NULL, 'Sénégal', 1),
(23, 'AR', NULL, 'Argentine', 1),
(24, 'CM', NULL, 'Cameroun', 1),
(25, 'PT', NULL, 'Portugal', 1),
(26, 'SA', NULL, 'Arabie Saoudite', 1),
(27, 'MC', NULL, 'Monaco', 1),
(28, 'AU', NULL, 'Australia', 1),
(29, 'SG', NULL, 'Singapour', 1),
(30, 'AF', NULL, 'Afghanistan', 1),
(31, 'AX', NULL, 'Iles Aland', 1),
(32, 'AL', NULL, 'Albanie', 1),
(33, 'AS', NULL, 'Samoa américaines', 1),
(34, 'AD', NULL, 'Andorre', 1),
(35, 'AO', NULL, 'Angola', 1),
(36, 'AI', NULL, 'Anguilla', 1),
(37, 'AQ', NULL, 'Antarctique', 1),
(38, 'AG', NULL, 'Antigua-et-Barbuda', 1),
(39, 'AM', NULL, 'Arménie', 1),
(40, 'AW', NULL, 'Aruba', 1),
(41, 'AT', NULL, 'Autriche', 1),
(42, 'AZ', NULL, 'Azerbaïdjan', 1),
(43, 'BS', NULL, 'Bahamas', 1),
(44, 'BH', NULL, 'Bahreïn', 1),
(45, 'BD', NULL, 'Bangladesh', 1),
(46, 'BB', NULL, 'Barbade', 1),
(47, 'BY', NULL, 'Biélorussie', 1),
(48, 'BZ', NULL, 'Belize', 1),
(49, 'BJ', NULL, 'Bénin', 1),
(50, 'BM', NULL, 'Bermudes', 1),
(51, 'BT', NULL, 'Bhoutan', 1),
(52, 'BO', NULL, 'Bolivia', 1),
(53, 'BA', NULL, 'Bosnie-Herzégovine', 1),
(54, 'BW', NULL, 'Botswana', 1),
(55, 'BV', NULL, 'Ile Bouvet', 1),
(56, 'BR', NULL, 'Brazil', 1),
(57, 'IO', NULL, 'Territoire britannique de l''Océan Indien', 1),
(58, 'BN', NULL, 'Brunei', 1),
(59, 'BG', NULL, 'Bulgarie', 1),
(60, 'BF', NULL, 'Burkina Faso', 1),
(61, 'BI', NULL, 'Burundi', 1),
(62, 'KH', NULL, 'Cambodge', 1),
(63, 'CV', NULL, 'Cap-Vert', 1),
(64, 'KY', NULL, 'Iles Cayman', 1),
(65, 'CF', NULL, 'République centrafricaine', 1),
(66, 'TD', NULL, 'Tchad', 1),
(67, 'CL', NULL, 'Chili', 1),
(68, 'CX', NULL, 'Ile Christmas', 1),
(69, 'CC', NULL, 'Iles des Cocos (Keeling)', 1),
(70, 'CO', NULL, 'Colombie', 1),
(71, 'KM', NULL, 'Comores', 1),
(72, 'CG', NULL, 'Congo', 1),
(73, 'CD', NULL, 'République démocratique du Congo', 1),
(74, 'CK', NULL, 'Iles Cook', 1),
(75, 'CR', NULL, 'Costa Rica', 1),
(76, 'HR', NULL, 'Croatie', 1),
(77, 'CU', NULL, 'Cuba', 1),
(78, 'CY', NULL, 'Chypre', 1),
(79, 'CZ', NULL, 'République Tchèque', 1),
(80, 'DK', NULL, 'Danemark', 1),
(81, 'DJ', NULL, 'Djibouti', 1),
(82, 'DM', NULL, 'Dominique', 1),
(83, 'DO', NULL, 'République Dominicaine', 1),
(84, 'EC', NULL, 'Equateur', 1),
(85, 'EG', NULL, 'Egypte', 1),
(86, 'SV', NULL, 'Salvador', 1),
(87, 'GQ', NULL, 'Guinée Equatoriale', 1),
(88, 'ER', NULL, 'Erythrée', 1),
(89, 'EE', NULL, 'Estonie', 1),
(90, 'ET', NULL, 'Ethiopie', 1),
(91, 'FK', NULL, 'Iles Falkland', 1),
(92, 'FO', NULL, 'Iles Féroé', 1),
(93, 'FJ', NULL, 'Iles Fidji', 1),
(94, 'FI', NULL, 'Finlande', 1),
(95, 'GF', NULL, 'Guyane française', 1),
(96, 'PF', NULL, 'Polynésie française', 1),
(97, 'TF', NULL, 'Terres australes françaises', 1),
(98, 'GM', NULL, 'Gambie', 1),
(99, 'GE', NULL, 'Géorgie', 1),
(100, 'GH', NULL, 'Ghana', 1),
(101, 'GI', NULL, 'Gibraltar', 1),
(102, 'GR', NULL, 'Grèce', 1),
(103, 'GL', NULL, 'Groenland', 1),
(104, 'GD', NULL, 'Grenade', 1),
(106, 'GU', NULL, 'Guam', 1),
(107, 'GT', NULL, 'Guatemala', 1),
(108, 'GN', NULL, 'Guinée', 1),
(109, 'GW', NULL, 'Guinée-Bissao', 1),
(111, 'HT', NULL, 'Haiti', 1),
(112, 'HM', NULL, 'Iles Heard et McDonald', 1),
(113, 'VA', NULL, 'Saint-Siège (Vatican)', 1),
(114, 'HN', NULL, 'Honduras', 1),
(115, 'HK', NULL, 'Hong Kong', 1),
(116, 'IS', NULL, 'Islande', 1),
(117, 'IN', NULL, 'India', 1),
(118, 'ID', NULL, 'Indonésie', 1),
(119, 'IR', NULL, 'Iran', 1),
(120, 'IQ', NULL, 'Iraq', 1),
(121, 'IL', NULL, 'Israel', 1),
(122, 'JM', NULL, 'Jamaïque', 1),
(123, 'JP', NULL, 'Japon', 1),
(124, 'JO', NULL, 'Jordanie', 1),
(125, 'KZ', NULL, 'Kazakhstan', 1),
(126, 'KE', NULL, 'Kenya', 1),
(127, 'KI', NULL, 'Kiribati', 1),
(128, 'KP', NULL, 'Corée du Nord', 1),
(129, 'KR', NULL, 'Corée du Sud', 1),
(130, 'KW', NULL, 'Koweït', 1),
(131, 'KG', NULL, 'Kirghizistan', 1),
(132, 'LA', NULL, 'Laos', 1),
(133, 'LV', NULL, 'Lettonie', 1),
(134, 'LB', NULL, 'Liban', 1),
(135, 'LS', NULL, 'Lesotho', 1),
(136, 'LR', NULL, 'Liberia', 1),
(137, 'LY', NULL, 'Libye', 1),
(138, 'LI', NULL, 'Liechtenstein', 1),
(139, 'LT', NULL, 'Lituanie', 1),
(140, 'LU', NULL, 'Luxembourg', 1),
(141, 'MO', NULL, 'Macao', 1),
(142, 'MK', NULL, 'ex-République yougoslave de Macédoine', 1),
(143, 'MG', NULL, 'Madagascar', 1),
(144, 'MW', NULL, 'Malawi', 1),
(145, 'MY', NULL, 'Malaisie', 1),
(146, 'MV', NULL, 'Maldives', 1),
(147, 'ML', NULL, 'Mali', 1),
(148, 'MT', NULL, 'Malte', 1),
(149, 'MH', NULL, 'Iles Marshall', 1),
(151, 'MR', NULL, 'Mauritanie', 1),
(152, 'MU', NULL, 'Maurice', 1),
(153, 'YT', NULL, 'Mayotte', 1),
(154, 'MX', NULL, 'Mexique', 1),
(155, 'FM', NULL, 'Micronésie', 1),
(156, 'MD', NULL, 'Moldavie', 1),
(157, 'MN', NULL, 'Mongolie', 1),
(158, 'MS', NULL, 'Monserrat', 1),
(159, 'MZ', NULL, 'Mozambique', 1),
(160, 'MM', NULL, 'Birmanie (Myanmar)', 1),
(161, 'NA', NULL, 'Namibie', 1),
(162, 'NR', NULL, 'Nauru', 1),
(163, 'NP', NULL, 'Népal', 1),
(164, 'AN', NULL, 'Antilles néerlandaises', 1),
(165, 'NC', NULL, 'Nouvelle-Calédonie', 1),
(166, 'NZ', NULL, 'Nouvelle-Zélande', 1),
(167, 'NI', NULL, 'Nicaragua', 1),
(168, 'NE', NULL, 'Niger', 1),
(169, 'NG', NULL, 'Nigeria', 1),
(170, 'NU', NULL, 'Nioué', 1),
(171, 'NF', NULL, 'Ile Norfolk', 1),
(172, 'MP', NULL, 'Mariannes du Nord', 1),
(173, 'NO', NULL, 'Norvège', 1),
(174, 'OM', NULL, 'Oman', 1),
(175, 'PK', NULL, 'Pakistan', 1),
(176, 'PW', NULL, 'Palaos', 1),
(177, 'PS', NULL, 'Territoire Palestinien Occupé', 1),
(178, 'PA', NULL, 'Panama', 1),
(179, 'PG', NULL, 'Papouasie-Nouvelle-Guinée', 1),
(180, 'PY', NULL, 'Paraguay', 1),
(181, 'PE', NULL, 'Pérou', 1),
(182, 'PH', NULL, 'Philippines', 1),
(183, 'PN', NULL, 'Iles Pitcairn', 1),
(184, 'PL', NULL, 'Pologne', 1),
(185, 'PR', NULL, 'Porto Rico', 1),
(186, 'QA', NULL, 'Qatar', 1),
(188, 'RO', NULL, 'Roumanie', 1),
(189, 'RW', NULL, 'Rwanda', 1),
(190, 'SH', NULL, 'Sainte-Hélène', 1),
(191, 'KN', NULL, 'Saint-Christophe-et-Niévès', 1),
(192, 'LC', NULL, 'Sainte-Lucie', 1),
(193, 'PM', NULL, 'Saint-Pierre-et-Miquelon', 1),
(194, 'VC', NULL, 'Saint-Vincent-et-les-Grenadines', 1),
(195, 'WS', NULL, 'Samoa', 1),
(196, 'SM', NULL, 'Saint-Marin', 1),
(197, 'ST', NULL, 'Sao Tomé-et-Principe', 1),
(198, 'RS', NULL, 'Serbie', 1),
(199, 'SC', NULL, 'Seychelles', 1),
(200, 'SL', NULL, 'Sierra Leone', 1),
(201, 'SK', NULL, 'Slovaquie', 1),
(202, 'SI', NULL, 'Slovénie', 1),
(203, 'SB', NULL, 'Iles Salomon', 1),
(204, 'SO', NULL, 'Somalie', 1),
(205, 'ZA', NULL, 'Afrique du Sud', 1),
(206, 'GS', NULL, 'Iles Géorgie du Sud et Sandwich du Sud', 1),
(207, 'LK', NULL, 'Sri Lanka', 1),
(208, 'SD', NULL, 'Soudan', 1),
(209, 'SR', NULL, 'Suriname', 1),
(210, 'SJ', NULL, 'Iles Svalbard et Jan Mayen', 1),
(211, 'SZ', NULL, 'Swaziland', 1),
(212, 'SY', NULL, 'Syrie', 1),
(213, 'TW', NULL, 'Taïwan', 1),
(214, 'TJ', NULL, 'Tadjikistan', 1),
(215, 'TZ', NULL, 'Tanzanie', 1),
(216, 'TH', NULL, 'Thaïlande', 1),
(217, 'TL', NULL, 'Timor Oriental', 1),
(218, 'TK', NULL, 'Tokélaou', 1),
(219, 'TO', NULL, 'Tonga', 1),
(220, 'TT', NULL, 'Trinité-et-Tobago', 1),
(221, 'TR', NULL, 'Turquie', 1),
(222, 'TM', NULL, 'Turkménistan', 1),
(223, 'TC', NULL, 'Iles Turks-et-Caicos', 1),
(224, 'TV', NULL, 'Tuvalu', 1),
(225, 'UG', NULL, 'Ouganda', 1),
(226, 'UA', NULL, 'Ukraine', 1),
(227, 'AE', NULL, 'Émirats arabes unis', 1),
(228, 'UM', NULL, 'Iles mineures éloignées des États-Unis', 1),
(229, 'UY', NULL, 'Uruguay', 1),
(230, 'UZ', NULL, 'Ouzbékistan', 1),
(231, 'VU', NULL, 'Vanuatu', 1),
(232, 'VE', NULL, 'Vénézuela', 1),
(233, 'VN', NULL, 'Viêt Nam', 1),
(234, 'VG', NULL, 'Iles Vierges britanniques', 1),
(235, 'VI', NULL, 'Iles Vierges américaines', 1),
(236, 'WF', NULL, 'Wallis-et-Futuna', 1),
(237, 'EH', NULL, 'Sahara occidental', 1),
(238, 'YE', NULL, 'Yémen', 1),
(239, 'ZM', NULL, 'Zambie', 1),
(240, 'ZW', NULL, 'Zimbabwe', 1),
(241, 'GG', NULL, 'Guernesey', 1),
(242, 'IM', NULL, 'Ile de Man', 1),
(243, 'JE', NULL, 'Jersey', 1),
(244, 'ME', NULL, 'Monténégro', 1),
(245, 'BL', NULL, 'Saint-Barthélemy', 1),
(246, 'MF', NULL, 'Saint-Martin', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_propalst`
--

CREATE TABLE IF NOT EXISTS `llx_c_propalst` (
  `id` smallint(6) NOT NULL,
  `code` varchar(12) NOT NULL,
  `label` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_propalst` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_propalst`
--

INSERT INTO `llx_c_propalst` (`id`, `code`, `label`, `active`) VALUES
(0, 'PR_DRAFT', 'Brouillon', 1),
(1, 'PR_OPEN', 'Ouverte', 1),
(2, 'PR_SIGNED', 'Signée', 1),
(3, 'PR_NOTSIGNED', 'Non Signée', 1),
(4, 'PR_FAC', 'Facturée', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_prospectlevel`
--

CREATE TABLE IF NOT EXISTS `llx_c_prospectlevel` (
  `code` varchar(12) NOT NULL,
  `label` varchar(30) DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `active` smallint(6) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_prospectlevel`
--

INSERT INTO `llx_c_prospectlevel` (`code`, `label`, `sortorder`, `active`, `module`) VALUES
('PL_HIGH', 'High', 4, 1, NULL),
('PL_LOW', 'Low', 2, 1, NULL),
('PL_MEDIUM', 'Medium', 3, 1, NULL),
('PL_NONE', 'None', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_regions`
--

CREATE TABLE IF NOT EXISTS `llx_c_regions` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code_region` int(11) NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `cheflieu` varchar(50) DEFAULT NULL,
  `tncc` int(11) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `code_region` (`code_region`),
  KEY `idx_c_regions_fk_pays` (`fk_pays`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23210 ;

--
-- Volcado de datos para la tabla `llx_c_regions`
--

INSERT INTO `llx_c_regions` (`rowid`, `code_region`, `fk_pays`, `cheflieu`, `tncc`, `nom`, `active`) VALUES
(1, 0, 0, '0', 0, '-', 1),
(101, 1, 1, '97105', 3, 'Guadeloupe', 1),
(102, 2, 1, '97209', 3, 'Martinique', 1),
(103, 3, 1, '97302', 3, 'Guyane', 1),
(104, 4, 1, '97411', 3, 'Réunion', 1),
(105, 11, 1, '75056', 1, 'Île-de-France', 1),
(106, 21, 1, '51108', 0, 'Champagne-Ardenne', 1),
(107, 22, 1, '80021', 0, 'Picardie', 1),
(108, 23, 1, '76540', 0, 'Haute-Normandie', 1),
(109, 24, 1, '45234', 2, 'Centre', 1),
(110, 25, 1, '14118', 0, 'Basse-Normandie', 1),
(111, 26, 1, '21231', 0, 'Bourgogne', 1),
(112, 31, 1, '59350', 2, 'Nord-Pas-de-Calais', 1),
(113, 41, 1, '57463', 0, 'Lorraine', 1),
(114, 42, 1, '67482', 1, 'Alsace', 1),
(115, 43, 1, '25056', 0, 'Franche-Comté', 1),
(116, 52, 1, '44109', 4, 'Pays de la Loire', 1),
(117, 53, 1, '35238', 0, 'Bretagne', 1),
(118, 54, 1, '86194', 2, 'Poitou-Charentes', 1),
(119, 72, 1, '33063', 1, 'Aquitaine', 1),
(120, 73, 1, '31555', 0, 'Midi-Pyrénées', 1),
(121, 74, 1, '87085', 2, 'Limousin', 1),
(122, 82, 1, '69123', 2, 'Rhône-Alpes', 1),
(123, 83, 1, '63113', 1, 'Auvergne', 1),
(124, 91, 1, '34172', 2, 'Languedoc-Roussillon', 1),
(125, 93, 1, '13055', 0, 'Provence-Alpes-Côte d''Azur', 1),
(126, 94, 1, '2A004', 0, 'Corse', 1),
(201, 201, 2, '', 1, 'Flandre', 1),
(202, 202, 2, '', 2, 'Wallonie', 1),
(203, 203, 2, '', 3, 'Bruxelles-Capitale', 1),
(301, 301, 3, NULL, 1, 'Abruzzo', 1),
(302, 302, 3, NULL, 1, 'Basilicata', 1),
(303, 303, 3, NULL, 1, 'Calabria', 1),
(304, 304, 3, NULL, 1, 'Campania', 1),
(305, 305, 3, NULL, 1, 'Emilia-Romagna', 1),
(306, 306, 3, NULL, 1, 'Friuli-Venezia Giulia', 1),
(307, 307, 3, NULL, 1, 'Lazio', 1),
(308, 308, 3, NULL, 1, 'Liguria', 1),
(309, 309, 3, NULL, 1, 'Lombardia', 1),
(310, 310, 3, NULL, 1, 'Marche', 1),
(311, 311, 3, NULL, 1, 'Molise', 1),
(312, 312, 3, NULL, 1, 'Piemonte', 1),
(313, 313, 3, NULL, 1, 'Puglia', 1),
(314, 314, 3, NULL, 1, 'Sardegna', 1),
(315, 315, 3, NULL, 1, 'Sicilia', 1),
(316, 316, 3, NULL, 1, 'Toscana', 1),
(317, 317, 3, NULL, 1, 'Trentino-Alto Adige', 1),
(318, 318, 3, NULL, 1, 'Umbria', 1),
(319, 319, 3, NULL, 1, 'Valle d Aosta', 1),
(320, 320, 3, NULL, 1, 'Veneto', 1),
(401, 401, 4, '', 0, 'Andalucia', 1),
(402, 402, 4, '', 0, 'Aragón', 1),
(403, 403, 4, '', 0, 'Castilla y León', 1),
(404, 404, 4, '', 0, 'Castilla la Mancha', 1),
(405, 405, 4, '', 0, 'Canarias', 1),
(406, 406, 4, '', 0, 'Cataluña', 1),
(407, 407, 4, '', 0, 'Comunidad de Ceuta', 1),
(408, 408, 4, '', 0, 'Comunidad Foral de Navarra', 1),
(409, 409, 4, '', 0, 'Comunidad de Melilla', 1),
(410, 410, 4, '', 0, 'Cantabria', 1),
(411, 411, 4, '', 0, 'Comunidad Valenciana', 1),
(412, 412, 4, '', 0, 'Extemadura', 1),
(413, 413, 4, '', 0, 'Galicia', 1),
(414, 414, 4, '', 0, 'Islas Baleares', 1),
(415, 415, 4, '', 0, 'La Rioja', 1),
(416, 416, 4, '', 0, 'Comunidad de Madrid', 1),
(417, 417, 4, '', 0, 'Región de Murcia', 1),
(418, 418, 4, '', 0, 'Principado de Asturias', 1),
(419, 419, 4, '', 0, 'Pais Vasco', 1),
(420, 420, 4, '', 0, 'Otros', 1),
(501, 501, 5, '', 0, 'Deutschland', 1),
(601, 601, 6, '', 1, 'Cantons', 1),
(1001, 1001, 10, '', 0, 'Ariana', 1),
(1002, 1002, 10, '', 0, 'Béja', 1),
(1003, 1003, 10, '', 0, 'Ben Arous', 1),
(1004, 1004, 10, '', 0, 'Bizerte', 1),
(1005, 1005, 10, '', 0, 'Gabès', 1),
(1006, 1006, 10, '', 0, 'Gafsa', 1),
(1007, 1007, 10, '', 0, 'Jendouba', 1),
(1008, 1008, 10, '', 0, 'Kairouan', 1),
(1009, 1009, 10, '', 0, 'Kasserine', 1),
(1010, 1010, 10, '', 0, 'Kébili', 1),
(1011, 1011, 10, '', 0, 'La Manouba', 1),
(1012, 1012, 10, '', 0, 'Le Kef', 1),
(1013, 1013, 10, '', 0, 'Mahdia', 1),
(1014, 1014, 10, '', 0, 'Médenine', 1),
(1015, 1015, 10, '', 0, 'Monastir', 1),
(1016, 1016, 10, '', 0, 'Nabeul', 1),
(1017, 1017, 10, '', 0, 'Sfax', 1),
(1018, 1018, 10, '', 0, 'Sidi Bouzid', 1),
(1019, 1019, 10, '', 0, 'Siliana', 1),
(1020, 1020, 10, '', 0, 'Sousse', 1),
(1021, 1021, 10, '', 0, 'Tataouine', 1),
(1022, 1022, 10, '', 0, 'Tozeur', 1),
(1023, 1023, 10, '', 0, 'Tunis', 1),
(1024, 1024, 10, '', 0, 'Zaghouan', 1),
(1101, 1101, 11, '', 0, 'United-States', 1),
(2301, 2301, 23, '', 0, 'Norte', 1),
(2302, 2302, 23, '', 0, 'Litoral', 1),
(2303, 2303, 23, '', 0, 'Cuyana', 1),
(2304, 2304, 23, '', 0, 'Central', 1),
(2305, 2305, 23, '', 0, 'Patagonia', 1),
(2801, 2801, 28, '', 0, 'Australia', 1),
(4601, 4601, 46, '', 0, 'Barbados', 1),
(5601, 5601, 56, '', 0, 'Brasil', 1),
(6701, 6701, 67, NULL, NULL, 'Tarapacá', 1),
(6702, 6702, 67, NULL, NULL, 'Antofagasta', 1),
(6703, 6703, 67, NULL, NULL, 'Atacama', 1),
(6704, 6704, 67, NULL, NULL, 'Coquimbo', 1),
(6705, 6705, 67, NULL, NULL, 'Valparaíso', 1),
(6706, 6706, 67, NULL, NULL, 'General Bernardo O Higgins', 1),
(6707, 6707, 67, NULL, NULL, 'Maule', 1),
(6708, 6708, 67, NULL, NULL, 'Biobío', 1),
(6709, 6709, 67, NULL, NULL, 'Raucanía', 1),
(6710, 6710, 67, NULL, NULL, 'Los Lagos', 1),
(6711, 6711, 67, NULL, NULL, 'Aysén General Carlos Ibáñez del Campo', 1),
(6712, 6712, 67, NULL, NULL, 'Magallanes y Antártica Chilena', 1),
(6713, 6713, 67, NULL, NULL, 'Metropolitana de Santiago', 1),
(6714, 6714, 67, NULL, NULL, 'Los Ríos', 1),
(6715, 6715, 67, NULL, NULL, 'Arica y Parinacota', 1),
(7001, 7001, 70, '', 0, 'Colombie', 1),
(8601, 8601, 86, NULL, NULL, 'Central', 1),
(8602, 8602, 86, NULL, NULL, 'Oriental', 1),
(8603, 8603, 86, NULL, NULL, 'Occidental', 1),
(11401, 11401, 114, '', 0, 'Honduras', 1),
(11701, 11701, 117, '', 0, 'India', 1),
(15201, 15201, 152, '', 0, 'Rivière Noire', 1),
(15202, 15202, 152, '', 0, 'Flacq', 1),
(15203, 15203, 152, '', 0, 'Grand Port', 1),
(15204, 15204, 152, '', 0, 'Moka', 1),
(15205, 15205, 152, '', 0, 'Pamplemousses', 1),
(15206, 15206, 152, '', 0, 'Plaines Wilhems', 1),
(15207, 15207, 152, '', 0, 'Port-Louis', 1),
(15208, 15208, 152, '', 0, 'Rivière du Rempart', 1),
(15209, 15209, 152, '', 0, 'Savanne', 1),
(15210, 15210, 152, '', 0, 'Rodrigues', 1),
(15211, 15211, 152, '', 0, 'Les îles Agaléga', 1),
(15212, 15212, 152, '', 0, 'Les écueils des Cargados Carajos', 1),
(15401, 15401, 154, '', 0, 'Mexique', 1),
(23201, 23201, 232, '', 0, 'Los Andes', 1),
(23202, 23202, 232, '', 0, 'Capital', 1),
(23203, 23203, 232, '', 0, 'Central', 1),
(23204, 23204, 232, '', 0, 'Cento Occidental', 1),
(23205, 23205, 232, '', 0, 'Guayana', 1),
(23206, 23206, 232, '', 0, 'Insular', 1),
(23207, 23207, 232, '', 0, 'Los Llanos', 1),
(23208, 23208, 232, '', 0, 'Nor-Oriental', 1),
(23209, 23209, 232, '', 0, 'Zuliana', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_shipment_mode`
--

CREATE TABLE IF NOT EXISTS `llx_c_shipment_mode` (
  `rowid` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `description` text,
  `active` tinyint(4) DEFAULT '0',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_shipment_mode`
--

INSERT INTO `llx_c_shipment_mode` (`rowid`, `tms`, `code`, `libelle`, `description`, `active`, `module`) VALUES
(1, '2013-02-19 13:21:21', 'CATCH', 'Catch', 'Catch by client', 1, NULL),
(2, '2013-02-19 13:21:21', 'TRANS', 'Transporter', 'Generic transporter', 1, NULL),
(3, '2013-02-19 13:21:21', 'COLSUI', 'Colissimo Suivi', 'Colissimo Suivi', 0, NULL),
(4, '2013-02-19 13:21:21', 'LETTREMAX', 'Lettre Max', 'Courrier Suivi et Lettre Max', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_stcomm`
--

CREATE TABLE IF NOT EXISTS `llx_c_stcomm` (
  `id` int(11) NOT NULL,
  `code` varchar(12) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_stcomm` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_stcomm`
--

INSERT INTO `llx_c_stcomm` (`id`, `code`, `libelle`, `active`) VALUES
(-1, 'ST_NO', 'Ne pas contacter', 1),
(0, 'ST_NEVER', 'Jamais contacté', 1),
(1, 'ST_TODO', 'A contacter', 1),
(2, 'ST_PEND', 'Contact en cours', 1),
(3, 'ST_DONE', 'Contactée', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_tva`
--

CREATE TABLE IF NOT EXISTS `llx_c_tva` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_pays` int(11) NOT NULL,
  `taux` double NOT NULL,
  `localtax1` double NOT NULL DEFAULT '0',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2` double NOT NULL DEFAULT '0',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `note` varchar(128) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `accountancy_code_sell` varchar(15) DEFAULT NULL,
  `accountancy_code_buy` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2463 ;

--
-- Volcado de datos para la tabla `llx_c_tva`
--

INSERT INTO `llx_c_tva` (`rowid`, `fk_pays`, `taux`, `localtax1`, `localtax1_type`, `localtax2`, `localtax2_type`, `recuperableonly`, `note`, `active`, `accountancy_code_sell`, `accountancy_code_buy`) VALUES
(11, 1, 19.6, 0, '0', 0, '0', 0, 'VAT standard rate (France hors DOM-TOM)', 1, NULL, NULL),
(12, 1, 8.5, 0, '0', 0, '0', 0, 'VAT standard rate (DOM sauf Guyane et Saint-Martin)', 0, NULL, NULL),
(13, 1, 8.5, 0, '0', 0, '0', 1, 'VAT standard rate (DOM sauf Guyane et Saint-Martin), non perçu par le vendeur mais récupérable par acheteur', 0, NULL, NULL),
(14, 1, 5.5, 0, '0', 0, '0', 0, 'VAT reduced rate (France hors DOM-TOM)', 1, NULL, NULL),
(15, 1, 0, 0, '0', 0, '0', 0, 'VAT Rate 0 ou non applicable', 1, NULL, NULL),
(16, 1, 2.1, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(17, 1, 7, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(21, 2, 21, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(22, 2, 6, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(23, 2, 0, 0, '0', 0, '0', 0, 'VAT Rate 0 ou non applicable', 1, NULL, NULL),
(24, 2, 12, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(31, 3, 21, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(32, 3, 10, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(33, 3, 4, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(34, 3, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(41, 4, 21, 5.2, '3', -15, '1', 0, 'VAT standard rate', 1, NULL, NULL),
(42, 4, 10, 1.4, '3', -15, '1', 0, 'VAT reduced rate', 1, NULL, NULL),
(43, 4, 4, 0.5, '3', -15, '1', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(44, 4, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(51, 5, 19, 0, '0', 0, '0', 0, 'allgemeine Ust.', 1, NULL, NULL),
(52, 5, 7, 0, '0', 0, '0', 0, 'ermäßigte USt.', 1, NULL, NULL),
(53, 5, 0, 0, '0', 0, '0', 0, 'keine USt.', 1, NULL, NULL),
(54, 5, 5.5, 0, '0', 0, '0', 0, 'USt. Forst', 0, NULL, NULL),
(55, 5, 10.7, 0, '0', 0, '0', 0, 'USt. Landwirtschaft', 0, NULL, NULL),
(61, 6, 8, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(62, 6, 3.8, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(63, 6, 2.5, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(64, 6, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(71, 7, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(72, 7, 17.5, 0, '0', 0, '0', 0, 'VAT standard rate before 2011', 1, NULL, NULL),
(73, 7, 5, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(74, 7, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(91, 9, 17, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(92, 9, 13, 0, '0', 0, '0', 0, 'VAT reduced rate 0', 1, NULL, NULL),
(93, 9, 3, 0, '0', 0, '0', 0, 'VAT super reduced rate 0', 1, NULL, NULL),
(94, 9, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(101, 10, 6, 1, '4', 0.4, '7', 0, 'VAT 6%', 1, NULL, NULL),
(102, 10, 12, 1, '4', 0.4, '7', 0, 'VAT 12%', 1, NULL, NULL),
(103, 10, 18, 1, '4', 0.4, '7', 0, 'VAT 18%', 1, NULL, NULL),
(104, 10, 7.5, 1, '4', 0.4, '7', 0, 'VAT 6% Majoré à 25% (7.5%)', 1, NULL, NULL),
(105, 10, 15, 1, '4', 0.4, '7', 0, 'VAT 12% Majoré à 25% (15%)', 1, NULL, NULL),
(106, 10, 22.5, 1, '4', 0.4, '7', 0, 'VAT 18% Majoré à 25% (22.5%)', 1, NULL, NULL),
(107, 10, 0, 1, '4', 0.4, '7', 0, 'VAT Rate 0', 1, NULL, NULL),
(111, 11, 0, 0, '0', 0, '0', 0, 'No Sales Tax', 1, NULL, NULL),
(112, 11, 4, 0, '0', 0, '0', 0, 'Sales Tax 4%', 1, NULL, NULL),
(113, 11, 6, 0, '0', 0, '0', 0, 'Sales Tax 6%', 1, NULL, NULL),
(121, 12, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(122, 12, 14, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(123, 12, 10, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(124, 12, 7, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(125, 12, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(141, 14, 7, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(142, 14, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(171, 17, 19, 0, '0', 0, '0', 0, 'Algemeen BTW tarief', 1, NULL, NULL),
(172, 17, 6, 0, '0', 0, '0', 0, 'Verlaagd BTW tarief', 1, NULL, NULL),
(173, 17, 0, 0, '0', 0, '0', 0, '0 BTW tarief', 1, NULL, NULL),
(174, 17, 21, 0, '0', 0, '0', 0, 'Algemeen BTW tarief (vanaf 1 oktober 2012)', 0, NULL, NULL),
(201, 20, 25, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(202, 20, 12, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(203, 20, 6, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(204, 20, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(231, 23, 21, 0, '0', 0, '0', 0, 'IVA standard rate', 1, NULL, NULL),
(232, 23, 10.5, 0, '0', 0, '0', 0, 'IVA reduced rate', 1, NULL, NULL),
(233, 23, 0, 0, '0', 0, '0', 0, 'IVA Rate 0', 1, NULL, NULL),
(251, 25, 23, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(252, 25, 13, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(253, 25, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(254, 25, 6, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(271, 27, 19.6, 0, '0', 0, '0', 0, 'VAT standard rate (France hors DOM-TOM)', 1, NULL, NULL),
(272, 27, 8.5, 0, '0', 0, '0', 0, 'VAT standard rate (DOM sauf Guyane et Saint-Martin)', 0, NULL, NULL),
(273, 27, 8.5, 0, '0', 0, '0', 1, 'VAT standard rate (DOM sauf Guyane et Saint-Martin), non perçu par le vendeur mais récupérable par acheteur', 0, NULL, NULL),
(274, 27, 5.5, 0, '0', 0, '0', 0, 'VAT reduced rate (France hors DOM-TOM)', 0, NULL, NULL),
(275, 27, 0, 0, '0', 0, '0', 0, 'VAT Rate 0 ou non applicable', 1, NULL, NULL),
(276, 27, 2.1, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(277, 27, 7, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(281, 28, 10, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(282, 28, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(411, 41, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(412, 41, 10, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(413, 41, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(461, 46, 0, 0, '0', 0, '0', 0, 'No VAT', 1, NULL, NULL),
(462, 46, 15, 0, '0', 0, '0', 0, 'VAT 15%', 1, NULL, NULL),
(463, 46, 7.5, 0, '0', 0, '0', 0, 'VAT 7.5%', 1, NULL, NULL),
(561, 56, 0, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(591, 59, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(592, 59, 7, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(593, 59, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(671, 67, 19, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(672, 67, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(801, 80, 25, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(802, 80, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(861, 86, 13, 0, '0', 0, '0', 0, 'IVA 13', 1, NULL, NULL),
(862, 86, 0, 0, '0', 0, '0', 0, 'SIN IVA', 1, NULL, NULL),
(1141, 114, 0, 0, '0', 0, '0', 0, 'No ISV', 1, NULL, NULL),
(1142, 114, 12, 0, '0', 0, '0', 0, 'ISV 12%', 1, NULL, NULL),
(1161, 116, 25.5, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1162, 116, 7, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1163, 116, 0, 0, '0', 0, '0', 0, 'VAT rate 0', 1, NULL, NULL),
(1171, 117, 12.5, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1172, 117, 4, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1173, 117, 1, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(1174, 117, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1231, 123, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1232, 123, 5, 0, '0', 0, '0', 0, 'VAT Rate 5', 1, NULL, NULL),
(1401, 140, 15, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1402, 140, 12, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1403, 140, 6, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1404, 140, 3, 0, '0', 0, '0', 0, 'VAT super-reduced rate', 1, NULL, NULL),
(1405, 140, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1521, 152, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1522, 152, 15, 0, '0', 0, '0', 0, 'VAT Rate 15', 1, NULL, NULL),
(1541, 154, 0, 0, '0', 0, '0', 0, 'No VAT', 1, NULL, NULL),
(1542, 154, 16, 0, '0', 0, '0', 0, 'VAT 16%', 1, NULL, NULL),
(1543, 154, 10, 0, '0', 0, '0', 0, 'VAT Frontero', 1, NULL, NULL),
(1662, 166, 15, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1663, 166, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1731, 173, 25, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1732, 173, 14, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1733, 173, 8, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1734, 173, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1841, 184, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1842, 184, 7, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1843, 184, 3, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1844, 184, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1881, 188, 24, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(1882, 188, 9, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1883, 188, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(1884, 188, 5, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(1931, 193, 0, 0, '0', 0, '0', 0, 'No VAT in SPM', 1, NULL, NULL),
(2011, 201, 19, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(2012, 201, 10, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(2013, 201, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(2021, 202, 20, 0, '0', 0, '0', 0, 'VAT standard rate', 1, NULL, NULL),
(2022, 202, 8.5, 0, '0', 0, '0', 0, 'VAT reduced rate', 1, NULL, NULL),
(2023, 202, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(2261, 226, 20, 0, '0', 0, '0', 0, 'VAT standart rate', 1, NULL, NULL),
(2262, 226, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(2321, 232, 0, 0, '0', 0, '0', 0, 'No VAT', 1, NULL, NULL),
(2322, 232, 12, 0, '0', 0, '0', 0, 'VAT 12%', 1, NULL, NULL),
(2323, 232, 8, 0, '0', 0, '0', 0, 'VAT 8%', 1, NULL, NULL),
(2461, 246, 0, 0, '0', 0, '0', 0, 'VAT Rate 0', 1, NULL, NULL),
(2462, 52, 13, 0, '0', 0, '0', 0, 'Iva 13%', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_typent`
--

CREATE TABLE IF NOT EXISTS `llx_c_typent` (
  `id` int(11) NOT NULL,
  `code` varchar(12) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_typent` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_typent`
--

INSERT INTO `llx_c_typent` (`id`, `code`, `libelle`, `active`, `module`) VALUES
(0, 'TE_UNKNOWN', '-', 1, NULL),
(1, 'TE_STARTUP', 'Start-up', 0, NULL),
(2, 'TE_GROUP', 'Gran Contribuyente', 1, NULL),
(3, 'TE_MEDIUM', 'PME/PMI', 1, NULL),
(4, 'TE_SMALL', 'TPE', 0, NULL),
(5, 'TE_ADMIN', 'Administration', 1, NULL),
(6, 'TE_WHOLE', 'Grossiste', 0, NULL),
(7, 'TE_RETAIL', 'Revendeur', 0, NULL),
(8, 'TE_PRIVATE', 'Particulier', 1, NULL),
(100, 'TE_OTHER', 'Autres', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_type_contact`
--

CREATE TABLE IF NOT EXISTS `llx_c_type_contact` (
  `rowid` int(11) NOT NULL,
  `element` varchar(30) NOT NULL,
  `source` varchar(8) NOT NULL DEFAULT 'external',
  `code` varchar(16) NOT NULL,
  `libelle` varchar(64) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_c_type_contact_uk` (`element`,`source`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_c_type_contact`
--

INSERT INTO `llx_c_type_contact` (`rowid`, `element`, `source`, `code`, `libelle`, `active`, `module`) VALUES
(10, 'contrat', 'internal', 'SALESREPSIGN', 'Commercial signataire du contrat', 1, NULL),
(11, 'contrat', 'internal', 'SALESREPFOLL', 'Commercial suivi du contrat', 1, NULL),
(20, 'contrat', 'external', 'BILLING', 'Contact client facturation contrat', 1, NULL),
(21, 'contrat', 'external', 'CUSTOMER', 'Contact client suivi contrat', 1, NULL),
(22, 'contrat', 'external', 'SALESREPSIGN', 'Contact client signataire contrat', 1, NULL),
(31, 'propal', 'internal', 'SALESREPFOLL', 'Commercial à l''origine de la propale', 1, NULL),
(40, 'propal', 'external', 'BILLING', 'Contact client facturation propale', 1, NULL),
(41, 'propal', 'external', 'CUSTOMER', 'Contact client suivi propale', 1, NULL),
(50, 'facture', 'internal', 'SALESREPFOLL', 'Responsable suivi du paiement', 1, NULL),
(60, 'facture', 'external', 'BILLING', 'Contact client facturation', 1, NULL),
(61, 'facture', 'external', 'SHIPPING', 'Contact client livraison', 1, NULL),
(62, 'facture', 'external', 'SERVICE', 'Contact client prestation', 1, NULL),
(70, 'invoice_supplier', 'internal', 'SALESREPFOLL', 'Responsable suivi du paiement', 1, NULL),
(71, 'invoice_supplier', 'external', 'BILLING', 'Contact fournisseur facturation', 1, NULL),
(72, 'invoice_supplier', 'external', 'SHIPPING', 'Contact fournisseur livraison', 1, NULL),
(73, 'invoice_supplier', 'external', 'SERVICE', 'Contact fournisseur prestation', 1, NULL),
(80, 'agenda', 'internal', 'ACTOR', 'Responsable', 1, NULL),
(81, 'agenda', 'internal', 'GUEST', 'Guest', 1, NULL),
(85, 'agenda', 'external', 'ACTOR', 'Responsable', 1, NULL),
(86, 'agenda', 'external', 'GUEST', 'Guest', 1, NULL),
(91, 'commande', 'internal', 'SALESREPFOLL', 'Responsable suivi de la commande', 1, NULL),
(100, 'commande', 'external', 'BILLING', 'Contact client facturation commande', 1, NULL),
(101, 'commande', 'external', 'CUSTOMER', 'Contact client suivi commande', 1, NULL),
(102, 'commande', 'external', 'SHIPPING', 'Contact client livraison commande', 1, NULL),
(120, 'fichinter', 'internal', 'INTERREPFOLL', 'Responsable suivi de l''intervention', 1, NULL),
(121, 'fichinter', 'internal', 'INTERVENING', 'Intervenant', 1, NULL),
(130, 'fichinter', 'external', 'BILLING', 'Contact client facturation intervention', 1, NULL),
(131, 'fichinter', 'external', 'CUSTOMER', 'Contact client suivi de l''intervention', 1, NULL),
(140, 'order_supplier', 'internal', 'SALESREPFOLL', 'Responsable suivi de la commande', 1, NULL),
(141, 'order_supplier', 'internal', 'SHIPPING', 'Responsable réception de la commande', 1, NULL),
(142, 'order_supplier', 'external', 'BILLING', 'Contact fournisseur facturation commande', 1, NULL),
(143, 'order_supplier', 'external', 'CUSTOMER', 'Contact fournisseur suivi commande', 1, NULL),
(145, 'order_supplier', 'external', 'SHIPPING', 'Contact fournisseur livraison commande', 1, NULL),
(160, 'project', 'internal', 'PROJECTLEADER', 'Chef de Projet', 1, NULL),
(161, 'project', 'internal', 'CONTRIBUTOR', 'Intervenant', 1, NULL),
(170, 'project', 'external', 'PROJECTLEADER', 'Chef de Projet', 1, NULL),
(171, 'project', 'external', 'CONTRIBUTOR', 'Intervenant', 1, NULL),
(180, 'project_task', 'internal', 'TASKEXECUTIVE', 'Responsable', 1, NULL),
(181, 'project_task', 'internal', 'CONTRIBUTOR', 'Intervenant', 1, NULL),
(190, 'project_task', 'external', 'TASKEXECUTIVE', 'Responsable', 1, NULL),
(191, 'project_task', 'external', 'CONTRIBUTOR', 'Intervenant', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_type_fees`
--

CREATE TABLE IF NOT EXISTS `llx_c_type_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(12) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_c_type_fees` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_c_type_fees`
--

INSERT INTO `llx_c_type_fees` (`id`, `code`, `libelle`, `active`, `module`) VALUES
(1, 'TF_OTHER', 'Other', 1, NULL),
(2, 'TF_TRIP', 'Trip', 1, NULL),
(3, 'TF_LUNCH', 'Lunch', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_c_ziptown`
--

CREATE TABLE IF NOT EXISTS `llx_c_ziptown` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `fk_county` int(11) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL DEFAULT '0',
  `zip` varchar(10) NOT NULL,
  `town` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_ziptown_fk_pays` (`zip`,`town`,`fk_pays`),
  KEY `idx_c_ziptown_fk_county` (`fk_county`),
  KEY `idx_c_ziptown_fk_pays` (`fk_pays`),
  KEY `idx_c_ziptown_zip` (`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_deplacement`
--

CREATE TABLE IF NOT EXISTS `llx_deplacement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dated` datetime DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `type` varchar(12) NOT NULL,
  `fk_statut` int(11) NOT NULL DEFAULT '1',
  `km` double DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `note` text,
  `note_public` text,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_document_generator`
--

CREATE TABLE IF NOT EXISTS `llx_document_generator` (
  `rowid` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `classfile` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_document_model`
--

CREATE TABLE IF NOT EXISTS `llx_document_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_document_model` (`nom`,`type`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Volcado de datos para la tabla `llx_document_model`
--

INSERT INTO `llx_document_model` (`rowid`, `nom`, `entity`, `type`, `libelle`, `description`) VALUES
(17, 'merou', 1, 'shipping', NULL, NULL),
(18, 'sirocco', 1, 'delivery', 'sirocco', NULL),
(21, 'azur', 1, 'propal', NULL, NULL),
(25, 'baleine', 1, 'project', NULL, NULL),
(31, 'muscadet', 1, 'order_supplier', NULL, NULL),
(32, 'crabe', 1, 'invoice', NULL, NULL),
(33, 'soleil', 1, 'ficheinter', NULL, NULL),
(34, 'rouget', 1, 'shipping', NULL, NULL),
(35, 'typhon', 1, 'delivery', NULL, NULL),
(36, 'einstein', 1, 'order', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_domain`
--

CREATE TABLE IF NOT EXISTS `llx_domain` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_don`
--

CREATE TABLE IF NOT EXISTS `llx_don` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `datec` datetime DEFAULT NULL,
  `datedon` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_paiement` int(11) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `societe` varchar(50) DEFAULT NULL,
  `adresse` text,
  `cp` varchar(30) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(24) DEFAULT NULL,
  `phone_mobile` varchar(24) DEFAULT NULL,
  `public` smallint(6) NOT NULL DEFAULT '1',
  `fk_don_projet` int(11) DEFAULT NULL,
  `fk_user_author` int(11) NOT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_ecm_directories`
--

CREATE TABLE IF NOT EXISTS `llx_ecm_directories` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(64) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_parent` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cachenbofdoc` int(11) NOT NULL DEFAULT '0',
  `fullpath` varchar(255) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  `date_c` datetime DEFAULT NULL,
  `date_m` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_c` int(11) DEFAULT NULL,
  `fk_user_m` int(11) DEFAULT NULL,
  `acl` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_ecm_directories` (`label`,`fk_parent`,`entity`),
  KEY `idx_ecm_directories_fk_user_c` (`fk_user_c`),
  KEY `idx_ecm_directories_fk_user_m` (`fk_user_m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_ecm_documents`
--

CREATE TABLE IF NOT EXISTS `llx_ecm_documents` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(16) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `filemime` varchar(32) NOT NULL,
  `fullpath_dol` varchar(255) NOT NULL,
  `fullpath_orig` varchar(255) NOT NULL,
  `description` text,
  `manualkeyword` text,
  `fk_create` int(11) NOT NULL,
  `fk_update` int(11) DEFAULT NULL,
  `date_c` datetime NOT NULL,
  `date_u` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_directory` int(11) DEFAULT NULL,
  `fk_status` smallint(6) DEFAULT '0',
  `private` smallint(6) DEFAULT '0',
  `crc` varchar(32) NOT NULL DEFAULT '',
  `cryptkey` varchar(50) NOT NULL DEFAULT '',
  `cipher` varchar(50) NOT NULL DEFAULT 'twofish',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_ecm_documents` (`fullpath_dol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_element_contact`
--

CREATE TABLE IF NOT EXISTS `llx_element_contact` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datecreate` datetime DEFAULT NULL,
  `statut` smallint(6) DEFAULT '5',
  `element_id` int(11) NOT NULL,
  `fk_c_type_contact` int(11) NOT NULL,
  `fk_socpeople` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_element_contact_idx1` (`element_id`,`fk_c_type_contact`,`fk_socpeople`),
  KEY `fk_element_contact_fk_c_type_contact` (`fk_c_type_contact`),
  KEY `idx_element_contact_fk_socpeople` (`fk_socpeople`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `llx_element_contact`
--

INSERT INTO `llx_element_contact` (`rowid`, `datecreate`, `statut`, `element_id`, `fk_c_type_contact`, `fk_socpeople`) VALUES
(1, '2013-03-09 10:44:17', 4, 3, 101, 1),
(2, '2013-03-09 11:22:53', 4, 4, 101, 1),
(3, '2013-03-16 09:56:36', 4, 6, 101, 2),
(4, '2013-03-16 11:42:45', 4, 7, 101, 1),
(5, '2013-03-23 09:48:27', 4, 1, 41, 1),
(6, '2013-04-06 10:02:42', 4, 9, 101, 1),
(7, '2013-05-14 14:51:44', 4, 14, 101, 1),
(8, '2013-05-14 15:10:25', 4, 15, 101, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_element_element`
--

CREATE TABLE IF NOT EXISTS `llx_element_element` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_source` int(11) NOT NULL,
  `sourcetype` varchar(32) NOT NULL,
  `fk_target` int(11) NOT NULL,
  `targettype` varchar(32) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_element_element_idx1` (`fk_source`,`sourcetype`,`fk_target`,`targettype`),
  KEY `idx_element_element_fk_target` (`fk_target`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_element_element`
--

INSERT INTO `llx_element_element` (`rowid`, `fk_source`, `sourcetype`, `fk_target`, `targettype`) VALUES
(1, 1, 'commande', 13, 'facture'),
(10, 1, 'order_supplier', 10, 'invoice_supplier'),
(6, 1, 'shipping', 1, 'delivery'),
(2, 2, 'order_supplier', 1, 'invoice_supplier'),
(8, 2, 'shipping', 2, 'delivery'),
(3, 4, 'order_supplier', 3, 'invoice_supplier'),
(4, 4, 'order_supplier', 4, 'invoice_supplier'),
(5, 10, 'commande', 1, 'shipping'),
(7, 11, 'commande', 2, 'shipping'),
(9, 15, 'commande', 3, 'shipping');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_element_lock`
--

CREATE TABLE IF NOT EXISTS `llx_element_lock` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_element` int(11) NOT NULL,
  `elementtype` varchar(32) NOT NULL,
  `datel` datetime DEFAULT NULL,
  `datem` datetime DEFAULT NULL,
  `sessionid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_element_tag`
--

CREATE TABLE IF NOT EXISTS `llx_element_tag` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `lang` varchar(5) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `fk_element` int(11) NOT NULL,
  `element` varchar(64) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_element_tag` (`entity`,`lang`,`tag`,`fk_element`,`element`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_entrepot`
--

CREATE TABLE IF NOT EXISTS `llx_entrepot` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(255) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `description` text,
  `lieu` varchar(64) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `statut` tinyint(4) DEFAULT '1',
  `valo_pmp` float(12,4) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_entrepot_label` (`label`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Volcado de datos para la tabla `llx_entrepot`
--

INSERT INTO `llx_entrepot` (`rowid`, `datec`, `tms`, `label`, `entity`, `description`, `lieu`, `address`, `cp`, `ville`, `fk_departement`, `fk_pays`, `statut`, `valo_pmp`, `fk_user_author`, `import_key`) VALUES
(1, '2013-02-25 19:34:23', '2013-04-13 15:50:07', 'PDV01-MAX', 1, 'ALMACEN PUNTO DE VENTA MAX PAREDES', 'MAX PAREDES', 'max paredes #123', '', '', NULL, 52, 1, NULL, 1, NULL),
(2, '2013-03-02 14:09:25', '2013-03-02 18:09:25', 'Almacen MP huevos', 1, 'Almacen de huevos en bafles', 'Almacen de huevos', 'Chaco, planta industrial', '', '', NULL, 52, 1, NULL, 1, NULL),
(3, '2013-03-02 14:11:33', '2013-04-13 15:50:23', 'PDV02-ANEXO', 1, 'Anexo (con salon de te)', 'ANEXO', 'calle max paredes · 637 anexo', '', '', NULL, 52, 1, NULL, 1, NULL),
(4, '2013-03-02 14:12:35', '2013-04-13 15:50:40', 'PDV03-YUNGAS', 1, 'Tienda Yungas (frente al mercado)', 'YUNGAS', 'Calle Uchumayu #613 , sq Yungas', '', '', NULL, 52, 1, NULL, 1, NULL),
(5, '2013-03-02 14:13:47', '2013-04-13 15:51:01', 'PDV04-SANPEDRO', 1, 'Sucursal San pedro  a media cuadra de la gasolinera', 'SAN PEDRO', 'Calle Almirante Grau , Tel: 2147796', '', '', NULL, 52, 1, NULL, 1, NULL),
(6, '2013-03-02 14:15:18', '2013-04-13 15:51:21', 'PDV05-ASPIAZU', 1, 'Sopocachi : entre 6 de Agosto y 20 de Octubre,', 'ASPIAZU', 'c. Aspiazu Nº 380, Tel 2412447', '', '', NULL, 52, 1, NULL, 1, NULL),
(7, '2013-03-02 14:16:03', '2013-04-13 15:51:49', 'PDV06-ECUADOR', 1, 'Sopocachi: Entre F. Guachalla y Rosendo Gutierrez', 'ECUADOR', 'Calle Ecuador 2276, tel 2412447', '', '', NULL, 52, 1, NULL, 1, NULL),
(8, '2013-03-02 14:26:57', '2013-03-02 18:26:57', 'TOTAL TIENDAS', 1, 'Consolidado de todas tiendas', 'Total Tiendas', 'La Paz', '', '', NULL, 52, 1, NULL, 1, NULL),
(9, '2013-03-02 14:32:01', '2013-03-02 18:32:01', 'TOTAL PLANTA IND', 1, '', 'Planta Industrial Chacho', 'Chacho, planta industrial', '', '', NULL, 52, 1, NULL, 1, NULL),
(10, '2013-03-02 14:32:23', '2013-03-02 18:32:23', 'HORNO', 1, 'Horneado de masa', 'Horneado', '', '', '', NULL, 52, 1, NULL, 1, NULL),
(11, '2013-04-13 12:01:26', '2013-04-13 16:01:26', 'PROD TERMINADO', 1, 'PRODUCTO TERMINADO LISTO PARA DESPACHO A TIENDAS', 'PRODUCTO TERMINADO', 'C.CHACO #2120', '', '', NULL, 52, 1, NULL, 1, NULL),
(13, '2013-04-13 12:23:19', '2013-04-13 16:23:19', 'ALMACEN CENTRAL', 1, 'ALMACENES DE MATERIA PRIMA E INSUMOS', 'ALMACEN CENTRAL', '', '', '', NULL, 52, 1, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_entrepot_bank_soc`
--

CREATE TABLE IF NOT EXISTS `llx_entrepot_bank_soc` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `numero_ip` varchar(15) NOT NULL,
  `fk_entrepotid` int(11) NOT NULL,
  `fk_socid` int(11) NOT NULL,
  `fk_cajaid` int(11) NOT NULL,
  `fk_bankid` int(11) DEFAULT '0',
  `fk_banktcid` int(11) DEFAULT '0',
  `status` varchar(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Registra permisos de Almacenes Terceros por IP' AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `llx_entrepot_bank_soc`
--

INSERT INTO `llx_entrepot_bank_soc` (`rowid`, `entity`, `numero_ip`, `fk_entrepotid`, `fk_socid`, `fk_cajaid`, `fk_bankid`, `fk_banktcid`, `status`) VALUES
(1, 1, '192.168.2.199', 1, 1, 1, 0, 0, '1'),
(2, 1, '192.168.2.109', 1, 1, 4, 0, 0, '1'),
(3, 1, '192.168.2.112', 1, 1, 1, 0, 0, '1'),
(4, 1, '192.168.2.110', 7, 3, 8, 0, 0, '1'),
(5, 1, '192.168.2.197', 6, 1, 9, NULL, NULL, '1'),
(6, 1, '192.168.2.116', 7, 1, 11, NULL, NULL, '1'),
(7, 1, '192.168.2.103', 5, 3, 6, NULL, NULL, '1'),
(8, 1, '192.168.1.117', 3, 1, 5, NULL, NULL, '1'),
(9, 1, '192.168.1.105', 11, 8, 10, NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_entrepot_relation`
--

CREATE TABLE IF NOT EXISTS `llx_entrepot_relation` (
  `rowid` int(11) NOT NULL,
  `fk_entrepot_father` int(11) NOT NULL,
  `tipo` varchar(30) COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin COMMENT='Relacion dependencia de Almacenes ';

--
-- Volcado de datos para la tabla `llx_entrepot_relation`
--

INSERT INTO `llx_entrepot_relation` (`rowid`, `fk_entrepot_father`, `tipo`) VALUES
(1, 8, 'almacen'),
(2, 9, 'almacen'),
(3, 8, 'almacen'),
(4, 8, 'almacen'),
(5, 8, 'almacen'),
(6, 8, 'almacen'),
(7, 8, 'almacen'),
(10, 9, 'almacen'),
(11, 9, 'almacen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_events`
--

CREATE TABLE IF NOT EXISTS `llx_events` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(32) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `dateevent` datetime DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `description` varchar(250) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `fk_object` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_events_dateevent` (`dateevent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_events`
--

INSERT INTO `llx_events` (`rowid`, `tms`, `type`, `entity`, `dateevent`, `fk_user`, `description`, `ip`, `user_agent`, `fk_object`) VALUES
(1, '2013-05-25 21:55:18', 'USER_LOGIN', 1, '2013-05-25 17:55:18', 1, '(UserLogged,admindb)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22', NULL),
(2, '2013-05-26 22:40:20', 'USER_LOGIN', 1, '2013-05-26 18:40:20', 1, '(UserLogged,admindb)', '192.168.2.103', 'Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(3, '2013-05-31 13:33:55', 'USER_LOGIN', 1, '2013-05-31 09:33:55', 1, '(UserLogged,admindb)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22', NULL),
(4, '2013-05-31 13:36:59', 'USER_LOGIN', 1, '2013-05-31 09:36:59', 1, '(UserLogged,admindb)', '192.168.2.11', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22', NULL),
(5, '2013-06-01 13:13:37', 'USER_LOGIN', 1, '2013-06-01 09:13:37', 1, '(UserLogged,admindb)', '192.168.1.117', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(6, '2013-06-01 15:47:55', 'USER_LOGIN', 1, '2013-06-01 11:47:55', 1, '(UserLogged,admindb)', '192.168.1.117', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(7, '2013-06-01 19:02:06', 'USER_LOGIN', 1, '2013-06-01 15:02:05', 1, '(UserLogged,admindb)', '192.168.1.105', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(8, '2013-06-01 21:39:32', 'USER_LOGIN', 1, '2013-06-01 17:39:32', 1, '(UserLogged,admindb)', '192.168.1.105', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(9, '2013-06-02 11:13:15', 'USER_LOGIN', 1, '2013-06-02 07:13:15', 1, '(UserLogged,admindb)', '192.168.1.105', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0', NULL),
(10, '2013-06-02 23:36:56', 'USER_LOGIN', 1, '2013-06-02 23:36:56', 1, '(UserLogged,admindb)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:20.0) Gecko/20100101 Firefox/20.0', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_expedition`
--

CREATE TABLE IF NOT EXISTS `llx_expedition` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `ref_ext` varchar(30) DEFAULT NULL,
  `ref_int` varchar(30) DEFAULT NULL,
  `ref_customer` varchar(30) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `date_expedition` datetime DEFAULT NULL,
  `date_delivery` datetime DEFAULT NULL,
  `fk_address` int(11) DEFAULT NULL,
  `fk_expedition_methode` int(11) DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `height` int(11) DEFAULT NULL,
  `height_unit` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `size_units` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `weight_units` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `note` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_expedition_uk_ref` (`ref`,`entity`),
  KEY `idx_expedition_fk_soc` (`fk_soc`),
  KEY `idx_expedition_fk_user_author` (`fk_user_author`),
  KEY `idx_expedition_fk_user_valid` (`fk_user_valid`),
  KEY `idx_expedition_fk_expedition_methode` (`fk_expedition_methode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_expedition`
--

INSERT INTO `llx_expedition` (`rowid`, `tms`, `ref`, `entity`, `fk_soc`, `ref_ext`, `ref_int`, `ref_customer`, `date_creation`, `fk_user_author`, `date_valid`, `fk_user_valid`, `date_expedition`, `date_delivery`, `fk_address`, `fk_expedition_methode`, `tracking_number`, `fk_statut`, `height`, `height_unit`, `width`, `size_units`, `size`, `weight_units`, `weight`, `note`, `model_pdf`) VALUES
(1, '2013-04-10 23:55:32', 'SH1304-0001', 1, 1, NULL, NULL, '123', '2013-04-10 19:54:42', 1, '2013-04-10 19:55:32', 1, NULL, '2013-04-11 00:00:00', NULL, 2, '', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(2, '2013-04-11 01:25:42', 'SH1304-0002', 1, 1, NULL, NULL, '132213213', '2013-04-10 21:11:13', 1, '2013-04-10 21:11:19', 1, NULL, '2013-04-10 00:00:00', NULL, NULL, '', 2, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL),
(3, '2013-05-14 19:19:53', '(PROV3)', 1, 71, NULL, NULL, '60606516', '2013-05-14 15:19:53', 1, NULL, NULL, NULL, '2013-05-14 00:00:00', NULL, NULL, '', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_expeditiondet`
--

CREATE TABLE IF NOT EXISTS `llx_expeditiondet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_expedition` int(11) NOT NULL,
  `fk_origin_line` int(11) DEFAULT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_expeditiondet_fk_expedition` (`fk_expedition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_expeditiondet`
--

INSERT INTO `llx_expeditiondet` (`rowid`, `fk_expedition`, `fk_origin_line`, `fk_entrepot`, `qty`, `rang`) VALUES
(1, 1, 13, 1, 10, 0),
(2, 2, 14, 1, 10, 0),
(3, 3, 17, 13, 100, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_export_compta`
--

CREATE TABLE IF NOT EXISTS `llx_export_compta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(12) NOT NULL,
  `date_export` datetime NOT NULL,
  `fk_user` int(11) NOT NULL,
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_export_model`
--

CREATE TABLE IF NOT EXISTS `llx_export_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `label` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `field` text NOT NULL,
  `filter` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_export_model` (`label`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `elementtype` varchar(64) NOT NULL DEFAULT 'member',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(255) NOT NULL,
  `type` varchar(8) DEFAULT NULL,
  `size` varchar(8) DEFAULT NULL,
  `pos` int(11) DEFAULT '0',
  `fieldunique` int(11) DEFAULT '0',
  `fieldrequired` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_extrafields_name` (`name`,`entity`,`elementtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_fabrication`
--

CREATE TABLE IF NOT EXISTS `llx_fabrication` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `ref` varchar(12) NOT NULL,
  `fk_commande` int(11) DEFAULT NULL,
  `date_creation` date NOT NULL,
  `date_delivery` date NOT NULL,
  `description` text,
  `statut` int(1) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabla de fabricacion' AUTO_INCREMENT=34 ;

--
-- Volcado de datos para la tabla `llx_fabrication`
--

INSERT INTO `llx_fabrication` (`rowid`, `entity`, `ref`, `fk_commande`, `date_creation`, `date_delivery`, `description`, `statut`) VALUES
(1, 1, 'fa1112', 6, '2013-03-15', '2013-03-15', NULL, 1),
(2, 1, '414324', 7, '2013-03-15', '1969-12-31', NULL, 1),
(3, 1, '1321', 2, '2013-04-02', '1969-12-31', NULL, 0),
(4, 1, 'ECUA001', 9, '2013-04-05', '2013-04-06', NULL, 1),
(5, 1, '123', 10, '2013-04-09', '2013-04-10', NULL, 0),
(6, 1, '123654', 11, '2013-04-10', '2013-04-11', NULL, 0),
(7, 1, '12312', 1, '2013-04-10', '2013-04-11', NULL, 0),
(8, 1, '12132231', 7, '2013-04-10', '2013-04-11', NULL, 1),
(9, 1, '546', 4, '2013-04-12', '2013-04-13', NULL, 0),
(10, 1, 'op123123', 0, '2013-04-12', '2013-04-13', NULL, 2),
(11, 1, '2321', 0, '2013-04-12', '2013-04-13', NULL, 2),
(12, 1, 'OP456456', 0, '2013-04-14', '2013-04-25', NULL, 1),
(13, 1, 'joseluis', 12, '2013-04-14', '2013-04-20', NULL, 1),
(14, 1, '213321321', 0, '2013-04-14', '2013-04-15', NULL, 1),
(15, 1, 'op45654', 0, '2013-04-17', '2013-04-18', NULL, 1),
(16, 1, '546465', 0, '2013-04-17', '2013-04-18', NULL, 1),
(17, 1, '010101', 0, '2013-04-26', '2013-04-27', NULL, 1),
(18, 1, '456456', 8, '2013-04-26', '2013-04-27', NULL, 1),
(19, 1, '(PROV)', 0, '2013-05-03', '2013-05-04', NULL, 0),
(20, 1, '(PROV)', 0, '2013-05-13', '2013-05-14', NULL, 0),
(21, 1, '', 0, '2013-05-13', '2013-05-14', NULL, 1),
(22, 1, '', 0, '2013-05-13', '2013-05-14', NULL, 0),
(23, 1, '', 0, '2013-05-13', '2013-05-14', NULL, 1),
(24, 1, '', 15, '2013-05-14', '2013-05-14', NULL, 1),
(25, 1, 'PR1305-0001', 0, '2013-05-15', '2013-05-16', NULL, 1),
(26, 1, 'PR1305-0002', 20, '2013-05-17', '2013-05-18', NULL, 1),
(27, 1, 'PR1305-0003', 21, '2013-05-17', '2013-05-25', NULL, 1),
(28, 1, 'PR1305-0004', 22, '2013-05-24', '2013-05-25', NULL, 2),
(29, 1, 'PR1305-0005', 23, '2013-05-24', '2013-05-25', NULL, 1),
(30, 1, 'PR1305-0006', 24, '2013-05-24', '2013-05-25', NULL, 1),
(31, 1, '(PROV)', 18, '2013-05-24', '2013-05-16', NULL, 0),
(32, 1, 'PR1305-0007', 0, '2013-05-24', '2013-05-25', NULL, 1),
(33, 1, '(PROV)', 0, '2013-05-31', '2013-06-01', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_fabricationdet`
--

CREATE TABLE IF NOT EXISTS `llx_fabricationdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_fabrication` int(11) NOT NULL,
  `fk_product` int(11) NOT NULL,
  `qty` double NOT NULL,
  `qty_decrease` double DEFAULT NULL,
  `qty_first` int(11) DEFAULT NULL,
  `qty_second` int(11) DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `date_shipping` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Registra los items de fabricacion' AUTO_INCREMENT=33 ;

--
-- Volcado de datos para la tabla `llx_fabricationdet`
--

INSERT INTO `llx_fabricationdet` (`rowid`, `fk_fabrication`, `fk_product`, `qty`, `qty_decrease`, `qty_first`, `qty_second`, `date_end`, `date_shipping`) VALUES
(2, 10, 36, 1000, 10, 900, 10, '2013-04-12', NULL),
(3, 11, 43, 1, 0, 1, 0, '2013-04-12', NULL),
(4, 12, 1560, 100, NULL, NULL, NULL, NULL, NULL),
(6, 13, 1560, 1000, NULL, NULL, NULL, NULL, NULL),
(7, 14, 45, 10, NULL, NULL, NULL, NULL, NULL),
(8, 14, 1560, 500, NULL, NULL, NULL, NULL, NULL),
(9, 14, 33, 50, NULL, NULL, NULL, NULL, NULL),
(10, 15, 39, 100, NULL, NULL, NULL, NULL, NULL),
(11, 16, 50, 100, NULL, NULL, NULL, NULL, NULL),
(12, 16, 43, 10, NULL, NULL, NULL, NULL, NULL),
(13, 16, 1560, 10, NULL, NULL, NULL, NULL, NULL),
(14, 2, 1501, 2, NULL, NULL, NULL, NULL, NULL),
(15, 1, 46, 10, NULL, NULL, NULL, NULL, NULL),
(16, 1, 219, 100, NULL, NULL, NULL, NULL, NULL),
(17, 17, 1560, 100, NULL, NULL, NULL, NULL, NULL),
(18, 18, 188, 2, NULL, NULL, NULL, NULL, NULL),
(20, 8, 220, 200, NULL, NULL, NULL, NULL, NULL),
(21, 21, 43, 10, NULL, NULL, NULL, NULL, NULL),
(22, 22, 33, 100, NULL, NULL, NULL, NULL, NULL),
(23, 23, 43, 77, NULL, NULL, NULL, NULL, NULL),
(24, 24, 43, 100, NULL, NULL, NULL, NULL, NULL),
(25, 25, 48, 7, NULL, NULL, NULL, NULL, NULL),
(26, 26, 43, 10, NULL, NULL, NULL, NULL, NULL),
(27, 27, 38, 100, NULL, NULL, NULL, NULL, NULL),
(28, 28, 43, 10, 1, 9, 1, '2013-05-24', NULL),
(29, 29, 1500, 2, NULL, NULL, NULL, NULL, NULL),
(30, 30, 43, 1, NULL, NULL, NULL, NULL, NULL),
(31, 32, 43, 1, NULL, NULL, NULL, NULL, NULL),
(32, 33, 1294, 2, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facture`
--

CREATE TABLE IF NOT EXISTS `llx_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `facnumber` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) DEFAULT NULL,
  `ref_int` varchar(255) DEFAULT NULL,
  `ref_client` varchar(255) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `increment` varchar(10) DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `date_valid` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `close_code` varchar(16) DEFAULT NULL,
  `close_note` varchar(128) DEFAULT NULL,
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) DEFAULT NULL,
  `fk_cond_reglement` int(11) NOT NULL DEFAULT '1',
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `date_lim_reglement` date DEFAULT NULL,
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_facture_uk_facnumber` (`facnumber`,`entity`),
  KEY `idx_facture_fk_soc` (`fk_soc`),
  KEY `idx_facture_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_fk_user_valid` (`fk_user_valid`),
  KEY `idx_facture_fk_facture_source` (`fk_facture_source`),
  KEY `idx_facture_fk_projet` (`fk_projet`),
  KEY `idx_facture_fk_account` (`fk_account`),
  KEY `idx_facture_fk_currency` (`fk_currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

--
-- Volcado de datos para la tabla `llx_facture`
--

INSERT INTO `llx_facture` (`rowid`, `facnumber`, `entity`, `ref_ext`, `ref_int`, `ref_client`, `type`, `increment`, `fk_soc`, `datec`, `datef`, `date_valid`, `tms`, `paye`, `amount`, `remise_percent`, `remise_absolue`, `remise`, `close_code`, `close_note`, `tva`, `localtax1`, `localtax2`, `total`, `total_ttc`, `fk_statut`, `fk_user_author`, `fk_user_valid`, `fk_facture_source`, `fk_projet`, `fk_account`, `fk_currency`, `fk_cond_reglement`, `fk_mode_reglement`, `date_lim_reglement`, `note`, `note_public`, `model_pdf`, `import_key`, `extraparams`) VALUES
(1, 'FA1302-0001', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-02-28 20:03:56', '2013-02-28', '2013-02-28', '2013-03-01 00:03:56', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 80.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-02-28', 'Cash\nRecibido : 100 USD\nRendu : 20 USD\n\n--------------------------------------\n\nNINGUNA', NULL, '', NULL, NULL),
(2, 'FA1303-0002', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 12:23:22', '2013-03-02', '2013-03-02', '2013-03-02 16:23:22', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 120.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 200 USD\nRendu : 80 USD\n\n--------------------------------------', NULL, '', NULL, NULL),
(3, 'FA1303-0003', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 14:07:51', '2013-03-02', '2013-03-02', '2013-03-02 18:07:52', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 180.00000000, 180.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 200 BOB\nRendu : 20 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(4, 'FA1303-0004', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 15:48:49', '2013-03-02', '2013-03-02', '2013-03-02 19:48:49', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 229.00000000, 229.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 300 BOB\nRendu : 71 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(5, 'FA1303-0005', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 16:51:44', '2013-03-02', '2013-03-02', '2013-03-02 20:51:44', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 158.00000000, 158.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 200 BOB\nRendu : 42 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(6, 'FA1303-0006', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 16:58:25', '2013-03-02', '2013-03-02', '2013-03-02 20:58:59', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 392.00000000, 392.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 400 BOB\nRendu : 8 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(7, 'FA1303-0007', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 16:59:44', '2013-03-02', '2013-03-02', '2013-03-02 20:59:44', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 482.00000000, 482.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 500 BOB\nRendu : 18 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(8, 'FA1303-0008', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 17:01:38', '2013-03-02', '2013-03-02', '2013-03-02 21:01:38', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 1025.00000000, 1025.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 2000 BOB\nRendu : 975 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(9, 'FA1303-0009', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 17:10:14', '2013-03-02', '2013-03-02', '2013-03-02 21:10:14', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 766.00000000, 766.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 1000 BOB\nRendu : 234 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(10, 'FA1303-0010', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-02 17:14:56', '2013-03-02', '2013-03-02', '2013-03-02 21:14:56', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 777.00000000, 777.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-02', 'Cash\nRecibido : 1000 BOB\nRendu : 223 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(11, 'FA1303-0011', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-04 16:25:49', '2013-03-04', '2013-03-04', '2013-03-04 20:25:49', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 455.00000000, 455.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-04', 'Cash\nRecibido : 500 BOB\nRendu : 45 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(12, 'FA1303-0012', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-05 22:27:22', '2013-03-05', '2013-03-05', '2013-03-06 02:27:23', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 4526.00000000, 4526.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-05', 'Cash\nRecibido : 5000 BOB\nRendu : 474 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(13, 'FA1303-0013', 1, NULL, NULL, '2204193', 0, NULL, 1, '2013-03-05 23:03:13', '2013-03-05', '2013-03-05', '2013-03-06 03:16:24', 0, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 2440.00000000, 2440.00000000, 1, 1, 1, NULL, NULL, NULL, NULL, 1, 0, '2013-03-05', NULL, 'sr. mariaca', 'crabe', NULL, NULL),
(14, 'FA1303-0014', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-03-09 12:00:52', '2013-03-09', '2013-03-09', '2013-03-09 16:00:52', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 2617.00000000, 2617.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-03-09', 'Cash\nRecibido : 7000 BOB\nRendu : 4383 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(15, 'FA1304-0015', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-10 21:29:21', '2013-04-10', '2013-04-10', '2013-04-11 01:29:21', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 600.00000000, 600.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-04-10', 'Cash\nRecibido : 1000 BOB\nRendu : 400 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(16, '(PROV16)', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-11 19:05:17', '2013-04-11', NULL, '2013-04-11 23:05:17', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 1346.00000000, 1346.00000000, 2, 12, NULL, NULL, NULL, NULL, NULL, 0, 4, '2013-04-11', 'Cash\nRecibido : 2000 BOB\nRendu : 654 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(17, 'FA1304-0016', 1, NULL, NULL, NULL, 0, NULL, 3, '2013-04-13 10:17:47', '2013-04-13', '2013-04-13', '2013-04-13 14:17:47', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 273.00000000, 273.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 300 BOB\nRendu : 27 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(18, 'FA1304-0017', 1, NULL, NULL, NULL, 0, NULL, 3, '2013-04-13 10:20:12', '2013-04-13', '2013-04-13', '2013-04-13 14:20:12', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 39527.00000000, 39527.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 40000 BOB\nRendu : 473 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(19, 'FA1304-0018', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-13 10:54:13', '2013-04-13', '2013-04-13', '2013-04-13 14:54:13', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 57.00000000, 57.00000000, 2, 12, 12, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 60 BOB\nRendu : 3 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(20, 'FA1304-0019', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-13 10:57:11', '2013-04-13', '2013-04-13', '2013-04-13 14:57:11', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 20.00000000, 20.00000000, 2, 12, 12, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 30 BOB\nRendu : 10 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(21, 'FA1304-0020', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-13 10:57:30', '2013-04-13', '2013-04-13', '2013-04-13 14:57:30', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 16.00000000, 16.00000000, 2, 12, 12, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 20 BOB\nRendu : 4 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(22, 'FA1304-0021', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-13 12:08:29', '2013-04-13', '2013-04-13', '2013-04-13 16:08:29', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 45.00000000, 45.00000000, 2, 12, 12, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 50 BOB\nRendu : 5 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(23, 'FA1304-0022', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-04-13 12:09:16', '2013-04-13', '2013-04-13', '2013-04-13 16:09:16', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 150.00000000, 150.00000000, 2, 12, 12, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 200 BOB\nRendu : 50 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(24, 'FA1304-0023', 1, NULL, NULL, NULL, 0, NULL, 3, '2013-04-13 14:11:07', '2013-04-13', '2013-04-13', '2013-04-13 18:11:07', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 212.00000000, 212.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-04-13', 'Cash\nRecibido : 1000 BOB\nRendu : 788 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(25, 'FA1304-0024', 1, NULL, NULL, NULL, 0, NULL, 3, '2013-04-15 19:26:59', '2013-04-15', '2013-04-15', '2013-04-15 23:26:59', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 93.00000000, 93.00000000, 2, 3, 3, NULL, NULL, NULL, NULL, 0, 4, '2013-04-15', 'Cash\nRecibido : 100 BOB\nRendu : 7 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(26, '(PROV26)', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:29:58', '2013-05-04', NULL, '2013-05-04 13:59:55', 0, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 330.00000000, 330.00000000, 1, 9, NULL, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 350 BOB\nRendu : 20 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(27, 'FA1305-0025', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:36:20', '2013-05-04', '2013-05-04', '2013-05-04 13:46:33', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 65.00000000, 2, 9, 1, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 70 BOB\nRendu : 5 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(28, 'FA1305-0026', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:41:14', '2013-05-04', '2013-05-04', '2013-05-04 13:46:38', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 630.00000000, 630.00000000, 2, 9, 1, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 650 BOB\nRendu : 20 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(29, 'FA1305-0027', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:49:22', '2013-05-04', '2013-05-04', '2013-05-04 13:49:22', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 189.00000000, 189.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 200 BOB\nRendu : 11 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(30, 'FA1305-0028', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:51:31', '2013-05-04', '2013-05-04', '2013-05-04 13:51:31', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 258.00000000, 258.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 280 BOB\nRendu : 22 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(31, 'FA1305-0029', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 09:52:37', '2013-05-04', '2013-05-04', '2013-05-04 13:52:37', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 138.00000000, 138.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 150 BOB\nRendu : 12 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(32, 'FA1305-0030', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 11:18:09', '2013-05-04', '2013-05-04', '2013-05-04 15:18:09', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 291.00000000, 291.00000000, 2, 1, 1, NULL, NULL, 5, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 300 BOB\nRendu : 9 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(33, 'FA1305-0031', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 11:21:38', '2013-05-04', '2013-05-04', '2013-05-04 15:21:39', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 8329.00000000, 8329.00000000, 2, 6, 6, NULL, NULL, 5, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 10000 BOB\nRendu : 1671 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(34, 'FA1305-0032', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 11:22:07', '2013-05-04', '2013-05-04', '2013-05-04 15:22:07', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 8329.00000000, 8329.00000000, 2, 6, 6, NULL, NULL, 5, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 10000 BOB\nRendu : 1671 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(35, 'FA1305-0033', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 11:57:41', '2013-05-04', '2013-05-04', '2013-05-04 15:57:41', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 775.00000000, 775.00000000, 2, 6, 6, NULL, NULL, 5, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 1010 BOB\nRendu : 235 BOB\n\n--------------------------------------\n\nentrega en oficians de cliente', NULL, '', NULL, NULL),
(36, 'FA1305-0034', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-04 12:08:38', '2013-05-04', '2013-05-04', '2013-05-04 16:08:39', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 1529.00000000, 1529.00000000, 2, 6, 6, NULL, NULL, 5, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 2000 BOB\nRendu : 471 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(37, 'FA1305-0035', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-04 20:25:28', '2013-05-04', '2013-05-04', '2013-05-05 00:25:28', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 2433.00000000, 2433.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 3000 BOB\nRendu : 567 BOB\n\n--------------------------------------\n\nventa con joseluis', NULL, '', NULL, NULL),
(38, 'FA1305-0036', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-04 20:28:11', '2013-05-04', '2013-05-04', '2013-05-05 00:28:11', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 70.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 100 BOB\nRendu : 30 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(39, 'FA1305-0037', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-04 20:38:03', '2013-05-04', '2013-05-04', '2013-05-05 00:38:03', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 138.00000000, 138.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-04', 'Cash\nRecibido : 230 BOB\nRendu : 92 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(40, '(PROV40)', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-18 13:21:52', '2013-05-18', NULL, '2013-05-18 17:21:52', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 40.00000000, 2, 9, NULL, NULL, NULL, 9, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 50 BOB\nRendu : 10 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(41, '(PROV41)', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-18 13:22:16', '2013-05-18', NULL, '2013-05-18 17:22:16', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 90.00000000, 2, 9, NULL, NULL, NULL, 9, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 100 BOB\nRendu : 10 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(42, 'FA1305-0038', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-18 13:23:25', '2013-05-18', '2013-05-18', '2013-05-18 17:23:25', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 139.00000000, 139.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 200 BOB\nRendu : 61 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(43, 'FA1305-0039', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-18 13:24:23', '2013-05-18', '2013-05-18', '2013-05-18 17:24:23', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 134.00000000, 134.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 200 BOB\nRendu : 66 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(44, 'FA1305-0040', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-18 13:25:25', '2013-05-18', '2013-05-18', '2013-05-18 17:25:25', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 294.00000000, 294.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 300 BOB\nRendu : 6 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(45, 'FA1305-0041', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-18 13:27:21', '2013-05-18', '2013-05-18', '2013-05-18 17:27:21', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 24.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 50 BOB\nRendu : 26 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(46, 'FA1305-0042', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-18 13:27:38', '2013-05-18', '2013-05-18', '2013-05-18 17:27:38', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 400.00000000, 400.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 500 BOB\nRendu : 100 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(47, 'FA1305-0043', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-18 14:41:59', '2013-05-18', '2013-05-18', '2013-05-18 18:41:59', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 229.00000000, 229.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-18', 'Cash\nRecibido : 300 BOB\nRendu : 71 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(48, 'FA1305-0044', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-25 09:00:21', '2013-05-25', '2013-05-25', '2013-05-25 13:00:21', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 120.00000000, 2, 1, 1, NULL, NULL, 9, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 150 BOB\nRendu : 30 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(49, 'FA1305-0045', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-25 09:00:53', '2013-05-25', '2013-05-25', '2013-05-25 13:20:38', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 135.00000000, 135.00000000, 2, 1, 1, NULL, NULL, 9, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 150 BOB\nRendu : 15 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(50, 'FA1305-0046', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-25 09:07:30', '2013-05-25', '2013-05-25', '2013-05-25 13:07:30', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 59.00000000, 59.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 60 BOB\nRendu : 1 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(51, 'FA1305-0047', 1, NULL, NULL, NULL, 0, NULL, 1, '2013-05-25 09:16:14', '2013-05-25', '2013-05-25', '2013-05-25 13:16:14', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 60.00000000, 60.00000000, 2, 9, 9, NULL, NULL, 9, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 60 BOB\nRendu :  BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(52, 'FA1305-0048', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-25 09:41:27', '2013-05-25', '2013-05-25', '2013-05-25 13:41:27', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 224.00000000, 224.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 300 BOB\nRendu : 76 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(53, 'FA1305-0049', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-25 11:21:08', '2013-05-25', '2013-05-25', '2013-05-25 15:21:08', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 1130.00000000, 1130.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 2000 BOB\nRendu : 870 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(54, 'FA1305-0050', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-05-25 11:23:50', '2013-05-25', '2013-05-25', '2013-05-25 15:23:50', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 4096.00000000, 4096.00000000, 2, 1, 1, NULL, NULL, 10, NULL, 0, 4, '2013-05-25', 'Cash\nRecibido : 5000 BOB\nRendu : 904 BOB\n\n--------------------------------------', NULL, '', NULL, NULL),
(55, 'FA1306-0051', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-06-01 16:05:09', '2013-06-01', '2013-06-01', '2013-06-01 20:05:10', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 30.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-06-01', 'Cash\nRecibido : 50 Bs.\nRendu : 20 Bs.\n\n--------------------------------------', NULL, '', NULL, NULL),
(56, 'FA1306-0052', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-06-01 16:05:55', '2013-06-01', '2013-06-01', '2013-06-01 20:05:55', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 24.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-06-01', 'Cash\nRecibido : 30 Bs.\nRendu : 6 Bs.\n\n--------------------------------------', NULL, '', NULL, NULL),
(57, 'FA1306-0053', 1, NULL, NULL, NULL, 0, NULL, 8, '2013-06-01 16:17:48', '2013-06-01', '2013-06-01', '2013-06-01 20:17:48', 1, 0.00000000, NULL, NULL, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 115.00000000, 115.00000000, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 4, '2013-06-01', 'Cash\nRecibido : 120 Bs.\nRendu : 5 Bs.\n\n--------------------------------------', NULL, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facturedet`
--

CREATE TABLE IF NOT EXISTS `llx_facturedet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `fk_remise_except` int(11) DEFAULT NULL,
  `subprice` double(24,8) DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `total_ht` double(24,8) DEFAULT NULL,
  `total_tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `fk_product_fournisseur_price` int(11) DEFAULT NULL,
  `buy_price_ht` double(24,8) DEFAULT '0.00000000',
  `fk_code_ventilation` int(11) NOT NULL DEFAULT '0',
  `fk_export_compta` int(11) NOT NULL DEFAULT '0',
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_fk_remise_except` (`fk_remise_except`,`fk_facture`),
  KEY `idx_facturedet_fk_facture` (`fk_facture`),
  KEY `idx_facturedet_fk_product` (`fk_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

--
-- Volcado de datos para la tabla `llx_facturedet`
--

INSERT INTO `llx_facturedet` (`rowid`, `fk_facture`, `fk_parent_line`, `fk_product`, `label`, `description`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `qty`, `remise_percent`, `remise`, `fk_remise_except`, `subprice`, `price`, `total_ht`, `total_tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `info_bits`, `fk_product_fournisseur_price`, `buy_price_ht`, `fk_code_ventilation`, `fk_export_compta`, `special_code`, `rang`, `import_key`) VALUES
(1, 1, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(2, 2, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(3, 2, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(4, 2, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 18.00000000, NULL, 36.00000000, 0.00000000, 0.00000000, 0.00000000, 36.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(5, 3, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 4.00000000, NULL, 20.00000000, 0.00000000, 0.00000000, 0.00000000, 20.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(6, 3, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 4.00000000, NULL, 16.00000000, 0.00000000, 0.00000000, 0.00000000, 16.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(7, 3, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 8, 0, 0, NULL, 18.00000000, NULL, 144.00000000, 0.00000000, 0.00000000, 0.00000000, 144.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(8, 4, NULL, 171, NULL, 'Mate', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 6.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(9, 4, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(10, 4, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(11, 4, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(12, 4, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(13, 5, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(14, 5, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(15, 5, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(16, 6, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 3.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(17, 6, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(18, 6, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(19, 6, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(20, 6, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 100.00000000, NULL, 200.00000000, 0.00000000, 0.00000000, 0.00000000, 200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(21, 6, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(22, 6, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(23, 6, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(24, 6, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(25, 7, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(26, 7, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(27, 7, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(28, 7, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 18.00000000, NULL, 90.00000000, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(29, 7, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(30, 7, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(31, 7, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(32, 7, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(33, 7, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(34, 8, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(35, 8, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 100.00000000, NULL, 200.00000000, 0.00000000, 0.00000000, 0.00000000, 200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(36, 8, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 35.00000000, NULL, 105.00000000, 0.00000000, 0.00000000, 0.00000000, 105.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(37, 8, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 18.00000000, NULL, 108.00000000, 0.00000000, 0.00000000, 0.00000000, 108.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(38, 8, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(39, 8, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(40, 8, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(41, 8, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(42, 8, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(43, 8, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 65.00000000, NULL, 390.00000000, 0.00000000, 0.00000000, 0.00000000, 390.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(44, 9, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 100.00000000, NULL, 600.00000000, 0.00000000, 0.00000000, 0.00000000, 600.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(45, 9, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(46, 9, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 4.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(47, 9, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(48, 9, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(49, 10, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 100.00000000, NULL, 600.00000000, 0.00000000, 0.00000000, 0.00000000, 600.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(50, 10, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(51, 10, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 4.00000000, NULL, 16.00000000, 0.00000000, 0.00000000, 0.00000000, 16.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(52, 10, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 4.00000000, NULL, 8.00000000, 0.00000000, 0.00000000, 0.00000000, 8.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(53, 10, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(54, 10, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(55, 11, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(56, 11, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(57, 11, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(58, 11, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(59, 11, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(60, 11, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(61, 11, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(62, 11, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(63, 11, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(64, 11, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(65, 12, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 8, 0, 0, NULL, 40.00000000, NULL, 320.00000000, 0.00000000, 0.00000000, 0.00000000, 320.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(66, 12, NULL, 40, NULL, 'Croasant', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(67, 12, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(68, 12, NULL, 213, NULL, 'Copa Helada', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(69, 12, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 12.00000000, NULL, 36.00000000, 0.00000000, 0.00000000, 0.00000000, 36.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(70, 12, NULL, 167, NULL, 'Galleta navideña pequeña', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 20.00000000, NULL, 20.00000000, 0.00000000, 0.00000000, 0.00000000, 20.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(71, 12, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(72, 12, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(73, 12, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(74, 12, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 41, 0, 0, NULL, 100.00000000, NULL, 4100.00000000, 0.00000000, 0.00000000, 0.00000000, 4100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(75, 13, NULL, NULL, NULL, 'torta de moca y sandia', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 12.00000000, NULL, 1200.00000000, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 2, NULL),
(76, 13, NULL, 1, NULL, '(País de origen: Bolivia)', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, 4.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 3, NULL),
(77, 13, NULL, NULL, NULL, 'torta de pisos, 1er piso es con puca, 2do es con nubes, 4 redondas que sean con su carita, buscar de internet , mandara por correo la foto al correo de la empresa.', 0.000, 0.000, NULL, 0.000, NULL, 100, 0, 0, NULL, 12.00000000, NULL, 1200.00000000, 0.00000000, 0.00000000, 0.00000000, 1200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 4, NULL),
(78, 14, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 29, 0, 0, NULL, 65.00000000, NULL, 1885.00000000, 0.00000000, 0.00000000, 0.00000000, 1885.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(79, 14, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 40.00000000, NULL, 160.00000000, 0.00000000, 0.00000000, 0.00000000, 160.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(80, 14, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 100.00000000, NULL, 300.00000000, 0.00000000, 0.00000000, 0.00000000, 300.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(81, 14, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 35.00000000, NULL, 210.00000000, 0.00000000, 0.00000000, 0.00000000, 210.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(82, 14, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 3.00000000, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 3.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(83, 14, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(84, 14, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(85, 14, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(86, 15, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 100.00000000, NULL, 600.00000000, 0.00000000, 0.00000000, 0.00000000, 600.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(87, 16, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 3.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(88, 16, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 201, 0, 0, NULL, 4.00000000, NULL, 804.00000000, 0.00000000, 0.00000000, 0.00000000, 804.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(89, 16, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 4.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(90, 16, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(91, 16, NULL, 1542, NULL, 'Corazones  mediano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(92, 16, NULL, 213, NULL, 'Copa Helada', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(93, 16, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 40.00000000, NULL, 120.00000000, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(94, 16, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 15.00000000, NULL, 45.00000000, 0.00000000, 0.00000000, 0.00000000, 45.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(95, 16, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(96, 16, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(97, 16, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 12.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(98, 17, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 40.00000000, NULL, 120.00000000, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(99, 17, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(100, 17, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(101, 17, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(102, 18, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 39527.00000000, 0.00000000, 0.00000000, 0.00000000, 39527.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(103, 19, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(104, 19, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 4.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(105, 19, NULL, 213, NULL, 'Copa Helada', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(106, 20, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 4.00000000, NULL, 20.00000000, 0.00000000, 0.00000000, 0.00000000, 20.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(107, 21, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 4.00000000, NULL, 16.00000000, 0.00000000, 0.00000000, 0.00000000, 16.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(108, 22, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 15.00000000, NULL, 45.00000000, 0.00000000, 0.00000000, 0.00000000, 45.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(109, 23, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 10, 0, 0, NULL, 15.00000000, NULL, 150.00000000, 0.00000000, 0.00000000, 0.00000000, 150.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(110, 24, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 15.00000000, NULL, 45.00000000, 0.00000000, 0.00000000, 0.00000000, 45.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(111, 24, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(112, 24, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(113, 24, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(114, 24, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(115, 24, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(116, 25, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 3.00000000, NULL, 9.00000000, 0.00000000, 0.00000000, 0.00000000, 9.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(117, 25, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 4.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(118, 25, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(119, 26, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 100.00000000, NULL, 200.00000000, 0.00000000, 0.00000000, 0.00000000, 200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(120, 26, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 65.00000000, NULL, 130.00000000, 0.00000000, 0.00000000, 0.00000000, 130.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(121, 27, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(122, 28, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 100.00000000, NULL, 600.00000000, 0.00000000, 0.00000000, 0.00000000, 600.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(123, 28, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(124, 29, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(125, 29, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(126, 29, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 4.00000000, NULL, 24.00000000, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(127, 30, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 40.00000000, NULL, 240.00000000, 0.00000000, 0.00000000, 0.00000000, 240.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(128, 30, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(129, 31, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 40.00000000, NULL, 120.00000000, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(130, 31, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(131, 32, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(132, 32, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 100.00000000, NULL, 200.00000000, 0.00000000, 0.00000000, 0.00000000, 200.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(133, 32, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 18.00000000, NULL, 36.00000000, 0.00000000, 0.00000000, 0.00000000, 36.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(134, 32, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(135, 33, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 200, 0, 0, NULL, 40.00000000, NULL, 8000.00000000, 0.00000000, 0.00000000, 0.00000000, 8000.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(136, 33, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(137, 33, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(138, 33, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(139, 33, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(140, 33, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(141, 33, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(142, 33, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(143, 33, NULL, 171, NULL, 'Mate', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 6.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(144, 33, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 3.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(145, 34, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 200, 0, 0, NULL, 40.00000000, NULL, 8000.00000000, 0.00000000, 0.00000000, 0.00000000, 8000.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(146, 34, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(147, 34, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(148, 34, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(149, 34, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(150, 34, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(151, 34, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(152, 34, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(153, 34, NULL, 171, NULL, 'Mate', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 6.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(154, 34, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 3.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(155, 35, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(156, 35, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(157, 35, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 3.00000000, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 6.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(158, 35, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(159, 35, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 100.00000000, NULL, 500.00000000, 0.00000000, 0.00000000, 0.00000000, 500.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(160, 35, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(161, 35, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(162, 35, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(163, 35, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(164, 36, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 40.00000000, NULL, 80.00000000, 0.00000000, 0.00000000, 0.00000000, 80.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(165, 36, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 30.00000000, NULL, 90.00000000, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(166, 36, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 3.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(167, 36, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 15.00000000, NULL, 60.00000000, 0.00000000, 0.00000000, 0.00000000, 60.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(168, 36, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 100.00000000, NULL, 500.00000000, 0.00000000, 0.00000000, 0.00000000, 500.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(169, 36, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 65.00000000, NULL, 130.00000000, 0.00000000, 0.00000000, 0.00000000, 130.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(170, 36, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 15.00000000, NULL, 60.00000000, 0.00000000, 0.00000000, 0.00000000, 60.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(171, 36, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(172, 36, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(173, 36, NULL, 213, NULL, 'Copa Helada', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 5.00000000, NULL, 10.00000000, 0.00000000, 0.00000000, 0.00000000, 10.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(174, 36, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 12.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(175, 36, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(176, 36, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(177, 36, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 18.00000000, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 18.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(178, 36, NULL, 1542, NULL, 'Corazones  mediano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(179, 36, NULL, 215, NULL, 'Bon Bon relleno', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(180, 36, NULL, 1559, NULL, 'bolsas de bombon surtido', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(181, 37, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(182, 37, NULL, 213, NULL, 'Copa Helada', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(183, 37, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(184, 37, NULL, 38, NULL, 'Empanadas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 3.00000000, NULL, 60.00000000, 0.00000000, 0.00000000, 0.00000000, 60.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(185, 37, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 12.00000000, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 12.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(186, 37, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 18.00000000, NULL, 36.00000000, 0.00000000, 0.00000000, 0.00000000, 36.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(187, 37, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 15.00000000, NULL, 75.00000000, 0.00000000, 0.00000000, 0.00000000, 75.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(188, 37, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 20, 0, 0, NULL, 40.00000000, NULL, 2000.00000000, 0.00000000, 0.00000000, 0.00000000, 2000.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(189, 37, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 35.00000000, NULL, 175.00000000, 0.00000000, 0.00000000, 0.00000000, 175.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(190, 38, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(191, 38, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 30.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(192, 39, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(193, 39, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 12.00000000, NULL, 24.00000000, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(194, 39, NULL, 184, NULL, 'Hamburguesa', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(195, 39, NULL, 164, NULL, 'Roscas Navideñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(196, 39, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(197, 39, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(198, 40, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(199, 41, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 18.00000000, NULL, 90.00000000, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(200, 42, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(201, 42, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 65.00000000, NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 65.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(202, 42, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 35.00000000, NULL, 70.00000000, 0.00000000, 0.00000000, 0.00000000, 70.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(203, 43, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(204, 43, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 30.00000000, NULL, 90.00000000, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(205, 43, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(206, 44, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(207, 44, NULL, 177, NULL, 'Copa Michelline', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 30.00000000, NULL, 90.00000000, 0.00000000, 0.00000000, 0.00000000, 90.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(208, 44, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 4.00000000, NULL, 4.00000000, 0.00000000, 0.00000000, 0.00000000, 4.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(209, 44, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(210, 44, NULL, 207, NULL, 'Torta mediana (helada)', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 65.00000000, NULL, 130.00000000, 0.00000000, 0.00000000, 0.00000000, 130.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(211, 45, NULL, 50, NULL, 'Salteñas', 0.000, 0.000, NULL, 0.000, NULL, 6, 0, 0, NULL, 4.00000000, NULL, 24.00000000, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(212, 46, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 100.00000000, NULL, 400.00000000, 0.00000000, 0.00000000, 0.00000000, 400.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(213, 47, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(214, 47, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 40.00000000, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 40.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(215, 47, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(216, 47, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 18.00000000, NULL, 54.00000000, 0.00000000, 0.00000000, 0.00000000, 54.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(217, 48, NULL, 37, NULL, 'Brazo Gitano', 0.000, 0.000, NULL, 0.000, NULL, 3, 0, 0, NULL, 40.00000000, NULL, 120.00000000, 0.00000000, 0.00000000, 0.00000000, 120.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(218, 49, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(219, 49, NULL, 47, NULL, 'Pie', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 35.00000000, NULL, 35.00000000, 0.00000000, 0.00000000, 0.00000000, 35.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(220, 50, NULL, 1, NULL, 'PASTEL DE HOJA', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 4.00000000, NULL, 8.00000000, 0.00000000, 0.00000000, 0.00000000, 8.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL);
INSERT INTO `llx_facturedet` (`rowid`, `fk_facture`, `fk_parent_line`, `fk_product`, `label`, `description`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `qty`, `remise_percent`, `remise`, `fk_remise_except`, `subprice`, `price`, `total_ht`, `total_tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `info_bits`, `fk_product_fournisseur_price`, `buy_price_ht`, `fk_code_ventilation`, `fk_export_compta`, `special_code`, `rang`, `import_key`) VALUES
(221, 50, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(222, 50, NULL, 39, NULL, 'Rollo de Queso', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 18.00000000, NULL, 36.00000000, 0.00000000, 0.00000000, 0.00000000, 36.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(223, 51, NULL, 34, NULL, 'Galletas', 0.000, 0.000, NULL, 0.000, NULL, 5, 0, 0, NULL, 12.00000000, NULL, 60.00000000, 0.00000000, 0.00000000, 0.00000000, 60.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(224, 52, NULL, 212, NULL, '3 leches', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(225, 52, NULL, 1559, NULL, 'bolsas de bombon surtido', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(226, 52, NULL, 215, NULL, 'Bon Bon relleno', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(227, 53, NULL, 215, NULL, 'Bon Bon relleno', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 5.00000000, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 5.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(228, 53, NULL, 1559, NULL, 'bolsas de bombon surtido', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(229, 53, NULL, 49, NULL, 'Brazo Especial', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 45.00000000, NULL, 45.00000000, 0.00000000, 0.00000000, 0.00000000, 45.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(230, 53, NULL, 1532, NULL, 'Caja Grande de Chocolate frutilla de 140 grs', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(231, 53, NULL, 1529, NULL, 'Caja Grande de Chocolate cherries de 175 grs', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(232, 53, NULL, 1540, NULL, 'Caja de Chocolate Surtidos', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(233, 53, NULL, 1541, NULL, 'Caja Corazones (140 grs)', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 214.00000000, 0.00000000, 0.00000000, 0.00000000, 214.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(234, 53, NULL, 173, NULL, 'Café Cortado', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 10.00000000, NULL, 10.00000000, 0.00000000, 0.00000000, 0.00000000, 10.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(235, 54, NULL, 1542, NULL, 'Corazones  mediano', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 0.00000000, NULL, 4096.00000000, 0.00000000, 0.00000000, 0.00000000, 4096.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(236, 55, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 2, 0, 0, NULL, 15.00000000, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 30.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(237, 56, NULL, 168, NULL, 'Café', 0.000, 0.000, NULL, 0.000, NULL, 4, 0, 0, NULL, 6.00000000, NULL, 24.00000000, 0.00000000, 0.00000000, 0.00000000, 24.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(238, 57, NULL, 29, NULL, 'MED. ESPECIAL', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 100.00000000, NULL, 100.00000000, 0.00000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL),
(239, 57, NULL, 33, NULL, 'Queques', 0.000, 0.000, NULL, 0.000, NULL, 1, 0, 0, NULL, 15.00000000, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 15.00000000, 0, NULL, NULL, 0, NULL, 0.00000000, 0, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facturedet_rec`
--

CREATE TABLE IF NOT EXISTS `llx_facturedet_rec` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `tva_tx` double(6,3) DEFAULT '19.600',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `total_ht` double(24,8) DEFAULT NULL,
  `total_tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facture_fourn`
--

CREATE TABLE IF NOT EXISTS `llx_facture_fourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `facnumber` varchar(50) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `libelle` varchar(255) DEFAULT NULL,
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise` double(24,8) DEFAULT '0.00000000',
  `close_code` varchar(16) DEFAULT NULL,
  `close_note` varchar(128) DEFAULT NULL,
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_cond_reglement` int(11) NOT NULL DEFAULT '1',
  `date_lim_reglement` date DEFAULT NULL,
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_facture_fourn_ref` (`facnumber`,`fk_soc`,`entity`),
  KEY `idx_facture_fourn_date_lim_reglement` (`date_lim_reglement`),
  KEY `idx_facture_fourn_fk_soc` (`fk_soc`),
  KEY `idx_facture_fourn_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_fourn_fk_user_valid` (`fk_user_valid`),
  KEY `idx_facture_fourn_fk_projet` (`fk_projet`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_facture_fourn`
--

INSERT INTO `llx_facture_fourn` (`rowid`, `facnumber`, `entity`, `ref_ext`, `type`, `fk_soc`, `datec`, `datef`, `tms`, `libelle`, `paye`, `amount`, `remise`, `close_code`, `close_note`, `tva`, `localtax1`, `localtax2`, `total`, `total_ht`, `total_tva`, `total_ttc`, `fk_statut`, `fk_user_author`, `fk_user_valid`, `fk_facture_source`, `fk_projet`, `fk_cond_reglement`, `date_lim_reglement`, `note`, `note_public`, `model_pdf`, `import_key`, `extraparams`) VALUES
(1, '98789', 1, NULL, 0, 5, '2013-04-06 12:50:00', '2013-04-06', '2013-04-06 16:51:09', '', 1, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 8849.56000000, 1150.44000000, 10000.00000000, 2, 1, 1, NULL, NULL, 1, NULL, '', '', NULL, NULL, NULL),
(2, '89897789', 1, NULL, 0, 5, '2013-04-06 12:52:25', '2013-04-06', '2013-04-06 16:53:20', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 8849.56000000, 1150.44000000, 10000.00000000, 1, 1, 1, NULL, NULL, 1, '2013-04-06', '', '', NULL, NULL, NULL),
(3, '3312213', 1, NULL, 0, 5, '2013-04-06 12:59:02', '2013-04-06', '2013-04-06 16:59:11', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 88495.58000000, 11504.43000000, 100000.01000000, 1, 1, 1, NULL, NULL, 1, '2013-04-06', '', '', NULL, NULL, NULL),
(4, '1111', 1, NULL, 0, 5, '2013-04-06 12:59:35', '2013-04-06', '2013-04-06 16:59:40', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 88495.58000000, 11504.43000000, 100000.01000000, 1, 1, 1, NULL, NULL, 1, NULL, '', '', NULL, NULL, NULL),
(5, '123123', 1, NULL, 0, 5, '2013-04-06 13:03:47', '2013-04-06', '2013-04-06 17:04:39', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 8849.56000000, 1150.44000000, 10000.00000000, 1, 1, 1, NULL, NULL, 1, '2013-04-06', '', '', NULL, NULL, NULL),
(6, '54564', 1, NULL, 0, 5, '2013-04-15 18:47:23', '2013-04-15', '2013-04-15 22:49:47', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 1840.00000000, 239.20000000, 2079.20000000, 0, 3, NULL, NULL, NULL, 1, '2013-04-15', '', '', NULL, NULL, NULL),
(7, '456456654', 1, NULL, 0, 59, '2013-05-25 13:25:40', '2013-05-25', '2013-05-25 17:27:20', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 1, 1, 1, NULL, NULL, 1, '2013-05-25', '', 'por deuda gestion 2012 ', NULL, NULL, NULL),
(8, 'Copia de 456456654', 1, NULL, 0, 59, '2013-05-25 13:36:34', '2013-05-25', '2013-05-25 17:36:34', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, 1, NULL, NULL, NULL, 1, NULL, '', 'por deuda gestion 2012 ', NULL, NULL, NULL),
(9, '46645465', 1, NULL, 0, 40, '2013-05-25 13:45:02', '2013-05-25', '2013-05-25 17:45:34', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 88.50000000, 11.50000000, 100.00000000, 1, 1, 1, NULL, NULL, 1, NULL, '', '', NULL, NULL, NULL),
(10, 'Gonzalo teran', 1, NULL, 0, 5, '2013-06-01 12:01:38', '2013-06-01', '2013-06-01 16:02:12', '', 0, 0.00000000, 0.00000000, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 10000.00000000, 1300.00000000, 11300.00000000, 1, 1, 1, NULL, NULL, 1, NULL, '', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facture_fourn_det`
--

CREATE TABLE IF NOT EXISTS `llx_facture_fourn_det` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture_fourn` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `ref` varchar(50) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `pu_ht` double(24,8) DEFAULT NULL,
  `pu_ttc` double(24,8) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `total_ht` double(24,8) DEFAULT NULL,
  `tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_facture_fourn_det_fk_facture` (`fk_facture_fourn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `llx_facture_fourn_det`
--

INSERT INTO `llx_facture_fourn_det` (`rowid`, `fk_facture_fourn`, `fk_product`, `ref`, `label`, `description`, `pu_ht`, `pu_ttc`, `qty`, `remise_percent`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `total_ht`, `tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `import_key`) VALUES
(1, 1, 1253, NULL, NULL, 'colorante rosado electrico 383 gr/unidad', 88.49558000, 100.00001000, 100, 0, 13.000, 0.000, NULL, 0.000, NULL, 8849.56000000, 1150.44000000, 0.00000000, 0.00000000, 10000.00000000, 0, NULL, NULL, NULL),
(2, 2, 1253, NULL, NULL, 'colorante rosado electrico 383 gr/unidad', 88.49558000, 100.00001000, 100, 0, 13.000, 0.000, NULL, 0.000, NULL, 8849.56000000, 1150.44000000, 0.00000000, 0.00000000, 10000.00000000, 0, NULL, NULL, NULL),
(3, 3, 1253, NULL, NULL, 'colorante rosado electrico 383 gr/unidad', 88.49558000, 100.00001000, 1000, 0, 13.000, 0.000, NULL, 0.000, NULL, 88495.58000000, 11504.43000000, 0.00000000, 0.00000000, 100000.01000000, 0, NULL, NULL, NULL),
(4, 4, 1253, NULL, NULL, 'colorante rosado electrico 383 gr/unidad', 88.49558000, 100.00001000, 1000, 0, 13.000, 0.000, NULL, 0.000, NULL, 88495.58000000, 11504.43000000, 0.00000000, 0.00000000, 100000.01000000, 0, NULL, NULL, NULL),
(5, 5, 1253, NULL, NULL, 'colorante rosado electrico 383 gr/unidad', 88.49558000, 100.00001000, 100, 0, 13.000, 0.000, NULL, 0.000, NULL, 8849.56000000, 1150.44000000, 0.00000000, 0.00000000, 10000.00000000, 0, NULL, NULL, NULL),
(6, 6, 1114, NULL, NULL, 'Harina  TRIGAL qq', 184.00000000, 207.92000000, 10, 0, 13.000, 0.000, NULL, 0.000, NULL, 1840.00000000, 239.20000000, 0.00000000, 0.00000000, 2079.20000000, 0, NULL, NULL, NULL),
(7, 7, NULL, NULL, NULL, 'asdadsasd', 0.00000000, 0.00000000, 1, 0, 0.000, 0.000, NULL, 0.000, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, NULL),
(8, 8, NULL, NULL, NULL, 'asdadsasd', 0.00000000, 0.00000000, 1, 0, 0.000, 0.000, NULL, 0.000, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, NULL),
(9, 9, NULL, NULL, NULL, 'sasdadsa', 8.84956000, 10.00000000, 10, 0, 13.000, 0.000, NULL, 0.000, NULL, 88.50000000, 11.50000000, 0.00000000, 0.00000000, 100.00000000, 0, NULL, NULL, NULL),
(10, 10, NULL, NULL, NULL, 'jalea', 100.00000000, 113.00000000, 100, 0, 13.000, 0.000, NULL, 0.000, NULL, 10000.00000000, 1300.00000000, 0.00000000, 0.00000000, 11300.00000000, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_facture_rec`
--

CREATE TABLE IF NOT EXISTS `llx_facture_rec` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT '0',
  `fk_mode_reglement` int(11) DEFAULT '0',
  `date_lim_reglement` date DEFAULT NULL,
  `note` text,
  `note_public` text,
  `frequency` int(11) DEFAULT NULL,
  `unit_frequency` varchar(2) DEFAULT 'd',
  `date_when` datetime DEFAULT NULL,
  `date_last_gen` datetime DEFAULT NULL,
  `nb_gen_done` int(11) DEFAULT NULL,
  `nb_gen_max` int(11) DEFAULT NULL,
  `usenewprice` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_facture_rec_uk_titre` (`titre`,`entity`),
  KEY `idx_facture_rec_fk_soc` (`fk_soc`),
  KEY `idx_facture_rec_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_rec_fk_projet` (`fk_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_fichinter`
--

CREATE TABLE IF NOT EXISTS `llx_fichinter` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `fk_contrat` int(11) DEFAULT '0',
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `datei` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `duree` double DEFAULT NULL,
  `description` text,
  `note_private` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_fichinter_ref` (`ref`,`entity`),
  KEY `idx_fichinter_fk_soc` (`fk_soc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_fichinterdet`
--

CREATE TABLE IF NOT EXISTS `llx_fichinterdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_fichinter` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `description` text,
  `duree` int(11) DEFAULT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_fichinterdet_fk_fichinter` (`fk_fichinter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_holiday`
--

CREATE TABLE IF NOT EXISTS `llx_holiday` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `halfday` int(11) DEFAULT '0',
  `statut` int(11) NOT NULL DEFAULT '1',
  `fk_validator` int(11) NOT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `date_refuse` datetime DEFAULT NULL,
  `fk_user_refuse` int(11) DEFAULT NULL,
  `date_cancel` datetime DEFAULT NULL,
  `fk_user_cancel` int(11) DEFAULT NULL,
  `detail_refuse` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_holiday_fk_user` (`fk_user`),
  KEY `idx_holiday_date_debut` (`date_debut`),
  KEY `idx_holiday_date_fin` (`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_holiday_config`
--

CREATE TABLE IF NOT EXISTS `llx_holiday_config` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `llx_holiday_config`
--

INSERT INTO `llx_holiday_config` (`rowid`, `name`, `value`) VALUES
(1, 'userGroup', '2'),
(2, 'lastUpdate', '20130601091454'),
(3, 'nbUser', '12'),
(4, 'delayForRequest', '31'),
(5, 'AlertValidatorDelay', '0'),
(6, 'AlertValidatorSolde', '0'),
(7, 'nbHolidayDeducted', '1'),
(8, 'nbHolidayEveryMonth', '2.08334');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_holiday_events`
--

CREATE TABLE IF NOT EXISTS `llx_holiday_events` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_holiday_name` (`name`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_holiday_logs`
--

CREATE TABLE IF NOT EXISTS `llx_holiday_logs` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_action` datetime NOT NULL,
  `fk_user_action` int(11) NOT NULL,
  `fk_user_update` int(11) NOT NULL,
  `type_action` varchar(255) NOT NULL,
  `prev_solde` varchar(255) NOT NULL,
  `new_solde` varchar(255) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Volcado de datos para la tabla `llx_holiday_logs`
--

INSERT INTO `llx_holiday_logs` (`rowid`, `date_action`, `fk_user_action`, `fk_user_update`, `type_action`, `prev_solde`, `new_solde`) VALUES
(1, '2013-05-18 13:56:25', 1, 1, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(2, '2013-05-18 13:56:26', 1, 2, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(3, '2013-05-18 13:56:26', 1, 3, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(4, '2013-05-18 13:56:26', 1, 4, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(5, '2013-05-18 13:56:26', 1, 5, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(6, '2013-05-18 13:56:26', 1, 6, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(7, '2013-05-18 13:56:26', 1, 7, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(8, '2013-05-18 13:56:26', 1, 8, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(9, '2013-05-18 13:56:26', 1, 9, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(10, '2013-05-18 13:56:26', 1, 10, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(11, '2013-05-18 13:56:26', 1, 12, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(12, '2013-05-18 13:56:26', 1, 14, 'Event : Mise à jour mensuelle', '0.00', '2.08'),
(13, '2013-06-01 09:14:54', 1, 1, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(14, '2013-06-01 09:14:54', 1, 2, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(15, '2013-06-01 09:14:54', 1, 3, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(16, '2013-06-01 09:14:54', 1, 4, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(17, '2013-06-01 09:14:54', 1, 5, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(18, '2013-06-01 09:14:54', 1, 6, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(19, '2013-06-01 09:14:54', 1, 7, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(20, '2013-06-01 09:14:54', 1, 8, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(21, '2013-06-01 09:14:54', 1, 9, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(22, '2013-06-01 09:14:54', 1, 10, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(23, '2013-06-01 09:14:54', 1, 12, 'Event : Mise à jour mensuelle', '2.08', '4.16'),
(24, '2013-06-01 09:14:54', 1, 14, 'Event : Mise à jour mensuelle', '2.08', '4.16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_holiday_users`
--

CREATE TABLE IF NOT EXISTS `llx_holiday_users` (
  `fk_user` int(11) NOT NULL,
  `nb_holiday` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_holiday_users`
--

INSERT INTO `llx_holiday_users` (`fk_user`, `nb_holiday`) VALUES
(1, 4.16668),
(2, 4.16668),
(3, 4.16668),
(4, 4.16668),
(5, 4.16668),
(6, 4.16668),
(7, 4.16668),
(8, 4.16668),
(9, 4.16668),
(10, 4.16668),
(12, 4.16668),
(14, 4.16668);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_import_model`
--

CREATE TABLE IF NOT EXISTS `llx_import_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `label` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `field` text NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_import_model` (`label`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_import_model`
--

INSERT INTO `llx_import_model` (`rowid`, `fk_user`, `label`, `type`, `field`) VALUES
(1, 1, 'michelline import', 'produit_1', '1=p.ref');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_inv_local`
--

CREATE TABLE IF NOT EXISTS `llx_inv_local` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `nombre_local` varchar(50) NOT NULL,
  `id_contacto` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_inv_local`
--

INSERT INTO `llx_inv_local` (`rowid`, `id_cliente`, `nombre_local`, `id_contacto`) VALUES
(1, 3, 'PDV01', 1),
(3, 2, 'PDV02', 1),
(4, 1, 'PDV03', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_inv_material`
--

CREATE TABLE IF NOT EXISTS `llx_inv_material` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha_obtenido` date DEFAULT NULL,
  `numero_serie` varchar(50) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `id_tipo_material` int(11) NOT NULL,
  `id_local` int(11) NOT NULL,
  `costo` float(9,3) DEFAULT NULL,
  `bloqueo` int(1) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_inv_material`
--

INSERT INTO `llx_inv_material` (`rowid`, `label`, `estado`, `fecha_obtenido`, `numero_serie`, `descripcion`, `id_tipo_material`, `id_local`, `costo`, `bloqueo`) VALUES
(1, 'TMN001', 'EMBueno', '2013-03-09', '12312', '', 1, 1, 200.000, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_inv_movimiento`
--

CREATE TABLE IF NOT EXISTS `llx_inv_movimiento` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `id_movimiento_padre` int(11) DEFAULT NULL,
  `id_local_origen` int(11) NOT NULL,
  `id_local_destino` int(11) DEFAULT NULL,
  `responsable_origen` varchar(50) NOT NULL,
  `responsable_destino` varchar(50) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `tipo_movimiento` varchar(20) DEFAULT NULL,
  `descripcion` text,
  `estado` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_inv_movimiento`
--

INSERT INTO `llx_inv_movimiento` (`rowid`, `id_movimiento_padre`, `id_local_origen`, `id_local_destino`, `responsable_origen`, `responsable_destino`, `fecha_inicio`, `fecha_fin`, `tipo_movimiento`, `descripcion`, `estado`) VALUES
(1, 0, 1, 3, 'SuperAdmin', 'SuperAdmin', '2013-03-05', '2013-03-05', 'movimiento', 'prueba, prueba', ''),
(2, 0, 3, 4, 'SuperAdmin', 'SuperAdmin', '2013-03-05', '2013-03-05', 'movimiento', 'prueba 2', ''),
(3, 0, 1, 3, 'SuperAdmin', 'SuperAdmin', '2013-03-09', '2013-03-09', 'movimiento', 'zzd', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_inv_mov_material`
--

CREATE TABLE IF NOT EXISTS `llx_inv_mov_material` (
  `id_material` int(11) NOT NULL,
  `id_movimiento` int(11) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_inv_tipomaterial`
--

CREATE TABLE IF NOT EXISTS `llx_inv_tipomaterial` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(20) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_inv_tipomaterial`
--

INSERT INTO `llx_inv_tipomaterial` (`rowid`, `label`) VALUES
(1, 'material 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_livraison`
--

CREATE TABLE IF NOT EXISTS `llx_livraison` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `ref_ext` varchar(30) DEFAULT NULL,
  `ref_int` varchar(30) DEFAULT NULL,
  `ref_customer` varchar(30) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `date_delivery` date DEFAULT NULL,
  `fk_address` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_livraison_uk_ref` (`ref`,`entity`),
  KEY `idx_livraison_fk_soc` (`fk_soc`),
  KEY `idx_livraison_fk_user_author` (`fk_user_author`),
  KEY `idx_livraison_fk_user_valid` (`fk_user_valid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `llx_livraison`
--

INSERT INTO `llx_livraison` (`rowid`, `tms`, `ref`, `entity`, `fk_soc`, `ref_ext`, `ref_int`, `ref_customer`, `date_creation`, `fk_user_author`, `date_valid`, `fk_user_valid`, `date_delivery`, `fk_address`, `fk_statut`, `total_ht`, `note`, `note_public`, `model_pdf`) VALUES
(1, '2013-04-10 23:55:57', 'BL1304-0001', 1, 1, NULL, NULL, '123', '2013-04-10 19:55:35', 1, '2013-04-10 19:55:57', 1, '2013-04-11', NULL, 1, 0.00000000, NULL, NULL, NULL),
(2, '2013-04-11 01:26:29', 'BL1304-0002', 1, 1, NULL, NULL, '132213213', '2013-04-10 21:25:49', 1, '2013-04-10 21:26:01', 1, '2013-04-10', NULL, 1, 0.00000000, NULL, NULL, 'typhon');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_livraisondet`
--

CREATE TABLE IF NOT EXISTS `llx_livraisondet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_livraison` int(11) DEFAULT NULL,
  `fk_origin_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `description` text,
  `qty` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_livraisondet_fk_expedition` (`fk_livraison`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `llx_livraisondet`
--

INSERT INTO `llx_livraisondet` (`rowid`, `fk_livraison`, `fk_origin_line`, `fk_product`, `description`, `qty`, `subprice`, `total_ht`, `rang`) VALUES
(1, 1, 13, 207, 'Torta mediana', 10, 0.00000000, 0.00000000, 0),
(2, 2, 14, 43, 'Torta Mediana', 10, 0.00000000, 0.00000000, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_localtax`
--

CREATE TABLE IF NOT EXISTS `llx_localtax` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` date DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `note` text,
  `fk_bank` int(11) DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_mailing`
--

CREATE TABLE IF NOT EXISTS `llx_mailing` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `statut` smallint(6) DEFAULT '0',
  `titre` varchar(60) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `sujet` varchar(60) DEFAULT NULL,
  `body` mediumtext,
  `bgcolor` varchar(8) DEFAULT NULL,
  `bgimage` varchar(255) DEFAULT NULL,
  `cible` varchar(60) DEFAULT NULL,
  `nbemail` int(11) DEFAULT NULL,
  `email_from` varchar(160) DEFAULT NULL,
  `email_replyto` varchar(160) DEFAULT NULL,
  `email_errorsto` varchar(160) DEFAULT NULL,
  `tag` varchar(128) DEFAULT NULL,
  `date_creat` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_appro` datetime DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_appro` int(11) DEFAULT NULL,
  `joined_file1` varchar(255) DEFAULT NULL,
  `joined_file2` varchar(255) DEFAULT NULL,
  `joined_file3` varchar(255) DEFAULT NULL,
  `joined_file4` varchar(255) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_mailing_cibles`
--

CREATE TABLE IF NOT EXISTS `llx_mailing_cibles` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_mailing` int(11) NOT NULL,
  `fk_contact` int(11) NOT NULL,
  `nom` varchar(160) DEFAULT NULL,
  `prenom` varchar(160) DEFAULT NULL,
  `email` varchar(160) NOT NULL,
  `other` varchar(255) DEFAULT NULL,
  `tag` varchar(128) DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `source_url` varchar(160) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `source_type` varchar(16) DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_mailing_cibles` (`fk_mailing`,`email`),
  KEY `idx_mailing_cibles_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_material_alternative`
--

CREATE TABLE IF NOT EXISTS `llx_material_alternative` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL,
  `fk_unit` int(11) NOT NULL,
  `qty_base` double NOT NULL,
  `fk_product_alt` int(11) NOT NULL,
  `fk_unit_alt` int(11) NOT NULL,
  `qty_alt` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_material_of_bill`
--

CREATE TABLE IF NOT EXISTS `llx_material_of_bill` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_product_father` int(11) NOT NULL,
  `fk_unit_father` int(11) NOT NULL,
  `fk_product_son` int(11) NOT NULL,
  `fk_unit_son` int(11) NOT NULL,
  `qty_father` double NOT NULL,
  `qty_son` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_menu`
--

CREATE TABLE IF NOT EXISTS `llx_menu` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `menu_handler` varchar(16) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `module` varchar(64) DEFAULT NULL,
  `type` varchar(4) NOT NULL,
  `mainmenu` varchar(100) NOT NULL,
  `leftmenu` varchar(100) DEFAULT NULL,
  `fk_menu` int(11) NOT NULL,
  `fk_mainmenu` varchar(24) DEFAULT NULL,
  `fk_leftmenu` varchar(24) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` varchar(100) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `langs` varchar(100) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `perms` varchar(255) DEFAULT NULL,
  `enabled` varchar(255) DEFAULT '1',
  `usertype` int(11) NOT NULL DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_menu_uk_menu` (`menu_handler`,`fk_menu`,`position`,`url`,`entity`),
  KEY `idx_menu_menuhandler_type` (`menu_handler`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=621 ;

--
-- Volcado de datos para la tabla `llx_menu`
--

INSERT INTO `llx_menu` (`rowid`, `menu_handler`, `entity`, `module`, `type`, `mainmenu`, `leftmenu`, `fk_menu`, `fk_mainmenu`, `fk_leftmenu`, `position`, `url`, `target`, `titre`, `langs`, `level`, `perms`, `enabled`, `usertype`, `tms`) VALUES
(56, 'smartphone', 1, '', 'top', '', NULL, 0, NULL, NULL, 100, 'www.dolibarr.org', '', 'dolibarr bolivia', '', NULL, '', '', 2, '2013-03-02 17:45:11'),
(228, 'all', 1, 'margin', 'left', 'accountancy', 'margins', -1, 'accountancy', NULL, 100, '/margin/index.php', '', 'Margins', 'margins', NULL, '1', '$conf->margin->enabled', 2, '2013-04-13 14:28:54'),
(358, 'all', 1, 'holiday', 'top', 'holiday', 'holiday', 0, NULL, NULL, 100, '/holiday/index.php', '', 'CPTitreMenu', 'holiday', NULL, '$user->rights->holiday->write', '1', 2, '2013-05-04 23:59:34'),
(359, 'all', 1, 'holiday', 'left', 'holiday', 'holiday', -1, 'holiday', NULL, 100, '/holiday/index.php?mainmenu=holiday&leftmenu=holiday', '', 'CPTitreMenu', 'holiday', NULL, '$user->rights->holiday->write', '1', 2, '2013-05-04 23:59:34'),
(360, 'all', 1, 'holiday', 'left', 'holiday', 'holiday_add', -1, 'holiday', 'holiday', 101, '/holiday/fiche.php?mainmenu=holiday&action=request', '', 'MenuAddCP', 'holiday', NULL, '$user->rights->holiday->write', '$conf->holiday->enabled', 2, '2013-05-04 23:59:34'),
(361, 'all', 1, 'holiday', 'left', 'holiday', 'holiday_conf', -1, 'holiday', 'holiday', 102, '/holiday/define_holiday.php?mainmenu=holiday&action=request', '', 'MenuConfCP', 'holiday', NULL, '$user->rights->holiday->define_holiday', '$conf->holiday->enabled', 2, '2013-05-04 23:59:34'),
(362, 'all', 1, 'holiday', 'left', 'holiday_def', 'holiday', -1, 'holiday', 'holiday', 103, '/holiday/view_log.php?mainmenu=holiday&action=request', '', 'MenuLogCP', 'holiday', NULL, '$user->rights->holiday->view_log', '$conf->holiday->enabled', 2, '2013-05-04 23:59:34'),
(363, 'all', 1, 'holiday', 'left', 'holiday', 'holiday_report', -1, 'holiday', 'holiday', 104, '/holiday/month_report.php?mainmenu=holiday&action=request', '', 'MenuReportMonth', 'holiday', NULL, '$user->rights->holiday->view_log', '$conf->holiday->enabled', 2, '2013-05-04 23:59:34'),
(499, 'eldy', 1, '', 'left', '', NULL, 498, NULL, NULL, 100, 'www.gmail.com', '', 'subtitulo 1', '', NULL, '', '', 2, '2013-05-25 16:04:36'),
(500, 'eldy', 1, '', 'left', '', NULL, 498, NULL, NULL, 100, 'www.as.com', '', 'subtitulo 2', '', NULL, '', '', 2, '2013-05-25 16:04:58'),
(501, 'all', 1, 'agenda', 'top', 'agenda', NULL, 0, NULL, NULL, 100, '/comm/action/index.php', '', 'Agenda', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(502, 'all', 1, 'agenda', 'left', 'agenda', NULL, 501, NULL, NULL, 100, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda', '', 'Actions', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(503, 'all', 1, 'agenda', 'left', 'agenda', NULL, 502, NULL, NULL, 101, '/comm/action/fiche.php?mainmenu=agenda&amp;leftmenu=agenda&amp;action=create', '', 'NewAction', 'commercial', NULL, '($user->rights->agenda->myactions->create||$user->rights->agenda->allactions->create)', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(504, 'all', 1, 'agenda', 'left', 'agenda', NULL, 502, NULL, NULL, 102, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda', '', 'Calendar', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(505, 'all', 1, 'agenda', 'left', 'agenda', NULL, 504, NULL, NULL, 103, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=todo&amp;filter=mine', '', 'MenuToDoMyActions', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(506, 'all', 1, 'agenda', 'left', 'agenda', NULL, 504, NULL, NULL, 104, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=done&amp;filter=mine', '', 'MenuDoneMyActions', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(507, 'all', 1, 'agenda', 'left', 'agenda', NULL, 504, NULL, NULL, 105, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=todo', '', 'MenuToDoActions', 'agenda', NULL, '$user->rights->agenda->allactions->read', '$user->rights->agenda->allactions->read', 2, '2013-06-01 13:13:13'),
(508, 'all', 1, 'agenda', 'left', 'agenda', NULL, 504, NULL, NULL, 106, '/comm/action/index.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=done', '', 'MenuDoneActions', 'agenda', NULL, '$user->rights->agenda->allactions->read', '$user->rights->agenda->allactions->read', 2, '2013-06-01 13:13:13'),
(509, 'all', 1, 'agenda', 'left', 'agenda', NULL, 502, NULL, NULL, 112, '/comm/action/listactions.php?mainmenu=agenda&amp;leftmenu=agenda', '', 'List', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(510, 'all', 1, 'agenda', 'left', 'agenda', NULL, 509, NULL, NULL, 113, '/comm/action/listactions.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=todo&amp;filter=mine', '', 'MenuToDoMyActions', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(511, 'all', 1, 'agenda', 'left', 'agenda', NULL, 509, NULL, NULL, 114, '/comm/action/listactions.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=done&amp;filter=mine', '', 'MenuDoneMyActions', 'agenda', NULL, '$user->rights->agenda->myactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(512, 'all', 1, 'agenda', 'left', 'agenda', NULL, 509, NULL, NULL, 115, '/comm/action/listactions.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=todo', '', 'MenuToDoActions', 'agenda', NULL, '$user->rights->agenda->allactions->read', '$user->rights->agenda->allactions->read', 2, '2013-06-01 13:13:13'),
(513, 'all', 1, 'agenda', 'left', 'agenda', NULL, 509, NULL, NULL, 116, '/comm/action/listactions.php?mainmenu=agenda&amp;leftmenu=agenda&amp;status=done', '', 'MenuDoneActions', 'agenda', NULL, '$user->rights->agenda->allactions->read', '$user->rights->agenda->allactions->read', 2, '2013-06-01 13:13:13'),
(514, 'all', 1, 'agenda', 'left', 'agenda', NULL, 502, NULL, NULL, 120, '/comm/action/rapport/index.php?mainmenu=agenda&amp;leftmenu=agenda', '', 'Reportings', 'agenda', NULL, '$user->rights->agenda->allactions->read', '$conf->agenda->enabled', 2, '2013-06-01 13:13:13'),
(515, 'all', 1, 'ecm', 'top', 'ecm', NULL, 0, NULL, NULL, 100, '/ecm/index.php', '', 'MenuECM', 'ecm', NULL, '$user->rights->ecm->read || $user->rights->ecm->upload || $user->rights->ecm->setup', '$conf->ecm->enabled', 2, '2013-06-01 13:13:14'),
(516, 'all', 1, 'ecm', 'left', 'ecm', NULL, 515, NULL, NULL, 101, '/ecm/index.php', '', 'ECMArea', 'ecm', NULL, '$user->rights->ecm->read || $user->rights->ecm->upload', '$user->rights->ecm->read || $user->rights->ecm->upload', 2, '2013-06-01 13:13:14'),
(517, 'all', 1, 'ecm', 'left', 'ecm', NULL, 516, NULL, NULL, 100, '/ecm/docdir.php?action=create', '', 'ECMNewSection', 'ecm', NULL, '$user->rights->ecm->setup', '$user->rights->ecm->setup', 2, '2013-06-01 13:13:14'),
(518, 'all', 1, 'ecm', 'left', 'ecm', NULL, 516, NULL, NULL, 102, '/ecm/index.php?action=file_manager', '', 'ECMFileManager', 'ecm', NULL, '$user->rights->ecm->read || $user->rights->ecm->upload', '$user->rights->ecm->read || $user->rights->ecm->upload', 2, '2013-06-01 13:13:14'),
(519, 'all', 1, 'fabrication', 'top', 'fabrication', NULL, 0, NULL, NULL, 100, '/fabrication/liste.php', '', 'Fabrication', 'fabrication@fabrication', NULL, '', '$conf->fabrication->enabled', 2, '2013-06-01 13:18:11'),
(520, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, 519, NULL, NULL, 100, '/fabrication/liste.php', '', 'Fabrication', 'fabrication@fabrication', NULL, '1', '$conf->fabrication->enabled', 2, '2013-06-01 13:18:11'),
(521, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, 520, NULL, NULL, 100, '/fabrication/liste_pedido.php', '', 'Listar Pedidos', 'fabrication@fabrication', NULL, '', '$conf->fabrication->enabled', 2, '2013-06-01 13:18:11'),
(522, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, 520, NULL, NULL, 100, '/fabrication/liste.php', '', 'Listar Orden Produc.', 'fabrication@fabrication', NULL, '$user->rights->fabrication->leerop', '$conf->fabrication->enabled', 2, '2013-06-01 13:18:11'),
(523, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, 520, NULL, NULL, 100, '/fabrication/fiche.php?action=create', '', 'Nueva Orden Produc.', 'fabrication@fabrication', NULL, '$user->rights->fabrication->crearop', '$conf->fabrication->enabled', 2, '2013-06-01 13:18:11'),
(524, 'all', 1, 'fabrication', 'left', 'fabrication', 'listproduct', 520, NULL, NULL, 100, '/fabrication/productlist/liste.php', '', 'Materiales', 'fabrication@fabrication', NULL, '$user->rights->fabrication->leerlistproduct', '$conf->almacen->enabled', 2, '2013-06-01 13:18:11'),
(525, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, -1, 'fabrication', 'listproduct', 100, '/fabrication/productlist/liste.php', '', 'Lista Materiales', 'fabrication@fabrication', NULL, '$user->rights->fabrication->leerlistproduct', '$conf->almacen->enabled', 2, '2013-06-01 13:18:11'),
(526, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, -1, 'fabrication', 'listproduct', 100, '/fabrication/productlist/fiche.php?action=create', '', 'Crear Lista Material', 'fabrication@fabrication', NULL, '$user->rights->fabrication->crearlistproduct', '$conf->almacen->enabled', 2, '2013-06-01 13:18:11'),
(527, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, -1, 'fabrication', 'listproduct', 100, '/fabrication/productalternative/liste.php', '', 'Lista Productos Alternativos', 'fabrication@fabrication', NULL, '$user->rights->fabrication->leerlistproductalt', '$conf->almacen->enabled', 2, '2013-06-01 13:18:11'),
(528, 'all', 1, 'fabrication', 'left', 'fabrication', NULL, -1, 'fabrication', 'listproduct', 100, '/fabrication/productalternative/fiche.php?action=create', '', 'Crear Producto Alternativo', 'fabrication@fabrication', NULL, '$user->rights->fabrication->crearlistproductalt', '$conf->almacen->enabled', 2, '2013-06-01 13:18:11'),
(555, 'all', 1, 'ventas', 'top', 'ventas', NULL, 0, NULL, NULL, 100, '/ventas/ventas.php', '', 'Punto de Venta', 'ventas', NULL, '$user->rights->ventas->use', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(556, 'all', 1, 'ventas', 'left', 'ventas', 'ventas', 555, NULL, NULL, 100, '/ventas/index.php?user=', 'ventas', 'Ingresar a PDV', 'ventas', NULL, '$user->rights->ventas->use', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(557, 'all', 1, 'ventas', 'left', 'ventas', 'permisos', 556, NULL, NULL, 100, '/ventas/permiso/liste.php', '', 'PermisosTercerosAlmacen', 'ventas', NULL, '$user->rights->ventas->leerPermiso', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(558, 'all', 1, 'ventas', 'left', 'ventas', NULL, -1, 'ventas', 'permisos', 100, '/ventas/permiso/liste.php', '', 'ListarPermisos', 'ventas', NULL, '$user->rights->ventas->leerPermiso', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(559, 'all', 1, 'ventas', 'left', 'ventas', NULL, -1, 'ventas', 'permisos', 100, '/ventas/permiso/fiche.php?action=create', '', 'CrearPermiso', 'ventas', NULL, '$user->rights->ventas->crearPermiso', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(560, 'all', 1, 'ventas', 'left', 'ventas', 'caja', 556, NULL, NULL, 100, '/ventas/resumen/index.php', '', 'Resumen Caja', 'ventas', NULL, '$user->rights->ventas->verrescaja', '$conf->ventas->enabled', 0, '2013-06-01 13:18:53'),
(561, 'all', 1, 'almacen', 'top', 'almacen', NULL, 0, NULL, NULL, 100, '/almacen/liste.php', '', 'Almacenes', 'almacen@almacen', NULL, '', '0', 0, '2013-06-01 22:05:09'),
(562, 'all', 1, 'almacen', 'left', 'almacen', NULL, 561, NULL, NULL, 100, '/almacen/liste.php', '', 'Almacen', 'almacen@almacen', NULL, '$user->rights->almacen->leerpedido', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(563, 'all', 1, 'almacen', 'left', 'almacen', NULL, 562, NULL, NULL, 100, '/almacen/liste.php', '', 'Listar Solicitudes', 'almacen@almacen', NULL, '$user->rights->almacen->leerpedido', '$conf->almacen->enabled', 0, '2013-06-01 22:05:09'),
(564, 'all', 1, 'almacen', 'left', 'almacen', NULL, 562, NULL, NULL, 100, '/almacen/fiche.php?action=create', '', 'Crear Nueva Solicitud', 'almacen@almacen', NULL, '$user->rights->almacen->crearpedido', '$conf->almacen->enabled', 0, '2013-06-01 22:05:09'),
(565, 'all', 1, 'almacen', 'left', 'almacen', 'locales', 562, NULL, NULL, 100, '/almacen/local/liste.php', '', 'Locales', 'almacen@almacen', NULL, '$user->rights->almacen->leerlocal', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(566, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'locales', 100, '/almacen/local/liste.php', '', 'Listar Locales', 'almacen@almacen', NULL, '$user->rights->almacen->leerlocal', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(567, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'locales', 100, '/almacen/local/fiche.php?action=create', '', 'Crear Nuevo Local', 'almacen@almacen', NULL, '$user->rights->almacen->crearlocal', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(568, 'all', 1, 'almacen', 'left', 'almacen', 'unidades', 562, NULL, NULL, 100, '/almacen/units/liste.php', '', 'Unidades', 'almacen@almacen', NULL, '$user->rights->almacen->leerunidad', '$conf->almacen->enabled', 0, '2013-06-01 22:05:09'),
(569, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'unidades', 100, '/almacen/units/liste.php', '', 'Listar Unidades', 'almacen@almacen', NULL, '$user->rights->almacen->leerunidad', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(570, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'unidades', 100, '/almacen/units/fiche.php?action=create', '', 'Crear Nueva Unidad', 'almacen@almacen', NULL, '$user->rights->almacen->crearunidad', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(571, 'all', 1, 'almacen', 'left', 'almacen', 'transferencia', 562, NULL, NULL, 100, '/almacen/transferencia/liste.php', '', 'Transfers', 'almacen@almacen', NULL, '$user->rights->almacen->leertransferencia', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(572, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'transferencia', 100, '/almacen/transferencia/fiche.php?action=create', '', 'CreateNewTransfer', 'almacen@almacen', NULL, '$user->rights->almacen->creartransferencia', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(573, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'transferencia', 100, '/almacen/transferencia/entry.php?action=create', '', 'CreateNewMovementEntry', 'almacen@almacen', NULL, '$user->rights->almacen->creartransferencia', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(574, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'transferencia', 100, '/almacen/transferencia/out.php?action=create', '', 'CreateNewMovementOut', 'almacen@almacen', NULL, '$user->rights->almacen->creartransferencia', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(575, 'all', 1, 'almacen', 'left', 'almacen', 'inventario', 562, NULL, NULL, 100, '/almacen/inventario/inventario.php', '', 'Inventarios', 'almacen@almacen', NULL, '$user->rights->almacen->crearunidad', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(576, 'all', 1, 'almacen', 'left', 'almacen', NULL, -1, 'almacen', 'inventario', 100, '/almacen/inventario/kardex.php', '', 'Kardex', 'almacen@almacen', NULL, '$user->rights->almacen->crearunidad', '$conf->almacen->enabled', 2, '2013-06-01 22:05:09'),
(607, 'eldy', 1, '', 'top', '', NULL, 0, NULL, NULL, 100, '/contab/period/liste.php', '', 'Gestion Financiera', 'contab', NULL, '$user->rights->contab->leerperiod', '$conf->contab->enable', 0, '2013-06-02 11:53:42'),
(608, 'eldy', 1, '', 'left', '', NULL, 607, NULL, NULL, 100, '/contab/period/liste.php', '', 'Periods', 'contab', NULL, '$user->rights->contab->leerperiod', '$conf->contab->enable', 0, '2013-06-02 11:55:04'),
(609, 'all', 1, 'contab', 'top', 'contab', NULL, 0, NULL, NULL, 100, '/contab/period/liste.php', '', 'Contab', 'contab@contab', NULL, '', '0', 0, '2013-06-03 00:20:49'),
(610, 'all', 1, 'contab', 'left', 'contab', NULL, 609, NULL, NULL, 100, '/contab/period/liste.php', '', 'Contab', 'contab@contab', NULL, '', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(611, 'all', 1, 'contab', 'left', 'contab', 'period', 610, NULL, NULL, 100, '/contab/period/liste.php', '', 'Periods', 'contab@contab', NULL, '$user->rights->contab->leerperiod', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(612, 'all', 1, 'contab', 'left', 'contab', NULL, -1, 'contab', 'period', 100, '/contab/period/fiche.php?action=create', '', 'Create period', 'contab@contab', NULL, '$user->rights->contab->crearperiod', '$conf->contab->enabled', 2, '2013-06-03 00:20:49'),
(613, 'all', 1, 'contab', 'left', 'contab', 'chart', 610, NULL, NULL, 100, '/contab/accounts/liste.php', '', 'Chart of accounts', 'contab@contab', NULL, '$user->rights->contab->leeraccount', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(614, 'all', 1, 'contab', 'left', 'contab', NULL, -1, 'contab', 'chart', 100, '/contab/accounts/fiche.php?action=create', '', 'Create chart account', 'contab@contab', NULL, '$user->rights->contab->crearaccount', '$conf->contab->enabled', 2, '2013-06-03 00:20:49'),
(615, 'all', 1, 'contab', 'left', 'contab', 'points', 610, NULL, NULL, 100, '/contab/pointentry/liste.php', '', 'Entry points', 'contab@contab', NULL, '$user->rights->contab->leerpoint', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(616, 'all', 1, 'contab', 'left', 'contab', NULL, -1, 'contab', 'points', 100, '/contab/pointentry/fiche.php?action=create', '', 'Create entry points', 'contab@contab', NULL, '$user->rights->contab->crearpoint', '$conf->contab->enabled', 2, '2013-06-03 00:20:49'),
(617, 'all', 1, 'contab', 'left', 'contab', 'sseat', 610, NULL, NULL, 100, '/contab/standardseat/liste.php', '', 'Standard seat', 'contab@contab', NULL, '$user->rights->contab->leerseatst', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(618, 'all', 1, 'contab', 'left', 'contab', NULL, -1, 'contab', 'sseat', 100, '/contab/standardseat/fiche.php?action=create', '', 'Create standard seat', 'contab@contab', NULL, '$user->rights->contab->crearseatst', '$conf->contab->enabled', 2, '2013-06-03 00:20:49'),
(619, 'all', 1, 'contab', 'left', 'contab', 'seats', 610, NULL, NULL, 100, '/contab/seats/liste.php', '', 'Seats', 'contab@contab', NULL, '$user->rights->contab->leerseatma', '$conf->contab->enabled', 0, '2013-06-03 00:20:49'),
(620, 'all', 1, 'contab', 'left', 'contab', NULL, -1, 'contab', 'seats', 100, '/contab/seats/fiche.php?action=create', '', 'Create seats', 'contab@contab', NULL, '$user->rights->contab->crearseatma', '$conf->contab->enabled', 2, '2013-06-03 00:20:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_notify`
--

CREATE TABLE IF NOT EXISTS `llx_notify` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `daten` datetime DEFAULT NULL,
  `fk_action` int(11) NOT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `objet_type` varchar(24) NOT NULL,
  `objet_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_notify_def`
--

CREATE TABLE IF NOT EXISTS `llx_notify_def` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` date DEFAULT NULL,
  `fk_action` int(11) NOT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `type` varchar(16) DEFAULT 'email',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_paiement`
--

CREATE TABLE IF NOT EXISTS `llx_paiement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` datetime DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  `fk_paiement` int(11) NOT NULL,
  `num_paiement` varchar(50) DEFAULT NULL,
  `note` text,
  `fk_bank` int(11) NOT NULL DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_export_compta` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Volcado de datos para la tabla `llx_paiement`
--

INSERT INTO `llx_paiement` (`rowid`, `entity`, `datec`, `tms`, `datep`, `amount`, `fk_paiement`, `num_paiement`, `note`, `fk_bank`, `fk_user_creat`, `fk_user_modif`, `statut`, `fk_export_compta`) VALUES
(1, 1, '2013-02-28 20:03:56', '2013-03-01 00:03:56', '2013-02-28 20:03:56', 80.00000000, 4, '', 'Pago Factura FA1302-0001', 5, 1, NULL, 0, 0),
(2, 1, '2013-03-02 12:23:22', '2013-03-02 16:23:22', '2013-03-02 12:23:22', 120.00000000, 4, '', 'Pago Factura FA1303-0002', 9, 1, NULL, 0, 0),
(3, 1, '2013-03-02 14:07:52', '2013-03-02 18:07:52', '2013-03-02 14:07:51', 180.00000000, 4, '', 'Pago Factura FA1303-0003', 10, 1, NULL, 0, 0),
(4, 1, '2013-03-02 15:48:49', '2013-03-02 19:48:49', '2013-03-02 15:48:49', 229.00000000, 4, '', 'Pago Factura FA1303-0004', 16, 1, NULL, 0, 0),
(5, 1, '2013-03-02 16:51:44', '2013-03-02 20:51:44', '2013-03-02 16:51:44', 158.00000000, 4, '', 'Pago Factura FA1303-0005', 25, 1, NULL, 0, 0),
(6, 1, '2013-03-02 16:58:25', '2013-03-02 20:58:25', '2013-03-02 16:58:25', 392.00000000, 4, '', 'Pago Factura FA1303-0006', 26, 1, NULL, 0, 0),
(7, 1, '2013-03-02 16:59:44', '2013-03-02 20:59:44', '2013-03-02 16:59:44', 482.00000000, 4, '', 'Pago Factura FA1303-0007', 27, 1, NULL, 0, 0),
(8, 1, '2013-03-02 17:01:38', '2013-03-02 21:01:38', '2013-03-02 17:01:38', 1025.00000000, 4, '', 'Pago Factura FA1303-0008', 29, 1, NULL, 0, 0),
(9, 1, '2013-03-02 17:10:14', '2013-03-02 21:10:14', '2013-03-02 17:10:14', 766.00000000, 4, '', 'Pago Factura FA1303-0009', 30, 1, NULL, 0, 0),
(10, 1, '2013-03-02 17:14:56', '2013-03-02 21:14:56', '2013-03-02 17:14:56', 777.00000000, 4, '', 'Pago Factura FA1303-0010', 31, 1, NULL, 0, 0),
(11, 1, '2013-03-04 16:25:49', '2013-03-04 20:25:49', '2013-03-04 16:25:49', 455.00000000, 4, '', 'Pago Factura FA1303-0011', 34, 1, NULL, 0, 0),
(12, 1, '2013-03-05 22:27:23', '2013-03-06 02:27:23', '2013-03-05 22:27:22', 4526.00000000, 4, '', 'Pago Factura FA1303-0012', 35, 1, NULL, 0, 0),
(14, 1, '2013-03-05 23:24:44', '2013-03-06 03:24:44', '2013-03-05 12:00:00', 3000.00000000, 1, '', '', 36, 1, NULL, 0, 0),
(15, 1, '2013-03-09 12:00:52', '2013-03-09 16:00:52', '2013-03-09 12:00:52', 2617.00000000, 4, '', 'Pago Factura FA1303-0014', 37, 1, NULL, 0, 0),
(16, 1, '2013-04-10 21:29:21', '2013-04-11 01:29:21', '2013-04-10 21:29:20', 600.00000000, 4, '', 'Pago Factura FA1304-0015', 41, 1, NULL, 0, 0),
(17, 1, '2013-04-11 19:05:17', '2013-04-11 23:05:17', '2013-04-11 19:05:17', 1346.00000000, 4, '', 'Payment Invoice FA1304-0016', 44, 12, NULL, 0, 0),
(18, 1, '2013-04-13 10:17:47', '2013-04-13 14:17:47', '2013-04-13 10:17:47', 273.00000000, 4, '', 'Pago Factura FA1304-0016', 52, 1, NULL, 0, 0),
(19, 1, '2013-04-13 10:20:12', '2013-04-13 14:20:12', '2013-04-13 10:20:12', 39527.00000000, 4, '', 'Pago Factura FA1304-0017', 55, 1, NULL, 0, 0),
(20, 1, '2013-04-13 10:54:13', '2013-04-13 14:54:13', '2013-04-13 10:54:13', 57.00000000, 4, '', 'Pago Factura FA1304-0018', 58, 12, NULL, 0, 0),
(21, 1, '2013-04-13 10:57:11', '2013-04-13 14:57:11', '2013-04-13 10:57:11', 20.00000000, 4, '', 'Pago Factura FA1304-0019', 61, 12, NULL, 0, 0),
(22, 1, '2013-04-13 10:57:30', '2013-04-13 14:57:30', '2013-04-13 10:57:30', 16.00000000, 4, '', 'Pago Factura FA1304-0020', 62, 12, NULL, 0, 0),
(23, 1, '2013-04-13 12:08:29', '2013-04-13 16:08:29', '2013-04-13 12:08:29', 45.00000000, 4, '', 'Pago Factura FA1304-0021', 67, 12, NULL, 0, 0),
(24, 1, '2013-04-13 12:09:16', '2013-04-13 16:09:16', '2013-04-13 12:09:16', 150.00000000, 4, '', 'Pago Factura FA1304-0022', 68, 12, NULL, 0, 0),
(25, 1, '2013-04-13 14:11:07', '2013-04-13 18:11:07', '2013-04-13 14:11:07', 212.00000000, 4, '', 'Pago Factura FA1304-0023', 69, 1, NULL, 0, 0),
(26, 1, '2013-04-15 19:26:59', '2013-04-15 23:26:59', '2013-04-15 19:26:58', 93.00000000, 4, '', 'Pago Factura FA1304-0024', 70, 3, NULL, 0, 0),
(27, 1, '2013-05-04 09:29:58', '2013-05-04 13:29:58', '2013-05-04 09:29:58', 330.00000000, 4, '', 'Payment Invoice FA1305-0025', 73, 9, NULL, 0, 0),
(28, 1, '2013-05-04 09:36:20', '2013-05-04 13:36:20', '2013-05-04 09:36:20', 65.00000000, 4, '', 'Pago Factura FA1305-0025', 74, 1, NULL, 0, 0),
(29, 1, '2013-05-04 09:41:14', '2013-05-04 13:41:14', '2013-05-04 09:41:14', 630.00000000, 4, '', 'Pago Factura FA1305-0026', 75, 1, NULL, 0, 0),
(30, 1, '2013-05-04 09:49:22', '2013-05-04 13:49:22', '2013-05-04 09:49:22', 189.00000000, 4, '', 'Pago Factura FA1305-0027', 76, 9, NULL, 0, 0),
(31, 1, '2013-05-04 09:51:31', '2013-05-04 13:51:31', '2013-05-04 09:51:31', 258.00000000, 4, '', 'Pago Factura FA1305-0028', 77, 9, NULL, 0, 0),
(32, 1, '2013-05-04 09:52:37', '2013-05-04 13:52:37', '2013-05-04 09:52:37', 138.00000000, 4, '', 'Pago Factura FA1305-0029', 78, 9, NULL, 0, 0),
(33, 1, '2013-05-04 11:18:09', '2013-05-04 15:18:09', '2013-05-04 11:18:09', 291.00000000, 4, '', 'Pago Factura FA1305-0030', 81, 1, NULL, 0, 0),
(34, 1, '2013-05-04 11:21:39', '2013-05-04 15:21:39', '2013-05-04 11:21:38', 8329.00000000, 4, '', 'Pago Factura FA1305-0031', 84, 6, NULL, 0, 0),
(35, 1, '2013-05-04 11:22:07', '2013-05-04 15:22:07', '2013-05-04 11:22:07', 8329.00000000, 4, '', 'Pago Factura FA1305-0032', 85, 6, NULL, 0, 0),
(36, 1, '2013-05-04 11:57:41', '2013-05-04 15:57:41', '2013-05-04 11:57:41', 775.00000000, 4, '', 'Pago Factura FA1305-0033', 87, 6, NULL, 0, 0),
(37, 1, '2013-05-04 12:08:38', '2013-05-04 16:08:39', '2013-05-04 12:08:38', 1529.00000000, 4, '', 'Pago Factura FA1305-0034', 88, 6, NULL, 0, 0),
(38, 1, '2013-05-04 20:25:28', '2013-05-05 00:25:28', '2013-05-04 20:25:28', 2433.00000000, 4, '', 'Pago Factura FA1305-0035', 89, 1, NULL, 0, 0),
(39, 1, '2013-05-04 20:28:11', '2013-05-05 00:28:11', '2013-05-04 20:28:11', 70.00000000, 4, '', 'Pago Factura FA1305-0036', 92, 1, NULL, 0, 0),
(40, 1, '2013-05-04 20:38:03', '2013-05-05 00:38:03', '2013-05-04 20:38:03', 138.00000000, 4, '', 'Pago Factura FA1305-0037', 94, 1, NULL, 0, 0),
(41, 1, '2013-05-18 13:21:52', '2013-05-18 17:21:52', '2013-05-18 13:21:52', 40.00000000, 4, '', 'Payment Invoice FA1305-0038', 95, 9, NULL, 0, 0),
(42, 1, '2013-05-18 13:22:16', '2013-05-18 17:22:16', '2013-05-18 13:22:16', 90.00000000, 4, '', 'Payment Invoice FA1305-0038', 96, 9, NULL, 0, 0),
(43, 1, '2013-05-18 13:23:25', '2013-05-18 17:23:25', '2013-05-18 13:23:25', 139.00000000, 4, '', 'Pago Factura FA1305-0038', 97, 1, NULL, 0, 0),
(44, 1, '2013-05-18 13:24:23', '2013-05-18 17:24:23', '2013-05-18 13:24:23', 134.00000000, 4, '', 'Pago Factura FA1305-0039', 98, 1, NULL, 0, 0),
(45, 1, '2013-05-18 13:25:25', '2013-05-18 17:25:25', '2013-05-18 13:25:25', 294.00000000, 4, '', 'Pago Factura FA1305-0040', 99, 1, NULL, 0, 0),
(46, 1, '2013-05-18 13:27:21', '2013-05-18 17:27:21', '2013-05-18 13:27:21', 24.00000000, 4, '', 'Pago Factura FA1305-0041', 100, 9, NULL, 0, 0),
(47, 1, '2013-05-18 13:27:38', '2013-05-18 17:27:38', '2013-05-18 13:27:38', 400.00000000, 4, '', 'Pago Factura FA1305-0042', 101, 9, NULL, 0, 0),
(48, 1, '2013-05-18 14:41:59', '2013-05-18 18:41:59', '2013-05-18 14:41:59', 229.00000000, 4, '', 'Pago Factura FA1305-0043', 104, 1, NULL, 0, 0),
(49, 1, '2013-05-25 09:00:21', '2013-05-25 13:00:21', '2013-05-25 09:00:21', 120.00000000, 4, '', 'Pago Factura FA1305-0044', 108, 1, NULL, 0, 0),
(50, 1, '2013-05-25 09:00:53', '2013-05-25 13:00:53', '2013-05-25 09:00:53', 135.00000000, 4, '', 'Pago Factura FA1305-0045', 109, 1, NULL, 0, 0),
(51, 1, '2013-05-25 09:07:30', '2013-05-25 13:07:30', '2013-05-25 09:07:30', 59.00000000, 4, '', 'Pago Factura FA1305-0046', 115, 9, NULL, 0, 0),
(52, 1, '2013-05-25 09:16:14', '2013-05-25 13:16:14', '2013-05-25 09:16:14', 60.00000000, 4, '', 'Pago Factura FA1305-0047', 116, 9, NULL, 0, 0),
(53, 1, '2013-05-25 09:41:27', '2013-05-25 13:41:27', '2013-05-25 09:41:27', 224.00000000, 4, '', 'Pago Factura FA1305-0048', 117, 1, NULL, 0, 0),
(54, 1, '2013-05-25 11:21:08', '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1130.00000000, 4, '', 'Pago Factura FA1305-0049', 118, 1, NULL, 0, 0),
(55, 1, '2013-05-25 11:23:50', '2013-05-25 15:23:50', '2013-05-25 11:23:50', 4096.00000000, 4, '', 'Pago Factura FA1305-0050', 121, 1, NULL, 0, 0),
(56, 1, '2013-06-01 16:05:10', '2013-06-01 20:05:10', '2013-06-01 16:05:09', 30.00000000, 4, '', 'Pago Factura FA1306-0051', 123, 1, NULL, 0, 0),
(57, 1, '2013-06-01 16:05:55', '2013-06-01 20:05:55', '2013-06-01 16:05:55', 24.00000000, 4, '', 'Pago Factura FA1306-0052', 124, 1, NULL, 0, 0),
(58, 1, '2013-06-01 16:17:48', '2013-06-01 20:17:48', '2013-06-01 16:17:48', 115.00000000, 4, '', 'Pago Factura FA1306-0053', 132, 1, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_paiementcharge`
--

CREATE TABLE IF NOT EXISTS `llx_paiementcharge` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_charge` int(11) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_typepaiement` int(11) NOT NULL,
  `num_paiement` varchar(50) DEFAULT NULL,
  `note` text,
  `fk_bank` int(11) NOT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_paiementfourn`
--

CREATE TABLE IF NOT EXISTS `llx_paiementfourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datep` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_paiement` int(11) NOT NULL,
  `num_paiement` varchar(50) DEFAULT NULL,
  `note` text,
  `fk_bank` int(11) NOT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `llx_paiementfourn`
--

INSERT INTO `llx_paiementfourn` (`rowid`, `tms`, `datec`, `datep`, `amount`, `fk_user_author`, `fk_paiement`, `num_paiement`, `note`, `fk_bank`, `statut`) VALUES
(1, '2013-04-06 16:50:43', '2013-04-06 12:50:43', '2013-04-06 12:00:00', 100000, 1, 4, '', '', 38, 0),
(3, '2013-04-06 16:53:51', '2013-04-06 12:53:51', '2013-04-06 12:00:00', 100000, 1, 1, '', '', 39, 0),
(4, '2013-04-06 16:54:33', '2013-04-06 12:54:33', '2013-04-06 12:00:00', 90000, 1, 1, '', '', 40, 0),
(6, '2013-05-25 17:46:08', '2013-05-25 13:46:08', '2013-05-25 12:00:00', 50, 1, 1, '', '', 122, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_paiementfourn_facturefourn`
--

CREATE TABLE IF NOT EXISTS `llx_paiementfourn_facturefourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_paiementfourn` int(11) DEFAULT NULL,
  `fk_facturefourn` int(11) DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_paiementfourn_facturefourn` (`fk_paiementfourn`,`fk_facturefourn`),
  KEY `idx_paiementfourn_facturefourn_fk_facture` (`fk_facturefourn`),
  KEY `idx_paiementfourn_facturefourn_fk_paiement` (`fk_paiementfourn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `llx_paiementfourn_facturefourn`
--

INSERT INTO `llx_paiementfourn_facturefourn` (`rowid`, `fk_paiementfourn`, `fk_facturefourn`, `amount`) VALUES
(1, 1, 1, 100000.00000000),
(3, 3, 2, 100000.00000000),
(4, 4, 2, 90000.00000000),
(6, 6, 9, 50.00000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_paiement_facture`
--

CREATE TABLE IF NOT EXISTS `llx_paiement_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_paiement` int(11) DEFAULT NULL,
  `fk_facture` int(11) DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_paiement_facture` (`fk_paiement`,`fk_facture`),
  KEY `idx_paiement_facture_fk_facture` (`fk_facture`),
  KEY `idx_paiement_facture_fk_paiement` (`fk_paiement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Volcado de datos para la tabla `llx_paiement_facture`
--

INSERT INTO `llx_paiement_facture` (`rowid`, `fk_paiement`, `fk_facture`, `amount`) VALUES
(1, 1, 1, 80.00000000),
(2, 2, 2, 120.00000000),
(3, 3, 3, 180.00000000),
(4, 4, 4, 229.00000000),
(5, 5, 5, 158.00000000),
(6, 6, 6, 392.00000000),
(7, 7, 7, 482.00000000),
(8, 8, 8, 1025.00000000),
(9, 9, 9, 766.00000000),
(10, 10, 10, 777.00000000),
(11, 11, 11, 455.00000000),
(12, 12, 12, 4526.00000000),
(14, 14, 13, 3000.00000000),
(15, 15, 14, 2617.00000000),
(16, 16, 15, 600.00000000),
(17, 17, 16, 1346.00000000),
(18, 18, 17, 273.00000000),
(19, 19, 18, 39527.00000000),
(20, 20, 19, 57.00000000),
(21, 21, 20, 20.00000000),
(22, 22, 21, 16.00000000),
(23, 23, 22, 45.00000000),
(24, 24, 23, 150.00000000),
(25, 25, 24, 212.00000000),
(26, 26, 25, 93.00000000),
(27, 27, 26, 330.00000000),
(28, 28, 27, 65.00000000),
(29, 29, 28, 630.00000000),
(30, 30, 29, 189.00000000),
(31, 31, 30, 258.00000000),
(32, 32, 31, 138.00000000),
(33, 33, 32, 291.00000000),
(34, 34, 33, 8329.00000000),
(35, 35, 34, 8329.00000000),
(36, 36, 35, 775.00000000),
(37, 37, 36, 1529.00000000),
(38, 38, 37, 2433.00000000),
(39, 39, 38, 70.00000000),
(40, 40, 39, 138.00000000),
(41, 41, 40, 40.00000000),
(42, 42, 41, 90.00000000),
(43, 43, 42, 139.00000000),
(44, 44, 43, 134.00000000),
(45, 45, 44, 294.00000000),
(46, 46, 45, 24.00000000),
(47, 47, 46, 400.00000000),
(48, 48, 47, 229.00000000),
(49, 49, 48, 120.00000000),
(50, 50, 49, 135.00000000),
(51, 51, 50, 59.00000000),
(52, 52, 51, 60.00000000),
(53, 53, 52, 224.00000000),
(54, 54, 53, 1130.00000000),
(55, 55, 54, 4096.00000000),
(56, 56, 55, 30.00000000),
(57, 57, 56, 24.00000000),
(58, 58, 57, 115.00000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_prelevement_bons`
--

CREATE TABLE IF NOT EXISTS `llx_prelevement_bons` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(12) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `statut` smallint(6) DEFAULT '0',
  `credite` smallint(6) DEFAULT '0',
  `note` text,
  `date_trans` datetime DEFAULT NULL,
  `method_trans` smallint(6) DEFAULT NULL,
  `fk_user_trans` int(11) DEFAULT NULL,
  `date_credit` datetime DEFAULT NULL,
  `fk_user_credit` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_prelevement_bons_ref` (`ref`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_prelevement_facture`
--

CREATE TABLE IF NOT EXISTS `llx_prelevement_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_prelevement_lignes` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_prelevement_facture_fk_prelevement_lignes` (`fk_prelevement_lignes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_prelevement_facture_demande`
--

CREATE TABLE IF NOT EXISTS `llx_prelevement_facture_demande` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `amount` double NOT NULL,
  `date_demande` datetime NOT NULL,
  `traite` smallint(6) DEFAULT '0',
  `date_traite` datetime DEFAULT NULL,
  `fk_prelevement_bons` int(11) DEFAULT NULL,
  `fk_user_demande` int(11) NOT NULL,
  `code_banque` varchar(7) DEFAULT NULL,
  `code_guichet` varchar(6) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `cle_rib` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_prelevement_lignes`
--

CREATE TABLE IF NOT EXISTS `llx_prelevement_lignes` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_prelevement_bons` int(11) DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `statut` smallint(6) DEFAULT '0',
  `client_nom` varchar(255) DEFAULT NULL,
  `amount` double DEFAULT '0',
  `code_banque` varchar(7) DEFAULT NULL,
  `code_guichet` varchar(6) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `cle_rib` varchar(5) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`),
  KEY `idx_prelevement_lignes_fk_prelevement_bons` (`fk_prelevement_bons`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_prelevement_rejet`
--

CREATE TABLE IF NOT EXISTS `llx_prelevement_rejet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_prelevement_lignes` int(11) DEFAULT NULL,
  `date_rejet` datetime DEFAULT NULL,
  `motif` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_creation` int(11) DEFAULT NULL,
  `note` text,
  `afacturer` tinyint(4) DEFAULT '0',
  `fk_facture` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product`
--

CREATE TABLE IF NOT EXISTS `llx_product` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(128) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(128) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `virtual` tinyint(4) NOT NULL DEFAULT '0',
  `fk_parent` int(11) DEFAULT '0',
  `label` varchar(255) NOT NULL,
  `description` text,
  `note` text,
  `customcode` varchar(32) DEFAULT NULL,
  `fk_country` int(11) DEFAULT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `price_ttc` double(24,8) DEFAULT '0.00000000',
  `price_min` double(24,8) DEFAULT '0.00000000',
  `price_min_ttc` double(24,8) DEFAULT '0.00000000',
  `price_base_type` varchar(3) DEFAULT 'HT',
  `tva_tx` double(6,3) DEFAULT NULL,
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `fk_user_author` int(11) DEFAULT NULL,
  `tosell` tinyint(4) DEFAULT '1',
  `tobuy` tinyint(4) DEFAULT '1',
  `fk_product_type` int(11) DEFAULT '0',
  `duration` varchar(6) DEFAULT NULL,
  `seuil_stock_alerte` int(11) DEFAULT '0',
  `barcode` varchar(255) DEFAULT NULL,
  `fk_barcode_type` int(11) DEFAULT '0',
  `accountancy_code_sell` varchar(15) DEFAULT NULL,
  `accountancy_code_buy` varchar(15) DEFAULT NULL,
  `partnumber` varchar(32) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `weight_units` tinyint(4) DEFAULT NULL,
  `length` float DEFAULT NULL,
  `length_units` tinyint(4) DEFAULT NULL,
  `surface` float DEFAULT NULL,
  `surface_units` tinyint(4) DEFAULT NULL,
  `volume` float DEFAULT NULL,
  `volume_units` tinyint(4) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `pmp` double(24,8) NOT NULL DEFAULT '0.00000000',
  `canvas` varchar(32) DEFAULT 'default@product',
  `finished` tinyint(4) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT '0',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_ref` (`ref`,`entity`),
  KEY `idx_product_label` (`label`),
  KEY `idx_product_barcode` (`barcode`),
  KEY `idx_product_import_key` (`import_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1727 ;

--
-- Volcado de datos para la tabla `llx_product`
--

INSERT INTO `llx_product` (`rowid`, `ref`, `entity`, `ref_ext`, `datec`, `tms`, `virtual`, `fk_parent`, `label`, `description`, `note`, `customcode`, `fk_country`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `tobuy`, `fk_product_type`, `duration`, `seuil_stock_alerte`, `barcode`, `fk_barcode_type`, `accountancy_code_sell`, `accountancy_code_buy`, `partnumber`, `weight`, `weight_units`, `length`, `length_units`, `surface`, `surface_units`, `volume`, `volume_units`, `stock`, `pmp`, `canvas`, `finished`, `hidden`, `import_key`) VALUES
(1, 'pastel001', 1, NULL, '2013-02-28 19:59:20', '2013-05-25 13:07:30', 0, 0, 'PASTEL DE HOJA', '', '', '', 52, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 10, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, -36, 0.00000000, '', 1, 0, NULL),
(29, 'TMN001', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 20:17:48', 0, 0, 'MED. ESPECIAL', 'Torta mediana Especial', 'decoracion con crema', '', NULL, 100.00000000, 100.00000000, 100.00000000, 100.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, -99, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(30, 'JAL001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'JALEA FRUTILLA', 'Jalea Artifical de Frutilla', 'LUDAFA', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(31, 'SER001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 20:05:53', 0, 0, 'SERV. TE', 'Alquiler de local para Servicio de Te', NULL, NULL, NULL, 300.00000000, 300.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 1, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(32, 'Pasteles', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Pasteles', 'Pasteles', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(33, 'Queques', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 20:17:48', 0, 0, 'Queques', 'Queques', NULL, NULL, NULL, 15.00000000, 15.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 64, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(34, 'Galletas', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 13:16:14', 0, 0, 'Galletas', 'Galletas', NULL, NULL, NULL, 12.00000000, 12.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -12, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(35, 'Galletas Especiales', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Galletas Especiales', 'Galletas Especiales', NULL, NULL, NULL, 15.00000000, 15.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(36, 'Galletas_Surtidas_Grandes', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 17:19:13', 0, 0, 'Galletas Surtidas Grandes', 'Galletas Surtidas Grandes', '', '', NULL, 33.00000000, 33.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(37, 'Brazo Gitano', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 13:00:21', 0, 0, 'Brazo Gitano', 'Brazo Gitano', NULL, NULL, NULL, 40.00000000, 40.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -438, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(38, 'Empanadas', 1, NULL, '2013-01-01 00:00:00', '2013-05-05 00:25:28', 0, 0, 'Empanadas', 'Empanadas', NULL, NULL, NULL, 3.00000000, 3.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -43, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(39, 'Rollo_de_Queso', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 13:07:30', 0, 0, 'Rollo de Queso', 'Rollo de Queso', '', '', NULL, 18.00000000, 18.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 50, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, -31, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(40, 'Croasant', 1, NULL, '2013-01-01 00:00:00', '2013-03-06 02:27:23', 0, 0, 'Croasant', 'Croasant', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(41, 'Torta Porción', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Torta Porción', 'Torta Porción', NULL, NULL, NULL, 10.00000000, 10.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(42, 'Torta Pequeña', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Torta Pequeña', 'Torta Pequeña', NULL, NULL, NULL, 60.00000000, 60.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(43, 'TME', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 16:30:50', 0, 0, 'Torta Mediana', 'Torta Mediana', '', '', NULL, 85.00000000, 85.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, 110, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(44, 'Torta Mediana Especial', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Torta Mediana Especial', 'Torta Mediana Especial', NULL, NULL, NULL, 100.00000000, 100.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(45, 'Torta Grande', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Torta Grande', 'Torta Grande', NULL, NULL, NULL, 140.00000000, 140.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(46, 'Torta Grande Especial', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Torta Grande Especial', 'Torta Grande Especial', NULL, NULL, NULL, 170.00000000, 170.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(47, 'Pie', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 13:00:53', 0, 0, 'Pie', 'Pie', NULL, NULL, NULL, 35.00000000, 35.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -33, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(48, 'Pie Especial', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Pie Especial', 'Pie Especial', NULL, NULL, NULL, 40.00000000, 40.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(49, 'Brazo Especial', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Brazo Especial', 'Brazo Especial', NULL, NULL, NULL, 45.00000000, 45.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(50, 'Salteñas', 1, NULL, '2013-01-01 00:00:00', '2013-05-18 17:27:21', 0, 0, 'Salteñas', 'Salteñas', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -109, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(51, 'Refrescos', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Refrescos', 'Refrescos', NULL, NULL, NULL, 6.00000000, 6.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(52, 'Rollitos con Jamon', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Rollitos con Jamon', 'Rollitos con Jamon', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(53, 'Puca capas', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Puca capas', 'Puca capas', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(54, 'Pie Pequeño', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'Pie Pequeño', 'Pie Pequeño', NULL, NULL, NULL, 5.00000000, 5.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(55, 'porcion de pie', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 14:12:55', 0, 0, 'porcion de pie', 'porcion de pie', NULL, NULL, NULL, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302101255'),
(164, 'Ros001', 1, NULL, '2013-01-01 00:00:00', '2013-05-05 00:38:03', 0, 0, 'Roscas Navideñas', 'Roscas Navideñas', NULL, NULL, NULL, 40.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 40.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -47, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(165, 'Tro001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Tronquito', 'Tronquito', NULL, NULL, NULL, 45.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 45.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(166, 'Gal001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Galleta navideña grande', 'Galleta navideña grande', NULL, NULL, NULL, 33.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 33.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(167, 'Gal002', 1, NULL, '2013-01-01 00:00:00', '2013-03-06 02:27:23', 0, 0, 'Galleta navideña pequeña', 'Galleta navideña pequeña', NULL, NULL, NULL, 20.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 20.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(168, 'Caf001', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 20:05:55', 0, 0, 'Café', 'Café', NULL, NULL, NULL, 6.00000000, 6.36000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 896, 10.00000000, 'default@product', NULL, 0, '20130302125531'),
(169, 'Té001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Té', 'Té', NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(170, 'Tod001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Toddy', 'Toddy', NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(171, 'Mat001', 1, NULL, '2013-01-01 00:00:00', '2013-05-04 15:22:07', 0, 0, 'Mate', 'Mate', NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -3, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(172, 'Cap001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Capuchino', 'Capuchino', NULL, NULL, NULL, 13.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(173, 'Caf002', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Café Cortado', 'Café Cortado', NULL, NULL, NULL, 10.00000000, 11.00000000, 0.00000000, 0.00000000, 'HT', 10.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(174, 'Lec001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Leche', 'Leche', NULL, NULL, NULL, 8.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 8.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(175, 'Hlp001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Helado Porción', 'Helado Porción', NULL, NULL, NULL, 17.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 17.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(176, 'Hel002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Helado Copa', 'Helado Copa', NULL, NULL, NULL, 23.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 23.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(177, 'Cop001', 1, NULL, '2013-01-01 00:00:00', '2013-05-18 17:25:25', 0, 0, 'Copa Michelline', 'Copa Michelline', NULL, NULL, NULL, 30.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 30.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -14, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(178, 'Mac001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Macedonia', 'Macedonia', NULL, NULL, NULL, 23.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 23.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(179, 'Ice001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Ice Cream Soda', 'Ice Cream Soda', NULL, NULL, NULL, 17.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 17.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(180, 'Hot001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Hot Dog', 'Hot Dog', NULL, NULL, NULL, 9.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 9.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(181, 'Sal001', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 20:25:33', 0, 0, 'Salchipapa', 'Salchipapa', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(182, 'Pol001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Pollo', 'Pollo', NULL, NULL, NULL, 12.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 12.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(183, 'Jam001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Jamon', 'Jamon', NULL, NULL, NULL, 14.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 14.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(184, 'Ham001', 1, NULL, '2013-01-01 00:00:00', '2013-05-05 00:38:03', 0, 0, 'Hamburguesa', 'Hamburguesa', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -16, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(185, 'Ham002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Hamburguesa Doble', 'Hamburguesa Doble', NULL, NULL, NULL, 22.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 22.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(186, 'San001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'S. de Cerdo', 'S. de Cerdo', NULL, NULL, NULL, 7.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 7.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(187, 'Por001', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 20:24:13', 0, 0, 'Porción de Papas', 'Porción de Papas', NULL, NULL, NULL, 7.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 7.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -4, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(188, 'Lom001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Lomito', 'Lomito', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(189, 'Lom002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Lomito Especial', 'Lomito Especial', NULL, NULL, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 18.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(190, 'Jug001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Jugo con Agua', 'Jugo con Agua', NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(191, 'Ens001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Ensalada de Frutas', 'Ensalada de Frutas', NULL, NULL, NULL, 18.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 18.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(192, 'Hel001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Helado con leche', 'Helado con leche', NULL, NULL, NULL, 23.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 23.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(193, 'Vel001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela de Bengala', 'Vela de Bengala', NULL, NULL, NULL, 7.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 7.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(194, 'Vel002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela Magica', 'Vela Magica', NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(195, 'Vel003', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela Signo', 'Vela Signo', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(196, 'Vel004', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela Figura', 'Vela Figura', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(197, 'Vel005', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela 15', 'Vela 15', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(198, 'Vel006', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela Numero', 'Vela Numero', NULL, NULL, NULL, 1.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 1.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(199, 'Vel007', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vela Maginca Epqueña', 'Vela Maginca Epqueña', NULL, NULL, NULL, 8.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 8.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(200, 'Tab001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Tableta Breik', 'Tableta Breik', NULL, NULL, NULL, 9.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 9.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(201, 'Tab002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Tableta Diet', 'Tableta Diet', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(202, 'Tab003', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Tableta Arcor', 'Tableta Arcor', NULL, NULL, NULL, 10.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 10.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(203, 'Tab004', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Tableta pequeña', 'Tableta pqueña', NULL, NULL, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 3.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(204, 'tar001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'tarjeta feliz cumpleaños', 'tarjeta feliz cumpleaños', NULL, NULL, NULL, 3.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 3.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(205, 'tar002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 17:20:06', 0, 0, 'tarjeta dia del padre', 'tarjeta dia del padre', '', '', NULL, 3.00000000, 3.00000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(206, 'gom001', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 16:01:05', 0, 0, 'gomitas', 'gomitas', '', '', NULL, 8.00000000, 9.04000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, 0, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(207, 'TMDza001', 1, NULL, '2013-01-01 00:00:00', '2013-05-18 17:25:25', 0, 0, 'Torta mediana (helada)', 'Torta mediana', '', '', NULL, 65.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 65.000, 0, 0.000, 0.000, 1, 1, 1, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, -48, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(208, 'Bra001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Brazo delizia', 'Brazo delizia', NULL, NULL, NULL, 25.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 25.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(209, 'Lit001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Litro familiar', 'Litro familiar', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(210, 'Cas001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Casaata', 'Casaata', NULL, NULL, NULL, 15.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 15.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(211, 'Cap002', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Capuchino', 'Capuchino', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(212, '3lech001', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 13:41:27', 0, 0, '3 leches', '3 leches', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(213, 'Cop002', 1, NULL, '2013-01-01 00:00:00', '2013-05-05 00:25:28', 0, 0, 'Copa Helada', 'Copa Helada', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -5, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(214, 'Vie001', 1, NULL, '2013-01-01 00:00:00', '2013-03-02 16:55:31', 0, 0, 'Vienesa', 'Vienesa', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(215, 'Bon001', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Bon Bon relleno', 'Bon Bon relleno', NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 5.000, 0, 0.000, 0.000, 1, 1, 0, 0, '3 dias', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -3, 0.00000000, 'default@product', NULL, 0, '20130302125531'),
(216, 'desayuno', 1, NULL, '2013-03-16 10:25:17', '2013-03-16 14:25:17', 0, 0, 'desayuno', 'desayuno americano', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', NULL, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0.00000000, '', 1, 0, NULL),
(219, 'cupcakes', 1, NULL, '2013-03-16 11:23:14', '2013-03-16 15:23:14', 0, 0, 'cupcakes', 'cupcakes', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0, 0, '', NULL, NULL, 0, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, '', 0, 0, NULL),
(220, 'torta_de_15_anhos_fondant', 1, NULL, '2013-03-16 11:46:02', '2013-03-16 15:46:02', 0, 0, 'torta de 15 anhos fondant', 'torta de 15 anhos fondant', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0, 0, '', NULL, NULL, 0, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, '', 0, 0, NULL),
(221, 'porcion_papa', 1, NULL, '2013-03-16 16:59:56', '2013-03-16 20:59:56', 0, 0, 'porcion de papa frita (lomito o ham)', 'lomito de carne', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 10, NULL, 0, '', '', NULL, 0.5, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0.00000000, '', 1, 0, NULL),
(1114, '10101', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 14:50:25', 0, 0, 'HARINA Trigal', 'Harina  TRIGAL qq', '45 kg/ qq', '', NULL, 231.00000000, 261.03000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 0, 1, 0, '', 0, NULL, 0, '', '', NULL, 45, 0, NULL, 0, NULL, 0, NULL, 0, -21422, 150.00000000, 'default@product', NULL, 0, '20130323124714'),
(1115, '10102', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 16:22:26', 0, 0, 'Har Tri 5', 'Harina  TRIGAL 5 kgs', '5 kg/ bolsa', NULL, NULL, NULL, 30.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -24, 25.00000000, 'default@product', NULL, 0, '20130323124714'),
(1116, '10201', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:39', 0, 0, 'Maicena', 'Maicena Carguill', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 1, 0, '', 0, NULL, 0, '', '', NULL, 25, 0, NULL, 0, NULL, 0, NULL, 0, -2, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1117, '10301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'PolH Fle', 'Polvo de Hornear Fleischman', '12 kg/ caja', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1118, '10302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'PolH Lud', 'polvo de hornear Ludafa 5 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1119, '10401', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 14:35:13', 0, 0, 'Cocoa A', 'Cocoa  a granel LIDER', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1120, '10402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cocoa ', 'Cocoa  MILKO', '20 u/caja', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1121, '10501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Lec pol', 'Leche en polvo PIL', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1122, '10601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'prop cal', 'propianato de calcio', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1123, '10701', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'algemix crema vainilla', 'algemix crema vainilla', '1kg/ bolsa', NULL, NULL, NULL, 37.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1124, '10702', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'algemix frutilla', 'algemix frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1125, '10703', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'algemix banana', 'algemix banana', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1126, '20101', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:39', 0, 0, 'Azucar Guabira', 'azucar blanca GUABIRA 46kg/qq', '', '', NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 1, 0, '', 0, NULL, 0, '', '', NULL, 46, 0, NULL, 0, NULL, 0, NULL, 0, -11302, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1127, '20102', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:38', 0, 0, 'Azucar Unagro', 'azucar blanca UNAGRO', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 1, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, -10, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1128, '20103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Azu mol', 'Azúcar Molida 46 Kg/qq', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1129, '20201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sal', 'Sal yodada ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1130, '20301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Coco', 'Coco Ralllado', NULL, NULL, NULL, NULL, 621.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1131, '20401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel lim', 'Gelatina de Limon Kris 230 grs', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1132, '20402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Fram', 'Gelatina Frambuesa 230 grs.', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1133, '20403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Frut', 'Gelatina de Frutilla 230gr', '20 unid/paquete', NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1134, '20404', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Cer', 'Gelatina cereza 230 GRS.', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1135, '20405', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Tuti', 'Gelatina de Tutti fruti chicle 230gr', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1136, '20406', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Lima', 'Gelatina de lima 230 g', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1137, '20407', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Nar ', 'Gelatina de Naranja  250 gr.', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1138, '20408', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel piña', 'Gelatina de Piña 230 gr', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1139, '20409', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Nar ', 'Gelatina de Naranja  230 gr', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1140, '20410', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel uva', 'Gelatina Uva de  250 gr', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1141, '20411', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel man', 'Gelatina de manazana de 230gr', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1142, '20412', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel neu', 'Gelatina s/sabor neutra de 500 gr', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1143, '20413', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Lima1', 'Gelatina de lima 1Kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1144, '20414', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel Nar1', 'Gelatina de Naranja 1kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1145, '20415', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gel piña1', 'Gelatina de piña 1kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1146, '20501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Toddy', 'Toddy 2 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1147, '20502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Chocomilko', 'Chocomilko 1 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1148, '20601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caf nes', 'Nescafe 200 gr', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1149, '20602', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caf cop', 'Café Copacabana 250 gr', NULL, NULL, NULL, NULL, 11.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1150, '20603', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caf hai', 'Café Haiti', NULL, NULL, NULL, NULL, 10.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1151, '20604', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caf ecc', 'Café Ecco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1152, '20701', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Nesq Choc', 'Nesquick Chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1153, '20702', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Nesq Frut', 'Nesquick Frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1154, '20801', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Grajeas de chocolate', 'Grajeas de chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1155, '20802', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Grajeas multicolor', 'Grajeas multicolor', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1156, '20901', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Pudin de Chocolate Kriss', 'Pudin de Chocolate Kriss', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1157, '20902', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Flan de vainilla Kriss', 'Flan de vainilla Kriss', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1158, '30101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cherry frutas de curico sin palo 6/C', 'Cherry frutas de curico sin palo 6/C', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1159, '30102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cherry sin palo ROCOFRUT  6u/caja', 'Cherry sin palo ROCOFRUT  6u/caja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1160, '30103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cerezas Surfrut', 'Cerezas Surfrut', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1161, '30104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cerezas Perelo', 'Cerezas Perelo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1162, '30201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Durazno ARCOR caja de 12 latas (mitades)', 'Durazno ARCOR caja de 12 latas (mitades)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1163, '30202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Durazno Aconcagua (grande)   6/caja', 'Durazno Aconcagua (grande)   6/caja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1164, '30203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Duraznos al jugo Alco', 'Duraznos al jugo Alco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714');
INSERT INTO `llx_product` (`rowid`, `ref`, `entity`, `ref_ext`, `datec`, `tms`, `virtual`, `fk_parent`, `label`, `description`, `note`, `customcode`, `fk_country`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `tobuy`, `fk_product_type`, `duration`, `seuil_stock_alerte`, `barcode`, `fk_barcode_type`, `accountancy_code_sell`, `accountancy_code_buy`, `partnumber`, `weight`, `weight_units`, `length`, `length_units`, `surface`, `surface_units`, `volume`, `volume_units`, `stock`, `pmp`, `canvas`, `finished`, `hidden`, `import_key`) VALUES
(1165, '30204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Durazno al Jugo Bon Provecho', 'Durazno al Jugo Bon Provecho', '24 lata/caja', NULL, NULL, NULL, 259.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1166, '30301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'piña al jugo San Benito', 'piña al jugo San Benito', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1167, '30401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Leche Condensada', 'Leche Condensada', NULL, NULL, NULL, NULL, 9.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1168, '30402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Leche Evaporada Pil', 'Leche Evaporada Pil', NULL, NULL, NULL, NULL, 7.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1169, '30403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Leche Evaporada Gloria', 'Leche Evaporada Gloria', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1170, '30501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Atun Van Camps al aceite', 'Atun Van Camps al aceite', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1171, '30502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Atun Van Camps al agua', 'Atun Van Camps al agua', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1172, '40101', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:38', 0, 0, 'Manteca Karina', 'Manteca Karina', '15 kg/caja', NULL, NULL, NULL, 252.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -2, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1173, '40201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Margarina Regia ', 'Margarina Regia ', '10 kg/caja', NULL, NULL, NULL, 169.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1174, '40301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mantequilla PIL', 'Mantequilla PIL', '10 kg/caja', NULL, NULL, NULL, 390.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1175, '40401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'aceite a granel', 'aceite a granel', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1176, '40402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'aceite fino ligth 4.5 lt', 'aceite fino ligth 4.5 lt', '4.5 lt/ bidon', NULL, NULL, NULL, 64.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1177, '40403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'aceite fino ligth 1.8 lt', 'aceite fino ligth 1.8 lt', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1178, '50101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea Azul 25KG', 'Jalea Azul 25KG', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1179, '50102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea Azul', 'Jalea Azul', NULL, NULL, NULL, NULL, 70.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1180, '50103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Abrillantador', 'Jalea de Abrillantador', NULL, NULL, NULL, NULL, 75.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1181, '50104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Chicle', 'Jalea de Chicle', NULL, NULL, NULL, NULL, 70.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1182, '50105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Chicle 25KG', 'Jalea de Chicle 25KG', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1183, '50106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Chocolate (25 Kg)', 'Jalea de Chocolate (25 Kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1184, '50107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Chocolate (5 Kg)', 'Jalea de Chocolate (5 Kg)', NULL, NULL, NULL, NULL, 70.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1185, '50108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Durazno (25 Kg)', 'Jalea de Durazno (25 Kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1186, '50109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Durazno', 'Jalea de Durazno', NULL, NULL, NULL, NULL, 70.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1187, '50110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Fresa   (25 Kg.)', 'Jalea de Fresa   (25 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1188, '50111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Fresa   (5 Kg.)', 'Jalea de Fresa   (5 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1189, '50112', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Limón', 'Jalea de Limón', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1190, '50113', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Limón   (25 Kg.)', 'Jalea de Limón   (25 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1191, '50114', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Mango 5 Kg.', 'Jalea de Mango 5 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1192, '50115', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Mango DE 25 KGRS', 'Jalea de Mango DE 25 KGRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1193, '50116', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Manzana verde  (5kg)', 'Jalea de Manzana verde  (5kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1194, '50117', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Manzana verde  (25kg)', 'Jalea de Manzana verde  (25kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1195, '50118', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Naranja 25 Kg.', 'Jalea de Naranja 25 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1196, '50119', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Naranja', 'Jalea de Naranja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1197, '50120', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de neutra 5 Kg', 'Jalea de neutra 5 Kg', NULL, NULL, NULL, NULL, 75.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1198, '50121', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Piña 5 kg.', 'Jalea de Piña 5 kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1199, '50122', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 14:34:20', 0, 0, 'Jalea de Morada (5 Kg)', 'Jalea de Morada (5 Kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 2.00000000, 'default@product', NULL, 0, '20130323124714'),
(1200, '50123', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Uva  5 Kg.', 'Jalea de Uva  5 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1201, '50124', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Kiwi 5 Kg.', 'Jalea de Kiwi 5 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1202, '50125', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Yogurt   (5 Kg.)', 'Jalea de Yogurt   (5 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1203, '50126', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Yogurt   (25 Kg.)', 'Jalea de Yogurt   (25 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1204, '50127', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jalea de Canela  (5 Kg.)', 'Jalea de Canela  (5 Kg.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1205, '50201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mermelada FRUTILLA  Amazonas', 'Mermelada FRUTILLA  Amazonas', '5 kg/balde', NULL, NULL, NULL, 60.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1206, '50202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mermelada TUTIFRUTI  6Kg del Valle', 'Mermelada TUTIFRUTI  6Kg del Valle', '6kg/balde', NULL, NULL, NULL, 90.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1207, '50301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Dulce de Batata 5 Kg.', 'Dulce de Batata 5 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1208, '50302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Dulce de Membrillo 5 Kg.', 'Dulce de Membrillo 5 Kg.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1209, '50401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Manjar PIL S.A. (5 Kg)', 'Manjar PIL S.A. (5 Kg)', NULL, NULL, NULL, NULL, 86.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1210, '50501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Miel de abejas balde de 5 kg', 'Miel de abejas balde de 5 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1211, '50502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Miel de abejas frasco de 1,4 kg', 'Miel de abejas frasco de 1,4 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1212, '50601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cobertura de Chocolate selecta', 'Cobertura de Chocolate selecta', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1213, '50602', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cobertura de Chocolate Nestle 1 kg', 'Cobertura de Chocolate Nestle 1 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1214, '50603', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cobertura blanca Nestle 2.3 kg', 'Cobertura blanca Nestle 2.3 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1215, '50604', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jarabe de Chocolate breik  de 5 Kg', 'Jarabe de Chocolate breik  de 5 Kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1216, '50701', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Glucosa', 'Glucosa', '25 kg/balde', NULL, NULL, NULL, 625.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1217, '60101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de almendra', 'Esencia de almendra', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1218, '60102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de CANELA', 'Esencia de CANELA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1219, '60103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de Vainilla la Negrita', 'Esencia de Vainilla la Negrita', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1220, '60104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de FRUTILLA Efrain', 'Esencia de FRUTILLA Efrain', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1221, '60105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de naranja', 'Esencia de naranja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1222, '60106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de paneton', 'Esencia de paneton', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1223, '60107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de PIÑA', 'Esencia de PIÑA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1224, '60108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de VAINILLA  5 L', 'Esencia de VAINILLA  5 L', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1225, '60109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de paneton  (LUDAFA)', 'Esencia de paneton  (LUDAFA)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1226, '60110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de  mantequilla (LUDAFA)', 'Esencia de  mantequilla (LUDAFA)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1227, '60111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia de naranja(LUDAFA)', 'Esencia de naranja(LUDAFA)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1228, '60112', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Almendra', 'Esencia en aceite Aroma de Almendra', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1229, '60113', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Canela', 'Esencia en aceite Aroma de Canela', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1230, '60114', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Chocolate', 'Esencia en aceite Aroma de Chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1231, '60115', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Coco', 'Esencia en aceite Aroma de Coco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1232, '60116', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Naranja', 'Esencia en aceite Aroma de Naranja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1233, '60117', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Nueces', 'Esencia en aceite Aroma de Nueces', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1234, '60118', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Panetone', 'Esencia en aceite Aroma de Panetone', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1235, '60119', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Vainilla', 'Esencia en aceite Aroma de Vainilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1236, '60120', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Frutilla', 'Esencia en aceite Aroma de Frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1237, '60121', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de CAFÉ', 'Esencia en aceite Aroma de CAFÉ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1238, '60122', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Vainilla blanca', 'Esencia en aceite Aroma de Vainilla blanca', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1239, '60123', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esencia en aceite Aroma de Chirimoya', 'Esencia en aceite Aroma de Chirimoya', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1240, '60201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Coñac Dreber', 'Coñac Dreber', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1241, '60202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Coñac 3 palos', 'Coñac 3 palos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1242, '60203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Licor de Café Bols', 'Licor de Café Bols', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1243, '60204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Amareto Bols', 'Amareto Bols', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1244, '60205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Singani Casa Real', 'Singani Casa Real', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1245, '60206', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Vino Cepas Mendocina', 'Vino Cepas Mendocina', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1246, '60301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caramelina', 'Caramelina', '5 lt/unidad', NULL, NULL, NULL, 100.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1247, '60401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante amarillo huevo 383 gr/unidad', 'colorante amarillo huevo 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1248, '60402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante amarillo limon 383 gr/unidad', 'colorante amarillo limon 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1249, '60403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante azul cielo 383 gr/unidad', 'colorante azul cielo 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1250, '60404', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante azul electrico 383 gr/unidad', 'colorante azul electrico 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1251, '60405', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante azul royal 383 gr/unidad', 'colorante azul royal 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1252, '60406', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante café chocolate 128 gr/unidad', 'colorante café chocolate 128 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1253, '60407', 1, NULL, '2013-01-01 00:00:00', '2013-04-06 17:04:39', 0, 0, 'colorante rosado electrico 383 gr/unidad', 'colorante rosado electrico 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 88.49558000, 'default@product', NULL, 0, '20130323124714'),
(1254, '60408', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante rosado suave 383 gr/unidad', 'colorante rosado suave 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1255, '60409', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante super negro 383 gr/unidad', 'colorante super negro 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1256, '60410', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante super rojo 383 gr/unidad', 'colorante super rojo 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1257, '60411', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante tartrazina', 'colorante tartrazina', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1258, '60412', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante verde electrico 383 gr/unidad', 'colorante verde electrico 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1259, '60413', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'colorante violeta 383 gr/unidad', 'colorante violeta 383 gr/unidad', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1260, '70101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Pasas Camino', 'Pasas Camino', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1261, '70102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Pasas Fruseco', 'Pasas Fruseco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1262, '70201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Nuez Entera', 'Nuez Entera', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1263, '80101', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:38', 0, 0, 'Crema de leche Pil', 'Crema de leche Pil', NULL, NULL, NULL, NULL, 19.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, -200, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1264, '80102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Crema Hulala Standard', 'Crema Hulala Standard', NULL, NULL, NULL, NULL, 22.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1265, '80103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Crema Hulala Frutilla', 'Crema Hulala Frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1266, '80201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Leche Natural PIL', 'Leche Natural PIL', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1267, '80301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Queso Dambo PIL', 'Queso Dambo PIL', NULL, NULL, NULL, NULL, 62.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1268, '80302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Queso Criollo', 'Queso Criollo', NULL, NULL, NULL, NULL, 25.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1269, '80303', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Queso San Javier', 'Queso San Javier', NULL, NULL, NULL, NULL, 36.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1270, '90101', 1, NULL, '2013-01-01 00:00:00', '2013-06-01 13:40:38', 0, 0, 'Huevo de 2da Caisy', 'Huevo de 2da Caisy', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -290, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1271, '100101', 1, NULL, '2013-01-01 00:00:00', '2013-05-15 00:11:15', 0, 0, 'Coca cola 350 cc', 'Coca cola 350 cc', '24 unid/caja', NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -61, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1272, '100102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Fanta 350 cc', 'Fanta 350 cc', '24 unid/caja', NULL, NULL, NULL, 72.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1273, '100103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sprite 350 cc', 'Sprite 350 cc', '24 unid/caja', NULL, NULL, NULL, 72.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1274, '100104', 1, NULL, '2013-01-01 00:00:00', '2013-05-15 00:11:15', 0, 0, 'Coca cola 500 cc', 'Coca cola 500 cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -10, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1275, '100105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Fanta 500 cc', 'Fanta 500 cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1276, '100106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Coca Cola 2500 cc', 'Coca Cola 2500 cc', '6 unid/paquete', NULL, NULL, NULL, 50.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1277, '100107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Fanta 2000 cc', 'Fanta 2000 cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1278, '100108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sprite 2000 cc', 'Sprite 2000 cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1279, '100109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Simba', 'Simba', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1280, '110101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Té canela ', 'Té canela ', '100sobres/caja', NULL, NULL, NULL, 17.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1281, '110102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Té Verde', 'Té Verde', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1282, '110103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mate de Aniz (cajas de 100 sobres)', 'Mate de Aniz (cajas de 100 sobres)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1283, '110104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mate de Coca (caja de 100 sobres)', 'Mate de Coca (caja de 100 sobres)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1284, '110105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mate de Manzanilla (1PQT= 100 unid)', 'Mate de Manzanilla (1PQT= 100 unid)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1285, '110106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Trimate', 'Trimate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1286, '110201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mayonesa 9cc', 'Mayonesa 9cc', '250 sobres/caja', NULL, NULL, NULL, 50.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1287, '110202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mostaza 9cc', 'Mostaza 9cc', '250 sobres/caja', NULL, NULL, NULL, 36.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1288, '110203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ketchup 9cc', 'Ketchup 9cc', '250 sobres/caja', NULL, NULL, NULL, 36.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1289, '110204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Mayonesa 5 kg', 'Mayonesa 5 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1290, '110205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ketchup 5 kg', 'Ketchup 5 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1291, '110206', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Salsa Golf', 'Salsa Golf', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1292, '110207', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Salsa soya', 'Salsa soya', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1293, '120101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Frutilla ', 'Frutilla ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1294, '120102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papayas', 'Papayas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1295, '120103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Platanos', 'Platanos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714');
INSERT INTO `llx_product` (`rowid`, `ref`, `entity`, `ref_ext`, `datec`, `tms`, `virtual`, `fk_parent`, `label`, `description`, `note`, `customcode`, `fk_country`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `tobuy`, `fk_product_type`, `duration`, `seuil_stock_alerte`, `barcode`, `fk_barcode_type`, `accountancy_code_sell`, `accountancy_code_buy`, `partnumber`, `weight`, `weight_units`, `length`, `length_units`, `surface`, `surface_units`, `volume`, `volume_units`, `stock`, `pmp`, `canvas`, `finished`, `hidden`, `import_key`) VALUES
(1296, '120104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Uva', 'Uva', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1297, '120105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Manzana', 'Manzana', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1298, '120201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Repollos', 'Repollos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1299, '120202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tomates una caja eq. 23 kilos', 'Tomates una caja eq. 23 kilos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1300, '120203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'pimentones', 'pimentones', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1301, '120204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Locotos', 'Locotos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1302, '120205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papas  quintal de 50 kilos  (carga)', 'Papas  quintal de 50 kilos  (carga)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1303, '120206', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Limon', 'Limon', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1304, '120207', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cebollas  arroba de 11 kilos', 'Cebollas  arroba de 11 kilos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1305, '120301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Lomitos', 'Lomitos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1306, '120302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sardinas', 'Sardinas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1307, '120303', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Hamburguesas', 'Hamburguesas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1308, '120304', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gas  GLP', 'Gas  GLP', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1309, '120305', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cubito KRIS', 'Cubito KRIS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1310, '120306', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sopas y Cremas KRIS', 'Sopas y Cremas KRIS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1311, '120307', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Arroz Grano de oro   46Kg', 'Arroz Grano de oro   46Kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1312, '120308', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Arroz Especial   46Kg', 'Arroz Especial   46Kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1313, '120309', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Arroz 3/4   46Kg', 'Arroz 3/4   46Kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1314, '120310', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Arroz Granillo 46 kg', 'Arroz Granillo 46 kg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1315, '130101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caja grande', 'caja grande', NULL, NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1316, '130102', 1, NULL, '2013-01-01 00:00:00', '2013-05-18 19:01:39', 0, 0, 'caja mediana paquete de 100u', 'caja mediana paquete de 100u', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -10, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1317, '130103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'caja pequena', 'caja pequena', NULL, NULL, NULL, NULL, 3.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1318, '130201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de carton(d.26cm /50)', 'Bandeja redonda de carton(d.26cm /50)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1319, '130202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de carton(#5)', 'Bandeja redonda de carton(#5)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1320, '130203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja de carton rectangular # 5', 'Bandeja de carton rectangular # 5', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1321, '130204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja de carton rectangular (26cmx35cm)', 'Bandeja de carton rectangular (26cmx35cm)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1322, '130205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Obleas de carton 29 cm', 'Obleas de carton 29 cm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1323, '130301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico de 270 mm', 'Bandeja redonda de plastico de 270 mm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1324, '130302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico 300 mm', 'Bandeja redonda de plastico 300 mm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1325, '130303', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico 330 mm de 1ra', 'Bandeja Plastico 330 mm de 1ra', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1326, '130304', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico 330 mm de 2da', 'Bandeja Plastico 330 mm de 2da', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1327, '130305', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico de 360 mm 1RA', 'Bandeja redonda de plastico de 360 mm 1RA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1328, '130306', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico de 360 mm 2DA', 'Bandeja redonda de plastico de 360 mm 2DA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1329, '130307', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico de 390 mm 1RA', 'Bandeja redonda de plastico de 390 mm 1RA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1330, '130308', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja redonda de plastico de 390 mm 2DA', 'Bandeja redonda de plastico de 390 mm 2DA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1331, '130309', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico Rectangular 40 por. 1RA', 'Bandeja Plastico Rectangular 40 por. 1RA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1332, '130310', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico Rectangular 40 por.2DA', 'Bandeja Plastico Rectangular 40 por.2DA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1333, '130311', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico Rectangular 50 por.1RA', 'Bandeja Plastico Rectangular 50 por.1RA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1334, '130312', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja Plastico Rectangular 50 por.2DA', 'Bandeja Plastico Rectangular 50 por.2DA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1335, '130401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja termIca  BT35 (Brazo Gitano)', 'Bandeja termIca  BT35 (Brazo Gitano)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1336, '130402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandejas Cuadradas (617)', 'Bandejas Cuadradas (617)', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1337, '130403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja rtglar.  Plastoformo  619', 'Bandeja rtglar.  Plastoformo  619', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1338, '130404', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandejas Rectangulares (621)', 'Bandejas Rectangulares (621)', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1339, '130405', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandejas Pasteles Cod. 623', 'Bandejas Pasteles Cod. 623', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1340, '130406', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Base fantasia circular 22 cm', 'Base fantasia circular 22 cm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1341, '130407', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Obleas 629 Pequeñas', 'Obleas 629 Pequeñas', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1342, '130408', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Obleas 628 Medianas ', 'Obleas 628 Medianas ', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1343, '130409', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja floreada 300 mm (625)', 'Bandeja floreada 300 mm (625)', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1344, '130410', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja floreada 325 mm (628)', 'Bandeja floreada 325 mm (628)', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1345, '130411', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sanguchera 6x6', 'Sanguchera 6x6', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1346, '130501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Base Rectangular  N0 39/14.5  domo 12', 'Base Rectangular  N0 39/14.5  domo 12', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1347, '130502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Base Circular  N0 25.5  domo 12', 'Base Circular  N0 25.5  domo 12', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1348, '130503', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Envase rectangular pequeño con tapa', 'Envase rectangular pequeño con tapa', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1349, '130504', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuche de plastico cuadrado(17cmx17)', 'Estuche de plastico cuadrado(17cmx17)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1350, '130505', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuche de plastico pequeño(8cmx8)', 'Estuche de plastico pequeño(8cmx8)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1351, '130506', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuche tapa media 146  PET  para galletas especiales', 'Estuche tapa media 146  PET  para galletas especiales', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1352, '130507', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuche tapa media 133  PET  para galletas especiales', 'Estuche tapa media 133  PET  para galletas especiales', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1353, '130508', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuche tapa cod.105133 para las porcionde tortas', 'Estuche tapa cod.105133 para las porcionde tortas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1354, '130509', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'estuche tapa baja redonda', 'estuche tapa baja redonda', NULL, NULL, NULL, NULL, 1.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1355, '130510', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Estuches Plásticos cuadrados implast', 'Estuches Plásticos cuadrados implast', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1356, '130511', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'envase ovalado', 'envase ovalado', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1357, '130512', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Emvase fram 9*9', 'Emvase fram 9*9', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1358, '130513', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, ' 56 Envase Bombonero', ' 56 Envase Bombonero', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1359, '130514', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Contenedor 136 con tapa visual', 'Contenedor 136 con tapa visual', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1360, '130515', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Fim para Galleta 15"', 'Fim para Galleta 15"', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1361, '130601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bandeja de pie aluminio P-23', 'Bandeja de pie aluminio P-23', NULL, NULL, NULL, NULL, 1.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1362, '130602', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Molde  metalico para queque red. mediano', 'Molde  metalico para queque red. mediano', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1363, '130603', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Molde  metalico para queque red. pequeño', 'Molde  metalico para queque red. pequeño', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1364, '130604', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Molde de QUEQUE', 'Molde de QUEQUE', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1365, '130701', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel troquelado  Pequeño 10', 'Papel troquelado  Pequeño 10', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1366, '130702', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel troquelado Mediano 20', 'Papel troquelado Mediano 20', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1367, '130703', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel troquelado Grande  30', 'Papel troquelado Grande  30', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1368, '130704', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Sabana (para  Rollo', 'Papel Sabana (para  Rollo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1369, '130705', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Sabana (resma) DE 100 UNIDADES', 'Papel Sabana (resma) DE 100 UNIDADES', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1370, '130706', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Copia  (pequeño)', 'Papel Copia  (pequeño)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1371, '130707', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Copia  (mediano)', 'Papel Copia  (mediano)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1372, '130708', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Copia  (grande)', 'Papel Copia  (grande)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1373, '130709', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Molde para Queque', 'Papel Molde para Queque', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1374, '130710', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Dorado', 'Papel Dorado', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1375, '130711', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tiras de Cartón', 'Tiras de Cartón', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1376, '130712', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Etiqueta para Galleta  (roja)', 'Etiqueta para Galleta  (roja)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1377, '130713', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Etiqueta para galleta  (verde)', 'Etiqueta para galleta  (verde)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1378, '140101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ace     (Bolsa de 900 grs.)', 'Ace     (Bolsa de 900 grs.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1379, '140102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ace     (Bolsa de 160 grs.)', 'Ace     (Bolsa de 160 grs.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1380, '140103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ace     (PQT. c/u=7 bolsas 2,5 kg)', 'Ace     (PQT. c/u=7 bolsas 2,5 kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1381, '140104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Detergente multiuso"hit"(5L.)', 'Detergente multiuso"hit"(5L.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1382, '140105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jabon en barra', 'Jabon en barra', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1383, '140106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Suavisante ( 4 Lts)', 'Suavisante ( 4 Lts)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1384, '140107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Detergente ( 4Lts)', 'Detergente ( 4Lts)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1385, '140108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Detergente Clorado de(20 l.)', 'Detergente Clorado de(20 l.)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1386, '140109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Lava Vajilla  OLA', 'Lava Vajilla  OLA', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1387, '140110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ola lava Vajilla de (20 l.) AGRANEL', 'Ola lava Vajilla de (20 l.) AGRANEL', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1388, '140111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'JABON LIQUIDO DE 20 LTRS', 'JABON LIQUIDO DE 20 LTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1389, '140201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'DG6', 'DG6', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1390, '140202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectante Igenix(5 KL)', 'Desinfectante Igenix(5 KL)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1391, '140203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectante para inodoro "lord"', 'Desinfectante para inodoro "lord"', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1392, '140204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectante para inodoro"glade" Canasta', 'Desinfectante para inodoro"glade" Canasta', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1393, '140205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectante para inodoro"glade" Repuesto', 'Desinfectante para inodoro"glade" Repuesto', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1394, '140206', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectantes "igenix"(5kg)', 'Desinfectantes "igenix"(5kg)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1395, '140207', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Desinfectantes Lysoform aerosol 360cc', 'Desinfectantes Lysoform aerosol 360cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1396, '140208', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'neo lisolin 1000 ml', 'neo lisolin 1000 ml', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1397, '140209', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sanitizador neutro LIZ', 'Sanitizador neutro LIZ', '5lt/unid', NULL, NULL, NULL, 90.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1398, '140210', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ola Clorito normal  DE 5 LTRS', 'Ola Clorito normal  DE 5 LTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1399, '140301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Aromatizador GLADE aerosol 360cc', 'Aromatizador GLADE aerosol 360cc', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1400, '140302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Aromatizador"glade toque" con dosificador', 'Aromatizador"glade toque" con dosificador', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1401, '140303', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Aromatizador"glade toque" repuesto', 'Aromatizador"glade toque" repuesto', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1402, '140304', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Eco Fresh  Ambientador 5 Lt  LAVANDA', 'Eco Fresh  Ambientador 5 Lt  LAVANDA', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1403, '140305', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Eco Fresh  Floral  5Lt', 'Eco Fresh  Floral  5Lt', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1404, '140401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'LIMPIA VIDRIOS A GRANEL DE 20 LTRS', 'LIMPIA VIDRIOS A GRANEL DE 20 LTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1405, '140402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Limpia vidrios multiusos VIRGINIA(5l)', 'Limpia vidrios multiusos VIRGINIA(5l)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1406, '140501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Sapolio sacagrasa limon(450ml)', 'Sapolio sacagrasa limon(450ml)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1407, '140502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'ANTIGRASA A GRANEL DE 20 LTRS', 'ANTIGRASA A GRANEL DE 20 LTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1408, '140503', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Limpia Horno Aerosol', 'Limpia Horno Aerosol', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1409, '140504', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Antigrasa OLA ', 'Antigrasa OLA ', '5 Lt/unid', NULL, NULL, NULL, 57.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1410, '140601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'ANTISARRO A GRANEL DE 20 LTRS', 'ANTISARRO A GRANEL DE 20 LTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1411, '140602', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, ' Antisarro OLA maximus de 5 Lts.', ' Antisarro OLA maximus de 5 Lts.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1412, '140701', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Lustramuebles"ceramicol"', 'Lustramuebles"ceramicol"', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1413, '140702', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Limpiamuebles"blem" Aerosol 360 cm', 'Limpiamuebles"blem" Aerosol 360 cm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1414, '140801', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cera Liquida de20 Lts "Incol"', 'Cera Liquida de20 Lts "Incol"', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1415, '140802', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cera LORITO SACHETT', 'Cera LORITO SACHETT', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1416, '140803', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Ola Cera al agua Antibrillante', 'Ola Cera al agua Antibrillante', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1417, '140901', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'baygon amarillo aerosol', 'baygon amarillo aerosol', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1418, '140902', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'baygon verde', 'baygon verde', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1419, '140903', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Raid extermina cucaracha', 'Raid extermina cucaracha', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1420, '141001', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Pegamento Ultra Peg', 'Pegamento Ultra Peg', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1421, '141002', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Silicona para auto(spray)', 'Silicona para auto(spray)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1422, '141003', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Silicona Liquida 250 ml', 'Silicona Liquida 250 ml', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1423, '141004', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Talco para pies', 'Talco para pies', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1424, '141101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Alzadores(pala con mango) MEDIANOS', 'Alzadores(pala con mango) MEDIANOS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714');
INSERT INTO `llx_product` (`rowid`, `ref`, `entity`, `ref_ext`, `datec`, `tms`, `virtual`, `fk_parent`, `label`, `description`, `note`, `customcode`, `fk_country`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `tobuy`, `fk_product_type`, `duration`, `seuil_stock_alerte`, `barcode`, `fk_barcode_type`, `accountancy_code_sell`, `accountancy_code_buy`, `partnumber`, `weight`, `weight_units`, `length`, `length_units`, `surface`, `surface_units`, `volume`, `volume_units`, `stock`, `pmp`, `canvas`, `finished`, `hidden`, `import_key`) VALUES
(1425, '141102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Alzadores(pala con mango) GRANDES', 'Alzadores(pala con mango) GRANDES', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1426, '141103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'recogedor de basura pequeño', 'recogedor de basura pequeño', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1427, '141104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'recogedor de basura pequeño con escobilla', 'recogedor de basura pequeño con escobilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1428, '141105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'escoba grande plastico', 'escoba grande plastico', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1429, '141106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'escobas con cerda dura', 'escobas con cerda dura', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1430, '141107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'escobas con cerda suave', 'escobas con cerda suave', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1431, '141108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'escobillas(cepillos)', 'escobillas(cepillos)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1432, '141109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Goma secadora de piso (Grande)"ARAGAN"', 'Goma secadora de piso (Grande)"ARAGAN"', NULL, NULL, NULL, NULL, 18.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1433, '141110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'haragan de goma fanagom', 'haragan de goma fanagom', NULL, NULL, NULL, NULL, 10.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1434, '141111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Dispensador de jabon', 'Dispensador de jabon', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1435, '141112', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Dispensador de rollo papel higienico', 'Dispensador de rollo papel higienico', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1436, '141201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Dispensador de toalla  papel', 'Dispensador de toalla  papel', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1437, '141202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Virutilla', 'Virutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1438, '141203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bombril  DE 8  unid PQTE', 'Bombril  DE 8  unid PQTE', NULL, NULL, NULL, NULL, 5.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1439, '141204', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esponja de Acero', 'Esponja de Acero', NULL, NULL, NULL, NULL, 1.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1440, '141205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Esponja', 'Esponja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1441, '141301', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'esponjas grandes', 'esponjas grandes', '10 unid/paquete', NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1442, '141302', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa de basura normal 65x80cm ', 'Bolsa de basura normal 65x80cm ', '10 unid/paquete', NULL, NULL, NULL, 8.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1443, '141303', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa de basura tipo gigante 90x110 cm', 'Bolsa de basura tipo gigante 90x110 cm', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1444, '141304', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa de mercado', 'Bolsa de mercado', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1445, '141305', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa mediana', 'Bolsa mediana', NULL, NULL, NULL, NULL, 8.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1446, '141306', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa normal Chinita', 'Bolsa normal Chinita', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1447, '141307', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsa pequeña', 'Bolsa pequeña', NULL, NULL, NULL, NULL, 60.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1448, '141308', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Bolsas negras con agarrador', 'Bolsas negras con agarrador', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1449, '141401', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Secadores blancos Grandes', 'Secadores blancos Grandes', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1450, '141402', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Secadores blancos Pequeños', 'Secadores blancos Pequeños', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1451, '141403', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Paños Naranja', 'Paños Naranja', NULL, NULL, NULL, NULL, 4.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1452, '141404', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Trapo para piso  blanco', 'Trapo para piso  blanco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1453, '141405', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Trapo para piso  plomo Ober', 'Trapo para piso  plomo Ober', NULL, NULL, NULL, NULL, 6.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1454, '141406', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cepillo para sanitario', 'Cepillo para sanitario', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1455, '141501', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Papel Folex de 15 x 8', 'Caja Papel Folex de 15 x 8', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1456, '141502', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Papel Folex de 8 x 10', 'Caja Papel Folex de 8 x 10', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1457, '141503', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Higienico  elite', 'Papel Higienico  elite', NULL, NULL, NULL, NULL, 136.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1458, '141504', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Higienico Jumbo', 'Papel Higienico Jumbo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1459, '141505', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel Higienico  scott  ', 'Papel Higienico  scott  ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1460, '141506', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel toalla JUMBO Elite 300 MTRS', 'Papel toalla JUMBO Elite 300 MTRS', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1461, '141507', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel higienico  YUMBO ELITE de550 mts', 'Papel higienico  YUMBO ELITE de550 mts', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1462, '141508', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel toalla para baño JUMBO Scott', 'Papel toalla para baño JUMBO Scott', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1463, '141509', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel toalla para baño JUMBO Scott', 'Papel toalla para baño JUMBO Scott', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1464, '141510', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel higienico JOFER', 'Papel higienico JOFER', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1465, '141511', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel toalla para cocina Scott', 'Papel toalla para cocina Scott', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1466, '141512', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Papel toalla para cocina Elite', 'Papel toalla para cocina Elite', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1467, '141513', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Servilletas elite ', 'Servilletas elite ', NULL, NULL, NULL, NULL, 17.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1468, '141514', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Servilletas elite  DE 4 POR 300', 'Servilletas elite  DE 4 POR 300', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1469, '141601', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jeringas de 10ml', 'Jeringas de 10ml', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1470, '141602', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jeringas de 3ml', 'Jeringas de 3ml', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1471, '141603', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Jeringas de 5ml', 'Jeringas de 5ml', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1472, '141604', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Cofies Para Cebeza', 'Cofies Para Cebeza', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1473, '141605', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Barbijos', 'Barbijos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1474, '141606', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Gorras quirurgicas', 'Gorras quirurgicas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1475, '141607', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes de goma cortas talla 7 1/2', 'Guantes de goma cortas talla 7 1/2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1476, '141608', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes de goma largo talla L', 'Guantes de goma largo talla L', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1477, '141609', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes de goma largo talla M', 'Guantes de goma largo talla M', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1478, '141610', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes de latex talla M', 'Guantes de latex talla M', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1479, '141611', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes Nylon desechables', 'Guantes Nylon desechables', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1480, '141612', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Guantes de trabajo', 'Guantes de trabajo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1481, '150101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Pasas al Ron', 'Helado Pasas al Ron', NULL, NULL, NULL, NULL, 54.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1482, '150102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Vainilla', 'Helado Vainilla', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1483, '150103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Mora a la Crema', 'Helado Mora a la Crema', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1484, '150104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Canela ', 'Helado Canela ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1485, '150105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Kiwi', 'Helado Kiwi', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1486, '150106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Chicle', 'Helado Chicle', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1487, '150107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado coco', 'Helado coco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1488, '150108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado chocolate', 'Helado chocolate', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1489, '150109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Cherry chocolate', 'Helado Cherry chocolate', NULL, NULL, NULL, NULL, 54.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1490, '150110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Mango', 'Helado Mango', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1491, '150111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado de Limón', 'Helado de Limón', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1492, '150112', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Durazno', 'Helado Durazno', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1493, '150113', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Frutilla', 'Helado Frutilla', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1494, '150114', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Granizado', 'Helado Granizado', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1495, '150115', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Menta Chocolate', 'Helado Menta Chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1496, '150116', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado chirimoya Alegre', 'Helado chirimoya Alegre', NULL, NULL, NULL, NULL, 52.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1497, '150201', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Capuchino', 'Helado Capuchino', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1498, '150202', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Tres leches', 'Helado Tres leches', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1499, '150203', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Copa helada', 'Helado Copa helada', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1500, '150204', 1, NULL, '2013-01-01 00:00:00', '2013-04-27 16:01:05', 0, 0, 'Torta Helada de Leche', 'Torta Helada de Leche', NULL, NULL, NULL, NULL, 50.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1501, '150205', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Torta Helada Alemana', 'Torta Helada Alemana', NULL, NULL, NULL, NULL, 50.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1502, '150206', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Torta Helada Selva Negra', 'Torta Helada Selva Negra', NULL, NULL, NULL, NULL, 50.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1503, '150207', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Pasas al Ron 1 Ltr.', 'Helado Pasas al Ron 1 Ltr.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1504, '150208', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado    Chocolate 1 ltr.', 'Helado    Chocolate 1 ltr.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1505, '150209', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado  Frutilla 1 Ltr.', 'Helado  Frutilla 1 Ltr.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1506, '150210', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado  Vainilla 1 Ltr.', 'Helado  Vainilla 1 Ltr.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1507, '150211', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helado Cassata', 'Helado Cassata', NULL, NULL, NULL, NULL, 13.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1508, '150212', 1, NULL, '2013-01-01 00:00:00', '2013-04-11 22:48:20', 0, 0, 'Helados Bambino Frutilla', 'Helados Bambino Frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1509, '150213', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helados Bambino Vainilla', 'Helados Bambino Vainilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1510, '150214', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Helados Bambino Chocolate ', 'Helados Bambino Chocolate ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1511, '160101', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 0', 'vela # 0', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1512, '160102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 1', 'vela # 1', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1513, '160103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 2', 'vela # 2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1514, '160104', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 3', 'vela # 3', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1515, '160105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 4', 'vela # 4', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1516, '160106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 5', 'vela # 5', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1517, '160107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 6', 'vela # 6', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1518, '160108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 7', 'vela # 7', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1519, '160109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 8', 'vela # 8', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1520, '160110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela # 9', 'vela # 9', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1521, '160111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela 15 años', 'vela 15 años', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1522, '160112', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela bengala sobre c/4 velas', 'vela bengala sobre c/4 velas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1523, '160113', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'vela figura', 'vela figura', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1524, '160114', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Velas magicas', 'Velas magicas', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1525, '160115', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Velas magicas grandes', 'Velas magicas grandes', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1526, '160116', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Velas cigarrillo para regalar de 50 c/u', 'Velas cigarrillo para regalar de 50 c/u', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1527, '160117', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'velas cigarrillo para regalar 100 c/c', 'velas cigarrillo para regalar 100 c/c', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1528, '160118', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'velas signo', 'velas signo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1529, '170101', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Caja Grande de Chocolate cherries de 175 grs', 'Caja Grande de Chocolate cherries de 175 grs', NULL, NULL, NULL, NULL, 38.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1530, '170102', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Grande de Chocolate trufas de 140 grs', 'Caja Grande de Chocolate trufas de 140 grs', NULL, NULL, NULL, NULL, 30.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1531, '170103', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Grande de Chocolate Lanuit de 140 grs', 'Caja Grande de Chocolate Lanuit de 140 grs', NULL, NULL, NULL, NULL, 30.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1532, '170104', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Caja Grande de Chocolate frutilla de 140 grs', 'Caja Grande de Chocolate frutilla de 140 grs', NULL, NULL, NULL, NULL, 30.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1533, '170105', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Mediana de chocolate armonia de 120 grs', 'Caja Mediana de chocolate armonia de 120 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1534, '170106', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Mediana de chocolate fiore de 120 grs', 'Caja Mediana de chocolate fiore de 120 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1535, '170107', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Mediana de chocolate cuare de 120 grs', 'Caja Mediana de chocolate cuare de 120 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1536, '170108', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Mediana de chocolate arcadia de 120 grs', 'Caja Mediana de chocolate arcadia de 120 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1537, '170109', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Mediana de Chocolate mystic de 120 grs', 'Caja Mediana de Chocolate mystic de 120 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1538, '170110', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Grande de Chocolate Praline de 140 grs', 'Caja Grande de Chocolate Praline de 140 grs', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1539, '170111', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Caja Piramide deCorazon (120 grs)', 'Caja Piramide deCorazon (120 grs)', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1540, '170112', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Caja de Chocolate Surtidos', 'Caja de Chocolate Surtidos', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1541, '170113', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'Caja Corazones (140 grs)', 'Caja Corazones (140 grs)', NULL, NULL, NULL, NULL, 36.00000000, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1542, '170114', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:23:50', 0, 0, ' Corazones  mediano ', ' Corazones  mediano ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -2, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1543, '170115', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta Blanca de 100 grs.', 'Tableta Blanca de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1544, '170116', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta Cabernet de 100 grs.', 'Tableta Cabernet de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1545, '170117', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta Candy de 100 grs.', 'Tableta Candy de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1546, '170118', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta  semiamarga Bitter  100 grs.', 'Tableta  semiamarga Bitter  100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1547, '170119', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta Bitter menta 100 grs.', 'Tableta Bitter menta 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1548, '170120', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena mora de 100 grs.', 'Tableta rellena mora de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1549, '170121', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena frutilla de 100 grs.', 'Tableta rellena frutilla de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1550, '170122', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena piña de 100 grs. ', 'Tableta rellena piña de 100 grs. ', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1551, '170123', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena naranja de 100 grs.', 'Tableta rellena naranja de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1552, '170124', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena trufa de 100 grs.', 'Tableta rellena trufa de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1553, '170125', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena leche de 100 grs.', 'Tableta rellena leche de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1554, '170126', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena uva de 100 grs.', 'Tableta rellena uva de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1555, '170127', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena limon de 100 grs.', 'Tableta rellena limon de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714');
INSERT INTO `llx_product` (`rowid`, `ref`, `entity`, `ref_ext`, `datec`, `tms`, `virtual`, `fk_parent`, `label`, `description`, `note`, `customcode`, `fk_country`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `tobuy`, `fk_product_type`, `duration`, `seuil_stock_alerte`, `barcode`, `fk_barcode_type`, `accountancy_code_sell`, `accountancy_code_buy`, `partnumber`, `weight`, `weight_units`, `length`, `length_units`, `surface`, `surface_units`, `volume`, `volume_units`, `stock`, `pmp`, `canvas`, `finished`, `hidden`, `import_key`) VALUES
(1556, '170128', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena mani de 100 grs.', 'Tableta rellena mani de 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1557, '170129', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Tableta rellena cherry 100 grs.', 'Tableta rellena cherry 100 grs.', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1558, '170130', 1, NULL, '2013-01-01 00:00:00', '2013-03-23 16:47:14', 0, 0, 'Lata Bombon surtido', 'Lata Bombon surtido', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1559, '170131', 1, NULL, '2013-01-01 00:00:00', '2013-05-25 15:21:08', 0, 0, 'bolsas de bombon surtido', 'bolsas de bombon surtido', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 1, 1, 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -3, 0.00000000, 'default@product', NULL, 0, '20130323124714'),
(1560, 'Galletas_de_prueba', 1, NULL, '2013-04-06 09:53:01', '2013-04-06 14:03:17', 0, 0, 'Galletas', 'Galletas', '', '', NULL, 12.00000000, 12.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0, 0, '', 0, NULL, 0, '', '', NULL, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0.00000000, 'default@product', 1, 0, NULL),
(1699, 'PRM1001', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'batido vainilla', 'Premezcla batido vainilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1700, 'PRM1002', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'batido frutilla', 'Premezcla batido frutilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1701, 'PRM1003', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'batido nuez', 'Premezcla batido nuez', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1702, 'PRM1004', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'batido chocolate', 'Premezcla batido chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1703, 'PRM1005', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'pie', 'Premezcla pie', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1704, 'PRM1006', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'rollo 1', 'Premezcla rollo 1', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1705, 'PRM1007', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'rollo 2', 'Premezcla rollo 2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1706, 'PRM1008', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'rollo 3', 'Premezcla rollo 3', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1707, 'PRM1009', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'queque', 'Premezcla queque', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1708, 'PRM1010', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'empanada 1', 'Premezcla empanada 1', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1709, 'PRM1011', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'empanada 2', 'Premezcla empanada 2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1710, 'PRM1012', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'brazo vainilla', 'Premezcla brazo vainilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1711, 'PRM1013', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'brazo chocolate', 'Premezcla brazo chocolate', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1712, 'PRM1014', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'bomba', 'Premezcla bomba', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1713, 'PRM1015', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta vainilla', 'Premezcla galleta vainilla', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1714, 'PRM1016', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta naranja', 'Premezcla galleta naranja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1715, 'PRM1017', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta coco', 'Premezcla galleta coco', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1716, 'PRM1018', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta cauca', 'Premezcla galleta cauca', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1717, 'PRM1019', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta 2', 'Premezcla galleta 2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1718, 'PRM1020', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'galleta cauca 2', 'Premezcla galleta cauca 2', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1719, 'PRM1021', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'azucar1', 'Premezcla azucar1', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1720, 'PRM2001', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'Hoja', 'Premezcla Hoja', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1721, 'PRM2002', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'empanada', 'Premezcla empanada', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1722, 'PRM2003', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'rollo', 'Premezcla rollo', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1723, 'PRM2004', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'pie', 'Premezcla pie', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1724, 'PRM2005', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'bomba', 'Premezcla bomba', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '120 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1725, 'PRM2006', 1, NULL, '2013-01-01 00:00:00', '2013-04-18 23:50:37', 0, 0, 'queque', 'Premezcla queque', NULL, NULL, NULL, NULL, NULL, 0.00000000, 0.00000000, 'HT', NULL, 0, 0.000, 0.000, 1, 0, 0, 0, '121 DI', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, 'default@product', NULL, 0, '20130418195037'),
(1726, 'torta_de_cumpleanios', 1, NULL, '2013-05-25 12:38:33', '2013-05-25 16:38:33', 0, 0, 'torta de cumpleanios', 'torta de cumpleanios', '', '', NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0, 0, '', NULL, NULL, 0, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00000000, '', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_alternative`
--

CREATE TABLE IF NOT EXISTS `llx_product_alternative` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL,
  `fk_unit` int(11) NOT NULL,
  `qty` double NOT NULL,
  `fk_product_alt` int(11) NOT NULL,
  `fk_unit_alt` int(11) NOT NULL,
  `qty_alt` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_alternative_product_productalt` (`fk_product`,`fk_product_alt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_product_alternative`
--

INSERT INTO `llx_product_alternative` (`rowid`, `fk_product`, `fk_unit`, `qty`, `fk_product_alt`, `fk_unit_alt`, `qty_alt`, `statut`) VALUES
(1, 1126, 4, 1, 1127, 4, 1, 1),
(2, 1127, 4, 1, 1126, 4, 1, 1),
(4, 1263, 4, 1, 1264, 8, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_association`
--

CREATE TABLE IF NOT EXISTS `llx_product_association` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product_pere` int(11) NOT NULL DEFAULT '0',
  `fk_product_fils` int(11) NOT NULL DEFAULT '0',
  `qty` double DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_association` (`fk_product_pere`,`fk_product_fils`),
  KEY `idx_product_association_fils` (`fk_product_fils`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Volcado de datos para la tabla `llx_product_association`
--

INSERT INTO `llx_product_association` (`rowid`, `fk_product_pere`, `fk_product_fils`, `qty`) VALUES
(1, 219, 168, 1),
(2, 216, 38, 1),
(3, 216, 174, 1),
(4, 188, 187, 2),
(5, 188, 181, 1),
(6, 1560, 1114, 16),
(8, 1560, 1126, 8),
(12, 1560, 1270, 0.05),
(15, 43, 1172, 0.2),
(16, 43, 1270, 4),
(17, 43, 1126, 0.2),
(18, 43, 1114, 0.2),
(19, 43, 1116, 0.15),
(20, 50, 1115, 3),
(21, 50, 1270, 10),
(22, 1701, 1126, 1),
(25, 46, 1114, 0.2211),
(26, 46, 1271, 1),
(27, 46, 1274, 1),
(28, 46, 1126, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_product_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_product_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_fournisseur_price`
--

CREATE TABLE IF NOT EXISTS `llx_product_fournisseur_price` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `ref_fourn` varchar(30) DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `quantity` double DEFAULT NULL,
  `remise_percent` double NOT NULL DEFAULT '0',
  `remise` double NOT NULL DEFAULT '0',
  `unitprice` double(24,8) DEFAULT '0.00000000',
  `charges` double(24,8) DEFAULT '0.00000000',
  `unitcharges` double(24,8) DEFAULT '0.00000000',
  `tva_tx` double(6,3) NOT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_fournisseur_price_ref` (`ref_fourn`,`fk_soc`,`quantity`,`entity`),
  KEY `idx_product_fournisseur_price_fk_user` (`fk_user`),
  KEY `idx_product_fourn_price_fk_product` (`fk_product`,`entity`),
  KEY `idx_product_fourn_price_fk_soc` (`fk_soc`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `llx_product_fournisseur_price`
--

INSERT INTO `llx_product_fournisseur_price` (`rowid`, `entity`, `datec`, `tms`, `fk_product`, `fk_soc`, `ref_fourn`, `fk_availability`, `price`, `quantity`, `remise_percent`, `remise`, `unitprice`, `charges`, `unitcharges`, `tva_tx`, `fk_user`, `import_key`) VALUES
(1, 1, '2013-04-06 12:45:26', '2013-04-06 16:45:26', 1253, 5, '897', 0, 88.49558000, 1, 0, 0, 88.49558000, 0.00000000, 0.00000000, 13.000, 1, NULL),
(2, 1, '2013-04-11 18:51:00', '2013-04-11 22:51:00', 1271, 5, 'adsa', 0, 3.00000000, 1, 0, 0, 3.00000000, 0.00000000, 0.00000000, 13.000, 1, NULL),
(3, 1, '2013-04-15 18:49:16', '2013-04-15 22:49:16', 1114, 5, '35', 0, 184.00000000, 1, 0, 0, 184.00000000, 0.00000000, 0.00000000, 13.000, 3, NULL),
(4, 1, '2013-04-18 20:01:40', '2013-04-19 00:01:40', 1114, 7, 'har321', 0, 150.00000000, 1, 0, 0, 150.00000000, 0.00000000, 0.00000000, 13.000, 1, NULL),
(5, 1, '2013-06-01 12:21:10', '2013-06-01 16:21:10', 1115, 47, 'harina', 0, 25.00000000, 1, 0, 0, 25.00000000, 0.00000000, 0.00000000, 13.000, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_fournisseur_price_log`
--

CREATE TABLE IF NOT EXISTS `llx_product_fournisseur_price_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `fk_product_fournisseur` int(11) NOT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `quantity` double DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_lang`
--

CREATE TABLE IF NOT EXISTS `llx_product_lang` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL,
  `description` text,
  `note` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_lang` (`fk_product`,`lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_product_lang`
--

INSERT INTO `llx_product_lang` (`rowid`, `fk_product`, `lang`, `label`, `description`, `note`) VALUES
(1, 1726, 'es_ES', 'torta de cumpleanios', 'torta de cumpleanios', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_list`
--

CREATE TABLE IF NOT EXISTS `llx_product_list` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_product_father` int(11) NOT NULL,
  `fk_unit_father` int(11) NOT NULL,
  `fk_product_son` int(11) NOT NULL,
  `fk_unit_son` int(11) NOT NULL,
  `qty_father` double NOT NULL,
  `qty_son` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_list_fk_product_father_son` (`fk_product_father`,`fk_product_son`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='lista materiales' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_product_list`
--

INSERT INTO `llx_product_list` (`rowid`, `entity`, `fk_product_father`, `fk_unit_father`, `fk_product_son`, `fk_unit_son`, `qty_father`, `qty_son`, `statut`) VALUES
(1, 1, 43, 6, 1114, 4, 1, 1, 1),
(2, 1, 43, 6, 1127, 4, 1, 0.5, 1),
(3, 1, 48, 6, 1178, 4, 1, 0.2, 1),
(4, 1, 43, 6, 1263, 8, 1, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_price`
--

CREATE TABLE IF NOT EXISTS `llx_product_price` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) NOT NULL,
  `date_price` datetime NOT NULL,
  `price_level` smallint(6) DEFAULT '1',
  `price` double(24,8) DEFAULT NULL,
  `price_ttc` double(24,8) DEFAULT NULL,
  `price_min` double(24,8) DEFAULT NULL,
  `price_min_ttc` double(24,8) DEFAULT NULL,
  `price_base_type` varchar(3) DEFAULT 'HT',
  `tva_tx` double(6,3) NOT NULL,
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `fk_user_author` int(11) DEFAULT NULL,
  `tosell` tinyint(4) DEFAULT '1',
  `price_by_qty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Volcado de datos para la tabla `llx_product_price`
--

INSERT INTO `llx_product_price` (`rowid`, `entity`, `tms`, `fk_product`, `date_price`, `price_level`, `price`, `price_ttc`, `price_min`, `price_min_ttc`, `price_base_type`, `tva_tx`, `recuperableonly`, `localtax1_tx`, `localtax2_tx`, `fk_user_author`, `tosell`, `price_by_qty`) VALUES
(1, 1, '2013-02-28 23:59:20', 1, '2013-02-28 19:59:20', 1, 4.00000000, 4.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(2, 1, '2013-03-02 14:18:13', 29, '2013-03-02 10:18:13', 1, 100.00000000, 100.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(3, 1, '2013-03-02 14:18:28', 29, '2013-03-02 10:18:28', 1, 100.00000000, 100.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(4, 1, '2013-03-02 15:29:44', 33, '2013-03-02 11:29:44', 1, 15.00000000, 15.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(5, 1, '2013-03-02 16:26:49', 39, '2013-03-02 12:26:49', 1, 18.00000000, 18.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(6, 1, '2013-03-02 17:19:33', 206, '2013-03-02 13:19:33', 1, 8.00000000, 9.04000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 1, 0),
(7, 1, '2013-03-02 20:05:53', 31, '2013-03-02 16:05:53', 1, 300.00000000, 300.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(8, 1, '2013-03-16 14:24:18', 173, '2013-03-16 10:24:18', 1, 10.00000000, 11.00000000, 0.00000000, 0.00000000, 'HT', 10.000, 0, 0.000, 0.000, 1, 1, 0),
(9, 1, '2013-03-16 14:25:17', 216, '2013-03-16 10:25:17', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(10, 1, '2013-03-16 14:39:09', 168, '2013-03-16 10:39:09', 1, 6.00000000, 6.36000000, 0.00000000, 0.00000000, 'HT', 6.000, 0, 0.000, 0.000, 1, 1, 0),
(11, 1, '2013-03-16 15:21:52', 218, '2013-03-16 11:21:52', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(12, 1, '2013-03-16 15:23:14', 219, '2013-03-16 11:23:14', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(13, 1, '2013-03-16 15:46:02', 220, '2013-03-16 11:46:02', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(14, 1, '2013-03-16 20:59:56', 221, '2013-03-16 16:59:56', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(15, 1, '2013-04-06 13:53:01', 1560, '2013-04-06 09:53:01', 1, 12.00000000, 12.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(16, 1, '2013-04-11 22:49:42', 1271, '2013-04-11 18:49:42', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(17, 1, '2013-04-13 16:45:50', 1127, '2013-04-13 12:45:50', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(18, 1, '2013-04-15 23:31:07', 1116, '2013-04-15 19:31:07', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 3, 0, 0),
(19, 1, '2013-05-25 16:38:33', 1726, '2013-05-25 12:38:33', 1, 0.00000000, 0.00000000, 0.00000000, 0.00000000, '', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(20, 1, '2013-06-01 14:45:47', 29, '2013-06-01 10:45:47', 1, 100.00000000, 100.00000000, 100.00000000, 100.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 1, 0),
(21, 1, '2013-06-01 14:49:31', 1114, '2013-06-01 10:49:31', 1, 231.00000000, 231.00000000, 0.00000000, 0.00000000, 'HT', 0.000, 0, 0.000, 0.000, 1, 0, 0),
(22, 1, '2013-06-01 14:50:25', 1114, '2013-06-01 10:50:25', 1, 231.00000000, 261.03000000, 0.00000000, 0.00000000, 'HT', 13.000, 0, 0.000, 0.000, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_price_by_qty`
--

CREATE TABLE IF NOT EXISTS `llx_product_price_by_qty` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product_price` int(11) NOT NULL,
  `date_price` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `price` double(24,8) DEFAULT '0.00000000',
  `price_ttc` double(24,8) DEFAULT '0.00000000',
  `remise_percent` double NOT NULL DEFAULT '0',
  `remise` double NOT NULL DEFAULT '0',
  `qty_min` double DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_price_by_qty_level` (`fk_product_price`,`qty_min`),
  KEY `idx_product_price_by_qty_fk_product_price` (`fk_product_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_product_stock`
--

CREATE TABLE IF NOT EXISTS `llx_product_stock` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `reel` double DEFAULT NULL,
  `pmp` double(24,8) NOT NULL DEFAULT '0.00000000',
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_stock` (`fk_product`,`fk_entrepot`),
  KEY `idx_product_stock_fk_product` (`fk_product`),
  KEY `idx_product_stock_fk_entrepot` (`fk_entrepot`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=138 ;

--
-- Volcado de datos para la tabla `llx_product_stock`
--

INSERT INTO `llx_product_stock` (`rowid`, `tms`, `fk_product`, `fk_entrepot`, `reel`, `pmp`, `import_key`) VALUES
(1, '2013-03-02 19:48:49', 171, 1, -1, 0.00000000, NULL),
(2, '2013-03-09 16:00:52', 207, 1, -38, 0.00000000, NULL),
(3, '2013-03-04 20:25:49', 39, 1, -15, 0.00000000, NULL),
(4, '2013-04-11 01:29:21', 29, 1, -70, 0.00000000, NULL),
(5, '2013-03-09 16:00:52', 164, 1, -12, 0.00000000, NULL),
(6, '2013-04-13 14:54:13', 37, 1, -19, 0.00000000, NULL),
(7, '2013-03-09 16:00:52', 38, 1, -8, 0.00000000, NULL),
(8, '2013-03-09 16:00:52', 184, 1, -6, 0.00000000, NULL),
(9, '2013-04-13 14:57:11', 1, 1, -20, 0.00000000, NULL),
(10, '2013-04-13 14:57:30', 50, 1, -70, 0.00000000, NULL),
(11, '2013-03-09 16:00:52', 47, 1, -16, 0.00000000, NULL),
(12, '2013-03-06 02:27:23', 40, 1, -1, 0.00000000, NULL),
(13, '2013-03-06 02:27:23', 177, 1, -1, 0.00000000, NULL),
(14, '2013-04-13 14:54:13', 213, 1, -2, 0.00000000, NULL),
(15, '2013-03-06 02:27:23', 34, 1, -3, 0.00000000, NULL),
(16, '2013-03-06 02:27:23', 167, 1, -1, 0.00000000, NULL),
(17, '2013-03-06 03:16:24', 1, 9, -10, 0.00000000, NULL),
(18, '2013-03-16 16:10:56', 168, 1, 900, 10.00000000, NULL),
(19, '2013-03-16 16:10:33', 168, 3, 100, 0.00000000, NULL),
(20, '2013-04-06 17:05:40', 1270, 9, -10, 0.00000000, NULL),
(21, '2013-04-06 17:05:40', 1126, 9, -1600, 0.00000000, NULL),
(22, '2013-04-06 17:05:40', 1114, 9, -3200, 0.00000000, NULL),
(23, '2013-04-06 17:04:39', 1253, 2, 100, 88.49558000, NULL),
(24, '2013-04-15 23:18:26', 43, 1, 100, 0.00000000, NULL),
(25, '2013-04-11 22:48:20', 1508, 1, -100, 0.00000000, NULL),
(26, '2013-04-11 22:48:20', 1508, 3, 100, 0.00000000, NULL),
(27, '2013-04-15 23:18:26', 43, 3, 0, 0.00000000, NULL),
(28, '2013-04-11 22:48:20', 1271, 1, -80, 0.00000000, NULL),
(29, '2013-04-20 13:49:44', 1271, 3, 30, 0.00000000, NULL),
(30, '2013-05-25 13:00:21', 37, 6, -9, 0.00000000, NULL),
(31, '2013-05-25 13:00:53', 29, 6, -13, 0.00000000, NULL),
(32, '2013-05-25 13:00:53', 47, 6, 7, 0.00000000, NULL),
(33, '2013-05-25 13:07:30', 39, 6, -6, 0.00000000, NULL),
(34, '2013-04-15 23:18:26', 1114, 3, -10, 0.00000000, NULL),
(35, '2013-04-15 23:18:26', 1116, 3, -7.5, 0.00000000, NULL),
(36, '2013-04-15 23:18:26', 1126, 3, -10, 0.00000000, NULL),
(37, '2013-04-15 23:18:26', 1172, 3, -10, 0.00000000, NULL),
(38, '2013-05-04 16:08:38', 1270, 3, -230, 0.00000000, NULL),
(39, '2013-04-15 23:17:59', 43, 4, 0, 0.00000000, NULL),
(40, '2013-04-15 23:17:59', 1114, 4, 0, 0.00000000, NULL),
(41, '2013-04-15 23:17:59', 1116, 4, 0, 0.00000000, NULL),
(42, '2013-04-15 23:17:59', 1126, 4, 0, 0.00000000, NULL),
(43, '2013-04-15 23:17:59', 1172, 4, 0, 0.00000000, NULL),
(44, '2013-04-15 23:17:59', 1270, 4, 0, 0.00000000, NULL),
(45, '2013-06-01 20:17:48', 33, 11, 31, 0.00000000, NULL),
(46, '2013-04-13 16:09:16', 33, 1, 37, 0.00000000, NULL),
(47, '2013-06-01 13:40:39', 1114, 13, -18422.411, 150.00000000, NULL),
(48, '2013-06-01 13:40:38', 1270, 13, -104, 0.00000000, NULL),
(49, '2013-06-01 13:40:38', 1172, 13, -2.2, 0.00000000, NULL),
(50, '2013-06-01 13:40:39', 1126, 13, -9702.2, 0.00000000, NULL),
(51, '2013-06-01 13:40:39', 1116, 13, -1.65, 0.00000000, NULL),
(52, '2013-05-25 16:30:50', 43, 11, 10, 0.00000000, NULL),
(53, '2013-04-13 17:09:49', 1114, 11, 0.2, 0.00000000, NULL),
(54, '2013-04-13 17:09:49', 1116, 11, 0.15, 0.00000000, NULL),
(55, '2013-04-13 17:09:49', 1126, 11, 0.2, 0.00000000, NULL),
(56, '2013-04-13 17:09:49', 1172, 11, 0.2, 0.00000000, NULL),
(57, '2013-05-18 17:25:25', 1270, 11, -26, 0.00000000, NULL),
(58, '2013-05-25 13:07:30', 33, 6, 4, 0.00000000, NULL),
(59, '2013-05-18 17:27:21', 50, 6, -13, 0.00000000, NULL),
(60, '2013-04-13 18:11:07', 177, 6, -1, 0.00000000, NULL),
(61, '2013-04-27 14:35:13', 1119, 13, 2, 0.00000000, NULL),
(62, '2013-04-15 23:18:26', 1114, 1, 10, 0.00000000, NULL),
(63, '2013-04-15 23:18:26', 1116, 1, 7.5, 0.00000000, NULL),
(64, '2013-04-15 23:18:26', 1126, 1, 10, 0.00000000, NULL),
(65, '2013-04-15 23:18:26', 1172, 1, 10, 0.00000000, NULL),
(66, '2013-04-15 23:18:26', 1270, 1, 200, 0.00000000, NULL),
(67, '2013-04-15 23:26:59', 38, 5, -3, 0.00000000, NULL),
(68, '2013-04-15 23:26:59', 50, 5, -20, 0.00000000, NULL),
(69, '2013-04-15 23:26:59', 1, 5, -1, 0.00000000, NULL),
(70, '2013-04-19 19:00:59', 1114, 2, 200, 150.00000000, NULL),
(71, '2013-04-27 14:34:20', 1199, 13, 10, 2.00000000, NULL),
(72, '2013-04-27 16:01:05', 1500, 3, -100, 0.00000000, NULL),
(73, '2013-04-27 16:01:05', 1500, 4, 100, 0.00000000, NULL),
(74, '2013-04-27 16:01:05', 206, 3, -10, 0.00000000, NULL),
(75, '2013-04-27 16:01:05', 206, 4, 10, 0.00000000, NULL),
(76, '2013-04-27 20:25:33', 181, 13, 3, 0.00000000, NULL),
(77, '2013-04-27 20:24:13', 187, 13, -4, 0.00000000, NULL),
(78, '2013-05-15 00:11:15', 1271, 13, -11, 0.00000000, NULL),
(79, '2013-04-27 20:25:33', 181, 1, 5, 0.00000000, NULL),
(80, '2013-05-04 13:49:22', 207, 6, -2, 0.00000000, NULL),
(81, '2013-05-04 13:41:14', 184, 6, -2, 0.00000000, NULL),
(82, '2013-05-18 17:27:21', 1115, 6, -36, 0.00000000, NULL),
(83, '2013-05-18 17:27:21', 1270, 6, -120, 0.00000000, NULL),
(84, '2013-05-04 13:52:37', 164, 6, -9, 0.00000000, NULL),
(85, '2013-05-04 16:08:38', 184, 3, -7, 0.00000000, NULL),
(86, '2013-05-04 16:08:38', 29, 3, -14, 0.00000000, NULL),
(87, '2013-05-04 16:08:38', 39, 3, -5, 0.00000000, NULL),
(88, '2013-05-04 16:08:38', 37, 3, -404, 0.00000000, NULL),
(89, '2013-05-04 16:08:38', 47, 3, -6, 0.00000000, NULL),
(90, '2013-05-04 16:08:38', 33, 3, -8, 0.00000000, NULL),
(91, '2013-05-04 16:08:38', 164, 3, -5, 0.00000000, NULL),
(92, '2013-05-04 16:08:38', 50, 3, -3, 0.00000000, NULL),
(93, '2013-05-04 16:08:38', 1115, 3, -9, 0.00000000, NULL),
(94, '2013-05-04 16:08:38', 207, 3, -5, 0.00000000, NULL),
(95, '2013-05-04 15:22:07', 171, 3, -2, 0.00000000, NULL),
(96, '2013-05-04 16:08:38', 38, 3, -12, 0.00000000, NULL),
(97, '2013-05-04 16:08:38', 177, 3, -4, 0.00000000, NULL),
(98, '2013-05-04 16:08:38', 1, 3, -2, 0.00000000, NULL),
(99, '2013-05-04 16:08:38', 213, 3, -2, 0.00000000, NULL),
(100, '2013-05-04 16:08:38', 34, 3, -1, 0.00000000, NULL),
(101, '2013-05-04 16:08:38', 1542, 3, -1, 0.00000000, NULL),
(102, '2013-05-04 16:08:38', 215, 3, -1, 0.00000000, NULL),
(103, '2013-05-04 16:08:38', 1559, 3, -1, 0.00000000, NULL),
(104, '2013-05-18 18:41:59', 37, 11, -6, 0.00000000, NULL),
(105, '2013-05-05 00:25:28', 213, 11, -1, 0.00000000, NULL),
(106, '2013-05-18 17:25:25', 177, 11, -8, 0.00000000, NULL),
(107, '2013-05-05 00:25:28', 38, 11, -20, 0.00000000, NULL),
(108, '2013-05-05 00:38:03', 34, 11, -3, 0.00000000, NULL),
(109, '2013-05-18 18:41:59', 39, 11, -5, 0.00000000, NULL),
(110, '2013-05-05 00:38:03', 164, 11, -21, 0.00000000, NULL),
(111, '2013-05-18 18:41:59', 47, 11, -18, 0.00000000, NULL),
(112, '2013-05-05 00:38:03', 184, 11, -1, 0.00000000, NULL),
(113, '2013-05-05 00:38:03', 1, 11, -1, 0.00000000, NULL),
(114, '2013-05-25 15:21:08', 49, 11, -21, 0.00000000, NULL),
(115, '2013-05-05 00:42:29', 49, 6, 20, 0.00000000, NULL),
(116, '2013-05-15 00:11:14', 168, 13, -100, 0.00000000, NULL),
(117, '2013-05-15 00:11:15', 1274, 13, -10, 0.00000000, NULL),
(118, '2013-05-18 17:25:25', 50, 11, -3, 0.00000000, NULL),
(119, '2013-05-18 17:25:25', 1115, 11, -9, 0.00000000, NULL),
(120, '2013-05-18 17:25:25', 207, 11, -3, 0.00000000, NULL),
(121, '2013-06-01 20:17:48', 29, 11, -2, 0.00000000, NULL),
(122, '2013-05-18 19:01:39', 1316, 13, -10, 0.00000000, NULL),
(123, '2013-05-25 13:07:30', 1, 6, -2, 0.00000000, NULL),
(124, '2013-05-25 13:16:14', 34, 6, -5, 0.00000000, NULL),
(125, '2013-05-25 13:41:27', 212, 11, -1, 0.00000000, NULL),
(126, '2013-05-25 15:21:08', 1559, 11, -2, 0.00000000, NULL),
(127, '2013-05-25 15:21:08', 215, 11, -2, 0.00000000, NULL),
(128, '2013-06-01 13:40:38', 1263, 13, -200, 0.00000000, NULL),
(129, '2013-06-01 13:40:38', 1127, 13, -10, 0.00000000, NULL),
(130, '2013-05-25 15:21:08', 1532, 11, -1, 0.00000000, NULL),
(131, '2013-05-25 15:21:08', 1529, 11, -1, 0.00000000, NULL),
(132, '2013-05-25 15:21:08', 1540, 11, -1, 0.00000000, NULL),
(133, '2013-05-25 15:21:08', 1541, 11, -1, 0.00000000, NULL),
(134, '2013-05-25 15:21:08', 173, 11, -1, 0.00000000, NULL),
(135, '2013-05-25 15:23:50', 1542, 11, -1, 0.00000000, NULL),
(136, '2013-06-01 16:22:26', 1115, 13, 30, 25.00000000, NULL),
(137, '2013-06-01 20:05:55', 168, 11, -4, 0.00000000, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_projet`
--

CREATE TABLE IF NOT EXISTS `llx_projet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `datec` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dateo` date DEFAULT NULL,
  `datee` date DEFAULT NULL,
  `ref` varchar(50) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `description` text,
  `fk_user_creat` int(11) NOT NULL,
  `public` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `note_private` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_projet_ref` (`ref`,`entity`),
  KEY `idx_projet_fk_soc` (`fk_soc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_projet_task`
--

CREATE TABLE IF NOT EXISTS `llx_projet_task` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_projet` int(11) NOT NULL,
  `fk_task_parent` int(11) NOT NULL DEFAULT '0',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dateo` datetime DEFAULT NULL,
  `datee` datetime DEFAULT NULL,
  `datev` datetime DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `description` text,
  `duration_effective` double NOT NULL DEFAULT '0',
  `progress` int(11) DEFAULT '0',
  `priority` int(11) DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `note_private` text,
  `note_public` text,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_projet_task_fk_projet` (`fk_projet`),
  KEY `idx_projet_task_fk_user_creat` (`fk_user_creat`),
  KEY `idx_projet_task_fk_user_valid` (`fk_user_valid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_projet_task_time`
--

CREATE TABLE IF NOT EXISTS `llx_projet_task_time` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_task` int(11) NOT NULL,
  `task_date` date DEFAULT NULL,
  `task_duration` double DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_propal`
--

CREATE TABLE IF NOT EXISTS `llx_propal` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) DEFAULT NULL,
  `ref_int` varchar(255) DEFAULT NULL,
  `ref_client` varchar(255) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datep` date DEFAULT NULL,
  `fin_validite` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `price` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `note` text,
  `note_public` text,
  `model_pdf` varchar(255) DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `fk_input_reason` int(11) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `extraparams` varchar(255) DEFAULT NULL,
  `fk_adresse_livraison` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_propal_ref` (`ref`,`entity`),
  KEY `idx_propal_fk_soc` (`fk_soc`),
  KEY `idx_propal_fk_user_author` (`fk_user_author`),
  KEY `idx_propal_fk_user_valid` (`fk_user_valid`),
  KEY `idx_propal_fk_user_cloture` (`fk_user_cloture`),
  KEY `idx_propal_fk_projet` (`fk_projet`),
  KEY `idx_propal_fk_account` (`fk_account`),
  KEY `idx_propal_fk_currency` (`fk_currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_propal`
--

INSERT INTO `llx_propal` (`rowid`, `ref`, `entity`, `ref_ext`, `ref_int`, `ref_client`, `fk_soc`, `fk_projet`, `tms`, `datec`, `datep`, `fin_validite`, `date_valid`, `date_cloture`, `fk_user_author`, `fk_user_valid`, `fk_user_cloture`, `fk_statut`, `price`, `remise_percent`, `remise_absolue`, `remise`, `total_ht`, `tva`, `localtax1`, `localtax2`, `total`, `fk_account`, `fk_currency`, `fk_cond_reglement`, `fk_mode_reglement`, `note`, `note_public`, `model_pdf`, `date_livraison`, `fk_availability`, `fk_input_reason`, `import_key`, `extraparams`, `fk_adresse_livraison`) VALUES
(1, 'PR1303-0001', 1, NULL, NULL, 'asdasd', 3, NULL, '2013-03-23 13:56:49', '2013-03-23 09:48:27', '2013-03-23', '2013-04-07 12:00:00', '2013-03-23 09:56:49', NULL, 1, 1, NULL, 1, 0, NULL, NULL, 0, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, NULL, NULL, 1, 0, '', '', 'azur', NULL, 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_propaldet`
--

CREATE TABLE IF NOT EXISTS `llx_propaldet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_propal` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `fk_remise_except` int(11) DEFAULT NULL,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax1_type` varchar(1) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `localtax2_type` varchar(1) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `price` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `fk_product_fournisseur_price` int(11) DEFAULT NULL,
  `buy_price_ht` double(24,8) DEFAULT '0.00000000',
  `special_code` int(11) DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_propaldet_fk_propal` (`fk_propal`),
  KEY `idx_propaldet_fk_product` (`fk_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `llx_propaldet`
--

INSERT INTO `llx_propaldet` (`rowid`, `fk_propal`, `fk_parent_line`, `fk_product`, `label`, `description`, `fk_remise_except`, `tva_tx`, `localtax1_tx`, `localtax1_type`, `localtax2_tx`, `localtax2_type`, `qty`, `remise_percent`, `remise`, `price`, `subprice`, `total_ht`, `total_tva`, `total_localtax1`, `total_localtax2`, `total_ttc`, `product_type`, `date_start`, `date_end`, `info_bits`, `fk_product_fournisseur_price`, `buy_price_ht`, `special_code`, `rang`) VALUES
(1, 1, NULL, 43, NULL, 'Torta Mediana', NULL, 0.000, 0.000, NULL, 0.000, NULL, 2000, 0, 0, NULL, NULL, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0.00000000, 0, NULL, NULL, 0, 0, 0.00000000, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_rights_def`
--

CREATE TABLE IF NOT EXISTS `llx_rights_def` (
  `id` int(11) NOT NULL DEFAULT '0',
  `libelle` varchar(255) DEFAULT NULL,
  `module` varchar(64) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `perms` varchar(50) DEFAULT NULL,
  `subperms` varchar(50) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `bydefault` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_rights_def`
--

INSERT INTO `llx_rights_def` (`id`, `libelle`, `module`, `entity`, `perms`, `subperms`, `type`, `bydefault`) VALUES
(11, 'Lire les factures', 'facture', 1, 'lire', NULL, 'a', 1),
(12, 'Creer/modifier les factures', 'facture', 1, 'creer', NULL, 'a', 0),
(13, 'Dévalider les factures', 'facture', 1, 'invoice_advance', 'unvalidate', 'a', 0),
(14, 'Valider les factures', 'facture', 1, 'valider', NULL, 'a', 0),
(15, 'Envoyer les factures par mail', 'facture', 1, 'invoice_advance', 'send', 'a', 0),
(16, 'Emettre des paiements sur les factures', 'facture', 1, 'paiement', NULL, 'a', 0),
(19, 'Supprimer les factures', 'facture', 1, 'supprimer', NULL, 'a', 0),
(21, 'Lire les propositions commerciales', 'propale', 1, 'lire', NULL, 'r', 1),
(22, 'Creer/modifier les propositions commerciales', 'propale', 1, 'creer', NULL, 'w', 0),
(24, 'Valider les propositions commerciales', 'propale', 1, 'valider', NULL, 'd', 0),
(25, 'Envoyer les propositions commerciales aux clients', 'propale', 1, 'propal_advance', 'send', 'd', 0),
(26, 'Cloturer les propositions commerciales', 'propale', 1, 'cloturer', NULL, 'd', 0),
(27, 'Supprimer les propositions commerciales', 'propale', 1, 'supprimer', NULL, 'd', 0),
(28, 'Exporter les propositions commerciales et attributs', 'propale', 1, 'export', NULL, 'r', 0),
(31, 'Lire les produits', 'produit', 1, 'lire', NULL, 'r', 1),
(32, 'Creer/modifier les produits', 'produit', 1, 'creer', NULL, 'w', 0),
(34, 'Supprimer les produits', 'produit', 1, 'supprimer', NULL, 'd', 0),
(38, 'Exporter les produits', 'produit', 1, 'export', NULL, 'r', 0),
(41, 'Lire les projets et taches (partagés ou dont je suis contact)', 'projet', 1, 'lire', NULL, 'r', 1),
(42, 'Creer/modifier les projets et taches (partagés ou dont je suis contact)', 'projet', 1, 'creer', NULL, 'w', 0),
(44, 'Supprimer les projets et taches (partagés ou dont je suis contact)', 'projet', 1, 'supprimer', NULL, 'd', 0),
(61, 'Lire les fiches d''intervention', 'ficheinter', 1, 'lire', NULL, 'r', 1),
(62, 'Creer/modifier les fiches d''intervention', 'ficheinter', 1, 'creer', NULL, 'w', 0),
(64, 'Supprimer les fiches d''intervention', 'ficheinter', 1, 'supprimer', NULL, 'd', 0),
(67, 'Exporter les fiches interventions', 'ficheinter', 1, 'export', NULL, 'r', 0),
(68, 'Envoyer les fiches d''intervention par courriel', 'ficheinter', 1, 'ficheinter_advance', 'send', 'r', 0),
(71, 'Read members'' card', 'adherent', 1, 'lire', NULL, 'r', 1),
(72, 'Create/modify members (need also user module permissions if member linked to a user)', 'adherent', 1, 'creer', NULL, 'w', 0),
(74, 'Remove members', 'adherent', 1, 'supprimer', NULL, 'd', 0),
(75, 'Setup types and attributes of members', 'adherent', 1, 'configurer', NULL, 'w', 0),
(76, 'Export members', 'adherent', 1, 'export', NULL, 'r', 0),
(78, 'Read subscriptions', 'adherent', 1, 'cotisation', 'lire', 'r', 1),
(79, 'Create/modify/remove subscriptions', 'adherent', 1, 'cotisation', 'creer', 'w', 0),
(81, 'Lire les commandes clients', 'commande', 1, 'lire', NULL, 'r', 1),
(82, 'Creer/modifier les commandes clients', 'commande', 1, 'creer', NULL, 'w', 0),
(84, 'Valider les commandes clients', 'commande', 1, 'valider', NULL, 'd', 0),
(86, 'Envoyer les commandes clients', 'commande', 1, 'order_advance', 'send', 'd', 0),
(87, 'Cloturer les commandes clients', 'commande', 1, 'cloturer', NULL, 'd', 0),
(88, 'Annuler les commandes clients', 'commande', 1, 'annuler', NULL, 'd', 0),
(89, 'Supprimer les commandes clients', 'commande', 1, 'supprimer', NULL, 'd', 0),
(91, 'Lire les charges', 'tax', 1, 'charges', 'lire', 'r', 1),
(92, 'Creer/modifier les charges', 'tax', 1, 'charges', 'creer', 'w', 0),
(93, 'Supprimer les charges', 'tax', 1, 'charges', 'supprimer', 'd', 0),
(94, 'Exporter les charges', 'tax', 1, 'charges', 'export', 'r', 0),
(95, 'Lire CA, bilans, resultats', 'compta', 1, 'resultat', 'lire', 'r', 1),
(96, 'Parametrer la ventilation', 'compta', 1, 'ventilation', 'parametrer', 'r', 0),
(97, 'Lire les ventilations de factures', 'compta', 1, 'ventilation', 'lire', 'r', 1),
(98, 'Ventiler les lignes de factures', 'compta', 1, 'ventilation', 'creer', 'r', 0),
(101, 'Lire les expeditions', 'expedition', 1, 'lire', NULL, 'r', 1),
(102, 'Creer modifier les expeditions', 'expedition', 1, 'creer', NULL, 'w', 0),
(104, 'Valider les expeditions', 'expedition', 1, 'valider', NULL, 'd', 0),
(105, 'Envoyer les expeditions aux clients', 'expedition', 1, 'shipping_advance', 'send', 'd', 0),
(106, 'Exporter les expeditions', 'expedition', 1, 'shipment', 'export', 'r', 0),
(109, 'Supprimer les expeditions', 'expedition', 1, 'supprimer', NULL, 'd', 0),
(111, 'Lire les comptes bancaires', 'banque', 1, 'lire', NULL, 'r', 1),
(112, 'Creer/modifier montant/supprimer ecriture bancaire', 'banque', 1, 'modifier', NULL, 'w', 0),
(113, 'Configurer les comptes bancaires (creer, gerer categories)', 'banque', 1, 'configurer', NULL, 'a', 0),
(114, 'Rapprocher les ecritures bancaires', 'banque', 1, 'consolidate', NULL, 'w', 0),
(115, 'Exporter transactions et releves', 'banque', 1, 'export', NULL, 'r', 0),
(116, 'Virements entre comptes', 'banque', 1, 'transfer', NULL, 'w', 0),
(117, 'Gerer les envois de cheques', 'banque', 1, 'cheque', NULL, 'w', 0),
(121, 'Lire les societes', 'societe', 1, 'lire', NULL, 'r', 1),
(122, 'Creer modifier les societes', 'societe', 1, 'creer', NULL, 'w', 0),
(125, 'Supprimer les societes', 'societe', 1, 'supprimer', NULL, 'd', 0),
(126, 'Exporter les societes', 'societe', 1, 'export', NULL, 'r', 0),
(141, 'Lire tous les projets et taches (y compris prives qui ne me sont pas affectes)', 'projet', 1, 'all', 'lire', 'r', 0),
(142, 'Creer/modifier tous les projets et taches (y compris prives qui ne me sont pas affectes)', 'projet', 1, 'all', 'creer', 'w', 0),
(144, 'Supprimer tous les projets et taches (y compris prives qui ne me sont pas affectes)', 'projet', 1, 'all', 'supprimer', 'd', 0),
(171, 'Lire les deplacements', 'deplacement', 1, 'lire', NULL, 'r', 1),
(172, 'Creer/modifier les deplacements', 'deplacement', 1, 'creer', NULL, 'w', 0),
(173, 'Supprimer les deplacements', 'deplacement', 1, 'supprimer', NULL, 'd', 0),
(178, 'Exporter les deplacements', 'deplacement', 1, 'export', NULL, 'd', 0),
(241, 'Lire les categories', 'categorie', 1, 'lire', NULL, 'r', 1),
(242, 'Creer/modifier les categories', 'categorie', 1, 'creer', NULL, 'w', 0),
(243, 'Supprimer les categories', 'categorie', 1, 'supprimer', NULL, 'd', 0),
(251, 'Consulter les autres utilisateurs', 'user', 1, 'user', 'lire', 'r', 0),
(252, 'Consulter les permissions des autres utilisateurs', 'user', 1, 'user_advance', 'readperms', 'r', 0),
(253, 'Creer/modifier utilisateurs internes et externes', 'user', 1, 'user', 'creer', 'w', 0),
(254, 'Creer/modifier utilisateurs externes seulement', 'user', 1, 'user_advance', 'write', 'w', 0),
(255, 'Modifier le mot de passe des autres utilisateurs', 'user', 1, 'user', 'password', 'w', 0),
(256, 'Supprimer ou desactiver les autres utilisateurs', 'user', 1, 'user', 'supprimer', 'd', 0),
(262, 'Consulter tous les tiers par utilisateurs internes (sinon uniquement si contact commercial). Non effectif pour utilisateurs externes (tjs limités à eux-meme).', 'societe', 1, 'client', 'voir', 'r', 1),
(281, 'Lire les contacts', 'societe', 1, 'contact', 'lire', 'r', 1),
(282, 'Creer modifier les contacts', 'societe', 1, 'contact', 'creer', 'w', 0),
(283, 'Supprimer les contacts', 'societe', 1, 'contact', 'supprimer', 'd', 0),
(286, 'Exporter les contacts', 'societe', 1, 'contact', 'export', 'd', 0),
(331, 'Lire les bookmarks', 'bookmark', 1, 'lire', NULL, 'r', 1),
(332, 'Creer/modifier les bookmarks', 'bookmark', 1, 'creer', NULL, 'r', 1),
(333, 'Supprimer les bookmarks', 'bookmark', 1, 'supprimer', NULL, 'r', 1),
(341, 'Consulter ses propres permissions', 'user', 1, 'self_advance', 'readperms', 'r', 1),
(342, 'Creer/modifier ses propres infos utilisateur', 'user', 1, 'self', 'creer', 'w', 1),
(343, 'Modifier son propre mot de passe', 'user', 1, 'self', 'password', 'w', 1),
(344, 'Modifier ses propres permissions', 'user', 1, 'self_advance', 'writeperms', 'w', 1),
(351, 'Consulter les groupes', 'user', 1, 'group_advance', 'read', 'r', 0),
(352, 'Consulter les permissions des groupes', 'user', 1, 'group_advance', 'readperms', 'r', 0),
(353, 'Creer/modifier les groupes et leurs permissions', 'user', 1, 'group_advance', 'write', 'w', 0),
(354, 'Supprimer ou desactiver les groupes', 'user', 1, 'group_advance', 'delete', 'd', 0),
(358, 'Exporter les utilisateurs', 'user', 1, 'user', 'export', 'r', 0),
(531, 'Lire les services', 'service', 1, 'lire', NULL, 'r', 1),
(532, 'Creer/modifier les services', 'service', 1, 'creer', NULL, 'w', 0),
(534, 'Supprimer les services', 'service', 1, 'supprimer', NULL, 'd', 0),
(538, 'Exporter les services', 'service', 1, 'export', NULL, 'r', 0),
(1001, 'Lire les stocks', 'stock', 1, 'lire', NULL, 'r', 1),
(1002, 'Creer/Modifier les stocks', 'stock', 1, 'creer', NULL, 'w', 0),
(1003, 'Supprimer les stocks', 'stock', 1, 'supprimer', NULL, 'd', 0),
(1004, 'Lire mouvements de stocks', 'stock', 1, 'mouvement', 'lire', 'r', 1),
(1005, 'Creer/modifier mouvements de stocks', 'stock', 1, 'mouvement', 'creer', 'w', 0),
(1101, 'Lire les bons de livraison', 'expedition', 1, 'livraison', 'lire', 'r', 1),
(1102, 'Creer modifier les bons de livraison', 'expedition', 1, 'livraison', 'creer', 'w', 0),
(1104, 'Valider les bons de livraison', 'expedition', 1, 'livraison', 'valider', 'd', 0),
(1109, 'Supprimer les bons de livraison', 'expedition', 1, 'livraison', 'supprimer', 'd', 0),
(1181, 'Consulter les fournisseurs', 'fournisseur', 1, 'lire', NULL, 'r', 1),
(1182, 'Consulter les commandes fournisseur', 'fournisseur', 1, 'commande', 'lire', 'r', 1),
(1183, 'Creer une commande fournisseur', 'fournisseur', 1, 'commande', 'creer', 'w', 0),
(1184, 'Valider une commande fournisseur', 'fournisseur', 1, 'commande', 'valider', 'w', 0),
(1185, 'Approuver une commande fournisseur', 'fournisseur', 1, 'commande', 'approuver', 'w', 0),
(1186, 'Commander une commande fournisseur', 'fournisseur', 1, 'commande', 'commander', 'w', 0),
(1187, 'Receptionner une commande fournisseur', 'fournisseur', 1, 'commande', 'receptionner', 'd', 0),
(1188, 'Supprimer une commande fournisseur', 'fournisseur', 1, 'commande', 'supprimer', 'd', 0),
(1201, 'Lire les exports', 'export', 1, 'lire', NULL, 'r', 1),
(1202, 'Creer/modifier un export', 'export', 1, 'creer', NULL, 'w', 0),
(1231, 'Consulter les factures fournisseur', 'fournisseur', 1, 'facture', 'lire', 'r', 1),
(1232, 'Creer une facture fournisseur', 'fournisseur', 1, 'facture', 'creer', 'w', 0),
(1233, 'Valider une facture fournisseur', 'fournisseur', 1, 'facture', 'valider', 'w', 0),
(1234, 'Supprimer une facture fournisseur', 'fournisseur', 1, 'facture', 'supprimer', 'd', 0),
(1235, 'Envoyer les factures par mail', 'fournisseur', 1, 'supplier_invoice_advance', 'send', 'a', 0),
(1236, 'Exporter les factures fournisseurs, attributs et reglements', 'fournisseur', 1, 'facture', 'export', 'r', 0),
(1237, 'Exporter les commande fournisseurs, attributs', 'fournisseur', 1, 'commande', 'export', 'r', 0),
(1251, 'Run mass imports of external data (data load)', 'import', 1, 'run', NULL, 'r', 0),
(1321, 'Exporter les factures clients, attributs et reglements', 'facture', 1, 'facture', 'export', 'r', 0),
(1421, 'Exporter les commandes clients et attributs', 'commande', 1, 'commande', 'export', 'r', 0),
(2401, 'Read actions/tasks linked to his account', 'agenda', 1, 'myactions', 'read', 'r', 1),
(2402, 'Create/modify actions/tasks linked to his account', 'agenda', 1, 'myactions', 'create', 'w', 0),
(2403, 'Delete actions/tasks linked to his account', 'agenda', 1, 'myactions', 'delete', 'w', 0),
(2411, 'Read actions/tasks of others', 'agenda', 1, 'allactions', 'read', 'r', 0),
(2412, 'Create/modify actions/tasks of others', 'agenda', 1, 'allactions', 'create', 'w', 0),
(2413, 'Delete actions/tasks of others', 'agenda', 1, 'allactions', 'delete', 'w', 0),
(2501, 'Consulter/Télécharger les documents', 'ecm', 1, 'read', NULL, 'r', 1),
(2503, 'Soumettre ou supprimer des documents', 'ecm', 1, 'upload', NULL, 'w', 1),
(2515, 'Administrer les rubriques de documents', 'ecm', 1, 'setup', NULL, 'w', 1),
(20001, 'Créer / Modifier / Lire ses demandes de congés payés', 'holiday', 1, 'write', NULL, 'w', 1),
(20002, 'Lire / Modifier toutes les demandes de congés payés', 'holiday', 1, 'lire_tous', NULL, 'w', 0),
(20003, 'Supprimer des demandes de congés payés', 'holiday', 1, 'delete', NULL, 'w', 0),
(20004, 'Définir les congés payés des utilisateurs', 'holiday', 1, 'define_holiday', NULL, 'w', 0),
(20005, 'Voir les logs de modification des congés payés', 'holiday', 1, 'view_log', NULL, 'w', 0),
(20006, 'Accéder au rapport mensuel des congés payés', 'holiday', 1, 'month_report', NULL, 'w', 0),
(20120, 'Leer Pedidos Almacen', 'almacen', 1, 'leerpedido', NULL, 'w', 0),
(20121, 'Crear Pedidos Almacen', 'almacen', 1, 'crearpedido', NULL, 'w', 0),
(20130, 'Leer Unidades de Medida', 'almacen', 1, 'leerunidad', NULL, 'w', 0),
(20131, 'Crear Unidades de Medida', 'almacen', 1, 'crearunidad', NULL, 'w', 0),
(20140, 'Crear Entregas de Almacenes  ', 'almacen', 1, 'crearentrega', NULL, 'w', 1),
(20141, 'Leer Entregas de Almacenes  ', 'almacen', 1, 'leerentrega', NULL, 'w', 1),
(20142, 'Borrar Entregas de Almacenes  ', 'almacen', 1, 'supprimer', NULL, 'w', 1),
(20160, 'Leer Relacion Almacenes  ', 'almacen', 1, 'leerlocal', NULL, 'w', 1),
(20161, 'Crear Nueva Relacion Almacen  ', 'almacen', 1, 'crearlocal', NULL, 'w', 1),
(20162, 'Leer Traspasos entre Almacenes ', 'almacen', 1, 'leertransferencia', NULL, 'w', 1),
(20163, 'Crear Traspasos entre Almacenes ', 'almacen', 1, 'creartransferencia', NULL, 'w', 1),
(20164, 'Ver Kardex ', 'almacen', 1, 'leerkardex', NULL, 'w', 1),
(20165, 'Ver Inventarios ', 'almacen', 1, 'leerinventario', NULL, 'w', 1),
(20170, 'Leer Transferencias ', 'almacen', 1, 'leertransfer', NULL, 'w', 1),
(20171, 'Crear Transferencias ', 'almacen', 1, 'creartransfer', NULL, 'w', 1),
(20321, 'Leer Ordenes Produccion', 'fabrication', 1, 'leerop', NULL, 'w', 0),
(20322, 'Crear Ordenes Produccion', 'fabrication', 1, 'crearop', NULL, 'w', 0),
(20323, 'Borrar Ordenes Produccion', 'fabrication', 1, 'deleteop', NULL, 'w', 0),
(20324, 'Cerrar Ordenes Produccion', 'fabrication', 1, 'closeproduction', NULL, 'w', 0),
(20340, 'Lista Materiales ', 'fabrication', 1, 'leerlistproduct', NULL, 'w', 1),
(20341, 'Crear Lista Material ', 'fabrication', 1, 'crearlistproduct', NULL, 'w', 1),
(20342, 'Borrar Lista Material', 'fabrication', 1, 'supprimerlistproduct', NULL, 'w', 1),
(20343, 'Lista Productos Alternativos', 'fabrication', 1, 'leerlistproductalt', NULL, 'w', 1),
(20344, 'Crear Productos Alternativos', 'fabrication', 1, 'crearlistproductalt', NULL, 'w', 1),
(20345, 'Borrar Productos Alternativos', 'fabrication', 1, 'supprimerproductalt', NULL, 'w', 1),
(20401, 'Read chart account', 'contab', 1, 'leeraccount', NULL, 'w', 0),
(20402, 'Create account plan', 'contab', 1, 'crearaccount', NULL, 'w', 0),
(20403, 'Delete account plan', 'contab', 1, 'delaccount', NULL, 'w', 0),
(20410, 'Read period', 'contab', 1, 'leerperiod', NULL, 'w', 0),
(20411, 'Create period  ', 'contab', 1, 'crearperiod', NULL, 'w', 1),
(20412, 'Delete period', 'contab', 1, 'delperiod', NULL, 'w', 1),
(20413, 'Validate period', 'contab', 1, 'valperiod', NULL, 'w', 1),
(20420, 'Read point entry  ', 'contab', 1, 'leerpoint', NULL, 'w', 1),
(20421, 'Create point entry  ', 'contab', 1, 'crearpoint', NULL, 'w', 1),
(20422, 'Delete point entry', 'contab', 1, 'delepoint', NULL, 'w', 1),
(20430, 'Read standard seat', 'contab', 1, 'leerseatst', NULL, 'w', 1),
(20431, 'Create standard seat', 'contab', 1, 'crearseatst', NULL, 'w', 1),
(20432, 'Delete standard seat', 'contab', 1, 'delseatst', NULL, 'w', 1),
(20440, 'Read standard manual', 'contab', 1, 'leerseatma', NULL, 'w', 1),
(20441, 'Create standard manual', 'contab', 1, 'crearseatma', NULL, 'w', 1),
(20442, 'Delete standard manual', 'contab', 1, 'delseatma', NULL, 'w', 1),
(59201, 'Use punto de Venta', 'ventas', 1, 'use', NULL, 'a', 1),
(59202, 'Leer Permisos', 'ventas', 1, 'leerPermiso', NULL, 'a', 1),
(59203, 'Crear Permisos', 'ventas', 1, 'crearPermiso', NULL, 'a', 1),
(59204, 'Ver Resumen de Caja', 'ventas', 1, 'verrescaja', NULL, 'a', 1),
(59205, 'Crear Gastos', 'ventas', 1, 'creargasto', NULL, 'a', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe`
--

CREATE TABLE IF NOT EXISTS `llx_societe` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(128) DEFAULT NULL,
  `ref_int` varchar(60) DEFAULT NULL,
  `statut` tinyint(4) DEFAULT '0',
  `parent` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datea` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `code_client` varchar(24) DEFAULT NULL,
  `code_fournisseur` varchar(24) DEFAULT NULL,
  `code_compta` varchar(24) DEFAULT NULL,
  `code_compta_fournisseur` varchar(24) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `fk_departement` int(11) DEFAULT '0',
  `fk_pays` int(11) DEFAULT '0',
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `fk_effectif` int(11) DEFAULT '0',
  `fk_typent` int(11) DEFAULT '0',
  `fk_forme_juridique` int(11) DEFAULT '0',
  `fk_currency` int(11) DEFAULT '0',
  `siren` varchar(128) DEFAULT NULL,
  `siret` varchar(128) DEFAULT NULL,
  `ape` varchar(128) DEFAULT NULL,
  `idprof4` varchar(128) DEFAULT NULL,
  `idprof5` varchar(128) DEFAULT NULL,
  `idprof6` varchar(128) DEFAULT NULL,
  `tva_intra` varchar(20) DEFAULT NULL,
  `capital` double DEFAULT NULL,
  `fk_stcomm` int(11) NOT NULL DEFAULT '0',
  `note` text,
  `prefix_comm` varchar(5) DEFAULT NULL,
  `client` tinyint(4) DEFAULT '0',
  `fournisseur` tinyint(4) DEFAULT '0',
  `supplier_account` varchar(32) DEFAULT NULL,
  `fk_prospectlevel` varchar(12) DEFAULT NULL,
  `customer_bad` tinyint(4) DEFAULT '0',
  `customer_rate` double DEFAULT '0',
  `supplier_rate` double DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `remise_client` double DEFAULT '0',
  `mode_reglement` tinyint(4) DEFAULT NULL,
  `cond_reglement` tinyint(4) DEFAULT NULL,
  `tva_assuj` tinyint(4) DEFAULT '1',
  `localtax1_assuj` tinyint(4) DEFAULT '0',
  `localtax2_assuj` tinyint(4) DEFAULT '0',
  `barcode` varchar(255) DEFAULT NULL,
  `fk_barcode_type` int(11) DEFAULT '0',
  `price_level` int(11) DEFAULT NULL,
  `default_lang` varchar(6) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `canvas` varchar(32) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_societe_prefix_comm` (`prefix_comm`,`entity`),
  UNIQUE KEY `uk_societe_code_client` (`code_client`,`entity`),
  KEY `idx_societe_user_creat` (`fk_user_creat`),
  KEY `idx_societe_user_modif` (`fk_user_modif`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Volcado de datos para la tabla `llx_societe`
--

INSERT INTO `llx_societe` (`rowid`, `nom`, `entity`, `ref_ext`, `ref_int`, `statut`, `parent`, `tms`, `datec`, `datea`, `status`, `code_client`, `code_fournisseur`, `code_compta`, `code_compta_fournisseur`, `address`, `cp`, `ville`, `fk_departement`, `fk_pays`, `tel`, `fax`, `url`, `email`, `fk_effectif`, `fk_typent`, `fk_forme_juridique`, `fk_currency`, `siren`, `siret`, `ape`, `idprof4`, `idprof5`, `idprof6`, `tva_intra`, `capital`, `fk_stcomm`, `note`, `prefix_comm`, `client`, `fournisseur`, `supplier_account`, `fk_prospectlevel`, `customer_bad`, `customer_rate`, `supplier_rate`, `fk_user_creat`, `fk_user_modif`, `remise_client`, `mode_reglement`, `cond_reglement`, `tva_assuj`, `localtax1_assuj`, `localtax2_assuj`, `barcode`, `fk_barcode_type`, `price_level`, `default_lang`, `logo`, `canvas`, `import_key`) VALUES
(1, 'VentasxMenor', 1, NULL, NULL, 0, NULL, '2013-03-02 20:07:44', '2013-02-25 19:40:57', '2013-03-02 16:07:44', 1, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Trans America', 1, NULL, NULL, 0, NULL, '2013-03-05 13:19:13', '2013-03-05 09:19:13', '2013-03-05 09:19:13', 0, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Jose Luis Mariaca', 1, NULL, NULL, 0, NULL, '2013-03-05 13:43:18', '2013-03-05 09:43:18', '2013-03-05 09:43:18', 0, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 8, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'Pedro Mendez', 1, NULL, NULL, 0, NULL, '2013-03-16 13:56:19', '2013-03-16 09:56:19', '2013-03-16 09:56:19', 0, '6786876', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 8, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'quimica montes', 1, NULL, NULL, 0, NULL, '2013-04-06 16:43:22', '2013-04-06 12:43:22', '2013-04-06 12:43:22', 1, NULL, 'PROV00001', NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'don joseluis', 1, NULL, NULL, 0, NULL, '2013-04-15 23:13:24', '2013-04-15 19:13:24', '2013-04-15 19:13:24', 0, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 3, 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(7, 'proveedor generico', 1, NULL, NULL, 0, NULL, '2013-04-19 00:00:14', '2013-04-18 20:00:14', '2013-04-18 20:00:14', 1, NULL, 'PROV. GENERICO', NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(8, 'cliente generico', 1, NULL, NULL, 0, NULL, '2013-05-01 16:05:00', '2013-05-01 12:05:00', '2013-05-01 12:05:00', 1, 'GENERICO', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(40, 'Abarrotes en General', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0001', 'SU1305-0001', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(41, 'Industrias Venado S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0002', 'SU1305-0002', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(42, 'Quimica Industrial J. Montes S.R.L.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0003', 'SU1305-0003', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(43, 'Industrias Lider Ltda.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0004', 'SU1305-0004', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(44, 'Pil Andina S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0005', 'SU1305-0005', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(45, 'Esencial S.R.L.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0006', 'SU1305-0006', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(46, 'Industrias de Aceite S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0007', 'SU1305-0007', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(47, 'Almacen Santa Cruz', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0008', 'SU1305-0008', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(48, 'DVC importaciones', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0009', 'SU1305-0009', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(49, 'Cafe Copacabana', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0010', 'SU1305-0010', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(50, 'Café Haiti', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0011', 'SU1305-0011', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(51, 'Elena Coro', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0012', 'SU1305-0012', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(52, 'La Predilecta', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0013', 'SU1305-0013', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(53, 'Companex Bolivia S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0014', 'SU1305-0014', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(54, 'Mermelada Amazonas', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0015', 'SU1305-0015', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(55, 'Barr&Mart', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0016', 'SU1305-0016', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(56, 'La Serranita', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0017', 'SU1305-0017', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(57, 'Breick', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0018', 'SU1305-0018', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(58, 'Distribucion y Mercadeo Ltda.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0019', 'SU1305-0019', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(59, 'Arpo Ltda', 1, NULL, NULL, 0, NULL, '2013-05-25 17:37:33', NULL, '2013-05-25 13:37:33', 1, 'CU1305-0020', 'SU1305-0020', NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(60, 'Bertha Sanchez', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0021', 'SU1305-0021', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(61, 'Distribuidora SANTA RITA', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0022', 'SU1305-0022', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(62, 'Caisy Ltda.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0023', 'SU1305-0023', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(63, 'Embol S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0024', 'SU1305-0024', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(64, 'Hansa Ltda.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0025', 'SU1305-0025', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(65, 'La Papelera S.A.', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0026', 'SU1305-0026', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(66, 'Victor Salazar', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0027', 'SU1305-0027', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(67, 'Displas', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0028', 'SU1305-0028', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(68, 'Imprenta Rojas', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0029', 'SU1305-0029', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(69, 'HP medical', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0030', 'SU1305-0030', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(70, 'Delizia', 1, NULL, NULL, 0, NULL, '2013-05-25 17:43:22', NULL, NULL, 1, 'CU1305-0031', 'SU1305-0031', NULL, NULL, NULL, NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, NULL, 0, 0, 0, 1, NULL, 0, NULL, NULL, 1, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '20130504195753'),
(71, 'Jorge Rada', 1, NULL, NULL, 0, NULL, '2013-05-14 18:39:05', '2013-05-14 14:39:05', '2013-05-14 14:39:05', 0, 'CU1305-0032', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(72, 'Carlos Mendizabal', 1, NULL, NULL, 0, NULL, '2013-05-15 16:04:48', '2013-05-15 12:04:48', '2013-05-15 12:04:48', 0, 'CU1305-0033', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 8, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(73, 'sra. martha gutierrez', 1, NULL, NULL, 0, NULL, '2013-05-18 18:50:29', '2013-05-18 14:50:29', '2013-05-18 14:50:29', 0, 'CU1305-0034', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(74, 'ssss', 1, NULL, NULL, 0, NULL, '2013-05-25 13:42:11', '2013-05-25 09:42:11', '2013-05-25 09:42:11', 0, 'CU1305-0035', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL),
(75, 'Rita Mamani', 1, NULL, NULL, 0, NULL, '2013-05-25 13:42:57', '2013-05-25 09:42:57', '2013-05-25 09:42:57', 0, 'CU1305-0036', NULL, NULL, NULL, '', NULL, NULL, 0, 52, NULL, NULL, NULL, NULL, NULL, 8, NULL, 0, '', '', '', '', '', '', '', 0, 0, NULL, NULL, 1, 0, NULL, NULL, 0, 0, 0, 9, 9, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_address`
--

CREATE TABLE IF NOT EXISTS `llx_societe_address` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(30) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT '0',
  `name` varchar(60) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `note` text,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_commerciaux`
--

CREATE TABLE IF NOT EXISTS `llx_societe_commerciaux` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_societe_commerciaux` (`fk_soc`,`fk_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_societe_commerciaux`
--

INSERT INTO `llx_societe_commerciaux` (`rowid`, `fk_soc`, `fk_user`) VALUES
(1, 1, 1),
(2, 5, 1),
(3, 7, 1),
(4, 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_societe_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_societe_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_log`
--

CREATE TABLE IF NOT EXISTS `llx_societe_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datel` datetime DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_statut` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `author` varchar(30) DEFAULT NULL,
  `label` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_prices`
--

CREATE TABLE IF NOT EXISTS `llx_societe_prices` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `price_level` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_remise`
--

CREATE TABLE IF NOT EXISTS `llx_societe_remise` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `remise_client` double(6,3) NOT NULL DEFAULT '0.000',
  `note` text,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_remise_except`
--

CREATE TABLE IF NOT EXISTS `llx_societe_remise_except` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `amount_ht` double(24,8) NOT NULL,
  `amount_tva` double(24,8) NOT NULL DEFAULT '0.00000000',
  `amount_ttc` double(24,8) NOT NULL DEFAULT '0.00000000',
  `tva_tx` double(6,3) NOT NULL DEFAULT '0.000',
  `fk_user` int(11) NOT NULL,
  `fk_facture_line` int(11) DEFAULT NULL,
  `fk_facture` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_societe_remise_except_fk_user` (`fk_user`),
  KEY `idx_societe_remise_except_fk_soc` (`fk_soc`),
  KEY `idx_societe_remise_except_fk_facture_line` (`fk_facture_line`),
  KEY `idx_societe_remise_except_fk_facture` (`fk_facture`),
  KEY `idx_societe_remise_except_fk_facture_source` (`fk_facture_source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_societe_rib`
--

CREATE TABLE IF NOT EXISTS `llx_societe_rib` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(30) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `code_banque` varchar(7) DEFAULT NULL,
  `code_guichet` varchar(6) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `cle_rib` varchar(5) DEFAULT NULL,
  `bic` varchar(20) DEFAULT NULL,
  `iban_prefix` varchar(34) DEFAULT NULL,
  `domiciliation` varchar(255) DEFAULT NULL,
  `proprio` varchar(60) DEFAULT NULL,
  `adresse_proprio` varchar(255) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_socpeople`
--

CREATE TABLE IF NOT EXISTS `llx_socpeople` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_soc` int(11) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `civilite` varchar(6) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cp` varchar(25) DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `poste` varchar(80) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `phone_perso` varchar(30) DEFAULT NULL,
  `phone_mobile` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `jabberid` varchar(255) DEFAULT NULL,
  `priv` smallint(6) NOT NULL DEFAULT '0',
  `no_email` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT '0',
  `fk_user_modif` int(11) DEFAULT NULL,
  `note` text,
  `default_lang` varchar(6) DEFAULT NULL,
  `canvas` varchar(32) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_socpeople_fk_soc` (`fk_soc`),
  KEY `idx_socpeople_fk_user_creat` (`fk_user_creat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `llx_socpeople`
--

INSERT INTO `llx_socpeople` (`rowid`, `datec`, `tms`, `fk_soc`, `entity`, `civilite`, `name`, `firstname`, `address`, `cp`, `ville`, `fk_departement`, `fk_pays`, `birthday`, `poste`, `phone`, `phone_perso`, `phone_mobile`, `fax`, `email`, `jabberid`, `priv`, `no_email`, `fk_user_creat`, `fk_user_modif`, `note`, `default_lang`, `canvas`, `import_key`) VALUES
(1, '2013-03-05 09:43:18', '2013-03-05 13:43:18', 3, 1, 'MR', 'Mariaca', 'Jose Luis', '', '', '', NULL, 52, NULL, '', '', '', '', '', '', '', 0, 0, 1, 1, '', NULL, NULL, NULL),
(2, '2013-03-16 09:56:19', '2013-03-16 13:56:19', 4, 1, 'MR', 'Mendez', 'Pedro', '', '', '', NULL, 52, NULL, '', '', '', '', '', '', '', 0, 0, 1, 1, '', NULL, NULL, NULL),
(3, '2013-05-15 12:04:48', '2013-05-15 16:04:48', 72, 1, '', 'Mendizabal', 'Carlos', '', '', '', NULL, 52, NULL, '', '', '', '', '', '', '', 0, 0, 1, 1, '', NULL, NULL, NULL),
(4, '2013-05-25 09:42:57', '2013-05-25 13:42:57', 75, 1, 'MME', 'Mamani', 'Rita', '', '', '', NULL, 52, NULL, '', '', '', '', '', '', '', 0, 0, 9, 9, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_socpeople_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_socpeople_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_socpeople_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_sol_almacen`
--

CREATE TABLE IF NOT EXISTS `llx_sol_almacen` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `ref` varchar(30) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `fk_fabrication` int(11) NOT NULL DEFAULT '0',
  `date_creation` date NOT NULL,
  `date_delivery` date NOT NULL,
  `description` text,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabla de fabricacion' AUTO_INCREMENT=32 ;

--
-- Volcado de datos para la tabla `llx_sol_almacen`
--

INSERT INTO `llx_sol_almacen` (`rowid`, `entity`, `ref`, `fk_entrepot`, `fk_fabrication`, `date_creation`, `date_delivery`, `description`, `statut`) VALUES
(1, 1, 'aabb11', 3, -1, '2013-03-22', '2013-03-20', 'dddd', 0),
(2, 1, 'Bolsa', 2, 2, '2013-04-02', '2013-03-20', 'solicitud de bolsas', 0),
(3, 1, 'ECUA001', 9, 4, '2013-04-05', '2013-03-20', 'pedido a almacenes', 2),
(4, 1, 'prueba jose', 3, -1, '2013-04-05', '2013-03-20', '', 2),
(5, 1, '645645', 13, 10, '2013-04-12', '2013-03-20', 'PEDIDO PARA LAS GALLETAS', 2),
(6, 1, 'PEDIDO PARA', 13, 11, '2013-04-12', '2013-03-20', '', 2),
(7, 1, '23123', 13, 12, '2013-04-14', '2013-03-20', '', 2),
(8, 1, '12312', 13, 13, '2013-04-14', '2013-03-20', '', 2),
(9, 1, 'ASDASD', 13, 14, '2013-04-14', '2013-03-20', '', 0),
(10, 1, '654654', 2, -1, '2013-04-17', '2013-03-20', '', 0),
(11, 1, 'man180413', 13, -1, '2013-04-17', '2013-03-20', '', 2),
(12, 1, '45654', 13, -1, '2013-04-17', '2013-03-20', '', 0),
(13, 1, '56456', 13, 15, '2013-04-17', '2013-03-20', '', 0),
(14, 1, '123', 13, 16, '2013-04-17', '2013-03-20', '', 0),
(15, 1, '1234', 13, 1, '2013-04-26', '2013-03-20', '', 2),
(16, 1, '104104', 13, 17, '2013-04-26', '2013-03-20', '', 2),
(17, 1, '456456789789', 13, 18, '2013-04-26', '2013-03-20', '', 2),
(18, 1, '1232231', 13, 8, '2013-04-30', '2013-03-20', 'pedido req', 0),
(19, 1, 'PALM1304-000', 13, 6, '2013-04-30', '2013-03-20', '', 2),
(20, 1, '(PROV)', 13, 23, '2013-05-13', '2013-03-20', '', 0),
(21, 1, 'PALM1305-000', 13, 21, '2013-05-14', '2013-03-20', '', 2),
(22, 1, '(PROV)', 13, 8, '2013-05-14', '2013-03-20', '', 0),
(23, 1, 'PALM1305-000', 13, 24, '2013-05-14', '2013-03-20', '', 1),
(24, 1, 'PALM1305-000', 13, 25, '2013-05-15', '2013-03-20', '', 1),
(25, 1, '(PROV)', 13, 26, '2013-05-17', '2013-03-20', '', 0),
(26, 1, '(PROV)', 13, 27, '2013-05-17', '2013-03-20', '', 0),
(27, 1, 'PALM1305-0006', 13, 28, '2013-05-24', '2013-03-20', '', 2),
(28, 1, '(PROV)', 13, 29, '2013-05-24', '2013-03-20', 'sol almacen', 0),
(29, 1, '(PROV)', 13, 30, '2013-05-24', '2013-03-20', '', 0),
(30, 1, '(PROV)', 13, 32, '2013-05-24', '2013-03-20', '', 0),
(31, 1, '(PROV)', 13, -1, '2013-05-31', '2013-03-20', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_sol_almacendet`
--

CREATE TABLE IF NOT EXISTS `llx_sol_almacendet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_almacen` int(11) NOT NULL,
  `fk_product` int(11) NOT NULL,
  `qty` double NOT NULL,
  `qty_livree` double DEFAULT NULL,
  `date_shipping` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Registra los items de fabricacion' AUTO_INCREMENT=96 ;

--
-- Volcado de datos para la tabla `llx_sol_almacendet`
--

INSERT INTO `llx_sol_almacendet` (`rowid`, `fk_almacen`, `fk_product`, `qty`, `qty_livree`, `date_shipping`) VALUES
(1, 1, 194, 1, NULL, NULL),
(2, 1, 1, 3, NULL, NULL),
(3, 1, 221, 50, NULL, NULL),
(4, 3, 1270, 5, 5, NULL),
(5, 3, 1126, 800, 800, NULL),
(6, 3, 1114, 1600, 1600, NULL),
(7, 2, 168, 100, NULL, NULL),
(8, 4, 1271, 50, 50, NULL),
(9, 5, 1114, 10, 8, NULL),
(10, 6, 1270, 4, 4, NULL),
(11, 6, 1172, 0.2, 0.2, NULL),
(12, 6, 1126, 0.2, 0.2, NULL),
(13, 6, 1116, 0.15, 0.15, NULL),
(14, 6, 1114, 0.2, 0.2, NULL),
(15, 7, 1270, 5, 5, NULL),
(16, 7, 1126, 800, 800, NULL),
(17, 7, 1114, 1600, 1600, NULL),
(18, 7, 1119, 10, 5, NULL),
(19, 8, 1270, 50, 50, NULL),
(20, 8, 1126, 8000, 8000, NULL),
(21, 8, 1114, 16000, 16000, NULL),
(22, 9, 1270, 25, NULL, NULL),
(23, 9, 1126, 4000, NULL, NULL),
(24, 9, 1114, 8000, NULL, NULL),
(25, 11, 1114, 100, 100, NULL),
(26, 11, 1126, 100, 90, NULL),
(27, 12, 1114, 101, NULL, NULL),
(28, 14, 1270, 0.5, NULL, NULL),
(29, 14, 1172, 2, NULL, NULL),
(30, 14, 1126, 80, NULL, NULL),
(31, 14, 1116, 1.5, NULL, NULL),
(32, 14, 1115, 300, NULL, NULL),
(33, 14, 1114, 2, NULL, NULL),
(34, 14, 1719, 1, NULL, NULL),
(35, 15, 168, 100000, 100, NULL),
(36, 15, 1126, 10, 10, NULL),
(37, 15, 1114, 2.211, 2.211, NULL),
(38, 15, 1274, 10, 10, NULL),
(39, 15, 1271, 10, 10, NULL),
(40, 16, 1270, 5, 5, NULL),
(49, 16, 1126, 800, 800, NULL),
(52, 16, 1114, 1600, 1600, NULL),
(53, 17, 181, 2, 2, NULL),
(54, 17, 187, 4, 4, NULL),
(55, 17, 1271, 1, 1, NULL),
(56, 20, 1270, 308, NULL, NULL),
(57, 20, 1172, 15.4, NULL, NULL),
(58, 20, 1126, 15.4, NULL, NULL),
(59, 20, 1116, 11.55, NULL, NULL),
(60, 20, 1114, 15.4, NULL, NULL),
(61, 21, 1270, 40, 40, NULL),
(62, 21, 1172, 2, 2, NULL),
(63, 21, 1126, 2, 2, NULL),
(64, 21, 1116, 1.5, 1.5, NULL),
(65, 21, 1114, 2, 2, NULL),
(66, 23, 1270, 400, NULL, NULL),
(67, 23, 1172, 20, NULL, NULL),
(68, 23, 1127, 20, NULL, NULL),
(69, 23, 1116, 15, NULL, NULL),
(70, 23, 1114, 20, NULL, NULL),
(72, 23, 1126, 20, NULL, NULL),
(73, 19, 1316, 12, 10, NULL),
(74, 24, 1475, 10, NULL, NULL),
(75, 25, 1263, 100, NULL, NULL),
(76, 25, 1127, 5, NULL, NULL),
(77, 25, 1114, 10, NULL, NULL),
(78, 23, 1263, 1000, NULL, NULL),
(79, 21, 1263, 100, 100, NULL),
(80, 21, 1127, 5, 5, NULL),
(81, 27, 1263, 100, 100, NULL),
(82, 27, 1127, 5, 5, NULL),
(85, 27, 1114, 10, 10, NULL),
(86, 29, 1263, 10, NULL, NULL),
(87, 29, 1127, 0.5, NULL, NULL),
(88, 29, 1114, 1, NULL, NULL),
(90, 30, 1126, 0.5, NULL, NULL),
(91, 30, 1114, 1, NULL, NULL),
(92, 30, 1264, 50, NULL, NULL),
(93, 30, 1263, 10, NULL, NULL),
(94, 20, 1263, 770, NULL, NULL),
(95, 20, 1127, 38.5, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_stock_father`
--

CREATE TABLE IF NOT EXISTS `llx_stock_father` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` datetime NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `fk_almacen` int(11) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_stock_mouvement`
--

CREATE TABLE IF NOT EXISTS `llx_stock_mouvement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datem` datetime DEFAULT NULL,
  `fk_product` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `value` int(11) DEFAULT NULL,
  `price` float(13,4) DEFAULT '0.0000',
  `type_mouvement` smallint(6) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `label` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_stock_mouvement_fk_product` (`fk_product`),
  KEY `idx_stock_mouvement_fk_entrepot` (`fk_entrepot`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=359 ;

--
-- Volcado de datos para la tabla `llx_stock_mouvement`
--

INSERT INTO `llx_stock_mouvement` (`rowid`, `tms`, `datem`, `fk_product`, `fk_entrepot`, `value`, `price`, `type_mouvement`, `fk_user_author`, `label`) VALUES
(1, '2013-03-02 19:48:49', '2013-03-02 15:48:49', 171, 1, -1, 6.0000, 2, 1, 'Factura FA1303-0004 validada'),
(2, '2013-03-02 19:48:49', '2013-03-02 15:48:49', 207, 1, -1, 65.0000, 2, 1, 'Factura FA1303-0004 validada'),
(3, '2013-03-02 19:48:49', '2013-03-02 15:48:49', 39, 1, -1, 18.0000, 2, 1, 'Factura FA1303-0004 validada'),
(4, '2013-03-02 19:48:49', '2013-03-02 15:48:49', 29, 1, -1, 100.0000, 2, 1, 'Factura FA1303-0004 validada'),
(5, '2013-03-02 19:48:49', '2013-03-02 15:48:49', 164, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0004 validada'),
(6, '2013-03-02 20:51:44', '2013-03-02 16:51:44', 37, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0005 validada'),
(7, '2013-03-02 20:51:44', '2013-03-02 16:51:44', 29, 1, -1, 100.0000, 2, 1, 'Factura FA1303-0005 validada'),
(8, '2013-03-02 20:51:44', '2013-03-02 16:51:44', 39, 1, -1, 18.0000, 2, 1, 'Factura FA1303-0005 validada'),
(9, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 38, 1, -2, 3.0000, 2, 1, 'Factura FA1303-0006 validada'),
(10, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 184, 1, -1, 15.0000, 2, 1, 'Factura FA1303-0006 validada'),
(11, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 207, 1, -1, 65.0000, 2, 1, 'Factura FA1303-0006 validada'),
(12, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 37, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0006 validada'),
(13, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 29, 1, -2, 100.0000, 2, 1, 'Factura FA1303-0006 validada'),
(14, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 39, 1, -1, 18.0000, 2, 1, 'Factura FA1303-0006 validada'),
(15, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 1, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0006 validada'),
(16, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 50, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0006 validada'),
(17, '2013-03-02 20:58:25', '2013-03-02 16:58:25', 164, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0006 validada'),
(18, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 37, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0007 validada'),
(19, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 29, 1, -1, 100.0000, 2, 1, 'Factura FA1303-0007 validada'),
(20, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 47, 1, -2, 35.0000, 2, 1, 'Factura FA1303-0007 validada'),
(21, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 39, 1, -5, 18.0000, 2, 1, 'Factura FA1303-0007 validada'),
(22, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 164, 1, -2, 40.0000, 2, 1, 'Factura FA1303-0007 validada'),
(23, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 184, 1, -1, 15.0000, 2, 1, 'Factura FA1303-0007 validada'),
(24, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0007 validada'),
(25, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 1, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0007 validada'),
(26, '2013-03-02 20:59:44', '2013-03-02 16:59:44', 50, 1, -20, 4.0000, 2, 1, 'Factura FA1303-0007 validada'),
(27, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 37, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0008 validada'),
(28, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 29, 1, -2, 100.0000, 2, 1, 'Factura FA1303-0008 validada'),
(29, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 47, 1, -3, 35.0000, 2, 1, 'Factura FA1303-0008 validada'),
(30, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 39, 1, -6, 18.0000, 2, 1, 'Factura FA1303-0008 validada'),
(31, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 164, 1, -2, 40.0000, 2, 1, 'Factura FA1303-0008 validada'),
(32, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 184, 1, -1, 15.0000, 2, 1, 'Factura FA1303-0008 validada'),
(33, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0008 validada'),
(34, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 1, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0008 validada'),
(35, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 50, 1, -20, 4.0000, 2, 1, 'Factura FA1303-0008 validada'),
(36, '2013-03-02 21:01:38', '2013-03-02 17:01:38', 207, 1, -6, 65.0000, 2, 1, 'Factura FA1303-0008 validada'),
(37, '2013-03-02 21:10:14', '2013-03-02 17:10:14', 29, 1, -6, 100.0000, 2, 1, 'Factura FA1303-0009 validada'),
(38, '2013-03-02 21:10:14', '2013-03-02 17:10:14', 47, 1, -2, 35.0000, 2, 1, 'Factura FA1303-0009 validada'),
(39, '2013-03-02 21:10:14', '2013-03-02 17:10:14', 1, 1, -3, 4.0000, 2, 1, 'Factura FA1303-0009 validada'),
(40, '2013-03-02 21:10:14', '2013-03-02 17:10:14', 50, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0009 validada'),
(41, '2013-03-02 21:10:14', '2013-03-02 17:10:14', 164, 1, -2, 40.0000, 2, 1, 'Factura FA1303-0009 validada'),
(42, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 29, 1, -6, 100.0000, 2, 1, 'Factura FA1303-0010 validada'),
(43, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 47, 1, -2, 35.0000, 2, 1, 'Factura FA1303-0010 validada'),
(44, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 1, 1, -4, 4.0000, 2, 1, 'Factura FA1303-0010 validada'),
(45, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 50, 1, -2, 4.0000, 2, 1, 'Factura FA1303-0010 validada'),
(46, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 164, 1, -2, 40.0000, 2, 1, 'Factura FA1303-0010 validada'),
(47, '2013-03-02 21:14:56', '2013-03-02 17:14:56', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0010 validada'),
(48, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 37, 1, -2, 40.0000, 2, 1, 'Factura FA1303-0011 validada'),
(49, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 47, 1, -1, 35.0000, 2, 1, 'Factura FA1303-0011 validada'),
(50, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 39, 1, -1, 18.0000, 2, 1, 'Factura FA1303-0011 validada'),
(51, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 29, 1, -1, 100.0000, 2, 1, 'Factura FA1303-0011 validada'),
(52, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 50, 1, -20, 4.0000, 2, 1, 'Factura FA1303-0011 validada'),
(53, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 1, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0011 validada'),
(54, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0011 validada'),
(55, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 184, 1, -2, 15.0000, 2, 1, 'Factura FA1303-0011 validada'),
(56, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 164, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0011 validada'),
(57, '2013-03-04 20:25:49', '2013-03-04 16:25:49', 207, 1, -1, 65.0000, 2, 1, 'Factura FA1303-0011 validada'),
(58, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 37, 1, -8, 40.0000, 2, 1, 'Factura FA1303-0012 validada'),
(59, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 40, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0012 validada'),
(60, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 177, 1, -1, 30.0000, 2, 1, 'Factura FA1303-0012 validada'),
(61, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 213, 1, -1, 5.0000, 2, 1, 'Factura FA1303-0012 validada'),
(62, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 34, 1, -3, 12.0000, 2, 1, 'Factura FA1303-0012 validada'),
(63, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 167, 1, -1, 20.0000, 2, 1, 'Factura FA1303-0012 validada'),
(64, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0012 validada'),
(65, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 1, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0012 validada'),
(66, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 50, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0012 validada'),
(67, '2013-03-06 02:27:23', '2013-03-05 22:27:23', 29, 1, -41, 100.0000, 2, 1, 'Factura FA1303-0012 validada'),
(68, '2013-03-06 03:16:24', '2013-03-05 23:16:24', 1, 9, -10, 4.0000, 2, 1, 'Factura FA1303-0013 validada'),
(69, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 207, 1, -29, 65.0000, 2, 1, 'Factura FA1303-0014 validada'),
(70, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 37, 1, -4, 40.0000, 2, 1, 'Factura FA1303-0014 validada'),
(71, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 29, 1, -3, 100.0000, 2, 1, 'Factura FA1303-0014 validada'),
(72, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 47, 1, -6, 35.0000, 2, 1, 'Factura FA1303-0014 validada'),
(73, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 38, 1, -1, 3.0000, 2, 1, 'Factura FA1303-0014 validada'),
(74, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 50, 1, -1, 4.0000, 2, 1, 'Factura FA1303-0014 validada'),
(75, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 164, 1, -1, 40.0000, 2, 1, 'Factura FA1303-0014 validada'),
(76, '2013-03-09 16:00:52', '2013-03-09 12:00:52', 184, 1, -1, 15.0000, 2, 1, 'Factura FA1303-0014 validada'),
(77, '2013-03-16 16:10:32', '2013-03-16 12:10:32', 168, 1, -100, 0.0000, 1, 1, 'transferenci para venta'),
(78, '2013-03-16 16:10:33', '2013-03-16 12:10:32', 168, 3, 100, 0.0000, 0, 1, 'transferenci para venta'),
(79, '2013-03-16 16:10:56', '2013-03-16 12:10:56', 168, 1, 1000, 10.0000, 0, 1, ''),
(89, '2013-04-06 17:04:39', '2013-04-06 13:04:39', 1253, 2, 100, 88.4956, 3, 1, 'Factura 5 validada'),
(90, '2013-04-06 17:05:40', '2013-04-06 13:05:40', 1270, 9, -5, 0.0000, 1, 1, 'Salida de almacen segun pedido ECUA001'),
(91, '2013-04-06 17:05:40', '2013-04-06 13:05:40', 1126, 9, -800, 0.0000, 1, 1, 'Salida de almacen segun pedido ECUA001'),
(92, '2013-04-06 17:05:40', '2013-04-06 13:05:40', 1114, 9, -1600, 0.0000, 1, 1, 'Salida de almacen segun pedido ECUA001'),
(93, '2013-04-11 01:06:57', '2013-04-10 21:06:57', 43, 1, 100, 0.0000, 0, 1, ''),
(94, '2013-04-11 01:29:21', '2013-04-10 21:29:21', 29, 1, -6, 100.0000, 2, 1, 'Factura FA1304-0015 validada'),
(95, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 1508, 1, -100, 0.0000, 1, 1, 'por traspaso segun orden de doña rosario'),
(96, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 1508, 3, 100, 0.0000, 0, 1, 'por traspaso segun orden de doña rosario'),
(97, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 43, 1, -50, 0.0000, 1, 1, 'por traspaso segun orden de doña rosario'),
(98, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 43, 3, 50, 0.0000, 0, 1, 'por traspaso segun orden de doña rosario'),
(99, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 1271, 1, -80, 0.0000, 1, 1, 'por traspaso segun orden de doña rosario'),
(100, '2013-04-11 22:48:20', '2013-04-11 18:48:20', 1271, 3, 80, 0.0000, 0, 1, 'por traspaso segun orden de doña rosario'),
(101, '2013-04-13 14:17:47', '2013-04-13 10:17:47', 37, 6, -3, 40.0000, 2, 1, 'Factura FA1304-0016 validada'),
(102, '2013-04-13 14:17:47', '2013-04-13 10:17:47', 29, 6, -1, 100.0000, 2, 1, 'Factura FA1304-0016 validada'),
(103, '2013-04-13 14:17:47', '2013-04-13 10:17:47', 47, 6, -1, 35.0000, 2, 1, 'Factura FA1304-0016 validada'),
(104, '2013-04-13 14:17:47', '2013-04-13 10:17:47', 39, 6, -1, 18.0000, 2, 1, 'Factura FA1304-0016 validada'),
(105, '2013-04-13 14:20:12', '2013-04-13 10:20:12', 37, 6, -1, 40.0000, 2, 1, 'Factura FA1304-0017 validada'),
(106, '2013-04-13 14:54:13', '2013-04-13 10:54:13', 37, 1, -1, 40.0000, 2, 12, 'Factura FA1304-0018 validada'),
(107, '2013-04-13 14:54:13', '2013-04-13 10:54:13', 1, 1, -3, 4.0000, 2, 12, 'Factura FA1304-0018 validada'),
(108, '2013-04-13 14:54:13', '2013-04-13 10:54:13', 213, 1, -1, 5.0000, 2, 12, 'Factura FA1304-0018 validada'),
(109, '2013-04-13 14:57:11', '2013-04-13 10:57:11', 1, 1, -5, 4.0000, 2, 12, 'Factura FA1304-0019 validada'),
(110, '2013-04-13 14:57:30', '2013-04-13 10:57:30', 50, 1, -4, 4.0000, 2, 12, 'Factura FA1304-0020 validada'),
(111, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 43, 3, -10, 0.0000, 1, 1, ''),
(112, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1114, 3, -2, 0.0000, 1, 1, ''),
(113, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1116, 3, -2, 0.0000, 1, 1, ''),
(114, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1126, 3, -2, 0.0000, 1, 1, ''),
(115, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1172, 3, -2, 0.0000, 1, 1, ''),
(116, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1270, 3, -40, 0.0000, 1, 1, ''),
(117, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 43, 4, 10, 0.0000, 0, 1, ''),
(118, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1114, 4, 2, 0.0000, 0, 1, ''),
(119, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1116, 4, 2, 0.0000, 0, 1, ''),
(120, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1126, 4, 2, 0.0000, 0, 1, ''),
(121, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1172, 4, 2, 0.0000, 0, 1, ''),
(122, '2013-04-13 15:10:36', '2013-04-13 11:10:36', 1270, 4, 40, 0.0000, 0, 1, ''),
(123, '2013-04-13 16:05:51', '2013-04-13 12:05:51', 33, 11, 100, 0.0000, 0, 1, ''),
(124, '2013-04-13 16:06:30', '2013-04-13 12:06:30', 33, 11, -50, 0.0000, 1, 1, 'primer despacho'),
(125, '2013-04-13 16:06:30', '2013-04-13 12:06:30', 33, 1, 50, 0.0000, 0, 1, 'primer despacho'),
(126, '2013-04-13 16:08:29', '2013-04-13 12:08:29', 33, 1, -3, 15.0000, 2, 12, 'Factura FA1304-0021 validada'),
(127, '2013-04-13 16:09:16', '2013-04-13 12:09:16', 33, 1, -10, 15.0000, 2, 12, 'Factura FA1304-0022 validada'),
(128, '2013-04-13 16:55:39', '2013-04-13 12:55:39', 1114, 13, -8, 0.0000, 1, 1, 'Salida de almacen segun pedido 645645'),
(129, '2013-04-13 17:08:53', '2013-04-13 13:08:53', 1270, 13, -4, 0.0000, 1, 1, 'Salida de almacen segun pedido PEDIDO PARA'),
(130, '2013-04-13 17:08:53', '2013-04-13 13:08:53', 1172, 13, 0, 0.0000, 1, 1, 'Salida de almacen segun pedido PEDIDO PARA'),
(131, '2013-04-13 17:08:53', '2013-04-13 13:08:53', 1126, 13, 0, 0.0000, 1, 1, 'Salida de almacen segun pedido PEDIDO PARA'),
(132, '2013-04-13 17:08:53', '2013-04-13 13:08:53', 1116, 13, 0, 0.0000, 1, 1, 'Salida de almacen segun pedido PEDIDO PARA'),
(133, '2013-04-13 17:08:53', '2013-04-13 13:08:53', 1114, 13, 0, 0.0000, 1, 1, 'Salida de almacen segun pedido PEDIDO PARA'),
(134, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 43, 11, 1, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(135, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 1114, 11, 0, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(136, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 1116, 11, 0, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(137, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 1126, 11, 0, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(138, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 1172, 11, 0, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(139, '2013-04-13 17:09:49', '2013-04-13 13:09:49', 1270, 11, 4, 0.0000, 1, 1, 'Envio de acuerdo a Produccion 2321'),
(140, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 33, 6, -3, 15.0000, 2, 1, 'Factura FA1304-0023 validada'),
(141, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 39, 6, -1, 18.0000, 2, 1, 'Factura FA1304-0023 validada'),
(142, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 50, 6, -1, 4.0000, 2, 1, 'Factura FA1304-0023 validada'),
(143, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 47, 6, -1, 35.0000, 2, 1, 'Factura FA1304-0023 validada'),
(144, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 37, 6, -2, 40.0000, 2, 1, 'Factura FA1304-0023 validada'),
(145, '2013-04-13 18:11:07', '2013-04-13 14:11:07', 177, 6, -1, 30.0000, 2, 1, 'Factura FA1304-0023 validada'),
(146, '2013-04-15 23:11:12', '2013-04-15 19:11:12', 1270, 13, -5, 0.0000, 1, 3, 'Salida de almacen segun pedido 23123'),
(147, '2013-04-15 23:11:12', '2013-04-15 19:11:12', 1126, 13, -800, 0.0000, 1, 3, 'Salida de almacen segun pedido 23123'),
(148, '2013-04-15 23:11:12', '2013-04-15 19:11:12', 1119, 13, -5, 0.0000, 1, 3, 'Salida de almacen segun pedido 23123'),
(149, '2013-04-15 23:11:12', '2013-04-15 19:11:12', 1114, 13, -1600, 0.0000, 1, 3, 'Salida de almacen segun pedido 23123'),
(150, '2013-04-15 23:16:26', '2013-04-15 19:16:26', 1270, 13, -50, 0.0000, 1, 3, 'Salida de almacen segun pedido 12312'),
(151, '2013-04-15 23:16:26', '2013-04-15 19:16:26', 1126, 13, -8000, 0.0000, 1, 3, 'Salida de almacen segun pedido 12312'),
(152, '2013-04-15 23:16:27', '2013-04-15 19:16:27', 1114, 13, -16000, 0.0000, 1, 3, 'Salida de almacen segun pedido 12312'),
(153, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 43, 4, -10, 0.0000, 1, 3, ''),
(154, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1114, 4, -2, 0.0000, 1, 3, ''),
(155, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1116, 4, -2, 0.0000, 1, 3, ''),
(156, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1126, 4, -2, 0.0000, 1, 3, ''),
(157, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1172, 4, -2, 0.0000, 1, 3, ''),
(158, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1270, 4, -40, 0.0000, 1, 3, ''),
(159, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 43, 1, 10, 0.0000, 0, 3, ''),
(160, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1114, 1, 2, 0.0000, 0, 3, ''),
(161, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1116, 1, 2, 0.0000, 0, 3, ''),
(162, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1126, 1, 2, 0.0000, 0, 3, ''),
(163, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1172, 1, 2, 0.0000, 0, 3, ''),
(164, '2013-04-15 23:17:59', '2013-04-15 19:17:59', 1270, 1, 40, 0.0000, 0, 3, ''),
(165, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 43, 3, -40, 0.0000, 1, 3, ''),
(166, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1114, 3, -8, 0.0000, 1, 3, ''),
(167, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1116, 3, -6, 0.0000, 1, 3, ''),
(168, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1126, 3, -8, 0.0000, 1, 3, ''),
(169, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1172, 3, -8, 0.0000, 1, 3, ''),
(170, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1270, 3, -160, 0.0000, 1, 3, ''),
(171, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 43, 1, 40, 0.0000, 0, 3, ''),
(172, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1114, 1, 8, 0.0000, 0, 3, ''),
(173, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1116, 1, 6, 0.0000, 0, 3, ''),
(174, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1126, 1, 8, 0.0000, 0, 3, ''),
(175, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1172, 1, 8, 0.0000, 0, 3, ''),
(176, '2013-04-15 23:18:26', '2013-04-15 19:18:26', 1270, 1, 160, 0.0000, 0, 3, ''),
(177, '2013-04-15 23:26:59', '2013-04-15 19:26:59', 38, 5, -3, 3.0000, 2, 3, 'Factura FA1304-0024 validada'),
(178, '2013-04-15 23:26:59', '2013-04-15 19:26:59', 50, 5, -20, 4.0000, 2, 3, 'Factura FA1304-0024 validada'),
(179, '2013-04-15 23:26:59', '2013-04-15 19:26:59', 1, 5, -1, 4.0000, 2, 3, 'Factura FA1304-0024 validada'),
(180, '2013-04-19 00:16:51', '2013-04-18 20:16:51', 1126, 13, -90, 0.0000, 1, 1, 'Salida de almacen segun pedido man180413'),
(181, '2013-04-19 00:16:52', '2013-04-18 20:16:51', 1114, 13, -100, 0.0000, 1, 1, 'Salida de almacen segun pedido man180413'),
(182, '2013-04-19 18:53:37', '2013-04-19 14:53:37', 1114, 13, 100, 184.0000, 3, 1, 'Recepción del pedido a proveedor CF1304-0006  recepcion total'),
(183, '2013-04-19 19:00:48', '2013-04-19 15:00:48', 1114, 13, 800, 150.0000, 3, 1, 'Recepción del pedido a proveedor CF1304-0007'),
(184, '2013-04-19 19:00:59', '2013-04-19 15:00:59', 1114, 2, 200, 150.0000, 3, 1, 'Recepción del pedido a proveedor CF1304-0007'),
(185, '2013-04-20 13:49:44', '2013-04-20 09:49:44', 1271, 3, -50, 0.0000, 1, 1, 'Salida de almacen segun pedido prueba jose'),
(186, '2013-04-27 14:34:20', '2013-04-27 10:34:20', 1199, 13, 10, 2.0000, 0, 3, ''),
(187, '2013-04-27 14:35:00', '2013-04-27 10:35:00', 1119, 13, 10, 0.0000, 0, 3, ''),
(188, '2013-04-27 14:35:13', '2013-04-27 10:35:13', 1119, 13, -3, 0.0000, 1, 3, ''),
(189, '2013-04-27 16:01:05', '2013-04-27 12:01:05', 1500, 3, -100, 0.0000, 1, 1, ''),
(190, '2013-04-27 16:01:05', '2013-04-27 12:01:05', 1500, 4, 100, 0.0000, 0, 1, ''),
(191, '2013-04-27 16:01:05', '2013-04-27 12:01:05', 206, 3, -10, 0.0000, 1, 1, ''),
(192, '2013-04-27 16:01:05', '2013-04-27 12:01:05', 206, 4, 10, 0.0000, 0, 1, ''),
(193, '2013-04-27 20:24:13', '2013-04-27 16:24:13', 181, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido 456456789789'),
(194, '2013-04-27 20:24:13', '2013-04-27 16:24:13', 187, 13, -4, 0.0000, 1, 1, 'Salida de almacen segun pedido 456456789789'),
(195, '2013-04-27 20:24:13', '2013-04-27 16:24:13', 1271, 13, -1, 0.0000, 1, 1, 'Salida de almacen segun pedido 456456789789'),
(196, '2013-04-27 20:24:55', '2013-04-27 16:24:55', 181, 13, 10, 0.0000, 0, 1, ''),
(197, '2013-04-27 20:25:33', '2013-04-27 16:25:33', 181, 13, -5, 0.0000, 1, 1, ''),
(198, '2013-04-27 20:25:33', '2013-04-27 16:25:33', 181, 1, 5, 0.0000, 0, 1, ''),
(199, '2013-05-01 21:05:57', '2013-05-01 17:05:57', 1270, 13, -5, 0.0000, 1, 1, 'Salida de almacen segun pedido 104104'),
(200, '2013-05-01 21:05:57', '2013-05-01 17:05:57', 1126, 13, -800, 0.0000, 1, 1, 'Salida de almacen segun pedido 104104'),
(201, '2013-05-01 21:05:57', '2013-05-01 17:05:57', 1114, 13, -1600, 0.0000, 1, 1, 'Salida de almacen segun pedido 104104'),
(202, '2013-05-04 13:36:20', '2013-05-04 09:36:20', 207, 6, -1, 65.0000, 2, 1, 'Factura FA1305-0025 validada'),
(203, '2013-05-04 13:41:14', '2013-05-04 09:41:14', 29, 6, -6, 100.0000, 2, 1, 'Factura FA1305-0026 validada'),
(204, '2013-05-04 13:41:14', '2013-05-04 09:41:14', 184, 6, -2, 15.0000, 2, 1, 'Factura FA1305-0026 validada'),
(205, '2013-05-04 13:49:22', '2013-05-04 09:49:22', 29, 6, -1, 100.0000, 2, 9, 'Factura FA1305-0027 validada'),
(206, '2013-05-04 13:49:22', '2013-05-04 09:49:22', 207, 6, -1, 65.0000, 2, 9, 'Factura FA1305-0027 validada'),
(207, '2013-05-04 13:49:22', '2013-05-04 09:49:22', 50, 6, -6, 4.0000, 2, 9, 'Factura FA1305-0027 validada'),
(208, '2013-05-04 13:49:22', '2013-05-04 09:49:22', 1115, 6, -18, 0.0000, 2, 9, 'Factura FA1305-0027 validada'),
(209, '2013-05-04 13:49:22', '2013-05-04 09:49:22', 1270, 6, -60, 0.0000, 2, 9, 'Factura FA1305-0027 validada'),
(210, '2013-05-04 13:51:31', '2013-05-04 09:51:31', 164, 6, -6, 40.0000, 2, 9, 'Factura FA1305-0028 validada'),
(211, '2013-05-04 13:51:31', '2013-05-04 09:51:31', 39, 6, -1, 18.0000, 2, 9, 'Factura FA1305-0028 validada'),
(212, '2013-05-04 13:52:37', '2013-05-04 09:52:37', 164, 6, -3, 40.0000, 2, 9, 'Factura FA1305-0029 validada'),
(213, '2013-05-04 13:52:37', '2013-05-04 09:52:37', 39, 6, -1, 18.0000, 2, 9, 'Factura FA1305-0029 validada'),
(214, '2013-05-04 15:18:09', '2013-05-04 11:18:09', 184, 3, -1, 15.0000, 2, 1, 'Factura FA1305-0030 validada'),
(215, '2013-05-04 15:18:09', '2013-05-04 11:18:09', 29, 3, -2, 100.0000, 2, 1, 'Factura FA1305-0030 validada'),
(216, '2013-05-04 15:18:09', '2013-05-04 11:18:09', 39, 3, -2, 18.0000, 2, 1, 'Factura FA1305-0030 validada'),
(217, '2013-05-04 15:18:09', '2013-05-04 11:18:09', 37, 3, -1, 40.0000, 2, 1, 'Factura FA1305-0030 validada'),
(218, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 37, 3, -200, 40.0000, 2, 6, 'Factura FA1305-0031 validada'),
(219, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 47, 3, -1, 35.0000, 2, 6, 'Factura FA1305-0031 validada'),
(220, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 33, 3, -1, 15.0000, 2, 6, 'Factura FA1305-0031 validada'),
(221, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 39, 3, -1, 18.0000, 2, 6, 'Factura FA1305-0031 validada'),
(222, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 164, 3, -2, 40.0000, 2, 6, 'Factura FA1305-0031 validada'),
(223, '2013-05-04 15:21:38', '2013-05-04 11:21:38', 50, 3, -1, 4.0000, 2, 6, 'Factura FA1305-0031 validada'),
(224, '2013-05-04 15:21:39', '2013-05-04 11:21:38', 1115, 3, -3, 0.0000, 2, 6, 'Factura FA1305-0031 validada'),
(225, '2013-05-04 15:21:39', '2013-05-04 11:21:39', 1270, 3, -10, 0.0000, 2, 6, 'Factura FA1305-0031 validada'),
(226, '2013-05-04 15:21:39', '2013-05-04 11:21:39', 207, 3, -1, 65.0000, 2, 6, 'Factura FA1305-0031 validada'),
(227, '2013-05-04 15:21:39', '2013-05-04 11:21:39', 29, 3, -1, 100.0000, 2, 6, 'Factura FA1305-0031 validada'),
(228, '2013-05-04 15:21:39', '2013-05-04 11:21:39', 171, 3, -1, 6.0000, 2, 6, 'Factura FA1305-0031 validada'),
(229, '2013-05-04 15:21:39', '2013-05-04 11:21:39', 38, 3, -2, 3.0000, 2, 6, 'Factura FA1305-0031 validada'),
(230, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 37, 3, -200, 40.0000, 2, 6, 'Factura FA1305-0032 validada'),
(231, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 47, 3, -1, 35.0000, 2, 6, 'Factura FA1305-0032 validada'),
(232, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 33, 3, -1, 15.0000, 2, 6, 'Factura FA1305-0032 validada'),
(233, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 39, 3, -1, 18.0000, 2, 6, 'Factura FA1305-0032 validada'),
(234, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 164, 3, -2, 40.0000, 2, 6, 'Factura FA1305-0032 validada'),
(235, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 50, 3, -1, 4.0000, 2, 6, 'Factura FA1305-0032 validada'),
(236, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 1115, 3, -3, 0.0000, 2, 6, 'Factura FA1305-0032 validada'),
(237, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 1270, 3, -10, 0.0000, 2, 6, 'Factura FA1305-0032 validada'),
(238, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 207, 3, -1, 65.0000, 2, 6, 'Factura FA1305-0032 validada'),
(239, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 29, 3, -1, 100.0000, 2, 6, 'Factura FA1305-0032 validada'),
(240, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 171, 3, -1, 6.0000, 2, 6, 'Factura FA1305-0032 validada'),
(241, '2013-05-04 15:22:07', '2013-05-04 11:22:07', 38, 3, -2, 3.0000, 2, 6, 'Factura FA1305-0032 validada'),
(242, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 37, 3, -1, 40.0000, 2, 6, 'Factura FA1305-0033 validada'),
(243, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 177, 3, -1, 30.0000, 2, 6, 'Factura FA1305-0033 validada'),
(244, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 38, 3, -2, 3.0000, 2, 6, 'Factura FA1305-0033 validada'),
(245, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 184, 3, -2, 15.0000, 2, 6, 'Factura FA1305-0033 validada'),
(246, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 29, 3, -5, 100.0000, 2, 6, 'Factura FA1305-0033 validada'),
(247, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 207, 3, -1, 65.0000, 2, 6, 'Factura FA1305-0033 validada'),
(248, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 33, 3, -2, 15.0000, 2, 6, 'Factura FA1305-0033 validada'),
(249, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 47, 3, -2, 35.0000, 2, 6, 'Factura FA1305-0033 validada'),
(250, '2013-05-04 15:57:41', '2013-05-04 11:57:41', 1, 3, -1, 4.0000, 2, 6, 'Factura FA1305-0033 validada'),
(251, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 37, 3, -2, 40.0000, 2, 6, 'Factura FA1305-0034 validada'),
(252, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 177, 3, -3, 30.0000, 2, 6, 'Factura FA1305-0034 validada'),
(253, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 38, 3, -6, 3.0000, 2, 6, 'Factura FA1305-0034 validada'),
(254, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 184, 3, -4, 15.0000, 2, 6, 'Factura FA1305-0034 validada'),
(255, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 29, 3, -5, 100.0000, 2, 6, 'Factura FA1305-0034 validada'),
(256, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 207, 3, -2, 65.0000, 2, 6, 'Factura FA1305-0034 validada'),
(257, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 33, 3, -4, 15.0000, 2, 6, 'Factura FA1305-0034 validada'),
(258, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 47, 3, -2, 35.0000, 2, 6, 'Factura FA1305-0034 validada'),
(259, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 1, 3, -1, 4.0000, 2, 6, 'Factura FA1305-0034 validada'),
(260, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 213, 3, -2, 5.0000, 2, 6, 'Factura FA1305-0034 validada'),
(261, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 34, 3, -1, 12.0000, 2, 6, 'Factura FA1305-0034 validada'),
(262, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 164, 3, -1, 40.0000, 2, 6, 'Factura FA1305-0034 validada'),
(263, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 50, 3, -1, 4.0000, 2, 6, 'Factura FA1305-0034 validada'),
(264, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 1115, 3, -3, 0.0000, 2, 6, 'Factura FA1305-0034 validada'),
(265, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 1270, 3, -10, 0.0000, 2, 6, 'Factura FA1305-0034 validada'),
(266, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 39, 3, -1, 18.0000, 2, 6, 'Factura FA1305-0034 validada'),
(267, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 1542, 3, -1, 0.0000, 2, 6, 'Factura FA1305-0034 validada'),
(268, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 215, 3, -1, 5.0000, 2, 6, 'Factura FA1305-0034 validada'),
(269, '2013-05-04 16:08:38', '2013-05-04 12:08:38', 1559, 3, -1, 0.0000, 2, 6, 'Factura FA1305-0034 validada'),
(270, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0035 validada'),
(271, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 213, 11, -1, 5.0000, 2, 1, 'Factura FA1305-0035 validada'),
(272, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 177, 11, -1, 30.0000, 2, 1, 'Factura FA1305-0035 validada'),
(273, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 38, 11, -20, 3.0000, 2, 1, 'Factura FA1305-0035 validada'),
(274, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 34, 11, -1, 12.0000, 2, 1, 'Factura FA1305-0035 validada'),
(275, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 39, 11, -2, 18.0000, 2, 1, 'Factura FA1305-0035 validada'),
(276, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 33, 11, -5, 15.0000, 2, 1, 'Factura FA1305-0035 validada'),
(277, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 164, 11, -20, 40.0000, 2, 1, 'Factura FA1305-0035 validada'),
(278, '2013-05-05 00:25:28', '2013-05-04 20:25:28', 47, 11, -5, 35.0000, 2, 1, 'Factura FA1305-0035 validada'),
(279, '2013-05-05 00:28:11', '2013-05-04 20:28:11', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0036 validada'),
(280, '2013-05-05 00:28:11', '2013-05-04 20:28:11', 177, 11, -1, 30.0000, 2, 1, 'Factura FA1305-0036 validada'),
(281, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0037 validada'),
(282, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 34, 11, -2, 12.0000, 2, 1, 'Factura FA1305-0037 validada'),
(283, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 184, 11, -1, 15.0000, 2, 1, 'Factura FA1305-0037 validada'),
(284, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 164, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0037 validada'),
(285, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 33, 11, -1, 15.0000, 2, 1, 'Factura FA1305-0037 validada'),
(286, '2013-05-05 00:38:03', '2013-05-04 20:38:03', 1, 11, -1, 4.0000, 2, 1, 'Factura FA1305-0037 validada'),
(287, '2013-05-05 00:42:29', '2013-05-04 20:42:29', 49, 11, -20, 0.0000, 1, 1, 'DESPACHO TARDE '),
(288, '2013-05-05 00:42:29', '2013-05-04 20:42:29', 49, 6, 20, 0.0000, 0, 1, 'DESPACHO TARDE '),
(289, '2013-05-05 00:42:29', '2013-05-04 20:42:29', 47, 11, -10, 0.0000, 1, 1, 'DESPACHO TARDE '),
(290, '2013-05-05 00:42:29', '2013-05-04 20:42:29', 47, 6, 10, 0.0000, 0, 1, 'DESPACHO TARDE '),
(291, '2013-05-14 23:55:38', '2013-05-14 19:55:38', 33, 11, -8, 0.0000, 1, 1, ''),
(292, '2013-05-14 23:55:38', '2013-05-14 19:55:38', 33, 6, 8, 0.0000, 0, 1, ''),
(293, '2013-05-15 00:11:14', '2013-05-14 20:11:14', 168, 13, -100, 0.0000, 1, 1, 'Salida de almacen segun pedido 1234'),
(294, '2013-05-15 00:11:14', '2013-05-14 20:11:14', 1126, 13, -10, 0.0000, 1, 1, 'Salida de almacen segun pedido 1234'),
(295, '2013-05-15 00:11:15', '2013-05-14 20:11:15', 1114, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido 1234'),
(296, '2013-05-15 00:11:15', '2013-05-14 20:11:15', 1274, 13, -10, 0.0000, 1, 1, 'Salida de almacen segun pedido 1234'),
(297, '2013-05-15 00:11:15', '2013-05-14 20:11:15', 1271, 13, -10, 0.0000, 1, 1, 'Salida de almacen segun pedido 1234'),
(298, '2013-05-18 17:23:25', '2013-05-18 13:23:25', 50, 11, -1, 4.0000, 2, 1, 'Factura FA1305-0038 validada'),
(299, '2013-05-18 17:23:25', '2013-05-18 13:23:25', 1115, 11, -3, 0.0000, 2, 1, 'Factura FA1305-0038 validada'),
(300, '2013-05-18 17:23:25', '2013-05-18 13:23:25', 1270, 11, -10, 0.0000, 2, 1, 'Factura FA1305-0038 validada'),
(301, '2013-05-18 17:23:25', '2013-05-18 13:23:25', 207, 11, -1, 65.0000, 2, 1, 'Factura FA1305-0038 validada'),
(302, '2013-05-18 17:23:25', '2013-05-18 13:23:25', 47, 11, -2, 35.0000, 2, 1, 'Factura FA1305-0038 validada'),
(303, '2013-05-18 17:24:23', '2013-05-18 13:24:23', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0039 validada'),
(304, '2013-05-18 17:24:23', '2013-05-18 13:24:23', 177, 11, -3, 30.0000, 2, 1, 'Factura FA1305-0039 validada'),
(305, '2013-05-18 17:24:23', '2013-05-18 13:24:23', 50, 11, -1, 4.0000, 2, 1, 'Factura FA1305-0039 validada'),
(306, '2013-05-18 17:24:23', '2013-05-18 13:24:23', 1115, 11, -3, 0.0000, 2, 1, 'Factura FA1305-0039 validada'),
(307, '2013-05-18 17:24:23', '2013-05-18 13:24:23', 1270, 11, -10, 0.0000, 2, 1, 'Factura FA1305-0039 validada'),
(308, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0040 validada'),
(309, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 177, 11, -3, 30.0000, 2, 1, 'Factura FA1305-0040 validada'),
(310, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 50, 11, -1, 4.0000, 2, 1, 'Factura FA1305-0040 validada'),
(311, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 1115, 11, -3, 0.0000, 2, 1, 'Factura FA1305-0040 validada'),
(312, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 1270, 11, -10, 0.0000, 2, 1, 'Factura FA1305-0040 validada'),
(313, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 33, 11, -2, 15.0000, 2, 1, 'Factura FA1305-0040 validada'),
(314, '2013-05-18 17:25:25', '2013-05-18 13:25:25', 207, 11, -2, 65.0000, 2, 1, 'Factura FA1305-0040 validada'),
(315, '2013-05-18 17:27:21', '2013-05-18 13:27:21', 50, 6, -6, 4.0000, 2, 9, 'Factura FA1305-0041 validada'),
(316, '2013-05-18 17:27:21', '2013-05-18 13:27:21', 1115, 6, -18, 0.0000, 2, 9, 'Factura FA1305-0041 validada'),
(317, '2013-05-18 17:27:21', '2013-05-18 13:27:21', 1270, 6, -60, 0.0000, 2, 9, 'Factura FA1305-0041 validada'),
(318, '2013-05-18 17:27:38', '2013-05-18 13:27:38', 29, 6, -4, 100.0000, 2, 9, 'Factura FA1305-0042 validada'),
(319, '2013-05-18 18:41:59', '2013-05-18 14:41:59', 29, 11, -1, 100.0000, 2, 1, 'Factura FA1305-0043 validada'),
(320, '2013-05-18 18:41:59', '2013-05-18 14:41:59', 37, 11, -1, 40.0000, 2, 1, 'Factura FA1305-0043 validada'),
(321, '2013-05-18 18:41:59', '2013-05-18 14:41:59', 47, 11, -1, 35.0000, 2, 1, 'Factura FA1305-0043 validada'),
(322, '2013-05-18 18:41:59', '2013-05-18 14:41:59', 39, 11, -3, 18.0000, 2, 1, 'Factura FA1305-0043 validada'),
(323, '2013-05-18 19:01:39', '2013-05-18 15:01:39', 1316, 13, -10, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1304-000'),
(324, '2013-05-25 13:00:21', '2013-05-25 09:00:21', 37, 6, -3, 40.0000, 2, 1, 'Factura FA1305-0044 validada'),
(325, '2013-05-25 13:00:53', '2013-05-25 09:00:53', 29, 6, -1, 100.0000, 2, 1, 'Factura FA1305-0045 validada'),
(326, '2013-05-25 13:00:53', '2013-05-25 09:00:53', 47, 6, -1, 35.0000, 2, 1, 'Factura FA1305-0045 validada'),
(327, '2013-05-25 13:07:30', '2013-05-25 09:07:30', 1, 6, -2, 4.0000, 2, 9, 'Factura FA1305-0046 validada'),
(328, '2013-05-25 13:07:30', '2013-05-25 09:07:30', 33, 6, -1, 15.0000, 2, 9, 'Factura FA1305-0046 validada'),
(329, '2013-05-25 13:07:30', '2013-05-25 09:07:30', 39, 6, -2, 18.0000, 2, 9, 'Factura FA1305-0046 validada'),
(330, '2013-05-25 13:16:14', '2013-05-25 09:16:14', 34, 6, -5, 12.0000, 2, 9, 'Factura FA1305-0047 validada'),
(331, '2013-05-25 13:41:27', '2013-05-25 09:41:27', 212, 11, -1, 5.0000, 2, 1, 'Factura FA1305-0048 validada'),
(332, '2013-05-25 13:41:27', '2013-05-25 09:41:27', 1559, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0048 validada'),
(333, '2013-05-25 13:41:27', '2013-05-25 09:41:27', 215, 11, -1, 5.0000, 2, 1, 'Factura FA1305-0048 validada'),
(334, '2013-05-25 14:09:15', '2013-05-25 10:09:15', 1263, 13, -100, 0.0000, 1, 3, 'Salida de almacen segun pedido PALM1305-0006'),
(335, '2013-05-25 14:09:15', '2013-05-25 10:09:15', 1127, 13, -5, 0.0000, 1, 3, 'Salida de almacen segun pedido PALM1305-0006'),
(336, '2013-05-25 14:09:15', '2013-05-25 10:09:15', 1114, 13, -10, 0.0000, 1, 3, 'Salida de almacen segun pedido PALM1305-0006'),
(337, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 215, 11, -1, 5.0000, 2, 1, 'Factura FA1305-0049 validada'),
(338, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1559, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0049 validada'),
(339, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 49, 11, -1, 45.0000, 2, 1, 'Factura FA1305-0049 validada'),
(340, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1532, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0049 validada'),
(341, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1529, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0049 validada'),
(342, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1540, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0049 validada'),
(343, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 1541, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0049 validada'),
(344, '2013-05-25 15:21:08', '2013-05-25 11:21:08', 173, 11, -1, 10.0000, 2, 1, 'Factura FA1305-0049 validada'),
(345, '2013-05-25 15:23:50', '2013-05-25 11:23:50', 1542, 11, -1, 0.0000, 2, 1, 'Factura FA1305-0050 validada'),
(346, '2013-05-25 16:30:50', '2013-05-25 12:30:50', 43, 11, 9, 0.0000, 1, 1, 'Envio de acuerdo a Produccion PR1305-0004'),
(347, '2013-06-01 13:40:38', '2013-06-01 09:40:38', 1270, 13, -40, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(348, '2013-06-01 13:40:38', '2013-06-01 09:40:38', 1263, 13, -100, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(349, '2013-06-01 13:40:38', '2013-06-01 09:40:38', 1172, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(350, '2013-06-01 13:40:38', '2013-06-01 09:40:38', 1127, 13, -5, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(351, '2013-06-01 13:40:39', '2013-06-01 09:40:39', 1126, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(352, '2013-06-01 13:40:39', '2013-06-01 09:40:39', 1116, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(353, '2013-06-01 13:40:39', '2013-06-01 09:40:39', 1114, 13, -2, 0.0000, 1, 1, 'Salida de almacen segun pedido PALM1305-000'),
(354, '2013-06-01 16:22:26', '2013-06-01 12:22:26', 1115, 13, 30, 25.0000, 3, 1, 'Recepción del pedido a proveedor CF1306-0009'),
(355, '2013-06-01 20:05:10', '2013-06-01 16:05:10', 33, 11, -2, 15.0000, 2, 1, 'Factura FA1306-0051 validada'),
(356, '2013-06-01 20:05:55', '2013-06-01 16:05:55', 168, 11, -4, 6.0000, 2, 1, 'Factura FA1306-0052 validada'),
(357, '2013-06-01 20:17:48', '2013-06-01 16:17:48', 29, 11, -1, 100.0000, 2, 1, 'Factura FA1306-0053 validada'),
(358, '2013-06-01 20:17:48', '2013-06-01 16:17:48', 33, 11, -1, 15.0000, 2, 1, 'Factura FA1306-0053 validada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_texts`
--

CREATE TABLE IF NOT EXISTS `llx_texts` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) DEFAULT NULL,
  `typemodele` varchar(32) DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `private` smallint(6) NOT NULL DEFAULT '0',
  `fk_user` int(11) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `filename` varchar(128) DEFAULT NULL,
  `content` text,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_tva`
--

CREATE TABLE IF NOT EXISTS `llx_tva` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` date DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `note` text,
  `fk_bank` int(11) DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_units`
--

CREATE TABLE IF NOT EXISTS `llx_units` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(4) NOT NULL,
  `description` varchar(30) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `llx_units`
--

INSERT INTO `llx_units` (`rowid`, `ref`, `description`) VALUES
(2, 'BD', 'Balde'),
(3, 'qq', 'QUINTAL'),
(4, 'Kg', 'kilogramo'),
(5, 'Tn', 'Tonelada'),
(6, 'UNID', 'Unidad'),
(7, 'BOTE', 'BOTELLA'),
(8, 'BOLS', 'BOLSA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_units_product`
--

CREATE TABLE IF NOT EXISTS `llx_units_product` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL,
  `fk_units` int(11) NOT NULL,
  `fk_unitsproductid` int(11) NOT NULL DEFAULT '0',
  `process` varchar(10) NOT NULL,
  `base` double NOT NULL,
  `sequence` int(2) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_urq_entrepot`
--

CREATE TABLE IF NOT EXISTS `llx_urq_entrepot` (
  `rowid` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `tipo` varchar(30) COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin COMMENT='Relacion dependencia de Almacenes ';

--
-- Volcado de datos para la tabla `llx_urq_entrepot`
--

INSERT INTO `llx_urq_entrepot` (`rowid`, `fk_entrepot`, `tipo`) VALUES
(8, -1, 'almacen'),
(9, -1, 'almacen'),
(10, 9, 'almacen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user`
--

CREATE TABLE IF NOT EXISTS `llx_user` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(50) DEFAULT NULL,
  `ref_int` varchar(50) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login` varchar(24) NOT NULL,
  `pass` varchar(32) DEFAULT NULL,
  `pass_crypted` varchar(128) DEFAULT NULL,
  `pass_temp` varchar(32) DEFAULT NULL,
  `civilite` varchar(6) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `job` varchar(128) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `office_fax` varchar(20) DEFAULT NULL,
  `user_mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `signature` text,
  `admin` smallint(6) DEFAULT '0',
  `webcal_login` varchar(25) DEFAULT NULL,
  `phenix_login` varchar(25) DEFAULT NULL,
  `phenix_pass` varchar(128) DEFAULT NULL,
  `module_comm` smallint(6) DEFAULT '1',
  `module_compta` smallint(6) DEFAULT '1',
  `fk_societe` int(11) DEFAULT NULL,
  `fk_socpeople` int(11) DEFAULT NULL,
  `fk_member` int(11) DEFAULT NULL,
  `note` text,
  `datelastlogin` datetime DEFAULT NULL,
  `datepreviouslogin` datetime DEFAULT NULL,
  `egroupware_id` int(11) DEFAULT NULL,
  `ldap_sid` varchar(255) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `statut` tinyint(4) DEFAULT '1',
  `photo` varchar(255) DEFAULT NULL,
  `lang` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_user_login` (`login`,`entity`),
  UNIQUE KEY `uk_user_fk_socpeople` (`fk_socpeople`),
  UNIQUE KEY `uk_user_fk_member` (`fk_member`),
  KEY `uk_user_fk_societe` (`fk_societe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Volcado de datos para la tabla `llx_user`
--

INSERT INTO `llx_user` (`rowid`, `entity`, `ref_ext`, `ref_int`, `datec`, `tms`, `login`, `pass`, `pass_crypted`, `pass_temp`, `civilite`, `name`, `firstname`, `job`, `office_phone`, `office_fax`, `user_mobile`, `email`, `signature`, `admin`, `webcal_login`, `phenix_login`, `phenix_pass`, `module_comm`, `module_compta`, `fk_societe`, `fk_socpeople`, `fk_member`, `note`, `datelastlogin`, `datepreviouslogin`, `egroupware_id`, `ldap_sid`, `openid`, `statut`, `photo`, `lang`) VALUES
(1, 0, NULL, NULL, '2013-02-19 09:28:28', '2013-05-14 23:36:49', 'admindb', 'admindb', 'a961676177cb4a7c3fb72334600ef72b', NULL, NULL, 'SuperAdmin', '', '', '', '', '', '', '', 1, '', '', '', 1, 1, NULL, NULL, NULL, '', '2013-06-02 23:36:56', '2013-06-02 07:13:15', NULL, '', NULL, 1, NULL, NULL),
(2, 1, NULL, NULL, '2013-04-06 13:17:17', '2013-05-25 15:59:09', 'pmercado', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'MERCADO', 'PATRICIA', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(3, 1, NULL, NULL, '2013-04-06 13:20:07', '2013-04-06 17:21:52', 'alemercado', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'MERCADO', 'ALEJANDRO', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', '2013-05-25 11:16:57', '2013-05-25 10:06:27', NULL, '', NULL, 1, NULL, NULL),
(4, 1, NULL, NULL, '2013-04-06 13:22:18', '2013-04-06 17:32:54', 'anmercado', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'MERCADO', 'ANDREA', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(5, 1, NULL, NULL, '2013-04-06 13:29:56', '2013-05-04 14:13:49', 'cajeramax', '12345', '827ccb0eea8a706c4c34a16891f84e7b', NULL, NULL, 'MAX', 'CAJERA', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(6, 1, NULL, NULL, '2013-04-06 13:30:11', '2013-04-06 17:30:11', 'cajeraanexo', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'ANEXO', 'cajera', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(7, 1, NULL, NULL, '2013-04-06 13:30:37', '2013-04-06 17:30:37', 'cajerayungas', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'Yungas', 'Cajera', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(8, 1, NULL, NULL, '2013-04-06 13:30:55', '2013-04-06 17:30:55', 'cajeraecuador', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'ecuador', 'cajera', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(9, 1, NULL, NULL, '2013-04-06 13:31:18', '2013-05-04 13:26:07', 'cajeraaspiazu', '12345', '827ccb0eea8a706c4c34a16891f84e7b', NULL, NULL, 'Aspiazu', 'cajera', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', '2013-05-25 09:06:38', '2013-05-18 13:10:19', NULL, '', NULL, 1, NULL, NULL),
(10, 1, NULL, NULL, '2013-04-10 19:30:46', '2013-04-10 23:30:46', 'mcallejas', '123456789', '25f9e794323b453885f5181f1b624d0b', NULL, NULL, 'CALLEJAS', 'MARIA EUGENIA', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL),
(12, 1, NULL, NULL, '2013-04-11 18:55:32', '2013-04-11 23:01:40', 'monicamamani', '123456', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, 'Mamani', 'Monica', 'cajera', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', '2013-04-11 19:02:08', NULL, NULL, '', NULL, 1, NULL, NULL),
(14, 1, NULL, NULL, '2013-04-15 19:05:25', '2013-04-15 23:05:25', 'michelline', '0q1rxsfb', 'b67da9421ea73b84759de6a2b7d10f22', NULL, NULL, 'MICHELLINE', '', '', '', '', '', '', '', 0, '', '', '', 1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_usergroup`
--

CREATE TABLE IF NOT EXISTS `llx_usergroup` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` text,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_usergroup_name` (`nom`,`entity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `llx_usergroup`
--

INSERT INTO `llx_usergroup` (`rowid`, `nom`, `entity`, `datec`, `tms`, `note`) VALUES
(1, 'cajeras', 1, '2013-04-06 13:32:19', '2013-04-06 17:32:19', 'permisos para "Punto de Venta"'),
(2, 'PROPIETARIOS', 1, '2013-04-06 13:33:19', '2013-04-06 17:33:19', 'Permiso total'),
(3, 'USUARIOS OF. CENTRAL', 1, '2013-04-10 19:30:03', '2013-04-10 23:30:03', 'PERSONAL ADMINISTRATIVO , CONTABLE Y OTROS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_usergroup_rights`
--

CREATE TABLE IF NOT EXISTS `llx_usergroup_rights` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_usergroup` int(11) NOT NULL,
  `fk_id` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `fk_usergroup` (`fk_usergroup`,`fk_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=223 ;

--
-- Volcado de datos para la tabla `llx_usergroup_rights`
--

INSERT INTO `llx_usergroup_rights` (`rowid`, `fk_usergroup`, `fk_id`) VALUES
(149, 1, 81),
(148, 1, 82),
(150, 1, 84),
(152, 1, 121),
(153, 1, 122),
(157, 1, 262),
(155, 1, 281),
(156, 1, 282),
(142, 1, 59201),
(145, 1, 59204),
(146, 1, 59205),
(54, 2, 11),
(55, 2, 12),
(56, 2, 13),
(57, 2, 14),
(58, 2, 15),
(59, 2, 16),
(60, 2, 19),
(87, 2, 21),
(88, 2, 22),
(89, 2, 24),
(90, 2, 25),
(91, 2, 26),
(92, 2, 27),
(93, 2, 28),
(83, 2, 31),
(84, 2, 32),
(85, 2, 34),
(86, 2, 38),
(212, 2, 41),
(213, 2, 42),
(214, 2, 44),
(62, 2, 61),
(63, 2, 62),
(64, 2, 64),
(65, 2, 67),
(66, 2, 68),
(158, 2, 71),
(159, 2, 72),
(160, 2, 74),
(161, 2, 75),
(162, 2, 76),
(163, 2, 78),
(164, 2, 79),
(21, 2, 81),
(22, 2, 82),
(23, 2, 84),
(24, 2, 86),
(25, 2, 87),
(26, 2, 88),
(27, 2, 89),
(112, 2, 91),
(113, 2, 92),
(114, 2, 93),
(115, 2, 94),
(29, 2, 95),
(30, 2, 96),
(31, 2, 97),
(32, 2, 98),
(40, 2, 101),
(41, 2, 102),
(42, 2, 104),
(43, 2, 105),
(44, 2, 106),
(45, 2, 109),
(7, 2, 111),
(8, 2, 112),
(9, 2, 113),
(10, 2, 114),
(11, 2, 115),
(12, 2, 116),
(13, 2, 117),
(52, 2, 120),
(98, 2, 121),
(99, 2, 122),
(100, 2, 125),
(101, 2, 126),
(53, 2, 137),
(215, 2, 141),
(216, 2, 142),
(217, 2, 144),
(14, 2, 151),
(15, 2, 154),
(16, 2, 155),
(17, 2, 156),
(33, 2, 171),
(34, 2, 172),
(35, 2, 173),
(36, 2, 178),
(18, 2, 241),
(19, 2, 242),
(20, 2, 243),
(116, 2, 251),
(117, 2, 252),
(118, 2, 253),
(119, 2, 254),
(120, 2, 255),
(121, 2, 256),
(102, 2, 262),
(103, 2, 281),
(104, 2, 282),
(105, 2, 283),
(106, 2, 286),
(180, 2, 331),
(181, 2, 332),
(182, 2, 333),
(122, 2, 341),
(123, 2, 342),
(124, 2, 343),
(125, 2, 344),
(126, 2, 351),
(127, 2, 352),
(128, 2, 353),
(129, 2, 354),
(130, 2, 358),
(94, 2, 531),
(95, 2, 532),
(96, 2, 534),
(97, 2, 538),
(107, 2, 1001),
(108, 2, 1002),
(109, 2, 1003),
(110, 2, 1004),
(111, 2, 1005),
(46, 2, 1101),
(47, 2, 1102),
(48, 2, 1104),
(49, 2, 1109),
(67, 2, 1181),
(68, 2, 1182),
(69, 2, 1183),
(70, 2, 1184),
(71, 2, 1185),
(72, 2, 1186),
(73, 2, 1187),
(74, 2, 1188),
(50, 2, 1201),
(51, 2, 1202),
(75, 2, 1231),
(76, 2, 1232),
(77, 2, 1233),
(78, 2, 1234),
(79, 2, 1235),
(80, 2, 1236),
(81, 2, 1237),
(82, 2, 1251),
(61, 2, 1321),
(28, 2, 1421),
(1, 2, 2401),
(2, 2, 2402),
(3, 2, 2403),
(4, 2, 2411),
(5, 2, 2412),
(6, 2, 2413),
(37, 2, 2501),
(38, 2, 2503),
(39, 2, 2515),
(131, 2, 12800),
(132, 2, 12801),
(133, 2, 12802),
(134, 2, 12803),
(135, 2, 12804),
(136, 2, 12805),
(137, 2, 12806),
(138, 2, 12807),
(139, 2, 12808),
(140, 2, 12809),
(206, 2, 20001),
(207, 2, 20002),
(208, 2, 20003),
(209, 2, 20004),
(210, 2, 20005),
(211, 2, 20006),
(165, 2, 20120),
(166, 2, 20121),
(167, 2, 20130),
(168, 2, 20131),
(169, 2, 20140),
(170, 2, 20141),
(171, 2, 20142),
(172, 2, 20160),
(173, 2, 20161),
(174, 2, 20162),
(175, 2, 20163),
(176, 2, 20164),
(177, 2, 20165),
(178, 2, 20170),
(179, 2, 20171),
(196, 2, 20321),
(197, 2, 20322),
(198, 2, 20323),
(199, 2, 20324),
(200, 2, 20340),
(201, 2, 20341),
(202, 2, 20342),
(203, 2, 20343),
(204, 2, 20344),
(205, 2, 20345),
(183, 2, 20401),
(184, 2, 20402),
(185, 2, 20403),
(186, 2, 20410),
(187, 2, 20411),
(188, 2, 20412),
(189, 2, 20413),
(190, 2, 20420),
(191, 2, 20421),
(192, 2, 20422),
(193, 2, 20430),
(194, 2, 20431),
(195, 2, 20432),
(141, 2, 59101),
(218, 2, 59201),
(219, 2, 59202),
(220, 2, 59203),
(221, 2, 59204),
(222, 2, 59205);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_usergroup_user`
--

CREATE TABLE IF NOT EXISTS `llx_usergroup_user` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_user` int(11) NOT NULL,
  `fk_usergroup` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_usergroup_user` (`entity`,`fk_user`,`fk_usergroup`),
  KEY `fk_usergroup_user_fk_user` (`fk_user`),
  KEY `fk_usergroup_user_fk_usergroup` (`fk_usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `llx_usergroup_user`
--

INSERT INTO `llx_usergroup_user` (`rowid`, `entity`, `fk_user`, `fk_usergroup`) VALUES
(6, 1, 2, 2),
(7, 1, 3, 2),
(8, 1, 4, 2),
(5, 1, 5, 1),
(1, 1, 6, 1),
(4, 1, 7, 1),
(2, 1, 8, 1),
(3, 1, 9, 1),
(9, 1, 10, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user_alert`
--

CREATE TABLE IF NOT EXISTS `llx_user_alert` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user_clicktodial`
--

CREATE TABLE IF NOT EXISTS `llx_user_clicktodial` (
  `fk_user` int(11) NOT NULL,
  `login` varchar(32) DEFAULT NULL,
  `pass` varchar(64) DEFAULT NULL,
  `poste` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user_extrafields`
--

CREATE TABLE IF NOT EXISTS `llx_user_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_user_extrafields` (`fk_object`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `llx_user_extrafields`
--

INSERT INTO `llx_user_extrafields` (`rowid`, `tms`, `fk_object`, `import_key`) VALUES
(1, '2013-04-10 23:30:46', 10, NULL),
(3, '2013-04-11 23:01:40', 12, NULL),
(4, '2013-04-15 22:51:44', 2, NULL),
(6, '2013-04-15 23:05:25', 14, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user_param`
--

CREATE TABLE IF NOT EXISTS `llx_user_param` (
  `fk_user` int(11) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `param` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  UNIQUE KEY `uk_user_param` (`fk_user`,`param`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `llx_user_param`
--

INSERT INTO `llx_user_param` (`fk_user`, `entity`, `param`, `value`) VALUES
(1, 1, 'MAIN_BOXES_0', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_user_rights`
--

CREATE TABLE IF NOT EXISTS `llx_user_rights` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `fk_id` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_user_rights` (`fk_user`,`fk_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2642 ;

--
-- Volcado de datos para la tabla `llx_user_rights`
--

INSERT INTO `llx_user_rights` (`rowid`, `fk_user`, `fk_id`) VALUES
(2440, 1, 11),
(2433, 1, 12),
(2434, 1, 13),
(2436, 1, 14),
(2437, 1, 15),
(2439, 1, 16),
(2441, 1, 19),
(1327, 1, 21),
(1319, 1, 22),
(1321, 1, 24),
(1322, 1, 25),
(1324, 1, 26),
(1326, 1, 27),
(1328, 1, 28),
(2463, 1, 31),
(2460, 1, 32),
(2462, 1, 34),
(2464, 1, 38),
(2087, 1, 41),
(2086, 1, 42),
(2088, 1, 44),
(2470, 1, 61),
(2467, 1, 62),
(2469, 1, 64),
(2471, 1, 67),
(2472, 1, 68),
(1927, 1, 71),
(1922, 1, 72),
(1924, 1, 74),
(1928, 1, 75),
(1926, 1, 76),
(1930, 1, 78),
(1931, 1, 79),
(2514, 1, 81),
(2506, 1, 82),
(2508, 1, 84),
(2509, 1, 86),
(2511, 1, 87),
(2513, 1, 88),
(2515, 1, 89),
(306, 1, 91),
(303, 1, 92),
(305, 1, 93),
(307, 1, 94),
(249, 1, 95),
(250, 1, 96),
(252, 1, 97),
(253, 1, 98),
(2495, 1, 101),
(2490, 1, 102),
(2492, 1, 104),
(2493, 1, 105),
(2494, 1, 106),
(2496, 1, 109),
(2429, 1, 111),
(2420, 1, 112),
(2422, 1, 113),
(2424, 1, 114),
(2426, 1, 115),
(2428, 1, 116),
(2430, 1, 117),
(609, 1, 120),
(2522, 1, 121),
(2519, 1, 122),
(2521, 1, 125),
(2523, 1, 126),
(610, 1, 137),
(2092, 1, 141),
(2091, 1, 142),
(2093, 1, 144),
(619, 1, 151),
(620, 1, 154),
(621, 1, 155),
(618, 1, 156),
(606, 1, 161),
(607, 1, 164),
(2367, 1, 171),
(2364, 1, 172),
(2366, 1, 173),
(2368, 1, 178),
(145, 1, 241),
(144, 1, 242),
(146, 1, 243),
(2360, 1, 251),
(2341, 1, 252),
(2343, 1, 253),
(2344, 1, 254),
(2346, 1, 255),
(2348, 1, 256),
(2524, 1, 262),
(2530, 1, 281),
(2527, 1, 282),
(2529, 1, 283),
(2531, 1, 286),
(150, 1, 300),
(149, 1, 301),
(151, 1, 302),
(1683, 1, 331),
(1682, 1, 332),
(1684, 1, 333),
(2349, 1, 341),
(2350, 1, 342),
(2351, 1, 343),
(2352, 1, 344),
(2358, 1, 351),
(2355, 1, 352),
(2357, 1, 353),
(2359, 1, 354),
(2361, 1, 358),
(2287, 1, 531),
(2284, 1, 532),
(2286, 1, 534),
(2288, 1, 538),
(560, 1, 1001),
(559, 1, 1002),
(561, 1, 1003),
(563, 1, 1004),
(564, 1, 1005),
(2502, 1, 1101),
(2499, 1, 1102),
(2501, 1, 1104),
(2503, 1, 1109),
(2314, 1, 1181),
(2338, 1, 1182),
(2317, 1, 1183),
(2319, 1, 1184),
(2321, 1, 1185),
(2323, 1, 1186),
(2325, 1, 1187),
(2327, 1, 1188),
(316, 1, 1201),
(317, 1, 1202),
(2336, 1, 1231),
(2330, 1, 1232),
(2332, 1, 1233),
(2334, 1, 1234),
(2335, 1, 1235),
(2337, 1, 1236),
(2339, 1, 1237),
(318, 1, 1251),
(2442, 1, 1321),
(2516, 1, 1421),
(2260, 1, 2401),
(2259, 1, 2402),
(2261, 1, 2403),
(2265, 1, 2411),
(2264, 1, 2412),
(2266, 1, 2413),
(2372, 1, 2501),
(2371, 1, 2503),
(2373, 1, 2515),
(489, 1, 12800),
(490, 1, 12801),
(491, 1, 12802),
(492, 1, 12803),
(493, 1, 12804),
(494, 1, 12805),
(495, 1, 12806),
(496, 1, 12807),
(497, 1, 12808),
(498, 1, 12809),
(1914, 1, 20001),
(1915, 1, 20002),
(1916, 1, 20003),
(1917, 1, 20004),
(1918, 1, 20005),
(1919, 1, 20006),
(2532, 1, 20120),
(2533, 1, 20121),
(2534, 1, 20130),
(2536, 1, 20131),
(2537, 1, 20140),
(2538, 1, 20141),
(2539, 1, 20142),
(1786, 1, 20151),
(1787, 1, 20154),
(1788, 1, 20155),
(1789, 1, 20156),
(1790, 1, 20157),
(1791, 1, 20158),
(1792, 1, 20159),
(2540, 1, 20160),
(2541, 1, 20161),
(2542, 1, 20162),
(2543, 1, 20163),
(2544, 1, 20164),
(2545, 1, 20165),
(1799, 1, 20166),
(1800, 1, 20167),
(1801, 1, 20168),
(1802, 1, 20169),
(2546, 1, 20170),
(2547, 1, 20171),
(2374, 1, 20321),
(2375, 1, 20322),
(2376, 1, 20323),
(2377, 1, 20324),
(2378, 1, 20340),
(2379, 1, 20341),
(2380, 1, 20342),
(2381, 1, 20343),
(2382, 1, 20344),
(2383, 1, 20345),
(2626, 1, 20401),
(2627, 1, 20402),
(2628, 1, 20403),
(2629, 1, 20410),
(2630, 1, 20411),
(2631, 1, 20412),
(2632, 1, 20413),
(2633, 1, 20420),
(2634, 1, 20421),
(2635, 1, 20422),
(2636, 1, 20430),
(2637, 1, 20431),
(2638, 1, 20432),
(2639, 1, 20440),
(2640, 1, 20441),
(2641, 1, 20442),
(329, 1, 59101),
(2413, 1, 59201),
(2414, 1, 59202),
(2415, 1, 59203),
(2416, 1, 59204),
(2417, 1, 59205),
(622, 2, 11),
(623, 2, 21),
(624, 2, 31),
(625, 2, 61),
(660, 2, 81),
(661, 2, 82),
(627, 2, 91),
(628, 2, 95),
(629, 2, 97),
(630, 2, 101),
(631, 2, 111),
(632, 2, 120),
(1370, 2, 121),
(1371, 2, 122),
(1372, 2, 125),
(1373, 2, 126),
(634, 2, 137),
(635, 2, 151),
(636, 2, 154),
(637, 2, 155),
(638, 2, 156),
(639, 2, 171),
(1364, 2, 241),
(1365, 2, 242),
(1366, 2, 243),
(1374, 2, 262),
(1375, 2, 281),
(1376, 2, 282),
(1377, 2, 283),
(1378, 2, 286),
(643, 2, 341),
(644, 2, 342),
(645, 2, 343),
(646, 2, 344),
(647, 2, 531),
(648, 2, 1001),
(649, 2, 1004),
(650, 2, 1101),
(651, 2, 1181),
(652, 2, 1182),
(653, 2, 1201),
(654, 2, 1231),
(1637, 2, 2401),
(1638, 2, 2402),
(1639, 2, 2403),
(1640, 2, 2411),
(1641, 2, 2412),
(1642, 2, 2413),
(1367, 2, 2501),
(1368, 2, 2503),
(1369, 2, 2515),
(1351, 2, 20151),
(1352, 2, 20154),
(1353, 2, 20155),
(1354, 2, 20156),
(1355, 2, 20157),
(1356, 2, 20158),
(1357, 2, 20159),
(1358, 2, 20160),
(1359, 2, 20161),
(1360, 2, 20162),
(1361, 2, 20163),
(1362, 2, 20164),
(1363, 2, 20165),
(1382, 2, 20321),
(1383, 2, 20322),
(1384, 2, 20323),
(1385, 2, 20324),
(659, 2, 59101),
(1379, 2, 59201),
(1380, 2, 59202),
(1381, 2, 59203),
(662, 3, 11),
(663, 3, 21),
(664, 3, 31),
(665, 3, 61),
(666, 3, 81),
(1433, 3, 91),
(1434, 3, 92),
(1435, 3, 93),
(1436, 3, 94),
(1406, 3, 95),
(1407, 3, 96),
(1408, 3, 97),
(1409, 3, 98),
(670, 3, 101),
(1399, 3, 111),
(1400, 3, 112),
(1401, 3, 113),
(1402, 3, 114),
(1403, 3, 115),
(1404, 3, 116),
(1405, 3, 117),
(672, 3, 120),
(1442, 3, 121),
(1443, 3, 122),
(1444, 3, 125),
(1445, 3, 126),
(674, 3, 137),
(675, 3, 151),
(676, 3, 154),
(677, 3, 155),
(678, 3, 156),
(679, 3, 171),
(680, 3, 241),
(1418, 3, 251),
(1419, 3, 252),
(1420, 3, 253),
(1421, 3, 254),
(1422, 3, 255),
(1423, 3, 256),
(1446, 3, 262),
(1447, 3, 281),
(1448, 3, 282),
(1449, 3, 283),
(1450, 3, 286),
(1424, 3, 341),
(1425, 3, 342),
(1426, 3, 343),
(1427, 3, 344),
(1428, 3, 351),
(1429, 3, 352),
(1430, 3, 353),
(1431, 3, 354),
(1432, 3, 358),
(687, 3, 531),
(1437, 3, 1001),
(1438, 3, 1002),
(1439, 3, 1003),
(1440, 3, 1004),
(1441, 3, 1005),
(690, 3, 1101),
(691, 3, 1181),
(692, 3, 1182),
(693, 3, 1201),
(694, 3, 1231),
(695, 3, 2401),
(696, 3, 2501),
(697, 3, 2503),
(698, 3, 2515),
(1451, 3, 20151),
(1452, 3, 20154),
(1453, 3, 20155),
(1454, 3, 20156),
(1455, 3, 20157),
(1456, 3, 20158),
(1457, 3, 20159),
(1458, 3, 20160),
(1459, 3, 20161),
(1460, 3, 20162),
(1461, 3, 20163),
(1462, 3, 20164),
(1463, 3, 20165),
(1643, 3, 20321),
(1644, 3, 20322),
(1645, 3, 20323),
(1646, 3, 20324),
(699, 3, 59101),
(1415, 3, 59201),
(1416, 3, 59202),
(1417, 3, 59203),
(700, 4, 11),
(701, 4, 21),
(702, 4, 31),
(703, 4, 61),
(704, 4, 81),
(705, 4, 91),
(706, 4, 95),
(707, 4, 97),
(708, 4, 101),
(709, 4, 111),
(710, 4, 120),
(711, 4, 121),
(712, 4, 137),
(713, 4, 151),
(714, 4, 154),
(715, 4, 155),
(716, 4, 156),
(717, 4, 171),
(718, 4, 241),
(719, 4, 262),
(720, 4, 281),
(721, 4, 341),
(722, 4, 342),
(723, 4, 343),
(724, 4, 344),
(725, 4, 531),
(726, 4, 1001),
(727, 4, 1004),
(728, 4, 1101),
(729, 4, 1181),
(730, 4, 1182),
(731, 4, 1201),
(732, 4, 1231),
(733, 4, 2401),
(734, 4, 2501),
(735, 4, 2503),
(736, 4, 2515),
(737, 4, 59101),
(738, 5, 11),
(739, 5, 21),
(740, 5, 31),
(741, 5, 61),
(742, 5, 81),
(743, 5, 91),
(744, 5, 95),
(745, 5, 97),
(746, 5, 101),
(747, 5, 111),
(748, 5, 120),
(749, 5, 121),
(750, 5, 137),
(751, 5, 151),
(752, 5, 154),
(753, 5, 155),
(754, 5, 156),
(755, 5, 171),
(756, 5, 241),
(757, 5, 262),
(758, 5, 281),
(759, 5, 341),
(760, 5, 342),
(761, 5, 343),
(762, 5, 344),
(763, 5, 531),
(764, 5, 1001),
(765, 5, 1004),
(766, 5, 1101),
(767, 5, 1181),
(768, 5, 1182),
(769, 5, 1201),
(770, 5, 1231),
(771, 5, 2401),
(772, 5, 2501),
(773, 5, 2503),
(774, 5, 2515),
(775, 5, 59101),
(776, 6, 11),
(777, 6, 21),
(778, 6, 31),
(779, 6, 61),
(780, 6, 81),
(781, 6, 91),
(782, 6, 95),
(783, 6, 97),
(784, 6, 101),
(785, 6, 111),
(786, 6, 120),
(787, 6, 121),
(788, 6, 137),
(789, 6, 151),
(790, 6, 154),
(791, 6, 155),
(792, 6, 156),
(793, 6, 171),
(794, 6, 241),
(795, 6, 262),
(796, 6, 281),
(797, 6, 341),
(798, 6, 342),
(799, 6, 343),
(800, 6, 344),
(801, 6, 531),
(802, 6, 1001),
(803, 6, 1004),
(804, 6, 1101),
(805, 6, 1181),
(806, 6, 1182),
(807, 6, 1201),
(808, 6, 1231),
(809, 6, 2401),
(810, 6, 2501),
(811, 6, 2503),
(812, 6, 2515),
(813, 6, 59101),
(814, 7, 11),
(815, 7, 21),
(816, 7, 31),
(817, 7, 61),
(818, 7, 81),
(819, 7, 91),
(820, 7, 95),
(821, 7, 97),
(822, 7, 101),
(823, 7, 111),
(824, 7, 120),
(825, 7, 121),
(826, 7, 137),
(827, 7, 151),
(828, 7, 154),
(829, 7, 155),
(830, 7, 156),
(831, 7, 171),
(832, 7, 241),
(833, 7, 262),
(834, 7, 281),
(835, 7, 341),
(836, 7, 342),
(837, 7, 343),
(838, 7, 344),
(839, 7, 531),
(840, 7, 1001),
(841, 7, 1004),
(842, 7, 1101),
(843, 7, 1181),
(844, 7, 1182),
(845, 7, 1201),
(846, 7, 1231),
(847, 7, 2401),
(848, 7, 2501),
(849, 7, 2503),
(850, 7, 2515),
(851, 7, 59101),
(852, 8, 11),
(853, 8, 21),
(854, 8, 31),
(855, 8, 61),
(856, 8, 81),
(857, 8, 91),
(858, 8, 95),
(859, 8, 97),
(860, 8, 101),
(861, 8, 111),
(862, 8, 120),
(863, 8, 121),
(864, 8, 137),
(865, 8, 151),
(866, 8, 154),
(867, 8, 155),
(868, 8, 156),
(869, 8, 171),
(870, 8, 241),
(871, 8, 262),
(872, 8, 281),
(873, 8, 341),
(874, 8, 342),
(875, 8, 343),
(876, 8, 344),
(877, 8, 531),
(878, 8, 1001),
(879, 8, 1004),
(880, 8, 1101),
(881, 8, 1181),
(882, 8, 1182),
(883, 8, 1201),
(884, 8, 1231),
(885, 8, 2401),
(886, 8, 2501),
(887, 8, 2503),
(888, 8, 2515),
(889, 8, 59101),
(2056, 9, 11),
(2055, 9, 12),
(2057, 9, 14),
(2053, 9, 31),
(900, 9, 120),
(2255, 9, 121),
(2256, 9, 122),
(902, 9, 137),
(903, 9, 151),
(904, 9, 154),
(905, 9, 155),
(906, 9, 156),
(2052, 9, 241),
(2252, 9, 262),
(2253, 9, 281),
(2254, 9, 282),
(927, 9, 59101),
(1805, 9, 59201),
(1806, 9, 59204),
(1859, 9, 59205),
(1052, 10, 11),
(1053, 10, 21),
(1054, 10, 31),
(1055, 10, 61),
(1056, 10, 81),
(1057, 10, 91),
(1058, 10, 95),
(1059, 10, 97),
(1060, 10, 101),
(1061, 10, 111),
(1062, 10, 121),
(1063, 10, 171),
(1064, 10, 241),
(1065, 10, 262),
(1066, 10, 281),
(1067, 10, 341),
(1068, 10, 342),
(1069, 10, 343),
(1070, 10, 344),
(1071, 10, 531),
(1072, 10, 1001),
(1073, 10, 1004),
(1074, 10, 1101),
(1075, 10, 1181),
(1076, 10, 1182),
(1077, 10, 1201),
(1078, 10, 1231),
(1079, 10, 2401),
(1080, 10, 2501),
(1081, 10, 2503),
(1082, 10, 2515),
(1083, 10, 20157),
(1084, 10, 20158),
(1085, 10, 20159),
(1086, 10, 20160),
(1087, 10, 20161),
(1088, 10, 20162),
(1089, 10, 20163),
(1090, 10, 59201),
(1091, 10, 59202),
(1092, 10, 59203),
(1176, 12, 11),
(1177, 12, 21),
(1178, 12, 31),
(1179, 12, 61),
(1180, 12, 81),
(1181, 12, 91),
(1182, 12, 95),
(1183, 12, 97),
(1184, 12, 101),
(1185, 12, 111),
(1186, 12, 121),
(1187, 12, 171),
(1188, 12, 241),
(1189, 12, 262),
(1190, 12, 281),
(1191, 12, 341),
(1192, 12, 342),
(1193, 12, 343),
(1194, 12, 344),
(1195, 12, 531),
(1196, 12, 1001),
(1197, 12, 1004),
(1198, 12, 1101),
(1199, 12, 1181),
(1200, 12, 1182),
(1201, 12, 1201),
(1202, 12, 1231),
(1203, 12, 2401),
(1204, 12, 2501),
(1205, 12, 2503),
(1206, 12, 2515),
(1207, 12, 20157),
(1208, 12, 20158),
(1209, 12, 20159),
(1210, 12, 20160),
(1211, 12, 20161),
(1212, 12, 20162),
(1213, 12, 20163),
(1214, 12, 20164),
(1215, 12, 20165),
(1216, 12, 59201),
(1604, 14, 11),
(1605, 14, 12),
(1606, 14, 13),
(1607, 14, 14),
(1608, 14, 15),
(1609, 14, 16),
(1610, 14, 19),
(1508, 14, 21),
(1509, 14, 31),
(1510, 14, 61),
(1576, 14, 81),
(1577, 14, 82),
(1578, 14, 84),
(1579, 14, 86),
(1580, 14, 87),
(1581, 14, 88),
(1582, 14, 89),
(1512, 14, 91),
(1584, 14, 95),
(1585, 14, 96),
(1586, 14, 97),
(1587, 14, 98),
(1592, 14, 101),
(1593, 14, 102),
(1594, 14, 104),
(1595, 14, 105),
(1596, 14, 106),
(1597, 14, 109),
(1569, 14, 111),
(1570, 14, 112),
(1571, 14, 113),
(1572, 14, 114),
(1573, 14, 115),
(1574, 14, 116),
(1575, 14, 117),
(1517, 14, 121),
(1588, 14, 171),
(1589, 14, 172),
(1590, 14, 173),
(1591, 14, 178),
(1519, 14, 241),
(1520, 14, 262),
(1521, 14, 281),
(1522, 14, 341),
(1523, 14, 342),
(1524, 14, 343),
(1525, 14, 344),
(1628, 14, 531),
(1629, 14, 532),
(1630, 14, 534),
(1631, 14, 538),
(1632, 14, 1001),
(1633, 14, 1002),
(1634, 14, 1003),
(1635, 14, 1004),
(1636, 14, 1005),
(1598, 14, 1101),
(1599, 14, 1102),
(1600, 14, 1104),
(1601, 14, 1109),
(1612, 14, 1181),
(1613, 14, 1182),
(1614, 14, 1183),
(1615, 14, 1184),
(1616, 14, 1185),
(1617, 14, 1186),
(1618, 14, 1187),
(1619, 14, 1188),
(1602, 14, 1201),
(1603, 14, 1202),
(1620, 14, 1231),
(1621, 14, 1232),
(1622, 14, 1233),
(1623, 14, 1234),
(1624, 14, 1235),
(1625, 14, 1236),
(1626, 14, 1237),
(1627, 14, 1251),
(1611, 14, 1321),
(1583, 14, 1421),
(1550, 14, 2401),
(1551, 14, 2402),
(1552, 14, 2403),
(1553, 14, 2411),
(1554, 14, 2412),
(1555, 14, 2413),
(1535, 14, 2501),
(1536, 14, 2503),
(1537, 14, 2515),
(1556, 14, 20151),
(1557, 14, 20154),
(1558, 14, 20155),
(1559, 14, 20156),
(1560, 14, 20157),
(1561, 14, 20158),
(1562, 14, 20159),
(1563, 14, 20160),
(1564, 14, 20161),
(1565, 14, 20162),
(1566, 14, 20163),
(1567, 14, 20164),
(1568, 14, 20165),
(1547, 14, 59201),
(1548, 14, 59202),
(1549, 14, 59203);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_accountingaccount`
--
ALTER TABLE `llx_accountingaccount`
  ADD CONSTRAINT `fk_accountingaccount_fk_pcg_version` FOREIGN KEY (`fk_pcg_version`) REFERENCES `llx_accounting_system` (`pcg_version`);

--
-- Filtros para la tabla `llx_adherent`
--
ALTER TABLE `llx_adherent`
  ADD CONSTRAINT `adherent_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_adherent_adherent_type` FOREIGN KEY (`fk_adherent_type`) REFERENCES `llx_adherent_type` (`rowid`);

--
-- Filtros para la tabla `llx_boxes`
--
ALTER TABLE `llx_boxes`
  ADD CONSTRAINT `fk_boxes_box_id` FOREIGN KEY (`box_id`) REFERENCES `llx_boxes_def` (`rowid`);

--
-- Filtros para la tabla `llx_categorie_fournisseur`
--
ALTER TABLE `llx_categorie_fournisseur`
  ADD CONSTRAINT `fk_categorie_fournisseur_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`),
  ADD CONSTRAINT `fk_categorie_fournisseur_fk_soc` FOREIGN KEY (`fk_societe`) REFERENCES `llx_societe` (`rowid`);

--
-- Filtros para la tabla `llx_categorie_member`
--
ALTER TABLE `llx_categorie_member`
  ADD CONSTRAINT `fk_categorie_member_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`),
  ADD CONSTRAINT `fk_categorie_member_member_rowid` FOREIGN KEY (`fk_member`) REFERENCES `llx_adherent` (`rowid`);

--
-- Filtros para la tabla `llx_categorie_product`
--
ALTER TABLE `llx_categorie_product`
  ADD CONSTRAINT `fk_categorie_product_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`),
  ADD CONSTRAINT `fk_categorie_product_product_rowid` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`);

--
-- Filtros para la tabla `llx_categorie_societe`
--
ALTER TABLE `llx_categorie_societe`
  ADD CONSTRAINT `fk_categorie_societe_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`),
  ADD CONSTRAINT `fk_categorie_societe_fk_soc` FOREIGN KEY (`fk_societe`) REFERENCES `llx_societe` (`rowid`);

--
-- Filtros para la tabla `llx_commande`
--
ALTER TABLE `llx_commande`
  ADD CONSTRAINT `fk_commande_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_commande_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_commande_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_commande_fk_user_cloture` FOREIGN KEY (`fk_user_cloture`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_commande_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_commandedet`
--
ALTER TABLE `llx_commandedet`
  ADD CONSTRAINT `fk_commandedet_fk_commande` FOREIGN KEY (`fk_commande`) REFERENCES `llx_commande` (`rowid`);

--
-- Filtros para la tabla `llx_commande_fournisseur`
--
ALTER TABLE `llx_commande_fournisseur`
  ADD CONSTRAINT `fk_commande_fournisseur_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`);

--
-- Filtros para la tabla `llx_contrat`
--
ALTER TABLE `llx_contrat`
  ADD CONSTRAINT `fk_contrat_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_contrat_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_contratdet`
--
ALTER TABLE `llx_contratdet`
  ADD CONSTRAINT `fk_contratdet_fk_contrat` FOREIGN KEY (`fk_contrat`) REFERENCES `llx_contrat` (`rowid`),
  ADD CONSTRAINT `fk_contratdet_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`);

--
-- Filtros para la tabla `llx_contratdet_log`
--
ALTER TABLE `llx_contratdet_log`
  ADD CONSTRAINT `fk_contratdet_log_fk_contratdet` FOREIGN KEY (`fk_contratdet`) REFERENCES `llx_contratdet` (`rowid`);

--
-- Filtros para la tabla `llx_c_regions`
--
ALTER TABLE `llx_c_regions`
  ADD CONSTRAINT `fk_c_regions_fk_pays` FOREIGN KEY (`fk_pays`) REFERENCES `llx_c_pays` (`rowid`);

--
-- Filtros para la tabla `llx_c_ziptown`
--
ALTER TABLE `llx_c_ziptown`
  ADD CONSTRAINT `fk_c_ziptown_fk_county` FOREIGN KEY (`fk_county`) REFERENCES `llx_c_departements` (`rowid`),
  ADD CONSTRAINT `fk_c_ziptown_fk_pays` FOREIGN KEY (`fk_pays`) REFERENCES `llx_c_pays` (`rowid`);

--
-- Filtros para la tabla `llx_ecm_directories`
--
ALTER TABLE `llx_ecm_directories`
  ADD CONSTRAINT `fk_ecm_directories_fk_user_c` FOREIGN KEY (`fk_user_c`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_ecm_directories_fk_user_m` FOREIGN KEY (`fk_user_m`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_element_contact`
--
ALTER TABLE `llx_element_contact`
  ADD CONSTRAINT `fk_element_contact_fk_c_type_contact` FOREIGN KEY (`fk_c_type_contact`) REFERENCES `llx_c_type_contact` (`rowid`);

--
-- Filtros para la tabla `llx_expedition`
--
ALTER TABLE `llx_expedition`
  ADD CONSTRAINT `fk_expedition_fk_expedition_methode` FOREIGN KEY (`fk_expedition_methode`) REFERENCES `llx_c_shipment_mode` (`rowid`),
  ADD CONSTRAINT `fk_expedition_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_expedition_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_expedition_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_expeditiondet`
--
ALTER TABLE `llx_expeditiondet`
  ADD CONSTRAINT `fk_expeditiondet_fk_expedition` FOREIGN KEY (`fk_expedition`) REFERENCES `llx_expedition` (`rowid`);

--
-- Filtros para la tabla `llx_facture`
--
ALTER TABLE `llx_facture`
  ADD CONSTRAINT `fk_facture_fk_facture_source` FOREIGN KEY (`fk_facture_source`) REFERENCES `llx_facture` (`rowid`),
  ADD CONSTRAINT `fk_facture_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_facture_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_facture_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_facture_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_facturedet`
--
ALTER TABLE `llx_facturedet`
  ADD CONSTRAINT `fk_facturedet_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`);

--
-- Filtros para la tabla `llx_facture_fourn`
--
ALTER TABLE `llx_facture_fourn`
  ADD CONSTRAINT `fk_facture_fourn_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_facture_fourn_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_facture_fourn_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_facture_fourn_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_facture_fourn_det`
--
ALTER TABLE `llx_facture_fourn_det`
  ADD CONSTRAINT `fk_facture_fourn_det_fk_facture` FOREIGN KEY (`fk_facture_fourn`) REFERENCES `llx_facture_fourn` (`rowid`);

--
-- Filtros para la tabla `llx_facture_rec`
--
ALTER TABLE `llx_facture_rec`
  ADD CONSTRAINT `fk_facture_rec_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_facture_rec_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_facture_rec_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_fichinter`
--
ALTER TABLE `llx_fichinter`
  ADD CONSTRAINT `fk_fichinter_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`);

--
-- Filtros para la tabla `llx_fichinterdet`
--
ALTER TABLE `llx_fichinterdet`
  ADD CONSTRAINT `fk_fichinterdet_fk_fichinter` FOREIGN KEY (`fk_fichinter`) REFERENCES `llx_fichinter` (`rowid`);

--
-- Filtros para la tabla `llx_livraison`
--
ALTER TABLE `llx_livraison`
  ADD CONSTRAINT `fk_livraison_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_livraison_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_livraison_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_livraisondet`
--
ALTER TABLE `llx_livraisondet`
  ADD CONSTRAINT `fk_livraisondet_fk_livraison` FOREIGN KEY (`fk_livraison`) REFERENCES `llx_livraison` (`rowid`);

--
-- Filtros para la tabla `llx_paiement_facture`
--
ALTER TABLE `llx_paiement_facture`
  ADD CONSTRAINT `fk_paiement_facture_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`),
  ADD CONSTRAINT `fk_paiement_facture_fk_paiement` FOREIGN KEY (`fk_paiement`) REFERENCES `llx_paiement` (`rowid`);

--
-- Filtros para la tabla `llx_prelevement_facture`
--
ALTER TABLE `llx_prelevement_facture`
  ADD CONSTRAINT `fk_prelevement_facture_fk_prelevement_lignes` FOREIGN KEY (`fk_prelevement_lignes`) REFERENCES `llx_prelevement_lignes` (`rowid`);

--
-- Filtros para la tabla `llx_prelevement_lignes`
--
ALTER TABLE `llx_prelevement_lignes`
  ADD CONSTRAINT `fk_prelevement_lignes_fk_prelevement_bons` FOREIGN KEY (`fk_prelevement_bons`) REFERENCES `llx_prelevement_bons` (`rowid`);

--
-- Filtros para la tabla `llx_product_fournisseur_price`
--
ALTER TABLE `llx_product_fournisseur_price`
  ADD CONSTRAINT `fk_product_fournisseur_price_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`),
  ADD CONSTRAINT `fk_product_fournisseur_price_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_product_lang`
--
ALTER TABLE `llx_product_lang`
  ADD CONSTRAINT `fk_product_lang_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`);

--
-- Filtros para la tabla `llx_product_price_by_qty`
--
ALTER TABLE `llx_product_price_by_qty`
  ADD CONSTRAINT `fk_product_price_by_qty_fk_product_price` FOREIGN KEY (`fk_product_price`) REFERENCES `llx_product_price` (`rowid`);

--
-- Filtros para la tabla `llx_projet`
--
ALTER TABLE `llx_projet`
  ADD CONSTRAINT `fk_projet_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`);

--
-- Filtros para la tabla `llx_projet_task`
--
ALTER TABLE `llx_projet_task`
  ADD CONSTRAINT `fk_projet_task_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_projet_task_fk_user_creat` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_projet_task_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_propal`
--
ALTER TABLE `llx_propal`
  ADD CONSTRAINT `fk_propal_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  ADD CONSTRAINT `fk_propal_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_propal_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_propal_fk_user_cloture` FOREIGN KEY (`fk_user_cloture`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_propal_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_propaldet`
--
ALTER TABLE `llx_propaldet`
  ADD CONSTRAINT `fk_propaldet_fk_propal` FOREIGN KEY (`fk_propal`) REFERENCES `llx_propal` (`rowid`);

--
-- Filtros para la tabla `llx_societe_remise_except`
--
ALTER TABLE `llx_societe_remise_except`
  ADD CONSTRAINT `fk_societe_remise_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`),
  ADD CONSTRAINT `fk_societe_remise_fk_facture_line` FOREIGN KEY (`fk_facture_line`) REFERENCES `llx_facturedet` (`rowid`),
  ADD CONSTRAINT `fk_societe_remise_fk_facture_source` FOREIGN KEY (`fk_facture_source`) REFERENCES `llx_facture` (`rowid`),
  ADD CONSTRAINT `fk_societe_remise_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_societe_remise_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_socpeople`
--
ALTER TABLE `llx_socpeople`
  ADD CONSTRAINT `fk_socpeople_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_socpeople_user_creat_user_rowid` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`);

--
-- Filtros para la tabla `llx_usergroup_rights`
--
ALTER TABLE `llx_usergroup_rights`
  ADD CONSTRAINT `fk_usergroup_rights_fk_usergroup` FOREIGN KEY (`fk_usergroup`) REFERENCES `llx_usergroup` (`rowid`);

--
-- Filtros para la tabla `llx_usergroup_user`
--
ALTER TABLE `llx_usergroup_user`
  ADD CONSTRAINT `fk_usergroup_user_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`),
  ADD CONSTRAINT `fk_usergroup_user_fk_usergroup` FOREIGN KEY (`fk_usergroup`) REFERENCES `llx_usergroup` (`rowid`);

--
-- Filtros para la tabla `llx_user_rights`
--
ALTER TABLE `llx_user_rights`
  ADD CONSTRAINT `fk_user_rights_fk_user_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
