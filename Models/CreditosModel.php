<?php
class CreditosModel extends Query{
    public function __construct()
    {
        parent::__construct();
    }
    public function getHistorial($estado)
    {
        $sql = "SELECT cr.*, cl.nombre FROM creditos cr INNER JOIN ventas v ON cr.id_venta = v.id INNER JOIN clientes cl ON v.id_cliente = cl.id WHERE cr.estado = $estado";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getAbono($id_credito)
    {
        $sql = "SELECT SUM(abono) AS total FROM abonos WHERE id_credito = $id_credito";
        $data = $this->select($sql);
        return $data;
    }
    public function actualizarEstado($id_credito)
    {
        $sql = "UPDATE creditos SET estado = ? WHERE id = ?";
        $datos = array(0, $id_credito);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function getCredito($id_credito)
    {
        $sql = "SELECT * FROM creditos WHERE id = $id_credito";
        $data = $this->select($sql);
        return $data;
    }
    public function registrar($monto, $id_credito, $id_usuario)
    {
        $sql = "INSERT INTO abonos(abono, id_credito, id_usuario) VALUES (?,?,?)";
        $datos = array($monto, $id_credito, $id_usuario);
        $data = $this->insertar($sql, $datos);
        return $data;
    }

    public function getListarAbonos()
    {
        $sql = "SELECT a.*, u.nombre FROM abonos a INNER JOIN usuarios u ON a.id_usuario = u.id";
        $data = $this->selectAll($sql);
        return $data;
    }
}

?>