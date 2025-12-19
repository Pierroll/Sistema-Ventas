<?php include "Views/templates/header.php"; ?>
<h3 class="float-end"><i class="fas fa-users"></i> Usuarios</h3>
<?php if ($data['existe']) { ?>
    <button class="btn btn-outline-primary mb-2" type="button" onclick="frmUsuario();"><i class="fas fa-plus"></i></button>
<?php } ?>
<a class="btn btn-outline-warning mb-2" href="<?php echo BASE_URL; ?>usuarios/inactivos"><i class="fas fa-trash"></i></a>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="tblUsuarios" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Caja</th>
                        <th>Estado</th>
                        <th></th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/usuario.js"></script>

</body>

</html>