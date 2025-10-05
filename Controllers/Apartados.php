<?php

use Luecano\NumeroALetras\NumeroALetras;

class Apartados extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $data['modal'] = 'apartado';
        $this->views->getView('apartados', "index", $data);
    }
    public function agregar($id_producto)
    {
        $id = strClean($id_producto);
        $datos = $this->model->getProducto($id);
        $id_usuario = $_SESSION['id_usuario'];
        $precio = $datos['precio_venta'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle($id, $id_usuario);
        $cantidad_dis = $datos['cantidad'];
        if (empty($comprobar)) {
            if ($cantidad_dis < $cantidad) {
                $msg = array('msg' => 'No hay Stock, te quedan ' . $cantidad_dis, 'icono' => 'warning');
            } else {
                $sub_total = $precio * $cantidad;
                $data = $this->model->registrarDetalle($id, $id_usuario, $precio, $cantidad, $sub_total);
                if ($data == "ok") {
                    $msg = array('msg' => 'Producto agregado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al agregar', 'icono' => 'error');
                }
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + $cantidad;
            $sub_total = $total_cantidad * $precio;
            $stock_disponible = $cantidad_dis - $comprobar['cantidad'];
            if ($cantidad_dis < $total_cantidad) {
                $msg = array('msg' => 'No hay Stock, te quedan ' . $stock_disponible, 'icono' => 'warning');
            } else {
                $data = $this->model->actualizarDetalle('temp_apartados', $precio, $total_cantidad, $sub_total, $id_producto, $id_usuario);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Producto actualizado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al actualizar', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    //agregar cantidades
    public function cantidadApartado()
    {
        if (isset($_POST['id']) && isset($_POST['cantidad'])) {
            $id = strClean($_POST['id']);
            $cantidad = strClean($_POST['cantidad']);
            $temp = $this->model->detalle($id, 'temp_apartados');
            $producto = $this->model->getProducto($temp['id_producto']);
            if ($producto['cantidad'] >= $cantidad) {
                $data = $this->model->actualizarCantidad('temp_apartados', $cantidad, $id);
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
    public function delete($id)
    {
        $data = $this->model->deleteDetalle('temp_apartados', $id);
        if ($data == 'ok') {
            $msg = array('msg' => 'Producto eliminado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'success');
        }
        echo json_encode($msg);
        die();
    }
    public function ingresarApartado()
    {
        $id = $_POST['id'];
        $datos = $this->model->getProductos($id);
        $id_producto = $datos['id'];
        $id_usuario = $_SESSION['id_usuario'];
        $precio = $datos['precio_venta'];
        $cantidad = $_POST['cantidad'];
        $comprobar = $this->model->consultarDetalle($id_producto, $id_usuario);
        if (empty($comprobar)) {
            if ($datos['cantidad'] >= $cantidad) {
                $data = $this->model->registrarDetalle($id_producto, $id_usuario, $precio, $cantidad);
                if ($data == "ok") {
                    $msg = array('msg' => 'Producto Ingresado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al Ingresar el Producto', 'icono' => 'error');
                }
            } else {
                $msg = array('msg' => 'Stock no disponible: ' . $datos['cantidad'], 'icono' => 'warning');
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + $cantidad;
            $sub_total = $total_cantidad * $precio;
            if ($datos['cantidad'] < $total_cantidad) {
                $msg = array('msg' => 'Stock no disponible', 'icono' => 'warning');
            } else {
                $data = $this->model->actualizarDetalle('detalle_temp', $precio, $total_cantidad, $sub_total, $id_producto, $id_usuario,);
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
    public function listar()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $data['detalle'] = $this->model->getDetalle($id_usuario);
        $total = 0.00;
        for ($i = 0; $i < count($data['detalle']); $i++) {
            $precio = $data['detalle'][$i]['precio'];
            $cantidad = $data['detalle'][$i]['cantidad'];
            $data['detalle'][$i]['subTotal'] = number_format($precio * $cantidad, 2);
            $total = $total + ($precio * $cantidad);
        }
        $data['total_pagar'] = number_format($total, 2, '.', ',');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        $f_retiro = $_POST['start'];
        $fecha_actual = date('Y-m-d');
        if ($f_retiro >= $fecha_actual) {
            $id_usuario = $_SESSION['id_usuario'];
            $id_cliente = $_POST['id'];
            $abono = $_POST['abono'];
            $hora = $_POST['hora'];
            $fecha_retiro = $f_retiro . ' ' . $hora;
            if (empty($id_cliente) || empty($abono) || empty($hora)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                $detalle = $this->model->getDetalle($id_usuario);
                $total = 0.00;
                for ($i = 0; $i < count($detalle); $i++) {
                    $precio = $detalle[$i]['precio'];
                    $cantidad = $detalle[$i]['cantidad'];
                    $total = $total + ($precio * $cantidad);
                }
                $data = $this->model->registrarApartado($fecha_retiro, $abono, $total, $id_cliente);
                if ($data > 0) {
                    foreach ($detalle as $row) {
                        $this->model->registrarDetalleApartado($row['cantidad'], $row['precio'], $row['id_producto'], $data);
                        $stock_actual = $this->model->getProducto($row['id_producto']);
                        $stock = $stock_actual['cantidad'] - $row['cantidad'];
                        $this->model->actualizarStock($stock, $row['id_producto']);
                    }
                    $this->model->vaciarDetalle($id_usuario);
                    $msg = array('msg' => 'Productos Apartado', 'icono' => 'success', 'id_apartado' => $data);
                } else {
                    $msg = array('msg' => 'Error al Apartar los productos', 'icono' => 'error');
                }
            }
        } else {
            $msg = array('msg' => 'Seleccione una fecha actual', 'icono' => 'warning');
        }


        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function listarApartados()
    {
        $data = $this->model->getApartados();
        for ($i=0; $i < count($data); $i++) {
            $restante = number_format($data[$i]['total'] - $data[$i]['abono'], 2);
            $data[$i]['restante'] = '<span class="badge badge-danger">'.$restante.'</span>';
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-warning">Apartado</span>';
            }else{
                $data[$i]['estado'] = '<span class="badge badge-success">Entregado</span>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function verficar($id_apartado)
    {
        $data = $this->model->getVerificar($id_apartado);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function entrega($id_apartado)
    {
        $data = $this->model->actualizarApartado($id_apartado);
        if ($data == 1) {
            $mensaje = array('msg' => 'Productos Entregado', 'icono' => 'success');
        } else {
            $mensaje = array('msg' => 'Error en la Entrega', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function generarPdf($id_apartado)
    {
        if (is_numeric($id_apartado)) {
            $id_user = $_SESSION['id_usuario'];
            $perm = $this->model->verificarPermisos($id_user, "reporte_apartados");
            if (!empty($perm) || $id_user == 1) {
                $empresa = $this->model->getEmpresa();
                $productos = $this->model->getDetalleApartado($id_apartado);
                if (empty($productos)) {
                    echo 'No hay registros';
                    exit;
                } else {
                    $clientes = $this->model->getCliente($id_apartado);
                    require('Libraries/fpdf/html2pdf.php');
                    $pdf = new PDF_HTML('P', 'mm', array(80, 200));
                    $pdf->AddPage();
                    $pdf->SetMargins(5, 0, 5);
                    $pdf->SetTitle('Reporte Venta');
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->MultiCell(50, 10, utf8_decode($empresa['nombre']), 0, 'L');
                    $pdf->Image('assets/img/logo.png', 63, 5, 15, 15);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(18, 5, 'Ruc: ', 0, 0, 'L');
                    $pdf->Cell(40, 5, $empresa['ruc'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                    $pdf->Cell(40, 5, $empresa['telefono'], 0, 1, 'L');
                    $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                    $pdf->MultiCell(53, 5, utf8_decode($empresa['direccion']), 0, 'L');
                    $pdf->Cell(13, 5, 'Fecha: ', 0, 0, 'L');
                    $pdf->Cell(20, 5, $clientes['fecha_apartado'], 0, 1, 'L');
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(72, 5, '-------------------------------------------------------------', 0, 1, 'C');
                    //Encabezado de Clientes
                    
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
                    $total = $clientes['total'];
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
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(20, 5, 'Fecha Retiro: ', 0, 0, 'L');
                    $pdf->Cell(52, 5, $clientes['fecha_retiro'], 0, 1, 'L');

                    $pdf->SetFont('Arial', '', 8);
                    $pdf->WriteHTML(utf8_decode($empresa['mensaje']));
                    $pdf->Output();
                }
            } else {
                header('Location: ' . BASE_URL . 'administracion/permisos');
            }
        } else {
            header('Location: ' . BASE_URL . 'Errors');
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(18, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 5, $empresa['telefono'], 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(18, 5, utf8_decode('Dirección: '), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 5, utf8_decode($empresa['direccion']), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(18, 5, 'Folio: ', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 5, $id_apartado, 0, 1, 'L');
        $pdf->Ln();
        //Encabezado de productos
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 5, 'Cant', 0, 0, 'L', true);
        $pdf->Cell(35, 5, utf8_decode('Descripción'), 0, 0, 'L', true);
        $pdf->Cell(10, 5, 'Precio', 0, 0, 'L', true);
        $pdf->Cell(15, 5, 'Sub Total', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $total = 0.00;
        foreach ($productos as $row) {
            $sub_total = $row['cantidad'] * $row['precio'];
            $total = $total + $sub_total;
            $pdf->Cell(10, 5, $row['cantidad'], 0, 0, 'L');
            $pdf->Cell(35, 5, utf8_decode($row['descripcion']), 0, 0, 'L');
            $pdf->Cell(10, 5, $row['precio'], 0, 0, 'R');
            $pdf->Cell(15, 5, number_format($sub_total, 2, '.', ','), 0, 1, 'R');
        }
        $pdf->Ln();
        $pdf->Cell(70, 5, 'Total a pagar', 0, 1, 'R');
        $pdf->Cell(70, 5, number_format($total, 2, '.', ','), 0, 1, 'R');
        $pdf->Output();
    }
    //historial
    public function historial()
    {
        $this->views->getView('apartados', "historial");
    }
}
