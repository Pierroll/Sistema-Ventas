<?php
class Proveedor extends Controller
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
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_proveedor");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'proveedor';
        $this->views->getView('proveedor',  "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getProveedor(1);
        $id_user = $_SESSION['id_usuario'];
        $modificar = $this->model->verificarPermisos($id_user, "modificar_proveedor");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_proveedor");

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarPr(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarPr(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['ruc']) && isset($_POST['nombre']) && isset($_POST['telefono'])) {
            $ruc = strClean($_POST['ruc']);
            $nombre = strClean($_POST['nombre']);
            $telefono = strClean($_POST['telefono']);
            $direccion = strClean($_POST['direccion']);
            $id = strClean($_POST['id']);
            if (empty($ruc) || empty($nombre) || empty($telefono) || empty($direccion)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                if (strlen($ruc) < 8) {
                    $msg = array('msg' => 'La identidad debe contener un mínimo 8 caracteres', 'icono' => 'warning');
                } else {
                    if (strlen($nombre) < 10) {
                        $msg = array('msg' => 'El nombre debe contener un mínimo 10 caracteres', 'icono' => 'warning');
                    } else {
                        if (strlen($telefono) < 9) {
                            $msg = array('msg' => 'El teléfono debe contener un minímo 9 caracteres', 'icono' => 'warning');
                        } else {
                            if (strlen($direccion) < 5) {
                                $msg = array('msg' => 'La dirección debe contener un minímo 5 caracteres', 'icono' => 'warning');
                            } else {
                                if ($id == "") {
                                    $data = $this->model->registrar($ruc, $nombre, $telefono, $direccion);
                                    if ($data == "ruc") {
                                        $msg = array('msg' => 'el ruc ya existe', 'icono' => 'warning');
                                    } else if ($data == "telefono") {
                                        $msg = array('msg' => 'el telefono ya existe', 'icono' => 'warning');
                                    } else if ($data == "ok") {
                                        $msg = array('msg' => 'Proveedor registrado', 'icono' => 'success');
                                    } else {
                                        $msg = array('msg' => 'error al registrar', 'icono' => 'error');
                                    }
                                } else {
                                    $data = $this->model->modificar($ruc, $nombre, $telefono, $direccion, $id);
                                    if ($data == "ruc") {
                                        $msg = array('msg' => 'el ruc ya existe', 'icono' => 'warning');
                                    } else if ($data == "telefono") {
                                        $msg = array('msg' => 'el telefono ya existe', 'icono' => 'warning');
                                    } else if ($data == "ok") {
                                        $msg = array('msg' => 'Proveedor registrado', 'icono' => 'success');
                                    } else {
                                        $msg = array('msg' => 'error al registrar', 'icono' => 'error');
                                    }
                                }
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
        $data = $this->model->editarpr($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionpr(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Proveedor dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el proveedor', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionpr(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'proveedor reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar el proveedor', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function buscarProveedor()
    {
        if (isset($_GET['pr'])) {
            $data = $this->model->buscarProveedor($_GET['pr']);
            $datos = array();
            foreach ($data as $row) {
                $data['id'] = $row['id'];
                $data['label'] = $row['nombre'] . ' - ' . $row['direccion'];
                $data['value'] = $row['nombre'];
                $data['direccion'] = $row['direccion'];
                array_push($datos, $data);
            }
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_proveedor");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['proveedor'] = $this->model->getProveedor(0);
        $this->views->getView('proveedor',  "inactivos", $data);
    }
}
