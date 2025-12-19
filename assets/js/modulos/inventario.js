let t_inventario;
document.addEventListener("DOMContentLoaded", function() {

    $("#buscarInventario").autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax({
                url: base_url + "compras/buscarProducto",
                dataType: "json",
                data: {
                    pro: request.term,
                },
                success: function(data) {
                    response(data);
                    if (data.length > 0) {
                        document.getElementById('errorBusqueda').textContent = '';
                    } else {
                        document.getElementById('errorBusqueda').textContent = 'NO HAY REGISTRO';
                        return;
                    }

                }
            });
        },
        select: function(event, ui) {
            document.getElementById("id").value = ui.item.id;
            document.getElementById("buscarInventario").value = ui.item.codigo;
            document.getElementById("cantidad").value = ui.item.cantidad;
            document.getElementById("nombre").value = ui.item.descripcion;
            document.getElementById("agregar").focus();
        },
    });
    //Fin autocomple    
    t_inventario = $("#t_inventario").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "productos/listarInventario",
            dataSrc: "",
        },
        columns: [
            { data: "id_inventario" },
            { data: "descripcion" },
            { data: "fecha" },
            { data: "total_entradas" },
            { data: "total_salidas" },
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

function frmInventario() {
    $('.ui-dialog').css({
        zIndex: 999999
    })
    $('#myModal').modal('show');
}

function registrarInventario(e) {
    e.preventDefault();
    const id = document.getElementById("id").value;
    const codigo = document.getElementById("buscarInventario").value;
    const agregar = document.getElementById("agregar").value;
    if (id == "" || codigo == "" || agregar == "") {
        alertas("Todo los campos con * son requerido", "warning");
        return false;
    } else {
        const url = base_url + "productos/registrarInventario";
        const frm = document.getElementById("form_inventario");
        insertarRegistros(url, frm, t_inventario);
    }
}

function generarPdfInventario() {
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
                    url = base_url + "productos/pdfInventario/all";
                } else {
                    url = base_url + "productos/pdfInventario/" + desde + "/" + hasta;
                }
                window.open(url);
            }
        });
    }
}