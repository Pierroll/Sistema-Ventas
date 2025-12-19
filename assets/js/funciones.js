let tbl;
document.addEventListener("DOMContentLoaded", function() {
    $(".buscarCliente").autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax({
                url: base_url + "clientes/buscarCliente",
                dataType: "json",
                data: {
                    cli: request.term,
                },
                success: function(data) {
                    response(data);
                    if (data.length > 0) {
                        document.getElementById('errorBusqueda').textContent = '';
                    } else {
                        document.getElementById('errorBusqueda').textContent = 'NO HAY REGISTRO';
                    }

                }
            });
        },
        select: function(event, ui) {
            document.getElementById("id").value = ui.item.id;
            document.getElementById("buscarCliente").value = ui.item.nombre;
            document.getElementById("direccion").value = ui.item.direccion;
        },
    });
    // Global setting for DataTables language
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: base_url + 'assets/js/i18n/Spanish.json'
        }
    });

    tbl = $("#tbl").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        destroy: true,
        iDisplayLength: 10,
        order: [
            [0, "desc"]
        ],
    }); //Fin de la tabla usuarios
});

function preview(e) {
    var input = document.getElementById("imagen");
    var filePath = input.value;
    var extension = /(\.png|\.jpeg|\.jpg)$/i;
    if (!extension.exec(filePath)) {
        alertas("Seleccione un archivo valido", "warning");
        deleteImg();
        return false;
    } else {
        const url = e.target.files[0];
        const urlTmp = URL.createObjectURL(url);
        document.getElementById("img-preview").src = urlTmp;
        document.getElementById("icon-image").classList.add("d-none");
        document.getElementById("icon-cerrar").innerHTML = `
        <button class="btn btn-outline-danger" onclick="deleteImg()"><i class="fas fa-times-circle"></i></button>
        `;
    }
}

function accionImg(e) {
    const url = e.target.files[0];
    const urlTmp = URL.createObjectURL(url);
    document.getElementById("img-preview").src = urlTmp;
    document.getElementById("icon-image").classList.add("d-none");
    document.getElementById("icon-cerrar").innerHTML = `
        <button class="btn btn-outline-danger" onclick="deleteImg()"><i class="fas fa-times-circle"></i></button>
        `;
}

function deleteImg() {
    document.getElementById("icon-cerrar").innerHTML = "";
    document.getElementById("icon-image").classList.remove("d-none");
    document.getElementById("img-preview").src = "";
    document.getElementById("imagen").value = "";
    document.getElementById("foto_actual").value = "";
}

function deleteDetalle(id, accion) {
    let url;
    if (accion == 1) {
        url = base_url + "compras/delete/" + id;
    } else if (accion == 2) {
        url = base_url + "ventas/deleteVenta/" + id;
    } else if (accion == 0) {
        url = base_url + "cotizaciones/deleteCotizacion/" + id;
    } else {
        url = base_url + "apartados/delete/" + id;
    }
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertas(res.msg, res.icono);
            if (accion == 1) {
                cargarDetalle();
            } else if (accion == 2) {
                cargarDetalleVenta();
            } else if (accion == 0) {
                cargarDetalleCotizacion();
            } else {
                cargarDetalleApart();
            }
        }
    };
}

function generarReportes(accion, id) {
    let nombre;
    if (accion == 1) {
        nombre = 'compras';
    } else {
        nombre = 'ventas';
    }
    Swal.fire({
        title: 'Generar Reporte',
        icon: "warning",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "TICKED",
        denyButtonText: "FACTURA",
        cancelButtonText: "CANCELAR",
    }).then((result) => {
        if (result.isConfirmed) {
            window.open(base_url + nombre + '/generarPdf/' + id, '_blank');
        } else if (result.isDenied) {
            window.open(base_url + nombre + '/generarFactura/' + id, '_blank');
        }
    });
}

function mostrarTodo(e) {
    document.getElementById("min").value = "";
    document.getElementById("max").value = "";
    if (e.target.name == "compra") {
        t_h_c.draw();
    } else if (e.target.name == "venta") {
        t_h_v.draw();
    } else if (e.target.name == "inventario") {
        t_inventario.draw();
    } else {
        tbl.draw();
    }
}

function salir() {
    Swal.fire({
        title: "Esta seguro de cerrar la sesión?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si!",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = base_url + "usuarios/salir";
        }
    });
}

function pagarCon(e) {
    const total = document.getElementById("alert_total").textContent;
    let c_total = parseFloat(total.replace(",", "")) - parseFloat(e.target.value);
    document.getElementById("cambio").value = c_total.toFixed(2);
}

function anularProceso(e) {
    let fila = document.querySelectorAll("#detalle_ tr").length;
    if (fila < 2) {
        alertas("La tabla esta vacia", "warning");
        return false;
    } else {
        Swal.fire({
            title: "Esta seguro de anular el proceso?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si!",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                let url;
                if (e.target.name == "anularVenta") {
                    url = base_url + "compras/anularProceso/detalle_temp";
                } else {
                    url = base_url + "compras/anularProceso/detalle";
                }
                const http = new XMLHttpRequest();
                http.open("GET", url, true);
                http.send();
                http.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);
                        document.getElementById("cambio").value = "";
                        document.getElementById("pagar_con").value = "";
                        if (e.target.name == "anularVenta") {
                            cargarDetalleVenta();
                        } else {
                            cargarDetalle();
                        }
                    }
                };
            }
        });
    }
}