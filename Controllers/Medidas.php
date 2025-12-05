<?php
class Medidas extends Controller
{
    public function __construct()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_medida");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'medida';
        $this->views->getView('medidas',  "index", $data);
    }
    public function listar()
    {
        $id_user = $_SESSION['id_usuario'];
        $data = $this->model->getMedidas(1);
        $modificar = $this->model->verificarPermisos($id_user, "modificar_medida");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_medida");

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarMed(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarMed(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['nombre']) && isset($_POST['nombre_corto'])) {
            $nombre = strClean($_POST['nombre']);
            $nombre_corto = strClean($_POST['nombre_corto']);
            $id = strClean($_POST['id']);
            if (empty($nombre) || empty($nombre_corto)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                if (strlen($nombre) < 3) {
                    $msg = array('msg' => 'el nombre debe contener un mínimo 3 caracteres', 'icono' => 'warning');
                } else {
                    if (strlen($nombre_corto) < 2) {
                        $msg = array('msg' => 'el nombre corto debe contener un mínimo 2 caracteres', 'icono' => 'warning');
                    } else {
                        if ($id == "") {
                            $data = $this->model->registrarMedida($nombre, $nombre_corto);
                            if ($data == "ok") {
                                $msg = array('msg' => 'Medida registrado', 'icono' => 'success');
                            } else if ($data == "existe") {
                                $msg = array('msg' => 'La medida ya existe', 'icono' => 'warning');
                            } else {
                                $msg = array('msg' => 'Error al registrar la medida', 'icono' => 'error');
                            }
                        } else {
                            $data = $this->model->modificarMedida($nombre, $nombre_corto, $id);
                            if ($data == "modificado") {
                                $msg = array('msg' => 'Medida modificado', 'icono' => 'success');
                            } else if ($data == "existe") {
                                $msg = array('msg' => 'La medida ya existe', 'icono' => 'warning');
                            } else {
                                $msg = array('msg' => 'Error al modificar la medida', 'icono' => 'error');
                            }
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
        $data = $this->model->editarMedida($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionMedida(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Medida dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar la medida', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionMedida(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Medida reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar la media', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_medidas");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['medidas'] = $this->model->getMedidas(0);
        $this->views->getView('medidas',  "inactivos", $data);
    }
}
