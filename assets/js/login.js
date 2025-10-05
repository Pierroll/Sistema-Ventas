let myModal;

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('myModal')) {
        myModal = new bootstrap.Modal(document.getElementById('myModal'));
    }
})

function frmLogin(e) {
    e.preventDefault();
    const correo = document.getElementById("correo");
    const clave = document.getElementById("clave");
    if (correo.value == "") {
        clave.classList.remove("is-invalid");
        correo.classList.add("is-invalid");
        correo.focus();
    } else if (clave.value == "") {
        correo.classList.remove("is-invalid");
        clave.classList.add("is-invalid");
        clave.focus();
    } else {
        const url = base_url + "usuarios/validar";
        const frm = document.getElementById("frmLogin");
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.upload.addEventListener('progress', function() {
            document.getElementById('btnAccion').textContent = 'Procesando';
        });
        http.send(new FormData(frm));
        http.addEventListener('load', function() {
            document.getElementById('btnAccion').textContent = 'Login';
        });
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                const res = JSON.parse(this.responseText);
                if (res == "ok") {
                    let timerInterval;
                    Swal.fire({
                        title: "Bienvenido al Sistema",
                        html: "Será Redireccionado en <b></b> milisegundos...",
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                            timerInterval = setInterval(() => {
                                const content = Swal.getHtmlContainer();
                                if (content) {
                                    const b = content.querySelector("b");
                                    if (b) {
                                        b.textContent = Swal.getTimerLeft();
                                    }
                                }
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        },
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location = base_url + "administracion/home";
                        }
                    });
                } else {
                    document.getElementById('btnAccion').textContent = 'Login';
                    document.getElementById("alerta").classList.remove("d-none");
                    document.getElementById("alerta").innerHTML = res;
                }
            }
        }
    }
}

function recuperarClave(e) {
    e.preventDefault();
    const correo = document.getElementById('correo');
    if (correo.value == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Aviso!',
            text: 'El correo es requerido',
        })
        correo.focus();
    } else {
        const url = base_url + 'usuarios/enviarCorreo';
        const frm = document.getElementById('frmReset');
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.upload.addEventListener('progress', function() {
            document.getElementById('accion').textContent = 'Procesando ...';
        });
        http.send(new FormData(frm));
        http.addEventListener('load', function() {
            document.getElementById('accion').textContent = 'Restablecer';
            myModal.hide();
        });
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                const res = JSON.parse(this.responseText);
                frm.reset();
                Swal.fire({
                    icon: res.icono,
                    title: 'Aviso!',
                    text: res.msg,
                })
            }
        }
    }
}

function frmRestablecer(e) {
    e.preventDefault();
    const clave = document.getElementById('clave_nueva').value;
    const confirmar = document.getElementById('confirmar').value;
    if (clave == '' || confirmar == '') {
        document.getElementById("alerta").classList.remove("d-none");
        document.getElementById("alerta").textContent = 'Todo los campos son requeridos';
    } else if (clave != confirmar) {
        document.getElementById("alerta").classList.remove("d-none");
        document.getElementById("alerta").textContent = 'Las contraseña no coinciden';
    } else {
        const frm = document.getElementById('frmrestablecer');
        const http = new XMLHttpRequest();
        const url = base_url + "usuarios/resetear";
        http.open("POST", url, true);
        // upload progress event
        http.upload.addEventListener('progress', function(e) {
            document.getElementById('accion').textContent = 'Procesando ...';
        });
        http.send(new FormData(frm));
        http.addEventListener('load', function(e) {
            document.getElementById('accion').textContent = 'Procesando ...';
            frm.reset();
            document.getElementById("alerta").classList.add("d-none");
        });
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                const res = JSON.parse(this.responseText);
                Swal.fire({
                    icon: res.icono,
                    title: 'Aviso!',
                    text: res.msg,
                })
                if (res.icono == 'success') {
                    setTimeout(() => {
                        window.location = base_url;
                    }, 3000);
                }
            }
        }
    }
}