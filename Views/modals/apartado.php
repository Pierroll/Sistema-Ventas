<div class="modal fade" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="titulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="formulario" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Buscar Producto </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <input id="buscarProducto" class="form-control" type="text" placeholder="BarCode o Nombre">
                        </div>
                        <span class="text-danger float-end" id="errorBusquedaProducto"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-light table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Id</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Sub Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tblDetalleApart">
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="input-group mb-3">
                                <input type="hidden" id="id">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                                <input id="buscarCliente" class="form-control buscarCliente" type="text" placeholder="Buscar Cliente" name="nombre">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group mb-3">
                                <input id="direccion" class="form-control" type="text" placeholder="Direccion" name="direccion" disabled>
                                <div class="input-group-append d-none">
                                    <span class="input-group-text"><button class="btn btn-outline-success" type="submit" id="procesar"><i class="fas fa-save"></i></button></span>
                                </div>
                            </div>
                        </div>
                        <span class="text-danger float-end" id="errorBusqueda"></span>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Retiro </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-date"></i>
                                        </div>
                                    </div>
                                    <input id="start" name="start" class="form-control" type="date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hora Retiro </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-time"></i>
                                        </div>
                                    </div>
                                    <input id="hora" name="hora" class="form-control" type="time">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Monto Abonar </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-dollar-sing"></i>
                                        </div>
                                    </div>
                                    <input id="abono" name="abono" class="form-control" type="text" placeholder="Monto a Abonar">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total">Total a pagar</label>
                        <h1 id="total"></h1>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger" type="button" id="btnEliminar">Eliminar</button>
                    <button class="btn btn-info" id="btnAccion" type="button">Registrar</button>
                </div>
            </form>

        </div>
    </div>
</div>