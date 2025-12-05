<?php
class Productos extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            http_response_code(401);
            echo json_encode(array('success' => false, 'msg' => 'No autenticado'));
            die();
        }
        parent::__construct();
    }

    // ============ MÉTODOS PARA TESTSPRITE ============

    /**
     * GET /productos - Listar todos los productos activos
     */
    public function index()
    {
        try {
            $data = $this->model->getProductos(1);
            http_response_code(200);
            echo json_encode(array(
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'msg' => 'Error al obtener productos'));
        }
        die();
    }

    /**
     * GET /productos/detalle - Obtener detalle de un producto
     */
    public function detalle()
    {
        try {
            $id_producto = $_GET['id'] ?? null;

            if (empty($id_producto)) {
                http_response_code(400);
                echo json_encode(array('success' => false, 'msg' => 'ID producto requerido'));
                die();
            }

            $producto = $this->model->getProductos($id_producto);

            if (empty($producto)) {
                http_response_code(404);
                echo json_encode(array('success' => false, 'msg' => 'Producto no encontrado'));
                die();
            }

            http_response_code(200);
            echo json_encode(array(
                'success' => true,
                'data' => $producto
            ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
        }
        die();
    }

    // ============ MÉTODOS EXISTENTES (PARA NAVEGADOR) ============

    public function admin()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_producto");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['medidas'] = $this->model->getMedidas();
        $data['categorias'] = $this->model->getCategorias();
        $data['modal'] = 'producto';
        $this->views->getView('productos',  "index", $data);
    }

    public function listar()
    {
        $id_user = $_SESSION['id_usuario'];
        $data = $this->model->getProductos(1);
        $date = date('Y-m-d');
        $modificar = $this->model->verificarPermisos($id_user, "modificar_producto");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_producto");
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['date'] = $date;
            $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . BASE_URL . "assets/img/pro/" . $data[$i]['foto'] . '" width="50">';
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['subTotal'] = '<span class="badge bg-info">'.number_format($data[$i]['cantidad'] * $data[$i]['precio_venta'], 2).'</span>';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
            if (!empty($modificar) || $id_user == 1) {
                $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarPro(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            }
            if (!empty($eliminar) || $id_user == 1) {
                $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarPro(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['codigo']) && isset($_POST['descripcion']) && isset($_POST['precio_compra'])) {
            $codigo = strClean($_POST['codigo']);
            $nombre = strClean($_POST['descripcion']);
            $precio_compra = strClean($_POST['precio_compra']);
            $precio_venta = strClean($_POST['precio_venta']);
            $categoria = strClean($_POST['categoria']);
            $medida = strClean($_POST['medida']);
            $id = strClean($_POST['id']);
            $img = $_FILES['imagen'];
            $name = $img['name'];
            $tmpname = $img['tmp_name'];
            $fecha = date("YmdHis");
            if (empty($codigo) || empty($nombre) || empty($precio_compra) || empty($precio_venta)
            || empty($categoria) || empty($medida)) {
                $msg = array('msg' => 'Todo los campos con * son obligatorios', 'icono' => 'warning');
            }else{
                if (strlen($codigo) < 8) {
                    $msg = array('msg' => 'el código debe contener un mínimo 8 caracteres', 'icono' => 'warning');
                } else {
                    if (strlen($nombre) < 5) {
                        $msg = array('msg' => 'El nombre debe contener un mínimo 5 caracteres', 'icono' => 'warning');
                    } else {
                        if (strlen($precio_compra) < 1) {
                            $msg = array('msg' => 'El precio compra debe contener un minímo 1 caracter', 'icono' => 'warning');
                        } else {
                            if (strlen($precio_venta) < 1) {
                                $msg = array('msg' => 'La precio venta debe contener un minímo 1 caracter', 'icono' => 'warning');
                            } else {
                                if (!empty($name)) {
                                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                                    $formatos_permitidos =  array('png', 'jpeg', 'jpg');
                                    if (!in_array($extension, $formatos_permitidos)) {
                                        $msg = array('msg' => 'Archivo no permitido', 'icono' => 'warning');
                                    } else {
                                        $imgNombre = $fecha . ".jpg";
                                        $destino = "assets/img/pro/" . $imgNombre;
                                    }
                                }else if(!empty($_POST['foto_actual']) && empty($name)){
                                    $imgNombre = $_POST['foto_actual'];
                                }else{
                                    $imgNombre = "default.png";
                                }
                                if ($id == "") {
                                        $data = $this->model->registrarProducto($codigo, $nombre, $precio_compra, $precio_venta, $medida, $categoria, $imgNombre);
                                        if ($data == 0) {
                                            $msg = array('msg' => 'El producto ya existe', 'icono' => 'warning');
                                        } else if ($data > 0) {
                                            if (!empty($name)) {
                                                move_uploaded_file($tmpname, $destino);
                                            }
                                            $msg = array('msg' => 'Producto registrado', 'icono' => 'success');
                                        } else {
                                            $msg = array('msg' => 'Error al registrar el producto', 'icono' => 'error');
                                        }
                                }else{
                                    $imgDelete = $this->model->editarPro($id);
                                    if ($imgDelete['foto'] != 'default.png') {
                                        if (file_exists("assets/img/pro/" . $imgDelete['foto'])) {
                                            unlink("assets/img/pro/" . $imgDelete['foto']);
                                        }
                                    }
                                    $data = $this->model->modificarProducto($codigo, $nombre, $precio_compra, $precio_venta, $medida, $categoria, $imgNombre, $id);
                                    if ($data == "modificado") {
                                        if (!empty($name)) {
                                            move_uploaded_file($tmpname, $destino);
                                        }
                                        $msg = array('msg' => 'Producto modificado', 'icono' => 'success');
                                    } else if ($data == "existe") {
                                        $msg = array('msg' => 'El producto ya existe', 'icono' => 'warning');
                                    } else {
                                        $msg = array('msg' => 'Error al modificar el producto', 'icono' => 'error');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $msg = array('msg' => 'error fatal', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function editar($id)
    {
        $data = $this->model->editarPro($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar($id)
    {
        $data = $this->model->accionPro(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Producto dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reingresar($id)
    {
        $data = $this->model->accionPro(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Producto reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error la reingresar el producto', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function inventario()
    {
        $id_user = $_SESSION['id_usuario'];
        $inventario = $this->model->verificarPermisos($id_user, "inventario");
        $reporte = $this->model->verificarPermisos($id_user, "reporte_pdf_inventario");
        if (!empty($inventario) || $id_user == 1) {
            $data['inventario'] = true;
        } else {
            $data['inventario'] = false;
        }
        if (!empty($reporte) || $id_user == 1) {
            $data['reporte'] = true;
        } else {
            $data['reporte'] = false;
        }
        $data['modal'] = 'inventario';
        $this->views->getView('productos',  "inventario", $data);
    }

    public function registrarInventario()
    {
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "inventario");
        if (!$perm && $id_user != 1) {
            header('Location: Administracion/permisos');
        } else {
            $agregar = strClean($_POST['agregar']);
            $id = strClean($_POST['id']);
            $fecha = date('Y-m-d');
            if (empty($id) || empty($agregar)) {
                $msg = array('msg' => 'Todo los campos con * son obligatorios', 'icono' => 'warning');
            } else {
                if (is_numeric($agregar)) {
                    if ($agregar > 0) {
                        $data = $this->model->ingresarEntrada($id, $id_user, $agregar, $fecha);
                    } else {
                        $data = $this->model->ingresarSalida($id, $id_user, abs($agregar), $fecha);
                    }
                    if ($data == 1) {
                        $cantidad = $this->model->editarPro($id);
                        $cant_total = $cantidad['cantidad'] + $agregar;
                        $this->model->actualizarStock($cant_total, $id);
                        $msg = array('msg' => 'Cantidad del producto Ajustado', 'icono' => 'success');
                    } else {
                        $msg = array('msg' => 'Error al ajustar', 'icono' => 'error');
                    }
                } else {
                    $msg = array('msg' => 'Error ingresa un número valido', 'icono' => 'error');
                }
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function listarInventario()
    {
        $data = $this->model->getInventarios();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_producto");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['productos'] = $this->model->getProductos(0);
        $this->views->getView('productos',  "inactivos", $data);
    }
}
