<?php
class ProveedorModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProveedor(int $estado)
    {
        $sql = "SELECT * FROM proveedor WHERE estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function buscarProveedor(string $valor)
    {
        $sql = "SELECT id, nombre, direccion FROM proveedor WHERE ruc LIKE '%" . $valor . "%' AND estado = 1 OR nombre LIKE '%" . $valor . "%' AND estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrar(string $ruc, string $nombre, string $telefono, string $direccion)
    {
        $verficarRuc = "SELECT * FROM proveedor WHERE ruc = '$ruc'";
        $verficarTel = "SELECT * FROM proveedor WHERE telefono = '$telefono'";
        $existeRuc = $this->select($verficarRuc);
        $existeTel = $this->select($verficarTel);
        if (!empty($existeRuc)) {
            $res = "ruc";
        }else if (!empty($existeTel)) {
            $res = "telefono";
        } else {
            $sql = "INSERT INTO proveedor(ruc, nombre, telefono, direccion) VALUES (?,?,?,?)";
            $datos = array($ruc, $nombre, $telefono, $direccion);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            } else {
                $res = "error";
            }
        }
        return $res;
    }
    public function modificar(string $ruc, string $nombre, string $telefono, string $direccion, int $id)
    {
        $verficarRuc = "SELECT * FROM proveedor WHERE ruc = '$ruc' AND id != $id";
        $verficarTel = "SELECT * FROM proveedor WHERE telefono = '$telefono' AND id != $id";
        $existeRuc = $this->select($verficarRuc);
        $existeTel = $this->select($verficarTel);
        if (!empty($existeRuc)) {
            $res = "ruc";
        }else if (!empty($existeTel)) {
            $res = "telefono";
        } else {
            $sql = "UPDATE proveedor SET ruc = ?, nombre = ?, telefono = ? ,direccion = ? WHERE id = ?";
            $datos = array($ruc, $nombre, $telefono, $direccion, $id);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            } else {
                $res = "error";
            }
        }
        return $res;
    }
    public function editarpr(int $id)
    {
        $sql = "SELECT * FROM proveedor WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionpr(int $estado, int $id)
    {
        $sql = "UPDATE proveedor SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.id, p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
}
