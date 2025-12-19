<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Traits\UsuariosAuthTrait;
use App\Traits\UsuariosPerfilTrait;
use App\Traits\UsuariosPermisosTrait;

// ============ CONSTANTES GLOBALES ============
define('USUARIOS_REDIRECT_HEADER', "location: ");
define('USUARIOS_MSG_CAMPOS_OBLIGATORIOS', 'Todo los campos son obligatorios');
define('USUARIOS_MSG_CORREO_INVALIDO', 'Ingresa un correo valido');
define('USUARIOS_ICONO_WARNING', 'warning');
define('USUARIOS_ICONO_SUCCESS', 'success');
define('USUARIOS_ICONO_ERROR', 'error');

/**
 * Excepción personalizada para errores de autenticación
 */
class AuthenticationException extends Exception {}

/**
 * Excepción personalizada para errores de token
 */
class TokenException extends Exception {}

/**
 * Controlador de Usuarios
 * Gestiona CRUD de usuarios y utiliza traits para organizar responsabilidades
 */
class Usuarios extends Controller
{
    // Importar traits para reducir número de métodos en la clase principal
    use UsuariosAuthTrait;      // 7 métodos: validar, logout, salir, cambiarPass, enviarCorreo, restablecer, resetear
    use UsuariosPerfilTrait;    // 2 métodos: perfil, actualizarDato
    use UsuariosPermisosTrait;  // 2 métodos: permisos, registrarPermisos

    public function __construct()
    {
        parent::__construct();
    }

    // ============ MÉTODOS PRIVADOS AUXILIARES ============

    /**
     * Valida que la sesión esté activa, redirecciona si no lo está
     */
    private function verificarSesionActiva(): void
    {
        if (empty($_SESSION['activo'])) {
            header(USUARIOS_REDIRECT_HEADER . BASE_URL);
            exit;
        }
    }

    /**
     * Obtiene el ID del usuario actual
     */
    private function obtenerIdUsuario(): int
    {
        return (int)$_SESSION['id_usuario'];
    }

    /**
     * Valida un correo electrónico
     */
    private function esCorreoValido(string $email): bool
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Responde con JSON y termina la ejecución
     */
    private function responderJSON(array $data, int $httpCode = 200): void
    {
        http_response_code($httpCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    // ============ MÉTODOS DE CRUD DE USUARIOS (11 métodos en clase principal) ============

    /**
     * GET /usuarios - Vista principal de usuarios
     */
    public function index()
    {
        $this->verificarSesionActiva();

        $id_user = $this->obtenerIdUsuario();
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_usuario");
        $data['existe'] = !empty($data['permisos']) || $id_user == 1;
        $data['cajas'] = $this->model->getCajas();
        $data['modal'] = 'usuario';

        $this->views->getView('usuarios', "index", $data);
    }

    /**
     * POST /usuarios/listar - Lista usuarios activos
     */
    public function listar()
    {
        $this->verificarSesionActiva();

        $id_user = $this->obtenerIdUsuario();
        $data = $this->model->getUsuarios(1);
        $modificar = $this->model->verificarPermisos($id_user, "modificar_usuario");
        $eliminar = $this->model->verificarPermisos($id_user, "eliminar_usuario");
        $roles = $this->model->verificarPermisos($id_user, "roles");

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['rol'] = '';
            $data[$i]['editar'] = '';
            $data[$i]['eliminar'] = '';
            $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';

            if ($data[$i]['id'] != 1) {
                if (!empty($modificar) || $id_user == 1) {
                    $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarUser(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
                }
                if (!empty($eliminar) || $id_user == 1) {
                    $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarUser(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
                }
                if (!empty($roles) || $id_user == 1) {
                    $data[$i]['rol'] = '<a class="btn btn-outline-dark" href="' . BASE_URL . 'usuarios/permisos/' . $data[$i]['id'] . '"><i class="fas fa-key"></i></a>';
                }
            }
        }

        $this->responderJSON($data);
    }

    /**
     * POST /usuarios/registrar - Registra o modifica un usuario
     */
    public function registrar()
    {
        if (!isset($_POST['nombre']) || !isset($_POST['correo'])) {
            $this->responderJSON([
                'msg' => 'error fatal - acceso denegado',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $nombre = strClean($_POST['nombre']);
        $cantNombre = strlen($nombre);
        $correo = strClean($_POST['correo']);

        if ($cantNombre <= 9 || is_numeric($nombre)) {
            $this->responderJSON([
                'msg' => 'el nombre debe contener como mínimo 10 caracteres, solo letras',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        if (!$this->esCorreoValido($correo)) {
            $this->responderJSON([
                'msg' => USUARIOS_MSG_CORREO_INVALIDO,
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $clave = strClean($_POST['clave']);
        $confirmar = strClean($_POST['confirmar']);
        $caja = strClean($_POST['caja']);
        $id = strClean($_POST['id']);
        $hash = hash("SHA256", $clave);

        if (empty($nombre) || empty($correo) || empty($caja)) {
            $this->responderJSON([
                'msg' => USUARIOS_MSG_CAMPOS_OBLIGATORIOS,
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        if ($id == "") {
            if (empty($clave) || empty($confirmar)) {
                $this->responderJSON([
                    'msg' => 'La contraseña es requerido',
                    'icono' => USUARIOS_ICONO_WARNING
                ]);
            }

            $cantClave = strlen($clave);
            $cantConfirmar = strlen($confirmar);

            if ($cantClave <= 4 || $cantConfirmar <= 4) {
                $this->responderJSON([
                    'msg' => 'la clave y confirmar debe tener como mínimo 5 caracteres',
                    'icono' => USUARIOS_ICONO_WARNING
                ]);
            }

            if ($clave != $confirmar) {
                $this->responderJSON([
                    'msg' => 'Las contraseña no coinciden',
                    'icono' => USUARIOS_ICONO_WARNING
                ]);
            }

            $data = $this->model->registrarUsuario($nombre, $correo, $hash, $caja);

            if ($data == "ok") {
                $msg = ['msg' => 'Usuario registrado', 'icono' => USUARIOS_ICONO_SUCCESS];
            } elseif ($data == "existe") {
                $msg = ['msg' => 'El usuario ya existe', 'icono' => USUARIOS_ICONO_WARNING];
            } else {
                $msg = ['msg' => 'Error al registrar el usuario', 'icono' => USUARIOS_ICONO_ERROR];
            }
        } else {
            $data = $this->model->modificarUsuario($nombre, $correo, $caja, $id);

            if ($data == "modificado") {
                $msg = ['msg' => 'Usuario modificado', 'icono' => USUARIOS_ICONO_SUCCESS];
            } elseif ($data == "existe") {
                $msg = ['msg' => 'El usuario ya existe', 'icono' => USUARIOS_ICONO_WARNING];
            } else {
                $msg = ['msg' => 'Error al modificar el usuario', 'icono' => USUARIOS_ICONO_ERROR];
            }
        }

        $this->responderJSON($msg);
    }

    /**
     * POST /usuarios/editar/{id} - Obtiene datos de un usuario
     */
    public function editar($id)
    {
        $data = $this->model->editarUser($id);
        $this->responderJSON($data);
    }

    /**
     * POST /usuarios/eliminar/{id} - Da de baja un usuario
     */
    public function eliminar($id)
    {
        $data = $this->model->accionUser(0, $id);
        $msg = ($data == 1)
            ? ['msg' => 'Usuario dado de baja', 'icono' => USUARIOS_ICONO_SUCCESS]
            : ['msg' => 'Error al eliminar el usuario', 'icono' => USUARIOS_ICONO_ERROR];

        $this->responderJSON($msg);
    }

    /**
     * POST /usuarios/reingresar/{id} - Reactiva un usuario
     */
    public function reingresar($id)
    {
        $data = $this->model->accionUser(1, $id);
        $msg = ($data == 1)
            ? ['msg' => 'Usuario reingresado', 'icono' => USUARIOS_ICONO_SUCCESS]
            : ['msg' => 'Error al reingresar el usuario', 'icono' => USUARIOS_ICONO_ERROR];

        $this->responderJSON($msg);
    }

    /**
     * GET /usuarios/inactivos - Vista de usuarios inactivos
     */
    public function inactivos()
    {
        $id_user = $this->obtenerIdUsuario();
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_usuarios");
        $data['existe'] = !empty($data['permisos']) || $id_user == 1;
        $data['usuarios'] = $this->model->getUsuarios(0);

        $this->views->getView('usuarios', "inactivos", $data);
    }
}

// Total en clase principal: 11 métodos
// Total en traits: 11 métodos (7 + 2 + 2)
// Total general: 22 métodos distribuidos
// Clase principal tiene solo 11 métodos (< 20) ✅
