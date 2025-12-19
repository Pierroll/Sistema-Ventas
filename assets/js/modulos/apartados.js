const eliminar = document.getElementById("btnEliminar");
const btnAccion = document.getElementById("btnAccion");
document.addEventListener("DOMContentLoaded", function() {
    //autocomplete venta
    $("#buscarProducto").autocomplete({
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
                        document.getElementById('errorBusquedaProducto').textContent = '';
                    } else {
                        document.getElementById('errorBusquedaProducto').textContent = 'NO HAY REGISTRO';
                        return;
                    }

                }
            });
        },
        select: function(event, ui) {
            agregarProducto(ui.item.id);
        },
    });
    cargarDetalleApart();
    let calendarEl = document.getElementById("calendar");
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "es",
        headerToolbar: {
            left: "prev next today",
            center: "title",
            right: "dayGridMonth timeGridWeek listWeek",
        },
        events: base_url + "apartados/listarApartados",
        editable: true,
        dateClick: function(info) {
            //console.log(info);
            document.getElementById("id").value = "";
            eliminar.classList.add("d-none");
            document.getElementById("start").value = info.dateStr;
            document.getElementById("btnAccion").textContent = "Registrar";
            document.getElementById("titulo").textContent = "Registro de Evento";
            myModal.show();
        },
        eventClick: function(info) {
            const url = base_url + "apartados/verficar/" + info.event.id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    const deuda = parseFloat(res.total) - parseFloat(res.abono);
                    Swal.fire({
                        title: "Mensaje?",
                        text: "Entregar los productos al cliente: " +
                            info.event.title +
                            " DUEDA: " +
                            deuda.toFixed(2),
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si!",
                        cancelButtonText: "No",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const id_apartado = info.event.id;
                            const url = base_url + "apartados/entrega/" + id_apartado;
                            const http = new XMLHttpRequest();
                            http.open("GET", url, true);
                            http.send();
                            http.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    const res = JSON.parse(this.responseText);
                                    alertas(res.msg, res.icono);
                                    calendar.refetchEvents();
                                }
                            };
                        }
                    });
                }
            };
        },
    });
    calendar.render();
    btnAccion.addEventListener("click", function() {
        const id_cliente = document.getElementById("id").value;
        const select_cliente = document.getElementById("buscarCliente").value;
        const abono = document.getElementById("abono").value;
        const start = document.getElementById("start").value;
        const hora = document.getElementById("hora").value;
        if (id_cliente == "" || select_cliente == "" || abono == "" || start == "" || hora == "") {
            Swal.fire("Aviso", "Todo los campos con * son requeridos", "warning");
        } else {
            const url = base_url + "apartados/registrar";
            let data = new FormData();
            data.append('start', start);
            data.append('hora', hora);
            data.append('id', id_cliente);
            data.append('abono', abono);
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        calendar.refetchEvents();
                        cargarDetalleApart();
                        if (res.id_apartado) {
                            setTimeout(() => {
                                window.open(
                                    base_url + "apartados/generarPdf/" + res.id_apartado
                                );
                            }, 2000);
                        }
                    }
                    myModal.hide();
                    Swal.fire("Aviso", res.msg, res.icono);
                }
            };
        }
    });
});

function agregarProducto(id_producto) {
    const url = base_url + "apartados/agregar/" + id_producto;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            if (res.icono == "success") {
                cargarDetalleApart();
                document.getElementById("buscarProducto").value = "";
            } else {
                alertas(res.msg, res.icono);
            }
        }
    };
}

function cantidadApartado(id, e) {
    const url = base_url + "apartados/cantidadApartado";
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
            cargarDetalleApart();
        }
    };
}

function ingresarApartado(e) {
    e.preventDefault();
    const cant = document.getElementById("cantidad").value;
    const precio = document.getElementById("precio").value;
    document.getElementById("sub_total").value = precio * cant;
    if (e.which == 13) {
        if (cant > 0) {
            const url = base_url + "apartados/ingresarApartado";
            const frm = document.getElementById("formulario");
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(new FormData(frm));
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    document.getElementById("id").value = "";
                    document.getElementById("codigo").value = "";
                    document.getElementById("nombre").value = "";
                    document.getElementById("cantidad").value = "";
                    document.getElementById("precio").value = "";
                    document.getElementById("sub_total").value = "";
                    cargarDetalleApart();
                    document
                        .getElementById("cantidad")
                        .setAttribute("disabled", "disabled");
                    document.getElementById("codigo").focus();
                }
            };
        }
    }
}

function cargarDetalleApart() {
    const url = base_url + "apartados/listar";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = "";
            res.detalle.forEach((row) => {
                html += `<tr>
                 <td>${row.id}</td>
                 <td>${row.descripcion}</td>
                 <td width="120"><input type="number" class="form-control" value="${row.cantidad}" step="0.01" min="0.01" onchange="cantidadApartado(${row.id}, event)" /> </td>
                 <td>${row.precio}</td>
                 <td>${row.subTotal}</td>
                 <td>
                 <button class="btn btn-danger" type="button" onclick="deleteDetalle(${row.id}, 3)">
                 <i class="fas fa-trash-alt"></i></button>
                 </td>
                 </tr>`;
            });
            document.getElementById("tblDetalleApart").innerHTML = html;
            document.getElementById("total").textContent = res.total_pagar;
        }
    };
}