<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>admin_assets/plugins/daterangepicker/daterangepicker.css">

<div class="card card-primary card-outline">
    <div class="card-header">
        <h4>
            <i class="fas fa-clipboard-list"></i> Auditoria PDV
        </h4>

        <button class="btn btn-primary btn-sm mt-2" id="filterBtn">
            <i class="fas fa-filter"></i> Filtros
        </button>

        <div class="row mt-3" id="filterDiv" style="display:none;">
            <div class="col-sm-12 filterbox p-3 bg-light rounded">
                <form class="form-inline" id="filterForm">
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2">Terminal:</label>
                        <select name="terminal_id" id="filtro_terminal" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <?php foreach ($terminais as $t): ?>
                            <option value="<?php echo (int) $t->id; ?>"><?php echo html_escape($t->numero . ' - ' . $t->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2">Operador:</label>
                        <select name="operador_id" id="filtro_operador" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <?php foreach ($operadores as $o): ?>
                            <option value="<?php echo (int) $o->id; ?>"><?php echo html_escape($o->nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2">Acao:</label>
                        <select name="acao" id="filtro_acao" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            <?php foreach ($acoes as $a): ?>
                            <option value="<?php echo html_escape($a->acao); ?>"><?php echo html_escape($a->acao); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2">De:</label>
                        <input type="date" name="data_inicio" id="filtro_data_inicio" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2">Ate:</label>
                        <input type="date" name="data_fim" id="filtro_data_fim" class="form-control form-control-sm">
                    </div>
                    <button type="button" id="btnFiltrar" class="btn btn-success btn-sm mb-2">
                        <i class="fas fa-search"></i> Filtrar
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
        if ($message) echo '<div class="alert alert-success">' . html_escape($message) . '</div>';
        if ($exception) echo '<div class="alert alert-danger">' . html_escape($exception) . '</div>';
        ?>

        <div class="table-responsive">
            <table id="tblAuditoria" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Terminal</th>
                        <th>Operador</th>
                        <th>Acao</th>
                        <th>Entidade</th>
                        <th>Detalhes</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered" id="tblDetalhesInfo">
                    <tbody>
                        <tr><th style="width:120px">ID</th><td id="det_id"></td></tr>
                        <tr><th>Data/Hora</th><td id="det_data"></td></tr>
                        <tr><th>Terminal</th><td id="det_terminal"></td></tr>
                        <tr><th>Operador</th><td id="det_operador"></td></tr>
                        <tr><th>Acao</th><td id="det_acao"></td></tr>
                        <tr><th>Entidade</th><td id="det_entidade"></td></tr>
                        <tr><th>IP</th><td id="det_ip"></td></tr>
                    </tbody>
                </table>
                <h6 class="mt-3">Detalhes (JSON):</h6>
                <pre id="det_json" class="bg-dark text-light p-3 rounded" style="max-height:400px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>application/modules/pdv/assets/js/auditoria.js"></script>
