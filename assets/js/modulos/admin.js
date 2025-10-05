let editor;
const id = document.getElementById('id');
const ruc = document.getElementById('ruc');
const nombre = document.getElementById('nombre');
const telefono = document.getElementById('telefono');
const correo = document.getElementById('correo');
const direccion = document.getElementById('direccion');
const cant_factura = document.getElementById('cant_factura');
const moneda = document.getElementById('moneda');
const impuesto = document.getElementById('impuesto');
const btn = document.getElementById('btnAccion');
const frm = document.getElementById('formulario');
document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: [
                'bold',
                'italic',
                'link',
                'undo',
                'redo',
                'numberedList',
                'bulletedList',
                'blockQuote',
            ],
        })
        .then((newEditor) => {
            editor = newEditor;
        })
        .catch((error) => {
            console.error(error);
        });
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
                minlength: 9,
            },
            direccion: {
                required: true,
                minlength: 4,
            },
            correo: {
                required: 'El correo es requerido',
                email: 'Ingresa un correo valido',
            },
            moneda: 'required',
            impuesto: 'required',
            cant_factura: 'required',
        },
        messages: {
            moneda: 'La moneda es requerido',
            impuesto: 'El impuesto es requerido',
            cant_factura: 'La cantidad de factura es requerido',
            nombre: {
                required: 'El nombre es requerido',
                minlength: 'El nombre debe tener un mínimo 10 caracteres',
            },
            telefono: {
                required: 'El teléfono es requerido',
                minlength: 'El teléfono debe tener un mínimo 9 caracteres',
            },
            direccion: {
                required: 'La dirección es requerido',
                minlength: 'La dirección debe tener un mínimo 4 caracteres',
            },
            correo: {
                required: 'El correo es requerido',
                email: 'Ingresa un correo valido',
            },
        },
    });

    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($('#formulario').valid()) {
            if (id.value.trim() == '' ||
                ruc.value.trim() == '' ||
                nombre.value.trim() == '' ||
                telefono.value.trim() == '' ||
                correo.value.trim() == '' ||
                direccion.value.trim() == '' ||
                cant_factura.value.trim() == '' ||
                impuesto.value.trim() == '' ||
                moneda.value.trim() == '') {
                alertas('Todo los campos son requerido', 'warning');
                return;
            } else {
                const url = base_url + 'administracion/modificar';
                const http = new XMLHttpRequest();
                let frmData = new FormData(frm);
                frmData.append('mensaje', editor.getData());
                http.open('POST', url, true);
                http.send(frmData);
                http.upload.addEventListener('progress', function() {
                    btn.textContent = 'Procesando...';
                });
                http.addEventListener('load', function() {
                    btn.textContent = 'Guardar';
                });
                http.onreadystatechange = function() {
                    btn.textContent = 'Procesando...';
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);
                    }
                };
            }
        } else {
            alertas('corrige los problemas', 'warning');
            return;
        }
    });
});

function previewLogo(e) {
    var input = document.getElementById('imagen');
    var filePath = input.value;
    var extension = /(\.png)$/i;
    if (!extension.exec(filePath)) {
        alertas('Seleccione un formato png', 'warning');
        deleteImg();
        return false;
    } else {
        const url = e.target.files[0];
        const urlTmp = URL.createObjectURL(url);
        document.getElementById('img-preview').src = urlTmp;
        document.getElementById('icon-image').classList.add('d-none');
        document.getElementById('icon-cerrar').innerHTML = `
        <button class='btn btn-outline-danger' onclick='deleteImg()'><i class='fas fa-times-circle'></i></button>
        `;
    }
}