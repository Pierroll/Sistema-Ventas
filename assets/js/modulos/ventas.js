document.addEventListener("DOMContentLoaded", function() {
    //autocomplete venta
    $("#buscarVenta").autocomplete({
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
                        document.getElementById('errorBusquedaVenta').textContent = '';
                    } else {
                        document.getElementById('errorBusquedaVenta').textContent = 'NO HAY REGISTRO';
                        return;
                    }

                }
            });
        },
        select: function(event, ui) {
            agregarVenta(ui.item.id);
        },
    });
    cargarDetalleVenta();
})


function cargarDetalleVenta() {
    const url = base_url + "compras/listar/detalle_temp";
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
               <td width="120"><input type="number" class="form-control" value="${row.cantidad}" step="0.01" min="0.01" onchange="cantidadVenta(${row.id}, event)" /> </td>
               <td>${row.precio}</td>
               <td>${row.sub_total}</td>
               <td>
               <button class="btn btn-outline-danger" type="button" onclick="deleteDetalle(${row.id}, 2)">
               <i class="fas fa-trash-alt"></i></button>
               </td>
               </tr>`;
            });
            document.getElementById("tblDetalleVenta").innerHTML = html;
            document.getElementById("alert_total").textContent = res.total_pagar;
        }
    };
    desactivarCampos();
}

function agregarVenta(id_producto) {
    const url = base_url + "ventas/agregarVenta/" + id_producto;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            if (res.icono == "success") {
                cargarDetalleVenta();
                document.getElementById("buscarVenta").value = "";
            } else {
                alertas(res.msg, res.icono);
            }
        }
    };
}

function cantidadVenta(id, e) {
    const url = base_url + "ventas/cantidadVenta";
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
            cargarDetalleVenta();
        }
    };
}

//registrar clientes desde ventas
function activarCampos() {
    document.getElementById('activarCliente').classList.add('d-none');
    document.getElementById('desactivarCliente').classList.remove('d-none');
    document.getElementById("id").value = '';
    document.getElementById("clienteVenta").classList.remove("d-none");
    document.getElementById("errorBusqueda").textContent = '';
    document.getElementById("activarBotonGuardar").classList.remove("d-none");
    document.getElementById("dni").removeAttribute("disabled");
    document.getElementById("telefono").removeAttribute("disabled");
    document.getElementById("direccion").removeAttribute("disabled");
    document.getElementById('formulario').reset();

}

function desactivarCampos() {
    document.getElementById('activarCliente').classList.remove('d-none');
    document.getElementById('desactivarCliente').classList.add('d-none');
    document.getElementById("id").value = '';
    document.getElementById("clienteVenta").classList.add("d-none");
    document.getElementById("errorBusqueda").textContent = '';
    document.getElementById("activarBotonGuardar").classList.add("d-none");
    document.getElementById("dni").setAttribute("disabled", "disabled");
    document.getElementById("telefono").setAttribute("disabled", "disabled");
    document.getElementById("direccion").setAttribute("disabled", "disabled");
}

function registrarCliVenta(e) {
    e.preventDefault();
    const dni = document.getElementById("dni").value;
    const nombre = document.getElementById("buscarCliente").value;
    const telefono = document.getElementById("telefono").value;
    const direccion = document.getElementById("direccion").value;
    if (dni == "" || nombre == "" || telefono == "" || direccion == "") {
        alertas("Todo los campos son obligatorios", "warning");
    } else {
        const url = base_url + "clientes/registrar";
        const frm = document.getElementById("formulario");
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(new FormData(frm));
        http.upload.addEventListener("progress", function() {
            document.getElementById("btnAccion").textContent = "Procesando...";
        });
        http.addEventListener("load", function() {
            document.getElementById("btnAccion").textContent = "GUARDAR";
        });
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("btnAccion").textContent = "Procesando...";
                const res = JSON.parse(this.responseText);
                alertas(res.msg, res.icono);
                if (res.icono == "success") {
                    desactivarCampos();
                    document.getElementById('id').value = res.id_cliente;
                }
            }
        };
    }
}

function procesarVenta() {
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
                const url = base_url + "ventas/registrarVenta";
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
                                cargarDetalleVenta();
                                generarReportes(2, res.id);
                            }, 2000);
                        }
                    }
                };
            }
        }
    });
}