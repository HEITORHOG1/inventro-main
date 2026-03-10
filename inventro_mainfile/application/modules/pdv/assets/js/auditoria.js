/**
 * PDV Auditoria — DataTable server-side + filtros + detalhes
 *
 * Fase 10: Admin audit log page
 */
(function () {
    'use strict';

    // Actions that should be highlighted in red
    var ACOES_ALERTA = [
        'cupom_cancelado', 'venda_cancelada', 'sangria', 'devolucao',
        'desconto_item', 'desconto_venda', 'login_falha'
    ];

    // Badge color mapping for action types
    var ACAO_BADGES = {
        'login':                   'badge-success',
        'login_falha':             'badge-danger',
        'logout':                  'badge-secondary',
        'abertura_caixa':          'badge-info',
        'fechamento_caixa':        'badge-info',
        'venda_finalizada':        'badge-primary',
        'venda_inicio':            'badge-light',
        'venda_item':              'badge-light',
        'venda_item_cancelado':    'badge-warning',
        'venda_cancelada':         'badge-danger',
        'venda_suspensa':          'badge-warning',
        'venda_recuperada':        'badge-success',
        'sangria':                 'badge-danger',
        'suprimento':              'badge-success',
        'troca_operador':          'badge-info',
        'devolucao':               'badge-danger',
        'cupom_cancelado':         'badge-danger',
        'reimpressao':             'badge-secondary',
        'desconto_item':           'badge-warning',
        'desconto_venda':          'badge-warning',
        'fiado_venda':             'badge-warning',
        'recebimento_fiado':       'badge-success',
        'fiado_supervisor_autorizado': 'badge-info',
        'consulta_preco':          'badge-light',
        'leitura_x':               'badge-info',
        'gaveta_aberta':           'badge-secondary',
        'venda_item_generico':     'badge-light',
        'cadastro_cliente_rapido': 'badge-info'
    };

    var table;

    $(document).ready(function () {

        // Toggle filters
        $('#filterBtn').on('click', function () {
            $('#filterDiv').slideToggle(200);
        });

        // Initialize DataTable
        table = $('#tblAuditoria').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: base_url + 'pdv/auditoria/listar',
                type: 'POST',
                data: function (d) {
                    d.terminal_id = $('#filtro_terminal').val();
                    d.operador_id = $('#filtro_operador').val();
                    d.acao        = $('#filtro_acao').val();
                    d.data_inicio = $('#filtro_data_inicio').val();
                    d.data_fim    = $('#filtro_data_fim').val();
                    d.csrf_test_name = $('input[name="csrf_test_name"]').val();
                },
                dataSrc: function (json) {
                    // Update CSRF token if regenerated
                    if (json.csrf_token) {
                        $('input[name="csrf_test_name"]').val(json.csrf_token);
                    }
                    return json.aaData;
                }
            },
            columns: [
                { data: 'created_at' },
                { data: 'terminal' },
                { data: 'operador' },
                {
                    data: 'acao',
                    render: function (data) {
                        var badge = ACAO_BADGES[data] || 'badge-secondary';
                        return '<span class="badge ' + badge + '">' + $('<span>').text(data).html() + '</span>';
                    }
                },
                { data: 'entidade' },
                {
                    data: 'detalhes',
                    render: function (data, type, row) {
                        if (!data || data === '') return '-';
                        var maxLen = 80;
                        var display = data.length > maxLen ? data.substring(0, maxLen) + '...' : data;
                        return '<span class="text-muted small">' + display + '</span>';
                    }
                },
                { data: 'ip' }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                processing: '<i class="fas fa-spinner fa-spin fa-2x"></i>',
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Nenhum registro encontrado',
                infoFiltered: '(filtrado de _MAX_ registros)',
                zeroRecords: 'Nenhum registro encontrado',
                emptyTable: 'Nenhum dado disponivel',
                paginate: {
                    first: 'Primeiro',
                    previous: 'Anterior',
                    next: 'Proximo',
                    last: 'Ultimo'
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-outline-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-outline-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.pageSize = 'A4';
                        doc.pageOrientation = 'landscape';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-sm btn-outline-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            createdRow: function (row, data) {
                if (data.is_alerta) {
                    $(row).addClass('table-danger');
                }
            },
            drawCallback: function () {
                // Make rows clickable for detail view
                $('#tblAuditoria tbody tr').css('cursor', 'pointer');
            }
        });

        // Row click -> show detail modal
        $('#tblAuditoria tbody').on('click', 'tr', function () {
            var data = table.row(this).data();
            if (!data) return;

            $('#det_id').text(data.id);
            $('#det_data').text(data.created_at);
            $('#det_terminal').text(data.terminal);
            $('#det_operador').text(data.operador);
            $('#det_acao').text(data.acao);
            $('#det_entidade').text(data.entidade);
            $('#det_ip').text(data.ip);

            // Format JSON nicely
            var jsonText = data.detalhes_full || '{}';
            try {
                var parsed = JSON.parse(jsonText);
                jsonText = JSON.stringify(parsed, null, 2);
            } catch (e) {
                // Keep as-is
            }
            $('#det_json').text(jsonText);

            $('#modalDetalhes').modal('show');
        });

        // Filter button
        $('#btnFiltrar').on('click', function () {
            table.ajax.reload();
        });

        // Clear filters
        $('#btnLimpar').on('click', function () {
            $('#filtro_terminal').val('');
            $('#filtro_operador').val('');
            $('#filtro_acao').val('');
            $('#filtro_data_inicio').val('');
            $('#filtro_data_fim').val('');
            table.ajax.reload();
        });

        // Also reload on Enter in date fields
        $('#filtro_data_inicio, #filtro_data_fim').on('change', function () {
            table.ajax.reload();
        });

        // Dropdown change -> reload
        $('#filtro_terminal, #filtro_operador, #filtro_acao').on('change', function () {
            table.ajax.reload();
        });
    });
})();
