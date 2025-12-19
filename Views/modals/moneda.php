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
                        <label>Simbolo <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="simbolo" class="form-control" type="text" name="simbolo" aria-describedby="valid-simbolo" placeholder="Simbolo de la moneda" required>
                        </div>
                        <span class="text-danger error" id="valid-simbolo"></span>
                    </div>
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="nombre" class="form-control" type="text" name="nombre" aria-describedby="valid-nombre" placeholder="Nombre del Moneda" required>
                        </div>
                        <span class="text-danger error" id="valid-nombre"></span>
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