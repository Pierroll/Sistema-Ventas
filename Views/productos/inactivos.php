<?php include "Views/templates/header.php"; ?>
<a class="btn btn-outline-primary mb-2" href="<?php echo BASE_URL; ?>productos"><i class="fas fa-reply"></i></a>
<div class="card">
    <div class="card-header">
        Productos Inactivos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" style="width: 100%;" id="tbl">
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
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['productos'] as $row) {
                        if ($row['estado'] == 0) {
                            $estado = '<span class="badge bg-danger">Inactivo</span>';
                        }
                    ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><img class="img-thumbnail" src="<?php echo BASE_URL; ?>Assets/img/pro/<?php echo $row['foto'] ?>" width="50"></td>
                            <td><?php echo $row['codigo']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td><?php echo $row['medida']; ?></td>
                            <td><?php echo $row['categoria']; ?></td>
                            <td><?php echo $row['precio_venta']; ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $estado; ?></td>
                            <td>
                                <?php if ($data['existe']) { ?>
                                    <button class="btn btn-outline-success" type="button" onclick="btnReingresarPro(<?php echo $row['id'] ?>);"><i class="fas fa-trash-restore"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/producto.js"></script>

</body>

</html>