<?php include "Views/templates/header.php"; ?>
<?php if ($data['existe']) { ?>
    <button class="btn btn-outline-primary mb-2" type="button" onclick="frmCaja();"><i class="fas fa-plus"></i></button>
<?php } ?>
<a class="btn btn-outline-danger mb-2" href="<?php echo BASE_URL; ?>cajas/inactivos"><i class="fas fa-trash"></i></a>
<div class="card">
    <div class="card-header">
        Cajas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="tblCajas" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/caja.js"></script>

</body>

</html>