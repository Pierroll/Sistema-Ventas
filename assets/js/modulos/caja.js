let tblCajas;
const btn = document.getElementById('btnAccion');
const frm = document.getElementById('formulario');
document.addEventListener('DOMContentLoaded', function() {
    tblCajas = $('#tblCajas').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'cajas/listar',
            dataSrc: '',
        },
        columns: [
            { data: 'id' },
            { data: 'caja' },
            { data: 'estado' },
            { data: 'editar' },
            { data: 'eliminar' },
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
    }); //Fin de la tabla Cajas
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            nombre: {
                required: true,
                minlength: 3,
            }
        },
        messages: {
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 3 caracteres',
            }
        }
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (document.getElementById('nombre').value.trim() == '') {
                alertas('Todo los campos son requerido', 'warning');
                return;
            } else {
                const url = base_url + 'cajas/registrar';
                insertarRegistros(url, frm, tblCajas);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
});

function frmCaja() {
    nuevoModal('nuevo caja');
}

function btnEditarCaja(id) {
    document.getElementById('title').textContent = 'Actualizar caja';
    btn.textContent = 'Modificar';
    const url = base_url + 'cajas/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById('id').value = res.id;
            document.getElementById('nombre').value = res.caja;
            myModal.show();
        }
    };
}

function btnEliminarCaja(id) {
    const url = base_url + 'cajas/eliminar/' + id;
    eliminarRegistro(url, tblCajas);
}

function btnReingresarCaja(id) {
    const url = base_url + 'cajas/reingresar/' + id;
    reingresarRegistro(url, tblCajas);
} //Fin Cajas