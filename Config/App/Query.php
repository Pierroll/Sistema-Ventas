<?php
class Query {
    private $con, $sql, $datos;
    public function __construct()
    {
        $pdo = "mysql:host=" . HOST . ";dbname=" . DB . ";" . CHARSET;
        try {
            $this->con = new PDO($pdo, USER, PASS);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->con = null;
            error_log("DATABASE CONNECTION FAILED: " . $e->getMessage());
        }
    }
    public function select(string $sql)
    {
        if ($this->con === null) return false;
        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($data) && count($data) > 0) {
            return $data[0];
        }
        return false;
    }
    public function selectAll(string $sql)
    {
        if ($this->con === null) return [];
        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public function save(string $sql, array $datos)
    {
        if ($this->con === null) return "No database connection in save";
        try {
            $this->sql = $sql;
            $this->datos = $datos;
            $insert = $this->con->prepare($this->sql);
            $data = $insert->execute($this->datos);
            if ($data) {
                $res = 1;
            } else {
                $res = 0;
            }
        } catch (PDOException $e) {
            $res = "Error BD: " . $e->getMessage();
        }
        return $res;
    }
    public function insertar(string $sql, array $datos)
    {
        if ($this->con === null) return 0;
        $this->sql = $sql;
        $this->datos = $datos;
        $insert = $this->con->prepare($this->sql);
        $data = $insert->execute($this->datos);
        if ($data) {
            $res = $this->con->lastInsertId();
        } else {
            $res = 0;
        }
        return $res;
    }
}
