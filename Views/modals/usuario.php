<!-- Modal with form -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formulario" autocomplete="off" method="POST" method="POST">
                <div class="modal-body">
                    <?php include_once "Views/templates/alerta.php"; ?>                    
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del usuario" aria-describedby="valid-nombre" required>
                        </div>
                        <span class="text-danger error" id="valid-nombre"></span>
                    </div>
                    <div class="form-group">
                        <label>Correo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <input id="correo" class="form-control" type="email" name="correo" placeholder="Correo electrónico" aria-describedby="valid-correo" required>
                        </div>
                        <span class="text-danger error" id="valid-correo"></span>
                    </div>
                    <div class="form-group">
                        <label>Caja <span class="text-danger">*</span></label>
                        <select id="caja" class="form-control" name="caja" aria-describedby="valid-caja" required>
                            <?php foreach ($data['cajas'] as $row) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['caja']; ?></option>
                            <?php } ?>
                        </select>
                        <span class="text-danger error" id="valid-caja"></span>
                    </div>
                    <div class="row" id="claves">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    <input id="clave" class="form-control" type="password" name="clave" aria-describedby="valid-clave" placeholder="Contraseña">
                                </div>
                                <span class="text-danger error" id="valid-clave"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirmar Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-unlock"></i>
                                        </div>
                                    </div>
                                    <input id="confirmar" class="form-control" type="password" name="confirmar" aria-describedby="valid-confirmar" placeholder="Confirmar contraseña">
                                </div>
                                <span class="text-danger error" id="valid-confirmar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect" id="btnAccion">Registrar</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>