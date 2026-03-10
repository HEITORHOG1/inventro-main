<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-bag"></i> Pedido <?php echo html_escape($order->order_number); ?>
            <?php
            $status_labels = [
                'pendente' => ['label' => 'Pendente', 'class' => 'warning'],
                'confirmado' => ['label' => 'Confirmado', 'class' => 'info'],
                'preparando' => ['label' => 'Preparando', 'class' => 'purple'],
                'pronto_coleta' => ['label' => 'Pronto p/ Coleta', 'class' => 'warning'],
                'saiu_entrega' => ['label' => 'Saiu Entrega', 'class' => 'primary'],
                'entregue' => ['label' => 'Entregue', 'class' => 'success'],
                'cancelado' => ['label' => 'Cancelado', 'class' => 'danger']
            ];
            $st = $status_labels[$order->status] ?? ['label' => $order->status, 'class' => 'default'];
            ?>
            <span class="label label-<?php echo $st['class']; ?>" style="font-size:0.6em;vertical-align:middle;">
                <?php echo $st['label']; ?>
            </span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li><a href="<?php echo base_url('delivery/orders'); ?>">Pedidos</a></li>
            <li class="active">#<?php echo html_escape($order->order_number); ?></li>
        </ol>
    </section>

    <section class="content">
        <?php if ($this->session->flashdata('message')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check"></i> <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('exception')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-times"></i> <?php echo $this->session->flashdata('exception'); ?>
            </div>
        <?php endif; ?>

        <!-- WhatsApp Link (auto-abrir se disponível) -->
        <?php if ($this->session->flashdata('whatsapp_link')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-whatsapp"></i> <strong>Notificar cliente:</strong>
                <a href="<?php echo $this->session->flashdata('whatsapp_link'); ?>" target="_blank" class="btn btn-success btn-sm">
                    <i class="fa fa-whatsapp"></i> Enviar WhatsApp
                </a>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Coluna Principal -->
            <div class="col-md-8">
                <!-- Status + Timeline -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-flag"></i> Status do Pedido</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo base_url('delivery/orders/update_status/' . (int)$order->id); ?>" method="POST" class="form-inline" id="formStatus">
                            <input type="hidden" name="csrf_test_name" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group">
                                <select name="status" class="form-control input-lg" id="statusSelect">
                                    <option value="pendente" <?php echo $order->status == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="confirmado" <?php echo $order->status == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                    <option value="preparando" <?php echo $order->status == 'preparando' ? 'selected' : ''; ?>>Preparando</option>
                                    <option value="pronto_coleta" <?php echo $order->status == 'pronto_coleta' ? 'selected' : ''; ?>>Pronto p/ Coleta</option>
                                    <option value="saiu_entrega" <?php echo $order->status == 'saiu_entrega' ? 'selected' : ''; ?>>Saiu para Entrega</option>
                                    <option value="entregue" <?php echo $order->status == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                    <option value="cancelado" <?php echo $order->status == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Atualizar Status
                            </button>
                            <label style="margin-left:15px;font-weight:normal;cursor:pointer;">
                                <input type="checkbox" id="autoPrint" checked> <i class="fa fa-print"></i> Imprimir cupom
                            </label>
                        </form>

                        <!-- Timeline de Status -->
                        <div style="margin-top:20px;">
                            <h4><i class="fa fa-clock-o"></i> Timeline</h4>
                            <ul class="timeline" style="list-style:none;padding-left:0;">
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle text-warning"></i>
                                    <strong>Recebido:</strong> <?php echo date('d/m H:i', strtotime($order->created_at)); ?>
                                </li>
                                <?php if (!empty($order->hora_confirmado)): ?>
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle text-info"></i>
                                    <strong>Confirmado:</strong> <?php echo date('d/m H:i', strtotime($order->hora_confirmado)); ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($order->hora_preparando)): ?>
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle" style="color:#9b59b6;"></i>
                                    <strong>Preparando:</strong> <?php echo date('d/m H:i', strtotime($order->hora_preparando)); ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($order->hora_pronto_coleta)): ?>
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle" style="color:#e67e22;"></i>
                                    <strong>Pronto p/ Coleta:</strong> <?php echo date('d/m H:i', strtotime($order->hora_pronto_coleta)); ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($order->hora_saiu_entrega)): ?>
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle text-primary"></i>
                                    <strong>Saiu Entrega:</strong> <?php echo date('d/m H:i', strtotime($order->hora_saiu_entrega)); ?>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($order->hora_entregue)): ?>
                                <li style="padding:5px 0;">
                                    <i class="fa fa-circle text-success"></i>
                                    <strong>Entregue:</strong> <?php echo date('d/m H:i', strtotime($order->hora_entregue)); ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Itens do Pedido</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th width="80" class="text-center">Qtd</th>
                                    <th width="120" class="text-right">Unitario</th>
                                    <th width="120" class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->items as $item): ?>
                                    <tr>
                                        <td><?php echo html_escape($item->product_name); ?></td>
                                        <td class="text-center"><strong><?php echo (int)$item->quantity; ?>x</strong></td>
                                        <td class="text-right">R$ <?php echo number_format($item->unit_price, 2, ',', '.'); ?></td>
                                        <td class="text-right"><strong>R$ <?php echo number_format($item->total_price, 2, ',', '.'); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">Subtotal:</td>
                                    <td class="text-right">R$ <?php echo number_format($order->subtotal, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Taxa de Entrega:</td>
                                    <td class="text-right">
                                        <?php if ($order->taxa_entrega == 0): ?>
                                            <span class="text-success">GRATIS</span>
                                        <?php else: ?>
                                            R$ <?php echo number_format($order->taxa_entrega, 2, ',', '.'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (isset($order->desconto_cupom) && $order->desconto_cupom > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-right text-danger">
                                        Desconto (cupom <?php echo html_escape($order->cupom_codigo); ?>):
                                    </td>
                                    <td class="text-right text-danger">- R$ <?php echo number_format($order->desconto_cupom, 2, ',', '.'); ?></td>
                                </tr>
                                <?php elseif (isset($order->desconto) && $order->desconto > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-right text-danger">Desconto:</td>
                                    <td class="text-right text-danger">- R$ <?php echo number_format($order->desconto, 2, ',', '.'); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="bg-success">
                                    <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                                    <td class="text-right"><strong style="font-size: 1.3em;">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Observacoes -->
                <?php if (!empty($order->observacao)): ?>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-comment"></i> Observacoes do Cliente</h3>
                    </div>
                    <div class="box-body">
                        <p class="lead"><?php echo nl2br(html_escape($order->observacao)); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Avaliacao do Cliente -->
                <?php if (!empty($order->avaliacao_nota)): ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-star"></i> Avaliacao do Cliente</h3>
                    </div>
                    <div class="box-body">
                        <div style="font-size:1.5em;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa fa-star<?php echo $i <= $order->avaliacao_nota ? '' : '-o'; ?>" style="color:<?php echo $i <= $order->avaliacao_nota ? '#f1c40f' : '#ccc'; ?>;"></i>
                            <?php endfor; ?>
                            <span style="font-size:0.7em;margin-left:10px;">(<?php echo (int)$order->avaliacao_nota; ?>/5)</span>
                        </div>
                        <?php if (!empty($order->avaliacao_comentario)): ?>
                            <blockquote style="margin-top:10px;font-style:italic;">
                                <?php echo nl2br(html_escape($order->avaliacao_comentario)); ?>
                            </blockquote>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Coluna Lateral -->
            <div class="col-md-4">
                <!-- Dados do Cliente -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> Dados do Cliente</h3>
                    </div>
                    <div class="box-body">
                        <dl>
                            <dt><i class="fa fa-user"></i> Nome</dt>
                            <dd><?php echo html_escape($order->cliente_nome); ?></dd>

                            <dt><i class="fa fa-phone"></i> Telefone</dt>
                            <dd>
                                <a href="tel:<?php echo html_escape($order->cliente_telefone); ?>">
                                    <?php echo html_escape($order->cliente_telefone); ?>
                                </a>
                                <a href="https://wa.me/55<?php echo preg_replace('/[^0-9]/', '', $order->cliente_telefone); ?>"
                                   target="_blank" class="btn btn-success btn-xs">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </a>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Endereco de Entrega -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-map-marker"></i>
                            <?php echo (isset($order->tipo_entrega) && $order->tipo_entrega === 'retirada') ? 'Retirada na Loja' : 'Entrega'; ?>
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if (isset($order->tipo_entrega) && $order->tipo_entrega === 'retirada'): ?>
                            <p class="text-info"><i class="fa fa-home"></i> <strong>Cliente retira no local</strong></p>
                        <?php else: ?>
                            <p><strong><?php echo html_escape($order->cliente_endereco); ?></strong></p>
                            <?php if (!empty($order->cliente_complemento)): ?>
                                <p class="text-muted"><?php echo html_escape($order->cliente_complemento); ?></p>
                            <?php endif; ?>
                            <p>
                                <span class="label label-info">
                                    <i class="fa fa-map"></i> <?php echo html_escape($order->zona_nome ?? 'N/A'); ?>
                                </span>
                            </p>
                            <a href="https://www.google.com/maps/search/<?php echo urlencode($order->cliente_endereco); ?>"
                               target="_blank" class="btn btn-default btn-block">
                                <i class="fa fa-map"></i> Ver no Mapa
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Entregador -->
                <?php if (!isset($order->tipo_entrega) || $order->tipo_entrega !== 'retirada'): ?>
                <div class="box box-purple">
                    <div class="box-header with-border" style="background:#9b59b6;color:#fff;">
                        <h3 class="box-title"><i class="fa fa-motorcycle"></i> Entregador</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($order->entregador_nome)): ?>
                            <p><strong><?php echo html_escape($order->entregador_nome); ?></strong></p>
                        <?php else: ?>
                            <p class="text-muted">Nenhum entregador atribuido</p>
                        <?php endif; ?>

                        <?php if (!empty($entregadores)): ?>
                        <div style="margin-top:10px;">
                            <select id="entregadorSelect" class="form-control">
                                <option value="">-- Selecionar Entregador --</option>
                                <?php foreach ($entregadores as $e): ?>
                                    <option value="<?php echo (int)$e->id; ?>"
                                        <?php echo (isset($order->entregador_id) && $order->entregador_id == $e->id) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($e->nome); ?> (<?php echo html_escape($e->veiculo); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary btn-block btn-sm" style="margin-top:5px;" onclick="atribuirEntregador()">
                                <i class="fa fa-check"></i> Atribuir
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Pagamento -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> Pagamento</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $pagamento_icons = [
                            'dinheiro' => ['icon' => 'fa-money', 'label' => 'Dinheiro', 'class' => 'success'],
                            'cartao' => ['icon' => 'fa-credit-card', 'label' => 'Cartao', 'class' => 'primary'],
                            'pix' => ['icon' => 'fa-qrcode', 'label' => 'Pix', 'class' => 'info']
                        ];
                        $pag = $pagamento_icons[$order->forma_pagamento] ?? ['icon' => 'fa-question', 'label' => 'Outro', 'class' => 'default'];
                        ?>
                        <p>
                            <span class="label label-<?php echo $pag['class']; ?>" style="font-size: 1.2em;">
                                <i class="fa <?php echo $pag['icon']; ?>"></i> <?php echo $pag['label']; ?>
                            </span>
                            <?php if (isset($order->pagamento_confirmado) && $order->pagamento_confirmado): ?>
                                <span class="label label-success"><i class="fa fa-check"></i> Pago</span>
                            <?php else: ?>
                                <span class="label label-warning"><i class="fa fa-clock-o"></i> Pendente</span>
                            <?php endif; ?>
                        </p>
                        <?php if ($order->forma_pagamento == 'dinheiro' && isset($order->troco_para) && $order->troco_para > 0): ?>
                            <div class="callout callout-warning">
                                <h4><i class="fa fa-money"></i> Troco</h4>
                                <p>Cliente pagara com: <strong>R$ <?php echo number_format($order->troco_para, 2, ',', '.'); ?></strong></p>
                                <p>Levar troco de: <strong class="text-danger">R$ <?php echo number_format($order->troco_para - $order->total, 2, ',', '.'); ?></strong></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!(isset($order->pagamento_confirmado) && $order->pagamento_confirmado)): ?>
                        <button class="btn btn-success btn-block btn-sm" onclick="confirmarPagamento()">
                            <i class="fa fa-check"></i> Confirmar Pagamento
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informacoes -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Informacoes</h3>
                    </div>
                    <div class="box-body">
                        <dl>
                            <dt>Pedido</dt>
                            <dd><strong class="text-primary">#<?php echo html_escape($order->order_number); ?></strong></dd>

                            <dt>Data/Hora</dt>
                            <dd><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></dd>

                            <dt>Tipo</dt>
                            <dd>
                                <?php if (isset($order->tipo_entrega) && $order->tipo_entrega === 'retirada'): ?>
                                    <i class="fa fa-home text-info"></i> Retirada na Loja
                                <?php else: ?>
                                    <i class="fa fa-motorcycle text-primary"></i> Entrega
                                <?php endif; ?>
                            </dd>

                            <dt>Origem</dt>
                            <dd>
                                <?php if (isset($order->tipo_checkout) && $order->tipo_checkout == 'whatsapp'): ?>
                                    <i class="fa fa-whatsapp text-success"></i> WhatsApp
                                <?php else: ?>
                                    <i class="fa fa-globe text-primary"></i> Site
                                <?php endif; ?>
                            </dd>

                            <?php if (!empty($order->cpf_nota)): ?>
                            <dt>CPF Nota</dt>
                            <dd><?php echo html_escape($order->cpf_nota); ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-success btn-block" onclick="enviarCupomWhatsApp()">
                            <i class="fa fa-whatsapp"></i> Enviar Cupom via WhatsApp
                        </button>
                        <a href="<?php echo base_url('delivery/orders/print_order/' . (int)$order->id); ?>?auto=1"
                           target="_blank" class="btn btn-default btn-block" style="margin-top:5px;">
                            <i class="fa fa-print"></i> Imprimir Cupom
                        </a>
                        <a href="<?php echo base_url('cardapio/acompanhar/' . html_escape($order->order_number)); ?>"
                           target="_blank" class="btn btn-info btn-block" style="margin-top:5px;">
                            <i class="fa fa-eye"></i> Link de Acompanhamento
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="<?php echo base_url('delivery/orders'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Voltar para Lista
                </a>
                <a href="<?php echo base_url('delivery/orders/kanban'); ?>" class="btn btn-info">
                    <i class="fa fa-columns"></i> Kanban
                </a>
            </div>
        </div>
    </section>
</div>

<style>
.bg-purple { background-color: #9b59b6 !important; }
.label-purple { background-color: #9b59b6; }
.box-purple > .box-header { background-color: #9b59b6; color: #fff; }
</style>

<script>
var baseUrl = '<?php echo base_url(); ?>';
var orderId = <?php echo (int)$order->id; ?>;
var csrfName = 'csrf_test_name';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Enviar cupom não-fiscal via WhatsApp
function enviarCupomWhatsApp() {
    $.ajax({
        url: baseUrl + 'delivery/orders/enviar_cupom/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(r) {
            if (r.success && r.whatsapp_link) {
                window.open(r.whatsapp_link, '_blank');
            } else {
                showToast(r.message || 'Erro ao gerar cupom', 'error');
            }
        },
        error: function() {
            // Fallback: abrir via redirect
            window.location.href = baseUrl + 'delivery/orders/enviar_cupom/' + orderId;
        }
    });
}

// Auto-print ao atualizar status
document.getElementById('formStatus').addEventListener('submit', function() {
    if (document.getElementById('autoPrint').checked) {
        var printUrl = baseUrl + 'delivery/orders/print_order/' + orderId + '?auto=1';
        window.open(printUrl, '_blank', 'width=350,height=600');
    }
});

function atribuirEntregador() {
    var entregadorId = document.getElementById('entregadorSelect').value;
    if (!entregadorId) { showToast('Selecione um entregador', 'warning'); return; }

    $.ajax({
        url: baseUrl + 'delivery/orders/atribuir_entregador',
        type: 'POST',
        data: {order_id: orderId, entregador_id: entregadorId, [csrfName]: csrfHash},
        dataType: 'json',
        success: function(r) {
            if (r.csrf_token) csrfHash = r.csrf_token;
            if (r.success) {
                showToast(r.message, 'success');
                if (r.whatsapp_link) window.open(r.whatsapp_link, '_blank');
                location.reload();
            } else {
                showToast(r.message || 'Erro ao atribuir entregador', 'error');
            }
        }
    });
}

function confirmarPagamento() {
    if (!confirm('Confirmar pagamento deste pedido?')) return;

    $.ajax({
        url: baseUrl + 'delivery/orders/confirmar_pagamento',
        type: 'POST',
        data: {order_id: orderId, [csrfName]: csrfHash},
        dataType: 'json',
        success: function(r) {
            if (r.csrf_token) csrfHash = r.csrf_token;
            if (r.success) {
                showToast(r.message, 'success');
                location.reload();
            } else {
                showToast(r.message || 'Erro ao confirmar pagamento', 'error');
            }
        }
    });
}
</script>
