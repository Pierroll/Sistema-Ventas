let tblLanding;
const pagina = document.getElementById("pagina");
const nombre = document.getElementById("nombre");
const telefono = document.getElementById("telefono");
const correo = document.getElementById("correo");
const negocio = document.getElementById("negocio");
const frm = document.getElementById("formulario");
const btn = document.getElementById("btnAccion");

const btnprocesar = document.getElementById("btnprocesar");
const dni = document.getElementById("dni");
const direccion = document.getElementById("direccion");
const idProceso = document.getElementById("idProceso");
document.addEventListener("DOMContentLoaded", function() {
    tblLanding = $("#tblLanding").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "landing/listar",
            dataSrc: "",
        },
        columns: [
            { data: "hora_registro" },
            { data: "fecha_registro" },
            { data: "pagina" },
            { data: "nombre" },
            { data: "telefono" },
            { data: "correo" },
            { data: "negocio" },
            { data: "estado" },
            { data: "accion" },
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json",
        },
        dom,
        buttons,
        resonsieve: true,
        bDestroy: true,
        iDisplayLength: 10,
        order: [
            [0, "desc"]
        ],
    }); //Fin de la tabla
    frm.addEventListener("submit", function(e) {
        e.preventDefault();
        if (
            pagina.value == "" ||
            nombre.value == "" ||
            telefono.value == "" ||
            correo.value == ""
        ) {
            alertas("Todo los campos con * son obligatorios", "warning");
        } else {
            const url = base_url + "landing/registrar";
            insertarRegistros(url, frm, tblLanding);
        }
    });

    //agregar a clientes
    btnprocesar.addEventListener("click", function() {
        if (dni.value == "" || direccion.value == "" || idProceso.value == "") {
            alertas("TODO LOS CAMPOS SON REQUERIDOS", "warning");
        } else {
            const url = base_url + "landing/agregarCliente";
            const data = new FormData();
            data.append("dni", dni.value);
            data.append("direccion", direccion.value);
            data.append("id", idProceso.value);
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (res.icono == "success") {
                        $("#modalCliente").modal("hide");
                        tblLanding.ajax.reload();
                    }
                }
            };
        }
    });
});

function nuevoLanding() {
    nuevoModal("nuevo landing");
}

function btnEditarLan(id) {
    document.getElementById("title").textContent = "Actualizar Landing";
    document.getElementById("btnAccion").textContent = "Modificar";
    const url = base_url + "landing/editar/" + id;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.getElementById("id").value = res.id;
            pagina.value = res.pagina;
            nombre.value = res.nombre;
            telefono.value = res.telefono;
            correo.value = res.correo;
            negocio.value = res.negocio;
            myModal.show();
        }
    };
}

function btnEliminarLan(id) {
    const url = base_url + "landing/eliminar/" + id;
    eliminarRegistro(url, tblLanding);
}

function procesarRegistro(id) {
    document.querySelector('#idProceso').value = id;
    $("#modalCliente").modal("show");
}