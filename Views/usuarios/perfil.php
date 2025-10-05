<?php include "Views/templates/header.php"; ?>
<div class="row mt-sm-4">
    <div class="col-12 col-md-12 col-lg-4">
        <div class="card author-box">
            <div class="card-body">
                <div class="author-box-center">
                    <input type="hidden" id="foto_actual">
                    <span id="icon-cerrar"></span>
                    <img alt="image" id="img-preview" src="<?php echo BASE_URL . 'assets/img/users/' . $data['perfil'] ?>" class="rounded-circle author-box-picture">
                    <div class="clearfix"></div>
                    <div class="author-box-name">
                        <a href="#"><?php echo $data['nombre']; ?></a>
                    </div>
                    <div class="author-box-job"><?php echo $data['correo']; ?></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Cambiar Contraseña</h4>
            </div>
            <div class="card-body">
                <form id="frmCambiarPass" onsubmit="frmCambiarPass(event);">
                    <div class="form-group">
                        <label>Contraseña Actual <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="clave_actual" class="form-control" type="password" name="clave_actual" placeholder="Contraseña Actual" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Contraseña Nueva <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="clave_nueva" class="form-control" type="password" name="clave_nueva" placeholder="Contraseña Nueva" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirmar Contraseña <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <input id="confirmar_clave" class="form-control" type="password" name="confirmar_clave" placeholder="Confirmar Contraseña" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-2">
                        <button class="btn btn-outline-primary" type="submit">Modificar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-8">
        <div class="card">
            <div class="padding-20">
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab2" data-bs-toggle="tab" href="#about" role="tab" aria-selected="true">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab2" data-bs-toggle="tab" href="#settings" role="tab" aria-selected="false">Setting</a>
                    </li>
                </ul>
                <div class="tab-content tab-bordered" id="myTab3Content">
                    <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="home-tab2">
                        <div class="row">
                            <div class="col-md-3 col-6 b-r">
                                <strong>Nombre</strong>
                                <br>
                                <p class="text-muted"><?php echo $data['nombre']; ?></p>
                            </div>
                            <div class="col-md-3 col-6 b-r">
                                <strong>Teléfono</strong>
                                <br>
                                <p class="text-muted"><?php echo $data['telefono']; ?></p>
                            </div>
                            <div class="col-md-3 col-6 b-r">
                                <strong>Email</strong>
                                <br>
                                <p class="text-muted"><?php echo $data['correo']; ?></p>
                            </div>
                            <div class="col-md-3 col-6">
                                <strong>Fecha Registro</strong>
                                <br>
                                <p class="text-muted"><?php echo $data['fecha']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="profile-tab2">
                        <form id="frmDatos" onsubmit="actualizarDatos(event)" autocomplete="off">
                            <div class="card-header">
                                <h4>Edit Profile</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $data['nombre']; ?>" placeholder="Nombre">
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $data['apellido']; ?>" placeholder="Apellido">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-7 col-12">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $data['correo']; ?>" placeholder="Correo">
                                    </div>
                                    <div class="form-group col-md-5 col-12">
                                        <label>Telefono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $data['telefono']; ?>" placeholder="Telefono">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>Direccion</label>
                                        <textarea class="form-control summernote-simple" id="direccion" name="direccion" placeholder="Direccion"><?php echo $data['direccion']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group mb-0 col-12">
                                        <label for="imagen" class="btn btn-outline-info" id="icon-image">
                                            <i class="fas fa-camera fa-2x"></i>
                                            <input id="imagen" class="d-none" type="file" onchange="preview(event)" name="imagen">
                                            <input type="hidden" name="foto_actual" value="<?php echo $data['perfil'] ?>">
                                        </label>
                                        <span id="icon-cerrar"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary" type="submit">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/usuario.js"></script>

</body>

</html>