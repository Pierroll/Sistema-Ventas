<?php
class CajasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getCajas(int $estado)
    {
        $sql = "SELECT * FROM caja WHERE estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getCierre_caja(int $id)
    {
        $sql = "SELECT * FROM cierre_caja WHERE id_usuario = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarCaja(string $caja)
    {
        $verficar = "SELECT * FROM caja WHERE caja = '$caja'";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            # code...
            $sql = "INSERT INTO caja (caja) VALUES (?)";
            $datos = array($caja);
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
    public function modificarCaja(string $caja, int $id)
    {
        $verficar = "SELECT * FROM caja WHERE caja = '$caja' AND id != $id";
        $existe = $this->select($verficar);
        if (empty($existe)) {
            $sql = "UPDATE caja SET caja = ? WHERE id = ?";
            $datos = array($caja, $id);
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
    public function editarCaja(int $id)
    {
        $sql = "SELECT * FROM caja WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionCaja(int $estado, int $id)
    {
        $sql = "UPDATE caja SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function registrarArqueo(int $id_usuario, string $monto_inical, string $fecha_apertura)
    {
        $verificar = "SELECT * FROM cierre_caja WHERE id_usuario = '$id_usuario' AND estado = 1";
        $existe = $this->select($verificar);
        if (empty($existe)) {
            $sql = "INSERT INTO cierre_caja (id_usuario, monto_inicial, fecha_apertura) VALUES (?,?,?)";
            $datos = array($id_usuario, $monto_inical, $fecha_apertura);
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
    public function getVentas(int $id_user)
    {
        $sql = "SELECT total, SUM(total) AS total FROM ventas WHERE id_usuario = $id_user AND estado = 1 AND apertura = 1";
        $data = $this->select($sql);
        return $data;
    }
    public function getTotalVentas(int $id_user)
    {
        $sql = "SELECT COUNT(total) AS total FROM ventas WHERE id_usuario = $id_user AND estado = 1 AND apertura = 1";
        $data = $this->select($sql);
        return $data;
    }
    public function getMontoInicial(int $id_user)
    {
        $sql = "SELECT id, monto_inicial FROM cierre_caja WHERE id_usuario = $id_user AND estado = 1";
        $data = $this->select($sql);
        return $data;
    }
    public function actualizarArqueo(string $final, string $cierre, string $ventas, string $general, int $id)
    {
        $sql = "UPDATE cierre_caja SET monto_final=?, fecha_cierre=?,total_ventas=?, monto_total=?, estado=? WHERE id = ?";
        $datos = array($final, $cierre, $ventas, $general, 0, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "ok";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function actualizarApertura(int $id)
    {
        $sql = "UPDATE ventas SET apertura=? WHERE id_usuario = ?";
        $datos = array(0, $id);
        $this->save($sql, $datos);
    }
    public function verificarPermisos($id_user, $permiso)
    {
        $sql = "SELECT p.id, p.permiso, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.permiso = '$permiso'";
        $existe = $this->select($sql);
        return $existe;
    }
}
