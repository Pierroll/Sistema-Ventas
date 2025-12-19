<?php
class Creditos extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL . "admin/home");
        }
        parent::__construct();
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $this->views->getView('creditos',  "index");
    }
    public function listar($valor)
    {
        $estado = (empty($valor)) ? 0 : $valor ;
        $data = $this->model->getHistorial($estado);
        for ($i = 0; $i < count($data); $i++) {
            $abono = $this->model->getAbono($data[$i]['id']);
            $restante = $data[$i]['monto'] - $abono['total'];
            $data[$i]['restante'] = number_format($data[$i]['monto'] - $abono['total'], 2);
            $data[$i]['abonado'] = number_format($data[$i]['monto'] - $restante, 2);
            $data[$i]['accion'] = '<button class="btn btn-outline-warning" type="button" onclick="btnAddAbono(' . $data[$i]['id'] . ');"><i class="fas fa-ban"></i></button>';
            if ($restante < 1) {
                $this->model->actualizarEstado($data[$i]['id']);
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function verificarMonto(int $id_credito)
    {
        $credito = $this->model->getCredito($id_credito);
        $abono = $this->model->getAbono($id_credito);
        $data['restante']  = $credito['monto'] - $abono['total'];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrarAbono()
    {
        if (isset($_POST['id_credito']) && isset($_POST['monto'])) {
            $id_credito = strClean($_POST['id_credito']);
            $monto = strClean($_POST['monto']);
            if (empty($monto) || empty($id_credito)) {
                $msg = array('msg' => 'el monto es requerido', 'icono' => 'warning');
            } else {
                $data = $this->model->registrar($monto, $id_credito, $this->id_usuario);
                if ($data > 0) {
                    $msg = array('msg' => 'abono registrado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'error al registrar', 'icono' => 'error');
                }
            }
        } else {
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    //finalizados
    public function finalizados()
    {
        $this->views->getView('creditos',  "finalizados");
    }
    //abonos
    public function abonos()
    {
        $this->views->getView('creditos',  "abonos");
    }
    public function listarAbonos()
    {
        $data = $this->model->getListarAbonos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
