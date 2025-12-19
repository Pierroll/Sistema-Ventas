let tblMedidas;
const nombre = document.getElementById('nombre');
const nombre_corto = document.getElementById('nombre_corto');
const frm = document.getElementById('formulario');
const btn = document.getElementById('btnAccion');
document.addEventListener('DOMContentLoaded', function() {
    tblMedidas = $('#tblMedidas').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'medidas/listar',
            dataSrc: '',
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'nombre_corto' },
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
            },
            nombre_corto: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 3 caracteres'
            },
            nombre_corto: {
                required: 'El nombre corto es requerido',
                minlength: 'El nombre corto debe contener un minímo 2 caracteres',
            }
        }
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (nombre.value.trim() == '' || nombre_corto.value.trim() == '') {
                alertas('Todo los campos son obligatorios', 'warning');
            } else {
                const url = base_url + 'medidas/registrar';
                insertarRegistros(url, frm, tblMedidas);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
})

function frmMedida() {
    nuevoModal('nueva medida');
}

function btnEditarMed(id) {
    document.getElementById('title').textContent = 'Actualizar medida';
    btn.textContent = 'Modificar';
    const url = base_url + 'medidas/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById('id').value = res.id;
            nombre.value = res.nombre;
            nombre_corto.value = res.nombre_corto;
            myModal.show();
        }
    }
}

function btnEliminarMed(id) {
    const url = base_url + 'medidas/eliminar/' + id;
    eliminarRegistro(url, tblMedidas);
}

function btnReingresarMed(id) {
    const url = base_url + 'medidas/reingresar/' + id;
    reingresarRegistro(url, tblMedidas);
} //Fin Medidas