let tblProveedor;
const ruc = document.getElementById('ruc');
const nombre = document.getElementById('nombre');
const telefono = document.getElementById('telefono');
const direccion = document.getElementById('direccion');
const frm = document.getElementById('formulario');
const btn = document.getElementById('btnAccion');
document.addEventListener('DOMContentLoaded', function() {
    //Datatable proveedor
    tblProveedor = $('#tblProveedor').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'proveedor/listar',
            dataSrc: '',
        },
        columns: [{
                data: 'id',
            },
            {
                data: 'ruc',
            },
            {
                data: 'nombre',
            },
            {
                data: 'telefono',
            },
            {
                data: 'direccion',
            },
            {
                data: 'estado',
            },
            {
                data: 'editar',
            },
            {
                data: 'eliminar',
            },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
        },
        dom,
        buttons,
        resonsieve: true,
        bDestroy: true,
        iDisplayLength: 10,
        order: [
            [0, 'desc']
        ],
    }); //Fin de la tabla proveedor
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            ruc: {
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
            ruc: {
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
            if (ruc.value.trim() == '' || nombre.value.trim() == '' || telefono.value.trim() == '' || direccion.value.trim() == '') {
                alertas('Todo los campos con * son obligatorios', 'warning');
            } else {
                const url = base_url + 'proveedor/registrar';
                insertarRegistros(url, frm, tblProveedor);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
})

function frmProveedor() {
    nuevoModal('nuevo proveedor');
}

function btnEditarPr(id) {
    document.getElementById('title').textContent = 'Actualizar Proveedor';
    btn.textContent = 'Modificar';
    const url = base_url + 'proveedor/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById('id').value = res.id;
            ruc.value = res.ruc;
            nombre.value = res.nombre;
            telefono.value = res.telefono;
            direccion.value = res.direccion;
            myModal.show();
        }
    };
}

function btnEliminarPr(id) {
    const url = base_url + 'proveedor/eliminar/' + id;
    eliminarRegistro(url, tblProveedor);
}

function btnReingresarPr(id) {
    const url = base_url + 'proveedor/reingresar/' + id;
    reingresarRegistro(url, tblProveedor);
} //Fin Proveedor