<?php

class Productos extends Controller{
    public function __construct() {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $data['medidas'] = $this->model->getMedidas();
        $data['categorias'] = $this->model->getCategorias();
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_producto");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'producto';
        $this->views->getView('productos',  "index", $data);
    }
    public function listar()
    {
        $id_user = $_SESSION['id_usuario'];
        $data = $this->model->getProductos(1);
        $date = date('Y-m-d');
        $modificar = $this->model->verificarPermisos($id_user, "modificar_producto");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_producto");
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['date'] = $date;
            $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . BASE_URL . "assets/img/pro/" . $data[$i]['foto'] . '" width="50">';
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['subTotal'] = '<span class="badge bg-info">'.number_format($data[$i]['cantidad'] * $data[$i]['precio_venta'], 2).'</span>';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarPro(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarPro(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['codigo']) && isset($_POST['descripcion']) && isset($_POST['precio_compra'])) {
            $codigo = strClean($_POST['codigo']);
            $nombre = strClean($_POST['descripcion']);
            $precio_compra = strClean($_POST['precio_compra']);
            $precio_venta = strClean($_POST['precio_venta']);
            $categoria = strClean($_POST['categoria']);
            $medida = strClean($_POST['medida']);
            $id = strClean($_POST['id']);
            $img = $_FILES['imagen'];
            $name = $img['name'];
            $tmpname = $img['tmp_name'];        
            $fecha = date("YmdHis");
            if (empty($codigo) || empty($nombre) || empty($precio_compra) || empty($precio_venta)
            || empty($categoria) || empty($medida)) {
                $msg = array('msg' => 'Todo los campos con * son obligatorios', 'icono' => 'warning');
            }else{
                if (strlen($codigo) < 8) {
                    $msg = array('msg' => 'el código debe contener un mínimo 8 caracteres', 'icono' => 'warning');
                } else {
                    if (strlen($nombre) < 5) {
                        $msg = array('msg' => 'El nombre debe contener un mínimo 5 caracteres', 'icono' => 'warning');
                    } else {
                        if (strlen($precio_compra) < 1) {
                            $msg = array('msg' => 'El precio compra debe contener un minímo 1 caracter', 'icono' => 'warning');
                        } else {
                            if (strlen($precio_venta) < 1) {
                                $msg = array('msg' => 'La precio venta debe contener un minímo 1 caracter', 'icono' => 'warning');
                            } else {
                                if (!empty($name)) {
                                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                                    $formatos_permitidos =  array('png', 'jpeg', 'jpg');
                                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                                    if (!in_array($extension, $formatos_permitidos)) {
                                        $msg = array('msg' => 'Archivo no permitido', 'icono' => 'warning');
                                    } else {
                                        $imgNombre = $fecha . ".jpg";
                                        $destino = "assets/img/pro/" . $imgNombre;
                                    }
                                }else if(!empty($_POST['foto_actual']) && empty($name)){
                                    $imgNombre = $_POST['foto_actual'];
                                }else{
                                    $imgNombre = "default.png";
                                }
                                if ($id == "") {
                                        $data = $this->model->registrarProducto($codigo, $nombre, $precio_compra, $precio_venta, $medida, $categoria, $imgNombre);
                                        if ($data == 0) {
                                            $msg = array('msg' => 'El producto ya existe', 'icono' => 'warning');                          
                                        } else if ($data > 0) {
                                            if (!empty($name)) {
                                                move_uploaded_file($tmpname, $destino);
                                            }
                                            $msg = array('msg' => 'Producto registrado', 'icono' => 'success');
                                        } else {
                                            $msg = array('msg' => 'Error al registrar el producto', 'icono' => 'error');
                                        }
                                }else{
                                    $imgDelete = $this->model->editarPro($id);
                                    if ($imgDelete['foto'] != 'default.png') {
                                        if (file_exists("assets/img/pro/" . $imgDelete['foto'])) {
                                            unlink("assets/img/pro/" . $imgDelete['foto']);
                                        }
                                    }
                                    $data = $this->model->modificarProducto($codigo, $nombre, $precio_compra, $precio_venta, $medida, $categoria, $imgNombre, $id);
                                    if ($data == "modificado") {
                                        if (!empty($name)) {
                                            move_uploaded_file($tmpname, $destino);
                                        }
                                        $msg = array('msg' => 'Producto modificado', 'icono' => 'success');
                                    } else if ($data == "existe") {
                                        $msg = array('msg' => 'El producto ya existe', 'icono' => 'warning');
                                    } else {
                                        $msg = array('msg' => 'Error al modificar el producto', 'icono' => 'error');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }        
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar(int $id)
    {
        $data = $this->model->editarPro($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionPro(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Producto dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionPro(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Producto reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar el producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inventario()
    {
        $id_user = $_SESSION['id_usuario'];
        $inventario = $this->model->verificarPermisos($id_user, "inventario");
        $reporte = $this->model->verificarPermisos($id_user, "reporte_pdf_inventario");
        if (!empty($inventario) || $id_user == 1) {
            $data['inventario'] = true;
        } else {
            $data['inventario'] = false;
        }
        if (!empty($reporte) || $id_user == 1) {
            $data['reporte'] = true;
        } else {
            $data['reporte'] = false;
        }
        $data['modal'] = 'inventario';
        $this->views->getView('productos',  "inventario", $data);
    }
    public function registrarInventario()
    {
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "inventario");
        if (!$perm && $id_user != 1) {
            header('Location: Administracion/permisos');
        } else {
            $agregar = strClean($_POST['agregar']);
            $id = strClean($_POST['id']);
            $fecha = date('Y-m-d');
            if (empty($id) || empty($agregar)) {
                $msg = array('msg' => 'Todo los campos con * son obligatorios', 'icono' => 'warning');
            } else {
                if (is_numeric($agregar)) {
                    if ($agregar > 0) {
                        $data = $this->model->ingresarEntrada($id, $id_user, $agregar, $fecha);
                    } else {
                        $data = $this->model->ingresarSalida($id, $id_user, abs($agregar), $fecha);
                    }
                    if ($data == 1) {
                        $cantidad = $this->model->editarPro($id);
                        $cant_total = $cantidad['cantidad'] + $agregar;
                        $this->model->actualizarStock($cant_total, $id);
                        $msg = array('msg' => 'Cantidad del producto Ajustado', 'icono' => 'success');
                    } else {
                        $msg = array('msg' => 'Error al ajustar', 'icono' => 'error');
                    }
                } else {
                    $msg = array('msg' => 'Error ingresa un número valido', 'icono' => 'error');
                }
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function listarInventario()
    {
        $data = $this->model->getInventarios();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function pdfInventario($accion)
    {
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "reporte_pdf_inventario");
        if (!empty($perm) || $id_user == 1) {
            $empresa = $this->model->getEmpresa();
            if ($accion == 'all') {
                $productos = $this->model->getInventarios();
            } else {
                $array = explode(',', $accion);
                $desde = $array[0];
                $hasta = $array[1];
                $productos = $this->model->filtroInventarios($desde, $hasta);
            }
            if (empty($productos)) {
                echo 'No hay registro';
            } else {
                require('Libraries/fpdf/fpdf.php');
                include('Libraries/phpqrcode/qrlib.php');
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();
                $pdf->SetMargins(10, 0, 0);
                $pdf->SetTitle('Reporte Inventario');
                $pdf->SetFont('Arial', '', 14);
                $pdf->Cell(195, 8, utf8_decode($empresa['nombre']), 0, 1, 'C');
                QRcode::png($empresa['ruc'], 'assets/qr.png');
                $pdf->Image('assets/qr.png', 95, 18, 25, 25);
                $pdf->Image('assets/img/logo.png', 170, 10, 25, 25);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                $pdf->Cell(20, 5, utf8_decode($empresa['direccion']), 0, 1, 'L');
                $pdf->Ln(10);
                //Encabezado de productos
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 5, 'Detalle de Productos', 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetFillColor(0, 100, 50);
                $pdf->SetTextColor(255, 255, 255);
				$pdf->Cell(15, 5, utf8_decode('N°'), 1, 0, 'L', true);
                $pdf->Cell(100, 5, utf8_decode('Descripción'), 1, 0, 'L', true);
                $pdf->Cell(25, 5, 'Fecha', 1, 0, 'L', true);
                $pdf->Cell(25, 5, 'Entradas.', 1, 0, 'L', true);
                $pdf->Cell(25, 5, 'Salidas', 1, 1, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
				$i = 1;
                foreach ($productos as $row) {
                    $pdf->Cell(15, 5, $i, 1, 0, 'L');
					$pdf->Cell(100, 5, utf8_decode($row['descripcion']), 1, 0, 'L');
                    $pdf->Cell(25, 5, $row['fecha'], 1, 0, 'L');
                    $pdf->Cell(25, 5, $row['total_entradas'], 1, 0, 'R');
                    $pdf->Cell(25, 5, $row['total_salidas'], 1, 1, 'R');
					$i++;
                }
                $pdf->Output();
            }
        } else {
            header('Location: Administracion/permisos'); 
        }
    }
    public function pdfCompra($accion)
    {
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Reporte_pdf_compras");
        if (!empty($perm) || $id_user == 1) {
            $empresa = $this->model->getEmpresa();
            if ($accion == 'all') {
                $productos = $this->model->getCompras();
            } else {
                $array = explode(',', $accion);
                $desde = $array[0];
                $hasta = $array[1];
                $productos = $this->model->filtroCompras($desde, $hasta);
            }
            if (empty($productos)) {
                echo 'No hay registro';
            } else {
                require('Libraries/fpdf/fpdf.php');
                include('Libraries/phpqrcode/qrlib.php');
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();
                $pdf->SetMargins(5, 0, 0);
                $pdf->SetTitle('Reporte Compras');
                $pdf->SetFont('Arial', '', 14);
                $pdf->Cell(195, 8, utf8_decode($empresa['nombre']), 0, 1, 'C');
                QRcode::png($empresa['ruc'], 'assets/qr.png');
                $pdf->Image('assets/qr.png', 95, 18, 25, 25);
                $pdf->Image('assets/img/logo.png', 170, 10, 25, 25);
                $pdf->SetFont('Arial', '', 9);

                $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                $pdf->Cell(20, 5, utf8_decode($empresa['direccion']), 0, 1, 'L');
                $pdf->Ln(10);
                //Encabezado de productos
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(195, 5, 'Detalle de Compras', 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetFillColor(0, 100, 50);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 5, utf8_decode('N°'), 1, 0, 'L', true);
                $pdf->Cell(50, 5, 'Total', 1, 0, 'L', true);
                $pdf->Cell(40, 5, 'Fecha', 1, 0, 'L', true);
                $pdf->Cell(35, 5, 'Hora', 1, 0, 'L', true);
                $pdf->Cell(50, 5, 'Usuario', 1, 1, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                foreach ($productos as $row) {
                    $pdf->Cell(20, 5, $row['id'], 1, 0, 'L');
                    $pdf->Cell(50, 5, $row['total'], 1, 0, 'L');
                    $pdf->Cell(40, 5, $row['fecha'], 1, 0, 'L');
                    $pdf->Cell(35, 5, $row['hora'], 1, 0, 'L');
                    $pdf->Cell(50, 5, utf8_decode($row['nombre']), 1, 1, 'L');
                }
                $pdf->Output();
            }
        } else {
            header('Location: Administracion/permisos');
        }
    }
    public function pdfVenta($accion)
    {
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Reporte_pdf_ventas");
        if (!empty($perm) || $id_user == 1) {
            $empresa = $this->model->getEmpresa();
            if ($accion == 'all') {
                $productos = $this->model->getVentas();
            } else {
                $array = explode(',', $accion);
                $desde = $array[0];
                $hasta = $array[1];
                $productos = $this->model->filtroVentas($desde, $hasta);
            }
            if (empty($productos)) {
                echo 'No hay registro';
            } else {
                require('Libraries/fpdf/fpdf.php');
                include('Libraries/phpqrcode/qrlib.php');
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();
                $pdf->SetMargins(5, 0, 0);
                $pdf->SetTitle('Reporte Ventas');
                $pdf->SetFont('Arial', '', 14);
                $pdf->Cell(195, 8, utf8_decode($empresa['nombre']), 0, 1, 'C');
                QRcode::png($empresa['ruc'], 'assets/qr.png');
                $pdf->Image('assets/qr.png', 95, 18, 25, 25);
                $pdf->Image('assets/img/logo.png', 170, 10, 25, 25);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                $pdf->Cell(20, 5, utf8_decode($empresa['direccion']), 0, 1, 'L');
                $pdf->Ln(10);
                //Encabezado de productos
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(195, 5, 'Detalle de Ventas', 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetFillColor(0, 100, 50);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(12, 5, utf8_decode('N°'), 1, 0, 'L', true);
                $pdf->Cell(53, 5, 'Cliente', 1, 0, 'L', true);
                $pdf->Cell(30, 5, 'Total', 1, 0, 'L', true);
                $pdf->Cell(25, 5, 'Fecha', 1, 0, 'L', true);
                $pdf->Cell(25, 5, 'Hora', 1, 0, 'L', true);
                $pdf->Cell(50, 5, 'Usuario', 1, 1, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                foreach ($productos as $row) {
                    $pdf->Cell(12, 5, $row['id'], 1, 0, 'L');
                    $pdf->Cell(53, 5, utf8_decode($row['cliente']), 1, 0, 'L');
                    $pdf->Cell(30, 5, $row['total'], 1, 0, 'L');
                    $pdf->Cell(25, 5, $row['fecha'], 1, 0, 'L');
                    $pdf->Cell(25, 5, $row['hora'], 1, 0, 'L');
                    $pdf->Cell(50, 5, utf8_decode($row['nombre']), 1, 1, 'L');
                }
                $pdf->Output();
            }
        } else {
            header('Location: Administracion/permisos');
        }
    }
    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_producto");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['productos'] = $this->model->getProductos(0);
        $this->views->getView('productos',  "inactivos", $data);
    }
}
