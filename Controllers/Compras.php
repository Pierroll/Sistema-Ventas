<?php

use Luecano\NumeroALetras\NumeroALetras;

class Compras extends Controller
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
        
        $perm = $this->model->verificarPermisos($this->id_usuario, "nueva_compra");
        if (!empty($perm) || $this->id_usuario == 1) {
            $data = $this->model->getEmpresa();
            $data['mini'] = true;
            $this->views->getView('compras',   "index", $data);
        } else {
            header('Location: ' . BASE_URL . 'administracion/permisos');
        }
    }
    public function buscarProducto()
    {
        $data = $this->model->buscarProducto($_GET['pro']);
        $datos = array();
        foreach ($data as $row) {
            $data['id'] = $row['id'];
            $data['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
            $data['value'] = $row['codigo'];
            $data['descripcion'] = $row['descripcion'];
            $data['cantidad'] = $row['cantidad'];
            array_push($datos, $data);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function agregarCompra($id_producto)
    {
        $id = strClean($id_producto);
        $datos = $this->model->getProducto($id);
        $id_usuario = $_SESSION['id_usuario'];
        $precio = $datos['precio_compra'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle('detalle', $id, $id_usuario);
        if (empty($comprobar)) {
            $data = $this->model->registrarDetalle('detalle', $id, $id_usuario, $precio, $cantidad);
            if ($data == "ok") {
                $msg = array('msg' => 'Producto ingresado a la compra', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al ingresar el producto a la compra', 'icono' => 'error');
            }
        } else {
            $total_cant = $comprobar['cantidad'] + $cantidad;
            $data = $this->model->actualizarDetalle('detalle', $precio, $total_cant, $id_producto, $id_usuario);
            if ($data == "modificado") {
                $msg = array('msg' => 'Producto actualizado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al actualizar el producto', 'icono' => 'error');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    //agregar cantidades
    public function cantidadCompra()
    {
        if (isset($_POST['id']) && isset($_POST['cantidad'])) {
            $id = strClean($_POST['id']);
            $cantidad = strClean($_POST['cantidad']);
            $data = $this->model->actualizarCantidad('detalle', $cantidad, $id);
            if ($data == 'ok') {
                $msg = array('msg' => 'ok', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'error al agregar', 'icono' => 'warning');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function listar($table)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $data['detalle'] = $this->model->getDetalle($table, $id_usuario);
        $total = 0.00;
        for ($i = 0; $i < count($data['detalle']); $i++) {
            $subTotal = $data['detalle'][$i]['precio'] * $data['detalle'][$i]['cantidad'];
            $total += $subTotal;
            $data['detalle'][$i]['precio'] = number_format($data['detalle'][$i]['precio'], 2);
            $data['detalle'][$i]['sub_total'] = number_format($subTotal, 2);
        }
        $data['total_pagar'] = number_format($total, 2);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function delete($id)
    {
        $data = $this->model->deleteDetalle('detalle', $id);
        if ($data == 'ok') {
            $msg = array('msg' => 'Producto eliminado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'success');
        }
        echo json_encode($msg);
        die();
    }
    public function registrarCompra()
    {
        if (isset($_POST['id_pr'])) {
            $id_pr = (!empty($_POST['id_pr'])) ? strClean($_POST['id_pr']) : 1;
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $id_usuario = $_SESSION['id_usuario'];
            $serie = 1;
            $detalle = $this->calcularTotal();
            $data = $this->model->registraCompra($id_usuario, $id_pr, $detalle['total'], $fecha, $hora, $serie);
            if ($data > 0) {
                foreach ($detalle['detalle'] as $row) {
                    $cantidad = $row['cantidad'];
                    $precio = $row['precio'];
                    $id_pro = $row['id_producto'];
                    $this->model->registrarDetalleCompra($data, $id_pro, $cantidad, $precio);
                    $stock_actual = $this->model->getProducto($id_pro);
                    $stock = $stock_actual['cantidad'] + $cantidad;
                    $this->model->actualizarStock($stock, $id_pro);
                    $this->model->ingresarEntrada($id_pro, $id_usuario, $cantidad, $fecha);
                }
                $vaciar = $this->model->vaciarDetalle('detalle', $id_usuario);
                if ($vaciar == 'ok') {
                    $msg = array('msg' => 'Compra generada', 'id' => $data, 'icono' => 'success');
                }
            } else {
                $msg = array('msg' => 'Error al realizar la compra', 'icono' => 'error');
            }
        }
        echo json_encode($msg);
        die();
    }
    public function generarPdf($id_compra)
    {
        if (is_numeric($id_compra)) {
            
            $perm = $this->model->verificarPermisos($this->id_usuario, "reporte_compras");
            if (!empty($perm) || $this->id_usuario == 1) {
                $empresa = $this->model->getEmpresa();
                $productos = $this->model->getProCompra($id_compra);
                if (empty($productos)) {
                    header('Location: ' . BASE_URL . 'errors');
                } else {
                    $fecha = $this->model->getFecha('compras',  $id_compra);
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
                    $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                    $pdf->MultiCell(55, 5, utf8_decode($empresa['direccion']), 0, 'L');
                    $pdf->Cell(13, 5, 'Fecha: ', 0, 0, 'L');
                    $pdf->Cell(20, 5, $fecha['fecha'], 0, 1, 'L');
                    $pdf->Ln();

                    //Encabezado de Clientes
                    $proveedor = $this->model->proveedor($id_compra);
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, 'Dni: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($proveedor['ruc']), 0, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, 'Nombre: ', 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($proveedor['nombre']), 0, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(45, 5, $proveedor['telefono'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, utf8_decode('Dirección:'), 0, 0, 'L');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(40, 5, utf8_decode($proveedor['direccion']), 0, 'L');

                    //Encabezado de productos
                    $pdf->Cell(80, 5, 'Detalle de Productos', 0, 1, 'C');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(9, 5, 'Cant', 0, 0, 'L');
                    $pdf->Cell(38, 5, utf8_decode('Descripción'), 0, 0, 'L');
                    $pdf->Cell(13, 5, 'Precio', 0, 0, 'L');
                    $pdf->Cell(15, 5, 'Sub Total', 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
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
                    $pdf->Cell(72, 5, 'Total a pagar', 0, 1, 'R');
                    $pdf->Cell(72, 5, number_format($fecha['total'], 2, '.', ','), 0, 1, 'R');
                    $pdf->Ln();
                    require 'vendor/autoload.php';
                    $formatter = new NumeroALetras();
                    $pdf->MultiCell(72, 5, $formatter->toMoney(utf8_decode($fecha['total']), 2, '', ''), 0, 'R');
                    $pdf->WriteHTML(utf8_decode($empresa['mensaje']));

                    $pdf->Output();
                }
            } else {
                header('Location: ' . BASE_URL . 'administracion/permisos');
            }
        }
    }
    public function generarFactura($id_compra)
    {
        if (is_numeric($id_compra)) {
            
            $perm = $this->model->verificarPermisos($this->id_usuario, "reporte_compras");
            if (!empty($perm) || $this->id_usuario == 1) {
                $empresa = $this->model->getEmpresa();
                $productos = $this->model->getProCompra($id_compra);
                if (empty($productos)) {
                    echo 'No hay registros';
                    exit;
                } else {
                    $fecha = $this->model->getFecha('compras',  $id_compra);
                    require('Libraries/fpdf/html2pdf.php');
                    include('Libraries/phpqrcode/qrlib.php');
                    $pdf = new PDF_HTML('P', 'mm', 'A4');
                    $pdf->AddPage();
                    $pdf->SetMargins(15, 0, 0);
                    $pdf->SetTitle('Factura');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Cell(195, 10, utf8_decode($empresa['nombre']), 0, 1, 'C');
                    QRcode::png($empresa['ruc'], 'assets/qr.png');
                    $pdf->Image('assets/qr.png', 95, 18, 25, 25);
                    $pdf->Image('assets/img/logo.png', 165, 16, 20, 20);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                    $pdf->Cell(20, 5, $empresa['ruc'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                    $pdf->MultiCell(60, 5, utf8_decode($empresa['direccion']), 0, 'L');
                    $pdf->Cell(25, 5, 'Fecha y Hora: ', 0, 0, 'L');
                    $pdf->Cell(35, 5, $fecha['fecha'] . ' - ' . $fecha['hora'], 0, 1, 'R');
                    $pdf->Ln();
                    //Encabezado de Proveedor
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(185, 8, 'Datos del Proveedor', 1, 1, 'C', true);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(80, 5, 'Nombre', 1, 0, 'L');
                    $pdf->Cell(45, 5, utf8_decode('Teléfono'), 1, 0, 'L');
                    $pdf->Cell(60, 5, utf8_decode('Dirección'), 1, 1, 'L');
                    $pdf->SetTextColor(0, 0, 0);
                    $proveedor = $this->model->proveedor($id_compra);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(80, 5, utf8_decode($proveedor['nombre']), 0, 0, 'L');
                    $pdf->Cell(45, 5, $proveedor['telefono'], 0, 0, 'L');
                    $pdf->Cell(60, 5, utf8_decode($proveedor['direccion']), 0, 1, 'L');
                    $pdf->Ln();
                    //Encabezado de productos
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(185, 8, 'Detalle de Productos', 1, 1, 'C', true);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(25, 5, 'Cant', 1, 0, 'L');
                    $pdf->Cell(90, 5, utf8_decode('Descripción'), 1, 0, 'L');
                    $pdf->Cell(35, 5, 'Precio', 1, 0, 'L');
                    $pdf->Cell(35, 5, 'Sub Total', 1, 1, 'L');
                    $total = $fecha['total'];
                    foreach ($productos as $row) {
                        $pdf->Cell(25, 5, $row['cantidad'], 0, 0, 'L');
                        $pdf->Cell(90, 5, utf8_decode($row['descripcion']), 0, 0, 'L');
                        $pdf->Cell(35, 5, number_format($row['precio'], 2, '.', ','), 0, 0, 'L');
                        $pdf->Cell(35, 5, number_format($row['precio'] * $row['cantidad'], 2, '.', ','), 0, 1, 'L');
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 10);
                    $igv = $empresa['impuesto'] / 100;
                    $base = $total / (1 + $igv);
                    $impuesto = $total - $base;
                    $pdf->Cell(185, 8, 'Sub Total: ' . $empresa['simbolo'] . '   ' . number_format($base, 2, '.', ','), 0, 1, 'R');
                    $pdf->Cell(185, 8, 'Impuesto ' . $empresa['impuesto'] . '%: ' . $empresa['simbolo'] . '   ' . number_format($impuesto, 2, '.', ','), 0, 1, 'R');
                    $pdf->Cell(185, 8, 'Total a pagar: ' . $empresa['simbolo'] . '   ' . number_format($total, 2, '.', ','), 0, 1, 'R');
                    $pdf->Ln();
                    require 'vendor/autoload.php';
                    $formatter = new NumeroALetras();
                    $pdf->MultiCell(185, 5, $formatter->toMoney(utf8_decode($total), 2, '', ''), 0, 'R');

                    $pdf->WriteHTML(utf8_decode($empresa['mensaje']));
                    $pdf->Output();
                }
            } else {
                header('Location: ' . BASE_URL . 'administracion/permisos');
            }
        } else {
            header('Location: ' . BASE_URL . 'errors');
        }
    }
    public function historial()
    {
        
        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "reporte_pdf_compras");
        if (!empty($data['permisos']) || $this->id_usuario == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $this->views->getView('compras',   "historial", $data);
    }
    public function listar_historial()
    {
        $data = $this->model->getHistorialcompras(1);
        
        $anular = $this->model->verificarPermisos($this->id_usuario, "anular_compra");
        $reporte = $this->model->verificarPermisos($this->id_usuario, "reporte_compras");
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            if (!empty($anular) || $this->id_usuario == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-warning" type="button" onclick="btnAnularC(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>';
            }
            if (!empty($reporte) || $this->id_usuario == 1) {
                $data[$i]['eliminar'] = '<a class="btn btn-outline-danger" href="#" onclick="generarReportes('. 1 . ','. $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function anularC($id)
    {
        if (isset($_GET)) {
            $existe = $this->model->getAnularCompras($id);
            if (!empty($existe)) {
                foreach ($existe as $row) {
                    $stock = $this->model->getProducto($row['id_producto']);
                    $cantidad = $stock['cantidad'] - $row['cantidad'];
                    $this->model->actualizarStock($cantidad, $row['id_producto']);
                }
                $data = $this->model->anular('compras',  $id);
                if ($data == 'ok') {
                    $msg = array('msg' => 'Compra anulada', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al anular la compra', 'icono' => 'error');
                }
            } else {
                $msg = array('msg' => 'Error al anular la compra', 'icono' => 'error');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function anularProceso($table)
    {
        $vaciar = $this->model->vaciarDetalle($table, $_SESSION['id_usuario']);
        if ($vaciar == 'ok') {
            $msg = array('msg' => 'Proceso Anulado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error ala anular', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        $data['compras'] = $this->model->getHistorialCompras(0);
        $this->views->getView('compras',   "inactivos", $data);
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
        $detalle = $this->model->getDetalle('detalle', $this->id_usuario);
        for ($i=0; $i < count($detalle); $i++) { 
            $data['total'] += $detalle[$i]['precio'] * $detalle[$i]['cantidad'];
        }
        $data['detalle'] = $detalle;
        return $data;
    }
}
