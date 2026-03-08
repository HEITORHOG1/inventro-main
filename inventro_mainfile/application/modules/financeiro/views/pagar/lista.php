<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/financeiro/assets/css/financeiro.css">

<div class="card card-primary card-outline">
    <div class="card-header">
        <h4>
            <?php echo makeString(['contas_a_pagar']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('financeiro/contas_pagar/form') ?>" class="btn btn-primary btn-md">
                    <i class="ti-plus" aria-hidden="true"></i>
                    <?php echo makeString(['nova_conta_pagar']); ?>
                </a>
            </small>
        </h4>

        <button class="btn btn-primary btn-sm mt-2" id="filterBtn">
            <i class="fas fa-filter"></i> <?php echo makeString(['filter']); ?>
        </button>

        <div class="row mt-3" id="filterDiv" style="display:none;">
            <div class="col-sm-12 filterbox p-3 bg-light rounded">
                <form class="form-inline" id="filterForm">
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2"><?php echo makeString(['status']); ?>:</label>
                        <select name="status" id="filtro_status" class="form-control form-control-sm">
                            <option value=""><?php echo makeString(['todos_status']); ?></option>
                            <option value="aberto"><?php echo makeString(['status_aberto']); ?></option>
                            <option value="parcial"><?php echo makeString(['status_parcial']); ?></option>
                            <option value="pago"><?php echo makeString(['status_pago']); ?></option>
                            <option value="vencido"><?php echo makeString(['status_vencido']); ?></option>
                            <option value="cancelado"><?php echo makeString(['status_cancelado']); ?></option>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2"><?php echo makeString(['supplier']); ?>:</label>
                        <select name="fornecedor_id" id="filtro_fornecedor" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <?php foreach($fornecedores as $f): ?>
                            <option value="<?php echo $f->supplier_id; ?>"><?php echo $f->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2"><?php echo makeString(['from_date']); ?>:</label>
                        <input type="date" name="data_inicio" id="filtro_data_inicio" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2"><?php echo makeString(['to_date']); ?>:</label>
                        <input type="date" name="data_fim" id="filtro_data_fim" class="form-control form-control-sm">
                    </div>
                    <button type="button" id="btnFiltrar" class="btn btn-success btn-sm mb-2">
                        <i class="fas fa-search"></i> <?php echo makeString(['find']); ?>
                    </button>
                    <button type="button" id="btnLimpar" class="btn btn-secondary btn-sm mb-2 ml-2">
                        <i class="fas fa-eraser"></i> Limpar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php
        $message = $this->session->flashdata('message');
        $exception = $this->session->flashdata('exception');
        if ($message) echo '<div class="alert alert-success">'.$message.'</div>';
        if ($exception) echo '<div class="alert alert-danger">'.$exception.'</div>';
        
        $currency = $get_appsetting->currencyname;
        $position = $get_appsetting->position;
        ?>
        
        <div class="table-responsive">
            <table id="tblContasPagar" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo makeString(['codigo']); ?></th>
                        <th><?php echo makeString(['description']); ?></th>
                        <th><?php echo makeString(['supplier']); ?></th>
                        <th><?php echo makeString(['categoria']); ?></th>
                        <th class="text-right"><?php echo makeString(['valor_original']); ?></th>
                        <th class="text-right"><?php echo makeString(['valor_pago']); ?></th>
                        <th class="text-right"><?php echo makeString(['valor_pendente']); ?></th>
                        <th><?php echo makeString(['data_vencimento']); ?></th>
                        <th><?php echo makeString(['status']); ?></th>
                        <th><?php echo makeString(['action']); ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo makeString(['total']); ?>:</th>
                        <th id="totalOriginal" class="text-right">0,00</th>
                        <th id="totalPago" class="text-right">0,00</th>
                        <th id="totalPendente" class="text-right">0,00</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="currency" value="<?php echo $currency; ?>">
<input type="hidden" id="position" value="<?php echo $position; ?>">

<script>
$(document).ready(function() {
    // Toggle filtro
    $('#filterBtn').click(function() {
        $('#filterDiv').slideToggle();
    });
    
    // DataTable
    var table = $('#tblContasPagar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'financeiro/contas_pagar/get_lista',
            type: 'POST',
            data: function(d) {
                d.csrf_test_name = $('#csrf_token').val();
                d.status = $('#filtro_status').val();
                d.fornecedor_id = $('#filtro_fornecedor').val();
                d.data_inicio = $('#filtro_data_inicio').val();
                d.data_fim = $('#filtro_data_fim').val();
            }
        },
        columns: [
            {data: 'sl'},
            {data: 'codigo'},
            {data: 'descricao'},
            {data: 'fornecedor'},
            {data: 'categoria'},
            {data: 'valor_original', className: 'text-right'},
            {data: 'valor_pago', className: 'text-right'},
            {data: 'valor_pendente', className: 'text-right font-weight-bold'},
            {data: 'vencimento'},
            {data: 'status'},
            {data: 'button', orderable: false}
        ],
        order: [[8, 'asc']], // Ordenar por vencimento
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            var totalOriginal = 0, totalPago = 0, totalPendente = 0;
            
            data.forEach(function(row) {
                totalOriginal += parseFloat(row.valor_original.replace('.', '').replace(',', '.')) || 0;
                totalPago += parseFloat(row.valor_pago.replace('.', '').replace(',', '.')) || 0;
                totalPendente += parseFloat(row.valor_pendente.replace('.', '').replace(',', '.')) || 0;
            });
            
            $('#totalOriginal').html(totalOriginal.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
            $('#totalPago').html(totalPago.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
            $('#totalPendente').html(totalPendente.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
        }
    });
    
    // Filtrar
    $('#btnFiltrar').click(function() {
        table.ajax.reload();
    });
    
    // Limpar filtros
    $('#btnLimpar').click(function() {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });
    
    var base_url = $('#base_url').val();
});
</script>
