<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-bag"></i> Pedido <?php echo $order->order_number; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li><a href="<?php echo base_url('delivery/orders'); ?>">Pedidos</a></li>
            <li class="active"><?php echo $order->order_number; ?></li>
        </ol>
    </section>

    <section class="content">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check"></i> <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Coluna Principal -->
            <div class="col-md-8">
                <!-- Status do Pedido -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-flag"></i> Status do Pedido</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo base_url('delivery/orders/update_status/' . $order->id); ?>" method="POST" class="form-inline">
                            <div class="form-group">
                                <select name="status" class="form-control input-lg">
                                    <option value="pendente" <?php echo $order->status == 'pendente' ? 'selected' : ''; ?>>🟡 Pendente</option>
                                    <option value="confirmado" <?php echo $order->status == 'confirmado' ? 'selected' : ''; ?>>🔵 Confirmado</option>
                                    <option value="preparando" <?php echo $order->status == 'preparando' ? 'selected' : ''; ?>>🟣 Preparando</option>
                                    <option value="saiu_entrega" <?php echo $order->status == 'saiu_entrega' ? 'selected' : ''; ?>>🚀 Saiu para Entrega</option>
                                    <option value="entregue" <?php echo $order->status == 'entregue' ? 'selected' : ''; ?>>✅ Entregue</option>
                                    <option value="cancelado" <?php echo $order->status == 'cancelado' ? 'selected' : ''; ?>>❌ Cancelado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Atualizar Status
                            </button>
                        </form>
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
                                    <th width="120" class="text-right">Unitário</th>
                                    <th width="120" class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item->product_name); ?></td>
                                        <td class="text-center"><strong><?php echo $item->quantity; ?>x</strong></td>
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
                                            <span class="text-success">GRÁTIS</span>
                                        <?php else: ?>
                                            R$ <?php echo number_format($order->taxa_entrega, 2, ',', '.'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($order->desconto > 0): ?>
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

                <!-- Observações -->
                <?php if (!empty($order->observacao)): ?>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-comment"></i> Observações do Cliente</h3>
                    </div>
                    <div class="box-body">
                        <p class="lead"><?php echo nl2br(htmlspecialchars($order->observacao)); ?></p>
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
                            <dd><?php echo htmlspecialchars($order->cliente_nome); ?></dd>
                            
                            <dt><i class="fa fa-phone"></i> Telefone</dt>
                            <dd>
                                <a href="tel:<?php echo $order->cliente_telefone; ?>">
                                    <?php echo $order->cliente_telefone; ?>
                                </a>
                                <a href="https://wa.me/55<?php echo preg_replace('/[^0-9]/', '', $order->cliente_telefone); ?>" 
                                   target="_blank" class="btn btn-success btn-xs">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </a>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Endereço de Entrega -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-map-marker"></i> Entrega</h3>
                    </div>
                    <div class="box-body">
                        <p><strong><?php echo htmlspecialchars($order->cliente_endereco); ?></strong></p>
                        <?php if ($order->cliente_complemento): ?>
                            <p class="text-muted"><?php echo htmlspecialchars($order->cliente_complemento); ?></p>
                        <?php endif; ?>
                        <p>
                            <span class="label label-info">
                                <i class="fa fa-map"></i> <?php echo htmlspecialchars($order->zona_nome ?? 'N/A'); ?>
                            </span>
                        </p>
                        <a href="https://www.google.com/maps/search/<?php echo urlencode($order->cliente_endereco); ?>" 
                           target="_blank" class="btn btn-default btn-block">
                            <i class="fa fa-map"></i> Ver no Mapa
                        </a>
                    </div>
                </div>

                <!-- Pagamento -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> Pagamento</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $pagamento_icons = [
                            'dinheiro' => ['icon' => 'fa-money', 'label' => 'Dinheiro', 'class' => 'success'],
                            'cartao' => ['icon' => 'fa-credit-card', 'label' => 'Cartão', 'class' => 'primary'],
                            'pix' => ['icon' => 'fa-qrcode', 'label' => 'Pix', 'class' => 'info']
                        ];
                        $pag = $pagamento_icons[$order->forma_pagamento] ?? ['icon' => 'fa-question', 'label' => 'Outro', 'class' => 'default'];
                        ?>
                        <p>
                            <span class="label label-<?php echo $pag['class']; ?>" style="font-size: 1.2em;">
                                <i class="fa <?php echo $pag['icon']; ?>"></i> <?php echo $pag['label']; ?>
                            </span>
                        </p>
                        <?php if ($order->forma_pagamento == 'dinheiro' && $order->troco_para > 0): ?>
                            <div class="callout callout-warning">
                                <h4><i class="fa fa-money"></i> Troco</h4>
                                <p>Cliente pagará com: <strong>R$ <?php echo number_format($order->troco_para, 2, ',', '.'); ?></strong></p>
                                <p>Levar troco de: <strong class="text-danger">R$ <?php echo number_format($order->troco_para - $order->total, 2, ',', '.'); ?></strong></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informações do Pedido -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Informações</h3>
                    </div>
                    <div class="box-body">
                        <dl>
                            <dt>Pedido</dt>
                            <dd><strong class="text-primary"><?php echo $order->order_number; ?></strong></dd>
                            
                            <dt>Data/Hora</dt>
                            <dd><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></dd>
                            
                            <dt>Origem</dt>
                            <dd>
                                <?php if ($order->tipo_checkout == 'whatsapp'): ?>
                                    <i class="fa fa-whatsapp text-success"></i> WhatsApp
                                <?php else: ?>
                                    <i class="fa fa-globe text-primary"></i> Site
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('delivery/orders/print_order/' . $order->id); ?>" 
                           target="_blank" class="btn btn-default btn-block">
                            <i class="fa fa-print"></i> Imprimir Pedido
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
            </div>
        </div>
    </section>
</div>
