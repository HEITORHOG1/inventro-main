<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-map-marker-alt"></i> <?php echo makeString(['zonas_entrega']); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('delivery/zones/form'); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> <?php echo makeString(['add_new']); ?>
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php
        $message   = $this->session->flashdata('message');
        $exception = $this->session->flashdata('exception');
        if ($message) echo '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $message . '</div>';
        if ($exception) echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $exception . '</div>';
        ?>

        <?php if (empty($zones)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhuma zona de entrega cadastrada.
                <a href="<?php echo base_url('delivery/zones/form'); ?>">Clique aqui para adicionar.</a>
            </div>
        <?php else: ?>
            <!-- Resumo -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo count(array_filter($zones, function($z) { return $z->taxa == 0 && $z->ativo; })); ?></h3>
                            <p>Entrega Grátis</p>
                        </div>
                        <div class="icon"><i class="fas fa-gift"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo count(array_filter($zones, function($z) { return $z->ativo; })); ?></h3>
                            <p>Zonas Ativas</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo count($zones); ?></h3>
                            <p>Total de Zonas</p>
                        </div>
                        <div class="icon"><i class="fas fa-map"></i></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="zonesTable">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Bairro/Região</th>
                            <th width="120"><?php echo makeString(['delivery_fee']); ?></th>
                            <th width="150">Tempo Estimado</th>
                            <th width="100">Status</th>
                            <th width="120"><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zones as $index => $zone): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    <strong><?php echo html_escape($zone->nome); ?></strong>
                                </td>
                                <td>
                                    <?php if ($zone->taxa == 0): ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-gift"></i> GRÁTIS
                                        </span>
                                    <?php else: ?>
                                        <strong class="text-info">R$ <?php echo number_format($zone->taxa, 2, ',', '.'); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-clock"></i>
                                    <?php echo (int)$zone->tempo_min; ?> - <?php echo (int)$zone->tempo_max; ?> min
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-xs btn-<?php echo $zone->ativo ? 'success' : 'danger'; ?> toggle-status"
                                            data-id="<?php echo (int)$zone->id; ?>"
                                            title="Clique para alterar">
                                        <i class="fas fa-<?php echo $zone->ativo ? 'check' : 'times'; ?>"></i>
                                        <?php echo $zone->ativo ? 'Ativo' : 'Inativo'; ?>
                                    </button>
                                </td>
                                <td>
                                    <a href="<?php echo base_url('delivery/zones/form/' . (int)$zone->id); ?>"
                                       class="btn btn-primary btn-xs" title="<?php echo makeString(['edit']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs delete-zone"
                                            data-id="<?php echo (int)$zone->id; ?>"
                                            data-nome="<?php echo html_escape($zone->nome); ?>"
                                            title="<?php echo makeString(['delete']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-footer text-muted">
        <small><i class="fas fa-info-circle"></i> Taxa GRÁTIS = R$ 0,00. Clique no status para ativar/desativar.</small>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white"><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a zona <strong id="zoneNameToDelete"></strong>?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo makeString(['cancel']); ?></button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> <?php echo makeString(['delete']); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#zonesTable').DataTable({
        "paging": false,
        "info": false,
        "searching": true,
        "language": {
            "search": "Buscar:",
            "zeroRecords": "Nenhuma zona encontrada"
        }
    });

    // Toggle Status via AJAX
    $('.toggle-status').click(function() {
        var btn = $(this);
        var id = btn.data('id');
        $.ajax({
            url: '<?php echo base_url("delivery/zones/toggle_status/"); ?>' + id,
            type: 'POST',
            data: { 'csrf_test_name': $('input[name="csrf_test_name"]').val() },
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    $('input[name="csrf_test_name"]').val(response.csrf_token);
                }
                if (response.success) {
                    if (response.new_status == 1) {
                        btn.removeClass('btn-danger').addClass('btn-success');
                        btn.html('<i class="fas fa-check"></i> Ativo');
                    } else {
                        btn.removeClass('btn-success').addClass('btn-danger');
                        btn.html('<i class="fas fa-times"></i> Inativo');
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
