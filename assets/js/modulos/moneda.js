let t_moneda;
const frm = document.getElementById('formulario');
const nombre = document.getElementById('nombre');
const simbolo = document.getElementById('simbolo');
const id_moneda = document.getElementById('id');
const btn = document.getElementById('btnAccion');
document.addEventListener('DOMContentLoaded', function() {
    t_moneda = $('#t_moneda').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: '' + base_url + 'administracion/listarMonedas',
            dataSrc: '',
        },
        columns: [{
                data: 'id',
            },
            {
                data: 'simbolo',
            },
            {
                data: 'nombre',
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
    });
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            nombre: {
                required: true,
                minlength: 3,
            },
            simbolo: {
                required: true,
                minlength: 1,
            },
        },
        messages: {
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe tener un mínimo 3 caracteres',
            },
            simbolo: {
                required: 'El simbolo es requerido',
                minlength: 'El simbolo debe tener un mínimo 1 caracter',
            },
        },
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (nombre.value.trim() == '' || simbolo.value.trim() == '') {
                alertas('Todo los campos con * son requeridos', 'warning');
                return;
            } else {
                const url = base_url + 'administracion/registrarMoneda';
                insertarRegistros(url, frm, t_moneda);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
});
//Monedas
function frmMoneda() {
    nuevoModal('nueva moneda');
}

function btnEditarMoneda(id) {
    document.getElementById('title').textContent = 'Modificar Moneda';
    btn.textContent = 'Modificar';
    const url = base_url + 'administracion/editarMoneda/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            id_moneda.value = res.id;
            nombre.value = res.nombre;
            simbolo.value = res.simbolo;
            myModal.show();
        }
    };
}

function btnEliminarMoneda(id) {
    const url = base_url + 'administracion/eliminarMoneda/' + id;
    eliminarRegistro(url, t_moneda);
}

function btnReingresarMoneda(id) {
    const url = base_url + 'administracion/reingresarMoneda/' + id;
    reingresarRegistro(url, t_moneda);
} //fin moneda