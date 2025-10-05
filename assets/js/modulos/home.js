let myChart,
    myChart_;
document.addEventListener("DOMContentLoaded", function() {
    reporteStock();
    topProductos();
    actualizarGrafico();
})

function actualizarGrafico() {
    const anio = document.getElementById("year").value;
    let ctx = document.getElementById("ventas_mes").getContext("2d");
    if (myChart) {
        myChart.destroy();
    }
    const url = base_url + "administracion/actualizarGrafico/" + anio;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Setiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre",
                    ],
                    datasets: [{
                            label: "Compras",
                            data: [
                                res.compras.ene,
                                res.compras.feb,
                                res.compras.mar,
                                res.compras.abr,
                                res.compras.may,
                                res.compras.jun,
                                res.compras.jul,
                                res.compras.ago,
                                res.compras.sep,
                                res.compras.oct,
                                res.compras.nov,
                                res.compras.dic,
                            ],
                            backgroundColor: ["rgba(13, 0, 240, 0.8)"],
                            borderColor: ["rgb(255, 99, 132)"],
                            borderWidth: 1,
                        },
                        {
                            label: "Ventas",
                            data: [
                                res.ventas.ene,
                                res.ventas.feb,
                                res.ventas.mar,
                                res.ventas.abr,
                                res.ventas.may,
                                res.ventas.jun,
                                res.ventas.jul,
                                res.ventas.ago,
                                res.ventas.sep,
                                res.ventas.oct,
                                res.ventas.nov,
                                res.ventas.dic,
                            ],
                            backgroundColor: ["rgba(13, 202, 240, 0.8)"],
                            borderColor: ["rgb(255, 99, 132)"],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        }
    };
}

function reporteStock() {
    const url = base_url + "administracion/reporteStock";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let nombre = [];
            let cantidad = [];
            for (let i = 0; i < res.length; i++) {
                nombre.push(res[i]["descripcion"]);
                cantidad.push(res[i]["cantidad"]);
            }
            var ctx = document.getElementById("stockMinimo");
            var myPieChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: nombre,
                    datasets: [{
                        data: cantidad,
                        backgroundColor: [
                            "#024A86",
                            "#E7D40A",
                            "#581845",
                            "#C82A54",
                            "#EF280F",
                            "#8C4966",
                            "#FF689D",
                            "#E36B2C",
                            "#69C36D",
                            "#23BAC4",
                        ],
                    }, ],
                },
            });
        }
    };
}

function topProductos() {
    const url = base_url + "administracion/topProductos";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let nombre = [];
            let cantidad = [];
            for (let i = 0; i < res.length; i++) {
                nombre.push(res[i]["descripcion"]);
                cantidad.push(res[i]["total"]);
            }
            var ctx = document.getElementById("topProductos");
            var myPieChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: nombre,
                    datasets: [{
                        data: cantidad,
                        backgroundColor: [
                            "#C82A54",
                            "#69C36D",
                            "#EF280F",
                            "#E7D40A",
                            "#581845",
                            "#8C4966",
                            "#FF689D",
                            "#024A86",
                            "#E36B2C",
                            "#23BAC4",
                        ],
                    }, ],
                },
            });
        }
    };
}