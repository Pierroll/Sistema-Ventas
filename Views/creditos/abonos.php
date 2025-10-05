<?php include "Views/templates/header.php"; ?>
<div class="card">
    <div class="card-header">
        Historial Abonos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover display responsive nowrap" id="tblAbonos" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>N° Credito</th>
                        <th>Abonado</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "Views/templates/footer.php"; ?>

<script>
    $("#tblAbonos").DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + "creditos/listarAbonos",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "fecha" },
            { data: "id_credito" },
            { data: "abono" },
            { data: "nombre" }
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
</script>
</body>

</html>