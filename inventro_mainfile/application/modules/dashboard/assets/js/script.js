(function($) {
    'use strict';
     //datatable
    $('#user_list').DataTable({ 
        responsive: true, 
        dom: "<'row'<'col-sm-4 text-center'B><'col-sm-4'f>>",
        buttons: [  
            {extend: 'copy', className: 'btn-sm'}, 
            {extend: 'csv', title: 'ExampleFile', className: 'btn-sm'}, 
            {extend: 'excel', title: 'ExampleFile', className: 'btn-sm', title: 'exportTitle'}, 
            {extend: 'pdf', title: 'ExampleFile', className: 'btn-sm'}, 
            {extend: 'print', className: 'btn-sm'} 
        ] 
    });


    $(".datepickerd").datepicker({
        dateFormat: 'yy-mm-dd',
        
    });

})(jQuery);
