$(document).ready(function(){
    $("#example").DataTable({
        //destroy: true,
        //lengthChange: false,
        pageLength:10,
        
        lengthMenu:[10,50,100,500],
        language: {
             "url":"https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json",
        },
        order: [[0, 'desc']],    
        //dom: 'Bfrtip',
        //buttons: [ 'copy', 'excel', 'pdf', 'colvis' ],
    });
    });
    