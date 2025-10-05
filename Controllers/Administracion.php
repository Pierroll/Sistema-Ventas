<?php

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Administracion extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $this->id_usuario = $_SESSION['id_usuario'];
        parent::__construct();
    }
    public function index()
    {

        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "configuracion");
        $data['empresa'] = $this->model->getEmpresa();
        $data['monedas'] = $this->model->getMonedas(1);
        if (!empty($data['permisos']) || $this->id_usuario == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $this->views->getView('admin', "index", $data);
    }
    public function home()
    {
        $data['usuarios'] = $this->model->getDatos('usuarios');
        $data['clientes'] = $this->model->getDatos('clientes');
        $data['productos'] = $this->model->getDatos('productos');
        $data['categorias'] = $this->model->getDatos('categorias');
        $data['medidas'] = $this->model->getDatos('medidas');        
        $data['monto_total'] = $this->model->getMontoCaja($this->id_usuario);
        $data['inicial'] = $this->model->getMontoInicial($this->id_usuario);
        $general = (!empty($data['monto_total']['total'])) ? $data['monto_total']['total'] : 0;
        $inicial = (!empty($data['inicial']['monto_inicial'])) ? $data['inicial']['monto_inicial'] : 0;
        $data['monto_general'] = number_format($general + $inicial, 2, '.', ',');
        $data['ventas'] = $this->model->getVentas('ventas', $this->id_usuario);
        $data['compras'] = $this->model->getVentas('compras', $this->id_usuario);
        $data['empresa'] = $this->model->getEmpresaMoneda();
        $this->views->getView('admin',  "home", $data);
    }
    function is_valid_email($str)
    {
        return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
    }
    public function modificar()
    {
        if ($this->is_valid_email($_POST['correo'])) {
            $ruc = intval(strClean($_POST['ruc']));
            $nombre = strClean($_POST['nombre']);
            $tel = strClean($_POST['telefono']);
            $dir = strClean($_POST['direccion']);
            $correo = strClean($_POST['correo']);
            $mensaje = strClean($_POST['mensaje']);
            $moneda = strClean($_POST['moneda']);
            $impuesto = strClean($_POST['impuesto']);
            $cant_factura = strClean($_POST['cant_factura']);
            $site = strClean($_POST['site']);
            $id = intval(strClean($_POST['id']));
            $img = $_FILES['imagen'];
            $tmpName = $img['tmp_name'];
            if (empty($id) || empty($nombre) || empty($tel) || empty($correo) || empty($dir) || empty($moneda) || empty($impuesto) || empty($cant_factura)) {
                $msg = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
            } else {
                $name = "logo.png";
                $destino = 'assets/img/logo.png';
                $data = $this->model->modificar($ruc, $nombre, $tel, $correo, $dir, $mensaje, $name, $moneda, $impuesto, $cant_factura, $site, $id);
                if ($data == 'ok') {
                    if (!empty($img['name'])) {
                        $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
                        $formatos_permitidos =  array('png');
                        $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
                        if (!in_array($extension, $formatos_permitidos)) {
                            $msg = array('msg' => 'Imagen no permitido', 'icono' => 'warning');
                        } else {
                            move_uploaded_file($tmpName, $destino);
                            $msg = array('msg' => 'Datos modificado', 'icono' => 'success');
                        }
                    } else {
                        $msg = array('msg' => 'Datos modificado', 'icono' => 'success');
                    }
                } else {
                    $msg = array('msg' => 'Error al modificar', 'icono' => 'error');
                }
            }
        } else {
            $msg = array('msg' => 'Ingrese un correo valido', 'icono' => 'warning');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function actualizarGrafico($anio)
    {
        $desde = $anio . '-01-01';
        $hasta = $anio . '-12-31';
        
        $data['ventas'] = $this->model->getproductosVendidos('ventas', $desde, $hasta, $this->id_usuario);
        $data['compras'] = $this->model->getproductosVendidos('compras', $desde, $hasta, $this->id_usuario);
        echo json_encode($data);
        die();
    }
    public function reporteStock()
    {
        $data = $this->model->getStockMinimo();
        echo json_encode($data);
        die();
    }
    public function topProductos()
    {
        $data = $this->model->topProductos();
        echo json_encode($data);
        die();
    }
    public function importarProductos()
    {
        if (empty($_FILES['b_datos'])) {
            $msg = array('msg' => 'No hay datos', 'icono' => 'error');
        } else {
            $archivo = $_FILES['b_datos']['name'];
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            $formatos_permitidos =  array('csv', 'xlsx', 'xls');
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            if (!in_array($extension, $formatos_permitidos)) {
                $msg = array('msg' => 'Archivo no permitido', 'icono' => 'warning');
            } else {
                require 'vendor/autoload.php';
                if ($extension == 'csv') {
                    $reader = new Csv();
                } else if ($extension == 'xls') {
                    $reader = new Xls();
                } else {
                    $reader = new Xlsx();
                }
                $spread = $reader->load($_FILES['b_datos']['tmp_name']);
                $sheetdata = $spread->getActiveSheet()->toArray();
                $data = count($sheetdata);
                if ($data > 1) {
                    for ($i = 1; $i < $data; $i++) {
                        $this->model->importar($sheetdata[$i][0], $sheetdata[$i][1], $sheetdata[$i][2], $sheetdata[$i][3], $sheetdata[$i][4]);
                    }
                    $msg = array('msg' => 'Productos importado', 'icono' => 'success');
                }
            }
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function permisos()
    {
        $this->views->getView('admin',  "permisos");
    }
    //Monedas
    public function moneda()
    {
        
        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "crear_moneda");
        if (!empty($data['permisos']) || $this->id_usuario == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['modal'] = 'moneda';
        $this->views->getView('admin',  'moneda', $data);
    }
    public function listarMonedas()
    {
        
        $data = $this->model->getMonedas(1);
        $modificarMoneda = $this->model->verificarPermisos($this->id_usuario, "modificar_moneda");
        $eliminar_moneda = $this->model->verificarPermisos($this->id_usuario, "eliminar_moneda");
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificarMoneda) || $this->id_usuario == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarMoneda(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar_moneda) || $this->id_usuario == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarMoneda(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrarMoneda()
    {
        if (isset($_POST['simbolo']) && isset($_POST['nombre'])) {
            $simbolo = strClean($_POST['simbolo']);
            $cantSimbolo = strlen($simbolo);
            $nombre = strClean($_POST['nombre']);
            $cantNombre = strlen($nombre);
            $id = strClean($_POST['id']);
            if (empty($simbolo) || empty($nombre)) {
                $msg = array('msg' => 'todo los campos con * son requeridos', 'icono' => 'warning');
            } else {
                if ($cantSimbolo > 0 && !is_numeric($simbolo)) {
                    if ($cantNombre > 2 && !is_numeric($nombre)) {
                        if ($id == '') {
                            $data = $this->model->registrarMoneda($simbolo, $nombre);
                            if ($data == 'ok') {
                                $msg = array('msg' => 'Moneda registrado', 'icono' => 'success');
                            } else if ($data == "existe") {
                                $msg = array('msg' => 'La moneda ya existe', 'icono' => 'warning');
                            } else {
                                $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
                            }
                        } else {
                            $data = $this->model->modificarMoneda($simbolo, $nombre, $id);
                            if ($data == "modificado") {
                                $msg = array('msg' => 'Moneda modificado', 'icono' => 'success');
                            } else if ($data == "existe") {
                                $msg = array('msg' => 'La moneda ya existe', 'icono' => 'warning');
                            } else {
                                $msg = "Error al modificar la moneda";
                                $msg = array('msg' => 'Error al modificar la moneda', 'icono' => 'error');
                            }
                        }
                    } else {
                        $msg = array('msg' => 'El nombre debe tener un mínimo 3 caracteres, solo letras', 'icono' => 'warning');
                    }
                } else {
                    $msg = array('msg' => 'El simbolo debe tener un mínimo 1 caracter, solo letras', 'icono' => 'warning');
                }
            }
        } else {
            $msg = array('msg' => 'error fatal - acceso denegado', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editarMoneda(int $id)
    {
        $data = $this->model->editarMoneda($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminarMoneda(int $id)
    {
        $data = $this->model->accionMoneda(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Moneda dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el cliente', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresarMoneda(int $id)
    {
        $data = $this->model->accionMoneda(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Moneda reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al reingresar la moneda', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        
        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "restaurar_moneda");
        if (!empty($data['permisos']) || $this->id_usuario == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['monedas'] = $this->model->getMonedas(0);
        $this->views->getView('admin',  "inactivos", $data);
    }
}
