<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-truck"></i> Zonas de Entrega
            <small>Gerenciar bairros e taxas de delivery</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li class="active">Zonas de Entrega</li>
        </ol>
    </section>

    <section class="content">
        <!-- Mensagens -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check"></i> <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-warning"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-map-marker"></i> Lista de Zonas</h3>
                <div class="box-tools">
                    <a href="<?php echo base_url('delivery/zones/form'); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Nova Zona
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if (empty($zones)): ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Nenhuma zona de entrega cadastrada.
                        <a href="<?php echo base_url('delivery/zones/form'); ?>">Clique aqui para adicionar.</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="zonesTable">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Bairro/Região</th>
                                    <th width="120">Taxa</th>
                                    <th width="150">Tempo Estimado</th>
                                    <th width="100">Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($zones as $index => $zone): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <i class="fa fa-map-marker text-primary"></i>
                                            <strong><?php echo htmlspecialchars($zone->nome); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($zone->taxa == 0): ?>
                                                <span class="label label-success">
                                                    <i class="fa fa-gift"></i> GRÁTIS
                                                </span>
                                            <?php else: ?>
                                                <span class="text-info">
                                                    <strong>R$ <?php echo number_format($zone->taxa, 2, ',', '.'); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo $zone->tempo_min; ?> - <?php echo $zone->tempo_max; ?> min
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-xs btn-<?php echo $zone->ativo ? 'success' : 'danger'; ?> toggle-status"
                                                    data-id="<?php echo $zone->id; ?>"
                                                    title="Clique para alterar">
                                                <i class="fa fa-<?php echo $zone->ativo ? 'check' : 'times'; ?>"></i>
                                                <?php echo $zone->ativo ? 'Ativo' : 'Inativo'; ?>
                                            </button>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('delivery/zones/form/' . $zone->id); ?>" 
                                               class="btn btn-primary btn-xs" title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-xs delete-zone" 
                                                    data-id="<?php echo $zone->id; ?>"
                                                    data-nome="<?php echo htmlspecialchars($zone->nome); ?>"
                                                    title="Excluir">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    Taxa GRÁTIS = R$ 0,00. Clique no status para ativar/desativar rapidamente.
                </small>
            </div>
        </div>

        <!-- Resumo -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-gift"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Entrega Grátis</span>
                        <span class="info-box-number">
                            <?php echo count(array_filter($zones, function($z) { return $z->taxa == 0 && $z->ativo; })); ?>
                        </span>
                        <span class="progress-description">zonas com taxa zero</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Zonas Ativas</span>
                        <span class="info-box-number">
                            <?php echo count(array_filter($zones, function($z) { return $z->ativo; })); ?>
                        </span>
                        <span class="progress-description">disponíveis no cardápio</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-map"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Zonas</span>
                        <span class="info-box-number"><?php echo count($zones); ?></span>
                        <span class="progress-description">cadastradas no sistema</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-warning"></i> Confirmar Exclusão</h4>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a zona <strong id="zoneNameToDelete"></strong>?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Excluir
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // DataTable
    $('#zonesTable').DataTable({
        "paging": false,
        "info": false,
        "searching": true,
        "language": {
            "search": "Buscar:",
            "zeroRecords": "Nenhuma zona encontrada"
        }
    });

    // Toggle Status
    $('.toggle-status').click(function() {
        var btn = $(this);
        var id = btn.data('id');
        
        $.ajax({
            url: '<?php echo base_url("delivery/zones/toggle_status/"); ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.new_status == 1) {
                        btn.removeClass('btn-danger').addClass('btn-success');
                        btn.html('<i class="fa fa-check"></i> Ativo');
                    } else {
                        btn.removeClass('btn-success').addClass('btn-danger');
                        btn.html('<i class="fa fa-times"></i> Inativo');
                    }
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete Zone
    $('.delete-zone').click(function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        
        $('#zoneNameToDelete').text(nome);
        $('#confirmDeleteBtn').attr('href', '<?php echo base_url("delivery/zones/delete/"); ?>' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
