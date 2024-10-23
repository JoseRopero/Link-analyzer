//Utilizamos jQuery para inicializar una tabla con librerías DataTables. Librería JavaScript para funcionalidades avanzadas en tablas
//como paginación, búsqueda, ordenación y filtrado.

jQuery(document).ready(function($) {  //Ejecuta el código dentro de la función solo cuando todo el html está cargado.
    $(".link-analyzer-table").DataTable({  //Se inicializa la tabla, después las opciones de configuración.
        "pageLength": 10,
        "order": [[0, "asc"]],  //El primer valor es el índice de la columna y el segundo el tipo de ordenación ("asc", "desc").
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

//Código que maneja las gráficas de Chart.js
jQuery(document).ready(function($) {
    // Solo inicializamos las gráficas si estamos en la página del Dashboard Visual
    if ($('#link-analyzer-dashboard').length) {
        // Gráfico de enlaces internos y externos
        var ctxInternosExternos = document.getElementById("enlacesInternosExternos").getContext("2d");
        var ctxDofollowNofollow = document.getElementById("enlacesDofollowNofollow").getContext("2d");

        new Chart(ctxInternosExternos, {
            type: "pie",
            data: {
                labels: ["Internos", "Externos"],
                datasets: [{
                    label: "Enlaces Internos vs Externos",
                    data: [linkAnalyzerData.internos, linkAnalyzerData.externos],  // Usar datos dinámicos
                    backgroundColor: ["#36a2eb", "#ff6384"]
                }]
            }
        });

        // Gráfico de dofollow y nofollow
        new Chart(ctxDofollowNofollow, {
            type: "doughnut",
            data: {
                labels: ["Dofollow", "Nofollow"],
                datasets: [{
                    label: "Enlaces Dofollow vs Nofollow",
                    data: [linkAnalyzerData.dofollow, linkAnalyzerData.nofollow],  // Usar datos dinámicos
                    backgroundColor: ["#4bc0c0", "#ffcd56"]
                }]
            }
        });
    }
});

