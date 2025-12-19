<?php
class Categorias extends Controller{
    public function __construct() {
        if (empty($_SESSION['activo'])) {
            header("location: ".BASE_URL);
        }
        parent::__construct();
    }
    public function index()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_categoria");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'categoria';
        $this->views->getView('categorias',  "index", $data);
    }
    public function listar()
    {
        $id_user = $_SESSION['id_usuario'];
        $data = $this->model->getCategorias(1);
        $modificar = $this->model->verificarPermisos($id_user, "modificar_categoria");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_categoria");
        for ($i=0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarCat(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarCat(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $nombre = strClean($_POST['nombre']);
        $id = strClean($_POST['id']);
        if (empty($nombre)) {
            $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        }else{
            if (strlen($nombre) < 3) {
                $msg = array('msg' => 'la categoria debe contener minímo 3 caracteres', 'icono' => 'warning');
            } else {
                if ($id == "") {
                    $data = $this->model->registrarCategoria($nombre);
                    if ($data == "ok") {
                        $msg = array('msg' => 'Categoria registrado', 'icono' => 'success');
                    }else if($data == "existe"){
                        $msg = array('msg' => 'La categoria ya existe', 'icono' => 'warning');
                    }else{
                        $msg = array('msg' => 'Error al registrar la categoria', 'icono' => 'error');
                    }
            }else{
                $data = $this->model->modificarCategoria($nombre, $id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Categoria modificado', 'icono' => 'success');
                }else if($data == "existe"){
                    $msg = array('msg' => 'La categoria ya existe', 'icono' => 'warning');
                }else {
                    $msg = array('msg' => 'Error al modificar la categoria', 'icono' => 'error');
                }
            }
            }
            
        }
        } else {
            $msg = array('msg' => 'Error fatal', 'icono' => 'error');
        }
        
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar(int $id)
    {
        $data = $this->model->editarCat($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionCat(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Categoria dado de baja', 'icono' => 'success');
        }else{
            $msg = array('msg' => 'Error al eliminar la categoria', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionCat(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Categoria reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar la categoria', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_categoria");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['categorias'] = $this->model->getCategorias(0);
        $this->views->getView('categorias',  "inactivos", $data);
    }
}
