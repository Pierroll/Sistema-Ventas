<?php
class ClientesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getClientes(int $estado)
    {
        $sql = "SELECT * FROM clientes WHERE estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function buscarCliente(string $valor)
    {
        $sql = "SELECT id, dni, nombre, direccion FROM clientes WHERE dni LIKE '%" . $valor . "%' AND estado = 1 OR nombre LIKE '%" . $valor . "%' AND estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarCliente($dni, string $nombre, string $telefono, string $direccion)
    {
        $verficarDni = "SELECT * FROM clientes WHERE dni = '$dni'";
        $verficarTel = "SELECT * FROM clientes WHERE telefono = '$telefono'";
        $existeDni = $this->select($verficarDni);
        $existeTel = $this->select($verficarTel);
        if (!empty($existeDni)) {
            $res = 'dni';
        } else if (!empty($existeTel)) {
            $res = 'telefono';
        }else {
            $sql = "INSERT INTO clientes(dni, nombre, telefono, direccion) VALUES (?,?,?,?)";
            $datos = array($dni, $nombre, $telefono, $direccion);
            $data = $this->insertar($sql, $datos);
            if ($data > 0) {
                $res = $data;
            } else {
                $res = 0;
            }
        }
        return $res;
    }
    public function modificarCliente($dni, string $nombre, string $telefono, string $direccion, int $id)
    {
        $verficarDni = "SELECT * FROM clientes WHERE dni = '$dni' AND id != $id";
        $verficarTel = "SELECT * FROM clientes WHERE telefono = '$telefono' AND id != $id";
        $existeDni = $this->select($verficarDni);
        $existeTel = $this->select($verficarTel);
        if (!empty($existeDni)) {
            $res = 'dni';
        } else if (!empty($existeTel)) {
            $res = 'telefono';
        }else {
            $sql = "UPDATE clientes SET dni = ?, nombre = ?, telefono = ?,direccion = ? WHERE id = ?";
            $datos = array($dni, $nombre, $telefono, $direccion, $id);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            } else {
                $res = "error";
            }
        }
        return $res;
    }
    public function editarCli(int $id)
    {
        $sql = "SELECT * FROM clientes WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionCli(int $estado, int $id)
    {
        $sql = "UPDATE clientes SET estado = ? WHERE id = ?";
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
