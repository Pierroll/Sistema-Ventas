<div class="modal fade" id="myModal" aria-labelledby="Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Label">Ajuste de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_inventario" onsubmit="registrarInventario(event);" autocomplete="off" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="hidden" id="id" name="id">
                                <label>Buscar Producto <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                    <input id="buscarInventario" class="form-control" type="text" placeholder="Buscar Producto" required>
                                </div>
                            </div>
                            <span class="text-danger float-end" id="errorBusqueda"></span>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-list"></i>
                                        </div>
                                    </div>
                                    <input id="nombre" class="form-control" type="text" placeholder="Descripción del productos" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cantidad <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <input id="cantidad" class="form-control" min="0.01" step="0.01" type="number" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agregar + ó Restar - <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <input id="agregar" class="form-control" type="number" name="agregar" placeholder="Agregar Existencia" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-save"></i> Ajustar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>