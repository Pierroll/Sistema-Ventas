<?php include "Views/templates/header.php"; ?>
<div class="row mt-2">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-cash-register"></i> Nueva Cotización</h3>
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
                        <input id="buscarCotizacion" class="form-control" type="text" placeholder="BarCode o Nombre">
                    </div>
                    <span class="text-danger float-end" id="errorBusquedaCotizacion"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover display responsive nowrap" style="width: 100%;" id="detalle_">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Medida</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Desc.</th>
                                <th>SubTotal</th>
                                <th>Impuesto</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblDetalleCotizacion">
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label for="comentario">Comentario</label>
                    <textarea id="comentario" class="form-control" rows="3" placeholder="Comentario"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <div class="alert alert-info fw-bold text-center" role="alert">
                        <label for="">Total</label>
                        <?php echo $data['simbolo']; ?> : <span id="alert_total"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pagar_con" class="font-weight-bold">Pagar con: </label>
                    <input id="pagar_con" class="form-control" type="text" placeholder="Pagar con" onkeyup="pagarCon(event)">
                </div>
                <div class="form-group">
                    <label for="cambio" class="font-weight-bold">Cambio</label>

                    <input id="cambio" class="form-control" type="text" placeholder="Cambio" disabled>
                </div>
                <div class="form-group">
                    <label for="validez">Validez</label>
                    <select id="validez" class="form-control">
                        <option value="5 DIAS">5 DIAS</option>
                        <option value="10 DIAS">10 DIAS</option>
                        <option value="15 DIAS">15 DIAS</option>
                        <option value="20 DIAS">20 DIAS</option>
                        <option value="30 DIAS">30 DIAS</option>
                    </select>
                </div>

                <span class="text-danger float-end" id="errorBusqueda"></span>

                <div class="input-group mb-3">
                    <input type="hidden" id="id" name="id">
                    <input id="buscarCliente" class="form-control buscarCliente" type="text" placeholder="Buscar Cliente" name="nombre">
                </div>
                <div class="input-group mb-3">
                    <input id="direccion" class="form-control" type="text" placeholder="Direccion" name="direccion" disabled>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" type="button" onclick="procesarCotizacion()">Generar Cotización</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/cotizaciones.js"></script>

</body>

</html>