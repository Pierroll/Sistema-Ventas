<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Usuarios extends Controller
{
    public function __construct()
    {
        session_start();

        parent::__construct();
    }
    public function index()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "crear_usuario");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['cajas'] = $this->model->getCajas();
        $data['modal'] = 'usuario';
        $this->views->getView('usuarios',  "index", $data);
    }
    public function listar()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $id_user = $_SESSION['id_usuario'];
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
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function validar()
    {
        $correo = strClean($_POST['correo']);
        $clave = strClean($_POST['clave']);
        if (empty($correo) || empty($clave)) {
            $msg = "Los campos estan vacios";
        } else {
            $hash = hash("SHA256", $clave);
            $data = $this->model->getUsuario($correo, $hash);
            if ($data) {
                $_SESSION['id_usuario'] = $data['id'];
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['correo'] = $data['correo'];
                $_SESSION['perfil'] = $data['perfil'];
                $_SESSION['activo'] = true;
                $msg = "ok";
            } else {
                $msg = "Usuario o contraseña incorrecta";
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        if (isset($_POST['nombre']) && isset($_POST['correo'])) {
            $nombre = strClean($_POST['nombre']);
            $cantNombre = strlen($nombre);
            $correo = strClean($_POST['correo']);
            if ($cantNombre > 9 && !is_numeric($nombre)) {
                if ($this->is_valid_email($correo)) {
                    $clave = strClean($_POST['clave']);
                    $confirmar = strClean($_POST['confirmar']);
                    $caja = strClean($_POST['caja']);
                    $id = strClean($_POST['id']);
                    $hash = hash("SHA256", $clave);
                    if (empty($nombre) || empty($correo) || empty($caja)) {
                        $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
                    } else {
                        if ($id == "") {
                            if (!empty($clave) && !empty($confirmar)) {
                                $cantClave = strlen($clave);
                                $cantConfirmar = strlen($confirmar);
                                if ($cantClave > 4 && $cantConfirmar > 4) {
                                    if ($clave != $confirmar) {
                                        $msg = array('msg' => 'Las contraseña no coinciden', 'icono' => 'warning');
                                    } else {
                                        $data = $this->model->registrarUsuario($nombre, $correo, $hash, $caja);
                                        if ($data == "ok") {
                                            $msg = array('msg' => 'Usuario registrado', 'icono' => 'success');
                                        } else if ($data == "existe") {
                                            $msg = array('msg' => 'El usuario ya existe', 'icono' => 'warning');
                                        } else {
                                            $msg = array('msg' => 'Error al registrar el usuario', 'icono' => 'error');
                                        }
                                    }
                                } else {
                                    $msg = array('msg' => 'la clave y confirmar debe tener como minímo 5 caracteres', 'icono' => 'warning');
                                }
                            } else {
                                $msg = array('msg' => 'La contraseña es requerido', 'icono' => 'warning');
                            }
                        } else {
                            $data = $this->model->modificarUsuario($nombre, $correo, $caja, $id);
                            if ($data == "modificado") {
                                $msg = array('msg' => 'Usuario modificado', 'icono' => 'success');
                            } else if ($data == "existe") {
                                $msg = array('msg' => 'El usuario ya existe', 'icono' => 'warning');
                            } else {
                                $msg = array('msg' => 'Error al modificar el usuario', 'icono' => 'error');
                            }
                        }
                    }
                } else {
                    $msg = array('msg' => 'Ingresa un correo valido', 'icono' => 'warning');
                }
            } else {
                $msg = array('msg' => 'el nombre debe contener como minímo 10 caracteres, solo letras', 'icono' => 'warning');
            }
        }else{
            $msg = array('msg' => 'error fatal - acceso denegado', 'icono' => 'warning');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar(int $id)
    {
        $data = $this->model->editarUser($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionUser(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Usuario dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el usuario', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar(int $id)
    {
        $data = $this->model->accionUser(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Usuario reingresado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al reingresar el usuario', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function cambiarPass()
    {
        $actual = strClean($_POST['clave_actual']);
        $nueva = strClean($_POST['clave_nueva']);
        $confirmar = strClean($_POST['confirmar_clave']);
        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            $mensaje = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        } else {
            if ($nueva != $confirmar) {
                $mensaje = array('msg' => 'Las contraseña no coinciden', 'icono' => 'warning');
            } else {
                $id = $_SESSION['id_usuario'];
                $hash = hash("SHA256", $actual);
                $data = $this->model->getPass($hash, $id);
                if (!empty($data)) {
                    $verificar = $this->model->modificarPass(hash("SHA256", $nueva), $id);
                    if ($verificar == 1) {
                        $mensaje = array('msg' => 'Contraseña Modificada', 'icono' => 'success');
                    } else {
                        $mensaje = array('msg' => 'Error al modificar la contraseña', 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'La contraseña actual incorrecta', 'icono' => 'warning');
                }
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function permisos($id)
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $data['datos'] = $this->model->getPermisos();
        $permisos = $this->model->getDetallePermisos($id);
        $data['asignados'] = array();
        foreach ($permisos as $permiso) {
            $data['asignados'][$permiso['id_permiso']] = true;
        }
        $data['id_usuario'] = $id;
        $this->views->getView('usuarios',  "permisos", $data);
    }
    public function registrarPermisos()
    {
        $id_user = strClean($_POST['id_usuario']);
        $permisos = $_POST['permisos'];
        $this->model->deletePermisos($id_user);
        if ($permisos != "") {
            foreach ($permisos as $permiso) {
                $this->model->actualizarPermisos($id_user, $permiso);
            }
        }
        $msg = array('msg' => 'Permisos Modificado', 'icono' => 'success');
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function perfil()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $id_user = $_SESSION['id_usuario'];
        $data = $this->model->editarUser($id_user);
        $this->views->getView('usuarios',  "perfil", $data);
    }
    public function actualizarDato()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . BASE_URL);
        }
        $nombre = strClean($_POST['nombre']);
        $correo = strClean($_POST['correo']);
        $telefono = strClean($_POST['telefono']);
        $direccion = strClean($_POST['direccion']);
        $apellido = strClean($_POST['apellido']);
        $id = $_SESSION['id_usuario'];
        $perfil = $_FILES['imagen'];
        $name = $perfil['name'];
        $tmpname = $perfil['tmp_name'];
        $fecha = date("YmdHis");
        if (!empty($name)) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $formatos_permitidos =  array('png', 'jpeg', 'jpg');
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            if (!in_array($extension, $formatos_permitidos)) {
                $msg = array('msg' => 'Archivo no permitido', 'icono' => 'warning');
            } else {
                $imgNombre = $fecha . ".jpg";
                $destino = "assets/img/users/" . $imgNombre;
            }
        } else {
            $imgNombre = strClean($_POST['foto_actual']);
        }
        if (empty($nombre) || empty($apellido) || empty($correo) || empty($telefono) || empty($direccion)) {
            $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        } else {
            if (!empty($name)) {
                $imgDelete = $this->model->editarUser($id);
                if ($imgDelete['perfil'] != 'avatar.svg') {
                    if (file_exists("assets/img/users/" . $imgDelete['perfil'])) {
                        unlink("assets/img/users/" . $imgDelete['perfil']);
                    }
                }
            }
            $data = $this->model->modificarDato($nombre, $apellido, $correo, $telefono, $direccion, $imgNombre, $id);
            if ($data == 1) {
                if (!empty($name)) {
                    move_uploaded_file($tmpname, $destino);
                }
                $msg = array('msg' => 'Usuario modificado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al modificar el usuario', 'icono' => 'error');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function salir()
    {
        session_destroy();
        header("location: " . BASE_URL);
    }
    public function enviarCorreo()
    {
        if ($this->is_valid_email($_POST['correo'])) {
            $correo = strClean($_POST['correo']);
            $data = $this->model->getCorreo($correo);
            $empresa = $this->model->getEmpresa();
            if (empty($data)) {
                $mensaje = array('msg' => 'El correo no existe, ingrese el correo con la cuál se registro', 'icono' => 'warning');
            } else {
                require 'vendor/autoload.php';
                $mail = new PHPMailer(true);
                try {
                    $token = md5(uniqid());
                    $datos = $this->model->actualizarToken($token, $correo);
                    if ($datos == 'ok') {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = USER_SMTP;                     //SMTP username
                        $mail->Password   = CLAVE_SMTP;                               //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                        $mail->Port       = PUERTO_SMTP;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom($empresa['correo'], $empresa['nombre']);
                        $mail->addAddress($correo);     //Add a recipient
                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = $empresa['nombre'];
                        $mail->Body    = '<h1>Restablecer contraseña</h1>
                    <p>Has pedido restablecer tu contraseña, si no has sido tu puedes omitir este correo
                    <b>Atentamente ' . $empresa['nombre'] . '</b>
                    <h6>Para restablecer has click en el siguiente enlace</h6>
                    ' . BASE_URL . 'usuarios/restablecer/' . $token . '
                    </p>
                    ';
                        $mail->CharSet = 'UTF-8';
                        $mail->send();
                        $mensaje = array('msg' => 'Se ha enviado un correo electrónico con el código para restablecer', 'icono' => 'success');
                    }
                } catch (Exception $e) {
                    $mensaje = array('msg' => $e->getMessage(), 'icono' => 'error');
                }
            }
        } else {
            $mensaje = array('msg' => 'Ingresa un correo valido', 'icono' => 'warning');
        }

        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function restablecer($token)
    {
        $data = $this->model->getToken($token);
        if (empty($data)) {
            header('location: ' . BASE_URL);
        } else {
            $this->views->getView('usuarios',  'restablecer', $token);
        }
    }
    public function resetear()
    {
        $token = strClean($_POST['token']);
        $clave = strClean($_POST['clave_nueva']);
        $confirmar = strClean($_POST['confirmar']);
        if (empty($clave) || empty($confirmar)) {
            $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        } else {
            if ($clave != $confirmar) {
                $msg = array('msg' => 'Las contraseñas no coinciden', 'icono' => 'warning');
            } else {
                $hash = hash("SHA256", $clave);
                $data = $this->model->resetearPass($hash, $token);
                if ($data == 'ok') {
                    $msg = array('msg' => 'Contraseña restablecida con exito', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al restablecer la contraseña', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function inactivos()
    {
        $id_user = $_SESSION['id_usuario'];
        $data['permisos'] = $this->model->verificarPermisos($id_user, "restaurar_usuarios");
        if (!empty($data['permisos']) || $id_user == 1) {
            $data['existe'] = true;
        } else {
            $data['existe'] = false;
        }
        $data['usuarios'] = $this->model->getUsuarios(0);
        $this->views->getView('usuarios',  "inactivos", $data);
    }
    function is_valid_email($str)
    {
        return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
    }
}
