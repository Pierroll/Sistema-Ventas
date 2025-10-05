<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Compras
    </div>
    <div class="card-body">
        <?php if ($data['existe']) { ?>
            <button class="btn btn-outline-danger mb-2" type="button" onclick="generarPdfCompra()"><i class="fas fa-file-pdf"></i></button>
        <?php }?>
        <a class="btn btn-outline-warning mb-2" href="<?php echo BASE_URL; ?>compras/inactivos"><i class="fas fa-ban"></i></a>
        <div class="row mb-2">
            <div class="col-md-4">
                <label for="">Desde</label>
                <input class="form-control" id="min" type="date" name="compras_min" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Inicio">
            </div>
            <div class="col-md-4">
                <label for="">Hasta</label>
                <input class="form-control" id="max" type="date" name="compras_max" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Fin">
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <label>Acción</label>
                    <button class="btn btn-outline-primary" type="button" name="compra" onclick="mostrarTodo(event)">Mostrar Todo</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="t_historial_c" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/historial_compras.js"></script>

</body>

</html>