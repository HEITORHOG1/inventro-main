$(document).ready(function() { 
"use strict";
var base_url = $('#base_url').val();
var totalitem = $('#totalitem').val();
var CSRF_TOKEN = $('#csrf_token').val();
   var table=$('#productList').DataTable({ 
             responsive: true,

             "aaSorting": [[ 1, "asc" ]],
             "columnDefs": [
                { "bSortable": false, "aTargets": [0,2,3,4,5,6,7,8,9] },

            ],
           'processing': true,
           'serverSide': true,

          
           'lengthMenu':[[10, 25, 50,100,250,500, totalitem], [10, 25, 50,100,250,500, "All"]],

             dom:"'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "ProductList",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: "ProductList", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           }, title: " ProductList", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5 ] //Your Colume value those you want print
                           },title: "<center>ProductList</center>", className: "btn-sm prints"
            },{
                extend:"colvis"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
               'url':base_url+'item/item/CheckProductList',
               data: function(d) {
                   d.csrf_test_name = $('#csrf_token').val();
               },
            },
          'columns': [
             { data: 'sl' },
             { data: 'name' },
             { data: 'product_model'},
             { data: 'supplier_name'},
             { data: 'price'},
             { data: 'purchase_price'},
             { data: 'unit' },
             { data: 'category'},
             { data: 'image'},
             { data: 'button'}
          ],




    });

table.buttons().container().appendTo('#productList .col-md-6:eq(0)' );


});



"use strict";
function barcodeqtcode(id){
    var CSRF_TOKEN = $('#csrf_token').val();
       var geturl=$("#url_"+id).val();
       var myurl =geturl+'/'+id;
        var dataString = "id="+id + "&_token=" + CSRF_TOKEN;
         $.ajax({
         type: "GET",
         url: myurl,
         data: dataString,
         success: function(data) {
             $('.barcodeqrcodinfo').html(data);
             $('#barcodebody').modal('show');
             $('.select2').select2();
         } 
    });
    }
    
"use strict";
    function qrcode(id){
        var CSRF_TOKEN = $('#csrf_token').val();
       var geturl=$("#qrcode_"+id).val();
       var myurl =geturl+'/'+id;
        var dataString = "id="+id  + "&_token=" + CSRF_TOKEN;
         $.ajax({
         type: "GET",
         url: myurl,
         data: dataString,
         success: function(data) {
             $('.barcodeqrcodinfo').html(data);
             $('#barcodebody').modal('show');
             $('.select2').select2();
         } 
    });
    }
