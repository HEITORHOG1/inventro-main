$(function() {
    "use strict";
    var base_url = $('#base_url').val();
    var frm = $("#bank_form");
    var CSRF_TOKEN = $('#csrf_token').val();
    frm.on('submit', function(e) {
        e.preventDefault(); 
        $.ajax({
            url : base_url+"bank/bank/bank_form",
            method : $(this).attr('method'),
            dataType : 'json',
            data : frm.serialize() + "&_token=" + CSRF_TOKEN,
            success: function(data) 
            {
               if(data.status==true){
                Toast.fire({
                      type: 'success',
                      title: data.message
                  });
                location.reload();

          }else{

            Toast.fire({
                  type: 'error',
                  title: data.message
              });

          }
            },
            error: function(xhr)
            {
                alert('failed!');
            }
        });
    });

    $(function() {
    "use strict";
   var ufrm = $("#upbank_form");
   var base_url = $('#base_url').val();
   var CSRF_TOKEN = $('#csrf_token').val();
    ufrm.on('submit', function(e) {
        e.preventDefault(); 
        $.ajax({
            url : base_url+"bank/bank/bank_form",
            method : $(this).attr('method'),
            dataType : 'json',
            data : ufrm.serialize() + "&_token=" + CSRF_TOKEN,
            success: function(data) 
            {
               if(data.status==true){
            Toast.fire({
                  type: 'success',
                  title: data.message
              });
            location.reload();

          }else{

            Toast.fire({
                  type: 'error',
                  title: data.message
              });

          }
            },
            error: function(xhr)
            {
                alert('failed!');
            }
        });
    });
    });


    });

"use strict";
function deletebank(id){
 
    if(confirm('Do you want to delete')===false){
      return false;
    } 
 
      var base_url = $('#base_url').val();
      var submit_url = base_url+"bank/bank/delete_bank";
      var dataString = "id="+id;
      var CSRF_TOKEN = $('#csrf_token').val();

      $.ajax({

        type: 'POST',
        url: submit_url,
        data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
        success: function(res) {
     
          if(res){
            Toast.fire({
                  type: 'success',
                  title: 'Delete Successfull'
              });
              window.location.reload();
          }else{
            Toast.fire({
                  type: 'error',
                  title: 'Wops! Thre have some problems please try again'
              });
          }
           

        },error: function() {

        }
      });


  }

  $( function() {
 "use strict";
 var CSRF_TOKEN = $('#csrf_token').val();
  var base_url = $('#base_url').val();
 var mytable = $('#ledger').DataTable({ 
             responsive: true,

             "aaSorting": [[2, "desc" ]],
             "columnDefs": [
                { "bSortable": false, "aTargets": [0,1,3,4,5,6] },

            ],
           'processing': true,
           'serverSide': true,
           
           'lengthMenu':[[10, 25, 50,100,250,500,1000], [10, 25, 50,100,250,500, 1000]],

             dom:"'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4 ,5,6] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "Bank Ledger",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5,6] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5,6] //Your Colume value those you want print
                           }, title: "Bank Ledger", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5,6] //Your Colume value those you want print
                           }, title: " Bank Ledger", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4,5,6] //Your Colume value those you want print
                           },title: "<center>Bank Ledger</center>", className: "btn-sm prints"
            },{
                extend:"colvis"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
                  'url':base_url+'bank/bank/search_bankledger',
                 "data": function ( data) {
                  data.csrf_test_name = CSRF_TOKEN; // Include CSRF token in request dat
                  data.fromdate = $('#from_date').val();
                  data.todate = $('#to_date').val();
                  data.bank_id = $('#bank_id').val();
                }
            },
          'columns': [
             { data: 'sl' },
             { data: 'bank_name'},
             { data: 'date'},
             { data: 'description'},
             { data: 'debit'},
             { data: 'credit'},
             { data: 'balance',class:"totalbalance"},
             
          ],

  "footerCallback": function(row, data, start, end, display) {
  var api = this.api();
   api.columns('.totalbalance', {
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
   
mytable.buttons().container().appendTo('#ledger .col-md-6:eq(0)' );

$("#btn-filter").on('click', function(){
  mytable.ajax.reload();  
});

});

