<?php include 'header.php'; ?>

<div class="pagetitle">
    <h1>Notifications</h1>
</div>

<section class="section">
    <form action="notification-action.php" id="notificationForm" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Demandes</h3>
                        <div class="tab-content" id="notification-content">
                            <!-- table User -->
                            <div class="overflow-auto">
                                <div>
                                    <table id="notificationTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="250">
                                                    <a href="#">Nom d'utilisateur</a>
                                                </th>
                                                <th>
                                                    <a href="#">N°</a>
                                                </th>
                                                <th>
                                                    <a href="#">Type</a>
                                                </th>
                                                <th>
                                                    <a href="#">Date</a>
                                                </th>
                                                
                                                <th>
                                                    <a href="#">Action</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            echo devisNotificationData();
                                            echo invoiceNotificationData();
                                            echo paymentNotificationData();
                                        ?>
                                        
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>








<?php include 'footer.php'; ?>