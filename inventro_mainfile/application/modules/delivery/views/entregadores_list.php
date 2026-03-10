<div class="card card-primary card-outline">
    <div class="card-header">
        <h4>
            <i class="fas fa-motorcycle"></i> <?php echo makeString(['entregadores']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('delivery/entregadores/form'); ?>" class="btn btn-primary btn-md">
                    <i class="ti-plus" aria-hidden="true"></i>
                    <?php echo makeString(['novo_entregador']); ?>
                </a>
            </small>
        </h4>
    </div>

    <div class="card-body">
        <?php
        $message   = $this->session->flashdata('message');
        $exception = $this->session->flashdata('exception');
        if ($message) echo '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . html_escape($message) . '</div>';
        if ($exception) echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $exception . '</div>';
        ?>

        <?php if (empty($entregadores)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <?php echo makeString(['nenhum_entregador']); ?>
                <a href="<?php echo base_url('delivery/entregadores/form'); ?>"><?php echo makeString(['clique_para_adicionar']); ?></a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="tblEntregadores" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th><?php echo makeString(['nome']); ?></th>
                            <th><?php echo makeString(['telefone']); ?></th>
                            <th><?php echo makeString(['veiculo']); ?></th>
                            <th width="130"><?php echo makeString(['status']); ?></th>
                            <th width="180"><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $veiculo_icons = array(
                            'moto'      => 'fa-motorcycle',
                            'bicicleta' => 'fa-bicycle',
                            'carro'     => 'fa-car',
                            'a_pe'      => 'fa-walking',
                        );
                        $veiculo_labels = array(
                            'moto'      => 'Moto',
                            'bicicleta' => 'Bicicleta',
                            'carro'     => 'Carro',
                            'a_pe'      => 'A pe',
                        );
                        $status_badges = array(
                            'disponivel'   => 'badge-success',
                            'em_entrega'   => 'badge-warning',
                            'indisponivel' => 'badge-danger',
                        );
                        $status_labels = array(
                            'disponivel'   => 'Disponivel',
                            'em_entrega'   => 'Em Entrega',
                            'indisponivel' => 'Indisponivel',
                        );
                        ?>
                        <?php foreach ($entregadores as $index => $ent): ?>
                            <tr<?php echo !$ent->ativo ? ' class="text-muted"' : ''; ?>>
                                <td><?php echo (int) ($index + 1); ?></td>
                                <td>
                                    <strong><?php echo html_escape($ent->nome); ?></strong>
                                    <?php if (!$ent->ativo): ?>
                                        <span class="badge badge-secondary"><?php echo makeString(['inativo']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo html_escape($ent->telefone); ?></td>
                                <td>
                                    <?php
                                    $icon  = isset($veiculo_icons[$ent->veiculo]) ? $veiculo_icons[$ent->veiculo] : 'fa-question';
                                    $label = isset($veiculo_labels[$ent->veiculo]) ? $veiculo_labels[$ent->veiculo] : html_escape($ent->veiculo);
                                    ?>
                                    <i class="fas <?php echo $icon; ?>"></i> <?php echo html_escape($label); ?>
                                </td>
                                <td>
                                    <?php
                                    $badge = isset($status_badges[$ent->status]) ? $status_badges[$ent->status] : 'badge-secondary';
                                    $sLabel = isset($status_labels[$ent->status]) ? $status_labels[$ent->status] : html_escape($ent->status);
                                    ?>
                                    <span class="badge <?php echo $badge; ?> p-1">
                                        <?php echo html_escape($sLabel); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($ent->ativo && $ent->status !== 'em_entrega'): ?>
                                        <button type="button"
                                                class="btn btn-xs btn-<?php echo ($ent->status === 'disponivel') ? 'success' : 'secondary'; ?> btn-toggle-status"
                                                data-id="<?php echo (int) $ent->id; ?>"
                                                title="<?php echo makeString(['toggle_status']); ?>">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    <?php endif; ?>

                                    <a href="<?php echo base_url('delivery/entregadores/form/' . (int) $ent->id); ?>"
                                       class="btn btn-xs btn-primary"
                                       title="<?php echo makeString(['edit']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <?php if ($ent->ativo): ?>
                                        <button type="button"
                                                class="btn btn-xs btn-danger btn-delete-entregador"
                                                data-id="<?php echo (int) $ent->id; ?>"
                                                data-nome="<?php echo html_escape($ent->nome); ?>"
                                                title="<?php echo makeString(['delete']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmacao de Exclusao -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo makeString(['confirm_delete']); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo makeString(['confirm_delete_message']); ?> <strong id="entregadorNameToDelete"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo makeString(['cancel']); ?>
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> <?php echo makeString(['delete']); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // DataTable
    $('#tblEntregadores').DataTable({
        "paging": true,
        "info": true,
        "searching": true,
        "order": [[1, 'asc']],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        }
    });

    // Toggle Status via AJAX
    $(document).on('click', '.btn-toggle-status', function() {
        var btn = $(this);
        var id = btn.data('id');
        var csrfToken = $('#csrf_token').val();

        $.ajax({
            url: $('#mainsiteurl').val() + 'delivery/entregadores/toggle_status/' + id,
            type: 'POST',
            dataType: 'json',
            data: {
                'csrf_test_name': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    // Update CSRF token
                    if (response.csrf_token) {
                        $('#csrf_token').val(response.csrf_token);
                    }
                    // Reload to reflect changes
                    location.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                showToast('Erro ao atualizar status.', 'error');
            }
        });
    });

    // Delete Entregador
    $(document).on('click', '.btn-delete-entregador', function() {
        var id   = $(this).data('id');
        var nome = $(this).data('nome');

        $('#entregadorNameToDelete').text(nome);
        $('#confirmDeleteBtn').attr('href', $('#mainsiteurl').val() + 'delivery/entregadores/delete/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
