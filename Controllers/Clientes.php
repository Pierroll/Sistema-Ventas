<?php
class Clientes extends Controller{
    public function __construct() {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: ".BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_cliente");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'cliente';
        $this->views->getView('clientes',  "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getClientes(1);
        $id_user = $_SESSION['id_usuario'];
        $modificar = $this->model->verificarPermisos($id_user, "modificar_cliente");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_cliente");
        
        for ($i=0; $i < count($data); $i++) { 
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarCli(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarCli(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['dni']) && isset($_POST['nombre']) && isset($_POST['telefono'])) {
            $dni = strClean($_POST['dni']);
            $nombre = strClean($_POST['nombre']);
            $telefono = strClean($_POST['telefono']);
            $direccion = strClean($_POST['direccion']);
            $id = strClean($_POST['id']);
            if (empty($dni) || empty($nombre) || empty($telefono) || empty($direccion)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            }else{
                if (strlen($dni) < 8) {
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
                                    $data = $this->model->registrarCliente($dni, $nombre, $telefono, $direccion);
                                    if ($data == "dni") {
                                        $msg = array('msg' => 'el dni ya existe', 'icono' => 'warning');
                                    } else if ($data == "telefono") {
                                        $msg = array('msg' => 'el telefono ya existe', 'icono' => 'warning');
                                    }else if ($data > 0) {
                                        $msg = array('msg' => 'Cliente registrado', 'icono' => 'success', 'id_cliente' => $data);
                                    } else {
                                        $msg = array('msg' => 'Error al registrar el cliente', 'icono' => 'error');
                                    }
                                }else{
                                    $data = $this->model->modificarCliente($dni, $nombre, $telefono, $direccion, $id);
                                    if ($data == "dni") {
                                        $msg = array('msg' => 'el dni ya existe', 'icono' => 'warning');
                                    } else if ($data == "telefono") {
                                        $msg = array('msg' => 'el telefono ya existe', 'icono' => 'warning');
                                    }else if ($data == "ok") {
                                        $msg = array('msg' => 'Cliente modificado', 'icono' => 'success');
                                    } else {
                                        $msg = array('msg' => 'Error al modificar el cliente', 'icono' => 'error');
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
        $data = $this->model->editarCli($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionCli(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Cliente dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el cliente', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionCli(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Cliente reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar el cliente', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function buscarCliente()
    {
        if (isset($_GET['cli'])) {
            $data = $this->model->buscarCliente($_GET['cli']);
            $datos = array();
            foreach ($data as $row) {
                $data['id'] = $row['id'];
                $data['label'] = $row['dni'] . ' - ' . $row['nombre'] . ' - ' . $row['direccion'];
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
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_cliente");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['clientes'] = $this->model->getClientes(0);
        $this->views->getView('clientes',  "inactivos", $data);
    }
}
