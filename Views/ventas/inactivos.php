<?php include "Views/templates/header.php"; ?>
<a class="btn btn-outline-primary mb-2" href="<?php echo BASE_URL; ?>ventas/historial"><i class="fas fa-reply"></i></a>
<div class="card">
    <div class="card-header">
        Ventas Anulados
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4">
                <label for="">Desde</label>
                <input class="form-control" id="min" type="date" name="historial_min" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Inicio">
            </div>
            <div class="col-md-4">
                <label for="">Hasta</label>
                <input class="form-control" id="max" type="date" name="historial_max" value="<?php echo date('Y-m-d'); ?>" placeholder="Selecciona Fecha Fin">
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <label>Acción</label>
                    <button class="btn btn-outline-primary" type="button" name="venta_inactivo" onclick="mostrarTodo(event)">Mostrar Todo</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" style="width: 100%;" id="tbl">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['ventas'] as $venta) { ?>
                        <tr>
                            <td><?php echo $venta['id']; ?></td>
                            <td><?php echo $venta['nombre']; ?></td>
                            <td><?php echo $venta['fecha']; ?></td>
                            <td><?php echo $venta['hora']; ?></td>
                            <td><?php echo $venta['total']; ?></td>
                            <td><a href="#" class="btn btn-outline-danger" onclick="generarReportes(2,<?php echo $venta['id']; ?>)"><i class="fas fa-file-pdf"></i></a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

</body>

</html>