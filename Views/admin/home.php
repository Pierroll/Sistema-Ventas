<?php include "Views/templates/header.php"; ?>
<div class="row">

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-primary">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Usuarios</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['usuarios']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>usuarios">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/user.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-danger">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Medidas</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['medidas']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>medidas">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/medida.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-dark">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Categorias</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['categorias']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>categorias">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/categoria.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-warning">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Productos</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['productos']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>productos">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/producto.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-info">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Clientes</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['clientes']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>clientes">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/client.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-success">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Ventas por Día</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['ventas']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>ventas/historial">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/sales.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-danger">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Compras por Día</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['compras']['total']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>compras/historial">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/compra.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="card card-primary">
            <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                    <div class="row ">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                            <div class="card-content">
                                <h5 class="font-15">Caja</h5>
                                <h2 class="mb-3 font-18"><?php echo $data['empresa']['simbolo'] . ':  ' . $data['monto_general']; ?></h2>
                                <p class="mb-0"><span class="col-green"><a href="<?php echo BASE_URL; ?>cajas/arqueo">Ver Detalle</a></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                            <div class="banner-img">
                                <img src="<?php echo BASE_URL; ?>assets/img/modulos/caja.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row my-4">
    <div class="col-xl-8 col-md-8 col-sm-12">
        <div class="card">
            <div class="card-header border-info fw-bold text-center">
                
                <select id="year" class="float-end" onchange="actualizarGrafico()">
                    <?php
                    $fecha = date('Y');
                    for ($i = 2021; $i <= $fecha; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $fecha) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
                Comparación de Compras y Ventas por Mes
            </div>
            <div class="card-body">
                <canvas id="ventas_mes"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6">
        <div class="card">
            <div class="card-header border-info fw-bold">
                Productos Más Vendidos
            </div>
            <div class="card-body">
                <canvas id="topProductos"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-6 mt-2">
        <div class="card">
            <div class="card-header border-info fw-bold">
                Stock Mínimo
            </div>
            <div class="card-body">
                <canvas id="stockMinimo"></canvas>
            </div>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/home.js"></script>

</body>

</html>