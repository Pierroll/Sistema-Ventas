<?php

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Administracion extends Controller
{
    private $id_usuario;
    private const PERMITTED_IMAGE_FORMATS = ['png'];
    private const PERMITTED_FILE_FORMATS = ['csv', 'xlsx', 'xls'];
    private const LOGO_NAME = 'logo.png';
    private const LOGO_PATH = 'assets/img/logo.png';

    public function __construct()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $this->id_usuario = $_SESSION['id_usuario'];
        parent::__construct();
    }

    public function index(): void
    {
        $data['permisos'] = $this->model->verificarPermisos($this->id_usuario, "configuracion");
        $data['empresa'] = $this->model->getEmpresa();
        $data['monedas'] = $this->model->getMonedas(1);
        $data['existe'] = !empty($data['permisos']) || $this->id_usuario === 1;
        $this->views->getView('admin', "index", $data);
    }

    public function home(): void
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
        $this->views->getView('admin', "home", $data);
    }

    private function isValidEmail(string $str): bool
    {
        return false !== filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public function modificar(): void
    {
        if (!$this->isValidEmail($_POST['correo'] ?? '')) {
            echo json_encode(['msg' => 'Ingrese un correo válido', 'icono' => 'warning'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $ruc = intval(strClean($_POST['ruc'] ?? ''));
        $nombre = strClean($_POST['nombre'] ?? '');
        $tel = strClean($_POST['telefono'] ?? '');
        $dir = strClean($_POST['direccion'] ?? '');
        $correo = strClean($_POST['correo'] ?? '');
        $mensaje = strClean($_POST['mensaje'] ?? '');
        $moneda = strClean($_POST['moneda'] ?? '');
        $impuesto = strClean($_POST['impuesto'] ?? '');
        $cant_factura = strClean($_POST['cant_factura'] ?? '');
        $site = strClean($_POST['site'] ?? '');
        $id = intval(strClean($_POST['id'] ?? ''));

        $campos = compact('id', 'nombre', 'tel', 'correo', 'dir', 'moneda', 'impuesto', 'cant_factura');
        if ($this->validarCamposModificar($campos)) {
            echo json_encode(['msg' => 'Todos los campos son requeridos', 'icono' => 'warning'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $data = $this->model->modificar($ruc, $nombre, $tel, $correo, $dir, $mensaje, self::LOGO_NAME, $moneda, $impuesto, $cant_factura, $site, $id);
        if ($data === 'ok') {
            $this->procesarCargaLogo();
        } else {
            $msg = ['msg' => 'Error al modificar', 'icono' => 'error'];
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    private function validarCamposModificar(array $campos): bool
    {
        return empty($campos['id'])
            || empty($campos['nombre'])
            || empty($campos['tel'])
            || empty($campos['correo'])
            || empty($campos['dir'])
            || empty($campos['moneda'])
            || empty($campos['impuesto'])
            || empty($campos['cant_factura']);
    }

    private function procesarCargaLogo(): void
    {
        $img = $_FILES['imagen'] ?? [];
        if (empty($img['name'])) {
            $msg = ['msg' => 'Datos modificado', 'icono' => 'success'];
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            return;
        }

        $extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::PERMITTED_IMAGE_FORMATS, true)) {
            $msg = ['msg' => 'Formato de imagen no permitido', 'icono' => 'warning'];
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (move_uploaded_file($img['tmp_name'], self::LOGO_PATH)) {
            $msg = ['msg' => 'Datos modificado', 'icono' => 'success'];
        } else {
            $msg = ['msg' => 'Error al cargar la imagen', 'icono' => 'error'];
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
    }

    public function actualizarGrafico(string $anio): void
    {
        $desde = $anio . '-01-01';
        $hasta = $anio . '-12-31';
        $data['ventas'] = $this->model->getproductosVendidos('ventas', $desde, $hasta, $this->id_usuario);
        $data['compras'] = $this->model->getproductosVendidos('compras', $desde, $hasta, $this->id_usuario);
        echo json_encode($data);
        die();
    }

    public function reporteStock(): void
    {
        $data = $this->model->getStockMinimo();
        echo json_encode($data);
        die();
    }

    public function topProductos(): void
    {
        $data = $this->model->topProductos();
        echo json_encode($data);
        die();
    }

    public function importarProductos(): void
    {
        if (empty($_FILES['b_datos'])) {
            $msg = ['msg' => 'No hay datos', 'icono' => 'error'];
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }

        $archivo = $_FILES['b_datos']['name'];
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

        if (!in_array($extension, self::PERMITTED_FILE_FORMATS, true)) {
            $msg = ['msg' => 'Archivo no permitido', 'icono' => 'warning'];
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }

        $this->procesarImportacion($extension, $_FILES['b_datos']['tmp_name']);
    }

    private function procesarImportacion(string $extension, string $tmpName): void
    {
        $reader = match($extension) {
            'csv' => new Csv(),
            'xls' => new Xls(),
            default => new Xlsx(),
        };

        $spread = $reader->load($tmpName);
        $sheetdata = $spread->getActiveSheet()->toArray();

        if (count($sheetdata) > 1) {
            for ($i = 1; $i < count($sheetdata); $i++) {
                $this->model->importar(
                    $sheetdata[$i][0] ?? '',
                    $sheetdata[$i][1] ?? '',
                    $sheetdata[$i][2] ?? '',
                    $sheetdata[$i][3] ?? '',
                    $sheetdata[$i][4] ?? ''
                );
            }
            $msg = ['msg' => 'Productos importados', 'icono' => 'success'];
        } else {
            $msg = ['msg' => 'El archivo no contiene datos', 'icono' => 'warning'];
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function permisos(): void
    {
        $this->views->getView('admin', "permisos");
    }
}
