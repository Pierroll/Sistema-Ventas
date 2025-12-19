let tblUsuarios;
const frm = document.getElementById('formulario');
const btn = document.getElementById('btnAccion');
const nombre = document.getElementById('nombre');
const correo = document.getElementById('correo');
const caja = document.getElementById('caja');
const id_user = document.getElementById('id');
const clave = document.getElementById('clave');
const confirmar = document.getElementById('confirmar');
document.addEventListener('DOMContentLoaded', function() {
    tblUsuarios = $('#tblUsuarios').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'usuarios/listar',
            dataSrc: '',
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'correo' },
            { data: 'caja' },
            { data: 'estado' },
            { data: 'editar' },
            { data: 'eliminar' },
            { data: 'rol' },
        ],
        language: {
            url: base_url + 'assets/js/i18n/Spanish.json',
        },
        dom,
        buttons,
        resonsieve: true,
        bDestroy: true,
        iDisplayLength: 10,
        order: [
            [0, 'desc']
        ],
    }); //Fin de la tabla usuarios
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            nombre: {
                required: true,
                minlength: 10,
            },
            caja: 'required',
            clave: {
                required: true,
                minlength: 5,
            },
            confirmar: {
                required: true,
                minlength: 5,
                equalTo: '#clave',
            },
            correo: {
                required: true,
                email: true,
            },
        },
        messages: {
            caja: 'La caja es requerido',
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 10 caracteres',
            },
            correo: {
                required: 'El correo es requerido',
                email: 'Ingresa un correo valido',
            },
            clave: {
                required: 'La contraseña es requerido',
                minlength: 'La contraseña debe contener minímo 5 caracteres',
            },
            confirmar: {
                required: 'El confirmar clave es requerido',
                minlength: 'El confirmar debe contener minímo 5 caracteres',
                equalTo: 'Las contraseñas no coinciden',
            },
        }
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (correo.value.trim() == '' ||
                nombre.value.trim() == '' ||
                caja.value.trim() == '') {
                alertas('todo los campos con * son requeridos', 'warning');
            } else {
                if (id_user.value.trim() == '') {
                    if (clave.value.trim() == '' || confirmar.value.trim() == '') {
                        alertas('la clave y confirmar es requerido', 'warning');
                    } else {
                        if (clave.value.trim() != confirmar.value.trim()) {
                            alertas('las contraseñas no coinciden', 'warning');
                        } else {
                            const url = base_url + 'usuarios/registrar';
                            insertarRegistros(url, frm, tblUsuarios);
                        }
                    }
                } else {
                    const url = base_url + 'usuarios/registrar';
                    insertarRegistros(url, frm, tblUsuarios);
                }
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
});

function frmUsuario() {
    document.querySelector('#claves').classList.remove('d-none');
    document.querySelector('#confirmar').setAttribute('required', 'required');
    document.querySelector('#clave').setAttribute('required', 'required');
    nuevoModal('Nuevo Usuario');
}

function btnEditarUser(id) {
    document.getElementById('title').textContent = 'Actualizar usuario';
    btn.textContent = 'Modificar';
    const url = base_url + 'usuarios/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            id_user.value = res.id;
            nombre.value = res.nombre;
            correo.value = res.correo;
            caja.value = res.id_caja;
            document.getElementById('claves').classList.add('d-none');
            myModal.show();
        }
    };
}

function btnEliminarUser(id) {
    const url = base_url + 'usuarios/eliminar/' + id;
    eliminarRegistro(url, tblUsuarios);
}

function btnReingresarUser(id) {
    const url = base_url + 'usuarios/reingresar/' + id;
    reingresarRegistro(url, tblUsuarios);
}
//Fin Usuarios

function registrarPermisos(e) {
    e.preventDefault();
    const frm = document.getElementById('formulario');
    const url = base_url + 'usuarios/registrarPermisos';
    insertarRegistros(url, frm, null);
}

function actualizarDatos(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre').value;
    const correo = document.getElementById('correo').value;
    const telefono = document.getElementById('telefono').value;
    const direccion = document.getElementById('direccion').value;
    const apellido = document.getElementById('apellido').value;
    if (nombre == '' ||
        apellido == '' ||
        correo == '' ||
        telefono == '' ||
        direccion == '') {
        alertas('Todo los campos son requeridos', 'warning');
        return;
    } else {
        const url = base_url + 'usuarios/actualizarDato';
        const frm = document.getElementById('frmDatos');
        insertarRegistros(url, frm, null);
    }
}

function frmCambiarPass(e) {
    e.preventDefault();
    const actual = document.getElementById('clave_actual').value;
    const nueva = document.getElementById('clave_nueva').value;
    const confirmar = document.getElementById('confirmar_clave').value;
    if (actual == '' || nueva == '' || confirmar == '') {
        alertas('Todo los campos son obligatorios', 'warning');
        return;
    } else {
        if (nueva != confirmar) {
            alertas('Las contraseñas no coinciden', 'warning');
            return;
        } else {
            const url = base_url + 'usuarios/cambiarPass';
            const frm = document.getElementById('frmCambiarPass');
            const http = new XMLHttpRequest();
            http.open('POST', url, true);
            http.send(new FormData(frm));
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    myModal.hide();
                    frm.reset();
                }
            };
        }
    }
}