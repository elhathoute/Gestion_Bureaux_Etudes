<?php
include 'header.php';

if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}

?>

<div class="pagetitle">
    <h1>Liste des services</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Services information</h3>
                    <div class="tab-content" id="services-content">
                        <!-- table Services -->
                        <div class="overflow-auto">
                            <div>
                                <table id="servicesTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Titre</a>
                                            </th>
                                            <th>
                                                <a href="#">Référence</a>
                                            </th>
                                            <!-- <th>
                                                <a href="#">Prix</a>
                                            </th> -->
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $res = getServiceData();
                                        $check_edit =  ($role->hasPerm('edit service')) ? "":"hide-element";
                                        $check_delete = ($role->hasPerm('delete service')) ? "":"hide-element";
                                        $number = 1;
                                        $html = '';
                                        while($row = mysqli_fetch_assoc($res)){
                                            $html .= '<tr>';
                                            $html .= '<td>'.$number.'</td>';
                                            $html .= '<td>'.$row['title'].'</td>';
                                            $html .= '<td>'.$row['ref'].'</td>';
                                            // $html .= '<td>'.$row['prix'].'</td>';
                                            $html .= '  <td>
                                                            <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editServiceBtn '.$check_edit.' " title="Modifier Service" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                            <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteServiceBtn '.$check_delete.' " title="Supprimer Service" ><span><i class="bi bi-trash"></i></span></a>
                                                        </td>';
                                            $html .= '</tr>';
                                            $number++;
                                        }
                                        echo $html;
                                            
                                            
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Services END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Service-->
<div class="modal fade" id="editServiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Modifier Service</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editServiceForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="tr_id" id="tr_id" value="">
                    <div class="mb-3">
                        <label for="tile" class="form-label">Titre</label>
                        <input type="text" class="form-control serTitleTxt" name="title" id="title" placeholder="Title" required>
                    </div>
                    <div class="mb-3">
                        <label for="prix" class="form-label">Référence</label>
                        <input type="text" class="form-control servRef servRef_update"  name="ref" id="servRef" placeholder="Référence" required>
                    </div>
                    <p class="feedback text-danger ps-1">
                                    
                    </p>
                    
                    <!-- <div class="mb-3">
                        <label for="prix" class="form-label">Prix</label>
                        <input type="number" class="form-control serTitlePrix"  name="prix" min="0" step="0.01" id="prix" placeholder="Prix" required>
                    </div> -->
                    <input type="hidden" name="" id="hiddenPrix">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="submit" id="sev_update" class="btn btn-primary">Modifier</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- edit Service modal end -->
<!-- Delete service modal -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce service ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteServiceModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete service modal end -->
<?php include 'footer.php'; ?>