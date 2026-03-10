$(function () {
"use strict";
    var base_url = $('#base_url').val();
    var mytable = $('#BankBookList').DataTable({
        responsive: true,

        "aaSorting": [[1, "desc"]],
        "columnDefs": [
            {"bSortable": false, "aTargets": [ 1, 2, 3]},

        ],
        'processing': true,
        'serverSide': true,

        'lengthMenu': [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],

        dom: "'<'col-sm-4'l><'col-sm-4 float-right'><'col-sm-4'>Bfrtip", buttons: [{
            extend: "copy", exportOptions: {
                columns: [0, 1, 2, 3, 4]
            }, className: "btn-sm prints"
        }
            , {
                extend: "csv", title: "Relatório Livro Banco", exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }, className: "btn-sm prints"
            }
            , {
                extend: "excel", exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }, title: "Relatório Livro Banco", className: "btn-sm prints"
            }
            , {
                extend: "pdf", exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }, title: "Relatório Livro Banco", className: "btn-sm prints"
            }
            , {
                extend: "print", exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }, title: "<center>Relatório Livro Banco</center>", className: "btn-sm prints"
            }, {
                extend: "colvis"
            }
        ],

        'serverMethod': 'post',
        'ajax': {
            'url': base_url + 'report/report/getBankBookreport',
            "data": function (data) {
                data.csrf_test_name = $('#csrf_token').val();
                data.fromdate = $('#from_date').val();
                data.todate = $('#to_date').val();
                data.bank_id = $('#bank_id').val();
            }
        },
        'columns': [
            {data: 'bank_name'},
            {data: 'date'},
            {data: 'deposit', class: "total_deposit"},
            {data: 'withdraw', class: "total_withdraw"},
            {data: 'balance', class: "total_balance"},

        ],

        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            api.columns('.total_deposit', {
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

            api.columns('.total_withdraw', {
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

            api.columns('.total_balance', {
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

    mytable.buttons().container().appendTo('#BankBookList .col-md-6:eq(0)');

    $("#btn-filter").on('click', function(){
        mytable.ajax.reload();
    });

});
