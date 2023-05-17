<?php
include 'header.php';

if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}


?>

<div class="pagetitle">
    <h1>Intermédiaires List</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Intermédiaire information</h3>
                    <div class="tab-content" id="brokers-content">
                        <!-- table broker -->
                        <div class="overflow-auto">
                            <div>
                                <table id="brokersTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">Prenom</a>
                                            </th>
                                            <th>
                                                <a href="#">Telephone</a>
                                            </th>
                                            <th>
                                                <a href="#">Address</a>
                                            </th>
                                            <th>
                                                <a href="#">Sold</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $res = getBrokerData();
                                            //TODO add permissions to broker
                                            $check_edit =  ($role->hasPerm('edit broker')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete broker')) ? "":"hide-element";
                                            $number = 1;
                                            $html = '';
                                            while($row = mysqli_fetch_assoc($res)){
                                                $html .= '<tr>';
                                                $html .= '<td>'.$number.'</td>';
                                                $html .= '<td>'.$row['nom'].'</td>';
                                                $html .= '<td>'.$row['prenom'].'</td>';
                                                $html .= '<td>'.$row['phone'].'</td>';
                                                $html .= '<td>'.$row['address'].'</td>';
                                                $html .= '<td>'.$row['sold'].'</td>';
                                                $html .= '  <td>
                                                                <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editBrokerBtn '.$check_edit.' " title="Modifier Intermédiaire" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                                <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteBrokerBtn '.$check_delete.' " title="Supprimer Intermédiaire" ><span><i class="bi bi-trash"></i></span></a>
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
                        <!-- table broker END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Broker-->
<div class="modal fade" id="editBrokerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">mettre à jour l'intermédiaire</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editBrokerForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="tr_id" id="tr_id" value="">
                    <div class="mb-3">
                        <label for="brkNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="brkNom" id="brkNom" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="brkPrenom" class="form-label">Prenom</label>
                        <input type="text" class="form-control"  name="brkPrenom" id="brkPrenom" placeholder="Prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="brkPhone" class="form-label">Telephone</label>
                        <input type="number" class="form-control" min="0" name="brkPhone" id="brkPhone" placeholder="Telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="brkAdr" class="form-label">Address</label>
                        <input type="text" class="form-control"  name="brkAdr" id="brkAdr" placeholder="Address" >
                    </div>
                    <input type="hidden" name="brkSold" id="brkSold">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="submit" class="btn btn-primary">mettre à jour </button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- edit Broker modal end -->
<!-- Delete Broker modal -->
<div class="modal fade" id="deleteBrokerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer cet intermédiaire ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteBrokerModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Broker modal end -->
<?php include 'footer.php'; ?>