<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Ventas
    </div>
    <div class="card-body">
        <?php if ($data['existe']) { ?>
            <button class="btn btn-outline-danger mb-2" type="button" onclick="generarPdfVenta()"><i class="fas fa-file-pdf"></i></button>
        <?php } ?>
        <a class="btn btn-outline-warning mb-2" href="<?php echo BASE_URL; ?>ventas/inactivos"><i class="fas fa-ban"></i></a>
        <div class="row mb-2">
            <div class="col-md-4">
                <label for="">Desde</label>
                <input class="form-control" id="min" type="date" name="ventas_min" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Inicio">
            </div>
            <div class="col-md-4">
                <label for="">Hasta</label>
                <input class="form-control" id="max" type="date" name="ventas_max" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Fin">
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <label>Acción</label>
                    <button class="btn btn-outline-primary" type="button" name="venta" onclick="mostrarTodo(event)">Mostrar Todo</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="t_historial_v" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clientes</th>
                        <th>Fecha Venta</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>Metodo</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/historial_ventas.js"></script>

</body>

</html>