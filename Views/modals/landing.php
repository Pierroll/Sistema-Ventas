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
                        <label>Página <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                            <input id="pagina" class="form-control" type="text" name="pagina" placeholder="Página" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Teléfono <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                            <input id="telefono" class="form-control" type="number" name="telefono" placeholder="Teléfono" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Correo <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <input id="correo" class="form-control" type="email" name="correo" placeholder="Correo" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Negocio <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="negocio" class="form-control" type="text" name="negocio" placeholder="Negocio" required>
                        </div>
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

<div id="modalCliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar a Cliente</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    </symbol>
                    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                    </symbol>
                    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                    </symbol>
                </svg>
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    <div>
                        Para agregar a clientes se necesita 2 Campos Adicionales
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" id="idProceso">
                    <label>Dni (Identidad) <span class="text-danger">*</span> </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </div>
                        </div>
                        <input id="dni" class="form-control" type="number" name="dni" placeholder="Dni" required>
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
                        <input id="direccion" class="form-control" type="text" name="direccion" placeholder="Dirección" required>
                    </div>
                </div>

                <div class="float-end">
                    <button class="btn btn-outline-primary" type="submit" id="btnprocesar">Registrar</button>
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </div>
        </div>
    </div>
</div>