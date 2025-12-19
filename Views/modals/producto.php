<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formulario" autocomplete="off" method="POST">
                <div class="modal-body">
                    <?php include_once "Views/templates/alerta.php"; ?>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <label>BarCode <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </div>
                                    </div>
                                    <input id="codigo" class="form-control" type="text" name="codigo" placeholder="Código de barras" aria-describedby="valid-codigo" required>
                                </div>
                                <span class="text-danger error" id="valid-codigo"></span>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Descripción <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-list"></i>
                                        </div>
                                    </div>
                                    <input id="descripcion" class="form-control" type="text" name="descripcion" placeholder="Nombre del Producto" aria-describedby="valid-descripcion" required>
                                </div>
                                <span class="text-danger error" id="valid-descripcion"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Precio Compra <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                    </div>
                                    <input id="precio_compra" class="form-control" type="number" name="precio_compra" step="0.01" min="0.01" placeholder="Precio Compra" aria-describedby="valid-precio_compra" required>
                                </div>
                                <span class="text-danger error" id="valid-precio_compra"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Precio Venta <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                    </div>
                                    <input id="precio_venta" class="form-control" type="number" name="precio_venta" step="0.01" min="0.01" placeholder="Precio Venta" aria-describedby="valid-precio_venta" required>
                                </div>
                                <span class="text-danger error" id="valid-precio_venta"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="medida"><i class="fas fa-balance-scale"></i> Medidas <span class="text-danger fw-bold">*</span></label>
                                <select id="medida" class="form-control" name="medida" aria-describedby="valid-medida" required>
                                    <?php foreach ($data['medidas'] as $row) { ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger error" id="valid-medida"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="categoria"><i class="fas fa-tags"></i> Categorias <span class="text-danger fw-bold">*</span></label>
                                <select id="categoria" class="form-control" name="categoria" aria-describedby="valid-categoria" required>
                                    <?php foreach ($data['categorias'] as $row) { ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger error" id="valid-categoria"></span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label>Foto (Opcional)</label>
                            <div class="form-group">
                                <label for="imagen" id="icon-image" class="btn btn-primary"><i class="fas fa-image"></i></label>
                                <span id="icon-cerrar"></span>
                                <input id="imagen" class="d-none" type="file" name="imagen" onchange="preview(event)">
                                <input type="hidden" id="foto_actual" name="foto_actual">
                                <img class="img-thumbnail" id="img-preview" width="300">
                            </div>
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

<div class="modal fade" id="base_datos" tabindex="-1" aria-labelledby="Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="Label">Importar Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <a href="<?php echo BASE_URL; ?>assets/ejemplo.xlsx" download="ejemplo.xlsx" class="btn btn-outline-success mx-2">Descargar Formato Excel</a>
                <a href="<?php echo BASE_URL; ?>assets/ejemplo.csv" download="ejemplo.csv" class="btn btn-outline-dark mx-2">Descargar Formato Csv</a>

                <form id="frmBd" onsubmit="importarProductos(event)">
                    <div class="form-group">
                        <label class="m-2">Seleccionar</label><br>
                        <label for="b_datos" class="btn btn-outline-danger"><i class="fas fa-cloud-upload-alt"></i></label>
                        <input id="b_datos" class="d-none" type="file" onchange="base_datos(event)" name="b_datos">
                    </div>
                    <button class="btn btn-outline-primary my-2 d-none" type="submit" id="importar_bd">Importar</button>
                </form>
            </div>
        </div>
    </div>
</div>