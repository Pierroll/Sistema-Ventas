<?php include "Views/templates/header.php"; ?>
<?php if ($data['existe']) { ?>
    <button class="btn btn-outline-primary mb-2" type="button" onclick="nuevoLanding();"><i class="fas fa-plus"></i></button>
<?php } ?>
<div class="card">
    <div class="card-header">
        Landing
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="tblLanding" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Fecha</th>
                        <th>Página</th>
                        <th>Nombre</th>
                        <th>Télefono</th>
                        <th>Correo</th>
                        <th>Negocio</th>
                        <th>Estado</th>
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

<script src="<?php echo BASE_URL; ?>assets/js/modulos/landing.js"></script>

</body>

</html>