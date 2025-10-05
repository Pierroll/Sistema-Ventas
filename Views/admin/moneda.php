<?php include "Views/templates/header.php"; ?>
<h3><i class="fas fa-dollar-sign"></i> Monedas</h3>
<div class="card">
    <div class="card-body">
        <?php if ($data['existe']) { ?>
            <button class="btn btn-outline-primary mb-2" type="button" onclick="frmMoneda();"><i class="fas fa-plus"></i></button>
        <?php } ?>
        <a class="btn btn-outline-danger mb-2" href="<?php echo BASE_URL; ?>administracion/inactivos"><i class="fas fa-trash"></i></a>
        <div class="table-responsive">
            <table class="table table-bordered table-striped display nowrap" id="t_moneda" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Símbolo moneda</th>
                        <th>Nombre moneda</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/moneda.js"></script>

</body>

</html>