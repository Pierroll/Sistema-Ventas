document.addEventListener("DOMContentLoaded", function() {
    //autocomplete venta
    $("#buscarCotizacion").autocomplete({
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
                        document.getElementById('errorBusquedaCotizacion').textContent = '';
                    } else {
                        document.getElementById('errorBusquedaCotizacion').textContent = 'NO HAY REGISTRO';
                        return;
                    }

                }
            });
        },
        select: function(event, ui) {
            agregarCotizacion(ui.item.id);
        },
    });
    cargarDetalleCotizacion();
})


function cargarDetalleCotizacion() {
    const url = base_url + "cotizaciones/listar";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = "";
            let totalgeneral = 0;
            res.detalle.forEach((row) => {
                let igv = parseInt(row.impuesto);
                let subtotal = parseFloat(row.subtotal) * (parseFloat(igv) / parseInt(100));
                let total = parseInt(igv) > 0 ? parseFloat(subtotal) + parseFloat(row.subtotal) : row.subtotal;
                totalgeneral += parseFloat(total);
                html += `<tr>
               <td>${row.descripcion}</td>
               <td width="120"><input type="text" class="form-control" value="${row.medida}" step="0.01" min="0.01" onchange="medidaCotizacion(${row.id}, event)" /> </td>
               <td width="120"><input type="number" class="form-control" value="${row.cantidad}" step="0.01" min="0.01" onchange="cantidadCotizacion(${row.id}, event)" /> </td>
               <td>${row.precio}</td>
               <td width="120"><input type="number" class="form-control" value="${row.descuento}" step="0.01" min="0.01" onchange="descuentoCotizacion(${row.id}, event)" /> </td>              
               <td>${Number.parseFloat(row.subtotal).toFixed(2)}</td>
               <td width="120"><input type="number" class="form-control" value="${row.impuesto}" onchange="impuestoCotizacion(${row.id}, event)" /> </td>
               <td>${Number.parseFloat(total).toFixed(2)}</td>
               <td>
               <button class="btn btn-outline-danger" type="button" onclick="deleteDetalle(${row.id}, 0)">
               <i class="fas fa-trash-alt"></i></button>
               </td>
               </tr>`;
            });
            document.getElementById("tblDetalleCotizacion").innerHTML = html;
            document.getElementById("alert_total").textContent = totalgeneral.toFixed(2);
        }
    };
}

function agregarCotizacion(id_producto) {
    const url = base_url + "cotizaciones/agregarCotizacion/" + id_producto;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.icono == "success") {
                cargarDetalleCotizacion();
                document.getElementById("buscarCotizacion").value = "";
            } else {
                alertas(res.msg, res.icono);
            }
        }
    };
}

function cantidadCotizacion(id, e) {
    const url = base_url + "cotizaciones/itemCotizacion";
    let data = new FormData();
    data.append("id", id);
    data.append("item", e.target.value);
    data.append("campo", 'cantidad');
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.icono != "success") {
                alertas(res.msg, res.icono);
            }
            cargarDetalleCotizacion();
        }
    };
}

function medidaCotizacion(id, e) {
    const url = base_url + "cotizaciones/itemCotizacion";
    let data = new FormData();
    data.append("id", id);
    data.append("item", e.target.value);
    data.append("campo", 'medida');
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.icono != "success") {
                alertas(res.msg, res.icono);
            }
            cargarDetalleCotizacion();
        }
    };
}

function descuentoCotizacion(id, e) {
    const url = base_url + "cotizaciones/itemCotizacion";
    let data = new FormData();
    data.append("id", id);
    data.append("item", e.target.value);
    data.append("campo", 'descuento');
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.icono != "success") {
                alertas(res.msg, res.icono);
            }
            cargarDetalleCotizacion();
        }
    };
}

function impuestoCotizacion(id, e) {
    const url = base_url + "cotizaciones/itemCotizacion";
    let data = new FormData();
    data.append("id", id);
    data.append("item", e.target.value);
    data.append("campo", 'impuesto');
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.icono != "success") {
                alertas(res.msg, res.icono);
            }
            cargarDetalleCotizacion();
        }
    };
}


function procesarCotizacion() {
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
            let id_cliente = document.getElementById("id");
            let comentario = document.getElementById("comentario");
            let validez = document.getElementById("validez");
            let fila = document.querySelectorAll("#detalle_ tr").length;
            if (fila < 2) {
                alertas("La tabla esta vacia", "warning");
                return false;
            } else {
                const url = base_url + "cotizaciones/registrarCotizacion";
                const http = new XMLHttpRequest();
                let data = new FormData();
                data.append('id_cliente', id_cliente.value);
                data.append('validez', validez.value);
                data.append('comentario', comentario.value);
                http.open("POST", url, true);
                http.send(data);
                http.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);
                        if (res.icono == "success") {
                            setTimeout(() => {
                                cargarDetalleCotizacion();
                                Swal.fire({
                                    title: 'Generar Reporte',
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "SI, GENERAR",
                                    cancelButtonText: "CANCELAR",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.open(base_url + 'cotizaciones/generarFactura/' + res.id, '_blank');
                                    }
                                });
                            }, 2000);
                        }
                    }
                };
            }
        }
    });
}