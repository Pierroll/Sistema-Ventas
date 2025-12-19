<?php
class CotizacionesModel extends Query
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

    public function getProductos(int $id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function registrarDetalle($precio, $cantidad, $id_producto, $id_usuario)
    {
        $sql = "INSERT INTO temp_cotizaciones (precio, cantidad, id_producto, id_usuario) VALUES (?,?,?,?)";
        $datos = array($precio, $cantidad, $id_producto, $id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getDetalle(int $id)
    {
        $sql = "SELECT d.*, p.descripcion FROM temp_cotizaciones d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id";
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

    public function consultarDetalle(int $id_producto, int $id_usuario)
    {
        $sql = "SELECT * FROM temp_cotizaciones WHERE id_producto = $id_producto AND id_usuario = $id_usuario";
        $data = $this->select($sql);
        return $data;
    }

    public function consultarCotizacion(int $id_usuario)
    {
        $sql = "SELECT t.*, p.descripcion FROM temp_cotizaciones t INNER JOIN productos p ON t.id_producto = p.id WHERE t.id_usuario = $id_usuario";
        $data = $this->selectAll($sql);
        return $data;
    }

    public function actualizarDetalle(string $precio, $cantidad, int $id_producto, int $id_usuario)
    {
        $sql = "UPDATE temp_cotizaciones SET precio = ?, cantidad = ? WHERE id_producto = ? AND id_usuario = ?";
        $datos = array($precio, $cantidad, $id_producto, $id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "modificado";
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

    public function getCotizacion(int $id)
    {
        $sql = "SELECT c.*, cl.nombre, cl.telefono, cl.direccion FROM cotizaciones c INNER JOIN clientes cl ON c.id_cliente = cl.id WHERE c.id = $id";
        $data = $this->select($sql);
        return $data;
    }

    public function getHistorial()
    {
        $sql = "SELECT ct.*, c.nombre FROM cotizaciones ct INNER JOIN clientes c ON ct.id_cliente = c.id";
        $data = $this->selectAll($sql);
        return $data;
    }

    public function registraCotizacion($productos, $total, $fecha, $hora, $validez, $comentario, $id_cliente, $id_usuario)
    {
        $sql = "INSERT INTO cotizaciones (productos, total, fecha, hora, validez, comentario, id_cliente, id_usuario) VALUES (?,?,?,?,?,?,?,?)";
        $datos = array($productos, $total, $fecha, $hora,$validez, $comentario, $id_cliente, $id_usuario);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }

    public function getDetalleTemp(int $id)
    {
        $sql = "SELECT * FROM temp_cotizaciones WHERE id = $id";
        $data = $this->select($sql);
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

    public function getTerminos()
    {
        $sql = "SELECT * FROM terminos";
        $data = $this->selectAll($sql);
        return $data;
    }

    //agregarCantidad
    public function actualizarCantidad($campo, $item, $id)
    {
        $sql = "UPDATE temp_cotizaciones SET $campo = ? WHERE id = ?";
        $datos = array($item, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 'ok';
        } else {
            $res = 'error';
        }
        return $res;
    }

}