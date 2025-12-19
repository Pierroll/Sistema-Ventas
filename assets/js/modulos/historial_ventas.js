let t_h_v;
document.addEventListener("DOMContentLoaded", function() {
    t_h_v = $("#t_historial_v").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "ventas/listar_historial",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "nombre" },
            { data: "fecha" },
            { data: "hora" },
            { data: "total" },
            { data: "metodo" },
            { data: "anular" },
            { data: "reporte" },
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
})

function btnAnularVenta(id) {
    const url = base_url + "ventas/anularVenta/" + id;
    anular(url, t_h_v, "venta");
}

function generarPdfVenta() {
    const desde = document.getElementById("min").value;
    const hasta = document.getElementById("max").value;
    if (desde > hasta) {
        alertas(
            "Fecha Incorrecta, la fecha desde no puede ser mayor a hasta",
            "warning"
        );
        return false;
    } else {
        let timerInterval, url;
        Swal.fire({
            title: "Generando reporte",
            html: "Procesando <b></b> milisegundos.",
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const b = Swal.getHtmlContainer().querySelector("b");
                timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft();
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            },
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                if (desde == "" || hasta == "") {
                    url = base_url + "productos/pdfVenta/all";
                } else {
                    url = base_url + "productos/pdfVenta/" + desde + "/" + hasta;
                }
                window.open(url);
            }
        });
    }
}