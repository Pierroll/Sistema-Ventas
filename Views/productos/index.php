<!DOCTYPE html>
<?php include "Views/templates/header.php"; ?>
<?php if ($data['existe']) { ?>
    <button class="btn btn-outline-primary mb-2" type="button" onclick="frmProducto();"><i class="fas fa-plus"></i></button>
<?php } ?>
<a class="btn btn-outline-success mb-2" href="<?php echo BASE_URL; ?>productos/inactivos"><i class="fas fa-trash"></i></a>
<div class="card">
    <div class="card-header">
        Productos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover display responsive nowrap" id="tblProductos" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Medida</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Total</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/producto.js"></script>

</body>

</html>