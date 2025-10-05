<?php
class CategoriasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getCategorias(int $estado)
    {
        $sql = "SELECT * FROM categorias WHERE estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarCategoria(string $nombre)
    {
        $verficar = "SELECT * FROM categorias WHERE nombre = '$nombre'";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            # code...
            $sql = "INSERT INTO categorias(nombre) VALUES (?)";
            $datos = array($nombre);
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
    public function modificarCategoria(string $nombre, int $id)
    {
        $verficar = "SELECT * FROM categorias WHERE nombre = '$nombre' AND id != $id";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            $sql = "UPDATE categorias SET nombre = ? WHERE id = ?";
            $datos = array($nombre, $id);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "modificado";
            } else {
                $res = "error";
            }
        }else {
            $res = "existe";
        }
        return $res;
    }
    public function editarCat(int $id)
    {
        $sql = "SELECT * FROM categorias WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionCat(int $estado, int $id)
    {
        $sql = "UPDATE categorias SET estado = ? WHERE id = ?";
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
