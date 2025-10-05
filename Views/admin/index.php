<?php include "Views/templates/header.php"; ?>
<h3><i class="fas fa-home"></i> Datos de la Empresa</h3>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form id="formulario" autocomplete="off">
                <?php include_once "Views/templates/alerta.php"; ?>

                <div class="row">
                    <input id="id" type="hidden" name="id" value="<?php echo $data['empresa']['id'] ?>" required>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ruc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                                <input id="ruc" class="form-control" type="number" name="ruc" placeholder="Ruc" value="<?php echo $data['empresa']['ruc'] ?>" aria-describedby="valid-ruc" required>
                            </div>
                            <span class="text-danger error" id="valid-ruc"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-list"></i>
                                    </div>
                                </div>
                                <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre" value="<?php echo $data['empresa']['nombre'] ?>" aria-describedby="valid-nombre" required>
                            </div>
                            <span class="text-danger error" id="valid-nombre"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Telefono <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                                <input id="telefono" class="form-control" type="text" name="telefono" placeholder="Teléfono" value="<?php echo $data['empresa']['telefono'] ?>" aria-describedby="valid-telefono" required>
                            </div>
                            <span class="text-danger error" id="valid-telefono"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Correo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <input id="correo" class="form-control" type="text" name="correo" placeholder="Correo" value="<?php echo $data['empresa']['correo'] ?>" aria-describedby="valid-correo" required>
                            </div>
                            <span class="text-danger error" id="valid-correo"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Web <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-list"></i>
                                    </div>
                                </div>
                                <input id="site" class="form-control" type="text" name="site" placeholder="Web" value="<?php echo $data['empresa']['site'] ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-home"></i> Dirección <span class="text-danger">*</span></label>
                            <textarea id="direccion" class="form-control" name="direccion" rows="3" placeholder="Dirección" aria-describedby="valid-direccion" required><?php echo $data['empresa']['direccion'] ?></textarea>
                            <span class="text-danger error" id="valid-direccion"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cantidad Factura <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <input id="cant_factura" class="form-control" type="number" name="cant_factura" placeholder="Cantidad Factura" value="<?php echo $data['empresa']['cant_factura'] ?>" aria-describedby="valid-factura" required>
                            </div>
                            <span class="text-danger error" id="valid-factura"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Impuesto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </div>
                                <input id="impuesto" class="form-control" type="number" name="impuesto" placeholder="Impuesto" value="<?php echo $data['empresa']['impuesto'] ?>" aria-describedby="valid-impuesto" required>
                            </div>
                            <span class="text-danger error" id="valid-impuesto"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Moneda <span class="text-danger">*</span></label>
                            <select id="moneda" class="form-control" name="moneda" aria-describedby="valid-moneda">
                                <?php foreach ($data['monedas'] as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $data['empresa']['moneda']) ? 'selected' : ''; ?>>
                                        <?php echo $row['simbolo'] . ' - ' . $row['nombre'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <span class="text-danger error" id="valid-moneda"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="">Agradecimiento</label>
                        <div id="editor">
                            <?php echo $data['empresa']['mensaje'] ?>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label><i class="fas fa-image"></i> Logo - PNG (512 x 512 pixeles) recomendado </label>
                            <input type="hidden" id="foto_actual">
                            <label for="imagen" id="icon-image" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> </label>
                            <span id="icon-cerrar"></span>
                            <input id="imagen" class="d-none" type="file" name="imagen" onchange="previewLogo(event)">
                            <img class="img-thumbnail" id="img-preview" src="<?php echo BASE_URL; ?>assets/img/<?php echo $data['empresa']['logo']; ?>" width="200">
                        </div>
                    </div>
                </div>
                <?php if ($data['existe']) { ?>
                    <div class="float-end">
                    <button class="btn btn-outline-primary" type="submit" id="btnAccion">Modificar</button>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/admin.js"></script>

</body>

</html>