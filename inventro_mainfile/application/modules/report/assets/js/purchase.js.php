$( function() {
"use strict";
  var base_url = $('#base_url').val();
  var totalpurchase = $('#totalpurchase').val();
  var CSRF_TOKEN = $('#csrf_token').val();
 var mytable = $('#PurList').DataTable({ 
             responsive: true,

             "aaSorting": [[4, "desc" ]],
             "columnDefs": [
                { "bSortable": false, "aTargets": [0,1,2,3,5] },

            ],
           'processing': true,
           'serverSide': true,
           
           'lengthMenu':[[10, 25, 50,100,250,500,1000], [10, 25, 50,100,250,500, 1000]],

             dom:"'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "Purchase Report",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: "Purchase Report", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: " Purchase Report", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           },title: "<center>Purchase Report</center>", className: "btn-sm prints"
            },{
                extend:"colvis"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
               'url':base_url+'report/report/CheckPurchasereport',
                 "data": function ( data) {

        data.csrf_test_name = CSRF_TOKEN; // Include CSRF token in request dat
         data.fromdate = $('#from_date').val();
         data.todate = $('#to_date').val();
         data.supplier_id = $('#supplier_id').val();


}
            },
          'columns': [
             { data: 'sl' },
             { data: 'chalan_no'},
             { data: 'purchase_id'},
             { data: 'supplier_name'},
             { data: 'purchase_date' },
             { data: 'total_amount',class:"totalpurchase"},
             
          ],

  "footerCallback": function(row, data, start, end, display) {
  var api = this.api();
   api.columns('.totalpurchase', {
    page: 'current'
  }).every(function() {
    var sum = this
      .data()
      .reduce(function(a, b) {
        var x = parseFloat(a) || 0;
        var y = parseFloat(b) || 0;
        return x + y;
      }, 0);
    $(this.footer()).html(sum.toFixed(2, 2));
  });
}


    });
   
mytable.buttons().container().appendTo('#PurList .col-md-6:eq(0)' );

$("#btn-filter").on('click', function(){
mytable.ajax.reload();  
});

});

 
