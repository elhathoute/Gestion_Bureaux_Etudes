-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 15 mai 2023 à 23:30
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `beplandb`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAllDossierByBroker` (IN `broker_id` INT)   SELECT devis.id AS devis_id,devis.id_client AS client_id,devis.number, devis.objet, detail_devis.service_name,detail_devis.id AS service_id,
devis.date_creation, detail_devis.approved
FROM devis INNER JOIN detail_devis ON devis.id = detail_devis.id_devis INNER JOIN broker_devis ON 
broker_devis.id_devis = devis.id 
WHERE broker_devis.id_broker = broker_id AND detail_devis.approved=1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllDossier` ()   SELECT devis.id AS devis_id,devis.id_client AS client_id,devis.number, devis.objet, detail_devis.service_name,dossier.date,detail_devis.id AS service_id
FROM devis INNER JOIN detail_devis ON devis.id=detail_devis.id_devis INNER JOIN dossier ON detail_devis.id = dossier.id_service$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllUsers` (IN `user_id` INT)   SELECT users.id,users.prenom,users.nom, roles.role_name,users.status,users.last_login
FROM `users` JOIN `user_role` ON users.id = user_role.user_id JOIN roles on user_role.role_id=roles.id
WHERE users.id <> 1 AND users.id <> user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevis` ()   SELECT devis.id,client.id AS client_id,devis.number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,devis.date_creation,devis.net_total,devis.status,devis.type,devis.client_approve
FROM devis INNER JOIN client ON devis.id_client=client.id
WHERE devis.remove=0
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByBroker` (IN `broker_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,detail_devis.service_name,detail_devis.ref,
    IF(devis.remove_tva=0,ROUND(detail_devis.prix*0.2+detail_devis.prix,2),detail_devis.prix) AS srv_prix,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE detail_devis.id=devis_payments.id_devis AND devis_payments.pending=0),0) AS solde
FROM devis INNER JOIN client ON devis.id_client=client.id INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
INNER JOIN broker_devis ON devis.id = broker_devis.id_devis
WHERE devis.remove=0 AND broker_devis.id_broker= broker_id AND detail_devis.paid_srv = 0
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByClient` (IN `client_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,detail_devis.service_name,detail_devis.ref,
    IF(devis.remove_tva=0,ROUND(detail_devis.prix*0.2+detail_devis.prix,2),detail_devis.prix) AS srv_prix,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE detail_devis.id=devis_payments.id_devis AND devis_payments.pending=0),0) AS solde
FROM devis INNER JOIN client ON devis.id_client=client.id INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
WHERE devis.remove=0 AND devis.id_client= client_id AND detail_devis.paid_srv = 0
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPaymentInfo` ()   SELECT devis.id,devis_payments.id AS pay_id,
devis_payments.user_id,devis_payments.pay_method, devis.number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis_payments.pay_date,devis_payments.prix
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
INNER JOIN devis_payments 
ON detail_devis.id = devis_payments.id_devis
WHERE devis.remove=0 AND devis_payments.pending = 0
ORDER BY devis_payments.pay_date DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisReceipt` (IN `payment_id` INT)   SELECT devis.number,receipt.R_number, devis_payments.pay_method,detail_devis.service_name,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis_payments.pay_date,devis_payments.prix,receipt.pay_giver,devis.objet
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
INNER JOIN devis_payments ON detail_devis.id = devis_payments.id_devis
INNER JOIN receipt ON devis_payments.id = receipt.id_payment
WHERE devis_payments.id = payment_id 
ORDER BY devis_payments.pay_date DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisSituation` (IN `cl_id` INT)   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis.objet,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE devis_payments.id_devis = detail_devis.id AND devis_payments.pending=0),0) AS avance,
detail_devis.paid_srv
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
WHERE devis.remove=0 AND devis.id_client = cl_id 
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisSituationBoth` (IN `cl_id` INT, IN `srv_status` INT, IN `srv_name` VARCHAR(100))   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis.objet,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE devis_payments.id_devis = detail_devis.id AND devis_payments.pending=0),0) AS avance,
detail_devis.paid_srv
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
WHERE devis.remove=0 AND devis.id_client = cl_id AND detail_devis.service_name = srv_name AND detail_devis.paid_srv = srv_status
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisSituationSrv` (IN `cl_id` INT, IN `srv_name` VARCHAR(100))   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis.objet,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE devis_payments.id_devis = detail_devis.id AND devis_payments.pending=0),0) AS avance,
detail_devis.paid_srv
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
WHERE devis.remove=0 AND devis.id_client = cl_id AND detail_devis.service_name = srv_name
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisSituationStatus` (IN `cl_id` INT, IN `srv_status` INT)   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
devis.objet,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE devis_payments.id_devis = detail_devis.id AND devis_payments.pending=0),0) AS avance,
detail_devis.paid_srv
FROM devis INNER JOIN client ON devis.id_client=client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
WHERE devis.remove=0 AND devis.id_client = cl_id AND detail_devis.paid_srv = srv_status
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDossierDetail` (IN `detailDevis_id` INT)   SELECT detail_devis.id,detail_devis.ref,detail_devis.service_name, detail_devis.prix,
devis.objet
FROM detail_devis INNER JOIN devis ON detail_devis.id_devis=devis.id
WHERE detail_devis.id = detailDevis_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getInvoice` ()   SELECT invoice.id,client.id AS client_id,invoice.F_number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,invoice.objet,invoice.date_creation,invoice.net_total,invoice.status,invoice.type,
    invoice.paid_inv,IFNULL((SELECT SUM(invoice_payments.prix)
                           FROM invoice_payments 
                           WHERE invoice.id=invoice_payments.id_invoice
                            AND invoice_payments.pending=0),0) AS solde
FROM invoice INNER JOIN client ON invoice.id_client=client.id 
WHERE invoice.remove=0 
ORDER BY invoice.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getInvoiceNotifications` ()   select user_invoice.*,invoice.* from invoice 
INNER JOIN user_invoice on user_invoice.id_invoice=invoice.id
where type="encours" and remove=0
GROUP by user_invoice.id_user,user_invoice.id_invoice,invoice.id
ORDER by invoice.date_creation DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getInvPayByClient` (IN `client_id` INT)   SELECT invoice.id, invoice.F_number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,invoice.date_creation,invoice.net_total,invoice.objet,IFNULL(
(SELECT SUM(invoice_payments.prix)
FROM invoice_payments 
WHERE invoice.id=invoice_payments.id_invoice AND invoice_payments.pending=0),0) AS solde
FROM invoice INNER JOIN client ON invoice.id_client=client.id 
WHERE invoice.remove=0 AND invoice.paid_inv=0 AND invoice.id_client= client_id AND invoice.type="Approved"
ORDER BY invoice.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getNotifications` ()   SELECT user_devis.*, devis.*
FROM devis
INNER JOIN user_devis ON user_devis.id_devis = devis.id
INNER JOIN user_role ON user_role.user_id = user_devis.id_user
INNER JOIN roles ON roles.id = user_role.role_id
WHERE devis.type = "encours" AND devis.remove = 0
  AND (user_devis.action = "update" OR (user_devis.action = "add" AND roles.role_name = "assistant"))
GROUP BY user_devis.id_user, user_devis.id_devis, devis.id
ORDER BY devis.date_creation DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getPaymentInfo` ()   SELECT invoice.id,invoice_payments.id AS pay_id,
invoice_payments.user_id,invoice_payments.pay_method, invoice.F_number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
invoice_payments.pay_date,invoice_payments.prix
FROM invoice INNER JOIN client ON invoice.id_client=client.id
INNER JOIN invoice_payments 
ON invoice.id = invoice_payments.id_invoice
WHERE invoice.remove=0  AND invoice.type="Approved" AND invoice_payments.pending = 0
ORDER BY invoice_payments.pay_date DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getPaymentNotification` ()   SELECT devis_payments.id AS pay_id, devis_payments.user_id, devis.id AS id_devis,
devis.id_client,devis.number,devis_payments.pay_date,detail_devis.id AS detail_id
FROM devis INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
INNER JOIN devis_payments ON detail_devis.id= devis_payments.id_devis
WHERE devis.remove=0 AND devis_payments.pending= 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getReceipt` (IN `payment_id` INT)   SELECT invoice.F_number,receipt.R_number, invoice_payments.pay_method,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
invoice_payments.pay_date,invoice_payments.prix,receipt.pay_giver,invoice.objet
FROM invoice INNER JOIN client ON invoice.id_client=client.id
INNER JOIN invoice_payments ON invoice.id = invoice_payments.id_invoice
INNER JOIN receipt ON invoice_payments.id = receipt.id_payment
WHERE invoice_payments.id = payment_id
ORDER BY invoice_payments.pay_date DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getSelectedDossier` (IN `service_id` INT)   SELECT detail_devis.ref, dossier.N_dossier,devis.objet, detail_devis.service_name,detail_devis.prix
FROM devis INNER JOIN detail_devis ON devis.id=detail_devis.id_devis INNER JOIN 
dossier ON detail_devis.id = dossier.id_service
WHERE detail_devis.id = service_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getSituation` (IN `cl_id` INT)   SELECT invoice.id, invoice.F_number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',upper(client_individual.nom))AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT upper(client_entreprise.nom) FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,
invoice.objet,
invoice.net_total,IFNULL(
(SELECT SUM(invoice_payments.prix)
FROM invoice_payments 
WHERE invoice_payments.id_invoice = invoice.id AND invoice_payments.pending=0),0) AS avance,
invoice.paid_inv
FROM invoice INNER JOIN client ON invoice.id_client=client.id
WHERE invoice.remove=0 AND invoice.id_client = cl_id AND invoice.type="Approved" 
ORDER BY invoice.date_creation$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `broker`
--

CREATE TABLE `broker` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `sold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `broker`
--

INSERT INTO `broker` (`id`, `nom`, `prenom`, `phone`, `address`, `sold`) VALUES
(1, 'madani', 'said', '02912011111', 'agadir', 240.00),
(4, 'ELHATHOUT', 'mustapha', '0630258502', 'ESBIAAT YOUSSOUFIA', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `broker_devis`
--

CREATE TABLE `broker_devis` (
  `id` int(11) NOT NULL,
  `id_broker` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `remove` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `id_client`, `remove`, `date`, `type`) VALUES
(11, 12, 0, '2023-05-15 14:28:15', 'individual'),
(12, 7, 0, '2023-05-15 14:28:29', 'entreprise');

-- --------------------------------------------------------

--
-- Structure de la table `client_entreprise`
--

CREATE TABLE `client_entreprise` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `ICE` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `solde` decimal(10,0) NOT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client_entreprise`
--

INSERT INTO `client_entreprise` (`id`, `nom`, `ICE`, `email`, `tel`, `address`, `solde`, `delete_status`) VALUES
(7, 'Youcode', ' Youcode12345', 'falicires@mailinator.com', '0630258502', ' ESBIAAT YOUSSOUFIA', 0, 0);

--
-- Déclencheurs `client_entreprise`
--
DELIMITER $$
CREATE TRIGGER `deleteEntrepClient` AFTER UPDATE ON `client_entreprise` FOR EACH ROW BEGIN
   IF (NEW.delete_status <> OLD.delete_status) THEN
   
   SET @cl_id = (SELECT client.id FROM `client` WHERE client.id_client=OLD.id AND client.type='entreprise');
    UPDATE `client` SET client.remove = 1 WHERE 					client.id_client=OLD.id AND client.type = 'entreprise';
    UPDATE `devis` SET devis.remove=1 WHERE 						devis.id_client= @cl_id;
    UPDATE `invoice` SET invoice.remove=1 WHERE 						invoice.id_client= @cl_id;
   END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_entrep_client` AFTER INSERT ON `client_entreprise` FOR EACH ROW INSERT INTO `client`(`id`, `id_client`, `remove`, `date`, `type`) VALUES (null,NEW.id,false,NOW(),'entreprise')
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `client_individual`
--

CREATE TABLE `client_individual` (
  `id` int(11) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `solde` decimal(10,0) NOT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client_individual`
--

INSERT INTO `client_individual` (`id`, `prenom`, `nom`, `email`, `tel`, `address`, `solde`, `delete_status`) VALUES
(12, 'ABDELAZIZ', 'ELHATHOUT', 'falicires@mailinator.com', '0630258502', 'YOUSSOUFIA', 0, 0);

--
-- Déclencheurs `client_individual`
--
DELIMITER $$
CREATE TRIGGER `deleteIndvClient` AFTER UPDATE ON `client_individual` FOR EACH ROW BEGIN
   IF (NEW.delete_status <> OLD.delete_status) THEN
   
   SET @cl_id = (SELECT client.id FROM `client` WHERE client.id_client=OLD.id AND client.type='individual');
    UPDATE `client` SET client.remove = 1 WHERE 					client.id_client=OLD.id AND client.type = 'individual';
    UPDATE `devis` SET devis.remove=1 WHERE 						devis.id_client= @cl_id;
    UPDATE `invoice` SET invoice.remove=1 WHERE 						invoice.id_client= @cl_id;
   END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insertIndvClient` AFTER INSERT ON `client_individual` FOR EACH ROW INSERT INTO `client`(`id`, `id_client`, `remove`, `date`, `type`) VALUES (null,NEW.id,false,NOW(),'individual')
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `detail_broker_devis`
--

CREATE TABLE `detail_broker_devis` (
  `id` int(11) NOT NULL,
  `id_broker_devis` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `detail_devis`
--

CREATE TABLE `detail_devis` (
  `id` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `ref` varchar(100) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `paid_srv` tinyint(1) NOT NULL DEFAULT 0,
  `srv_avance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_made` tinyint(1) NOT NULL DEFAULT 0,
  `srv_notif` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `detail_devis`
--

INSERT INTO `detail_devis` (`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`, `approved`, `confirmed`, `paid_srv`, `srv_avance`, `payment_made`, `srv_notif`) VALUES
(172, 79, 'create website', 200.00, 1, 0.00, '04-Nov-2009', 'cws', 0, 1, 1, 0.00, 0, 0),
(173, 79, 'create Logo', 300.00, 1, 0.00, '06-Apr-1970', 'crl', 0, 1, 1, 0.00, 0, 0),
(174, 79, 'service', 500.00, 1, 0.00, 'dd', 'dss', 0, 1, 1, 0.00, 0, 0),
(175, 80, 'Consequatur et quia', 69.00, 61, 72.00, '09-Jul-1984', 'Q', 0, 0, 1, 0.00, 0, 0),
(176, 80, 'Odio aliquam rem asp', 19.00, 35, 59.00, '02-Dec-2016', 'Q', 0, 0, 1, 0.00, 0, 0),
(177, 80, 'Odit repellendus Qu', 95.00, 7, 84.00, '24-May-1974', 'Q', 0, 0, 1, 0.00, 0, 0),
(178, 80, 'Facere vero sint es', 100.00, 88, 84.00, '10-Apr-2021', 'Q', 0, 0, 1, 0.00, 0, 0),
(179, 80, 'Nihil rem architecto', 51.00, 71, 58.00, '05-Jul-1973', 'Q', 0, 0, 1, 0.00, 0, 0),
(180, 81, 'A aute duis aliquam ', 75.00, 50, 86.00, '01-Apr-1975', 'C', 0, 1, 1, 0.00, 0, 0),
(181, 81, 'Architecto sit est ', 10.00, 32, 78.00, '13-Sep-2000', 'C', 0, 1, 1, 0.00, 0, 0),
(182, 81, 'Sed non in optio et', 100.00, 88, 24.00, '11-Oct-2020', 'C', 0, 1, 1, 0.00, 0, 0),
(183, 81, 'Optio neque quisqua', 4.00, 91, 94.00, '17-Apr-1973', 'C', 0, 1, 1, 0.00, 0, 0),
(184, 81, 'Asperiores voluptate', 13.00, 21, 16.00, '28-Jul-1975', 'C', 0, 1, 1, 0.00, 0, 0),
(185, 81, 'Vel eum perspiciatis', 20.00, 36, 37.00, '07-Jul-2022', 'C', 0, 1, 1, 0.00, 0, 0),
(186, 82, 'Magni et voluptates ', 100.00, 26, 14.00, '04-Mar-1978', 'sss', 0, 1, 1, 0.00, 0, 0),
(187, 82, 'Ea aut id dolorum d', 72.00, 28, 28.00, '24-Aug-2004', 'dsd', 0, 1, 1, 0.00, 0, 0),
(188, 83, 'Ea nostrud consectet', 100.00, 2, 30.00, '07-Jun-2015', 'ssssa', 0, 0, 1, 0.00, 0, 0),
(189, 83, 'Quia minim aliquip a', 200.00, 5, 4.00, '02-Aug-2010', 'zdds', 0, 0, 1, 0.00, 0, 0),
(190, 84, 'Consequatur pariatu', 58.00, 19, 6.00, '02-Mar-2011', 'DSDS', 0, 0, 1, 0.00, 0, 0),
(191, 84, 'Quam excepteur nisi ', 22.00, 65, 17.00, '19-Nov-1974', 'DSDSD', 0, 0, 1, 0.00, 0, 0),
(192, 84, 'qsqklj', 100.00, 1, 0.00, 'i', 'z', 0, 0, 1, 0.00, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `detail_invoice`
--

CREATE TABLE `detail_invoice` (
  `id` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `ref` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

CREATE TABLE `devis` (
  `id` int(11) NOT NULL,
  `number` varchar(255) NOT NULL,
  `id_client` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_validation` datetime NOT NULL DEFAULT current_timestamp(),
  `sub_total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `net_total` decimal(10,2) NOT NULL,
  `remove` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL,
  `remove_tva` tinyint(1) NOT NULL DEFAULT 0,
  `client_approve` tinyint(1) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `objet` text NOT NULL,
  `located` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis`
--

INSERT INTO `devis` (`id`, `number`, `id_client`, `type`, `date_creation`, `date_validation`, `sub_total`, `discount`, `net_total`, `remove`, `status`, `remove_tva`, `client_approve`, `comment`, `objet`, `located`) VALUES
(79, '001/2023', 11, 'Approved', '2023-05-15 15:24:44', '2023-05-15 15:24:44', 1000.00, 0.00, 1000.00, 0, 'accepter', 1, 0, 'Ut et tenetur iste r', 'Excepteur qui mollit', 'Provident deserunt '),
(80, '002/2023', 12, 'Approved', '2023-05-15 17:08:04', '2023-05-15 17:08:04', 4486.39, 13473.61, 4486.39, 0, 'accepter', 1, 0, 'Aut tempora quam aut', 'Enim enim voluptatum', 'Ipsam quidem harum e'),
(81, '003/2023', 12, 'Approved', '2023-05-15 17:42:30', '2023-05-15 17:42:30', 7988.16, 6238.84, 7988.16, 0, 'accepter', 1, 0, 'Non qui nisi itaque ', 'Facilis maiores et q', 'Voluptatem error te'),
(82, '004/2023', 12, 'Approved', '2023-05-15 21:44:28', '2023-05-15 21:44:28', 3687.52, 928.48, 3687.52, 0, 'accepter', 1, 0, 'Quasi numquam commod', 'Exercitationem velit', 'Id nemo dolorem nat'),
(83, '005/2023', 12, 'Approved', '2023-05-15 21:47:04', '2023-05-15 21:47:04', 1100.00, 100.00, 1100.00, 0, 'accepter', 1, 0, 'Placeat velit null', 'Sit pariatur Sequi ', 'Enim nobis dolor con'),
(84, '006/2023', 12, 'Approved', '2023-05-15 21:49:57', '2023-05-15 21:49:57', 2322.78, 309.22, 2787.34, 0, 'accepter', 1, 0, 'Nam aliquam mollitia', 'Consequuntur facilis', 'Est eos laboris vo');

-- --------------------------------------------------------

--
-- Structure de la table `devis_payments`
--

CREATE TABLE `devis_payments` (
  `id` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `pay_method` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_date` datetime NOT NULL DEFAULT current_timestamp(),
  `pending` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis_payments`
--

INSERT INTO `devis_payments` (`id`, `id_devis`, `prix`, `pay_method`, `user_id`, `pay_date`, `pending`) VALUES
(744, 172, 200.00, 'Espéce', 1, '2023-05-15 15:25:14', 0),
(745, 173, 200.00, 'Espéce', 1, '2023-05-15 15:25:14', 0),
(746, 174, 500.00, 'Espéce', 1, '2023-05-15 17:03:33', 0),
(747, 173, 100.00, 'Espéce', 1, '2023-05-15 17:05:51', 0),
(748, 175, 69.00, 'Espéce', 1, '2023-05-15 17:09:42', 0),
(749, 176, 19.00, 'Espéce', 1, '2023-05-15 17:09:42', 0),
(750, 177, 95.00, 'Espéce', 1, '2023-05-15 17:15:34', 0),
(751, 179, 51.00, 'Espéce', 1, '2023-05-15 17:15:34', 0),
(752, 178, 90.00, 'Trita', 1, '2023-05-15 17:18:41', 0),
(753, 178, 10.00, 'Espéce', 1, '2023-05-15 17:43:55', 0),
(754, 180, 75.00, 'Espéce', 1, '2023-05-15 17:43:55', 0),
(755, 181, 10.00, 'Espéce', 1, '2023-05-15 17:43:55', 0),
(756, 182, 100.00, 'Espéce', 1, '2023-05-15 17:43:55', 0),
(757, 183, 4.00, 'Espéce', 1, '2023-05-15 17:43:55', 0),
(758, 184, 13.00, 'Espéce', 1, '2023-05-15 17:43:56', 0),
(759, 185, 20.00, 'Espéce', 1, '2023-05-15 17:43:56', 0),
(760, 186, 100.00, 'Espéce', 1, '2023-05-15 21:45:25', 0),
(761, 187, 72.00, 'Espéce', 1, '2023-05-15 21:45:25', 0),
(762, 188, 100.00, 'Check', 1, '2023-05-15 21:47:24', 0),
(763, 189, 100.00, 'Check', 1, '2023-05-15 21:47:24', 0),
(764, 189, 100.00, 'Espéce', 1, '2023-05-15 21:48:58', 0),
(765, 190, 58.00, 'Espéce', 1, '2023-05-15 21:50:39', 0),
(766, 191, 22.00, 'Espéce', 1, '2023-05-15 22:21:19', 0),
(767, 192, 58.00, 'Espéce', 1, '2023-05-15 22:21:19', 0),
(768, 192, 42.00, 'Espéce', 1, '2023-05-15 22:26:50', 0);

-- --------------------------------------------------------

--
-- Structure de la table `devis_to_service`
--

CREATE TABLE `devis_to_service` (
  `id_devis` int(11) NOT NULL,
  `id_service` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossier`
--

CREATE TABLE `dossier` (
  `id` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `N_dossier` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dossier`
--

INSERT INTO `dossier` (`id`, `id_service`, `N_dossier`, `date`) VALUES
(4, 168, 'dossier1', '2023-05-15 14:34:41'),
(5, 166, 'create website1', '2023-05-15 14:35:38');

-- --------------------------------------------------------

--
-- Structure de la table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `F_number` varchar(255) NOT NULL,
  `id_client` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime NOT NULL DEFAULT current_timestamp(),
  `date_validation` datetime NOT NULL DEFAULT current_timestamp(),
  `sub_total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `net_total` decimal(10,2) NOT NULL,
  `remove` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL,
  `remove_tva` tinyint(1) NOT NULL DEFAULT 0,
  `paid_inv` tinyint(1) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `objet` text NOT NULL,
  `located` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invoice_payments`
--

CREATE TABLE `invoice_payments` (
  `id` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `pay_method` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_date` datetime NOT NULL DEFAULT current_timestamp(),
  `pending` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `invoice_payments`
--
DELIMITER $$
CREATE TRIGGER `update_inv_paid` AFTER UPDATE ON `invoice_payments` FOR EACH ROW BEGIN
   IF (NEW.pending <> OLD.pending) THEN
   
   SET @sumInvs = (SELECT SUM(invoice_payments.prix) FROM `invoice_payments` WHERE invoice_payments.id_invoice = NEW.id_invoice);
   SET @total = (SELECT invoice.net_total FROM `invoice` WHERE invoice.id = NEW.id_invoice);
       IF (@sumInvs = @total) THEN
        UPDATE `invoice` SET invoice.paid_inv=1 WHERE invoice.id=NEW.id_invoice;
        END IF;
   END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_document` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `id_document`, `date`, `active`) VALUES
(63, 76, '2023-05-15 15:30:41', 1),
(64, 77, '2023-05-15 15:32:39', 1),
(65, 78, '2023-05-15 16:12:44', 1),
(66, 79, '2023-05-15 16:24:44', 1),
(67, 174, '2023-05-15 18:03:33', 0),
(68, 173, '2023-05-15 18:05:51', 0),
(69, 80, '2023-05-15 18:08:04', 1),
(70, 175, '2023-05-15 18:09:42', 0),
(71, 176, '2023-05-15 18:09:42', 0),
(72, 178, '2023-05-15 18:18:41', 0),
(73, 81, '2023-05-15 18:42:30', 1),
(74, 178, '2023-05-15 18:43:56', 0),
(75, 180, '2023-05-15 18:43:56', 0),
(76, 181, '2023-05-15 18:43:56', 0),
(77, 182, '2023-05-15 18:43:56', 0),
(78, 183, '2023-05-15 18:43:56', 0),
(79, 184, '2023-05-15 18:43:56', 0),
(80, 185, '2023-05-15 18:43:56', 0),
(81, 82, '2023-05-15 22:44:28', 1),
(82, 83, '2023-05-15 22:47:04', 1),
(83, 189, '2023-05-15 22:48:58', 0),
(84, 84, '2023-05-15 22:49:58', 1),
(85, 191, '2023-05-15 23:21:19', 0),
(86, 192, '2023-05-15 23:21:19', 0),
(87, 192, '2023-05-15 23:26:50', 0);

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `perm_desc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `perm_desc`) VALUES
(1, 'show client'),
(2, 'create client'),
(3, 'edit client'),
(4, 'delete client'),
(5, 'show service'),
(6, 'create service'),
(7, 'edit service'),
(8, 'delete service'),
(9, 'show devis'),
(10, 'create devis'),
(11, 'edit devis'),
(12, 'delete devis'),
(13, 'show role'),
(14, 'create role'),
(15, 'edit role'),
(16, 'delete role'),
(17, 'export devis'),
(18, 'show user'),
(19, 'create user'),
(20, 'edit user'),
(21, 'delete user'),
(22, 'show notifications'),
(23, 'show invoice'),
(24, 'create invoice'),
(25, 'edit invoice'),
(26, 'delete invoice'),
(27, 'export invoice'),
(28, 'show payment'),
(29, 'create payment'),
(30, 'show all'),
(31, 'show history'),
(32, 'show situation'),
(33, 'show purchase'),
(34, 'create purchase'),
(35, 'edit purchase'),
(36, 'delete purchase'),
(37, 'show broker'),
(38, 'create broker'),
(39, 'edit broker'),
(40, 'delete broker');

-- --------------------------------------------------------

--
-- Structure de la table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `P_number` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `remove` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `R_number` varchar(100) NOT NULL,
  `id_payment` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `pay_giver` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `receipt`
--

INSERT INTO `receipt` (`id`, `R_number`, `id_payment`, `date`, `pay_giver`) VALUES
(761, '001-05/2023', 744, '2023-05-15 15:25:14', 'OCP'),
(762, '002-05/2023', 745, '2023-05-15 15:25:14', 'OCP'),
(763, '003-05/2023', 746, '2023-05-15 17:03:33', 'OCP'),
(764, '004-05/2023', 747, '2023-05-15 17:05:51', 'OCP'),
(765, '005-05/2023', 748, '2023-05-15 17:09:42', 'Iusto ipsa sed in c'),
(766, '006-05/2023', 749, '2023-05-15 17:09:42', 'Iusto ipsa sed in c'),
(767, '007-05/2023', 750, '2023-05-15 17:15:34', 'Perspiciatis in inc'),
(768, '008-05/2023', 751, '2023-05-15 17:15:34', 'Perspiciatis in inc'),
(769, '009-05/2023', 752, '2023-05-15 17:18:41', 'OCP'),
(770, '010-05/2023', 753, '2023-05-15 17:43:55', 'OCP'),
(771, '011-05/2023', 754, '2023-05-15 17:43:55', 'OCP'),
(772, '012-05/2023', 755, '2023-05-15 17:43:55', 'OCP'),
(773, '013-05/2023', 756, '2023-05-15 17:43:55', 'OCP'),
(774, '014-05/2023', 757, '2023-05-15 17:43:55', 'OCP'),
(775, '015-05/2023', 758, '2023-05-15 17:43:56', 'OCP'),
(776, '016-05/2023', 759, '2023-05-15 17:43:56', 'OCP'),
(777, '017-05/2023', 760, '2023-05-15 21:45:25', 'OCP'),
(778, '018-05/2023', 761, '2023-05-15 21:45:25', 'OCP'),
(779, '019-05/2023', 762, '2023-05-15 21:47:24', 'OCP'),
(780, '020-05/2023', 763, '2023-05-15 21:47:25', 'OCP'),
(781, '021-05/2023', 764, '2023-05-15 21:48:58', 'OCP'),
(782, '022-05/2023', 765, '2023-05-15 21:50:39', 'Perspiciatis in inc'),
(783, '023-05/2023', 766, '2023-05-15 22:21:19', 'OCP'),
(784, '024-05/2023', 767, '2023-05-15 22:21:19', 'OCP'),
(785, '025-05/2023', 768, '2023-05-15 22:26:50', 'OCP');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(3, 'Owner'),
(5, 'admin'),
(6, 'assistant');

-- --------------------------------------------------------

--
-- Structure de la table `role_perm`
--

CREATE TABLE `role_perm` (
  `role_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role_perm`
--

INSERT INTO `role_perm` (`role_id`, `perm_id`) VALUES
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 17),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(6, 1),
(6, 2),
(6, 3),
(6, 5),
(6, 9),
(6, 10),
(6, 11),
(6, 17),
(6, 23),
(6, 24),
(6, 25),
(6, 27),
(6, 28),
(6, 29),
(6, 32),
(6, 33),
(6, 34),
(6, 35),
(3, 37),
(3, 38),
(3, 39),
(3, 40),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(5, 37),
(5, 38),
(5, 39),
(5, 40),
(5, 9),
(5, 10),
(5, 11),
(5, 12),
(5, 17),
(5, 23),
(5, 24),
(5, 25),
(5, 26),
(5, 27),
(5, 28),
(5, 29),
(5, 32),
(5, 33),
(5, 34),
(5, 35),
(5, 18),
(5, 19),
(5, 20),
(5, 21),
(5, 22),
(5, 31);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ref` varchar(100) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `title`, `ref`, `prix`) VALUES
(20, 'create website', 'cws', 0.00),
(21, 'create Logo', 'crl', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `situation`
--

CREATE TABLE `situation` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `situation`
--

INSERT INTO `situation` (`id`, `id_client`, `date`) VALUES
(66, 11, '2023-05-15 14:56:59');

-- --------------------------------------------------------

--
-- Structure de la table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `sold` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `supplier`
--

INSERT INTO `supplier` (`id`, `full_name`, `address`, `phone`, `sold`, `cat_id`) VALUES
(1, 'Fourn1', 'Agadir', '1212121233', 0.00, 1),
(3, 'Fourni2', 'xxxx', '112121212121', 300.00, 4),
(4, 'Mohammed', ' YOUSSOUFIA', '0630258502', 500.00, 4);

-- --------------------------------------------------------

--
-- Structure de la table `supp_category`
--

CREATE TABLE `supp_category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `supp_category`
--

INSERT INTO `supp_category` (`id`, `title`, `type`) VALUES
(1, 'Bureau d\'étude', 'Bureau d\'étude'),
(4, 'Bureau de contrôle', 'Bureau de contrôle');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `prenom`, `nom`, `email`, `tel`, `username`, `password`, `date`, `last_login`, `status`) VALUES
(1, 'user1', 'user1', 'user1@test.com', '06176726', 'owner', 'admin123', '2022-11-05 03:17:09', '2023-05-15 18:02:15', 1),
(2, 'test', 'test', 'test@test.test', '0909090909', 'admin2', 'admin123', '2022-11-26 01:55:54', '2023-05-15 17:54:00', 1),
(4, 'assistant', 'test', 'test@test.test', '12121212', 'assist', 'admin123', '2023-02-01 16:38:04', '2023-05-15 15:18:43', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user_broker`
--

CREATE TABLE `user_broker` (
  `id_user` int(11) NOT NULL,
  `broker` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_broker`
--

INSERT INTO `user_broker` (`id_user`, `broker`, `action`, `date`) VALUES
(1, 'said madani', 'Add', '2023-02-09 16:59:07'),
(1, 'said madani', 'Update', '2023-02-09 17:04:34'),
(1, 'madanis said', 'Update', '2023-05-02 12:07:25'),
(1, 'madani said', 'Update', '2023-05-02 12:14:08'),
(1, 'madanis said', 'Update', '2023-05-02 12:14:49'),
(1, 'madanis saids', 'Update', '2023-05-02 12:17:54'),
(1, 'madanis saids', 'Update', '2023-05-02 12:19:17'),
(1, 'madanis said', 'Update', '2023-05-02 12:19:50'),
(1, 'madanis saids', 'Update', '2023-05-02 12:20:36'),
(1, 'madanis saiddeded', 'Update', '2023-05-02 12:20:43'),
(1, 'madanis said', 'Update', '2023-05-02 12:20:49'),
(1, 'azezdze zdzed', 'Add', '2023-05-07 17:52:05'),
(1, 'madani said', 'Update', '2023-05-07 17:52:31'),
(1, 'asazs sqsq', 'Add', '2023-05-07 17:54:35'),
(1, 'ELHATHOUT mustapha', 'Add', '2023-05-15 14:29:08');

-- --------------------------------------------------------

--
-- Structure de la table `user_client`
--

CREATE TABLE `user_client` (
  `id_user` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `cl_type` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_client`
--

INSERT INTO `user_client` (`id_user`, `id_client`, `cl_type`, `action`, `date`) VALUES
(1, 6, 'individual', 'Update', '2022-12-18 12:19:54'),
(1, 4, 'entreprise', 'Add', '2023-01-03 20:46:18'),
(1, 9, 'individual', 'Add', '2023-01-18 16:14:28'),
(2, 10, 'individual', 'Add', '2023-02-01 14:16:03'),
(2, 5, 'entreprise', 'Add', '2023-02-01 14:16:34'),
(2, 11, 'individual', 'Add', '2023-02-01 18:25:56'),
(2, 6, 'entreprise', 'Add', '2023-02-01 18:26:30'),
(1, 6, 'entreprise', 'Update', '2023-02-06 21:11:11'),
(1, 12, 'individual', 'Add', '2023-05-15 14:28:15'),
(1, 7, 'entreprise', 'Add', '2023-05-15 14:28:29');

-- --------------------------------------------------------

--
-- Structure de la table `user_devis`
--

CREATE TABLE `user_devis` (
  `id_user` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_devis`
--

INSERT INTO `user_devis` (`id_user`, `id_devis`, `action`, `date`) VALUES
(1, 79, 'Add', '2023-05-15 15:24:44'),
(1, 79, 'Devis Approved', '2023-05-15 15:24:48'),
(1, 79, 'Devis Approved', '2023-05-15 15:24:50'),
(1, 79, 'Devis Approved', '2023-05-15 15:24:51'),
(1, 79, 'Paiement Effectué', '2023-05-15 15:25:14'),
(1, 79, 'Paiement Effectué', '2023-05-15 15:25:14'),
(1, 79, 'Paiement Effectué', '2023-05-15 17:03:33'),
(1, 79, 'Paiement Effectué', '2023-05-15 17:05:51'),
(1, 80, 'Add', '2023-05-15 17:08:04'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:09:42'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:09:42'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:15:34'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:15:34'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:18:41'),
(1, 81, 'Add', '2023-05-15 17:42:30'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:45'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:46'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:47'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:48'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:50'),
(1, 81, 'Devis Approved', '2023-05-15 17:42:51'),
(1, 80, 'Paiement Effectué', '2023-05-15 17:43:55'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:55'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:55'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:55'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:56'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:56'),
(1, 81, 'Paiement Effectué', '2023-05-15 17:43:56'),
(1, 82, 'Add', '2023-05-15 21:44:28'),
(1, 82, 'Devis Approved', '2023-05-15 21:44:33'),
(1, 82, 'Devis Approved', '2023-05-15 21:44:35'),
(1, 82, 'Paiement Effectué', '2023-05-15 21:45:25'),
(1, 82, 'Paiement Effectué', '2023-05-15 21:45:25'),
(1, 83, 'Add', '2023-05-15 21:47:04'),
(1, 83, 'Paiement Effectué', '2023-05-15 21:47:24'),
(1, 83, 'Paiement Effectué', '2023-05-15 21:47:25'),
(1, 83, 'Paiement Effectué', '2023-05-15 21:48:58'),
(1, 84, 'Add', '2023-05-15 21:49:57'),
(1, 84, 'Paiement Effectué', '2023-05-15 21:50:39'),
(1, 84, 'Update', '2023-05-15 22:20:11'),
(1, 84, 'Paiement Effectué', '2023-05-15 22:21:19'),
(1, 84, 'Paiement Effectué', '2023-05-15 22:21:19'),
(1, 84, 'Paiement Effectué', '2023-05-15 22:26:50');

-- --------------------------------------------------------

--
-- Structure de la table `user_invoice`
--

CREATE TABLE `user_invoice` (
  `id_user` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_purchase`
--

CREATE TABLE `user_purchase` (
  `id_user` int(11) NOT NULL,
  `id_purchase` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(2, 5),
(1, 3),
(4, 6);

-- --------------------------------------------------------

--
-- Structure de la table `user_service`
--

CREATE TABLE `user_service` (
  `id_user` int(11) NOT NULL,
  `service` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_service`
--

INSERT INTO `user_service` (`id_user`, `service`, `action`, `date`) VALUES
(1, '2', 'Update', '2022-12-18 13:14:10'),
(2, '4', 'Add', '2023-02-01 14:17:12'),
(2, '5', 'Add', '2023-02-01 18:28:05'),
(2, '6', 'Add', '2023-02-01 18:31:24'),
(1, '13', 'Add', '2023-02-07 15:19:15'),
(1, '13', 'Delete', '2023-02-07 15:21:56'),
(1, '13', 'Delete', '2023-02-07 15:24:14'),
(1, '14', 'Add', '2023-02-07 15:24:32'),
(1, '14', 'Delete', '2023-02-07 15:24:39'),
(1, '15', 'Add', '2023-02-07 15:47:49'),
(1, '2', 'Update', '2023-02-07 15:56:21'),
(1, '2', 'Update', '2023-02-07 16:00:31'),
(1, '2', 'Update', '2023-02-07 16:01:10'),
(1, '2', 'Update', '2023-02-07 16:02:17'),
(1, '2', 'Update', '2023-02-07 16:03:07'),
(1, '16', 'Add', '2023-02-09 14:07:51'),
(1, '16', 'Delete', '2023-02-09 14:15:48'),
(1, 'service test', 'Delete', '2023-02-09 14:52:24'),
(1, 'test', 'Update', '2023-02-09 14:52:57'),
(1, 'service 10', 'Add', '2023-02-28 13:24:26'),
(1, 'service1ss', 'Update', '2023-04-30 22:23:26'),
(1, 'service1', 'Update', '2023-04-30 22:24:51'),
(1, 'service1dd', 'Update', '2023-04-30 22:28:31'),
(1, 'service1', 'Update', '2023-04-30 22:28:37'),
(1, 'servazed', 'Add', '2023-04-30 22:28:56'),
(1, 'servaxzxxxxx', 'Update', '2023-04-30 22:29:09'),
(1, 'servaxzxxxxxaa', 'Update', '2023-04-30 22:29:22'),
(1, 'servaxzxxxxxaa', 'Delete', '2023-04-30 22:29:29'),
(1, 'create website', 'Add', '2023-05-15 14:28:42'),
(1, 'create Logo', 'Add', '2023-05-15 14:28:52');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `broker`
--
ALTER TABLE `broker`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `broker_devis`
--
ALTER TABLE `broker_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broker_devis_devis` (`id_devis`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `client_individual`
--
ALTER TABLE `client_individual`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_broker_devis` (`id_broker_devis`);

--
-- Index pour la table `detail_devis`
--
ALTER TABLE `detail_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_devis` (`id_devis`);

--
-- Index pour la table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_invoice` (`id_invoice`);

--
-- Index pour la table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `devis_payments`
--
ALTER TABLE `devis_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_devis_payment` (`id_devis`);

--
-- Index pour la table `devis_to_service`
--
ALTER TABLE `devis_to_service`
  ADD PRIMARY KEY (`id_devis`,`id_service`),
  ADD KEY `id_devis` (`id_devis`,`id_service`),
  ADD KEY `FK_service` (`id_service`);

--
-- Index pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_dossier_service` (`id_service`);

--
-- Index pour la table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_invoiceClient` (`id_client`);

--
-- Index pour la table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_invoice_payment` (`id_invoice`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_receipt_payment` (`id_payment`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `role_perm`
--
ALTER TABLE `role_perm`
  ADD KEY `FK_role_perm_role` (`role_id`),
  ADD KEY `FK_role_perm_perm` (`perm_id`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `situation`
--
ALTER TABLE `situation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_supplier_suppCat` (`cat_id`);

--
-- Index pour la table `supp_category`
--
ALTER TABLE `supp_category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_broker`
--
ALTER TABLE `user_broker`
  ADD KEY `FK_user_broker_user` (`id_user`);

--
-- Index pour la table `user_client`
--
ALTER TABLE `user_client`
  ADD KEY `FK_user_client_user` (`id_user`);

--
-- Index pour la table `user_devis`
--
ALTER TABLE `user_devis`
  ADD KEY `FK_user_devis_user` (`id_user`),
  ADD KEY `FK_user_devis_devis` (`id_devis`);

--
-- Index pour la table `user_invoice`
--
ALTER TABLE `user_invoice`
  ADD KEY `FK_user_invoice_user` (`id_user`),
  ADD KEY `FK_user_invoice_invoice` (`id_invoice`);

--
-- Index pour la table `user_purchase`
--
ALTER TABLE `user_purchase`
  ADD KEY `FK_user_purchase_user` (`id_user`),
  ADD KEY `FK_user_purchase_purchase` (`id_purchase`);

--
-- Index pour la table `user_role`
--
ALTER TABLE `user_role`
  ADD KEY `FK_user_role_user` (`user_id`),
  ADD KEY `FK_user_role_role` (`role_id`);

--
-- Index pour la table `user_service`
--
ALTER TABLE `user_service`
  ADD KEY `FK_user_service_user` (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `broker`
--
ALTER TABLE `broker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `broker_devis`
--
ALTER TABLE `broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `client_individual`
--
ALTER TABLE `client_individual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `detail_devis`
--
ALTER TABLE `detail_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT pour la table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `devis_payments`
--
ALTER TABLE `devis_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=769;

--
-- AUTO_INCREMENT pour la table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `situation`
--
ALTER TABLE `situation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `supp_category`
--
ALTER TABLE `supp_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `broker_devis`
--
ALTER TABLE `broker_devis`
  ADD CONSTRAINT `broker_devis_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  ADD CONSTRAINT `FK_detail_broker_devis` FOREIGN KEY (`id_broker_devis`) REFERENCES `broker_devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `detail_devis`
--
ALTER TABLE `detail_devis`
  ADD CONSTRAINT `FK_detail_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  ADD CONSTRAINT `FK_detail_invoice` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `FK_devisClient` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis_payments`
--
ALTER TABLE `devis_payments`
  ADD CONSTRAINT `FK_detail_devis_payment` FOREIGN KEY (`id_devis`) REFERENCES `detail_devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis_to_service`
--
ALTER TABLE `devis_to_service`
  ADD CONSTRAINT `FK_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`),
  ADD CONSTRAINT `FK_service` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

--
-- Contraintes pour la table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_invoiceClient` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD CONSTRAINT `FK_invoice_payment` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `FK_receipt_payment` FOREIGN KEY (`id_payment`) REFERENCES `devis_payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `role_perm`
--
ALTER TABLE `role_perm`
  ADD CONSTRAINT `FK_role_perm_perm` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_role_perm_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `FK_supplier_suppCat` FOREIGN KEY (`cat_id`) REFERENCES `supp_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_broker`
--
ALTER TABLE `user_broker`
  ADD CONSTRAINT `FK_user_broker_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_client`
--
ALTER TABLE `user_client`
  ADD CONSTRAINT `FK_user_client_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_devis`
--
ALTER TABLE `user_devis`
  ADD CONSTRAINT `FK_user_devis_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_devis_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_invoice`
--
ALTER TABLE `user_invoice`
  ADD CONSTRAINT `FK_user_invoice_invoice` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_invoice_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_purchase`
--
ALTER TABLE `user_purchase`
  ADD CONSTRAINT `FK_user_purchase_purchase` FOREIGN KEY (`id_purchase`) REFERENCES `purchase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_purchase_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_role_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_service`
--
ALTER TABLE `user_service`
  ADD CONSTRAINT `FK_user_service_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
