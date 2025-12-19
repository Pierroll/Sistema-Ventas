let t_h_c;
document.addEventListener("DOMContentLoaded", function() {
    //Buscar proveedor con plugi Jquery-UI
    $("#buscarProveedor").autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax({
                url: base_url + "proveedor/buscarProveedor",
                dataType: "json",
                data: {
                    pr: request.term,
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
            document.getElementById("id_pr").value = ui.item.id;
            document.getElementById("buscarProveedor").value = ui.item.nombre;
            document.getElementById("direccion_pr").value = ui.item.direccion;
        },
    });
    //autocomplete
    $("#buscarCompra").autocomplete({
        minLength: 3,
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
                        document.getElementById('errorBusquedaCompra').textContent = '';
                    } else {
                        document.getElementById('errorBusquedaCompra').textContent = 'NO HAY REGISTRO';
                        return;
                    }

                }
            });
        },
        select: function(event, ui) {
            agregarCompra(ui.item.id);
        },
    });
    t_h_c = $("#t_historial_c").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "compras/listar_historial",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "total" },
            { data: "fecha" },
            { data: "hora" },
            { data: "editar" },
            { data: "eliminar" },
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
    cargarDetalle();
})

function cargarDetalle() {
    const url = base_url + "compras/listar/detalle";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = "";
            res.detalle.forEach((row) => {
                html += `<tr>
               <td>${row.descripcion}</td>
               <td width="120"><input type="number" class="form-control" value="${row.cantidad}" step="0.01" min="0.01" onchange="cantidadCompra(${row.id}, event)" /> </td>
               <td>${row.precio}</td>
               <td>${row.sub_total}</td>
               <td>
               <button class="btn btn-outline-danger" type="button" onclick="deleteDetalle(${row.id}, 1)">
               <i class="fas fa-trash-alt"></i></button>
               </td>
               </tr>`;
            });
            document.getElementById("tblDetalle").innerHTML = html;
            document.getElementById("alert_total").textContent = res.total_pagar;
        }
    };
}

function btnAnularC(id) {
    const url = base_url + "compras/anularC/" + id;
    anular(url, t_h_c, "compra");
}

function generarPdfCompra() {
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
                    url = base_url + "productos/pdfCompra/all";
                } else {
                    url = base_url + "productos/pdfCompra/" + desde + "/" + hasta;
                }
                window.open(url);
            }
        });
    }
}
//funcion para agregar compra
function agregarCompra(id_producto) {
    const url = base_url + "compras/agregarCompra/" + id_producto;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            if (res.icono == "success") {
                cargarDetalle();
                document.getElementById("buscarCompra").value = "";
            } else {
                alertas(res.msg, res.icono);
            }
        }
    };
}

function cantidadCompra(id, e) {
    const url = base_url + "compras/cantidadCompra";
    let data = new FormData();
    data.append("id", id);
    data.append("cantidad", e.target.value);
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            if (res.icono != "success") {
                alertas(res.msg, res.icono);
            }
            cargarDetalle();
        }
    };
}

function procesarCompra() {
    Swal.fire({
        title: "Esta seguro de Procesar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si!",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            let formu = document.getElementById("formulario");
            let fila = document.querySelectorAll("#detalle_ tr").length;
            if (fila < 2) {
                alertas("La tabla esta vacia", "warning");
                return false;
            } else {
                const url = base_url + "compras/registrarCompra";
                const http = new XMLHttpRequest();
                http.open("POST", url, true);
                http.send(new FormData(formu));
                http.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);
                        if (res.icono == "success") {
                            formu.reset();
                            setTimeout(() => {
                                cargarDetalle();
                                generarReportes(1, res.id);
                            }, 2000);
                        }
                    }
                };
            }
        }
    });
}