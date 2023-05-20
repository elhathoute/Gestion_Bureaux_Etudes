-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 20 mai 2023 à 10:14
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByBroker` (IN `broker_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,dossier.N_dossier ,detail_devis.quantity,
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getDevisPayByClient` (IN `client_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,dossier.N_dossier ,detail_devis.quantity,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,detail_devis.service_name,detail_devis.ref,
    IF(devis.remove_tva=0,ROUND(detail_devis.prix*0.2+detail_devis.prix,2),detail_devis.prix) AS srv_prix,IFNULL(
(SELECT SUM(devis_payments.prix)
FROM devis_payments 
WHERE detail_devis.id=devis_payments.id_devis AND devis_payments.pending=0),0) AS solde
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
(7, 'mustapha', 'Elhathout', '0630258502', 'Marrakech', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `broker_devis`
--

CREATE TABLE `broker_devis` (
  `id` int(11) NOT NULL,
  `id_broker` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `broker_devis`
--

INSERT INTO `broker_devis` (`id`, `id_broker`, `id_devis`) VALUES
(141, 7, 169),
(142, 7, 172),
(143, 7, 173),
(144, 7, 174),
(145, 0, 175),
(146, 7, 176),
(147, 7, 177),
(148, 7, 178),
(149, 7, 179),
(150, 7, 180),
(151, 7, 181);

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
(24, 22, 0, '2023-05-19 14:31:26', 'individual'),
(25, 10, 0, '2023-05-19 14:32:17', 'entreprise'),
(26, 23, 0, '2023-05-19 21:32:09', 'individual');

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
(10, 'Youcode', ' Youcode12345', 'youcode@gmail.com', '0630258502', ' ', 0, 0);

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
(22, 'ABDELAZIZ', 'ELHATHOUT', '', '0630258502', 'ESBIAAT YOUSSOUFIA', 0, 0),
(23, 'ADMDI', 'MOUAD', '', '0630258502', 'AGADIR', 0, 0);

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

--
-- Déchargement des données de la table `detail_broker_devis`
--

INSERT INTO `detail_broker_devis` (`id`, `id_broker_devis`, `srv_unique_id`, `new_prix`, `new_discount`) VALUES
(106, 141, 170, 61.00, 32),
(107, 141, 171, 13.00, 49),
(108, 141, 172, 41.00, 21),
(109, 146, 177, 29.00, 94),
(110, 148, 179, 94.00, 92),
(111, 148, 180, 37.00, 82),
(112, 148, 181, 12.00, 17),
(113, 148, 182, 5.00, 89),
(114, 150, 181, 31.00, 87),
(115, 151, 182, 20.00, 30),
(116, 151, 183, 10.00, 99),
(117, 151, 184, 10.00, 20),
(118, 151, 185, 40.00, 43),
(119, 151, 186, 50.00, 37);

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
  `srv_notif` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `detail_devis`
--

INSERT INTO `detail_devis` (`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`, `srv_unique_id`, `approved`, `confirmed`, `paid_srv`, `srv_avance`, `payment_made`, `srv_notif`) VALUES
(334, 168, 'Vel pariatur Sunt ', 62.00, 80, 52.00, '28-Oct-1984', 'z', 169, 0, 0, 0, 0.00, 0, 0),
(335, 168, 'Ut eos eaque neque ', 31.00, 61, 69.00, '10-May-2002', 'z', 170, 0, 0, 0, 0.00, 0, 0),
(336, 169, 'Est modi dolore ips', 1.00, 4, 32.00, '03-May-2015', 'S', 170, 0, 1, 1, 0.00, 0, 0),
(337, 169, 'Porro sed mollit err', 123.00, 20, 49.00, '15-Apr-2003', 'SSQ', 171, 0, 0, 1, 0.00, 0, 0),
(338, 169, 'Odio inventore digni', 41.00, 6, 21.00, '08-Jan-2006', 'QSQS', 172, 0, 0, 0, 0.00, 0, 0),
(339, 170, 'Adipisicing commodi ', 81.00, 91, 69.00, '21-Aug-2011', 'qsqsq', 171, 0, 1, 0, 0.00, 0, 0),
(340, 171, 'Officia inventore no', 121.00, 1, 0.00, '13-Feb-1995', 'sdf', 172, 1, 1, 0, 0.00, 0, 0),
(341, 172, 'In enim repellendus', 46.00, 94, 58.00, '03-Apr-1986', '<', 173, 0, 0, 0, 0.00, 0, 0),
(342, 172, 'Id laboriosam dist', 91.00, 34, 43.00, '15-May-1982', '<', 174, 0, 0, 0, 0.00, 0, 0),
(343, 172, 'Quas nostrum tempore', 31.00, 79, 18.00, '15-Jun-2012', '<', 175, 0, 0, 0, 0.00, 0, 0),
(344, 173, 'Consequat Est conse', 24.00, 89, 5.00, '02-Nov-1977', 'qs', 174, 0, 0, 0, 0.00, 0, 0),
(345, 173, 'Rerum sed molestiae ', 11.00, 36, 12.00, '14-Dec-2004', 'qs', 175, 0, 0, 0, 0.00, 0, 0),
(346, 173, 'Et quo voluptate vol', 1.00, 59, 3.00, '06-Jul-1995', 'sqsq', 176, 0, 0, 0, 0.00, 0, 0),
(347, 174, 'Mollitia sint except', 100.00, 66, 6.00, '22-Apr-2013', 'dqsdsq', 175, 0, 0, 0, 0.00, 0, 0),
(348, 175, 'Ea suscipit sit id ', 17.00, 3, 70.00, '16-Nov-1999', 'azaz', 176, 0, 0, 0, 0.00, 0, 0),
(349, 176, 'Temporibus neque est', 29.00, 60, 94.00, '06-May-1972', 'zaz', 177, 0, 0, 0, 0.00, 0, 0),
(350, 177, 'Voluptates ea saepe ', 53.00, 92, 73.00, '19-Apr-1991', 'azea', 178, 0, 1, 0, 0.00, 0, 0),
(351, 178, 'Adipisicing velit ni', 94.00, 72, 92.00, '25-Mar-1970', 'q', 179, 0, 0, 0, 0.00, 0, 0),
(352, 178, 'Dolore tempor ea nos', 37.00, 87, 82.00, '24-Dec-1983', 'qq', 180, 0, 0, 0, 0.00, 0, 0),
(353, 178, 'Vitae fugiat sunt ', 23.00, 98, 17.00, '02-May-1983', 'q', 181, 1, 1, 0, 0.00, 0, 0),
(354, 178, 'Ipsam laboris eu ver', 5.00, 0, 89.00, '16-Nov-2006', 'q', 182, 0, 0, 0, 0.00, 0, 0),
(355, 179, 'partie frent end', 1000.00, 2, 2.00, 'p', 'pfe', 180, 0, 0, 0, 0.00, 0, 0),
(356, 179, 'partie back end', 2000.00, 1, 5.00, 'p', 'pbe', 181, 0, 0, 0, 0.00, 0, 0),
(357, 179, 'create Logo', 500.00, 1, 0.00, 'p', 'cL3', 182, 0, 0, 0, 0.00, 0, 0),
(358, 179, 'marketing', 500.00, 1, 0.00, 'p', 'mrk', 183, 0, 0, 0, 0.00, 0, 0),
(359, 179, 'page facebook', 200.00, 1, 0.00, 'p', 'pfc', 184, 0, 0, 0, 0.00, 0, 0),
(360, 180, 'Nihil in maxime temp', 31.00, 85, 87.00, '17-Mar-2013', 'zeze', 181, 0, 0, 0, 0.00, 0, 0),
(361, 181, 'Odit nesciunt totam', 80.00, 2, 30.00, '09-Jan-1991', 'éé', 182, 1, 1, 0, 0.00, 0, 0),
(362, 181, 'Quae dolorum omnis v', 79.00, 3, 97.00, '14-Nov-1981', 'éé2', 183, 0, 1, 0, 0.00, 0, 0),
(363, 181, 'Id blanditiis duis c', 17.00, 1, 20.00, '25-May-2008', 'éé3', 184, 0, 1, 0, 0.00, 0, 0),
(364, 181, 'Corporis esse tempo', 42.00, 4, 43.00, '20-Jul-2016', 'éé3', 185, 0, 0, 0, 0.00, 0, 0),
(365, 181, 'Quisquam ipsam iure ', 63.00, 1, 37.00, '12-Mar-2021', 'éé4', 186, 0, 0, 0, 0.00, 0, 0),
(366, 182, 'Itaque atque assumen', 2.00, 71, 93.00, '19-Dec-1970', 'zkj', 183, 0, 0, 0, 0.00, 0, 0),
(367, 182, 'Reiciendis impedit ', 57.00, 18, 92.00, '17-Oct-2011', 'klkz', 184, 0, 0, 0, 0.00, 0, 0);

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

--
-- Déchargement des données de la table `detail_invoice`
--

INSERT INTO `detail_invoice` (`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`) VALUES
(58, 30, 'Adipisicing commodi ', 81.00, 49, 69.00, '21-Aug-2011', 'qsqsq');

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
(168, '001/2023', 24, 'Approved', '2023-05-19 14:35:40', '2023-05-19 14:35:40', 2967.01, 3883.99, 3560.41, 0, 'accepter', 0, 0, 'Laboriosam ex labor', 'Temporibus nihil rat', 'Quia deserunt tenetu'),
(169, '002/2023', 25, 'Approved', '2023-05-19 14:36:51', '2023-05-19 14:36:51', 1451.66, 1258.34, 1451.66, 0, 'accepter', 1, 0, 'Fugit amet non nes', 'Vitae sequi rerum el', 'Laboriosam esse aut'),
(170, '003/2023', 24, 'Approved', '2023-05-19 14:46:13', '2023-05-19 14:46:13', 2285.01, 5085.99, 2742.01, 0, 'accepter', 0, 0, 'Ut cupidatat consequ', 'Omnis praesentium ma', 'Ut exercitationem ex'),
(171, '004/2023', 24, 'Approved', '2023-05-19 14:50:39', '2023-05-19 14:50:39', 121.00, 0.00, 121.00, 0, 'accepter', 1, 0, 'Harum facilis qui ne', 'Optio nisi beatae i', 'Ullamco commodi cum '),
(172, '005/2023', 24, 'Approved', '2023-05-19 16:49:10', '2023-05-19 16:49:10', 5587.84, 4279.16, 6705.41, 0, 'accepter', 0, 0, 'Animi vel lorem mol', 'Ipsa officiis esse', 'Quia nostrud perfere'),
(173, '006/2023', 24, 'Approved', '2023-05-19 16:50:22', '2023-05-19 16:50:22', 2434.91, 156.09, 2434.91, 0, 'accepter', 1, 0, 'Aut autem quis sit ', 'Laboris officia in v', 'Qui molestias ipsam '),
(174, '007/2023', 24, 'Approved', '2023-05-19 16:51:43', '2023-05-19 16:51:43', 6204.00, 396.00, 6204.00, 0, 'accepter', 1, 0, 'Non et ut qui et ame', 'Quod eaque deserunt ', 'Sed facere adipisci '),
(175, '008/2023', 24, 'Approved', '2023-05-19 16:53:01', '2023-05-19 16:53:01', 15.30, 35.70, 18.36, 0, 'accepter', 0, 0, 'Soluta similique lib', 'Nobis consequatur i', 'Elit et eiusmod hic'),
(176, '009/2023', 24, 'Approved', '2023-05-19 16:56:36', '2023-05-19 16:56:36', 104.40, 1635.60, 125.28, 0, 'accepter', 0, 0, 'Exercitation ipsam d', 'Sed minim culpa des', 'Ipsum eaque nostrud'),
(177, '010/2023', 24, 'Approved', '2023-05-19 16:57:20', '2023-05-19 16:57:20', 1316.52, 3559.48, 1316.52, 0, 'accepter', 1, 0, 'Exercitationem ad no', 'Labore est consequa', 'Dicta quia ut dolore'),
(178, '011/2023', 24, 'Approved', '2023-05-19 17:01:42', '2023-05-19 17:01:42', 2991.68, 9249.32, 3590.02, 0, 'accepter', 0, 0, 'Explicabo Est neces', 'Dolore sint quibusd', 'Enim qui illum aut '),
(179, '012/2023', 26, 'Approved', '2023-05-19 21:34:47', '2023-05-19 21:34:47', 5060.00, 140.00, 5060.00, 1, 'accepter', 1, 0, 'You welcome Mr Admdi', 'Creation de site web', 'Agadir'),
(180, '013/2023', 26, 'Approved', '2023-05-19 21:36:29', '2023-05-19 21:36:29', 342.55, 2292.45, 411.06, 1, 'accepter', 0, 0, 'Ea et animi libero ', 'Corporis placeat of', 'Consectetur vel hic'),
(181, '013/2023', 26, 'Approved', '2023-05-19 21:37:56', '2023-05-19 21:37:56', 268.16, 376.84, 321.79, 0, 'accepter', 0, 0, 'Quasi velit vero eiu', 'Aut dolor vitae ut d', 'Quia quia similique '),
(182, '014/2023', 25, 'Approved', '2023-05-19 21:40:30', '2023-05-19 21:40:30', 92.02, 1075.98, 92.02, 1, 'accepter', 1, 0, 'Cumque sequi qui ut ', 'Eveniet est enim et', 'Officia qui veniam ');

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
(780, 336, 73.20, 'Espéce', 1, '2023-05-19 15:36:36', 0),
(781, 337, 15.60, 'Espéce', 1, '2023-05-19 15:36:36', 0),
(782, 338, 11.20, 'Espéce', 1, '2023-05-19 15:36:36', 0);

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
(6, 353, 'N-1234', '2023-05-19 21:28:41'),
(7, 340, 'N-324757', '2023-05-19 21:30:07'),
(8, 361, 'N-1234', '2023-05-19 22:01:08'),
(9, 361, 'N-2344', '2023-05-20 00:55:33');

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

--
-- Déchargement des données de la table `invoice`
--

INSERT INTO `invoice` (`id`, `F_number`, `id_client`, `type`, `date_creation`, `due_date`, `date_validation`, `sub_total`, `discount`, `net_total`, `remove`, `status`, `remove_tva`, `paid_inv`, `comment`, `objet`, `located`) VALUES
(30, '001/2023', 24, 'Approved', '2023-05-19 15:54:46', '2023-05-19 15:54:46', '2023-05-19 15:54:46', 1230.39, 2738.61, 1476.47, 0, 'accepter', 0, 0, 'Ut cupidatat consequ', 'Omnis praesentium ma', 'Ut exercitationem ex');

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
(186, 168, '2023-05-19 15:35:40', 1),
(187, 169, '2023-05-19 15:36:51', 1),
(188, 170, '2023-05-19 15:46:13', 1),
(189, 171, '2023-05-19 15:50:39', 1),
(190, 30, '2023-05-19 16:54:46', 1),
(191, 172, '2023-05-19 17:49:10', 1),
(192, 173, '2023-05-19 17:50:22', 1),
(193, 174, '2023-05-19 17:51:43', 1),
(194, 175, '2023-05-19 17:53:01', 1),
(195, 176, '2023-05-19 17:56:36', 1),
(196, 177, '2023-05-19 17:57:20', 1),
(197, 178, '2023-05-19 18:01:42', 1),
(198, 179, '2023-05-19 22:34:47', 1),
(199, 180, '2023-05-19 22:36:29', 1),
(200, 181, '2023-05-19 22:37:56', 1),
(201, 182, '2023-05-19 22:40:30', 1);

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
(797, '001-05/2023', 780, '2023-05-19 15:36:36', 'Iusto ipsa sed in c'),
(798, '002-05/2023', 781, '2023-05-19 15:36:36', 'Iusto ipsa sed in c'),
(799, '003-05/2023', 782, '2023-05-19 15:36:36', 'Iusto ipsa sed in c');

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
(29, 'create website', 'cw4', 0.00),
(30, 'create Logo', 'cL3', 0.00);

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

--
-- Déchargement des données de la table `supplier`
--

INSERT INTO `supplier` (`id`, `full_name`, `address`, `phone`, `sold`, `cat_id`) VALUES
(6, 'Jocelyn Kirby', 'Ducimus aliquam exc', '67', 0.00, 1);

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
(4, 'Bureau de contrôle', 'Bureau de contrôle'),
(5, 'Bureau d\'étude2', 'Bureau d\'étude');

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
(1, 'user1', 'user1', 'user1@test.com', '06176726', 'owner', 'admin123', '2022-11-05 03:17:09', '2023-05-19 15:31:03', 1),
(2, 'test', 'test', 'test@test.test', '0909090909', 'admin2', 'admin123', '2022-11-26 01:55:54', '2023-05-17 22:07:36', 1),
(4, 'assistant', 'test', 'test@test.test', '12121212', 'assist', 'admin123', '2023-02-01 16:38:04', '2023-05-17 23:07:20', 1),
(5, 'user1', 'user2', 'user2@gmail.com', '0630234455', 'assistant2', 'admin123', '2023-05-16 10:58:31', '2023-05-16 12:06:41', 1);

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
(1, 'mustapha Elhathout', 'Add', '2023-05-19 14:31:46');

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
(1, 23, 'individual', 'Add', '2023-05-19 21:32:09');

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

--
-- Déchargement des données de la table `user_devis`
--

INSERT INTO `user_devis` (`id_user`, `id_devis`, `action`, `date`, `is_vue`) VALUES
(1, 168, 'Add', '2023-05-19 14:35:40', 0),
(1, 169, 'Add', '2023-05-19 14:36:51', 0),
(1, 169, 'Update', '2023-05-19 14:38:04', 0),
(1, 169, 'Update', '2023-05-19 14:38:55', 0),
(1, 169, 'Update', '2023-05-19 14:39:37', 0),
(1, 169, 'Update', '2023-05-19 14:40:51', 0),
(1, 169, 'Update', '2023-05-19 14:41:47', 0),
(1, 170, 'Add', '2023-05-19 14:46:13', 0),
(1, 171, 'Add', '2023-05-19 14:50:39', 0),
(1, 171, 'Update', '2023-05-19 14:50:57', 0),
(1, 169, 'Update', '2023-05-19 14:52:46', 0),
(1, 171, 'Update', '2023-05-19 14:56:21', 0),
(1, 171, 'Update', '2023-05-19 14:57:33', 0),
(1, 171, 'Update', '2023-05-19 14:57:54', 0),
(1, 171, 'Update', '2023-05-19 14:58:27', 0),
(1, 171, 'Update', '2023-05-19 14:59:04', 0),
(1, 169, 'Paiement Effectué', '2023-05-19 15:36:36', 0),
(1, 169, 'Paiement Effectué', '2023-05-19 15:36:36', 0),
(1, 169, 'Paiement Effectué', '2023-05-19 15:36:36', 0),
(1, 169, 'Update', '2023-05-19 15:41:30', 0),
(1, 170, 'Devis Approved', '2023-05-19 15:54:44', 0),
(1, 171, 'Devis Approved', '2023-05-19 15:59:43', 0),
(1, 171, 'Update', '2023-05-19 16:00:05', 0),
(1, 169, 'Update', '2023-05-19 16:04:04', 0),
(1, 170, 'Update', '2023-05-19 16:25:06', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:30:57', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:31:00', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:31:06', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:31:22', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:31:27', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:31:29', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:32:59', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:33:03', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:33:45', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:33:46', 0),
(1, 171, 'Devis canceled', '2023-05-19 16:33:49', 0),
(1, 171, 'Devis Approved', '2023-05-19 16:33:52', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:38:36', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:42:45', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:43:13', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:43:22', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:43:23', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:45:38', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:45:39', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:45:43', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:46:25', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:46:55', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:46:56', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:47:00', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:47:34', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:47:54', 0),
(1, 169, 'Devis Approved', '2023-05-19 16:47:56', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:48:19', 0),
(1, 169, 'Devis canceled', '2023-05-19 16:48:22', 0),
(1, 172, 'Add', '2023-05-19 16:49:10', 0),
(1, 173, 'Add', '2023-05-19 16:50:22', 0),
(1, 174, 'Add', '2023-05-19 16:51:43', 0),
(1, 175, 'Add', '2023-05-19 16:53:01', 0),
(1, 176, 'Add', '2023-05-19 16:56:36', 0),
(1, 177, 'Add', '2023-05-19 16:57:20', 0),
(1, 177, 'Devis Approved', '2023-05-19 17:00:26', 0),
(1, 177, 'Devis canceled', '2023-05-19 17:00:45', 0),
(1, 177, 'Devis Approved', '2023-05-19 17:00:47', 0),
(1, 178, 'Add', '2023-05-19 17:01:42', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:01:50', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:02:18', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:03:10', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:03:11', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:03:24', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:03:25', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:03:39', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:04:13', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:04:46', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:04:47', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:04:48', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:04:49', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:04:51', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:04:52', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:04:53', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:06:41', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:07:40', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:08:00', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:08:58', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:08:59', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:09:01', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:09:25', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:09:38', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:09:39', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:10:01', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:10:02', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:10:40', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:10:41', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:10:42', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:10:43', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:10:44', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:15:51', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:16:16', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:17:32', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:17:53', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:17:53', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:17:56', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:19:20', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:19:30', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:19:31', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:19:32', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:21:14', 0),
(1, 178, 'Devis Approved', '2023-05-19 17:21:18', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:21:45', 0),
(1, 178, 'Devis canceled', '2023-05-19 17:21:49', 0),
(1, 179, 'Add', '2023-05-19 21:34:47', 0),
(1, 180, 'Add', '2023-05-19 21:36:29', 0),
(1, 180, 'Delete', '2023-05-19 21:36:46', 0),
(1, 179, 'Update', '2023-05-19 21:36:52', 0),
(1, 179, 'Update', '2023-05-19 21:37:06', 0),
(1, 181, 'Add', '2023-05-19 21:37:56', 0),
(1, 182, 'Add', '2023-05-19 21:40:30', 0),
(1, 181, 'Devis Approved', '2023-05-19 21:42:36', 0),
(1, 181, 'Devis Approved', '2023-05-19 21:42:37', 0),
(1, 181, 'Devis Approved', '2023-05-19 21:42:38', 0),
(1, 179, 'Delete', '2023-05-19 21:52:32', 0),
(1, 181, 'Update', '2023-05-19 22:48:39', 0),
(1, 181, 'Update', '2023-05-19 22:49:14', 0),
(1, 182, 'Delete', '2023-05-19 22:49:47', 0),
(1, 181, 'Update', '2023-05-19 22:50:06', 0),
(1, 181, 'Devis canceled', '2023-05-19 22:51:06', 0),
(1, 181, 'Devis canceled', '2023-05-19 22:51:08', 0),
(1, 181, 'Devis Approved', '2023-05-19 22:51:12', 0),
(1, 181, 'Devis Approved', '2023-05-19 22:51:13', 0),
(1, 181, 'Devis Approved', '2023-05-19 23:31:29', 0),
(1, 181, 'Devis canceled', '2023-05-19 23:33:10', 0);

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

--
-- Déchargement des données de la table `user_invoice`
--

INSERT INTO `user_invoice` (`id_user`, `id_invoice`, `action`, `date`, `is_vue`) VALUES
(1, 30, 'Add', '2023-05-19 15:54:46', 0);

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
(1, 'create Logo', 'Add', '2023-05-19 14:33:17');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `broker_devis`
--
ALTER TABLE `broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `client_individual`
--
ALTER TABLE `client_individual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT pour la table `detail_devis`
--
ALTER TABLE `detail_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT pour la table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT pour la table `devis_payments`
--
ALTER TABLE `devis_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=783;

--
-- AUTO_INCREMENT pour la table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=800;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
