$(function () {
"use strict";
    var base_url = $('#base_url').val();
    var total_transaction = $('#total_transaction').val();
    var CSRF_TOKEN = $('#csrf_token').val();
    var mytable = $('#CashBookList').DataTable({
        responsive: true,

        "aaSorting": [[0, "desc"]],
        "columnDefs": [
            {"bSortable": false, "aTargets": [ 1, 2, 3]},

        ],
        'processing': true,
        'serverSide': true,

        'lengthMenu': [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],

        dom: "'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons: [{
            extend: "copy", exportOptions: {
                columns: [0, 1, 2, 3]
            }, className: "btn-sm prints"
        }
            , {
                extend: "csv", title: "Relatório Livro Caixa", exportOptions: {
                    columns: [0, 1, 2, 3]
                }, className: "btn-sm prints"
            }
            , {
                extend: "excel", exportOptions: {
                    columns: [0, 1, 2, 3]
                }, title: "Relatório Livro Caixa", className: "btn-sm prints"
            }
            , {
                extend: "pdf", exportOptions: {
                    columns: [0, 1, 2, 3]
                }, title: "Relatório Livro Caixa", className: "btn-sm prints"
            }
            , {
                extend: "print", exportOptions: {
                    columns: [0, 1, 2, 3]
                }, title: "<center>Relatório Livro Caixa</center>", className: "btn-sm prints"
            }, {
                extend: "colvis"
            }
        ],

        'serverMethod': 'post',
        'ajax': {
            'url': base_url + 'report/report/CheckCashBookReport',
            "data": function (data) {

                data.csrf_test_name = CSRF_TOKEN;
                data.fromdate = $('#from_date').val();
                data.todate = $('#to_date').val();



            }
        },
        'columns': [

            {data: 'date'},
            {data: 'description'},
            {data: 'payment', class: "totalpayment"},
            {data: 'receive', class: "total_receive"},

        ],

        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            api.columns('.totalpayment', {
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

            api.columns('.total_receive', {
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

    mytable.buttons().container().appendTo('#CashBookList .col-md-6:eq(0)');

    $("#btn-filter").on('click', function(){
        mytable.ajax.reload();
    });

});
