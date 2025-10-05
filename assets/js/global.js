let myModal;
const buttons = [{
        //Botón para Excel
        extend: "excelHtml5",
        footer: true,
        title: "Reporte",
        filename: "Reporte",
        //Aquí es donde generas el botón personalizado
        text: '<span class="badge bg-success"><i class="fas fa-file-excel"></i></span>',
    },
    //Botón para PDF
    {
        extend: "pdfHtml5",
        download: "open",
        footer: true,
        title: "Reporte",
        filename: "Reporte",
        text: '<span class="badge bg-danger"><i class="fas fa-file-pdf"></i></span>',
        exportOptions: {
            columns: [0, 1, 2, 3, 5],
        },
    },
    //Botón para print
    {
        extend: "print",
        footer: true,
        filename: "Reporte",
        text: '<span class="badge bg-warning"><i class="fas fa-print"></i></span>',
    },
    //Botón para print
    {
        extend: "csvHtml5",
        footer: true,
        filename: "Reporte",
        text: '<span class="badge bg-success"><i class="fas fa-file-csv"></i></span>',
    },
    {
        extend: "colvis",
        text: '<span class="badge bg-info"><i class="fas fa-columns"></i></span>',
        postfixButtons: ["colvisRestore"],
    },
];
const dom =
    "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-5'i><'col-sm-7'p>>";
document.addEventListener("DOMContentLoaded", function() {
    $("input[type='text']").on("keypress", function() {
        $input = $(this);
        setTimeout(function() {
            $input.val($input.val().toUpperCase());
        }, 50);
    });
    let tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    if (document.getElementById("myModal")) {
        myModal = new bootstrap.Modal(document.getElementById("myModal"));
    }
    //busqueda por rango de fecha
    $("#min").change(function(e) {
        if (e.target.name == "compras_min") {
            t_h_c.draw();
        } else if (e.target.name == "ventas_min") {
            t_h_v.draw();
        } else if (e.target.name == "inventario_min") {
            t_inventario.draw();
        } else {
            tbl.draw();
        }
    });
    $("#max").change(function(e) {
        if (e.target.name == "compras_max") {
            t_h_c.draw();
        } else if (e.target.name == "ventas_max") {
            t_h_v.draw();
        } else if (e.target.name == "inventario_max") {
            t_inventario.draw();
        } else {
            tbl.draw();
        }
    });

});

//busqueda rango de fechas
if (document.getElementById("min") && document.getElementById("max")) {
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let desde = $("#min").val();
        let hasta = $("#max").val();
        let fecha = data[2].trim();
        if (desde == "" || hasta == "") {
            return true;
        }
        if (fecha >= desde && fecha <= hasta) {
            return true;
        } else {
            return false;
        }
    });
}
//funciones gloables
function nuevoModal(title) {
    document.getElementById("id").value = "";
    document.getElementById("title").textContent = title.toUpperCase();
    document.getElementById("btnAccion").textContent = "REGISTRAR";
    document.getElementById("formulario").reset();
    myModal.show();
}

function insertarRegistros(url, frm, table, accion = 'btnAccion') {
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(new FormData(frm));
    http.upload.addEventListener("progress", function() {
        document.getElementById(accion).textContent = "Procesando...";
    });
    http.addEventListener("load", function() {
        document.getElementById(accion).textContent = "GUARDAR";
    });
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertas(res.msg, res.icono);
            if (res.icono == "success") {
                myModal.hide();
                if (table != null) {
                    table.ajax.reload();
                }
            }
        }
    };
}

function eliminarRegistro(url, table) {
    Swal.fire({
        title: "Esta seguro de eliminar?",
        text: "El registro no se eliminará de forma permanente, solo cambiará el estado a inactivo!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si!",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (res.icono == "success") {
                        table.ajax.reload();
                    }
                }
            };
        }
    });
}

function anular(url, table, nombre) {
    Swal.fire({
        title: "Esta seguro de anular la " + nombre.toUpperCase(),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si!",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (res.icono == "success") {
                        table.ajax.reload();
                    }
                }
            };
        }
    });
}

function reingresarRegistro(url, table) {
    Swal.fire({
        title: "Esta seguro de restaurar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si!",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (res.icono == "success") {
                        window.location.reload();
                    }
                }
            };
        }
    });
}

function alertas(mensaje, icono) {
    Swal.fire({
        position: "top-end",
        icon: icono,
        title: mensaje.toUpperCase(),
        showConfirmButton: false,
        timer: 3000,
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