-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 22 mai 2023 à 02:49
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByBroker` (IN `broker_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,dossier.N_dossier,dossier.id as id_dossier ,detail_devis.quantity,
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
LEFT join dossier on dossier.id_service=detail_devis.id
WHERE devis.remove=0 AND broker_devis.id_broker= broker_id AND detail_devis.paid_srv = 0 AND detail_devis.confirmed=1
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByClient` (IN `client_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,dossier.N_dossier,dossier.id as id_dossier,detail_devis.quantity,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,detail_devis.service_name,detail_devis.ref,
    IF(devis.remove_tva=0,ROUND(detail_devis.prix*0.2+detail_devis.prix,2),detail_devis.prix) AS srv_prix,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE detail_devis.id=devis_payments.id_devis AND devis_payments.pending=0),0) AS solde,dossier.dossier_prix,dossier.dossier_avc,dossier.dossier_status,detail_devis.srv_avance
FROM devis INNER JOIN client ON devis.id_client=client.id   
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
LEFT join dossier on dossier.id_service=detail_devis.id
WHERE devis.remove=0 AND devis.id_client= client_id AND detail_devis.paid_srv = 0 and detail_devis.confirmed=1 
ORDER BY devis.date_creation$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPaymentInfo` ()   SELECT devis.id,devis_payments.id AS pay_id,
devis_payments.user_id,devis_payments.pay_method, devis.number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE  client.id_client=client_individual.id)
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
devis.objet,devis.located
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getInvoiceNotifications` ()   SELECT user_invoice.*,user_invoice.date as 'date-action',user_role.role_id ,roles.role_name as 'role',invoice.* 
from user_invoice
left JOIN user_role on user_role.user_id=user_invoice.id_user
left JOIN roles on roles.id=user_role.role_id 
left JOIN invoice on invoice.id=user_invoice.id_invoice

where roles.role_name="assistant" AND user_invoice.is_vue="0"
ORDER by date DESC$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getNotifications` ()   SELECT user_devis.*,user_devis.date as 'date-action',user_role.role_id ,roles.role_name as 'role',devis.* 
from user_devis
left JOIN user_role on user_role.user_id=user_devis.id_user
left JOIN roles on roles.id=user_role.role_id 
left JOIN devis on devis.id=user_devis.id_devis

where roles.role_name="assistant" AND user_devis.is_vue="0"
ORDER by date DESC$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getSelectedDossier` (IN `service_id` INT)   SELECT detail_devis.ref, dossier.N_dossier,devis.objet, detail_devis.service_name,detail_devis.prix,devis.located
FROM devis 
INNER JOIN detail_devis ON devis.id=detail_devis.id_devis INNER JOIN 
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
  `srv_unique_id` int(11) DEFAULT NULL,
  `new_prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `new_discount` int(11) NOT NULL DEFAULT 0
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
  `srv_unique_id` int(11) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `paid_srv` tinyint(1) NOT NULL DEFAULT 0,
  `srv_avance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_made` tinyint(1) NOT NULL DEFAULT 0,
  `srv_notif` tinyint(1) NOT NULL DEFAULT 0,
  `empl` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `located` text NOT NULL,
  `is_facture` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `dossier_prix` decimal(10,2) DEFAULT 0.00,
  `dossier_avc` decimal(10,2) DEFAULT 0.00,
  `dossier_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 31),
(6, 1),
(6, 2),
(6, 3),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 37),
(6, 38),
(6, 39),
(6, 40),
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
(6, 35);

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
(31, 'Attestaion de stabilité.', 'ATS', 0.00),
(32, 'Attestaion de la notice de sécurité', 'ATN', 0.00),
(33, 'Etablissement des plans', 'ET', 0.00),
(34, 'Visa et controle', 'VS', 0.00),
(35, 'Attestaion de fin de dravaux', 'ATF', 0.00),
(36, 'Att de :Incendie et la panique', 'ATI', 0.00),
(37, 'Att de :Instalation electrique', 'ATINS', 0.00),
(38, 'Att de :Travaux de plomberie', 'ATB', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `situation`
--

CREATE TABLE `situation` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'user1', 'user1', 'user1@test.com', '06176726', 'owner', 'admin123', '2022-11-05 03:17:09', '2023-05-22 01:54:14', 1),
(2, 'test', 'test', 'test@test.test', '0909090909', 'admin2', 'admin123', '2022-11-26 01:55:54', '2023-05-22 02:48:18', 1),
(4, 'assistant', 'test', 'test@test.test', '12121212', 'assist', 'admin123', '2023-02-01 16:38:04', '2023-05-22 02:32:27', 1),
(5, 'user1', 'user2', 'user2@gmail.com', '0630234455', 'assistant2', 'admin123', '2023-05-16 10:58:31', '2023-05-22 00:44:34', 1);

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
(1, 'ELHATHOUT mustapha', 'Add', '2023-05-15 14:29:08'),
(1, 'Consectetur libero  Aliquid est aut vel ', 'Add', '2023-05-17 14:15:08'),
(1, 'madani said', 'Update', '2023-05-17 14:15:48'),
(1, 'Sed quia magni eu te Optio quaerat illo ', 'Add', '2023-05-17 16:30:09'),
(1, 'mustapha Elhathout', 'Add', '2023-05-19 14:31:46'),
(1, 'Brahim ADMDi', 'Update', '2023-05-21 21:23:10'),
(1, 'Broker Mohammed', 'Add', '2023-05-21 21:31:34');

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
(2, 13, 'individual', 'Add', '2023-05-16 11:02:08'),
(2, 13, 'individual', 'Delete', '2023-05-16 11:02:14'),
(2, 12, 'individual', 'Update', '2023-05-16 11:02:28'),
(2, 14, 'individual', 'Add', '2023-05-16 21:46:16'),
(1, 14, 'individual', 'Delete', '2023-05-17 10:02:11'),
(1, 12, 'individual', 'Update', '2023-05-17 10:03:37'),
(1, 7, 'entreprise', 'Update', '2023-05-17 10:03:58'),
(1, 12, 'individual', 'Update', '2023-05-17 10:04:23'),
(1, 12, 'individual', 'Update', '2023-05-17 10:05:53'),
(1, 12, 'individual', 'Update', '2023-05-17 10:06:14'),
(1, 12, 'individual', 'Update', '2023-05-17 10:08:49'),
(1, 12, 'individual', 'Update', '2023-05-17 10:09:06'),
(1, 12, 'individual', 'Update', '2023-05-17 10:09:14'),
(1, 12, 'individual', 'Update', '2023-05-17 10:09:38'),
(1, 12, 'individual', 'Update', '2023-05-17 10:11:45'),
(1, 12, 'individual', 'Update', '2023-05-17 10:12:07'),
(1, 12, 'individual', 'Update', '2023-05-17 10:12:29'),
(1, 12, 'individual', 'Update', '2023-05-17 10:12:46'),
(1, 12, 'individual', 'Update', '2023-05-17 10:15:08'),
(1, 12, 'individual', 'Update', '2023-05-17 10:15:16'),
(1, 7, 'entreprise', 'Update', '2023-05-17 10:15:26'),
(1, 15, 'individual', 'Add', '2023-05-17 10:22:48'),
(1, 16, 'individual', 'Add', '2023-05-17 10:23:20'),
(1, 17, 'individual', 'Add', '2023-05-17 10:25:18'),
(1, 18, 'individual', 'Add', '2023-05-17 10:28:41'),
(1, 12, 'individual', 'Update', '2023-05-17 10:30:50'),
(1, 12, 'individual', 'Update', '2023-05-17 10:30:56'),
(1, 19, 'individual', 'Add', '2023-05-17 10:31:40'),
(1, 20, 'individual', 'Add', '2023-05-17 10:32:07'),
(1, 12, 'individual', 'Update', '2023-05-17 10:40:54'),
(1, 12, 'individual', 'Delete', '2023-05-17 10:41:03'),
(1, 15, 'individual', 'Delete', '2023-05-17 10:41:09'),
(1, 16, 'individual', 'Delete', '2023-05-17 10:41:11'),
(1, 8, 'entreprise', 'Add', '2023-05-17 10:41:40'),
(1, 21, 'individual', 'Add', '2023-05-17 16:28:23'),
(1, 9, 'entreprise', 'Add', '2023-05-17 16:28:57'),
(1, 22, 'individual', 'Add', '2023-05-19 14:31:26'),
(1, 10, 'entreprise', 'Add', '2023-05-19 14:32:17'),
(1, 23, 'individual', 'Add', '2023-05-19 21:32:09'),
(1, 11, 'entreprise', 'Add', '2023-05-20 16:27:24'),
(1, 11, 'entreprise', 'Update', '2023-05-20 16:27:35');

-- --------------------------------------------------------

--
-- Structure de la table `user_devis`
--

CREATE TABLE `user_devis` (
  `id_user` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `is_vue` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_invoice`
--

CREATE TABLE `user_invoice` (
  `id_user` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `is_vue` int(11) NOT NULL DEFAULT 0
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
(4, 6),
(5, 6);

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
(1, 'create Logo', 'Add', '2023-05-15 14:28:52'),
(1, 'Ut ab sequi quisquam', 'Add', '2023-05-17 10:32:27'),
(1, 'create website', 'Update', '2023-05-17 10:33:29'),
(1, 'create website', 'Update', '2023-05-17 10:33:30'),
(1, 'create website', 'Update', '2023-05-17 10:33:32'),
(1, 'create websitei', 'Update', '2023-05-17 10:33:38'),
(1, 'create websitei', 'Update', '2023-05-17 10:33:39'),
(1, 'create websitei', 'Update', '2023-05-17 10:33:39'),
(1, 'create websitei', 'Update', '2023-05-17 10:33:40'),
(1, 'create websitei', 'Update', '2023-05-17 10:33:40'),
(1, 'create Logo', 'Update', '2023-05-17 10:34:07'),
(1, 'Ab eos est in nihi', 'Add', '2023-05-17 10:34:17'),
(1, 'create websitei', 'Update', '2023-05-17 10:34:39'),
(1, 'create websitei', 'Update', '2023-05-17 10:35:33'),
(1, 'create websitei', 'Update', '2023-05-17 10:35:33'),
(1, 'create websitei', 'Update', '2023-05-17 10:36:28'),
(1, 'create websitei', 'Update', '2023-05-17 10:38:00'),
(1, 'create websitei', 'Update', '2023-05-17 10:39:10'),
(1, 'create websiteikdlqskd', 'Update', '2023-05-17 10:39:20'),
(1, 'create Logo', 'Update', '2023-05-17 10:39:26'),
(1, 'create websiteikdlqskd', 'Delete', '2023-05-17 10:39:33'),
(1, 'create Logo', 'Delete', '2023-05-17 10:39:37'),
(1, 'create website', 'Add', '2023-05-17 10:42:20'),
(1, 'cr', 'Update', '2023-05-17 10:42:33'),
(1, 'cr', 'Update', '2023-05-17 10:42:39'),
(1, 'Ab eos est in nihi', 'Update', '2023-05-17 10:42:43'),
(1, 'cr', 'Update', '2023-05-17 10:59:59'),
(1, 'Ab eos est in nihi', 'Update', '2023-05-17 11:00:31'),
(1, 'cr', 'Update', '2023-05-17 11:00:46'),
(1, 'cr', 'Update', '2023-05-17 11:07:05'),
(1, 'cr', 'Update', '2023-05-17 11:07:36'),
(1, 'Ab eos est in nihi', 'Update', '2023-05-17 11:07:40'),
(1, 'xxxxxxxx', 'Add', '2023-05-17 11:07:52'),
(1, 'Voluptate nihil impe', 'Add', '2023-05-17 11:08:10'),
(1, 'cr', 'Update', '2023-05-17 11:08:15'),
(1, 'cr', 'Update', '2023-05-17 11:10:27'),
(1, 'cr', 'Update', '2023-05-17 11:11:40'),
(1, 'cr', 'Update', '2023-05-17 11:11:53'),
(1, 'eeeeeeee', 'Add', '2023-05-17 11:17:33'),
(1, 'cr', 'Update', '2023-05-17 11:20:05'),
(1, 'cr', 'Update', '2023-05-17 11:20:24'),
(1, 'cr', 'Update', '2023-05-17 11:36:16'),
(1, 'cr', 'Update', '2023-05-17 11:39:12'),
(1, 'cr', 'Update', '2023-05-17 11:39:19'),
(1, 'cr', 'Update', '2023-05-17 11:39:32'),
(1, 'Ab eos est in nihi', 'Update', '2023-05-17 11:49:03'),
(1, 'cr', 'Update', '2023-05-17 11:49:44'),
(1, 'cr', 'Update', '2023-05-17 11:51:21'),
(1, 'cr', 'Update', '2023-05-17 11:56:23'),
(1, 'Ad quia voluptatum p', 'Add', '2023-05-17 14:11:04'),
(1, 'Adse', 'Update', '2023-05-17 14:11:51'),
(1, 'Adsence', 'Update', '2023-05-17 14:12:03'),
(1, 'Adsence', 'Update', '2023-05-17 14:12:17'),
(1, 'Adsence1', 'Update', '2023-05-17 14:13:15'),
(1, 'cr', 'Delete', '2023-05-17 14:13:33'),
(1, 'create website', 'Delete', '2023-05-17 14:13:36'),
(1, 'Ab eos est in nihi', 'Delete', '2023-05-17 14:13:38'),
(1, 'xxxxxxxx', 'Delete', '2023-05-17 14:13:41'),
(1, 'Voluptate nihil impe', 'Delete', '2023-05-17 14:13:44'),
(1, 'df', 'Update', '2023-05-17 16:29:35'),
(1, 'Adsence1', 'Update', '2023-05-17 16:29:53'),
(1, 'create website', 'Add', '2023-05-19 14:32:31'),
(1, 'create website', 'Update', '2023-05-19 14:32:52'),
(1, 'create Logo', 'Add', '2023-05-19 14:33:17'),
(1, 'create website', 'Delete', '2023-05-21 21:19:15'),
(1, 'create Logo', 'Delete', '2023-05-21 21:19:18'),
(1, 'Attestaion de stabilité', 'Add', '2023-05-21 21:19:37'),
(1, 'Attestaion de la notice de sécurité', 'Add', '2023-05-21 21:20:07'),
(1, 'Etablissement des plans', 'Add', '2023-05-21 21:20:28'),
(1, 'Visa et controle', 'Add', '2023-05-21 21:20:45'),
(1, 'Attestaion de fin de dravaux', 'Add', '2023-05-21 21:21:04'),
(1, 'Att de :Incendie et la panique', 'Add', '2023-05-21 21:21:50'),
(1, 'Att de :Instalation electrique', 'Add', '2023-05-21 21:22:17'),
(1, 'Att de :Travaux de plomberie', 'Add', '2023-05-21 21:22:48'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:23:26'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:25:42'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:26:22'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:26:34'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:27:09'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:27:57'),
(1, 'Attestaion de stabilité.', 'Update', '2023-05-21 21:29:01'),
(1, 'Attestaion de la notice de sécurité', 'Update', '2023-05-21 21:29:12');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `broker_devis`
--
ALTER TABLE `broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `client_individual`
--
ALTER TABLE `client_individual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT pour la table `detail_devis`
--
ALTER TABLE `detail_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4323;

--
-- AUTO_INCREMENT pour la table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT pour la table `devis_payments`
--
ALTER TABLE `devis_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- AUTO_INCREMENT pour la table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=803;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `situation`
--
ALTER TABLE `situation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `supp_category`
--
ALTER TABLE `supp_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
