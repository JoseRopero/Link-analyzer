jQuery(document).ready(function($) {
    $(".link-analyzer-table").DataTable({
        "pageLength": 10,
        "order": [[0, "asc"]],
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ enlaces por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ enlaces",
            "infoEmpty": "No hay enlaces disponibles",
            "infoFiltered": "(filtrado de _MAX_ enlaces totales)",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});
