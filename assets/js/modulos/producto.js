let tblProductos;
const codigo = document.getElementById('codigo');
const nombre = document.getElementById('descripcion');
const precio_compra = document.getElementById('precio_compra');
const precio_venta = document.getElementById('precio_venta');
const id_medida = document.getElementById('medida');
const id_cat = document.getElementById('categoria');
const frm = document.getElementById('formulario');
const btn = document.getElementById('btnAccion');
document.addEventListener('DOMContentLoaded', function() {
    tblProductos = $('#tblProductos').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        pageLength: 25,
        ajax: {
            url: base_url + 'productos/listar',
            dataSrc: '',
        },
        columns: [
            { data: 'id' },
            { data: 'imagen' },
            { data: 'codigo' },
            { data: 'descripcion' },
            { data: 'medida' },
            { data: 'categoria' },
            { data: 'precio_venta' },
            { data: 'cantidad' },
            { data: 'subTotal' },
            { data: 'estado' },
            { data: 'editar' },
            { data: 'eliminar' },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json',
        },
        createdRow: function(row, data, index) {
            //pintar una celda
            if (data.cantidad == 0) {
                $('td', row)
                    .eq(7)
                    .html('<span class="badge bg-warning ">Agotado</span>');
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
    }); //Fin de productos
    $('#formulario').validate({
        errorElement: 'span',
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            codigo: {
                required: true,
                minlength: 8,
            },
            descripcion: {
                required: true,
                minlength: 5,
            },
            precio_compra: {
                required: true,
                minlength: 1
            },
            precio_venta: {
                required: true,
                minlength: 1
            },
            medida: 'required',
            categoria: 'required'
        },
        messages: {
            codigo: {
                required: 'El código es requerido',
                minlength: 'El código debe contener un mínimo 8 caracteres',
            },
            descripcion: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe contener un mínimo 5 caracteres'
            },
            precio_compra: {
                required: 'El precio compra es requerido',
                minlength: 'El precio compra debe contener un minímo 1 caracter',
            },
            precio_venta: {
                required: 'El precio venta es requerido',
                minlength: 'La precio venta debe contener un minímo 1 caracter',
            },
            medida: 'La medida es requerido',
            categoria: 'La categoria es requerido'
        }
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (codigo.value.trim() == '' || nombre.value.trim() == '' || precio_compra.value.trim() == '' ||
                precio_venta.value.trim() == '' || medida.value == '' || categoria.value == '') {
                alertas('Todo los campos con * son obligatorios', 'warning');
            } else {
                const url = base_url + 'productos/registrar';
                insertarRegistros(url, frm, tblProductos);
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
})

function frmProducto() {
    nuevoModal('nuevo producto');
    deleteImg();
}

function btnEditarPro(id) {
    document.getElementById('title').textContent = 'Actualizar Producto';
    document.getElementById('btnAccion').textContent = 'Modificar';
    const url = base_url + 'productos/editar/' + id;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById('id').value = res.id;
            codigo.value = res.codigo;
            descripcion.value = res.descripcion;
            precio_compra.value = res.precio_compra;
            precio_venta.value = res.precio_venta;
            id_medida.value = res.id_medida;
            id_cat.value = res.id_categoria;
            document.getElementById('img-preview').src =
                base_url + 'assets/img/pro/' + res.foto;
            document.getElementById('icon-cerrar').innerHTML = `
            <button class='btn btn-outline-danger' onclick='deleteImg()'>
            <i class='fas fa-times-circle'></i></button>`;
            document.getElementById('icon-image').classList.add('d-none');
            document.getElementById('foto_actual').value = res.foto;
            myModal.show();
        }
    };
}

function btnEliminarPro(id) {
    const url = base_url + 'productos/eliminar/' + id;
    eliminarRegistro(url, tblProductos);
}

function btnReingresarPro(id) {
    const url = base_url + 'productos/reingresar/' + id;
    reingresarRegistro(url, tblProductos);
}

function base_datos(e) {
    e.preventDefault();
    document.getElementById('importar_bd').textContent = 'Importar';
    var input = document.getElementById('b_datos');
    var filePath = input.value;
    var extension = /(\.csv|\.xlsx|\.xls)$/i;

    if (!extension.exec(filePath)) {
        alertas('Seleccione un archivo valido', 'warning');
        input.value = '';
        document.getElementById('importar_bd').classList.add('d-none');
        return false;
    } else {
        document.getElementById('importar_bd').classList.remove('d-none');
    }
}

function importarProductos(e) {
    e.preventDefault();
    const dato = document.getElementById('b_datos');
    if (dato.value == '') {
        alertas('Selecciona el archivo', 'warning');
    } else {
        const frm = document.getElementById('frmBd');
        const http = new XMLHttpRequest();
        const url = base_url + 'administracion/importarProductos';
        http.open('POST', url, true);
        // upload progress event
        http.upload.addEventListener('progress', function(e) {
            document.getElementById('importar_bd').textContent = 'Procesando';
        });
        http.send(new FormData(frm));
        http.addEventListener('load', function(e) {
            document.getElementById('importar_bd').textContent = 'Importar';
            document.getElementById('importar_bd').classList.add('d-none');
        });
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('importar_bd').textContent = 'Procesando';
                const res = JSON.parse(this.responseText);
                if (res.icono == 'success') {
                    alertas(res.msg, res.icono);
                    tblProductos.ajax.reload();
                } else {
                    alertas(
                        'Error al Importar los Productos, Asegurese de que sea el mismo formato',
                        'error'
                    );
                }
                frm.reset();
            }
        };
    }
}