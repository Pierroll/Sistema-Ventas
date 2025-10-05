<?php include "Views/templates/header.php"; ?>
<div class="col-md-12">
    <div class="card">
        <div class="card-header text-center bg-info text-white">
            Asignar Permisos
        </div>
        <form id="formulario" onsubmit="registrarPermisos(event)">
            <div class="card-body">
                <div class="row">
                    <?php foreach ($data['datos'] as $row) { ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 text-center text-capitalize p-2">
                            <label for=""><?php echo $row['permiso']; ?></label><br>
                            <input type="checkbox" name="permisos[]" value="<?php echo $row['id']; ?>" <?php echo isset($data['asignados'][$row['id']]) ? 'checked' : ''; ?>>
                        </div>
                    <?php } ?>
                    <input type="hidden" value="<?php echo $data['id_usuario']; ?>" name="id_usuario">
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <a class="btn btn-outline-danger" href="<?php echo BASE_URL; ?>usuarios">Cancelar</a>
                    <button class="btn btn-outline-primary" type="submit">Asignar Permisos</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/usuario.js"></script>

</body>

</html>