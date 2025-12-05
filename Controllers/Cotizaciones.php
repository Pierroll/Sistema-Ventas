<?php

use Luecano\NumeroALetras\NumeroALetras;

class Cotizaciones extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        parent::__construct();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
    }
    public function index()
    {
        $data = $this->model->getEmpresa();
        $this->views->getView('cotizaciones',  "index", $data);
    }

    public function agregarCotizacion($id_producto)
    {
        $id = strClean($id_producto);
        $datos = $this->model->getProductos($id);
        $precio = $datos['precio_venta'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle($id, $this->id_usuario);
        if (empty($comprobar)) {
            $data = $this->model->registrarDetalle($precio, $cantidad, $id, $this->id_usuario);
            if ($data == "ok") {
                $msg = array('msg' => 'Producto agregado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al agregar', 'icono' => 'error');
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + 1;
            $data = $this->model->actualizarDetalle('detalle_temp', $precio, $total_cantidad, $id_producto, $this->id_usuario);
            if ($data == "modificado") {
                $msg = array('msg' => 'Producto actualizado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al actualizar', 'icono' => 'error');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    //agregar cantidades
    public function itemCotizacion()
    {
        if (isset($_POST['id']) && isset($_POST['item'])) {
            $id = strClean($_POST['id']);
            $campo = strClean($_POST['campo']);
            $item = strClean($_POST['item']);
            $data = $this->model->actualizarCantidad($campo, $item, $id);
            if ($data == 'ok') {
                $msg = array('msg' => 'ok', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'error al agregar', 'icono' => 'warning');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function deleteCotizacion($id)
    {
        $data = $this->model->deleteDetalle('temp_cotizaciones', $id);
        if ($data == 'ok') {
            $msg = array('msg' => 'Producto eliminado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'success');
        }
        echo json_encode($msg);
        die();
    }
    public function registrarCotizacion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente = (!empty($_POST['id_cliente'])) ? strClean($_POST['id_cliente']) : 1;
            $comentario = strClean($_POST['comentario']);
            $validez = strClean($_POST['validez']);
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $detalle = $this->calcularTotal();
            $comprobar = $this->model->consultarCotizacion($this->id_usuario);
            $array = array();
            foreach ($comprobar as $producto) {
                $json['id'] = $producto['id_producto'];
                $json['precio'] = $producto['precio'];
                $json['cantidad'] = $producto['cantidad'];
                $json['nombre'] = $producto['descripcion'];
                $json['medida'] = $producto['medida'];
                $json['descuento'] = $producto['descuento'];
                $json['impuesto'] = $producto['impuesto'];
                array_push($array, $json);
            }
            $productosJson = json_encode($array);
            $data = $this->model->registraCotizacion($productosJson, $detalle['total'], $fecha, $hora, $validez, $comentario, $id_cliente, $this->id_usuario);
            if ($data > 0) {
                $vaciar = $this->model->vaciarDetalle('temp_cotizaciones', $this->id_usuario);
                if ($vaciar == 'ok') {
                    $msg = array('msg' => 'Cotizacion Generada', 'id' => $data, 'icono' => 'success');
                }
            } else {
                $msg = array('msg' => 'Error al realizar en la cotizacion', 'icono' => 'error');
            }
        }
        echo json_encode($msg);
        die();
    }
    public function historial()
    {
        $this->views->getView('cotizaciones',  "historial");
    }
    public function listar()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $data['detalle'] = $this->model->getDetalle($id_usuario);
        for ($i = 0; $i < count($data['detalle']); $i++) {
            $data['detalle'][$i]['subtotal'] = ($data['detalle'][$i]['precio'] * $data['detalle'][$i]['cantidad']) - $data['detalle'][$i]['descuento'];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function listar_historial()
    {
        $data = $this->model->getHistorial();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<a class="btn btn-outline-danger" target="_blank" href="'.BASE_URL.'cotizaciones/generarFactura/'.$data[$i]['id'].'"><i class="fas fa-file-pdf"></i></a>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generarFactura($id_cotizacion)
    {
        if (is_numeric($id_cotizacion)) {
            $empresa = $this->model->getEmpresa();
            $cotizacion = $this->model->getCotizacion($id_cotizacion);
            if (empty($cotizacion)) {
                echo 'No hay registros';
                exit;
            } else {
                require('Libraries/fpdf/html2pdf.php');
                $pdf = new PDF_HTML('P', 'mm', 'A4');
                $pdf->AddPage();
                $pdf->SetMargins(10, 0, 0);
                $pdf->SetTitle('Factura');
                $pdf->SetFont('Arial', '', 16);
                $pdf->Cell(15, 10, '', 0, 0, 'L');
                $pdf->Cell(120, 10, utf8_decode($empresa['nombre']), 0, 0, 'L');

                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', '', 14);
                $pdf->Cell(55, 10, utf8_decode('Ruc: ' . $empresa['ruc']), 0, 1, 'C', true);

                $pdf->Image('assets/img/logo.png', 4, 3, 25, 25);
                $pdf->Ln(2);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, 'Empresa: ', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(115, 5, $empresa['nombre'], 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(55, 5, 'COTIZACION: ', 0, 1, 'C');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(115, 5, $empresa['telefono'], 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 5, 'FECHA:', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(30, 5, $cotizacion['fecha'], 0, 1, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, utf8_decode('E-mail: '), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(115, 5, $empresa['correo'], 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 5, utf8_decode('N° COTIZ:'), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(30, 5, $cotizacion['id'], 0, 1, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(115, 5, utf8_decode($empresa['direccion']), 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 5, 'ID CLIENTE:', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(30, 5, $cotizacion['id_cliente'], 0, 1, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, 'Sitio Web', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(115, 5, $empresa['site'], 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 5, 'VALIDO:', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(30, 5, $cotizacion['validez'], 0, 1, 'L');

                $pdf->Ln();
                //Encabezado de Clientes
                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 6, 'Datos del Cliente', 1, 1, 'C', true);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 5, 'Nombre', 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(160, 5, utf8_decode($cotizacion['nombre']), 1, 1, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 5, utf8_decode('Teléfono'), 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(160, 5, utf8_decode($cotizacion['telefono']), 1, 1, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 5, utf8_decode('Dirección'), 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(160, 5, utf8_decode($cotizacion['direccion']), 1, 1, 'L');
                $pdf->Ln();
                //Encabezado de productos
                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 6);
                $pdf->Cell(15, 8, 'COD', 1, 0, 'L', true);
                $pdf->Cell(85, 8, utf8_decode('DESCRIPCION'), 1, 0, 'L', true);
                $pdf->Cell(10, 8, 'MEDIDA', 1, 0, 'L', true);
                $pdf->Cell(10, 8, 'CANT', 1, 0, 'L', true);
                $pdf->Cell(15, 8, 'PRECIO', 1, 0, 'L', true);
                $pdf->Cell(15, 8, 'DESCUENTO', 1, 0, 'L', true);
                $pdf->Cell(15, 8, 'SUBTOTAL', 1, 0, 'L', true);
                $pdf->Cell(10, 8, 'IMP', 1, 0, 'L', true);
                $pdf->Cell(15, 8, 'TOTAL', 1, 1, 'L', true);
                $total = $cotizacion['total'];
                $pdf->SetFont('Arial', '', 6);
                $json = json_decode($cotizacion['productos'], true);
                $pdf->SetTextColor(0, 0, 0);
                $sub = 0.00;
                $totalGeneral = 0.00;
                $descuento = 0.00;
                $impuesto = 0.00;
                $igvTotal = 0.00;
                foreach ($json as $row) {
                    $producto = $this->model->getProductos($row['id']);
                    $subTotal = ($row['precio'] * $row['cantidad']) - $row['descuento'];
                    $pdf->Cell(15, 5, utf8_decode($producto['codigo']), 1, 0, 'L');
                    $pdf->Cell(85, 5, utf8_decode($row['nombre']), 1, 0, 'L');
                    $pdf->Cell(10, 5, $row['medida'], 1, 0, 'L');
                    $pdf->Cell(10, 5, $row['cantidad'], 1, 0, 'L');
                    $pdf->Cell(15, 5, $row['precio'], 1, 0, 'L');
                    $pdf->Cell(15, 5, $row['descuento'], 1, 0, 'L');
                    $pdf->Cell(15, 5, number_format($subTotal, 2), 1, 0, 'L');
                    $pdf->Cell(10, 5, $row['impuesto'] . '%', 1, 0, 'L');

                    $igv = $row['impuesto'];
                    $base = $subTotal *  ($igv / 100);
                    $total = $igv > 0 ? $base + $subTotal : $subTotal;

                    $descuento += $row['descuento'];
                    $impuesto += $row['impuesto'];
                    $totalGeneral += $total;
                    $sub += $subTotal;

                    // 17 + 63

                    $igvTotal = $totalGeneral - $sub;

                    $pdf->Cell(15, 5, number_format($total, 2), 1, 1, 'L');
                }
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                //Encabezado de comentarios
                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(110, 6, 'Comentarios', 1, 0, 'C', true);
                $pdf->Cell(20, 6, '', 0, 0, 'C');

                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(25, 6, 'Descuento', 1, 0, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(35, 6, $empresa['simbolo']. number_format($descuento, 2), 1, 1, 'R');
                
                //$pdf->Cell(110, 5, utf8_decode($cotizacion['comentario']), 1, 0, 'L');
                $x = $pdf->GetX();
                $pdf->comentarios(110, 10, $x, $cotizacion['comentario']);
                
                
                $pdf->Cell(20, 6, '', 0, 0, 'C');                
                $pdf->Cell(25, 6, 'Sub-Total', 1, 0, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(35, 6, $empresa['simbolo']. number_format($sub, 2), 1, 1, 'R');

                $pdf->Cell(130, 6, '', 0, 0, 'C');                
                $pdf->Cell(25, 6, 'Impuesto', 1, 0, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(35, 6, $empresa['simbolo']. number_format($igvTotal, 2), 1, 1, 'R');

                $pdf->Cell(130, 6, '', 0, 0, 'C');                
                $pdf->Cell(25, 6, 'Total', 1, 0, 'L', true);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(35, 6, $empresa['simbolo']. number_format($totalGeneral, 2), 1, 1, 'R');

                $pdf->Ln();
                //Encabezado de terminos
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 6, 'TERMINOS Y CONDICONES', 1, 1, 'C', true);
                $terminos = $this->model->getTerminos();
                $pdf->SetTextColor(0, 0, 0);
                foreach ($terminos as $termino) {
                    $pdf->MultiCell(190, 5, $termino['id'] . ' . ' . utf8_decode($termino['titulo'] . '  : ' . $termino['descripcion']));
                }
                $pdf->Cell(190, 6, '', 0, 1, 'C');
                //Encabezado de CUENTAS
                $pdf->SetFillColor(113, 202, 249);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(95, 6, 'BANCO BVVA', 1, 0, 'C', true);
                $pdf->Cell(95, 6, 'BANCO INTERBANK', 1, 1, 'C', true);

                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(95, 6, utf8_decode('TITULAR: ' . $empresa['nombre']), 1, 0, 'C');
                $pdf->Cell(95, 6, utf8_decode('TITULAR: ' . $empresa['nombre']), 1, 1, 'C');

                require_once 'cuentas.php';
                $pdf->Cell(95, 6, 'Soles: ' . $cuentas[0]['soles'], 1, 0, 'C');
                $pdf->Cell(95, 6, 'Soles: ' . $cuentas[1]['soles'], 1, 1, 'C');

                $pdf->Cell(95, 6, 'Dolares: ' . $cuentas[0]['dolares'], 1, 0, 'C');
                $pdf->Cell(95, 6, 'Dolares: ' . $cuentas[1]['dolares'], 1, 1, 'C'); 
                
                $pdf->Ln();

                $pdf->WriteHTML(utf8_decode($empresa['mensaje']));                
                $pdf->Output();
            }
        } else {
            header('Location: ' . BASE_URL . 'Errors');
        }
    }

    public function calcularTotal()
    {
        $data['total'] = 0.00;
        $detalle = $this->model->getDetalle($this->id_usuario);
        for ($i = 0; $i < count($detalle); $i++) {
            $data['total'] += ($detalle[$i]['precio'] * $detalle[$i]['cantidad']) - $detalle[$i]['descuento'];
        }
        $data['detalle'] = $detalle;
        return $data;
    }
}
