<?php
class LandingModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getLanding()
    {
        $sql = "SELECT * FROM landing WHERE control != 2";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrar($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio)
    {
        $sql = "INSERT INTO landing(hora_registro, fecha_registro, pagina, nombre, telefono, correo, negocio) VALUES (?,?,?,?,?,?,?)";
        $datos = array($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function modificar($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio, int $id)
    {
        $sql = "UPDATE landing SET hora_registro=?, fecha_registro=?, pagina=?, nombre=?, telefono=?, correo=?, negocio=? WHERE id = ?";
        $datos = array($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function editar(int $id)
    {
        $sql = "SELECT * FROM landing WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accion(int $estado, int $id)
    {
        $sql = "UPDATE landing SET control = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function eliminar(int $id)
    {
        $sql = "DELETE FROM landing WHERE id = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
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

    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.id, p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
}
