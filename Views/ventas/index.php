<?php include "Views/templates/header.php"; ?>
<div class="row mt-2">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-cash-register"></i> Nueva Venta</h3>
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
                        <input id="buscarVenta" class="form-control" type="text" placeholder="BarCode o Nombre">
                    </div>
                    <span class="text-danger float-end" id="errorBusquedaVenta"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover display responsive nowrap" style="width: 100%;" id="detalle_">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Sub Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblDetalleVenta">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form id="formulario" onsubmit="registrarCliVenta(event);" autocomplete="off">
                    <div class="form-group">
                        <div class="alert alert-info fw-bold text-center" role="alert">
                            <label for="">Total</label>
                            <?php echo $data['simbolo']; ?> : <span id="alert_total"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pagar_con" class="font-weight-bold">Pagar con: </label>
                                <input id="pagar_con" class="form-control" type="text" placeholder="Pagar con" onkeyup="pagarCon(event)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cambio" class="font-weight-bold">Cambio</label>

                                <input id="cambio" class="form-control" type="text" placeholder="Cambio" disabled>
                            </div>
                        </div>
                    </div>
                    <span class="text-danger float-end" id="errorBusqueda"></span>

                    <div class="input-group mb-3">
                        <input type="hidden" id="id" name="id">
                        <input id="buscarCliente" class="form-control buscarCliente" type="text" placeholder="Buscar Cliente" name="nombre">
                        <div class="input-group-append" id="activarCliente">
                            <span class="input-group-text"><button class="btn btn-outline-info" type="button" onclick="activarCampos()"><i class="fas fa-plus"></i></button></span>
                        </div>
                        <div class="input-group-append d-none" id="desactivarCliente">
                            <span class="input-group-text"><button class="btn btn-outline-danger" type="button" onclick="desactivarCampos()"><i class="fas fa-minus"></i></button></span>
                        </div>
                    </div>
                    <div class="row d-none" id="clienteVenta">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dni </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                    </div>
                                    <input id="dni" class="form-control" type="number" placeholder="Dni" name="dni" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                    </div>
                                    <input id="telefono" class="form-control" type="number" placeholder="Teléfono" name="telefono" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="direccion" class="form-control" type="text" placeholder="Direccion" name="direccion" disabled>
                        <div class="input-group-append d-none" id="activarBotonGuardar">
                            <span class="input-group-text"><button class="btn btn-outline-success" type="submit" id="btnAccion"><i class="fas fa-save"></i></button></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="metodo">Metodo</label>
                        <select id="metodo" class="form-control" name="metodo">
                            <option value="1">Contado</option>
                            <option value="2">Credito</option>
                        </select>
                    </div>
                </form>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" type="button" onclick="procesarVenta()">Generar Venta</button>
                    <button class="btn btn-outline-danger" type="button" onclick="anularProceso(event)" name="anularVenta">Anular</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/ventas.js"></script>

</body>

</html>