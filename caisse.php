<?php 
include 'header.php';
?>
<div class="pagetitle">
    <h1>Caisse</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row my-2">
                        <div class="col-md-6">
                            <h3 class="card-title">Caisse information</h3>
                        </div>
                        <div class="col-md-6 d-flex align-items-center justify-content-end" id="SearchField">
                            
                        </div>
                    </div>
                    <div class="tab-content" id="">
                        <!-- table Situation -->
                        <div class="overflow-auto">
                            <div>
                                <table id="caiseTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Caise</a>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Situation END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include 'footer.php'; ?>

