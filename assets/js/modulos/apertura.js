let t_arqueo;
const btn = document.getElementById('btnAccion');
const frm = document.getElementById('formulario');
const monto_inicial = document.getElementById('monto_inicial');
const monto_final = document.getElementById('monto_final');
document.addEventListener('DOMContentLoaded', function() {
    t_arqueo = $('#t_arqueo').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'cajas/listar_arqueo',
            dataSrc: '',
        },
        columns: [{
                data: 'id',
            },
            {
                data: 'monto_inicial',
            },
            {
                data: 'monto_final',
            },
            {
                data: 'fecha_apertura',
            },
            {
                data: 'fecha_cierre',
            },
            {
                data: 'total_ventas',
            },
            {
                data: 'monto_total',
            },
            {
                data: 'estado',
            },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
        },
        createdRow: function(row, data, index) {
            //pintar una celda
            if (data.status == 0) {
                $('td', row).css({
                    'background-color': '#F89159',
                    color: 'white',
                });
            } else {
                $('td', row).css({
                    'background-color': '#59B6F8',
                    color: 'white',
                });
            }
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
            monto_inicial: {
                required: true,
                minlength: 1,
            },
        },
        messages: {
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 3 caracteres',
            },
            monto_inicial: {
                required: 'El monto inicial es requerido',
                minlength: 'El monto debe contener un mínimo 1 caracter',
            },
        },
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (monto_inicial.value.trim() == '') {
                alertas('Todo los campos son requerido', 'warning');
                return;
            } else {
                const url = base_url + 'cajas/abrirArqueo';
                const http = new XMLHttpRequest();
                http.open('POST', url, true);
                http.send(new FormData(frm));
                http.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        if (res.icono == 'success') {
                            myModal.hide();
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                        alertas(res.msg, res.icono);
                        if (document.getElementById('btnAbrirCaja')) {
                            document.getElementById('btnAbrirCaja').classList.add('d-none');
                        } else {
                            document.getElementById('btnCerrarCaja').classList.add('d-none');
                        }
                    }
                }
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
});

function arqueoCaja() {
    document.getElementById('title').textContent = 'Abrir Caja';
    document.getElementById('ocultar_campos').classList.add('d-none');
    monto_inicial.value = '';
    btn.textContent = 'Abrir Caja';
    myModal.show();
}

function cerrarCaja() {
    document.getElementById('title').textContent = 'Cerrar Caja';
    const url = base_url + 'cajas/getVentas';
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            monto_final.value = 0;
            if (res.monto_total.total != null) {
                monto_final.value = res.monto_total.total;
            }
            document.getElementById('total_ventas').value = res.total_ventas.total;
            monto_inicial.value = res.inicial.monto_inicial;
            document.getElementById('monto_general').value = res.monto_general;
            document.getElementById('id').value = res.inicial.id;
            document.getElementById('ocultar_campos').classList.remove('d-none');
            btn.textContent = 'Cerrar Caja';
            myModal.show();
        }
    };
}