<?php
class Query extends Conexion{
    private $pdo, $con, $sql, $datos;
    public function __construct() {
        $this->pdo = new Conexion();
        $this->con = $this->pdo->conect();
    }
    public function select(string $sql)
    {
        // LOGGING
        $log_message = "[" . date("Y-m-d H:i:s") . "] " . $sql . PHP_EOL;
        file_put_contents('sql_queries.log', $log_message, FILE_APPEND);

        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    public function selectAll(string $sql)
    {
        // LOGGING
        $log_message = "[" . date("Y-m-d H:i:s") . "] " . $sql . PHP_EOL;
        file_put_contents('sql_queries.log', $log_message, FILE_APPEND);

        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public function save(string $sql, array $datos)
    {
        // LOGGING
        $log_message = "[" . date("Y-m-d H:i:s") . "] " . $sql . " | DATA: " . json_encode($datos) . PHP_EOL;
        file_put_contents('sql_queries.log', $log_message, FILE_APPEND);
        
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
        // LOGGING
        $log_message = "[" . date("Y-m-d H:i:s") . "] " . $sql . " | DATA: " . json_encode($datos) . PHP_EOL;
        file_put_contents('sql_queries.log', $log_message, FILE_APPEND);

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
