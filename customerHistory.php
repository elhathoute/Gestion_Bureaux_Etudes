<?php
include 'header.php';

?>


<div class="row my-3">
    <div class="pagetitle">
        <h1>Registre des Maitres d'ouvrage</h1>
    </div>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <h3 class="card-title">Devis information</h3> -->
                    <div class="my-4"></div>
                    <div class="tab-content" id="cusHistory-content">
                        <!-- table History -->
                        <div class="">
                            <div>
                                <table id="cusHistoryTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">Nom de l'utilisateur</a>
                                            </th>
                                            <th>
                                                <a href="#">Client</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                            <th>
                                                <a href="#">Date</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo userClient_historyData();?>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table History END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<?php include 'footer.php'; ?>