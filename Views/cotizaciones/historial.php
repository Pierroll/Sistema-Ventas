<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Cotizaciones
    </div>
    <div class="card-body">               
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="t_historial" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clientes</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>Validez</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/historial_cotizaciones.js"></script>

</body>

</html>