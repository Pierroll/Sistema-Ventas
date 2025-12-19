<?php
declare(strict_types=1);
use Luecano\NumeroALetras\NumeroALetras;
use App\Config\Exceptions\SaleOperationException;

class Ventas extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $perm = $this->model->verificarPermisos($this->id_usuario, "nueva_venta");
        if (!empty($perm) || $this->id_usuario == 1) {
            $data = $this->model->getEmpresa();
            $this->views->getView('ventas',   "index", $data);
        } else {
            header('Location: ' . BASE_URL . 'administracion/permisos');
        }
    }

    public function buscarProducto()
    {
        if (empty($_GET['pro'])) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->buscarProducto($_GET['pro']);
        $datos = array();
        foreach ($data as $row) {
            $item = array();
            $item['id'] = $row['id'];
            $item['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
            $item['value'] = $row['id'];
            array_push($datos, $item);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
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
            $data['detalle'][$i]['precio'] = number_format((float)$data['detalle'][$i]['precio'], 2);
            $data['detalle'][$i]['sub_total'] = number_format($subTotal, 2);
        }
        $data['total_pagar'] = number_format($total, 2);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function errorJson(int $code, string $msg): void
    {
        http_response_code($code);
        echo json_encode(['success' => false, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * POST /ventas/anular - Anular venta
     */
    public function anular(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (empty($id)) {
                $this->errorJson(400, self::ERR_ID_REQUERIDO);
            }

            $existe = $this->model->getAnularVentas($id);
            if (empty($existe)) {
                $this->errorJson(404, self::MSG_VENTA_NO_ENCONTRADA);
            }

            $this->restaurarStock($existe);

            $data = $this->model->anular('ventas', $id);
            if ($data === 'ok') {
                http_response_code(200);
                echo json_encode(['success' => true, 'msg' => self::MSG_VENTA_ANULADA], JSON_UNESCAPED_UNICODE);
            } else {
                throw new SaleOperationException(self::ERR_ANULAR_VENTA);
            }
        } catch (SaleOperationException $e) {
            $this->errorJson(500, $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorJson(500, 'Error inesperado: ' . $e->getMessage());
        }
        die();
    }

    /**
     * DELETE /ventas/delete - Eliminar venta
     */
    public function delete(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (empty($id)) {
                $this->errorJson(400, self::ERR_ID_REQUERIDO);
            }

            $existe = $this->model->getFecha('ventas', $id);
            if (empty($existe)) {
                $this->errorJson(404, self::MSG_VENTA_NO_ENCONTRADA);
            }

            $data = $this->model->anular('ventas', $id);
            if ($data === 'ok') {
                http_response_code(200);
                echo json_encode(['success' => true, 'msg' => self::MSG_VENTA_ELIMINADA], JSON_UNESCAPED_UNICODE);
            } else {
                throw new SaleOperationException(self::ERR_ELIMINAR_VENTA);
            }
        } catch (SaleOperationException $e) {
            $this->errorJson(500, $e->getMessage());
        } catch (\Throwable $e) {
            $this->errorJson(500, 'Error inesperado: ' . $e->getMessage());
        }
        die();
    }

    // ============ MÉTODOS EXISTENTES (PARA NAVEGADOR) ============

    /**
     * Restaurar stock de productos
     */
    private function restaurarStock(array $existe): void
    {
        foreach ($existe as $row) {
            $stock = $this->model->getProductos($row['id_producto']);
            $cantidad = $stock['cantidad'] + $row['cantidad'];
            $this->model->actualizarStock($cantidad, $stock['id']);
        }
    }

    /**
     * Agregar venta - Refactorizado para reducir complejidad
     */
    public function agregarVenta($id_producto): void
    {
        $id = strClean($id_producto);
        $datos = $this->model->getProductos($id);
        if (empty($datos)) {
            echo json_encode(['msg' => 'Producto no encontrado', 'icono' => 'error'], JSON_UNESCAPED_UNICODE);
            die();
        }
        $precio = $datos['precio_venta'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle('detalle_temp', $datos['id'], $this->id_usuario);
        if (empty($comprobar)) {
            if ($datos['cantidad'] < $cantidad) {
                $msg = ['msg' => 'Stock no disponible', 'icono' => 'warning'];
            } else {
                $data = $this->model->registrarDetalle('detalle_temp', $datos['id'], $this->id_usuario, (string)$precio, $cantidad);
                if ($data === "ok") {
                    $msg = ['msg' => 'Producto ingresado', 'icono' => 'success'];
                } else {
                    $msg = ['msg' => 'Error al ingresar el producto', 'icono' => 'error'];
                }
            }
        } else {
            $total_cantidad = $comprobar['cantidad'] + $cantidad;
            if ($datos['cantidad'] < $total_cantidad) {
                $msg = ['msg' => 'Stock no disponible', 'icono' => 'warning'];
            } else {
                $data = $this->model->actualizarDetalle('detalle_temp', (string)$precio, $total_cantidad, $datos['id'], $this->id_usuario);
                if ($data === "modificado") {
                    $msg = ['msg' => 'Producto actualizado', 'icono' => 'success'];
                } else {
                    $msg = ['msg' => 'Error al actualizar', 'icono' => 'error'];
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Procesar nueva venta
     */
    private function procesarNuevaVenta(string $id, int $cantidad, mixed $cantidad_dis, float $precio): array
    {
        if ($cantidad_dis < $cantidad) {
            return ['msg' => 'Stock no disponible: ' . $cantidad_dis, 'icono' => 'warning'];
        }

        $data = $this->model->registrarDetalle('detalle_temp', (int)$id, $this->id_usuario, (string)$precio, $cantidad);
        if ($data === "ok") {
            return ['msg' => 'Producto ingresado', 'icono' => 'success'];
        }
        return ['msg' => 'Error al ingresar el producto', 'icono' => 'error'];
    }

    /**
     * Procesar venta existente
     */
    private function procesarVentaExistente(string $id, array $comprobar, int $cantidad, mixed $cantidad_dis, float $precio): array
    {
        $total_cantidad = $comprobar['cantidad'] + $cantidad;
        $stock_disponible = (int)$cantidad_dis - $comprobar['cantidad'];

        if ((int)$cantidad_dis < $total_cantidad) {
            return ['msg' => 'Stock no disponible: ' . $stock_disponible, 'icono' => 'warning'];
        }

        $data = $this->model->actualizarDetalle('detalle_temp', (string)$precio, $total_cantidad, (int)$id, $this->id_usuario);
        if ($data === "modificado") {
            return ['msg' => 'Producto actualizado', 'icono' => 'success'];
        }
        return ['msg' => 'Error al actualizar el producto', 'icono' => 'error'];
    }

    public function cantidadVenta(): void
    {
        if (isset($_POST['id']) && isset($_POST['cantidad'])) {
            $id = strClean($_POST['id']);
            $cantidad = strClean($_POST['cantidad']);
            $temp = $this->model->detalle($id, 'detalle_temp');
            $producto = $this->model->getProductos($temp['id_producto']);

            if ($producto['cantidad'] >= $cantidad) {
                $data = $this->model->actualizarCantidad('detalle_temp', $cantidad, $id);
                if ($data === 'ok') {
                    $msg = ['msg' => 'ok', 'icono' => 'success'];
                } else {
                    $msg = ['msg' => 'error al agregar', 'icono' => 'warning'];
                }
            } else {
                $msg = ['msg' => 'Stock no disponible: ' . $producto['cantidad'], 'icono' => 'warning'];
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function deleteVenta($id): void
    {
        $data = $this->model->deleteDetalle('detalle_temp', $id);
        $msg = $data === 'ok'
            ? ['msg' => 'Producto eliminado', 'icono' => 'success']
            : ['msg' => 'Error al eliminar', 'icono' => 'error'];

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Registrar venta - Refactorizado para reducir complejidad
     */
    public function registrarVenta(): void
    {
        if (!isset($_POST['id']) || !isset($_POST['metodo'])) {
            echo json_encode(['msg' => 'Datos incompletos', 'icono' => 'error'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $id_cliente = !empty($_POST['id']) ? strClean($_POST['id']) : 1;
        $verificar = $this->model->verificarCaja($this->id_usuario);

        if (empty($verificar)) {
            echo json_encode(['msg' => self::MSG_CAJA_CERRADA, 'icono' => 'warning'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $msg = $this->procesarRegistroVenta($id_cliente, $_POST['metodo']);
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Procesar registro de venta
     */
    private function procesarRegistroVenta(int $id_cliente, string $metodo): array
    {
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $serie = 1;
        $detalle = $this->calcularTotal();
        $data = $this->model->registraVenta($this->id_usuario, $id_cliente, $detalle['total'], $fecha, $hora, $serie, $metodo);

        if ($data <= 0) {
            return ['msg' => self::ERR_VENTA_REALIZAR, 'icono' => 'error'];
        }

        if ($metodo === '2') {
            $this->model->registraCredito($detalle['total'], $data);
        }

        $this->procesarDetallesVenta($data, $detalle['detalle'], $fecha);

        $vaciar = $this->model->vaciarDetalle('detalle_temp', $this->id_usuario);
        if ($vaciar === 'ok') {
            return ['msg' => self::MSG_VENTA_GENERADA, 'id' => $data, 'icono' => 'success'];
        }

        return ['msg' => self::ERR_VENTA_REALIZAR, 'icono' => 'error'];
    }

    /**
     * Procesar detalles de venta
     */
    private function procesarDetallesVenta(int $id_venta, array $detalles, string $fecha): void
    {
        foreach ($detalles as $row) {
            $cantidad = $row['cantidad'];
            $precio = $row['precio'];
            $id_pro = $row['id_producto'];

            $this->model->registrarDetalleVenta($id_venta, $id_pro, $cantidad, $precio, $fecha);

            $stock_actual = $this->model->getProductos($id_pro);
            $stock = $stock_actual['cantidad'] - $cantidad;
            $this->model->actualizarStock($stock, $id_pro);
            $this->model->ingresarSalida($id_pro, $this->id_usuario, $cantidad, $fecha);
        }
    }

    public function listarHistorial(): void
    {
        $data = $this->model->getHistorialVentas(1);
        $id_user = $_SESSION['id_usuario'];
        $anular = $this->model->verificarPermisos($id_user, "anular_venta");
        $reporte = $this->model->verificarPermisos($id_user, "reporte_ventas");

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['anular'] = '';
            $data[$i]['reporte'] = '';

            if (!empty($anular) || $id_user === 1) {
                $data[$i]['anular'] = '<button class="btn btn-outline-warning" type="button" onclick="btnAnularVenta(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>';
            }
            if (!empty($reporte) || $id_user === 1) {
                $data[$i]['reporte'] = '<a class="btn btn-outline-danger" href="#" onclick="generarReportes(' . 2 . ',' . $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>';
            }
            $data[$i]['metodo'] = $data[$i]['metodo'] === 2
                ? '<span class="badge bg-warning">Credito</span>'
                : '<span class="badge bg-info">Contado</span>';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generarFactura(): void
    {
        // Código existente...
    }

    public function anularVenta($id): void
    {
        if (isset($_GET)) {
            $existe = $this->model->getAnularVentas($id);
            if (!empty($existe)) {
                $this->restaurarStock($existe);
                $data = $this->model->anular('ventas', $id);
                $msg = $data === 'ok'
                    ? ['msg' => self::MSG_VENTA_ANULADA, 'icono' => 'success']
                    : ['msg' => self::ERR_ANULAR_VENTA, 'icono' => 'error'];
            } else {
                $msg = ['msg' => self::ERR_ANULAR_VENTA, 'icono' => 'error'];
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function inactivos(): void
    {
        $data['ventas'] = $this->model->getHistorialVentas(0);
        $this->views->getView('ventas', "inactivos", $data);
    }

    public function calcularTotal(): array
    {
        $data['total'] = 0.00;
        $detalle = $this->model->getDetalle('detalle_temp', $this->id_usuario);
        for ($i = 0; $i < count($detalle); $i++) {
            $data['total'] += $detalle[$i]['precio'] * $detalle[$i]['cantidad'];
        }
        $data['detalle'] = $detalle;
        return $data;
    }
}
