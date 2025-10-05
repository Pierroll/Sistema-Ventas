<?php
class Cajas extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_caja");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'caja';
        $this->views->getView('cajas',  "index", $data);
    }
    public function arqueo()
    {
        $id_user = $_SESSION['id_usuario'];
        $cerrar_caja = $this->model->verificarPermisos($id_user, "cerrar_caja");
        $abrir_caja = $this->model->verificarPermisos($id_user, "abrir_caja");
        if (!empty($abrir_caja) || $id_user == 1) {
            $data['abrir_caja'] = true;
        } else {
            $data['abrir_caja'] = false;
        }
        if (!empty($cerrar_caja) || $id_user == 1) {
            $data['cerrar_caja'] = true;
        } else {
            $data['cerrar_caja'] = false;
        }
        $data['datos'] = $this->model->getMontoInicial($id_user);
        $data['modal'] = 'apertura';
        $this->views->getView('cajas',  "arqueo", $data);
    }
    public function listar()
    {
        $id_user = $_SESSION['id_usuario'];
        $modificar = $this->model->verificarPermisos($id_user, "modificar_caja");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_caja");
        $data = $this->model->getCajas(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarCaja(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarCaja(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function listar_arqueo()
    {
        $data = $this->model->getCierre_caja($_SESSION['id_usuario']);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['status'] = $data[$i]['estado'];
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-success">Abierta</span>';
            } else {
                $data[$i]['estado'] = '<span class="badge bg-danger">Cerrada</span>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $caja = strClean($_POST['nombre']);
            $id = strClean($_POST['id']);
            if (empty($caja)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                if (strlen($caja) > 2) {
                    if ($id == "") {
                        $data = $this->model->registrarCaja($caja);
                        if ($data == "ok") {
                            $msg = array('msg' => 'Caja registrado', 'icono' => 'success');
                        } else if ($data == "existe") {
                            $msg = array('msg' => 'La caja ya existe', 'icono' => 'warning');
                        } else {
                            $msg = array('msg' => 'Error al registrar la caja', 'icono' => 'error');
                        }
                    } else {
                        $data = $this->model->modificarCaja($caja, $id);
                        if ($data == "modificado") {
                            $msg = array('msg' => 'Caja Modificado', 'icono' => 'success');
                        } else if ($data == "existe") {
                            $msg = array('msg' => 'La caja ya existe', 'icono' => 'warning');
                        } else {
                            $msg = array('msg' => 'Error al modificar la caja', 'icono' => 'error');
                        }
                    }
                } else {
                    $msg = array('msg' => 'el nombre debe contener un minímo 3 caracteres', 'icono' => 'warning');
                }
            }
        } else {
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function abrirArqueo()
    {
        if (isset($_POST['monto_inicial'])) {
            $monto_inicial = strClean($_POST['monto_inicial']);
            $fecha_apertura = date('Y-m-d');
            $id_usuario = $_SESSION['id_usuario'];
            $id = $_POST['id'];
            if (empty($monto_inicial)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                if ($id == '') {
                    $data = $this->model->registrarArqueo($id_usuario, $monto_inicial, $fecha_apertura);
                    if ($data == "ok") {
                        $msg = array('msg' => 'Caja abierta', 'icono' => 'success');
                    } else if ($data == "existe") {
                        $msg = array('msg' => 'La caja ya esta abierta', 'icono' => 'warning');
                    } else {
                        $msg = array('msg' => 'Error al abrir la caja', 'icono' => 'error');
                    }
                } else {
                    $monto_final = $this->model->getVentas($id_usuario);
                    if ($monto_final['total'] == 0) {
                        $msg = array('msg' => 'No pudes cerrar la caja sin ventas', 'icono' => 'warning');
                    } else {
                        $total_ventas = $this->model->getTotalVentas($id_usuario);
                        $inicial = $this->model->getMontoInicial($id_usuario);
                        $general = $monto_final['total'] + $inicial['monto_inicial'];
                        $data = $this->model->actualizarArqueo($monto_final['total'], $fecha_apertura, $total_ventas['total'], $general, $inicial['id']);
                        if ($data == "ok") {
                            $this->model->actualizarApertura($id_usuario);
                            $msg = array('msg' => 'Caja cerrada', 'icono' => 'success');
                        } else {
                            $msg = array('msg' => 'Error al cerrar la caja', 'icono' => 'error');
                        }
                    }
                }
            }
        } else {
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar(int $id)
    {
        $data = $this->model->editarCaja($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionCaja(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Caja dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al aliminar la caja', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionCaja(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Caja reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al reingresar la caja', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getVentas()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $data['monto_total'] = $this->model->getVentas($id_usuario);
        $data['total_ventas'] = $this->model->getTotalVentas($id_usuario);
        $data['inicial'] = $this->model->getMontoInicial($id_usuario);
        $data['monto_general'] = $data['monto_total']['total'] + $data['inicial']['monto_inicial'];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        //$this->model->eliminarbasura();
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_caja");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['cajas'] = $this->model->getCajas(0);
        $this->views->getView('cajas',  "inactivos", $data);
    }
}
