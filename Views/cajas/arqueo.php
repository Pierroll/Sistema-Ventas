<?php include "Views/templates/header.php"; ?>
<?php
if ($data['abrir_caja'] && empty($data['datos'])) {
    echo '<button class="btn btn-outline-primary mb-2" type="button" onclick="arqueoCaja();" id="btnAbrirCaja"><i class="fas fa-unlock"></i></button>';
}
if ($data['cerrar_caja'] && !empty($data['datos'])) {
    echo '<button class="btn btn-outline-warning mb-2" type="button" onclick="cerrarCaja();" id="btnCerrarCaja"><i class="fas fa-lock"></i></button>';
}
?>
<div class="card">
    <div class="card-header">
        Arqueo de Caja
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="t_arqueo" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Monto_inicial</th>
                        <th>Monto_final</th>
                        <th>Fecha_apertura</th>
                        <th>Fecha_cierre</th>
                        <th>Total ventas</th>
                        <th>Monto Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/apertura.js"></script>

</body>

</html>