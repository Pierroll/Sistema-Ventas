<?php
class Landing extends Controller
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
        $data['permisos'] = $this->model->verificarPermisos($id_user, "landing");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'landing';
        $this->views->getView('landing',  "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getLanding();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['control'] == 1) {
                $data[$i]['accion'] = '<a class="btn btn-outline-success" href="https://wa.me/'.$data[$i]['telefono'].'" target="_blank"><i class="fab fa-whatsapp"></i></a>';
                $data[$i]['estado'] = '<span class="badge bg-success">Cliente</span>';
            } else {
                $data[$i]['estado'] = '';
                $data[$i]['accion'] = '<div>
                <a class="btn btn-outline-success" href="https://wa.me/'.$data[$i]['telefono'].'" target="_blank"><i class="fab fa-whatsapp"></i></a>
                <button class="btn btn-outline-primary" type="button" onclick="btnEditarLan(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>
                <button class="btn btn-outline-danger" type="button" onclick="btnEliminarLan(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>
                <button class="btn btn-outline-info" type="button" onclick="procesarRegistro(' . $data[$i]['id'] . ');"><i class="fas fa-spinner"></i></button>
                </div>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['pagina']) && isset($_POST['nombre']) && isset($_POST['telefono'])) {
            $pagina = strClean($_POST['pagina']);
            $nombre = strClean($_POST['nombre']);
            $telefono = strClean($_POST['telefono']);
            $correo = strClean($_POST['correo']);
            $negocio = strClean($_POST['negocio']);
            $hora = date('H:i:s');
            $fecha = date('Y-m-d');
            $negocio = strClean($_POST['negocio']);
            $id = strClean($_POST['id']);
            if (empty($pagina) || empty($nombre) || empty($telefono) || empty($correo)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                if ($id == "") {
                    $data = $this->model->registrar($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio);
                    if ($data > 0) {
                        $msg = array('msg' => 'Landing registrado', 'icono' => 'success', 'id_cliente' => $data);
                    } else {
                        $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
                    }
                } else {
                    $data = $this->model->modificar($hora, $fecha, $pagina, $nombre, $telefono, $correo, $negocio, $id);
                    if ($data == "ok") {
                        $msg = array('msg' => 'Landing modificado', 'icono' => 'success');
                    } else {
                        $msg = array('msg' => 'Error al modificar', 'icono' => 'error');
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
        $data = $this->model->editar($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accion(2, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Landing dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }


    public function agregarCliente()
    {
        if (isset($_POST['dni']) && isset($_POST['direccion']) && isset($_POST['id'])) {
            $dni = strClean($_POST['dni']);
            $direccion = strClean($_POST['direccion']);
            $id = strClean($_POST['id']);

            if (empty($dni) || empty($direccion) || empty($id)) {
                $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
            } else {
                $datos = $this->model->editar($id);
                $data = $this->model->registrarCliente($dni, $datos['nombre'], $datos['telefono'], $direccion);
                if ($data == "dni") {
                    $msg = array('msg' => 'el dni ya existe', 'icono' => 'warning');
                } else if ($data == "telefono") {
                    $msg = array('msg' => 'el telefono ya existe', 'icono' => 'warning');
                } else if ($data > 0) {
                    $this->model->accion(1, $id);
                    $msg = array('msg' => 'Cliente registrado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al registrar el cliente', 'icono' => 'error');
                }
            }
        } else {
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
}
