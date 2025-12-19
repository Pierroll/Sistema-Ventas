<?php
class UsuariosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario(string $correo, string $clave)
    {
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo' AND clave = '$clave'";
        $data = $this->select($sql);
        return $data;
    }
    public function getCajas()
    {
        $sql = "SELECT * FROM caja WHERE estado = 1";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        $data = $this->select($sql);
        return $data;
    }
    public function getUsuarios(int $estado)
    {
        $sql = "SELECT u.id, u.nombre,u.correo, u.estado, c.caja FROM usuarios u INNER JOIN caja c WHERE u.id_caja = c.id AND u.estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarUsuario(string $nombre, string $correo, string $clave, int $id_caja)
    {
        $vericar = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $existe = $this->select($vericar);
        if (empty($existe)) {
            # code...
            $sql = "INSERT INTO usuarios(nombre, correo, clave, id_caja) VALUES (?,?,?,?)";
            $datos = array($nombre, $correo, $clave, $id_caja);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            } else {
                $res = "error";
            }
        } else {
            $res = "existe";
        }
        return $res;
    }
    public function modificarUsuario(string $nombre, string $correo, int $id_caja, int $id)
    {
        $verficar = "SELECT * FROM usuarios WHERE correo = '$correo' AND id != $id";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            $sql = "UPDATE usuarios SET nombre = ?, correo=?, id_caja = ? WHERE id = ?";
            $datos = array($nombre, $correo, $id_caja, $id);
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
    public function editarUser(int $id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function getPass(string $clave, int $id)
    {
        $sql = "SELECT * FROM usuarios WHERE clave = '$clave' AND id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionUser(int $estado, int $id)
    {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function modificarPass(string $clave, int $id)
    {
        $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
        $datos = array($clave, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function getPermisos()
    {
        $sql = "SELECT * FROM permisos";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getDetallePermisos(int $id)
    {
        $sql = "SELECT * FROM detalle_permisos WHERE id_usuario = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function deletePermisos(int $id)
    {
        $sql = "DELETE FROM detalle_permisos WHERE id_usuario = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function actualizarPermisos(int $id_usuario, int $permiso)
    {
        $sql = "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES (?,?)";
        $datos = array($id_usuario, $permiso);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.id, p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
    public function listarPermisos($id_user)
    {
        $sql = "SELECT p.id, p.permiso, d.id, d.id_usuario, d.id_permiso FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getCorreo(string $correo)
    {
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $data = $this->select($sql);
        return $data;
    }
    public function getToken(string $token)
    {
        $sql = "SELECT * FROM usuarios WHERE token = '$token'";
        $data = $this->select($sql);
        return $data;
    }
    public function actualizarToken(string $token, string $correo)
    {
        $sql = "UPDATE usuarios SET token = ? WHERE correo = ?";
        $datos = array($token, $correo);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function resetearPass(string $clave, string $token)
    {
        $sql = "UPDATE usuarios SET clave = ?, token = ? WHERE token = ?";
        $datos = array($clave, null, $token);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function modificarDato(string $nombre, string $apellido, string $correo, string $tel, string $dir, string $img, int $id)
    {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, correo=?, telefono=?, direccion=?, perfil=? WHERE id=?";
        $datos = array($nombre, $apellido, $correo, $tel, $dir, $img, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 1;
        } else {
            $res = 0;
        }
        return $res;
    }
}
