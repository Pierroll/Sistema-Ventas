<?php
class VentasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getClientes()
    {
        $sql = "SELECT * FROM clientes WHERE estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function buscarProducto(string $cod)
    {
        $sql = "SELECT * FROM productos WHERE codigo LIKE '%" . $cod . "%' AND estado = 1 OR descripcion LIKE '%" . $cod . "%' AND estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getProductos(int $id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getProductoPorCodigo(string $codigo)
    {
        $sql = "SELECT * FROM productos WHERE codigo = '$codigo'";
        $data = $this->select($sql);
        return $data;
    }
    public function registrarDetalle(string $table, int $id_producto, int $id_usuario, string $precio, $cantidad)
    {
        $sql = "INSERT INTO $table (id_producto, id_usuario, precio, cantidad) VALUES (?,?,?,?)";
        $datos = array($id_producto, $id_usuario, $precio, $cantidad);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getDetalle(string $table, int $id)
    {
        $sql = "SELECT d.*, p.descripcion FROM $table d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function deleteDetalle(string $table, int $id)
    {
        $sql = "DELETE FROM $table WHERE id = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function consultarDetalle(string $table, int $id_producto, int $id_usuario)
    {
        $sql = "SELECT * FROM $table WHERE id_producto = $id_producto AND id_usuario = $id_usuario";
        $data = $this->select($sql);
        return $data;
    }
    public function actualizarDetalle(string $table, string $precio, $cantidad, int $id_producto, int $id_usuario)
    {
        $sql = "UPDATE $table SET precio = ?, cantidad = ? WHERE id_producto = ? AND id_usuario = ?";
        $datos = array($precio, $cantidad, $id_producto, $id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "modificado";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function registrarDetalleVenta(int $id_venta, int $id_pro, $cantidad, string $precio, string $fecha)
    {
        $sql = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio, fecha) VALUES (?,?,?,?,?)";
        $datos = array($id_venta, $id_pro, $cantidad, $precio, $fecha);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getEmpresa()
    {
        $sql = "SELECT m.simbolo, c.* FROM moneda m INNER JOIN configuracion c ON m.id = c.moneda";
        $data = $this->select($sql);
        return $data;
    }
    public function vaciarDetalle(string $table, int $id_usuario)
    {
        $sql = "DELETE FROM $table WHERE id_usuario = ?";
        $datos = array($id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getProVenta(int $id_venta)
    {
        $sql = "SELECT v.*, d.*, p.descripcion FROM ventas v INNER JOIN detalle_ventas d ON v.id = d.id_venta INNER JOIN productos p ON p.id = d.id_producto WHERE v.id = $id_venta";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getFecha(string $table, int $id)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getHistorialVentas(int $estado)
    {
        $sql = "SELECT v.*, c.nombre FROM ventas v INNER JOIN clientes c ON c.id = v.id_cliente WHERE v.estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function actualizarStock($cantidad, int $id_pro)
    {
        $sql = "UPDATE productos SET cantidad = ? WHERE id = ?";
        $datos = array($cantidad, $id_pro);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function registraVenta(int $id_user, int $id_cliente, string $total, string $fecha, string $hora, int $serie, $metodo)
    {
        $sql = "INSERT INTO ventas (id_usuario, id_cliente, total, fecha, hora, serie, metodo) VALUES (?,?,?,?,?,?,?)";
        $datos = array($id_user, $id_cliente, $total, $fecha, $hora, $serie, $metodo);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function registraCredito($total, $id_venta)
    {
        $sql = "INSERT INTO creditos (monto, id_venta) VALUES (?,?)";
        $datos = array($total, $id_venta);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function clientesVenta(int $id)
    {
        $sql = "SELECT c.* FROM ventas v INNER JOIN clientes c ON c.id = v.id_cliente WHERE v.id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getDetalleTemp(int $id)
    {
        $sql = "SELECT * FROM detalle_temp WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function anular(string $table, int $id)
    {
        $sql = "UPDATE $table SET estado = ? WHERE id = ?";
        $datos = array(0, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getAnularVentas(int $id)
    {
        $sql = "SELECT * FROM detalle_ventas WHERE id_venta = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function verificarCaja(int $id)
    {
        $sql = "SELECT * FROM cierre_caja WHERE id_usuario = $id AND estado = 1";
        $data = $this->select($sql);
        return $data;
    }
    public function ingresarSalida(int $id, int $id_user, $cantidad, string $fecha)
    {
        $sql = "INSERT INTO inventario(id_producto, id_usuario, salidas, fecha) VALUES (?,?,?,?)";
        $datos = array($id, $id_user, $cantidad, $fecha);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
    public function detalle(int $id, string $table)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    //agregarCantidad
    public function actualizarCantidad($table, $cantidad, $id)
    {
        $sql = "UPDATE $table SET cantidad = ? WHERE id = ?";
        $datos = array($cantidad, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 'ok';
        } else {
            $res = 'error';
        }
        return $res;
    }
    public function registrarCliente($dni, string $nombre, string $telefono, string $direccion)
    {
        $sql = "INSERT INTO clientes(dni, nombre, telefono, direccion) VALUES (?,?,?,?)";
        $datos = array($dni, $nombre, $telefono, $direccion);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
}