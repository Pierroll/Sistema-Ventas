let tblCategorias;
const nombre = document.getElementById("nombre");
const frm = document.getElementById("formulario");
const btn = document.getElementById("btnAccion");
document.addEventListener("DOMContentLoaded", function() {
    tblCategorias = $("#tblCategorias").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "categorias/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "estado" },
            { data: "editar" },
            { data: "eliminar" },
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
            [0, "desc"]
        ],
    }); //Fin de la tabla categorias
    $("#formulario").validate({
        errorElement: "span",
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            nombre: {
                required: true,
                minlength: 3,
            },
        },
        messages: {
            nombre: {
                required: "El nombre es requerido",
                minlength: "El nombre debe contener un mínimo 3 caracteres",
            },
        },
    });
    frm.addEventListener('submit', function(e) {
        e.preventDefault();
        if ($("#formulario").valid()) {
            if (nombre.value.trim() == "") {
                alertas("Todo los campos son obligatorios", "warning");
            } else {
                const url = base_url + "categorias/registrar";
                insertarRegistros(url, frm, tblCategorias);
            }
        } else {
            alertas("corrige los problemas", "warning");
            return;
        }
    })
});

function frmCategoria() {
    nuevoModal("nueva categoria");
}

function btnEditarCat(id) {
    document.getElementById("title").textContent = "Actualizar Categoria";
    document.getElementById("btnAccion").textContent = "Modificar";
    const url = base_url + "categorias/editar/" + id;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById("id").value = res.id;
            nombre.value = res.nombre;
            myModal.show();
        }
    };
}

function btnEliminarCat(id) {
    const url = base_url + "categorias/eliminar/" + id;
    eliminarRegistro(url, tblCategorias);
}

function btnReingresarCat(id) {
    const url = base_url + "categorias/reingresar/" + id;
    reingresarRegistro(url, tblCategorias);
} //Fin categorias