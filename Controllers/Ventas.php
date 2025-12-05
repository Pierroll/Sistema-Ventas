<?php
declare(strict_types=1);
use Luecano\NumeroALetras\NumeroALetras;
use App\Config\Exceptions\SaleOperationException;

class Ventas extends Controller
{
    private $id_usuario;

    // Constantes de mensajes
    private const MSG_VENTA_NO_ENCONTRADA = 'Venta no encontrada';
    private const MSG_VENTA_ANULADA = 'Venta anulada';
    private const MSG_VENTA_ELIMINADA = 'Venta eliminada';
    private const MSG_NO_STOCK = 'No hay Stock, te quedan ';
    private const MSG_PRODUCTO_INGRESADO = 'Producto ingresado a la venta';
    private const MSG_PRODUCTO_ACTUALIZADO = 'Producto actualizado';
    private const MSG_ERROR_INGRESAR = 'Error al ingresar el producto a la venta';
    private const MSG_ERROR_ACTUALIZAR = 'Error al actualizar el producto';
    private const MSG_VENTA_GENERADA = 'Venta Generada';
    private const MSG_CAJA_CERRADA = 'La caja esta cerrada';

    private const ERR_ID_REQUERIDO = 'ID requerido';
    private const ERR_ANULAR_VENTA = 'Error al anular la venta';
    private const ERR_ELIMINAR_VENTA = 'Error al eliminar la venta';
    private const ERR_VENTA_REALIZAR = 'Error al realizar la venta';

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
        $precio = $datos['precio_venta'];
        $cantidad = 1;
        $comprobar = $this->model->consultarDetalle('detalle_temp', $id, $this->id_usuario);
        $cantidad_dis = $datos['cantidad'];

        $msg = $comprobar === null || $comprobar === false || (is_array($comprobar) && empty($comprobar))
            ? $this->procesarNuevaVenta($id, $cantidad, $cantidad_dis, $precio, $id_producto)
            : $this->procesarVentaExistente($id, $comprobar, $cantidad, $cantidad_dis, $precio, $id_producto);

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Procesar nueva venta
     */
    private function procesarNuevaVenta(int $cantidad, int $cantidad_dis, float $precio, string $id): array
    {
        if ($cantidad_dis < $cantidad) {
            return ['msg' => self::MSG_NO_STOCK . $cantidad_dis, 'icono' => 'warning'];
        }

        $data = $this->model->registrarDetalle('detalle_temp', $id, $this->id_usuario, $precio, $cantidad);
        if ($data === "ok") {
            return ['msg' => self::MSG_PRODUCTO_INGRESADO, 'icono' => 'success'];
        }
        return ['msg' => self::MSG_ERROR_INGRESAR, 'icono' => 'error'];
    }

    /**
     * Procesar venta existente
     */
    private function procesarVentaExistente(array $comprobar, int $cantidad, int $cantidad_dis, float $precio, string $id_producto): array
    {
        $total_cantidad = $comprobar['cantidad'] + $cantidad;
        $stock_disponible = $cantidad_dis - $comprobar['cantidad'];

        if ($cantidad_dis < $total_cantidad) {
            return ['msg' => self::MSG_NO_STOCK . $stock_disponible, 'icono' => 'warning'];
        }

        $data = $this->model->actualizarDetalle('detalle_temp', $precio, $total_cantidad, $id_producto, $this->id_usuario);
        if ($data === "modificado") {
            return ['msg' => self::MSG_PRODUCTO_ACTUALIZADO, 'icono' => 'success'];
        }
        return ['msg' => self::MSG_ERROR_ACTUALIZAR, 'icono' => 'error'];
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
                $msg = ['msg' => self::MSG_NO_STOCK . $producto['cantidad'], 'icono' => 'warning'];
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
