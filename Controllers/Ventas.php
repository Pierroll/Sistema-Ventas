<?php

use Luecano\NumeroALetras\NumeroALetras;

class Ventas extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        parent::__construct();
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        
        $perm = $this->model->verificarPermisos($this->id_usuario, "nueva_venta");
        if (!empty($perm) || $this->id_usuario == 1) {
            $data = $this->model->getEmpresa();
            $this->views->getView('ventas',  "index", $data);
        } else {
            header('Location: ' . BASE_URL . 'administracion/permisos');
        }
    }
    public function agregarVenta($id_producto)
    {
        $id = strClean($id_producto);
        $datos = $this->model->getProductos($id);
        $id_usuario = $_SESSION['id_usuario'];
        $precio = $datos['precio_venta'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle('detalle_temp', $id, $id_usuario);
        $cantidad_dis = $datos['cantidad'];
        if (empty($comprobar)) {
            if ($cantidad_dis < $cantidad) {
                $msg = array('msg' => 'No hay Stock, te quedan ' . $cantidad_dis, 'icono' => 'warning');
            } else {
                $data = $this->model->registrarDetalle('detalle_temp', $id, $id_usuario, $precio, $cantidad);
                if ($data == "ok") {
                    $msg = array('msg' => 'Producto ingresado a la venta', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al ingresar el producto a la venta', 'icono' => 'error');
                }
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + $cantidad;
            $stock_disponible = $cantidad_dis - $comprobar['cantidad'];
            if ($cantidad_dis < $total_cantidad) {
                $msg = array('msg' => 'No hay Stock, te quedan ' . $stock_disponible, 'icono' => 'warning');
            } else {
                $data = $this->model->actualizarDetalle('detalle_temp', $precio, $total_cantidad, $id_producto, $id_usuario);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Producto actualizado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al actualizar el producto', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    //agregar cantidades
    public function cantidadVenta()
    {
        if (isset($_POST['id']) && isset($_POST['cantidad'])) {
            $id = strClean($_POST['id']);
            $cantidad = strClean($_POST['cantidad']);
            $temp = $this->model->detalle($id, 'detalle_temp');
            $producto = $this->model->getProductos($temp['id_producto']);
            if ($producto['cantidad'] >= $cantidad) {
                $data = $this->model->actualizarCantidad('detalle_temp', $cantidad, $id);
                if ($data == 'ok') {
                    $msg = array('msg' => 'ok', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'error al agregar', 'icono' => 'warning');
                }
            } else {
                $msg = array('msg' => 'No hay Stock, te quedan ' . $producto['cantidad'], 'icono' => 'warning');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function deleteVenta($id)
    {
        $data = $this->model->deleteDetalle('detalle_temp', $id);
        if ($data == 'ok') {
            $msg = array('msg' => 'Producto eliminado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'success');
        }
        echo json_encode($msg);
        die();
    }
    public function registrarVenta()
    {
        if (isset($_POST['id']) && isset($_POST['metodo'])) {
            $id_cliente = (!empty($_POST['id'])) ? strClean($_POST['id']) : 1;
            $verificar = $this->model->verificarCaja($this->id_usuario);
            if (empty($verificar)) {
                $msg = array('msg' => 'La caja esta cerrada', 'icono' => 'warning');
            } else {
                $fecha = date('Y-m-d');
                $hora = date('H:i:s');
                $serie = 1;
                $metodo = $_POST['metodo'];
                $detalle = $this->calcularTotal();
                $data = $this->model->registraVenta($this->id_usuario, $id_cliente, $detalle['total'], $fecha, $hora, $serie, $metodo);
                if ($data > 0) {
                    if ($metodo == 2) {
                        $this->model->registraCredito($detalle['total'], $data);
                    }
                    foreach ($detalle['detalle'] as $row) {
                        $cantidad = $row['cantidad'];
                        $precio = $row['precio'];
                        $id_pro = $row['id_producto'];
                        $this->model->registrarDetalleVenta($data, $id_pro, $cantidad, $precio, $fecha);
                        $stock_actual = $this->model->getProductos($id_pro);
                        $stock = $stock_actual['cantidad'] - $cantidad;
                        $this->model->actualizarStock($stock, $id_pro);
                        $this->model->ingresarSalida($id_pro, $this->id_usuario, $cantidad, $fecha);
                    }
                    $vaciar = $this->model->vaciarDetalle('detalle_temp', $this->id_usuario);
                    if ($vaciar == 'ok') {
                        $msg = array('msg' => 'Venta Generada', 'id' => $data, 'icono' => 'success');
                    }
                } else {
                    $msg = array('msg' => 'Error al realizar la venta', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg);
        die();
    }
    public function historial()
    {
        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "reporte_pdf_ventas");
        if (!empty($data['permisos']) || $this->id_usuario == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $this->views->getView('ventas',  "historial", $data);
    }
    public function listar_historial()
    {
        $data = $this->model->getHistorialVentas(1);
        $id_user = $_SESSION['id_usuario'];
        $anular = $this->model->verificarPermisos($id_user, "anular_venta");
        $reporte = $this->model->verificarPermisos($id_user, "reporte_ventas");
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['anular'] = '';
            $data[$i]['reporte'] = '';
            if (!empty($anular) || $id_user == 1) {
                $data[$i]['anular'] = '<button class="btn btn-outline-warning" type="button" onclick="btnAnularVenta(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>';
            }
            if (!empty($reporte) || $id_user == 1) {
                $data[$i]['reporte'] = '<a class="btn btn-outline-danger" href="#" onclick="generarReportes(' . 2 . ',' . $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>';
            }
            if ($data[$i]['metodo'] == 2) {
                $data[$i]['metodo'] = '<span class="badge bg-warning">Credito</span>';
            }else{
                $data[$i]['metodo'] = '<span class="badge bg-info">Contado</span>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function generarPdf($id_venta)
    {
        if (is_numeric($id_venta)) {
            $perm = $this->model->verificarPermisos($this->id_usuario, "reporte_ventas");
            if (!empty($perm) || $this->id_usuario == 1) {
                $empresa = $this->model->getEmpresa();
                $productos = $this->model->getProVenta($id_venta);
                if (empty($productos)) {
                    echo 'No hay registros';
                    exit;
                } else {
                    $fecha = $this->model->getFecha('ventas', $id_venta);
                    require('Libraries/fpdf/html2pdf.php');
                    $pdf = new PDF_HTML('P', 'mm', array(80, 200));
                    $pdf->AddPage();
                    $pdf->SetMargins(5, 0, 5);
                    $pdf->SetTitle('Reporte Venta');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Cell(65, 10, utf8_decode($empresa['nombre']), 0, 1, 'L');
                    $pdf->Image('assets/img/logo.png', 63, 5, 15, 15);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                    $pdf->Cell(40, 5, $empresa['ruc'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->Cell(40, 5, $empresa['telefono'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                    $pdf->MultiCell(53, 5, utf8_decode($empresa['direccion']), 0, 'L');
                    $pdf->Cell(13, 5, 'Fecha: ', 0, 0, 'L');
                    $pdf->Cell(20, 5, $fecha['fecha'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
                    //Encabezado de Clientes
                    $clientes = $this->model->clientesVenta($id_venta);
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, 'Dni: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($clientes['dni']), 0, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, 'Nombre: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($clientes['nombre']), 0, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(45, 5, $clientes['telefono'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, utf8_decode('Dirección:'), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($clientes['direccion']), 0, 'L');

                    //Encabezado de productos
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(9, 5, 'Cant', 0, 0, 'L');
                    $pdf->Cell(38, 5, utf8_decode('Descripción'), 0, 0, 'L');
                    $pdf->Cell(12, 5, 'Precio', 0, 0, 'L');
                    $pdf->Cell(12, 5, 'Sub Total', 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
                    $total = $fecha['total'];
                    $pdf->SetFont('Arial', '', 7);
                    foreach ($productos as $row) {
                        $pdf->Cell(9, 5, $row['cantidad'], 0, 0, 'L');
                        $x = $pdf->GetX();
                        $pdf->myCell(38, 5, $x, utf8_decode($row['descripcion']));
                        $pdf->Cell(12, 5, number_format($row['precio'], 2), 0, 0, 'R');
                        $pdf->Cell(12, 5, number_format($row['precio'] * $row['cantidad'], 2, '.', ','), 0, 1, 'R');
                        $pdf->Cell(72, 5, '____________________________________________________', 0, 1, 'C');
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(35, 5, 'Total a pagar', 0, 0, 'R');
                    $pdf->Cell(36, 5, $empresa['simbolo'] . ' ' . number_format($total, 2, '.', ','), 0, 1, 'R');
                    $pdf->Ln();
                    require 'vendor/autoload.php';
                    $formatter = new NumeroALetras();
                    $pdf->MultiCell(72, 5, $formatter->toMoney(utf8_decode($total), 2, '', ''), 0, 'R');

                    $pdf->WriteHTML(utf8_decode($empresa['mensaje']));
                    $pdf->Output();
                }
            } else {
                header('Location: ' . BASE_URL . 'administracion/permisos');
            }
        } else {
            header('Location: ' . BASE_URL . 'Errors');
        }
    }
    public function generarFactura($id_venta)
    {
        if (is_numeric($id_venta)) {
            $perm = $this->model->verificarPermisos($this->id_usuario, "reporte_ventas");
            if (!empty($perm) || $this->id_usuario == 1) {
                $empresa = $this->model->getEmpresa();
                $productos = $this->model->getProVenta($id_venta);
                if (empty($productos)) {
                    echo 'No hay registros';
                    exit;
                } else {
                    $fecha = $this->model->getFecha('ventas', $id_venta);
                    require('Libraries/fpdf/html2pdf.php');
                    include('Libraries/phpqrcode/qrlib.php');
                    $pdf = new PDF_HTML('P', 'mm', 'A4');
                    $pdf->AddPage();
                    $pdf->SetMargins(10, 0, 0);
                    $pdf->SetTitle('Factura');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Cell(195, 10, utf8_decode($empresa['nombre']), 0, 1, 'C');
                    QRcode::png($empresa['ruc'], 'assets/qr.png');
                    $pdf->Image('assets/qr.png', 95, 18, 25, 25);
                    $pdf->Image('assets/img/logo.png', 165, 16, 25, 25);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(20, 5, 'Ruc: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(20, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->MultiCell(60, 5, utf8_decode($empresa['direccion']), 0, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(25, 5, 'Fecha y Hora: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(35, 5, $fecha['fecha'] . ' - ' . $fecha['hora'], 0, 1, 'R');
                    $pdf->Ln();
                    //Encabezado de Clientes
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(190, 8, 'Datos del Cliente', 1, 1, 'C', true);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(80, 8, 'Nombre', 1, 0, 'C');
                    $pdf->Cell(50, 8, utf8_decode('Teléfono'), 1, 0, 'C');
                    $pdf->Cell(60, 8, utf8_decode('Dirección'), 1, 1, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                    $clientes = $this->model->clientesVenta($id_venta);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(80, 5, utf8_decode($clientes['nombre']), 1, 0, 'C');
                    $pdf->Cell(50, 5, $clientes['telefono'], 1, 0, 'C');
                    $pdf->Cell(60, 5, utf8_decode($clientes['direccion']), 1, 1, 'C');
                    $pdf->Ln();
                    //Encabezado de productos
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(190, 8, 'Detalle de Productos', 1, 1, 'C', true);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(20, 8, 'Cant', 1, 0, 'L');
                    $pdf->Cell(110, 8, utf8_decode('Descripción'), 1, 0, 'L');
                    $pdf->Cell(30, 8, 'Precio', 1, 0, 'L');
                    $pdf->Cell(30, 8, 'Sub Total', 1, 1, 'L');
                    $total = $fecha['total'];
                    $pdf->SetFont('Arial', '', 9);
                    foreach ($productos as $row) {
                        $pdf->Cell(20, 8, $row['cantidad'], 0, 0, 'L');
                        $pdf->Cell(110, 8, utf8_decode($row['descripcion']), 0, 0, 'L');
                        $pdf->Cell(30, 8, number_format($row['precio'], 2, '.', ','), 0, 0, 'L');
                        $pdf->Cell(30, 8, number_format($row['precio'] * $row['cantidad'], 2, '.', ','), 0, 1, 'L');
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial', 'B', 10);
                    $igv = $empresa['impuesto'] / 100;
                    $base = $total / (1 + $igv);
                    $impuesto = $total - $base;
                    $pdf->Cell(190, 8, 'Sub Total: ' . $empresa['simbolo'] . '   ' . number_format($base, 2, '.', ','), 0, 1, 'R');
                    $pdf->Cell(190, 8, 'Impuesto ' . $empresa['impuesto'] . '%: ' . $empresa['simbolo'] . '   ' . number_format($impuesto, 2, '.', ','), 0, 1, 'R');
                    $pdf->Cell(190, 8, 'Total a pagar: ' . $empresa['simbolo'] . '   ' . number_format($total, 2, '.', ','), 0, 1, 'R');
                    $pdf->Ln();
                    require 'vendor/autoload.php';
                    $formatter = new NumeroALetras();
                    $pdf->MultiCell(190, 5, $formatter->toMoney($total, 2, '', ''), 0, 'R');
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->WriteHTML(utf8_decode($empresa['mensaje']));
                    $pdf->Output();
                }
            } else {
                header('Location: ' . BASE_URL . 'administracion/permisos');
            }
        } else {
            header('Location: ' . BASE_URL . 'Errors');
        }
    }
    public function anularVenta($id)
    {
        if (isset($_GET)) {
            $existe = $this->model->getAnularVentas($id);
            if (!empty($existe)) {
                foreach ($existe as $row) {
                    $stock = $this->model->getProductos($row['id_producto']);
                    $cantidad = $stock['cantidad'] + $row['cantidad'];
                    $this->model->actualizarStock($cantidad, $stock['id']);
                }
                $data = $this->model->anular('ventas', $id);
                if ($data == 'ok') {
                    $msg = array('msg' => 'Venta anulada', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al anular la venta', 'icono' => 'error');
                }
            } else {
                $msg = array('msg' => 'Error al anular la venta', 'icono' => 'error');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function inactivos()
    {
        $data['ventas'] = $this->model->getHistorialVentas(0);
        $this->views->getView('ventas',  "inactivos", $data);
    }
    function generate_numbers($start, $count, $digits)
    {
        $result = array();
        for ($n = $start; $n < $start + $count; $n++) {
            $result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }
        return $result;
    }
    public function calcularTotal()
    {
        $data['total'] = 0.00;
        $detalle = $this->model->getDetalle('detalle_temp', $this->id_usuario);
        for ($i=0; $i < count($detalle); $i++) { 
            $data['total'] += $detalle[$i]['precio'] * $detalle[$i]['cantidad'];
        }
        $data['detalle'] = $detalle;
        return $data;
    }
}
