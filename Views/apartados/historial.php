<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Historial Apartados
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="t_historial_apart" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clientes</th>
                        <th>Fecha Apartado</th>
                        <th>Fecha Retiro</th>
                        <th>Total</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/historial_apartado.js"></script>

</body>

</html>