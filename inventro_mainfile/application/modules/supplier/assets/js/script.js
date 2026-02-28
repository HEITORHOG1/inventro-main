$( function() {
    "use strict";
  var base_url = $('#base_url').val();
  var CSRF_TOKEN = $('#csrf_token').val();
 
  $('#Supledger').DataTable({ 
        responsive: true, 
        paging: true,
        dom: 'Bfrtip', 
        "lengthMenu": [[ 25, 50, 100, 150, 200, 500, -1], [ 25, 50, 100, 150, 200, 500, "All"]], 
       
        buttons: [  
            {extend: 'copy', className: 'btn-sm'}, 
            {extend: 'csv', title: 'ExampleFile', className: 'btn-sm'}, 
            {extend: 'excel', title: 'ExampleFile', className: 'btn-sm'}, 
            {extend: 'pdf', title: 'ExampleFile', className: 'btn-sm'}, 
            {extend: 'print', className: 'btn-sm'},
			{extend: 'colvis', className: 'btn-sm'}  
        ],
		"searching": true,
		"processing": true,
				 "serverSide": true,
				 "ajax":{
					url :base_url+'supplier/Supplierlist/ledgertotal',
					type: "post",
                    data: {'csrf_test_name': CSRF_TOKEN},
				  },
	    "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalcredit = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalcredit = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 			var pageTotalcredit = pageTotalcredit.toFixed(2); 
 			var totalcredit = totalcredit.toFixed(2); 
 			
 			 // Total over all pages
            totaldebit = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotaldebit = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 			var pageTotaldebit = pageTotaldebit.toFixed(2); 
 			var totaldebit = totaldebit.toFixed(2); 
 			
 			 // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 			var pageTotal = pageTotal.toFixed(2); 
 			var total = total.toFixed(2); 
            // Update footer
            $( api.column( 2 ).footer() ).html(pageTotalcredit);
            $( api.column( 3 ).footer() ).html(pageTotaldebit);
            $( api.column( 4).footer() ).html(pageTotal);
        }
    		});
 
  var table=$('#ledgerdetails').DataTable({
      "paging": true,
       lengthChange: false,
        buttons: [ 'copy', 'excel', 'pdf','print' ],
	 
      
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });
	 table.buttons().container()
        .appendTo( '#ledgerdetails_wrapper .col-md-6:eq(0)' );
  });
    
