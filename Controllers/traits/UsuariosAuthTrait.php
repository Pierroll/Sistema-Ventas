<?php

/**
 * Trait para métodos de autenticación y recuperación de contraseña
 * Agrupa métodos relacionados con login, logout y reseteo de contraseña
 */
namespace App\Traits;


trait UsuariosAuthTrait
{
    /**
     * POST /usuarios/validar - Valida login
     */
    public function validar()
    {
        $correo = strClean($_POST['correo'] ?? '');
        $clave = strClean($_POST['clave'] ?? '');

        if (empty($correo) || empty($clave)) {
            $this->responderJSON([
                'success' => false,
                'msg' => 'Correo y contraseña son requeridos'
            ], 400);
        }

        if (!$this->esCorreoValido($correo)) {
            $this->responderJSON([
                'success' => false,
                'msg' => USUARIOS_MSG_CORREO_INVALIDO
            ], 400);
        }

        $hash = hash("SHA256", $clave);
        $data = $this->model->getUsuario($correo, $hash);

        if (empty($data)) {
            $this->responderJSON([
                'success' => false,
                'msg' => 'Usuario o contraseña incorrecta'
            ], 401);
        }

        $_SESSION['id_usuario'] = $data['id'];
        $_SESSION['nombre'] = $data['nombre'];
        $_SESSION['correo'] = $data['correo'];
        $_SESSION['perfil'] = $data['perfil'];
        $_SESSION['activo'] = true;

        $this->responderJSON([
            'success' => true,
            'msg' => 'ok',
            'id_usuario' => $data['id']
        ]);
    }

    /**
     * POST /usuarios/logout - Cierra sesión
     */
    public function logout()
    {
        session_destroy();
        $this->responderJSON([
            'success' => true,
            'msg' => 'Sesión cerrada'
        ]);
    }

    /**
     * GET /usuarios/salir - Cierra sesión y redirecciona
     */
    public function salir()
    {
        session_destroy();
        header(USUARIOS_REDIRECT_HEADER . BASE_URL);
        exit;
    }

    /**
     * POST /usuarios/cambiarPass - Cambiar contraseña del usuario logueado
     */
    public function cambiarPass()
    {
        $actual = strClean($_POST['clave_actual']);
        $nueva = strClean($_POST['clave_nueva']);
        $confirmar = strClean($_POST['confirmar_clave']);

        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            $this->responderJSON([
                'msg' => USUARIOS_MSG_CAMPOS_OBLIGATORIOS,
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        if ($nueva != $confirmar) {
            $this->responderJSON([
                'msg' => 'Las contraseña no coinciden',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $id = $this->obtenerIdUsuario();
        $hash = hash("SHA256", $actual);
        $data = $this->model->getPass($hash, $id);

        if (empty($data)) {
            $this->responderJSON([
                'msg' => 'La contraseña actual incorrecta',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $verificar = $this->model->modificarPass(hash("SHA256", $nueva), $id);
        $mensaje = ($verificar == 1)
            ? ['msg' => 'Contraseña Modificada', 'icono' => USUARIOS_ICONO_SUCCESS]
            : ['msg' => 'Error al modificar la contraseña', 'icono' => USUARIOS_ICONO_ERROR];

        $this->responderJSON($mensaje);
    }

    /**
     * POST /usuarios/enviarCorreo - Envía correo para recuperar contraseña
     */
    public function enviarCorreo()
    {
        if (!$this->esCorreoValido($_POST['correo'])) {
            $this->responderJSON([
                'msg' => USUARIOS_MSG_CORREO_INVALIDO,
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $correo = strClean($_POST['correo']);
        $data = $this->model->getCorreo($correo);
        $empresa = $this->model->getEmpresa();

        if (empty($data)) {
            $this->responderJSON([
                'msg' => 'El correo no existe, ingrese el correo con la cuál se registro',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        require_once 'vendor/autoload.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $token = bin2hex(random_bytes(32));
            $datos = $this->model->actualizarToken($token, $correo);

            if ($datos != 'ok') {
                throw new TokenException('Error al generar token');
            }

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = HOST_SMTP;
            $mail->SMTPAuth = true;
            $mail->Username = USER_SMTP;
            $mail->Password = CLAVE_SMTP;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = PUERTO_SMTP;

            $mail->setFrom($empresa['correo'], $empresa['nombre']);
            $mail->addAddress($correo);
            $mail->isHTML(true);
            $mail->Subject = $empresa['nombre'];
            $mail->Body = '<h1>Restablecer contraseña</h1>
                <p>Has pedido restablecer tu contraseña, si no has sido tu puedes omitir este correo
                <b>Atentamente ' . $empresa['nombre'] . '</b>
                <h6>Para restablecer has click en el siguiente enlace</h6>
                ' . BASE_URL . 'usuarios/restablecer/' . $token . '
                </p>';
            $mail->CharSet = 'UTF-8';
            $mail->send();

            $mensaje = [
                'msg' => 'Se ha enviado un correo electrónico con el código para restablecer',
                'icono' => USUARIOS_ICONO_SUCCESS
            ];
        } catch (\Exception $e) {
            $mensaje = [
                'msg' => $e->getMessage(),
                'icono' => USUARIOS_ICONO_ERROR
            ];
        }

        $this->responderJSON($mensaje);
    }

    /**
     * GET /usuarios/restablecer/{token} - Muestra formulario para restablecer contraseña
     */
    public function restablecer($token)
    {
        $data = $this->model->getToken($token);

        if (empty($data)) {
            header(USUARIOS_REDIRECT_HEADER . BASE_URL);
            exit;
        }

        $this->views->getView('usuarios', 'restablecer', $token);
    }

    /**
     * POST /usuarios/resetear - Restablece la contraseña con el token
     */
    public function resetear()
    {
        $token = strClean($_POST['token']);
        $clave = strClean($_POST['clave_nueva']);
        $confirmar = strClean($_POST['confirmar']);

        if (empty($clave) || empty($confirmar)) {
            $this->responderJSON([
                'msg' => USUARIOS_MSG_CAMPOS_OBLIGATORIOS,
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        if ($clave != $confirmar) {
            $this->responderJSON([
                'msg' => 'Las contraseñas no coinciden',
                'icono' => USUARIOS_ICONO_WARNING
            ]);
        }

        $hash = hash("SHA256", $clave);
        $data = $this->model->resetearPass($hash, $token);

        $msg = ($data == 'ok')
            ? ['msg' => 'Contraseña restablecida con exito', 'icono' => USUARIOS_ICONO_SUCCESS]
            : ['msg' => 'Error al restablecer la contraseña', 'icono' => USUARIOS_ICONO_ERROR];

        $this->responderJSON($msg);
    }
}
