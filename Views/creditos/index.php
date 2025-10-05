<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Administrar Creditos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="tblCreditos" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clientes</th>
                        <th>Fecha</th>
                        <th>Monto Total</th>
                        <th>Abonado</th>
                        <th>Restante</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/creditos.js"></script>

</body>

</html>