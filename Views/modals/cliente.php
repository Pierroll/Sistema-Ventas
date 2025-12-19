<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formulario" autocomplete="off" method="POST">
                <div class="modal-body">
                    <?php include_once "Views/templates/alerta.php"; ?>

                    <div class="form-group">
                        <input type="hidden" id="id" name="id">
                        <label>Dni <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                            <input id="dni" class="form-control" type="number" name="dni" placeholder="Documento de Identidad" aria-describedby="valid-dni" required>
                        </div>
                        <span class="text-danger error" id="valid-dni"></span>
                    </div>
                    <div class="form-group">
                        <label>Nombres <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <input id="buscarCliente" class="form-control" type="text" name="nombre" placeholder="Nombre del cliente" aria-describedby="valid-nombre" required>
                        </div>
                        <span class="text-danger error" id="valid-nombre"></span>
                    </div>
                    <div class="form-group">
                        <label>Teléfono <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                            <input id="telefono" class="form-control" type="number" name="telefono" placeholder="Teléfono" aria-describedby="valid-telefono" required>
                        </div>
                        <span class="text-danger error" id="valid-telefono"></span>
                    </div>
                    <div class="form-group">
                        <label for="direccion"><i class="fas fa-home"></i> Dirección <span class="text-danger fw-bold">*</span> </label>
                        <textarea id="direccion" class="form-control" name="direccion" rows="3" placeholder="Dirección" aria-describedby="valid-direccion" required></textarea>
                        <span class="text-danger error" id="valid-direccion"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-primary" type="submit" id="btnAccion">Registrar</button>
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>