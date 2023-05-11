<?php
include 'includes/config.php';
if (!isset($_SESSION["user"])) {
    header("location:index.php");
}
$page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);

include 'functions.php';
include 'includes/autoloader.php';

include 'check-role.php';

$pagesToCheck = ["devis-show.php","devis-edit.php","devis_export.php","invoice-view.php","invoice-edit.php","invoice_export.php","purchase-edit.php","role-edit.php","user-edit.php"];
if(in_array($page,$pagesToCheck)){
    if($page == "devis-show.php" || $page == "devis-edit.php" || $page == "devis_export.php"){
        checkUrlVars("devis",$_GET['id']);
        checkUrlVars("client",$_GET['client_id']);
    }
    elseif($page == "invoice-view.php" || $page == "invoice-edit.php" || $page == "invoice_export.php"){
        checkUrlVars("invoice",$_GET['id']);
        checkUrlVars("client",$_GET['client_id']);
        
    }elseif($page == "role-edit.php"){
        checkUrlVars("roles",$_GET['r_id']);
    }
    elseif($page == "purchase-edit.php"){
        checkUrlVars("purchase",$_GET['p_id']);
    }elseif($page == "user-edit.php"){
        checkUrlVars("users",$_GET['u_id']);
    }
}




$perm_page = [
    "create client" => "customer-add.php", "show client" => "customer-view.php", "create service" => "services.php",
    "show service" => "services-view.php", "create devis" => "devis.php", "show devis" => "devis-view.php", "edit devis" => "devis-edit.php",
    "show role" => "role-list.php", "create role" => "role-create.php", "edit role" => "role-edit.php", "show user" => "user-list.php",
    "create user" => "user-create.php", "edit user" => "user-edit.php", "show notifications" => "notifications.php",
    "show invoice" => "invoice-list.php", "create invoice" => "invoice-create.php", "edit invoice" => "invoice-edit.php",
    "show payment" => "payments.php", "create payment" => "payment-create.php","show history"=>["customerHistory.php","serviceHistory.php","devisHistory.php","invoiceHistory.php","purchaseHistory.php"],
    "show situation"=> "situation-list.php","create purchase" => "purchase-create.php", "show purchase" => "purchase-list.php", "edit purchase" => "purchase-edit.php","create broker" => "broker.php","show broker" => "broker-view.php"
];
$user_perms = array_keys($role->get_perms());
$exist = false;
foreach ($perm_page as $perm => $_page) {
    if ((($page == $_page || (is_array($_page) && in_array($page,$_page))) && !in_array($perm, $user_perms)) ) {
        $exist = true;
        break;
    }
}
if ($exist) {
    header("location:page-error-404.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Architect Management System</title>
    <link rel="icon" href="images/BeplanLogo_2.png">
    <!-- CSS -->
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <!-- bootstrap icons cdn -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <!-- DataTable CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <!-- dateRangePicker css CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script defer type="text/javascript">
        function total_R() {
            rowTotal();
            brkRowTotal();
            $('#paymentByClientModal').modal('show');
        }
    </script>
    <!-- chartJS CDN-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>

<body onload="total_R()">
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="logo d-flex align-items-center">
                <img src="images/BeplanLogo.png" alt="" class="img-fluid">
                <!-- <span class="d-none d-lg-block">Company Name</span> -->
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->



        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <!-- <li class="nav-item dropdown"> -->

                <a class="nav-link nav-icon <?= ($role->hasPerm('show notifications')) ? "" : "hide-element" ?>" href="notifications.php" title="Notification">
                    <i class="bi bi-bell-fill"></i>
                    <!-- <i class="bi bi-megaphone-fill"></i> -->
                    <span class="badge bg-danger badge-number <?= (getNotifCount() == "0") ? "hide-element" : ''; ?> "><?= getNotifCount() ?></span>
                </a>
                <!-- End Notification Icon -->

                <!-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have 4 new notifications
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="notification-item">
                            <div>
                                <h4>Lorem Ipsum</h4>
                                <p>Quae dolorem earum veritatis oditseno</p>
                                <p>30 min. ago</p>
                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-footer">
                            <a href="#">Show all notifications</a>
                        </li>

                    </ul>
                    

                </li> -->
                <!-- End Notification Nav -->


                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="images/userImage.png" alt="Profile" class="img-fluid rounded-circle">
                        <?php
                            $user_id = $_SESSION["user_id"];
                            $userInfo = getUser($user_id);
                            $rl_id = getUserRoleId($user_id);
                            $query = "SELECT * FROM `roles` WHERE `id` ='$rl_id'";
                            $res = mysqli_query($cnx,$query);
                            $row = mysqli_fetch_assoc($res);
                        ?>
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?= ucfirst(str_split($userInfo["prenom"])[0]) . '.' . ucfirst($userInfo['nom']);?></span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?= ucfirst($userInfo["prenom"]). ' ' . ucfirst($userInfo["nom"]); ?></h6>
                            <span><?=ucfirst($row["role_name"]);?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li> -->
                        <!-- <li>
                            <hr class="dropdown-divider">
                        </li> -->

                        <!-- <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-gear"></i>
                                <span>Account Settings</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li> -->

                        <!-- <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-question-circle"></i>
                                <span>Need Help?</span>
                            </a>
                        </li> -->
                        <!-- <li>
                            <hr class="dropdown-divider">
                        </li> -->

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Déconnexion</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link <?= $page == 'dashboard.php' ? 'active' : '' ?> " href="dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span>Tableau de bord</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item <?= ($role->hasPerm('show client') || $role->hasPerm('create client')) ? "" : "hide-element" ?> ">
                <a class="nav-link collapsed <?= $page == 'customer-add.php' || $page == 'customer-view.php' ? 'active' : '' ?> " data-bs-target="#customers-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-people"></i><span>Clients</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="customers-nav" class="nav-content collapse <?= $page == 'customer-add.php' || $page == 'customer-view.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="customer-add.php" class="<?= $page == 'customer-add.php' ? 'active' : '' ?> <?= ($role->hasPerm('create client')) ? "" : "hide-element" ?>">
                            <i class="bi bi-person-plus"></i><span>Ajouter un client</span>
                        </a>
                    </li>
                    <li>
                        <a href="customer-view.php" class="<?= $page == 'customer-view.php' ? 'active' : '' ?> <?= ($role->hasPerm('show client')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les clients</span>
                        </a>
                    </li>



                </ul>
            </li><!-- End Customers Nav -->
            <!-- services nav -->
            <li class="nav-item <?= ($role->hasPerm('show service') || $role->hasPerm('create service')) ? "" : "hide-element" ?>">
                <a class="nav-link collapsed <?= $page == 'services.php' || $page == 'services-view.php' ? 'active' : '' ?>" data-bs-target="#services-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-archive"></i><span>Services</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="services-nav" class="nav-content collapse <?= $page == 'services.php' || $page == 'services-view.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="services.php" class="<?= $page == 'services.php' ? 'active' : '' ?> <?= ($role->hasPerm('create service')) ? "" : "hide-element" ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un service</span>
                        </a>
                    </li>
                    <li>
                        <a href="services-view.php" class="<?= $page == 'services-view.php' ? 'active' : '' ?> <?= ($role->hasPerm('show service')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les services</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- services Nav END -->
            <!-- broker nav -->
            <li class="nav-item <?= ($role->hasPerm('show broker') || $role->hasPerm('create broker')) ? "" : "hide-element" ?>">
                <a class="nav-link collapsed <?= $page == 'broker.php' || $page == 'broker-view.php' ? 'active' : '' ?>" data-bs-target="#broker-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-person-lines-fill"></i><span>intermédiaire</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="broker-nav" class="nav-content collapse <?= $page == 'broker.php' || $page == 'broker-view.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="broker.php" class="<?= $page == 'broker.php' ? 'active' : '' ?> <?= ($role->hasPerm('create broker')) ? "" : "hide-element" ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un intermédiaire</span>
                        </a>
                    </li>
                    <li>
                        <a href="broker-view.php" class="<?= $page == 'broker-view.php' ? 'active' : '' ?> <?= ($role->hasPerm('show service')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les intermédiaires</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Broker Nav END -->

            <!-- Supplier And Category -->


            <li class="nav-item ">
                <a class="nav-link collapsed <?= $page == 'supplier.php' || $page == 'supplier-list.php' || $page == 'category.php' || $page == 'category-list.php' ? 'active' : '' ?>" data-bs-target="#supplier-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-boxes"></i><span>Fournisseur</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="supplier-nav" class="nav-content collapse <?= $page == 'supplier.php' || $page == 'supplier-list.php' || $page == 'category.php' || $page == 'category-list.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="supplier.php" class="<?= $page == 'supplier.php' ? 'active' : '' ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un Fournisseur</span>
                        </a>
                    </li>
                    <li>
                        <a href="supplier-list.php" class="<?= $page == 'supplier-list.php' ? 'active' : '' ?>">
                            <i class="bi bi-gear"></i><span>Gérer les Fournisseurs</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link collapsed <?= $page == 'category.php' || $page == 'category-list.php' ? 'active' : '' ?>" data-bs-target="#cat-nav" data-bs-toggle="collapse" href="#">
                            <i class="bi bi-bookmark"></i><span>Catégorie</span><i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <ul id="cat-nav" class="nav-content collapse <?= $page == 'category.php' || $page == 'category-list.php' ? 'show' : '' ?>" data-bs-parent="#supplier-nav">
                            <li>
                                <a href="category.php" class="ms-3 <?= $page == 'category.php' ? 'active' : '' ?>">
                                    <i class="bi bi-bookmark-plus"></i><span>Ajouter une Catégorie</span>
                                </a>
                            </li>
                            <li>
                                <a href="category-list.php" class="ms-3 <?= $page == 'category-list.php' ? 'active' : '' ?>">
                                    <i class="bi bi-gear"></i><span>Gérer les Catégorie</span>
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                </ul>
            </li>



            <!-- Supplier And END -->



            <!-- devis nav -->
            <li class="nav-item <?= ($role->hasPerm('show devis') || $role->hasPerm('create devis')) ? "" : "hide-element" ?>">
                <a class="nav-link collapsed <?= $page == 'devis.php' || $page == 'devis-view.php' || $page == 'devis-edit.php' || $page == 'devis-show.php' || $page =='payments.php' || $page == 'payment-create.php' ? 'active' : '' ?>" data-bs-target="#devis-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-file-text"></i><span>Devis</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="devis-nav" class="nav-content collapse <?= $page == 'devis.php' || $page == 'devis-view.php' || $page == 'devis-edit.php' || $page == 'devis-show.php' || $page =='payments.php' || $page == 'payment-create.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="devis.php" class="<?= $page == 'devis.php' ? 'active' : '' ?> <?= ($role->hasPerm('create devis')) ? "" : "hide-element" ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un Devis</span>
                        </a>
                    </li>
                    <li>
                        <a href="devis-view.php" class="<?= $page == 'devis-view.php' ? 'active' : '' ?> <?= ($role->hasPerm('show devis')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les devis</span>
                        </a>
                    </li>
                    <li>
                        <a href="payments.php" class="<?= $page == 'payments.php' ? 'active' : '' ?> <?= ($role->hasPerm('show payment')) ? "" : "hide-element" ?>">
                            <i class="bi bi-bank"></i><span>Paiements</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- devis nav END -->

            <!-- Dossier nav -->
            <li class="nav-item">
                <a class="nav-link collapsed <?= $page == 'dossier.php' || $page == 'dossier-view.php' || $page == 'dossier-show.php'  ? 'active' : '' ?>" " data-bs-target="#dossier-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-card-text"></i><span>Dossier</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="dossier-nav" class="nav-content collapse <?= $page == 'dossier.php' || $page == 'dossier-view.php' || $page == 'dossier-show.php' ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="dossier.php" class="<?= $page == 'dossier.php' ? 'active' : '' ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un Dossier</span>
                        </a>
                    </li>
                    <li>
                        <a href="dossier-view.php" class="<?= $page == 'dossier-view.php' ? 'active' : '' ?>">
                            <i class="bi bi-gear"></i><span>Gérer les Dossier</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Dossier nav END -->

            <!-- facture nav -->
            <li class="nav-item <?= ($role->hasPerm('show invoice') || $role->hasPerm('create invoice')) ? "" : "hide-element" ?>">
                <a class="nav-link collapsed <?= $page == 'invoice-create.php' || $page == 'invoice-list.php' || $page == 'invoice-edit.php' || $page == 'invoice-view.php'  ? 'active' : '' ?>" data-bs-target="#facture-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-receipt"></i><span>Facture</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="facture-nav" class="nav-content collapse <?= $page == 'invoice-create.php' || $page == 'invoice-list.php' || $page == 'invoice-edit.php' || $page == 'invoice-view.php'  ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="invoice-create.php" class="<?= $page == 'invoice-create.php' ? 'active' : '' ?> <?= ($role->hasPerm('create invoice')) ? "" : "hide-element" ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter une Facture</span>
                        </a>
                    </li>
                    <li>
                        <a href="invoice-list.php" class="<?= $page == 'invoice-list.php' ? 'active' : '' ?> <?= ($role->hasPerm('show invoice')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les Facture</span>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="payments.php" class="<?= $page == 'payments.php' ? 'active' : '' ?> <?= ($role->hasPerm('show payment')) ? "" : "hide-element" ?>">
                            <i class="bi bi-bank"></i><span>Paiements</span>
                        </a>
                    </li> -->
                </ul>
            </li>
            <!-- facture nav END -->

            <!-- Stitutation -->
            <li class="nav-item">
                <a class="nav-link collapsed <?= $page == 'situation-list.php' ? 'active' : '' ?> <?= ($role->hasPerm('show situation')) ? "" : "hide-element" ?>" href="situation-list.php">
                    <i class="bi bi-list-columns-reverse"></i>
                    <span>Situation</span>
                </a>
            </li>
            <!-- Situation End -->

            <!-- Achat -->
            <li class="nav-item <?= ($role->hasPerm('show purchase') || $role->hasPerm('create purchase')) ? "" : "hide-element" ?>">
                <a class="nav-link collapsed <?= $page == 'purchase-create.php' || $page == 'purchase-list.php' || $page == 'purchase-edit.php'   ? 'active' : '' ?>" data-bs-target="#purchase-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-bag"></i><span>Achat</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="purchase-nav" class="nav-content collapse <?= $page == 'purchase-create.php' || $page == 'purchase-list.php' || $page == 'purchase-edit.php'  ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="purchase-create.php" class="<?= $page == 'purchase-create.php' ? 'active' : '' ?> <?= ($role->hasPerm('create purchase')) ? "" : "hide-element" ?>">
                            <i class="bi bi-plus-lg"></i><span>Ajouter un achat</span>
                        </a>
                    </li>
                    <li>
                        <a href="purchase-list.php" class="<?= $page == 'purchase-list.php' ? 'active' : '' ?> <?= ($role->hasPerm('show purchase')) ? "" : "hide-element" ?>">
                            <i class="bi bi-gear"></i><span>Gérer les Achats</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Achat End -->

            <!-- Role -->
            <li class="nav-item">
                <a class="nav-link collapsed <?= $page == 'role-list.php' || $page == 'role-create.php' ? 'active' : '' ?> <?= ($role->hasPerm('show all')) ? "" : "hide-element" ?>" href="role-list.php">
                    <i class="bi bi-diagram-3"></i>
                    <span>Role</span>
                </a>
            </li>
            <!-- role End -->

            <!-- User -->
            <li class="nav-item">
                <a class="nav-link collapsed <?= $page == 'user-list.php' || $page == 'user-create.php' ? 'active' : '' ?>  <?= ($role->hasPerm('show user') || $role->hasPerm('create user')) ? "" : "hide-element" ?>" href="user-list.php">
                    <i class="bi bi-person"></i>
                    <span>User</span>
                </a>
            </li>
            <!-- User End -->

            <!-- history -->
            <li class="nav-item">
                <a class="nav-link collapsed <?= $page == 'customerHistory.php' || $page == 'serviceHistory.php' || $page == 'devisHistory.php' || $page == 'invoiceHistory.php' || $page == 'purchaseHistory.php' || $page == 'brokerHistory.php'   ? 'active' : '' ?><?= $role->hasPerm('show history') ? "" : "hide-element" ?>" data-bs-target="#history-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-clock-history"></i><span>Historique</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="history-nav" class="nav-content collapse <?= $page == 'customerHistory.php' || $page == 'serviceHistory.php' || $page == 'devisHistory.php' || $page == 'invoiceHistory.php' || $page == 'purchaseHistory.php' || $page == 'brokerHistory.php'  ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="customerHistory.php" class="<?= $page == 'customerHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Clients</span>
                        </a>
                    </li>
                    <li>
                        <a href="serviceHistory.php" class="<?= $page == 'serviceHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Services</span>
                        </a>
                    </li>
                    <li>
                        <a href="brokerHistory.php" class="<?= $page == 'brokerHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Intermédiaires</span>
                        </a>
                    </li>
                    <li>
                        <a href="devisHistory.php" class="<?= $page == 'devisHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Devis</span>
                        </a>
                    </li>
                    <li>
                        <a href="invoiceHistory.php" class="<?= $page == 'invoiceHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Facture</span>
                        </a>
                    </li>
                    <li>
                        <a href="purchaseHistory.php" class="<?= $page == 'purchaseHistory.php' ? 'active' : '' ?>">
                            <i class="bi bi-dot"></i><span>Achat</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Achat End -->

            <!-- <li class="nav-item">
                <a class="nav-link collapsed  <?= $page == 'profile.php' ? 'active' : '' ?>" href="profile.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li> -->
             <!-- End Profile Page Nav  -->

        </ul>
    </aside><!-- End sidebar -->


    <!-- main Content Start -->
    <main id="main" class="main">