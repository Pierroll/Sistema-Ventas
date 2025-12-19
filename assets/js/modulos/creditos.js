let tblCreditos;
document.addEventListener("DOMContentLoaded", function() {
    tblCreditos = $("#tblCreditos").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "creditos/listar/1",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "fecha" },
            { data: "monto" },
            { data: "abonado" },
            { data: "restante" },
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
    });
    $("#tblFinalizados").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "creditos/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "fecha" },
            { data: "monto" },
            { data: "abonado" },
            { data: "restante" }
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
    });
});

function btnAddAbono(id_credito) {
    Swal.fire({
        title: "MONTO A ABONAR",
        input: "text",
        inputAttributes: {
            autocapitalize: "off",
        },
        showCancelButton: true,
        confirmButtonText: "Abonar",
        showLoaderOnConfirm: true,
        preConfirm: (valor) => {
            return valor;
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            verificarMonto(id_credito, result.value);
        }
    });
}

function verificarMonto(id_credito, monto) {
    const url = base_url + "creditos/verificarMonto/" + id_credito;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.restante >= monto) {
                insertarAbono(id_credito, monto);
            } else {
                alertas("INGRESA UN MONTO MENOR O IGUAL A RESTANTE", "warning");
                setTimeout(() => {
                    btnAddAbono(id_credito);
                }, 1500);
            }
        }
    };
}

function insertarAbono(id_credito, monto) {
    const url = base_url + "creditos/registrarAbono";
    let data = new FormData();
    data.append("monto", monto);
    data.append("id_credito", id_credito);
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertas(res.msg, res.icono);
            if (res.icono == 'success') {
                tblCreditos.ajax.reload();
            }
        }
    }
}