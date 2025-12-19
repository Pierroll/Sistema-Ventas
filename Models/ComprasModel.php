<?php
class ComprasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function buscarProducto(string $cod)
    {
        $sql = "SELECT p.* FROM productos p WHERE p.codigo LIKE '%" . $cod . "%' AND p.estado = 1 OR p.descripcion LIKE '%" . $cod . "%' AND p.estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getProducto(int $id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
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
    public function registraCompra(int $id_pr, int $id_user, string $total, string $fecha, string $hora, int $serie)
    {
        $sql = "INSERT INTO compras (id_proveedor,id_usuario, total, fecha, hora, serie) VALUES (?,?,?,?,?,?)";
        $datos = array($id_pr, $id_user, $total, $fecha, $hora, $serie);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = "error";
        }
        return $res;
    }
    public function registrarDetalleCompra(int $id_compra, int $id_pro, $cantidad, string $precio)
    {
        $sql = "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio) VALUES (?,?,?,?)";
        $datos = array($id_compra, $id_pro, $cantidad, $precio);
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
    public function getProCompra(int $id_compra)
    {
        $sql = "SELECT c.*, d.*, p.descripcion FROM compras c INNER JOIN detalle_compras d ON c.id = d.id_compra INNER JOIN productos p ON p.id = d.id_producto WHERE c.id = $id_compra";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getFecha(string $table, int $id)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getHistorialcompras(int $estado)
    {
        $sql = "SELECT c.id, c.total, c.fecha, c.hora, p.nombre FROM compras c INNER JOIN proveedor p ON c.id_proveedor = p.id WHERE c.estado = $estado";
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
    public function proveedor(int $id)
    {
        $sql = "SELECT p.* FROM compras c INNER JOIN proveedor p ON p.id = c.id_proveedor WHERE c.id = $id";
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
    public function getAnularCompras(int $id)
    {
        $sql = "SELECT * FROM detalle_compras WHERE id_compra = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function ingresarEntrada(int $id, int $id_user, $cantidad, string $fecha)
    {
        $sql = "INSERT INTO inventario(id_producto, id_usuario, entradas, fecha) VALUES (?,?,?,?)";
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
}
