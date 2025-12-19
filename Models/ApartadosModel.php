<?php

class ApartadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProducto(int $id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getApartados()
    {
        $sql = "SELECT c.nombre AS title, a.id, a.fecha_apartado, a.fecha_retiro AS start, a.abono, a.total, a.color, a.estado FROM apartados a INNER JOIN clientes c ON c.id = a.id_cliente";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function consultarDetalle(int $id_producto, int $id_usuario)
    {
        $sql = "SELECT * FROM temp_apartados WHERE id_producto = $id_producto AND id_usuario = $id_usuario";
        $data = $this->select($sql);
        return $data;
    }
    public function registrarDetalle(int $id_producto, int $id_usuario, string $precio, int $cantidad)
    {
        $sql = "INSERT INTO temp_apartados (id_producto, id_usuario, precio, cantidad) VALUES (?,?,?,?)";
        $datos = array($id_producto, $id_usuario, $precio, $cantidad);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function getDetalle(int $id_usuario)
    {
        $sql = "SELECT d.*, p.descripcion FROM temp_apartados d INNER JOIN productos p ON d.id_producto = p.id WHERE d.id_usuario = $id_usuario";
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
    
    public function actualizarDetalle(string $table, string $precio, int $cantidad, string $sub_total,int $id_producto, int $id_usuario)
    {
        $sql = "UPDATE $table SET precio = ?, cantidad = ?, sub_total = ? WHERE id_producto = ? AND id_usuario = ?";
        $datos = array($precio,$cantidad, $sub_total, $id_producto, $id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "modificado";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function registrarDetalleApartado(int $cantidad, string $precio, int $id_pro, int $id_apart)
    {
        $sql = "INSERT INTO detalle_apartados (cantidad, precio, id_producto, id_apartado) VALUES (?,?,?,?)";
        $datos = array($cantidad,$precio, $id_pro, $id_apart);
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
    public function vaciarDetalle(int $id_usuario)
    {
        $sql = "DELETE FROM temp_apartados WHERE id_usuario = ?";
        $datos = array($id_usuario);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function actualizarStock(int $cantidad, int $id_pro)
    {
        $sql = "UPDATE productos SET cantidad = ? WHERE id = ?";
        $datos = array($cantidad, $id_pro);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function registrarApartado(string $f_recojo,string $abono, string $total, int $id_cliente)
    {
        $sql = "INSERT INTO apartados (fecha_retiro, abono, total, id_cliente) VALUES (?,?,?,?)";
        $datos = array($f_recojo, $abono, $total, $id_cliente);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    //actualizar Apartado
    public function actualizarApartado(int $id_apartado)
    {
        $sql = "UPDATE apartados SET color=?, estado = ? WHERE id = ?";
        $datos = array('#198754', 0, $id_apartado);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 1;
        }else{
            $res = 0;
        }
        return $res;
    }
    public function getDetalleApartado(int $id_apartado)
    {
        $sql = "SELECT d.*, a.*, p.descripcion FROM detalle_apartados d INNER JOIN apartados a ON a.id = d.id_apartado INNER JOIN productos p ON p.id = d.id_producto WHERE a.id = $id_apartado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getVerificar(int $id_apartado)
    {
        $sql = "SELECT * FROM apartados WHERE id = $id_apartado";
        $data = $this->select($sql);
        return $data;
    }
    public function getCliente(int $id)
    {
        $sql = "SELECT c.*, a.* FROM apartados a INNER JOIN clientes c ON a.id_cliente = c.id WHERE a.id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
}
