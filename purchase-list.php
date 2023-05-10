<?php
include 'header.php';


    if(isset($_SESSION["success"])){
        echo $_SESSION["success"];
        unset($_SESSION["success"]);
    }
    
?>

<div class="row my-3">
    <div class="pagetitle">
        <h1>Liste des Achats</h1>
    </div>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <h3 class="card-title">Achat information</h3> -->
                    <div class="my-4"></div>
                    <div class="tab-content" id="roles-content">
                        <!-- table Purchase -->
                        <div class="">
                            <div>
                                <table id="purchaseTable" class="table table-hover table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">Note</a>
                                            </th>
                                            <th>
                                                <a href="#">Prix</a>
                                            </th>
                                            <th>
                                                <a href="#">Date</a>
                                            </th>
                                            <th>
                                                <a href="#"></a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        
                                            $query = "SELECT * FROM `purchase` WHERE `remove`='0';";
                                            $res = mysqli_query($cnx,$query);
                                            $html = '';
                                            $check_edit =  ($role->hasPerm('edit purchase')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete purchase')) ? "":"hide-element";
                                            while($row=mysqli_fetch_assoc($res)){
                                                $html .= '<tr>';
                                                $html .= '<td>'.$row['name'].'</td>';
                                                $html .= '<td>'.$row['note'].'</td>';
                                                $html .= '<td>'.number_format($row['price'],2).' DH</td>';
                                                $html .= '<td>'.$row['date'].'</td>';
                                                $html .= '<td class="text-center"> <a target="_blank" href="purchase_export.php?p='.$row["id"].'" title="Imprimer Reçu"><i class="bi bi-paperclip"></i></a> </td>';
                                                $html .= '<td>
                                                            <a href="purchase-edit.php?p_id='.$row["id"].'" data-id="" class="btn btn-primary btn-sm editPurchaseBtn '.$check_edit.' " title="Modifier Achat"><span><i class="bi bi-pencil-square"></i></span></a>
                                                            <a href="javascript:void(0)" data-id="'.$row["id"].'" class="btn btn-danger btn-sm deletePurchaseBtn '.$check_delete.' " title="Supprimer Achat"><span><i class="bi bi-trash"></i></span></a>
                                                        </td>';
                                                $html .= '</tr>';
                                            }
                                            
                                            echo $html;
                                        
                                        
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Purchase END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- Delete purchase modal -->
<div class="modal fade" id="deletePurchaseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer cet achat ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deletePurchaseModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete purchase modal end -->





<?php include 'footer.php'; ?>