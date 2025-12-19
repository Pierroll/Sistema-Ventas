<?php
class ProductosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getMedidas()
    {
        $sql = "SELECT * FROM medidas WHERE estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getCategorias()
    {
        $sql = "SELECT * FROM categorias WHERE estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getProductos(int $estado)
    {
        $sql = "SELECT p.*, m.nombre AS medida, c.nombre AS categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id INNER JOIN medidas m ON p.id_medida = m.id WHERE p.estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarProducto(string $codigo, string $nombre, string $precio_compra, string $precio_venta, int $medida, int $id_categoria, string $img)
    {
        $vericar = "SELECT * FROM productos WHERE codigo = '$codigo'";
        $existe = $this->select($vericar);
        if (empty($existe)) {
            $sql = "INSERT INTO productos(codigo, descripcion, precio_compra, precio_venta, id_medida, id_categoria, foto) VALUES (?,?,?,?,?,?,?)";
            $datos = array($codigo, $nombre, $precio_compra, $precio_venta, $medida, $id_categoria, $img);
            $data = $this->insertar($sql, $datos);
            if ($data > 0) {
                $res = $data;
            } else {
                $res = -1;
            }
        } else {
            $res = 0;
        }
        return $res;
    }
    public function modificarProducto(string $codigo, string $nombre, string $precio_compra, string $precio_venta, int $medida, int $id_categoria, string $img, int $id)
    {
        $verficar = "SELECT * FROM productos WHERE codigo = '$codigo' AND id != $id";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            $sql = "UPDATE productos SET codigo = ?, descripcion = ?, precio_compra = ?, precio_venta = ?, id_medida = ?, id_categoria =?, foto = ? WHERE id = ?";
            $datos = array($codigo, $nombre, $precio_compra, $precio_venta, $medida, $id_categoria, $img, $id);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "modificado";
            } else {
                $res = "error";
            }
        } else {
            $res = "existe";
        }
        return $res;
    }
    public function editarPro(int $id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionPro(int $estado, int $id)
    {
        $sql = "UPDATE productos SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function registrarDetalle(int $id_producto, int $id_usuario, string $precio, $cantidad, string $sub_total)
    {
        $sql = "INSERT INTO detalle_temp (id_producto, id_usuario, precio, cantidad, sub_total) VALUES (?,?,?,?,?)";
        $datos = array($id_producto, $id_usuario, $precio, $cantidad, $sub_total);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function consultarDetalle(int $id_producto, int $id_usuario)
    {
        $sql = "SELECT * FROM detalle_temp WHERE id_producto = $id_producto AND id_usuario = $id_usuario";
        $data = $this->select($sql);
        return $data;
    }
    public function ingresarInventario(int $id, int $id_user, $cantidad, string $fecha, string $hora, string $accion)
    {
        $sql = "INSERT INTO inventario(id_producto, id_usuario, cantidad, fecha, hora, accion) VALUES (?,?,?,?,?,?)";
        $datos = array($id, $id_user, $cantidad, $fecha, $hora, $accion);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function ingresarEntrada(int $id, int $id_user, $cantidad, string $fecha)
    {
        $sql = "INSERT INTO inventario(id_producto, id_usuario, entradas, fecha) VALUES (?,?,?,?)";
        $datos = array($id, $id_user, $cantidad, $fecha);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function ingresarSalida(int $id, int $id_user, $cantidad, string $fecha)
    {
        $sql = "INSERT INTO inventario(id_producto, id_usuario, salidas, fecha) VALUES (?,?,?,?)";
        $datos = array($id, $id_user, $cantidad, $fecha);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function getInventarios()
    {
        $sql = "SELECT p.descripcion, i.id AS id_inventario, i.id_producto, i.fecha, SUM(i.entradas) AS total_entradas, SUM(i.salidas) AS total_salidas FROM inventario i INNER JOIN productos p ON i.id_producto = p.id GROUP BY p.id, i.fecha ORDER BY i.id DESC";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function filtroInventarios(string $desde, string $hasta)
    {
        $sql = "SELECT p.descripcion, i.id AS id_inventario, i.id_producto, i.fecha, SUM(i.entradas) AS total_entradas, SUM(i.salidas) AS total_salidas FROM inventario i INNER JOIN productos p ON i.id_producto = p.id WHERE i.fecha BETWEEN '$desde' AND '$hasta' GROUP BY p.id, i.fecha ORDER BY i.id DESC";
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
    public function getEmpresa()
    {
        $sql = "SELECT m.id, m.simbolo, c.* FROM moneda m INNER JOIN configuracion c ON m.id = c.moneda";
        $data = $this->select($sql);
        return $data;
    }
    public function getCompras()
    {
        $sql = "SELECT c.*, u.id AS id_user, u.nombre FROM compras c INNER JOIN usuarios u ON c.id_usuario = u.id WHERE c.estado = 1 ORDER BY c.id DESC";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function filtroCompras(string $desde, string $hasta)
    {
        $sql = "SELECT c.*, u.id AS id_user, u.nombre FROM compras c INNER JOIN usuarios u ON c.id_usuario = u.id WHERE c.fecha BETWEEN '$desde' AND '$hasta' AND c.estado = 1 ORDER BY c.id DESC";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getVentas()
    {
        $sql = "SELECT v.*, u.id AS id_user, u.nombre, c.id AS id_cli, c.nombre AS cliente FROM ventas v INNER JOIN usuarios u ON v.id_usuario = u.id INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.estado = 1 ORDER BY v.id DESC";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function filtroVentas(string $desde, string $hasta)
    {
        $sql = "SELECT v.*, u.id AS id_user, u.nombre, c.id AS id_cli, c.nombre AS cliente FROM ventas v INNER JOIN usuarios u ON v.id_usuario = u.id INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.fecha BETWEEN '$desde' AND '$hasta' AND v.estado = 1 ORDER BY v.id DESC";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.id, p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
}
