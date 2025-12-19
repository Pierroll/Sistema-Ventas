<!DOCTYPE html>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

  <title>Iniciar | Sesión</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.min.css">
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
                <h4>Login</h4>
              </div>
              <div class="card-body">
                <div class="text-center">
                  <img class="img-thumbnail rounded-circle" src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="LOGO" width="150">
                </div>
                <div class="alert alert-danger text-center fw-bold d-none" role="alert" id="alerta">
                </div>
                <form id="frmLogin" class="form" onsubmit="frmLogin(event)" autocomplete="off">
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="correo" id="correo" value="admin@gmail.com" tabindex="1" required autofocus>
                  </div>
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="#" class="text-small" data-bs-toggle="modal" data-bs-target="#myModal">
                          Olvidate tu contraseña?
                        </a>
                      </div>
                    </div>
                    <input id="clave" type="password" class="form-control" name="clave" tabindex="2" value="admin" required>
                  </div>
                  <div class="form-group float-end">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnAccion" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Correo Electronico</h4>
        </div>
        <div class="modal-body">
          <form id="frmReset" onsubmit="recuperarClave(event)" autocomplete="off">
            <div class="modal-body">
              <div class="col-md-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-envelope"></i>
                    </div>
                  </div>
                  <input id="correo" class="form-control" type="email" name="correo" placeholder="Ingrese Email" required>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-outline-primary" type="submit" id="accion">Restablecer</button>
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
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