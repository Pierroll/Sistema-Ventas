<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo TITLE; ?></title>
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>assets/img/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>assets/img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>assets/img/favicon/favicon-16x16.png">
<link rel="manifest" href="<?php echo BASE_URL; ?>assets/img/favicon/site.webmanifest">

  <!-- General CSS Files -->
  <link href="<?php echo BASE_URL; ?>assets/DataTables/datatables.css" rel="stylesheet" />

  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/bundles/prism/prism.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/full-calendar.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components.css">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/custom.css">
  <link href="<?php echo BASE_URL; ?>assets/css/jquery-ui.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/estilos.css">
  
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar sticky">
        <div class="form-inline me-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                <i data-feather="maximize"></i>
              </a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="<?php echo BASE_URL; ?>assets/img/users/<?php echo $_SESSION['perfil']; ?>" class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
              <div class="dropdown-title"><?php echo $_SESSION['nombre']; ?></div>
              <a href="<?php echo BASE_URL; ?>usuarios/perfil" class="dropdown-item has-icon"> <i class="far
										fa-user"></i> Perfil
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?php echo BASE_URL; ?>usuarios/salir" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                Cerra Sesión
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="<?php echo BASE_URL; ?>administracion/home"> <img alt="image" src="<?php echo BASE_URL; ?>assets/img/logo.png" class="header-logo" /> <span class="logo-name"><?php echo TITLE; ?></span>
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown">
              <a href="<?php echo BASE_URL; ?>administracion/home" class="nav-link"><i data-feather="monitor"></i><span>Tablero</span></a>
            </li>
            <!-- administracion -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="settings"></i><span>Administración</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>administracion/moneda">Monedas</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>usuarios">Usuarios</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>administracion">Configuración</a></li>
              </ul>
            </li>
            <!-- Cajas -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="box"></i><span>Cajas</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>cajas">Lista Cajas</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>cajas/arqueo">Apertura y Cierre</a></li>
              </ul>
            </li>
            <!-- Clientes -->
            <li class="dropdown">
              <a href="<?php echo BASE_URL; ?>clientes" class="nav-link"><i data-feather="users"></i><span>Clientes</span></a>
            </li>
            <!-- Clientes -->
            <li class="dropdown">
              <a href="<?php echo BASE_URL; ?>landing" class="nav-link"><i data-feather="list"></i><span>Ladings</span></a>
            </li>
            <!-- Proveedor -->
            <li class="dropdown">
              <a href="<?php echo BASE_URL; ?>proveedor" class="nav-link"><i data-feather="home"></i><span>Proveedor</span></a>
            </li>
            <!-- Ineventario -->
            <li class="dropdown">
              <a href="<?php echo BASE_URL; ?>productos/inventario" class="nav-link"><i data-feather="calendar"></i><span>Inventario</span></a>
            </li>
            <!-- Productos -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="list"></i><span>Mantenimiento</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>medidas">Medidas</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>categorias">Categorias</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>productos">Productos</a></li>
              </ul>
            </li>
            <!-- Compras -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="truck"></i><span>Compras</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>compras">Nueva Compra</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>compras/historial">Historial Compras</a></li>
              </ul>
            </li>
            <!-- cotizaciones -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="list"></i><span>Cotizaciones</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>cotizaciones">Nueva Cotización</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>cotizaciones/historial">Historial Cotizaciónes</a></li>
              </ul>
            </li>

            <!-- Ventas -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="shopping-cart"></i><span>Ventas</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>ventas">Nueva Venta</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>ventas/historial">Historial Ventas</a></li>
              </ul>
            </li>

            <!-- apartados -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="save"></i><span>Apartados</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>apartados">Apartar Productos</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>apartados/historial">Historial Apartados</a></li>
              </ul>
            </li>

            <!-- credito -->
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="credit-card"></i><span>Creditos</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>creditos">Administrar Creditos</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>creditos/finalizados">Creditos Finalizados</a></li>
                <li><a class="nav-link" href="<?php echo BASE_URL; ?>creditos/abonos">Historial Abonos</a></li>
              </ul>
            </li>

          </ul>
        </aside>
      </div>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-body">