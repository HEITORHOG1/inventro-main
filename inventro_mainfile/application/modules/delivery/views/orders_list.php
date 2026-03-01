<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-bag"></i> Pedidos Online
            <small>Gerenciar pedidos do cardápio digital</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li class="active">Pedidos Online</li>
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

        <!-- Cards de Status -->
        <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo $status_counts['pendente']; ?></h3>
                        <p>Pendentes</p>
                    </div>
                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                    <a href="?status=pendente" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $status_counts['confirmado']; ?></h3>
                        <p>Confirmados</p>
                    </div>
                    <div class="icon"><i class="fa fa-check"></i></div>
                    <a href="?status=confirmado" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?php echo $status_counts['preparando']; ?></h3>
                        <p>Preparando</p>
                    </div>
                    <div class="icon"><i class="fa fa-cutlery"></i></div>
                    <a href="?status=preparando" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box" style="background:#e67e22;color:#fff;">
                    <div class="inner">
                        <h3><?php echo $status_counts['pronto_coleta'] ?? 0; ?></h3>
                        <p>Pronto Coleta</p>
                    </div>
                    <div class="icon"><i class="fa fa-archive"></i></div>
                    <a href="?status=pronto_coleta" class="small-box-footer" style="color:rgba(255,255,255,0.8);">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3><?php echo $status_counts['saiu_entrega']; ?></h3>
                        <p>Saiu Entrega</p>
                    </div>
                    <div class="icon"><i class="fa fa-motorcycle"></i></div>
                    <a href="?status=saiu_entrega" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $status_counts['entregue']; ?></h3>
                        <p>Entregues</p>
                    </div>
                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                    <a href="?status=entregue" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo $status_counts['cancelado']; ?></h3>
                        <p>Cancelados</p>
                    </div>
                    <div class="icon"><i class="fa fa-times-circle"></i></div>
                    <a href="?status=cancelado" class="small-box-footer">
                        Ver <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Lista de Pedidos</h3>
                <div class="box-tools">
                    <a href="<?php echo base_url('delivery/orders'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if (empty($orders)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fa fa-inbox fa-3x"></i>
                        <h4>Nenhum pedido encontrado</h4>
                        <p>Os pedidos aparecerão aqui quando os clientes fizerem pelo cardápio.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="100">Pedido</th>
                                    <th>Cliente</th>
                                    <th>Bairro</th>
                                    <th width="100">Total</th>
                                    <th width="100">Pagamento</th>
                                    <th width="120">Status</th>
                                    <th width="130">Data/Hora</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    $status_classes = [
                                        'pendente' => 'warning',
                                        'confirmado' => 'info',
                                        'preparando' => 'purple',
                                        'saiu_entrega' => 'primary',
                                        'entregue' => 'success',
                                        'cancelado' => 'danger'
                                    ];
                                    $status_class = $status_classes[$order->status] ?? 'default';
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?php echo $order->order_number; ?></strong>
                                            <?php if ($order->tipo_checkout == 'whatsapp'): ?>
                                                <br><small class="text-muted"><i class="fa fa-whatsapp"></i> WhatsApp</small>
                                            <?php else: ?>
                                                <br><small class="text-muted"><i class="fa fa-globe"></i> Site</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order->cliente_nome); ?></strong>
                                            <br><small class="text-muted">
                                                <i class="fa fa-phone"></i> <?php echo $order->cliente_telefone; ?>
                                            </small>
                                        </td>
                                        <td><?php echo htmlspecialchars($order->zona_nome ?? 'N/A'); ?></td>
                                        <td>
                                            <strong class="text-success">
                                                R$ <?php echo number_format($order->total, 2, ',', '.'); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php 
                                            $pagamento_icons = [
                                                'dinheiro' => 'fa-money',
                                                'cartao' => 'fa-credit-card',
                                                'pix' => 'fa-qrcode'
                                            ];
                                            $icon = $pagamento_icons[$order->forma_pagamento] ?? 'fa-question';
                                            ?>
                                            <i class="fa <?php echo $icon; ?>"></i>
                                            <?php echo ucfirst($order->forma_pagamento); ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?php echo $status_class; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $order->status)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <?php echo date('d/m/Y', strtotime($order->created_at)); ?>
                                                <br>
                                                <i class="fa fa-clock-o"></i> <?php echo date('H:i', strtotime($order->created_at)); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('delivery/orders/view/' . $order->id); ?>" 
                                               class="btn btn-info btn-xs" title="Ver Detalhes">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?php echo base_url('delivery/orders/print_order/' . (int)$order->id); ?>?auto=1"
                                               class="btn btn-default btn-xs" title="Imprimir Cupom" target="_blank">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<style>
.bg-purple {
    background-color: #9b59b6 !important;
}
.bg-purple a {
    color: rgba(255,255,255,0.8);
}
.label-purple {
    background-color: #9b59b6;
}
</style>

<script>
// Auto-refresh a cada 30 segundos
setTimeout(function() {
    location.reload();
}, 30000);
</script>
