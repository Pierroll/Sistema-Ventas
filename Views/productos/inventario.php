<?php include "Views/templates/header.php"; ?>
<?php if ($data['inventario']) { ?>
    <button class="btn btn-outline-primary mb-2" type="button" onclick="frmInventario()"><i class="fas fa-cog"></i></button>
<?php } ?>
<?php if ($data['reporte']) { ?>
    <button class="btn btn-outline-danger mb-2" type="button" onclick="generarPdfInventario()"><i class="fas fa-file-pdf"></i></button>
<?php } ?>
<div class="card">
    <div class="card-header">
        Inventario
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4">
                <label for=""><i class="fas fa-calendar-alt"></i> Desde</label>
                <input class="form-control" id="min" type="date" name="inventario_min" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Inicio">
            </div>
            <div class="col-md-4">
                <label for=""><i class="fas fa-calendar-alt"></i> Hasta</label>
                <input class="form-control" id="max" type="date" name="inventario_max" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Fin">
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <label>Acción</label>
                    <button class="btn btn-outline-primary" type="button" name="inventario" onclick="mostrarTodo(event)">Mostrar Todo</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover display responsive nowrap" id="t_inventario" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Entradas</th>
                        <th>Salidas</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/inventario.js"></script>

</body>

</html>