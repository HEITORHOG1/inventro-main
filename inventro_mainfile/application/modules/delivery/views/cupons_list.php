<div class="card card-primary card-outline">
    <div class="card-header">
        <h4>
            <i class="fas fa-tags"></i> <?php echo makeString(['cupons_desconto']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('delivery/cupons/form'); ?>" class="btn btn-primary btn-md">
                    <i class="fas fa-plus"></i>
                    <?php echo makeString(['novo_cupom']); ?>
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

        <?php if (empty($cupons)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <?php echo makeString(['nenhum_cupom_cadastrado']); ?>
                <a href="<?php echo base_url('delivery/cupons/form'); ?>"><?php echo makeString(['clique_para_adicionar']); ?></a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="tblCupons" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo makeString(['codigo']); ?></th>
                            <th><?php echo makeString(['type']); ?></th>
                            <th class="text-right"><?php echo makeString(['amount']); ?></th>
                            <th class="text-right"><?php echo makeString(['valor_minimo_pedido']); ?></th>
                            <th><?php echo makeString(['uso']); ?></th>
                            <th><?php echo makeString(['validade']); ?></th>
                            <th><?php echo makeString(['status']); ?></th>
                            <th><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cupons as $index => $cupom): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><code><?php echo html_escape($cupom->codigo); ?></code></strong>
                                </td>
                                <td>
                                    <?php if ($cupom->tipo === 'percentual'): ?>
                                        <span class="badge badge-primary"><?php echo makeString(['percentual']); ?></span>
                                    <?php elseif ($cupom->tipo === 'valor_fixo'): ?>
                                        <span class="badge badge-success"><?php echo makeString(['valor_fixo']); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?php echo makeString(['frete_gratis']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($cupom->tipo === 'percentual'): ?>
                                        <?php echo html_escape(number_format($cupom->valor, 2, ',', '.')); ?>%
                                    <?php elseif ($cupom->tipo === 'valor_fixo'): ?>
                                        R$ <?php echo html_escape(number_format($cupom->valor, 2, ',', '.')); ?>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    R$ <?php echo html_escape(number_format($cupom->valor_minimo_pedido, 2, ',', '.')); ?>
                                </td>
                                <td>
                                    <?php
                                    $uso_atual  = (int) $cupom->uso_atual;
                                    $uso_maximo = $cupom->uso_maximo;
                                    if ($uso_maximo !== null && $uso_maximo !== '') {
                                        echo html_escape($uso_atual) . ' / ' . html_escape($uso_maximo);
                                        if ($uso_atual >= (int) $uso_maximo) {
                                            echo ' <span class="badge badge-warning">' . makeString(['esgotado']) . '</span>';
                                        }
                                    } else {
                                        echo html_escape($uso_atual) . ' / <span class="text-muted">' . makeString(['ilimitado']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($cupom->validade_inicio) && !empty($cupom->validade_fim)) {
                                        echo html_escape(date('d/m/Y', strtotime($cupom->validade_inicio)))
                                            . ' - '
                                            . html_escape(date('d/m/Y', strtotime($cupom->validade_fim)));

                                        if (strtotime($cupom->validade_fim) < time()) {
                                            echo ' <span class="badge badge-danger">' . makeString(['expirado']) . '</span>';
                                        }
                                    } elseif (!empty($cupom->validade_inicio)) {
                                        echo makeString(['a_partir_de']) . ' ' . html_escape(date('d/m/Y', strtotime($cupom->validade_inicio)));
                                    } elseif (!empty($cupom->validade_fim)) {
                                        echo makeString(['ate']) . ' ' . html_escape(date('d/m/Y', strtotime($cupom->validade_fim)));
                                    } else {
                                        echo '<span class="text-muted">' . makeString(['sem_validade']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-xs btn-<?php echo $cupom->ativo ? 'success' : 'danger'; ?> toggle-status"
                                            data-id="<?php echo (int) $cupom->id; ?>"
                                            title="<?php echo makeString(['clique_para_alterar']); ?>">
                                        <i class="fas fa-<?php echo $cupom->ativo ? 'check' : 'times'; ?>"></i>
                                        <?php echo $cupom->ativo ? makeString(['ativo']) : makeString(['inativo']); ?>
                                    </button>
                                </td>
                                <td>
                                    <a href="<?php echo base_url('delivery/cupons/form/' . (int) $cupom->id); ?>"
                                       class="btn btn-primary btn-xs" title="<?php echo makeString(['edit']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs delete-cupom"
                                            data-id="<?php echo (int) $cupom->id; ?>"
                                            data-codigo="<?php echo html_escape($cupom->codigo); ?>"
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
</div>

<!-- Modal de Confirmacao de Exclusao -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> <?php echo makeString(['confirm_delete']); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><?php echo makeString(['confirm_delete_cupom']); ?> <strong id="cupomCodigoToDelete"></strong>?</p>
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

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">

<script>
$(document).ready(function() {
    var base_url  = $('#base_url').val();
    var csrfName  = $('#csrf_name').val();
    var csrfHash  = $('#csrf_hash').val();

    // DataTable
    $('#tblCupons').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        }
    });

    // Toggle Status via AJAX
    $(document).on('click', '.toggle-status', function() {
        var btn = $(this);
        var id  = btn.data('id');
        var postData = {};
        postData[csrfName] = csrfHash;

        $.ajax({
            url: base_url + 'delivery/cupons/toggle/' + id,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Atualizar CSRF token
                    if (response.csrf_token) {
                        csrfHash = response.csrf_token;
                        $('#csrf_hash').val(csrfHash);
                        $('input[name="' + csrfName + '"]').val(csrfHash);
                    }

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
            },
            error: function() {
                alert('Erro ao atualizar status.');
            }
        });
    });

    // Delete Cupom
    $(document).on('click', '.delete-cupom', function() {
        var id     = $(this).data('id');
        var codigo = $(this).data('codigo');

        $('#cupomCodigoToDelete').text(codigo);
        $('#confirmDeleteBtn').attr('href', base_url + 'delivery/cupons/delete/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
