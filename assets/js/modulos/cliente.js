let tblClientes;
const dni = document.getElementById('dni');
const nombre = document.getElementById('buscarCliente');
const telefono = document.getElementById('telefono');
const direccion = document.getElementById('direccion');
const frm = document.getElementById('formulario');
const btn = document.getElementById('btnAccion');
document.addEventListener('DOMContentLoaded', function() {
    tblClientes = $('#tblClientes').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'clientes/listar',
            dataSrc: '',
        },
        columns: [
            { data: 'id' },
            { data: 'dni' },
            { data: 'nombre' },
            { data: 'telefono' },
            { data: 'direccion' },
            { data: 'estado' },
            { data: 'editar' },
            { data: 'eliminar' },
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
    }); //Fin de la tabla clientes
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            dni: {
                required: true,
                minlength: 8,
            },
            nombre: {
                required: true,
                minlength: 10,
            },
            telefono: {
                required: true,
                minlength: 9
            },
            direccion: {
                required: true,
                minlength: 5
            },
        },
        messages: {
            dni: {
                required: 'La identidad es requerido',
                minlength: 'La identidad debe contener un mínimo 8 caracteres',
            },
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 10 caracteres'
            },
            telefono: {
                required: 'El teléfono es requerido',
                minlength: 'El teléfono debe contener un minímo 9 caracteres',
            },
            direccion: {
                required: 'La dirección es requerido',
                minlength: 'La dirección debe contener un minímo 5 caracteres',
            },
        }
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (dni.value.trim() == '' || nombre.value.trim() == '' || telefono.value.trim() == '' || direccion.value.trim() == '') {
                alertas('Todo los campos con * son obligatorios', 'warning');
            } else {
                const url = base_url + 'clientes/registrar';
                insertarRegistros(url, frm, tblClientes);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
})

function frmCliente() {
    nuevoModal('nuevo cliente');
}

function btnEditarCli(id) {
    document.getElementById('title').textContent = 'Actualizar cliente';
    document.getElementById('btnAccion').textContent = 'Modificar';
    const url = base_url + 'clientes/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById('id').value = res.id;
            dni.value = res.dni;
            nombre.value = res.nombre;
            telefono.value = res.telefono;
            direccion.value = res.direccion;
            myModal.show();
        }
    };
}

function btnEliminarCli(id) {
    const url = base_url + 'clientes/eliminar/' + id;
    eliminarRegistro(url, tblClientes);
}

function btnReingresarCli(id) {
    const url = base_url + 'clientes/reingresar/' + id;
    reingresarRegistro(url, tblClientes);
} //Fin Clientes