$( function() {
"use strict";
  var base_url = $('#base_url').val();
  var totalpurchase = $('#totalpurchase').val();
  var CSRF_TOKEN = $('#csrf_token').val();
 var mytable = $('#PurList').DataTable({ 
             responsive: true,

             "aaSorting": [[4, "desc" ]],
             "columnDefs": [
                { "bSortable": false, "aTargets": [0,1,2,3,5,6] },

            ],
           'processing': true,
           'serverSide': true,
           
           'lengthMenu':[[10, 25, 50,100,250,500, totalpurchase], [10, 25, 50,100,250,500, "All"]],

             dom:"'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "Purchase LIst",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: "Purchase LIst", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: " Purchase LIst", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           },title: "<center>Purchase LIst</center>", className: "btn-sm prints"
            },{
                extend:"colvis"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
               'url':base_url+'purchase/purchase/CheckPurchaseList',
                 "data": function ( data) {
                  data.csrf_test_name = CSRF_TOKEN; // Include CSRF token in request dat
         data.fromdate = $('#from_date').val();
         data.todate = $('#to_date').val();


}
            },
          'columns': [
             { data: 'sl' },
             { data: 'chalan_no'},
             { data: 'purchase_id'},
             { data: 'supplier_name'},
             { data: 'purchase_date' },
             { data: 'total_amount',class:"totalpurchase"},
             { data: 'button'},
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
$( function() {
"use strict";
    $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });

    $("#filterid").on('click', function(){
   var x = document.getElementById("filterdiv");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
});



 
  } );
 
