<?php include "Views/templates/header.php"; ?>

<div class="row mt-2">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-truck"></i> Nueva Compra</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Buscar Producto </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <input id="buscarCompra" class="form-control" type="text" name="codigo" placeholder="BarCode o Nombre">
                    </div>
                    <span class="text-danger float-end" id="errorBusquedaCompra"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover display responsive nowrap" style="width: 100%;" id="detalle_">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblDetalle">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form id="formulario" autocomplete="off">
                    <div class="form-group">
                        <div class="alert alert-info fw-bold text-center" role="alert">
                            <?php echo $data['simbolo']; ?> : <span id="alert_total">00.00</span>
                        </div>
                    </div>
                    <span class="text-danger float-end" id="errorBusqueda"></span>
                    <div class="input-group mb-3">
                        <input type="hidden" id="id_pr" name="id_pr">
                        <input id="buscarProveedor" class="form-control" type="text" placeholder="Buscar Proveedor">
                        <div class="input-group-append">
                            <span class="input-group-text"><button class="btn btn-outline-info" type="button"><i class="fas fa-search"></i></button></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dirección <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-home"></i>
                                </div>
                            </div>
                            <input id="direccion_pr" class="form-control" type="text" placeholder="Dirección" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pagar con: <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <input id="pagar_con" class="form-control" type="text" placeholder="Pagar con" onkeyup="pagarCon(event)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cambio: <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <input id="cambio" class="form-control" type="text" placeholder="Cambio" disabled>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button" onclick="procesarCompra()">Generar Compra</button>
                        <button class="btn btn-outline-danger" type="button" onclick="anularProceso(event)" name="anularCompra">Anular</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/compras.js"></script>

</body>

</html>