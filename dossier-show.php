<?php
include 'header.php';


if(isset($_GET["s_id"])){
    $row = getSelectedDossier($_GET["s_id"]);
}

?>

<div class="pagetitle">
    <h1>Détail du dossier</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row my-2">
                        <div class="col-md-8">
                            <h3 class="card-title">information de dossier </h3>
                        </div>

                    </div>

                    <div class="ds_content_show">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">N° Dossier</label>
                            <div class="input-group ps-3">
                                <span class="input-group-text" id="ds_ref"><?=ucfirst($row["ref"])?></span>
                                <input type="text" class="form-control refTxt" placeholder="N° Dossier" aria-describedby="ds_ref" value="<?=$row["N_dossier"]?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Objet</label>
                            <div class="ps-3">
                                <textarea id="ds_objet" class="form-control" rows="1" placeholder="Objet" disabled><?=ucfirst($row["objet"])?></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sis à</label>
                            <div class="ps-3">
                                <textarea id="ds_sis" class="form-control" rows="1" placeholder="Sis à" disabled><?=ucfirst($row["located"])?></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Service</label>
                            <div class="ps-3">
                                <input id="ds_srv" type="text" class="form-control" value="<?=ucfirst($row["service_name"])?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prix</label>
                            <div class="ps-3">
                                <input id="ds_price" type="text" class="form-control" value="<?=$row["prix"]?> DH" disabled>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-md-12 my-1">
        <a href='dossier-view.php' class="btn btn-secondary float-start"><i class="bi bi-caret-left"></i> Retour</a>
    </div>
</div>

<?php include 'footer.php'; ?>