<?php
// Views/admin/Index.php
namespace App\Views\Admin;  // Namespace para autoload PSR-4

// Imports con 'use' (resuelve Sonar – carga estática)
use App\Views\Templates\Header;  // Asume que migraste header a clase
use App\Views\Templates\Footer;  // Asume footer
use App\Views\Templates\Alerta;  // Asume alerta

class Index 
{
    private array $data;  // Tipado para Sonar

    public function __construct(array $data = []) 
    {
        $this->data = $data;  // Inyecta datos de controlador
    }

    public function render(): void  // Método principal – Sonar lo ama
    {
        // Render header (clase en lugar de include dinámico)
        $header = new Header();
        $header->render();

        // Contenido principal (tu HTML original, con $this->data)
        $empresa = $this->data['empresa'] ?? [];  // Accede seguro a datos
        $monedas = $this->data['monedas'] ?? [];
        $existe = $this->data['existe'] ?? false;
        ?>
        <h3><i class="fas fa-home"></i> Datos de la Empresa</h3>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="formulario" autocomplete="off">
                        <?php 
                        // Render alerta (clase en lugar de include)
                        $alerta = new Alerta($this->data);  // Pasa $data si necesita
                        $alerta->render();
                        ?>

                        <div class="row">
                            <input id="id" type="hidden" name="id" value="<?= htmlspecialchars($empresa['id'] ?? '') ?>" required>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ruc <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                        </div>
                                        <input id="ruc" class="form-control" type="number" name="ruc" placeholder="Ruc" value="<?= htmlspecialchars($empresa['ruc'] ?? '') ?>" aria-describedby="valid-ruc" required>
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
                                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" aria-describedby="valid-nombre" required>
                                    </div>
                                    <span class="text-danger error" id="valid-nombre"></span>
                                </div>
                            </div>
                            <!-- ... Repite para teléfono, correo, web, dirección, etc. (usa htmlspecialchars para seguridad) ... -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Telefono <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                        </div>
                                        <input id="telefono" class="form-control" type="text" name="telefono" placeholder="Teléfono" value="<?= htmlspecialchars($empresa['telefono'] ?? '') ?>" aria-describedby="valid-telefono" required>
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
                                        <input id="correo" class="form-control" type="text" name="correo" placeholder="Correo" value="<?= htmlspecialchars($empresa['correo'] ?? '') ?>" aria-describedby="valid-correo" required>
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
                                        <input id="site" class="form-control" type="text" name="site" placeholder="Web" value="<?= htmlspecialchars($empresa['site'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-home"></i> Dirección <span class="text-danger">*</span></label>
                                    <textarea id="direccion" class="form-control" name="direccion" rows="3" placeholder="Dirección" aria-describedby="valid-direccion" required><?= htmlspecialchars($empresa['direccion'] ?? '') ?></textarea>
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
                                        <input id="cant_factura" class="form-control" type="number" name="cant_factura" placeholder="Cantidad Factura" value="<?= htmlspecialchars($empresa['cant_factura'] ?? '') ?>" aria-describedby="valid-factura" required>
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
                                        <input id="impuesto" class="form-control" type="number" name="impuesto" placeholder="Impuesto" value="<?= htmlspecialchars($empresa['impuesto'] ?? '') ?>" aria-describedby="valid-impuesto" required>
                                    </div>
                                    <span class="text-danger error" id="valid-impuesto"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Moneda <span class="text-danger">*</span></label>
                                    <select id="moneda" class="form-control" name="moneda" aria-describedby="valid-moneda">
                                        <?php foreach ($monedas as $row) { ?>
                                            <option value="<?= htmlspecialchars($row['id']) ?>" <?= ($row['id'] == $empresa['moneda']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['simbolo'] . ' - ' . $row['nombre']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger error" id="valid-moneda"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="">Agradecimiento</label>
                                <div id="editor">
                                    <?= htmlspecialchars($empresa['mensaje'] ?? '') ?>
                                </div>
                            </div>
                            <div class="col-md-4 mt-2">
                                <div class="form-group">
                                    <label><i class="fas fa-image"></i> Logo - PNG (512 x 512 pixeles) recomendado </label>
                                    <input type="hidden" id="foto_actual">
                                    <label for="imagen" id="icon-image" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> </label>
                                    <span id="icon-cerrar"></span>
                                    <input id="imagen" class="d-none" type="file" name="imagen" onchange="previewLogo(event)">
                                    <img class="img-thumbnail" id="img-preview" src="<?= BASE_URL ?>assets/img/<?= htmlspecialchars($empresa['logo'] ?? '') ?>" width="200">
                                </div>
                            </div>
                        </div>
                        <?php if ($existe) { ?>
                            <div class="float-end">
                                <button class="btn btn-outline-primary" type="submit" id="btnAccion">Modificar</button>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- FIX: Script como método o include estático -->
        <script src="<?= BASE_URL ?>assets/js/modulos/admin.js"></script>
        <?php
    }

    // Métodos para templates (si migras header/footer/alerta a clases)
    private function renderHeader(): void 
    {
        include 'Views/templates/header.php';  // Temporal; migra a clase después
    }

    private function renderFooter(): void 
    {
        include 'Views/templates/footer.php';  // Temporal
    }

    private function renderAlerta(): void 
    {
        include_once 'Views/templates/alerta.php';  // Temporal
    }
}
?>