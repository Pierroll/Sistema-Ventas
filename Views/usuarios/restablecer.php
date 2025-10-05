<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>Iniciar | Sesión</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/bundles/bootstrap-social/bootstrap-social.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo BASE_URL; ?>assets/img/favicon/site.webmanifest">
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container mt-5">

                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Restablecer Contraseña</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <img class="img-thumbnail rounded-circle" src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="LOGO" width="150">
                                </div>
                                <form id="frmrestablecer" onsubmit="frmRestablecer(event)">
                                    <div class="form-group">
                                    <label class="small mb-1" for="clave_nueva">Nueva Contraseña</label>
                                        <input class="form-control py-4" id="clave_nueva" name="clave_nueva" type="password" placeholder="Ingrese tu nueva clave" />
                                    </div>
                                    <div class="form-group">
                                    <label class="small mb-1" for="confirmar">Confirmar Contraseña</label>
                                        <input type="hidden" value="<?php echo $data; ?>" name="token">
                                        <input class="form-control py-4" id="confirmar" name="confirmar" type="password" placeholder="Confirmar Contraseña" />
                                    </div>
                                    <div class="alert alert-danger text-center d-none" id="alerta" role="alert">
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="submit" id="accion">Restablecer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/app.min.js"></script>
    <!-- JS Libraies -->
    <!-- Page Specific JS File -->
    <!-- Template JS File -->
    <script src="<?php echo BASE_URL; ?>assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="<?php echo BASE_URL; ?>assets/js/custom.js"></script>

    <script src="<?php echo BASE_URL; ?>assets/js/sweetalert2.all.min.js"></script>
    <script>
        const base_url = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/login.js"></script>
</body>

</html>