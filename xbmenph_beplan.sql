-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 10, 2023 at 04:58 AM
-- Server version: 10.3.38-MariaDB-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xbmenph_beplan`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `getAllDossierByBroker` (IN `broker_id` INT)   SELECT devis.id AS devis_id,devis.id_client AS client_id,devis.number, devis.objet, detail_devis.service_name,detail_devis.id AS service_id,
devis.date_creation, detail_devis.approved
FROM devis INNER JOIN detail_devis ON devis.id = detail_devis.id_devis INNER JOIN broker_devis ON 
broker_devis.id_devis = devis.id 
WHERE broker_devis.id_broker = broker_id AND detail_devis.approved=1$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getAllDossier` ()   SELECT devis.id AS devis_id,devis.id_client AS client_id,devis.number, devis.objet, detail_devis.service_name,dossier.date,detail_devis.id AS service_id
FROM devis INNER JOIN detail_devis ON devis.id=detail_devis.id_devis INNER JOIN dossier ON detail_devis.id = dossier.id_service$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getAllUsers` (IN `user_id` INT)   SELECT users.id,users.prenom,users.nom, roles.role_name,users.status,users.last_login
FROM `users` JOIN `user_role` ON users.id = user_role.user_id JOIN roles on user_role.role_id=roles.id
WHERE users.id <> 1 AND users.id <> user_id$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevis` ()   SELECT devis.id,client.id AS client_id,devis.number,
CASE
	WHEN client.type="individual" THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id)
    WHEN client.type="entreprise" THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id))
    END AS client,devis.objet,devis.date_creation,devis.net_total,devis.status,devis.type,devis.client_approve
FROM devis INNER JOIN client ON devis.id_client=client.id
WHERE devis.remove=0
ORDER BY devis.date_creation$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisPayByBroker` (IN `broker_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,client.id AS client_id,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisPayByClient` (IN `client_id` INT)   SELECT devis.id,detail_devis.id AS srv_id, devis.number,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisPaymentInfo` ()   SELECT devis.id,devis_payments.id AS pay_id,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisReceipt` (IN `payment_id` INT)   SELECT devis.number,receipt.R_number, devis_payments.pay_method,detail_devis.service_name,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisSituation` (IN `cl_id` INT)   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisSituationBoth` (IN `cl_id` INT, IN `srv_status` INT, IN `srv_name` VARCHAR(100))   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisSituationSrv` (IN `cl_id` INT, IN `srv_name` VARCHAR(100))   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDevisSituationStatus` (IN `cl_id` INT, IN `srv_status` INT)   SELECT devis.id, devis.number,devis.remove_tva,detail_devis.ref,detail_devis.service_name,detail_devis.prix,devis.date_creation,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getDossierDetail` (IN `detailDevis_id` INT)   SELECT detail_devis.id,detail_devis.ref,detail_devis.service_name, detail_devis.prix,
devis.objet
FROM detail_devis INNER JOIN devis ON detail_devis.id_devis=devis.id
WHERE detail_devis.id = detailDevis_id$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getInvoice` ()   SELECT invoice.id,client.id AS client_id,invoice.F_number,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getInvoiceNotifications` ()   SELECT DISTINCT user_invoice.id_user, invoice.id AS id_invoice,invoice.id_client,invoice.F_number,invoice.date_creation
FROM invoice JOIN user_invoice ON invoice.id = user_invoice.id_invoice
WHERE invoice.remove = 0 AND invoice.type = 'encours'
ORDER BY invoice.date_creation DESC$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getInvPayByClient` (IN `client_id` INT)   SELECT invoice.id, invoice.F_number,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getNotifications` ()   SELECT DISTINCT user_devis.id_user, devis.id AS id_devis,devis.id_client,devis.number,devis.date_creation
FROM devis JOIN user_devis ON devis.id = user_devis.id_devis
WHERE devis.remove = false AND devis.type = 'encours'
ORDER BY devis.date_creation DESC$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getPaymentInfo` ()   SELECT invoice.id,invoice_payments.id AS pay_id,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getPaymentNotification` ()   SELECT devis_payments.id AS pay_id, devis_payments.user_id, devis.id AS id_devis,
devis.id_client,devis.number,devis_payments.pay_date,detail_devis.id AS detail_id
FROM devis INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
INNER JOIN devis_payments ON detail_devis.id= devis_payments.id_devis
WHERE devis.remove=0 AND devis_payments.pending= 1$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getReceipt` (IN `payment_id` INT)   SELECT invoice.F_number,receipt.R_number, invoice_payments.pay_method,
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

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getSelectedDossier` (IN `service_id` INT)   SELECT detail_devis.ref, dossier.N_dossier,devis.objet, detail_devis.service_name,detail_devis.prix
FROM devis INNER JOIN detail_devis ON devis.id=detail_devis.id_devis INNER JOIN 
dossier ON detail_devis.id = dossier.id_service
WHERE detail_devis.id = service_id$$

CREATE DEFINER=`cpses_xbc76jdqk5`@`localhost` PROCEDURE `sp_getSituation` (IN `cl_id` INT)   SELECT invoice.id, invoice.F_number,
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
-- Table structure for table `broker`
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
-- Dumping data for table `broker`
--

INSERT INTO `broker` (`id`, `nom`, `prenom`, `phone`, `address`, `sold`) VALUES
(1, 'madani', 'said', '02912011111', 'agadir', '240.00');

-- --------------------------------------------------------

--
-- Table structure for table `broker_devis`
--

CREATE TABLE `broker_devis` (
  `id` int(11) NOT NULL,
  `id_broker` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `broker_devis`
--

INSERT INTO `broker_devis` (`id`, `id_broker`, `id_devis`) VALUES
(1, 1, 65),
(4, 1, 73),
(5, 1, 74);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `remove` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `id_client`, `remove`, `date`, `type`) VALUES
(1, 6, 0, '2022-11-16 18:08:27', 'individual'),
(2, 3, 0, '2022-11-16 18:28:03', 'entreprise'),
(3, 7, 0, '2022-11-22 20:55:03', 'individual'),
(4, 8, 0, '2022-12-16 19:57:50', 'individual'),
(5, 4, 0, '2023-01-03 20:46:18', 'entreprise'),
(6, 9, 0, '2023-01-18 16:14:28', 'individual'),
(7, 10, 0, '2023-02-01 14:16:03', 'individual'),
(8, 5, 0, '2023-02-01 14:16:34', 'entreprise'),
(9, 11, 0, '2023-02-01 18:25:56', 'individual'),
(10, 6, 0, '2023-02-01 18:26:30', 'entreprise');

-- --------------------------------------------------------

--
-- Table structure for table `client_entreprise`
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
-- Dumping data for table `client_entreprise`
--

INSERT INTO `client_entreprise` (`id`, `nom`, `ICE`, `email`, `tel`, `address`, `solde`, `delete_status`) VALUES
(3, 'dsf', ' 2334EZ', 'sdf@sf.comqsd', '2342424', ' sdf12qsd', '0', 0),
(4, 'SARL XXX', ' 23197202783', 'XXX@test.com', '0928227837', ' adressXXXXXX', '0', 0),
(5, 'agency', ' 6546946484', 'rth@fg.com', '65464646', ' 12 sdfs sg', '0', 0),
(6, 'ADMDI', ' 45678676', 'tst@fgh.dtz', '546464111', ' 12 fzf agadir', '0', 0);

--
-- Triggers `client_entreprise`
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
-- Table structure for table `client_individual`
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
-- Dumping data for table `client_individual`
--

INSERT INTO `client_individual` (`id`, `prenom`, `nom`, `email`, `tel`, `address`, `solde`, `delete_status`) VALUES
(6, 'aze', 'qsd', 'sdf@sf.com', '0992829', '123qdqsd', '0', 0),
(7, 'client', 'last', 'email@test.com', '0019823', 'agadir inzegan', '0', 0),
(8, 'jhon', 'doe', 'jhonDoe@email.com', '0656875429', 'jhon doe address', '0', 0),
(9, 'kkkk', 'kkoo', 'test@dgjd.ckfz', '050255', 'dghfsj', '0', 0),
(10, 'brahim', 'ben', 'fhsfn@dhd.com', '12121212', 'agadir', '0', 0),
(11, 'mouad', 'ab', 'test@test.test', '02121212', ' 45 test test', '0', 0);

--
-- Triggers `client_individual`
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
-- Table structure for table `detail_broker_devis`
--

CREATE TABLE `detail_broker_devis` (
  `id` int(11) NOT NULL,
  `id_broker_devis` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_broker_devis`
--

INSERT INTO `detail_broker_devis` (`id`, `id_broker_devis`, `prix`) VALUES
(3, 4, '199.99'),
(4, 4, '1000.00'),
(5, 5, '100.00'),
(6, 5, '300.00');

-- --------------------------------------------------------

--
-- Table structure for table `detail_devis`
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
-- Dumping data for table `detail_devis`
--

INSERT INTO `detail_devis` (`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`, `approved`, `confirmed`, `paid_srv`, `srv_avance`, `payment_made`, `srv_notif`) VALUES
(12, 44, 'service1', '100.00', 1, '0.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(13, 44, 'service2', '199.99', 1, '0.00', 'm', 'sv2', 0, 0, 0, '0.00', 0, 0),
(14, 44, 'srev3', '10.00', 1, '10.00', 'u', 'sv3', 0, 0, 0, '0.00', 0, 0),
(52, 50, 'some service', '1000.00', 1, '0.00', 'u', 'ssrv1', 0, 0, 1, '0.00', 0, 0),
(53, 51, 'serv1', '1231.00', 1, '0.00', 'u', 's1', 0, 0, 0, '0.00', 1, 0),
(54, 51, 'serv2', '234.00', 1, '0.00', 'm', 's2', 0, 0, 0, '80.00', 1, 0),
(55, 51, 'serv3', '1234.00', 1, '0.00', 'm²', 's3', 0, 0, 0, '0.00', 0, 0),
(56, 51, 'ser4', '1242.00', 1, '0.00', 'u', 's4', 0, 0, 0, '0.00', 0, 0),
(57, 51, 'serv5', '1242.00', 1, '0.00', 'u', 's5', 0, 0, 0, '0.00', 0, 0),
(58, 52, 'service1', '100.00', 1, '10.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(60, 54, 'service2', '199.99', 1, '0.00', 'm', 'sv2', 0, 0, 0, '0.00', 0, 0),
(73, 1, 'service2', '199.99', 1, '0.00', 'u', 'sv2', 0, 0, 0, '0.00', 0, 0),
(74, 1, 'service1', '100.00', 1, '10.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(75, 1, 'serv3', '20.00', 2, '0.00', 'u', 's3', 0, 0, 0, '0.00', 0, 0),
(78, 55, 'service1', '100.00', 1, '0.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(81, 58, 'service1', '100.00', 1, '0.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(82, 58, 'service2', '199.99', 1, '0.00', 'u', 'sv2', 0, 0, 0, '0.00', 0, 0),
(85, 60, 'service1', '100.00', 1, '0.00', 'u', 'sv1', 0, 0, 0, '0.00', 0, 0),
(86, 60, 'service2', '199.99', 1, '0.00', 'u', 'sv2', 0, 0, 0, '0.00', 0, 0),
(87, 61, 'service1', '100.00', 1, '0.00', 'u', 'sv1', 0, 0, 1, '0.00', 1, 0),
(88, 61, 'serv4', '2000.00', 1, '0.00', 'u', 's4', 0, 0, 1, '0.00', 0, 0),
(121, 62, 'serv1', '1200.00', 1, '0.00', 'U', 's1', 1, 0, 1, '0.00', 0, 0),
(122, 62, 'serv2', '2013.00', 1, '0.00', 'U', 's2', 0, 0, 1, '0.00', 0, 0),
(123, 62, 'serv3', '1234.00', 1, '0.00', 'F', 's3', 0, 0, 0, '0.00', 0, 0),
(124, 62, 'serv4', '3234.00', 1, '0.00', 'U', 's4', 0, 0, 0, '0.00', 0, 0),
(125, 62, 'serv5', '873.00', 1, '0.00', 'm', 's5', 0, 0, 0, '0.00', 0, 0),
(126, 62, 'serv6', '912.00', 1, '0.00', 'F', 's6', 0, 0, 0, '0.00', 0, 0),
(127, 62, 'serv7', '1002.00', 1, '0.00', 'U', 's7', 0, 0, 0, '0.00', 0, 0),
(128, 62, 'serv8', '130.00', 1, '0.00', 'F', 's8', 0, 0, 0, '0.00', 0, 0),
(129, 62, 'serv9', '200.00', 1, '0.00', 'm', 's9', 0, 0, 0, '0.00', 0, 0),
(130, 62, 'serv10', '300.00', 1, '0.00', 'U', 's10', 0, 0, 0, '0.00', 0, 0),
(131, 62, 'serv11', '500.00', 1, '0.00', 'F', 's11', 0, 0, 0, '0.00', 0, 0),
(132, 62, 'serv12', '123.00', 1, '0.00', 'U', 's12', 0, 0, 0, '0.00', 0, 0),
(133, 62, 'serv13', '244.00', 1, '0.00', 'F', 's13', 0, 0, 0, '0.00', 0, 0),
(134, 62, 'serv14', '124.00', 1, '0.00', 'U', 's14', 0, 0, 0, '0.00', 0, 0),
(135, 62, 'serv15', '1245.00', 1, '0.00', 'F', 's15', 0, 0, 0, '0.00', 0, 0),
(136, 62, 'serv16', '762.00', 1, '0.00', 'F', 's16', 0, 0, 0, '0.00', 0, 0),
(137, 62, 'serv17', '234.00', 1, '0.00', 'U', 's17', 0, 0, 0, '0.00', 0, 0),
(138, 62, 'serv18', '125.00', 1, '0.00', 'm', 's18', 0, 0, 0, '0.00', 0, 0),
(139, 62, 'serv19', '126.00', 1, '0.00', 'U', 's19', 0, 0, 0, '0.00', 0, 0),
(140, 62, 'serv20', '1235.00', 1, '0.00', 'F', 's20', 0, 0, 0, '0.00', 0, 0),
(141, 62, 'serv21', '728.00', 1, '0.00', 'm', 's21', 0, 0, 0, '0.00', 0, 0),
(142, 62, 'serv22', '1253.00', 1, '0.00', 'U', 's22', 0, 0, 0, '0.00', 0, 0),
(144, 49, 'qsd', '12.00', 1, '0.00', 'u', 'q1', 0, 0, 0, '0.00', 0, 0),
(145, 63, 'service2', '199.99', 1, '0.00', 'U', 'sv2', 0, 0, 0, '0.00', 0, 0),
(146, 64, 'service', '200.00', 1, '0.00', 'u', 's0', 1, 0, 0, '0.00', 0, 0),
(147, 64, 'test service', '1000.00', 1, '0.00', 'u', 'tstS0', 0, 0, 0, '0.00', 0, 0),
(148, 65, 'service 3', '200.00', 1, '0.00', 'u', 'sv3', 1, 1, 1, '0.00', 0, 0),
(149, 65, 'service 4', '200.00', 1, '0.00', 'm', 'sv4', 0, 0, 1, '0.00', 0, 0),
(150, 50, 'creation plan 3d', '300.00', 1, '0.00', 'f', 'cp3d', 0, 0, 0, '0.00', 0, 0),
(161, 73, 'service2', '199.99', 1, '0.00', 'X', 'sv2', 0, 0, 1, '0.00', 0, 0),
(162, 73, 'service 10', '1000.00', 1, '0.00', 'Y', 's10', 0, 0, 1, '0.00', 0, 0),
(163, 74, 'service1', '200.00', 1, '0.00', 'D', 'sv1', 0, 0, 1, '0.00', 0, 0),
(164, 74, 'service 4', '400.00', 1, '0.00', 'Q', 'sv4', 0, 0, 1, '0.00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `detail_invoice`
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
-- Dumping data for table `detail_invoice`
--

INSERT INTO `detail_invoice` (`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`) VALUES
(1, 2, 'service1', '100.00', 1, '0.00', 'u', 'sv1'),
(2, 1, 'service2', '199.99', 1, '0.00', 'u', 'sv2'),
(3, 3, 'service1', '100.00', 1, '0.00', 'm', 'sv1'),
(4, 4, 'serv3', '124.00', 1, '0.00', 'u', 'sv3'),
(5, 4, 'ser4', '1000.00', 1, '0.00', 'm²', 's4'),
(6, 5, 'serv5', '1500.00', 1, '0.00', 'u', 'sv5'),
(7, 6, 'service1', '100.00', 1, '0.00', 'm', 'sv1'),
(8, 7, 'service1', '100.00', 1, '0.00', 'u', 'sv1'),
(9, 7, 'serv3', '2000.00', 1, '0.00', 'm²', 'sv3'),
(10, 8, 'serv5', '2500.00', 1, '0.00', 'u', 'sv5'),
(11, 9, 'service1', '10023.00', 1, '0.00', 'm', 'sv1'),
(12, 10, 'liljlio', '1200.00', 1, '0.00', 'u', 'l1'),
(17, 12, 'serv5', '2000.00', 1, '0.00', 'm²', 'sv5'),
(18, 12, 'serv6', '3000.00', 1, '0.00', 'm²', 'sv6'),
(19, 12, 'serv7', '200.00', 1, '0.00', 'm²', 'sv7'),
(20, 13, 'service1', '100.00', 1, '0.00', 'm²', 'sv1'),
(21, 13, 'serv4', '1234.00', 1, '0.00', 'u', 'sv4'),
(22, 14, 'service1', '100.00', 1, '0.00', 'm²', 'sv1'),
(23, 14, 'ser4', '1243.00', 1, '0.00', 'm', 's4'),
(24, 15, 'service2', '199.99', 1, '0.00', 'm²', 'sv2'),
(25, 15, 'serv3', '321.00', 1, '0.00', 'm²', 'sv3'),
(26, 16, 'serv10', '1000.00', 1, '0.00', 'm', 'sv10'),
(27, 16, 'serv11', '300.00', 2, '0.00', 'u', 'sv11'),
(28, 17, 'service1', '100.00', 1, '0.00', 'u', 'sv1'),
(29, 17, 'setrtss', '100.00', 1, '0.00', 'kg', 'setr1'),
(35, 22, 'service 3', '200.00', 1, '0.00', 'u', 'sv3');

-- --------------------------------------------------------

--
-- Table structure for table `devis`
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
-- Dumping data for table `devis`
--

INSERT INTO `devis` (`id`, `number`, `id_client`, `type`, `date_creation`, `date_validation`, `sub_total`, `discount`, `net_total`, `remove`, `status`, `remove_tva`, `client_approve`, `comment`, `objet`, `located`) VALUES
(1, '001/2022', 1, 'encours', '2022-11-16 18:29:24', '2022-11-16 18:29:24', '329.99', '10.00', '395.99', 0, 'encours', 0, 0, 'something', '', ''),
(44, '009/2022', 2, 'encours', '2022-11-20 20:23:04', '2022-11-20 20:23:04', '308.99', '1.00', '370.79', 0, 'encours', 0, 0, '', '', ''),
(49, '028/2022', 2, 'Approved', '2022-11-22 21:03:17', '2023-01-12 16:06:01', '12.00', '0.00', '14.40', 0, 'accepter', 0, 1, '', 'objetobjet...', ''),
(50, '031/2022', 3, 'Approved', '2022-11-22 21:27:25', '2023-04-19 18:44:10', '1300.00', '0.00', '1560.00', 0, 'accepter', 0, 0, '', 'szsz', 'dededed'),
(51, '032/2022', 3, 'Approved', '2022-11-22 22:09:42', '2022-12-19 15:00:49', '5183.00', '0.00', '6219.60', 0, 'accepter', 0, 1, '', '', ''),
(52, '033/2022', 3, 'encours', '2022-11-23 13:58:44', '2022-11-23 13:58:44', '90.00', '10.00', '108.00', 0, 'encours', 0, 0, '', '', ''),
(54, '027/2022', 3, 'Approved', '2022-11-23 21:22:05', '2023-04-19 15:36:56', '399.99', '0.00', '399.99', 0, 'accepter', 1, 0, '', 'test', ''),
(55, '029/2022', 3, 'Approved', '2022-11-28 19:08:32', '2022-11-28 21:28:57', '100.00', '0.00', '120.00', 0, 'accepter', 0, 1, '', '', ''),
(58, '034/2022', 3, 'Declined', '2022-11-28 20:39:20', '2022-11-28 21:34:42', '299.99', '0.00', '359.99', 0, 'rejeter', 0, 0, '', '', ''),
(60, '035/2022', 3, 'Approved', '2022-11-29 14:03:56', '2022-12-07 23:40:09', '299.99', '0.00', '359.99', 0, 'accepter', 0, 1, '', '', ''),
(61, '036/2022', 4, 'Approved', '2022-12-16 19:59:29', '2022-12-16 20:06:24', '2100.00', '0.00', '2520.00', 0, 'accepter', 0, 0, '', 'complex project', ''),
(62, '001/2023', 4, 'Approved', '2023-01-11 14:40:04', '2023-01-12 15:28:18', '17797.00', '0.00', '21356.40', 0, 'accepter', 0, 0, '', 'test height', ''),
(63, '002/2023', 3, 'Approved', '2023-01-18 16:23:07', '2023-01-18 16:23:31', '199.99', '0.00', '239.99', 0, 'accepter', 0, 0, '', 'tester', ''),
(64, '003/2023', 8, 'Approved', '2023-02-01 16:40:09', '2023-02-01 16:40:21', '1200.00', '0.00', '1440.00', 0, 'accepter', 0, 0, '', 'test test', ''),
(65, '004/2023', 10, 'Approved', '2023-02-23 17:00:33', '2023-02-23 17:00:39', '400.00', '0.00', '480.00', 0, 'accepter', 0, 0, '', 'test broker', ''),
(73, '005/2023', 7, 'Approved', '2023-04-28 21:26:17', '2023-04-28 20:27:05', '1199.99', '0.00', '1439.99', 0, 'accepter', 0, 0, '', 'lala', 'malaa'),
(74, '006/2023', 9, 'Approved', '2023-04-29 14:13:55', '2023-04-29 13:14:23', '600.00', '0.00', '720.00', 0, 'accepter', 0, 0, '', 'xxaxa', 'xaxaxa');

-- --------------------------------------------------------

--
-- Table structure for table `devis_payments`
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
-- Dumping data for table `devis_payments`
--

INSERT INTO `devis_payments` (`id`, `id_devis`, `prix`, `pay_method`, `user_id`, `pay_date`, `pending`) VALUES
(42, 87, '120.00', 'Espéce', 1, '2023-04-19 18:04:06', 0),
(43, 88, '400.00', 'Espéce', 1, '2023-04-19 18:04:06', 0),
(535, 88, '2000.00', 'Espéce', 1, '2023-04-19 21:36:43', 0),
(536, 121, '1000.00', 'Espéce', 1, '2023-04-19 21:36:43', 0),
(537, 121, '440.00', 'Espéce', 1, '2023-04-19 21:39:41', 0),
(538, 122, '2415.60', 'Espéce', 1, '2023-04-19 21:39:42', 0),
(539, 123, '880.00', 'Espéce', 1, '2023-04-19 21:39:42', 0),
(541, 148, '240.00', 'Espéce', 1, '2023-04-29 13:31:46', 0),
(542, 149, '240.00', 'Espéce', 1, '2023-04-29 13:31:47', 0),
(719, 161, '239.99', 'Espéce', 1, '2023-04-29 14:11:04', 0),
(720, 162, '1200.00', 'Espéce', 1, '2023-04-29 14:11:05', 0),
(733, 163, '240.00', 'Espéce', 1, '2023-04-29 15:05:32', 0),
(734, 164, '480.00', 'Espéce', 1, '2023-04-29 15:05:33', 0),
(735, 52, '200.00', 'Espéce', 1, '2023-05-07 21:53:39', 0),
(736, 52, '200.00', 'Espéce', 1, '2023-05-07 21:56:28', 0),
(737, 52, '100.00', 'Espéce', 1, '2023-05-07 22:05:40', 0),
(738, 52, '100.00', 'Espéce', 1, '2023-05-07 22:06:14', 0),
(739, 52, '600.00', 'Espéce', 1, '2023-05-07 22:07:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `devis_to_service`
--

CREATE TABLE `devis_to_service` (
  `id_devis` int(11) NOT NULL,
  `id_service` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dossier`
--

CREATE TABLE `dossier` (
  `id` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `N_dossier` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dossier`
--

INSERT INTO `dossier` (`id`, `id_service`, `N_dossier`, `date`) VALUES
(1, 146, '001', '2023-02-23 14:36:50'),
(2, 148, '002', '2023-02-23 22:41:43'),
(3, 121, '1234MM', '2023-02-28 14:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
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
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `F_number`, `id_client`, `type`, `date_creation`, `due_date`, `date_validation`, `sub_total`, `discount`, `net_total`, `remove`, `status`, `remove_tva`, `paid_inv`, `comment`, `objet`, `located`) VALUES
(1, '001/2022', 1, 'encours', '2022-11-30 17:04:19', '2022-12-14 00:00:00', '2022-11-30 17:04:19', '199.99', '0.00', '239.99', 0, 'encours', 0, 0, '', '', ''),
(2, '002/2022', 3, 'encours', '2022-12-06 00:14:48', '2022-12-08 00:00:00', '2022-12-06 00:14:48', '100.00', '0.00', '120.00', 0, 'encours', 0, 0, '', '', ''),
(3, '003/2022', 3, 'Approved', '2022-12-07 21:51:07', '2022-12-22 00:00:00', '2022-12-07 23:51:58', '100.00', '0.00', '120.00', 0, 'accepter', 0, 1, '', '', ''),
(4, '004/2022', 3, 'Approved', '2022-12-07 23:30:45', '2022-12-31 00:00:00', '2022-12-08 00:09:02', '1124.00', '0.00', '1348.80', 0, 'accepter', 0, 0, '', '', ''),
(5, '005/2022', 3, 'Approved', '2022-12-10 13:45:12', '2022-12-30 00:00:00', '2022-12-12 00:24:51', '1500.00', '0.00', '1800.00', 0, 'accepter', 0, 0, '', '', ''),
(6, '006/2022', 3, 'Approved', '2022-12-14 19:44:14', '2022-12-14 00:00:00', '2022-12-14 19:44:34', '100.00', '0.00', '120.00', 0, 'accepter', 0, 1, '', 'objet de cette facture', ''),
(7, '007/2022', 4, 'Approved', '2022-12-16 21:35:18', '2022-12-16 00:00:00', '2022-12-16 21:47:07', '2100.00', '0.00', '2520.00', 0, 'accepter', 0, 1, '', 'complex project', ''),
(8, '008/2022', 4, 'Approved', '2022-12-18 22:35:14', '2022-12-18 00:00:00', '2022-12-18 22:40:08', '2500.00', '0.00', '3000.00', 0, 'accepter', 0, 0, '', 'some objet', ''),
(9, '009/2022', 4, 'Approved', '2022-12-28 18:20:01', '2022-12-28 00:00:00', '2022-12-28 18:20:23', '10023.00', '0.00', '12027.60', 0, 'accepter', 0, 1, '', 'lakjazlk', ''),
(10, '010/2022', 4, 'Approved', '2022-12-28 18:24:06', '2022-12-28 00:00:00', '2022-12-28 18:24:46', '1200.00', '0.00', '1200.00', 0, 'accepter', 1, 0, '', 'jkhkjhjk', ''),
(12, '001/2023', 3, 'Approved', '2023-01-01 21:31:56', '2023-01-01 00:00:00', '2023-01-01 21:32:46', '5200.00', '0.00', '6240.00', 0, 'accepter', 0, 1, '', 'test payment_test', ''),
(13, '002/2023', 4, 'Approved', '2023-01-02 19:53:06', '2023-01-02 00:00:00', '2023-01-02 19:53:46', '1334.00', '0.00', '1600.80', 0, 'accepter', 0, 0, '', 'test test', ''),
(14, '003/2023', 5, 'Approved', '2023-01-05 20:09:10', '2023-01-05 00:00:00', '2023-01-05 20:09:44', '1343.00', '0.00', '1611.60', 0, 'accepter', 0, 0, '', 'Dash test', ''),
(15, '004/2023', 4, 'Approved', '2023-01-05 20:12:09', '2023-01-05 00:00:00', '2023-01-05 20:12:23', '520.99', '0.00', '625.19', 0, 'accepter', 0, 0, '', 'objet test4', ''),
(16, '005/2023', 5, 'Approved', '2023-01-09 20:29:29', '2023-01-09 00:00:00', '2023-01-09 20:40:12', '1600.00', '0.00', '1920.00', 0, 'accepter', 0, 1, '', 'test test test', ''),
(17, '006/2023', 7, 'Approved', '2023-02-01 16:42:05', '2023-02-01 00:00:00', '2023-02-01 16:42:21', '200.00', '0.00', '240.00', 0, 'accepter', 0, 0, '', 'test test', ''),
(22, '007/2023', 10, 'Approved', '2023-04-20 03:54:25', '2023-04-20 00:00:00', '2023-04-20 03:54:36', '400.00', '0.00', '480.00', 0, 'accepter', 0, 0, '', 'test broker', ''),
(23, '008/2023', 9, 'Approved', '2023-05-02 14:41:59', '2023-05-02 00:00:00', '2023-05-02 14:43:50', '1200.00', '0.00', '1440.00', 0, 'accepter', 0, 0, '', 'szsz', 'ccxcxc');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_payments`
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
-- Dumping data for table `invoice_payments`
--

INSERT INTO `invoice_payments` (`id`, `id_invoice`, `prix`, `pay_method`, `user_id`, `pay_date`, `pending`) VALUES
(25, 3, '120.00', 'Espéce', 1, '2022-12-13 17:55:57', 0),
(31, 6, '120.00', 'Espéce', 1, '2022-12-14 19:44:15', 0),
(32, 8, '500.00', 'Espéce', 1, '2022-12-18 22:35:14', 0),
(33, 9, '12027.60', 'Espéce', 1, '2022-12-28 18:20:02', 0),
(34, 10, '600.00', 'Check', 1, '2022-12-28 18:24:06', 0),
(35, 7, '2000.00', 'Check', 1, '2022-12-28 18:25:55', 0),
(38, 7, '520.00', 'Espéce', 1, '2023-01-01 18:16:51', 0),
(39, 8, '420.00', 'Espéce', 1, '2023-01-01 18:16:52', 0),
(40, 12, '6240.00', 'Espéce', 1, '2023-01-01 21:31:57', 0),
(41, 13, '600.00', 'Espéce', 1, '2023-01-02 20:03:18', 0),
(42, 16, '920.00', 'Espéce', 1, '2023-01-09 20:29:30', 0),
(43, 14, '1000.00', 'Espéce', 2, '2023-02-01 16:29:12', 0),
(44, 16, '1000.00', 'Check', 4, '2023-02-01 16:43:21', 0),
(45, 15, '300.00', 'Espéce', 2, '2023-02-01 16:47:41', 0),
(46, 5, '1000.00', 'Espéce', 4, '2023-02-01 16:49:00', 0);

--
-- Triggers `invoice_payments`
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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_document` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `id_document`, `date`, `active`) VALUES
(1, 55, '2022-11-28 19:08:32', 0),
(4, 58, '2022-11-28 20:39:20', 0),
(5, 59, '2022-11-29 12:32:23', 0),
(6, 60, '2022-11-29 14:03:57', 0),
(7, 4, '2022-12-07 23:30:46', 0),
(8, 5, '2022-12-10 13:45:12', 0),
(12, 3, '2022-12-12 18:18:21', 0),
(13, 3, '2022-12-12 20:59:26', 0),
(17, 5, '2022-12-13 20:56:38', 0),
(18, 5, '2022-12-13 20:58:32', 0),
(19, 5, '2022-12-13 21:04:01', 0),
(20, 6, '2022-12-14 19:44:19', 0),
(21, 61, '2022-12-16 19:59:29', 0),
(22, 7, '2022-12-16 21:35:19', 0),
(23, 8, '2022-12-18 22:35:15', 0),
(24, 9, '2022-12-28 18:20:03', 0),
(25, 10, '2022-12-28 18:24:07', 0),
(26, 11, '2023-01-01 17:01:39', 0),
(27, 12, '2023-01-01 21:31:58', 0),
(28, 13, '2023-01-02 19:53:07', 0),
(29, 13, '2023-01-02 20:03:19', 0),
(30, 14, '2023-01-05 20:09:13', 0),
(31, 15, '2023-01-05 20:12:10', 0),
(32, 16, '2023-01-09 20:29:30', 0),
(33, 62, '2023-01-11 14:40:07', 0),
(34, 63, '2023-01-18 16:23:07', 0),
(35, 64, '2023-02-01 16:40:09', 0),
(36, 17, '2023-02-01 16:42:05', 0),
(37, 16, '2023-02-01 16:43:21', 0),
(38, 15, '2023-02-01 16:47:41', 0),
(39, 5, '2023-02-01 16:49:00', 0),
(40, 65, '2023-02-23 17:00:33', 0),
(45, 142, '2023-04-19 07:23:33', 0),
(46, 88, '2023-04-19 20:45:49', 0),
(47, 88, '2023-04-19 21:15:38', 0),
(48, 88, '2023-04-19 21:21:40', 0),
(49, 88, '2023-04-19 21:24:11', 0),
(50, 88, '2023-04-19 21:32:27', 0),
(51, 88, '2023-04-19 21:36:43', 0),
(52, 148, '2023-04-19 23:21:05', 0),
(53, 68, '2023-04-20 03:49:34', 0),
(54, 69, '2023-04-20 03:50:04', 0),
(55, 22, '2023-04-20 03:54:25', 0),
(56, 70, '2023-04-20 03:56:09', 0),
(57, 71, '2023-04-20 04:00:51', 0),
(58, 72, '2023-04-20 04:01:22', 0),
(59, 73, '2023-04-28 20:26:17', 0),
(60, 74, '2023-04-29 13:13:56', 0),
(61, 52, '2023-05-07 21:06:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `perm_desc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
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
-- Table structure for table `purchase`
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

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`id`, `P_number`, `name`, `price`, `note`, `date`, `remove`) VALUES
(1, '001/2022', 'achat1', '1234.00', 'something got updated......', '2022-12-14 13:57:30', 0),
(2, '001/2023', 'fatima', '300.00', 'femme de minage', '2023-02-01 18:57:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `R_number` varchar(100) NOT NULL,
  `id_payment` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `pay_giver` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`id`, `R_number`, `id_payment`, `date`, `pay_giver`) VALUES
(65, '001-04/2023', 42, '2023-04-19 18:04:06', 'azrd'),
(66, '002-04/2023', 43, '2023-04-19 18:04:06', 'azrd'),
(553, '003-04/2023', 535, '2023-04-19 21:36:43', 'azed'),
(554, '004-04/2023', 536, '2023-04-19 21:36:43', 'azed'),
(555, '005-04/2023', 537, '2023-04-19 21:39:42', 'azeddine'),
(556, '006-04/2023', 538, '2023-04-19 21:39:42', 'azeddine'),
(557, '007-04/2023', 539, '2023-04-19 21:39:42', 'azeddine'),
(559, '008-04/2023', 541, '2023-04-29 13:31:47', 'azede'),
(560, '009-04/2023', 542, '2023-04-29 13:31:47', 'azede'),
(736, '010-04/2023', 719, '2023-04-29 14:11:05', 'azedx'),
(737, '011-04/2023', 720, '2023-04-29 14:11:05', 'azedx'),
(750, '012-04/2023', 733, '2023-04-29 15:05:32', 'azxxz'),
(751, '013-04/2023', 734, '2023-04-29 15:05:33', 'azxxz'),
(752, '001-05/2023', 735, '2023-05-07 21:53:39', 'azede'),
(753, '002-05/2023', 736, '2023-05-07 21:56:28', 'azeddine'),
(754, '003-05/2023', 737, '2023-05-07 22:05:40', 'test'),
(755, '004-05/2023', 738, '2023-05-07 22:06:14', 'azed'),
(756, '005-05/2023', 739, '2023-05-07 22:07:41', 'azeddine');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(3, 'Owner'),
(5, 'admin'),
(6, 'assistant');

-- --------------------------------------------------------

--
-- Table structure for table `role_perm`
--

CREATE TABLE `role_perm` (
  `role_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_perm`
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
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ref` varchar(100) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `title`, `ref`, `prix`) VALUES
(2, 'service1', 'sv1', '100.00'),
(3, 'service2', 'sv2', '199.99'),
(4, 'service 3', 'sv3', '200.00'),
(5, 'service 4', 'sv4', '200.00'),
(6, 'creation plan 3d', 'cp3d', '300.00'),
(15, 'test', 'tst1', '21.00'),
(18, 'service 10', 's10', '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `situation`
--

CREATE TABLE `situation` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `situation`
--

INSERT INTO `situation` (`id`, `id_client`, `date`) VALUES
(1, 4, '2022-12-31 21:35:19'),
(2, 4, '2022-12-31 21:37:26'),
(3, 4, '2022-12-31 21:39:30'),
(4, 4, '2022-12-31 21:42:25'),
(5, 4, '2022-12-31 21:45:28'),
(6, 4, '2022-12-31 21:46:02'),
(7, 4, '2022-12-31 21:46:25'),
(8, 4, '2022-12-31 21:46:45'),
(9, 4, '2022-12-31 21:47:13'),
(10, 5, '2023-01-18 14:54:33'),
(11, 5, '2023-02-01 16:22:20'),
(12, 4, '2023-02-01 16:22:38'),
(13, 4, '2023-02-01 16:24:29'),
(14, 3, '2023-02-01 16:24:50'),
(15, 5, '2023-02-01 16:29:37'),
(16, 5, '2023-02-01 16:30:39'),
(17, 3, '2023-02-01 18:55:06'),
(18, 4, '2023-02-01 18:55:47'),
(19, 4, '2023-02-03 16:07:51'),
(20, 5, '2023-02-06 21:11:48'),
(21, 4, '2023-04-19 23:27:24'),
(22, 10, '2023-04-19 23:27:53'),
(23, 10, '2023-04-19 23:27:59'),
(24, 4, '2023-04-20 00:48:00'),
(25, 4, '2023-04-20 02:38:09'),
(26, 4, '2023-04-20 02:38:31'),
(27, 4, '2023-04-20 02:38:47'),
(28, 4, '2023-04-20 02:39:45'),
(29, 4, '2023-04-20 02:39:56'),
(30, 4, '2023-04-20 02:40:36'),
(31, 4, '2023-04-20 02:41:39'),
(32, 4, '2023-04-20 02:41:48'),
(33, 4, '2023-04-20 02:41:57'),
(34, 4, '2023-04-20 02:42:07'),
(35, 4, '2023-04-20 02:42:19'),
(36, 4, '2023-04-20 02:46:16'),
(37, 4, '2023-04-20 02:49:03'),
(38, 3, '2023-04-20 02:49:21'),
(39, 4, '2023-04-26 12:46:13'),
(40, 4, '2023-04-26 12:46:48'),
(41, 8, '2023-04-26 12:47:39'),
(42, 4, '2023-04-26 12:53:54'),
(43, 8, '2023-04-26 12:54:26'),
(44, 8, '2023-04-26 12:55:00'),
(45, 7, '2023-04-26 14:08:34'),
(46, 4, '2023-04-26 14:08:47'),
(47, 3, '2023-04-26 14:10:04'),
(48, 3, '2023-04-26 14:10:06'),
(49, 4, '2023-04-26 14:12:14'),
(50, 3, '2023-04-26 14:33:55'),
(51, 4, '2023-04-26 15:34:23'),
(52, 4, '2023-04-26 16:47:46'),
(53, 4, '2023-04-28 12:20:14'),
(54, 4, '2023-04-28 16:56:32'),
(55, 4, '2023-04-28 16:58:09'),
(56, 4, '2023-04-28 16:58:21'),
(57, 3, '2023-04-28 17:13:42'),
(58, 3, '2023-04-28 17:14:28'),
(59, 3, '2023-04-28 17:16:48'),
(60, 3, '2023-04-28 17:16:55'),
(61, 3, '2023-04-28 17:35:09'),
(62, 3, '2023-04-28 17:37:35'),
(63, 3, '2023-04-28 17:39:27'),
(64, 3, '2023-04-28 17:40:47'),
(65, 3, '2023-04-28 18:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
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
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `full_name`, `address`, `phone`, `sold`, `cat_id`) VALUES
(1, 'Fourn1', 'Agadir', '1212121233', '0.00', 1),
(3, 'Fourni2', 'xxxx', '112121212121', '300.00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `supp_category`
--

CREATE TABLE `supp_category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supp_category`
--

INSERT INTO `supp_category` (`id`, `title`, `type`) VALUES
(1, 'Beplan', 'Bureau d\'étude'),
(4, 'Copol', 'Bureau de controle');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `prenom`, `nom`, `email`, `tel`, `username`, `password`, `date`, `last_login`, `status`) VALUES
(1, 'user1', 'user1', 'user1@test.com', '06176726', 'owner', 'admin123', '2022-11-05 03:17:09', '2023-05-09 22:47:09', 1),
(2, 'test', 'test', 'test@test.test', '0909090909', 'admin2', 'admin123', '2022-11-26 01:55:54', '2023-05-02 12:03:41', 1),
(4, 'assistant', 'test', 'test@test.test', '12121212', 'assist', 'admin123', '2023-02-01 16:38:04', '2023-02-01 18:23:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_broker`
--

CREATE TABLE `user_broker` (
  `id_user` int(11) NOT NULL,
  `broker` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_broker`
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
(1, 'asazs sqsq', 'Add', '2023-05-07 17:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_client`
--

CREATE TABLE `user_client` (
  `id_user` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `cl_type` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_client`
--

INSERT INTO `user_client` (`id_user`, `id_client`, `cl_type`, `action`, `date`) VALUES
(1, 6, 'individual', 'Update', '2022-12-18 12:19:54'),
(1, 4, 'entreprise', 'Add', '2023-01-03 20:46:18'),
(1, 9, 'individual', 'Add', '2023-01-18 16:14:28'),
(2, 10, 'individual', 'Add', '2023-02-01 14:16:03'),
(2, 5, 'entreprise', 'Add', '2023-02-01 14:16:34'),
(2, 11, 'individual', 'Add', '2023-02-01 18:25:56'),
(2, 6, 'entreprise', 'Add', '2023-02-01 18:26:30'),
(1, 6, 'entreprise', 'Update', '2023-02-06 21:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_devis`
--

CREATE TABLE `user_devis` (
  `id_user` int(11) NOT NULL,
  `id_devis` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_devis`
--

INSERT INTO `user_devis` (`id_user`, `id_devis`, `action`, `date`) VALUES
(1, 55, 'Add', '2022-11-28 19:08:32'),
(1, 58, 'Add', '2022-11-28 20:39:20'),
(1, 60, 'Add', '2022-11-29 14:03:56'),
(1, 61, 'Add', '2022-12-16 19:59:29'),
(1, 49, 'Update', '2022-12-18 15:11:52'),
(1, 51, 'Add', '2022-12-19 15:00:33'),
(1, 51, 'Devis Approved by client', '2022-12-19 15:00:57'),
(1, 49, 'Devis Approved by client', '2022-12-28 18:15:48'),
(1, 61, 'Devis canceled', '2023-01-02 23:33:59'),
(1, 61, 'Devis Approved by client', '2023-01-02 23:37:10'),
(1, 62, 'Add', '2023-01-11 14:40:07'),
(1, 62, 'Update', '2023-01-11 20:57:37'),
(1, 62, 'Update', '2023-01-12 15:27:52'),
(1, 49, 'Update', '2023-01-12 16:05:16'),
(1, 49, 'Update', '2023-01-12 16:05:53'),
(1, 61, 'Devis canceled', '2023-01-18 16:10:38'),
(1, 61, 'Devis Approved', '2023-01-18 16:10:45'),
(2, 63, 'Add', '2023-01-18 16:23:07'),
(4, 64, 'Add', '2023-02-01 16:40:09'),
(4, 64, 'Devis Approved', '2023-02-01 16:40:39'),
(4, 64, 'Devis canceled', '2023-02-01 16:40:47'),
(2, 64, 'Devis Approved', '2023-02-03 15:42:55'),
(2, 64, 'Devis canceled', '2023-02-03 15:42:57'),
(1, 64, 'Devis Approved', '2023-02-13 16:36:55'),
(1, 64, 'Devis canceled', '2023-02-13 16:36:58'),
(1, 65, 'Add', '2023-02-23 17:00:33'),
(1, 61, 'Devis canceled', '2023-04-13 08:54:58'),
(1, 61, 'Devis Approved', '2023-04-13 08:55:03'),
(1, 61, 'Devis canceled', '2023-04-13 08:55:05'),
(1, 61, 'Devis Approved', '2023-04-13 08:55:08'),
(1, 61, 'Devis canceled', '2023-04-13 08:55:11'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:45:30'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:45:31'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:45:31'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:45:31'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:45:32'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:48:56'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:48:56'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:48:56'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:48:56'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:48:56'),
(1, 51, 'Paiement Effectué', '2023-04-18 07:56:49'),
(1, 51, 'Paiement Effectué', '2023-04-18 08:16:15'),
(1, 51, 'Paiement Effectué', '2023-04-18 12:19:27'),
(1, 51, 'Paiement Effectué', '2023-04-18 12:19:28'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:15:34'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:15:34'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:34:44'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:34:44'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:37:39'),
(1, 51, 'Paiement Effectué', '2023-04-18 15:37:40'),
(1, 62, 'Paiement Effectué', '2023-04-19 05:23:33'),
(1, 61, 'Paiement Effectué', '2023-04-19 11:51:58'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:02:29'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:09:36'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:38:48'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:38:48'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:38:49'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:38:49'),
(1, 61, 'Paiement Effectué', '2023-04-19 12:38:49'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:05:07'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:05:07'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:05:08'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:05:08'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:05:08'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:08:51'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:08:51'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:19:41'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:21:15'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:36:25'),
(1, 61, 'Paiement Effectué', '2023-04-19 13:36:25'),
(1, 54, 'Update', '2023-04-19 15:36:18'),
(1, 50, 'Update', '2023-04-19 15:44:31'),
(1, 50, 'Update', '2023-04-19 15:47:00'),
(1, 50, 'Update', '2023-04-19 15:48:51'),
(1, 50, 'Update', '2023-04-19 15:51:23'),
(1, 50, 'Update', '2023-04-19 15:54:59'),
(1, 50, 'Update', '2023-04-19 15:57:47'),
(1, 50, 'Update', '2023-04-19 15:58:29'),
(1, 50, 'Update', '2023-04-19 15:59:11'),
(1, 50, 'Update', '2023-04-19 16:00:01'),
(1, 50, 'Update', '2023-04-19 16:00:29'),
(1, 50, 'Update', '2023-04-19 16:00:52'),
(1, 50, 'Update', '2023-04-19 16:01:10'),
(1, 61, 'Paiement Effectué', '2023-04-19 18:04:06'),
(1, 61, 'Paiement Effectué', '2023-04-19 18:04:06'),
(1, 61, 'Paiement Effectué', '2023-04-19 19:13:32'),
(1, 61, 'Paiement Effectué', '2023-04-19 20:32:26'),
(1, 61, 'Paiement Effectué', '2023-04-19 20:37:39'),
(1, 61, 'Paiement Effectué', '2023-04-19 20:45:49'),
(1, 61, 'Paiement Effectué', '2023-04-19 20:50:51'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:01:53'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:04:10'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:06:59'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:08:57'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:10:40'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:15:33'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:33'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:34'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:34'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:34'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:38'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:15:38'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:21:30'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:31'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:32'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:33'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:33'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:34'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:35'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:36'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:37'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:38'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:38'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:38'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:39'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:39'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:39'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:39'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:40'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:21:40'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:24:10'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:24:10'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:27:55'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:30:00'),
(1, 61, 'Paiement Effectué', '2023-04-19 21:36:43'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:36:43'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:39:42'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:39:42'),
(1, 62, 'Paiement Effectué', '2023-04-19 21:39:42'),
(1, 65, 'Paiement Effectué', '2023-04-19 23:21:05'),
(1, 73, 'Add', '2023-04-28 21:26:17'),
(1, 65, 'Paiement Effectué', '2023-04-29 13:31:47'),
(1, 65, 'Paiement Effectué', '2023-04-29 13:31:47'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:13'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:13'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:13'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:14'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:15'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:15'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:15'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:15'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:15'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:16'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:17'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:18'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:19'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:20'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:21'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:22'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:23'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:23'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:23'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:23'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:23'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:24'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:24'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:24'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:24'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:24'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:25'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:26'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:27'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:27'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:27'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:27'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:27'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:28'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:29'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:30'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:31'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:31'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:31'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:31'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:31'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:32'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:33'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:33'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:33'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:33'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:33'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:34'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:34'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:34'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:34'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:34'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:35'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:36'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:36'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:36'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:36'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:37'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:38'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:39'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:40'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:40'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:40'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:40'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:40'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:41'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:41'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:41'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:41'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:41'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:42'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:42'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:42'),
(1, 73, 'Paiement Effectué', '2023-04-29 13:35:42'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:00:08'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:01:42'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:07:00'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:08:04'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:11:05'),
(1, 73, 'Paiement Effectué', '2023-04-29 14:11:05'),
(1, 74, 'Add', '2023-04-29 14:13:56'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:15:28'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:15:29'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:18:52'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:20:30'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:33:20'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:33:20'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:34:36'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:35:25'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:50:25'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:50:26'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:58:05'),
(1, 74, 'Paiement Effectué', '2023-04-29 14:58:05'),
(1, 74, 'Paiement Effectué', '2023-04-29 15:05:32'),
(1, 74, 'Paiement Effectué', '2023-04-29 15:05:33'),
(1, 50, 'Paiement Effectué', '2023-05-07 21:53:39'),
(1, 50, 'Paiement Effectué', '2023-05-07 21:56:28'),
(1, 50, 'Paiement Effectué', '2023-05-07 22:05:40'),
(1, 50, 'Paiement Effectué', '2023-05-07 22:06:14'),
(1, 50, 'Paiement Effectué', '2023-05-07 22:07:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_invoice`
--

CREATE TABLE `user_invoice` (
  `id_user` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_invoice`
--

INSERT INTO `user_invoice` (`id_user`, `id_invoice`, `action`, `date`) VALUES
(1, 3, 'Add', '2022-12-07 21:51:07'),
(1, 4, 'Add', '2022-12-07 23:30:46'),
(1, 5, 'Add', '2022-12-10 13:45:12'),
(1, 6, 'Add', '2022-12-14 19:44:18'),
(1, 7, 'Add', '2022-12-16 21:35:19'),
(1, 8, 'Add', '2022-12-18 22:35:15'),
(1, 9, 'Paiement Effectué', '2022-12-28 18:20:03'),
(1, 9, 'Add', '2022-12-28 18:20:03'),
(1, 10, 'Paiement Effectué', '2022-12-28 18:24:07'),
(1, 10, 'Add', '2022-12-28 18:24:07'),
(1, 7, 'Paiement Effectué', '2022-12-28 18:25:56'),
(1, 8, 'Paiement Effectué', '2022-12-28 18:25:56'),
(1, 9, 'Paiement Effectué', '2022-12-28 18:25:56'),
(1, 10, 'Paiement Effectué', '2022-12-28 18:25:56'),
(1, 7, 'Paiement Effectué', '2023-01-01 18:16:52'),
(1, 8, 'Paiement Effectué', '2023-01-01 18:16:52'),
(1, 12, 'Paiement Effectué', '2023-01-01 21:31:57'),
(1, 12, 'Add', '2023-01-01 21:31:57'),
(1, 13, 'Add', '2023-01-02 19:53:07'),
(1, 13, 'Paiement Effectué', '2023-01-02 20:03:19'),
(1, 14, 'Add', '2023-01-05 20:09:13'),
(1, 15, 'Add', '2023-01-05 20:12:10'),
(1, 16, 'Paiement Effectué', '2023-01-09 20:29:30'),
(1, 16, 'Add', '2023-01-09 20:29:30'),
(2, 14, 'Paiement Effectué', '2023-02-01 16:29:12'),
(4, 17, 'Add', '2023-02-01 16:42:05'),
(4, 16, 'Paiement Effectué', '2023-02-01 16:43:21'),
(2, 15, 'Paiement Effectué', '2023-02-01 16:47:41'),
(4, 5, 'Paiement Effectué', '2023-02-01 16:49:00'),
(1, 22, 'Add', '2023-04-20 03:54:25'),
(1, 23, 'Update', '2023-05-02 14:43:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_purchase`
--

CREATE TABLE `user_purchase` (
  `id_user` int(11) NOT NULL,
  `id_purchase` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_purchase`
--

INSERT INTO `user_purchase` (`id_user`, `id_purchase`, `action`, `date`) VALUES
(2, 2, 'Add', '2023-02-01 18:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(2, 5),
(1, 3),
(4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `user_service`
--

CREATE TABLE `user_service` (
  `id_user` int(11) NOT NULL,
  `service` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_service`
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
(1, 'servaxzxxxxxaa', 'Delete', '2023-04-30 22:29:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `broker`
--
ALTER TABLE `broker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `broker_devis`
--
ALTER TABLE `broker_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broker_devis_devis` (`id_devis`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_individual`
--
ALTER TABLE `client_individual`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_broker_devis` (`id_broker_devis`);

--
-- Indexes for table `detail_devis`
--
ALTER TABLE `detail_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_devis` (`id_devis`);

--
-- Indexes for table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_invoice` (`id_invoice`);

--
-- Indexes for table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- Indexes for table `devis_payments`
--
ALTER TABLE `devis_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_detail_devis_payment` (`id_devis`);

--
-- Indexes for table `devis_to_service`
--
ALTER TABLE `devis_to_service`
  ADD PRIMARY KEY (`id_devis`,`id_service`),
  ADD KEY `id_devis` (`id_devis`,`id_service`),
  ADD KEY `FK_service` (`id_service`);

--
-- Indexes for table `dossier`
--
ALTER TABLE `dossier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_dossier_service` (`id_service`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_invoiceClient` (`id_client`);

--
-- Indexes for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_invoice_payment` (`id_invoice`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_receipt_payment` (`id_payment`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_perm`
--
ALTER TABLE `role_perm`
  ADD KEY `FK_role_perm_role` (`role_id`),
  ADD KEY `FK_role_perm_perm` (`perm_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `situation`
--
ALTER TABLE `situation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_supplier_suppCat` (`cat_id`);

--
-- Indexes for table `supp_category`
--
ALTER TABLE `supp_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_broker`
--
ALTER TABLE `user_broker`
  ADD KEY `FK_user_broker_user` (`id_user`);

--
-- Indexes for table `user_client`
--
ALTER TABLE `user_client`
  ADD KEY `FK_user_client_user` (`id_user`);

--
-- Indexes for table `user_devis`
--
ALTER TABLE `user_devis`
  ADD KEY `FK_user_devis_user` (`id_user`),
  ADD KEY `FK_user_devis_devis` (`id_devis`);

--
-- Indexes for table `user_invoice`
--
ALTER TABLE `user_invoice`
  ADD KEY `FK_user_invoice_user` (`id_user`),
  ADD KEY `FK_user_invoice_invoice` (`id_invoice`);

--
-- Indexes for table `user_purchase`
--
ALTER TABLE `user_purchase`
  ADD KEY `FK_user_purchase_user` (`id_user`),
  ADD KEY `FK_user_purchase_purchase` (`id_purchase`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD KEY `FK_user_role_user` (`user_id`),
  ADD KEY `FK_user_role_role` (`role_id`);

--
-- Indexes for table `user_service`
--
ALTER TABLE `user_service`
  ADD KEY `FK_user_service_user` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `broker`
--
ALTER TABLE `broker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `broker_devis`
--
ALTER TABLE `broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `client_entreprise`
--
ALTER TABLE `client_entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `client_individual`
--
ALTER TABLE `client_individual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_devis`
--
ALTER TABLE `detail_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `devis_payments`
--
ALTER TABLE `devis_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=740;

--
-- AUTO_INCREMENT for table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=757;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `situation`
--
ALTER TABLE `situation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supp_category`
--
ALTER TABLE `supp_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `broker_devis`
--
ALTER TABLE `broker_devis`
  ADD CONSTRAINT `broker_devis_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_broker_devis`
--
ALTER TABLE `detail_broker_devis`
  ADD CONSTRAINT `FK_detail_broker_devis` FOREIGN KEY (`id_broker_devis`) REFERENCES `broker_devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_devis`
--
ALTER TABLE `detail_devis`
  ADD CONSTRAINT `FK_detail_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_invoice`
--
ALTER TABLE `detail_invoice`
  ADD CONSTRAINT `FK_detail_invoice` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `FK_devisClient` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `devis_payments`
--
ALTER TABLE `devis_payments`
  ADD CONSTRAINT `FK_detail_devis_payment` FOREIGN KEY (`id_devis`) REFERENCES `detail_devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `devis_to_service`
--
ALTER TABLE `devis_to_service`
  ADD CONSTRAINT `FK_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`),
  ADD CONSTRAINT `FK_service` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_invoiceClient` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD CONSTRAINT `FK_invoice_payment` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `FK_receipt_payment` FOREIGN KEY (`id_payment`) REFERENCES `devis_payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_perm`
--
ALTER TABLE `role_perm`
  ADD CONSTRAINT `FK_role_perm_perm` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_role_perm_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `FK_supplier_suppCat` FOREIGN KEY (`cat_id`) REFERENCES `supp_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_broker`
--
ALTER TABLE `user_broker`
  ADD CONSTRAINT `FK_user_broker_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_client`
--
ALTER TABLE `user_client`
  ADD CONSTRAINT `FK_user_client_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_devis`
--
ALTER TABLE `user_devis`
  ADD CONSTRAINT `FK_user_devis_devis` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_devis_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_invoice`
--
ALTER TABLE `user_invoice`
  ADD CONSTRAINT `FK_user_invoice_invoice` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_invoice_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_purchase`
--
ALTER TABLE `user_purchase`
  ADD CONSTRAINT `FK_user_purchase_purchase` FOREIGN KEY (`id_purchase`) REFERENCES `purchase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_purchase_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_user_role_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_service`
--
ALTER TABLE `user_service`
  ADD CONSTRAINT `FK_user_service_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
