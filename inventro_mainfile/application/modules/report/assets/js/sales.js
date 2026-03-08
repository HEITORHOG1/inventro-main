$(function () {
"use strict";
    var base_url = $('#base_url').val();
    var totalsales = $('#totalsales').val();
    var CSRF_TOKEN = $('#csrf_token').val();
    var mytable = $('#SaleList').DataTable({
        responsive: true,

        "aaSorting": [[3, "desc"]],
        "columnDefs": [
            {"bSortable": false, "aTargets": [0, 1, 2, 4]},

        ],
        'processing': true,
        'serverSide': true,

        'lengthMenu': [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],

        dom: "'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons: [{
            extend: "copy", exportOptions: {
                columns: [0, 1, 2, 3, 4] //Your Colume value those you want
            }, className: "btn-sm prints"
        }
            , {
                extend: "csv", title: "Sales Report", exportOptions: {
                    columns: [0, 1, 2, 3, 4] //Your Colume value those you want print
                }, className: "btn-sm prints"
            }
            , {
                extend: "excel", exportOptions: {
                    columns: [0, 1, 2, 3, 4] //Your Colume value those you want print
                }, title: "Sales Report", className: "btn-sm prints"
            }
            , {
                extend: "pdf", exportOptions: {
                    columns: [0, 1, 2, 3, 4] //Your Colume value those you want print
                }, title: " Sales Report", className: "btn-sm prints"
            }
            , {
                extend: "print", exportOptions: {
                    columns: [0, 1, 2, 3, 4] //Your Colume value those you want print
                }, title: "<center>Purchase Report</center>", className: "btn-sm prints"
            }, {
                extend: "colvis"
            }
        ],

        'serverMethod': 'post',
        'ajax': {
            'url': base_url + 'report/report/CheckSalesReport',
            "data": function (data) {
                data.csrf_test_name = $('#csrf_token').val(); // Include fresh CSRF token
                data.fromdate = $('#from_date').val();
                data.todate = $('#to_date').val();
                data.customer_id = $('#customer_id').val();


            }
        },
        'columns': [
            {data: 'sl'},
            {data: 'invoice_id'},
            {data: 'customer_name'},
            {data: 'date'},
            {data: 'total_amount', class: "totalsales"},

        ],

        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            api.columns('.totalsales', {
                page: 'current'
            }).every(function () {
                var sum = this
                    .data()
                    .reduce(function (a, b) {
                        var x = parseFloat(a) || 0;
                        var y = parseFloat(b) || 0;
                        return x + y;
                    }, 0);
                $(this.footer()).html(sum.toFixed(2, 2));
            });
        }


    });

    mytable.buttons().container().appendTo('#SaleList .col-md-6:eq(0)');

   $("#btn-filter").on('click', function(){
        mytable.ajax.reload();
    });

});

 
