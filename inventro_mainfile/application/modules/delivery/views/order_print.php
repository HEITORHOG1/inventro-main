<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupom - Pedido #<?php echo html_escape($order->order_number); ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            color: #000;
            background: #fff;
        }

        .cupom { width: 100%; }

        /* Cabeçalho */
        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
            margin-bottom: 8px;
        }
        .header .loja-nome {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .header .loja-info { font-size: 10px; }
        .header .loja-cnpj { font-size: 11px; margin-top: 4px; }

        /* Tipo do documento */
        .doc-tipo {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 0;
            border-bottom: 1px dashed #000;
        }

        /* Pedido */
        .pedido-info {
            text-align: center;
            padding: 8px 0;
            border-bottom: 1px dashed #000;
        }
        .pedido-numero {
            font-size: 20px;
            font-weight: bold;
        }
        .pedido-data { font-size: 10px; margin-top: 4px; }
        .pedido-badges { margin-top: 5px; }
        .badge-cupom {
            display: inline-block;
            padding: 1px 8px;
            border: 1px solid #000;
            font-size: 10px;
            text-transform: uppercase;
            margin: 0 2px;
        }

        /* Cliente */
        .cliente {
            padding: 8px 0;
            border-bottom: 1px dashed #000;
            font-size: 11px;
        }
        .cliente-label { font-weight: bold; }

        /* Itens */
        .itens {
            padding: 8px 0;
            border-bottom: 1px dashed #000;
        }
        .itens-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 11px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 4px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 11px;
        }
        .item-nome {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 40mm;
        }
        .item-qtd { width: 12mm; text-align: center; }
        .item-valor { width: 20mm; text-align: right; }
        .item-detalhe {
            font-size: 9px;
            color: #666;
            padding-left: 5px;
        }

        /* Totais */
        .totais {
            padding: 8px 0;
            border-bottom: 1px dashed #000;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 11px;
        }
        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 6px;
        }
        .total-row.desconto { color: #333; }

        /* Pagamento */
        .pagamento {
            padding: 8px 0;
            text-align: center;
            border-bottom: 1px dashed #000;
        }
        .pagamento-tipo {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .troco { font-size: 11px; margin-top: 4px; }

        /* Observação */
        .observacao {
            padding: 6px 0;
            font-size: 10px;
            font-style: italic;
            border-bottom: 1px dashed #000;
        }

        /* Rodapé */
        .footer {
            text-align: center;
            padding-top: 12px;
            font-size: 10px;
        }
        .footer .obrigado {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .footer .legal {
            font-size: 9px;
            margin-top: 8px;
            color: #666;
            border-top: 1px dashed #000;
            padding-top: 6px;
        }

        /* Botões de ação (não imprimem) */
        .actions {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }
        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-print { background: #25D366; color: #fff; }
        .btn-print:hover { background: #128C7E; }
        .btn-voltar { background: #6c757d; color: #fff; }
        .btn-voltar:hover { background: #545b62; }

        @media print {
            .actions { display: none !important; }
            body { width: 80mm; padding: 2mm; margin: 0; }
        }
        @media screen {
            body {
                margin: 20px auto;
                border: 1px solid #ccc;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>

<!-- Botões de ação -->
<div class="actions no-print">
    <button class="btn-action btn-print" onclick="window.print()">
        IMPRIMIR
    </button>
    <a class="btn-action btn-voltar" href="<?php echo base_url('delivery/orders/view/' . (int)$order->id); ?>">
        VOLTAR
    </a>
</div>

<div class="cupom">
    <!-- Cabeçalho da Loja -->
    <div class="header">
        <div class="loja-nome"><?php echo html_escape($loja->title ?? 'LOJA'); ?></div>
        <?php if (!empty($loja->address)): ?>
            <div class="loja-info"><?php echo html_escape($loja->address); ?></div>
        <?php endif; ?>
        <?php if (!empty($loja->phone)): ?>
            <div class="loja-info">Tel: <?php echo html_escape($loja->phone); ?></div>
        <?php endif; ?>
        <?php if (!empty($loja->cnpj)): ?>
            <div class="loja-cnpj">CNPJ: <?php echo html_escape($loja->cnpj); ?></div>
        <?php endif; ?>
    </div>

    <!-- Tipo do documento -->
    <div class="doc-tipo">CUPOM NAO-FISCAL</div>

    <!-- Informações do Pedido -->
    <div class="pedido-info">
        <div class="pedido-numero">PEDIDO #<?php echo html_escape($order->order_number); ?></div>
        <div class="pedido-data">
            <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
        </div>
        <div class="pedido-badges">
            <span class="badge-cupom">
                <?php echo ($order->tipo_entrega ?? 'entrega') === 'retirada' ? 'RETIRADA' : 'ENTREGA'; ?>
            </span>
            <?php
            $status_nomes = [
                'pendente' => 'PENDENTE',
                'confirmado' => 'CONFIRMADO',
                'preparando' => 'PREPARANDO',
                'pronto_coleta' => 'PRONTO COLETA',
                'saiu_entrega' => 'SAIU ENTREGA',
                'entregue' => 'ENTREGUE',
                'cancelado' => 'CANCELADO'
            ];
            ?>
            <span class="badge-cupom">
                <?php echo $status_nomes[$order->status] ?? strtoupper(html_escape($order->status)); ?>
            </span>
        </div>
    </div>

    <!-- Dados do Cliente -->
    <div class="cliente">
        <div><span class="cliente-label">Cliente:</span> <?php echo html_escape($order->cliente_nome); ?></div>
        <div><span class="cliente-label">Tel:</span> <?php echo html_escape($order->cliente_telefone); ?></div>
        <?php if (($order->tipo_entrega ?? 'entrega') !== 'retirada'): ?>
            <div>
                <span class="cliente-label">End:</span> <?php echo html_escape($order->cliente_endereco); ?>
                <?php if (!empty($order->cliente_complemento)): ?>
                    - <?php echo html_escape($order->cliente_complemento); ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($order->zona_nome)): ?>
                <div><span class="cliente-label">Zona:</span> <?php echo html_escape($order->zona_nome); ?></div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($order->cpf_nota)): ?>
            <div style="margin-top:3px;"><span class="cliente-label">CPF:</span> <?php echo html_escape($order->cpf_nota); ?></div>
        <?php endif; ?>
    </div>

    <!-- Itens -->
    <div class="itens">
        <div class="itens-header">
            <span style="flex:1;">ITEM</span>
            <span style="width:12mm;text-align:center;">QTD</span>
            <span style="width:20mm;text-align:right;">VALOR</span>
        </div>
        <?php if (!empty($order->items)): ?>
        <?php foreach ($order->items as $item): ?>
            <div class="item">
                <span class="item-nome"><?php echo html_escape($item->product_name); ?></span>
                <span class="item-qtd"><?php echo (int)$item->quantity; ?>x</span>
                <span class="item-valor"><?php echo number_format($item->total_price, 2, ',', '.'); ?></span>
            </div>
            <?php if ($item->quantity > 1): ?>
                <div class="item-detalhe">
                    (<?php echo number_format($item->unit_price, 2, ',', '.'); ?> cada)
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Totais -->
    <div class="totais">
        <div class="total-row">
            <span>Subtotal</span>
            <span>R$ <?php echo number_format($order->subtotal, 2, ',', '.'); ?></span>
        </div>
        <?php if (($order->tipo_entrega ?? 'entrega') !== 'retirada'): ?>
        <div class="total-row">
            <span>Taxa de Entrega</span>
            <span><?php echo $order->taxa_entrega == 0 ? 'GRATIS' : 'R$ ' . number_format($order->taxa_entrega, 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($order->desconto) && $order->desconto > 0): ?>
        <div class="total-row desconto">
            <span>Desconto</span>
            <span>- R$ <?php echo number_format($order->desconto, 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($order->desconto_cupom) && $order->desconto_cupom > 0): ?>
        <div class="total-row desconto">
            <span>Cupom <?php echo !empty($order->cupom_codigo) ? '(' . html_escape($order->cupom_codigo) . ')' : ''; ?></span>
            <span>- R$ <?php echo number_format($order->desconto_cupom, 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row grand">
            <span>TOTAL</span>
            <span>R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
        </div>
    </div>

    <!-- Pagamento -->
    <div class="pagamento">
        <div class="pagamento-tipo">
            <?php
            $pagamentos = [
                'dinheiro' => 'DINHEIRO',
                'cartao'   => 'CARTAO',
                'pix'      => 'PIX'
            ];
            echo $pagamentos[$order->forma_pagamento] ?? strtoupper(html_escape($order->forma_pagamento));
            ?>
        </div>
        <?php if ($order->forma_pagamento === 'dinheiro' && !empty($order->troco_para) && $order->troco_para > 0): ?>
            <div class="troco">
                Troco para: R$ <?php echo number_format($order->troco_para, 2, ',', '.'); ?>
                <br>
                <strong>Troco: R$ <?php echo number_format($order->troco_para - $order->total, 2, ',', '.'); ?></strong>
            </div>
        <?php endif; ?>
    </div>

    <!-- Observação -->
    <?php if (!empty($order->observacao)): ?>
    <div class="observacao">
        <strong>Obs:</strong> <?php echo html_escape($order->observacao); ?>
    </div>
    <?php endif; ?>

    <!-- Entregador (se atribuído) -->
    <?php if (!empty($order->entregador_nome)): ?>
    <div style="padding:6px 0;font-size:11px;border-bottom:1px dashed #000;">
        <strong>Entregador:</strong> <?php echo html_escape($order->entregador_nome); ?>
    </div>
    <?php endif; ?>

    <!-- Rodapé -->
    <div class="footer">
        <div class="obrigado">OBRIGADO PELA PREFERENCIA!</div>
        <div><?php echo html_escape($loja->title ?? ''); ?></div>
        <div>Volte sempre!</div>
        <div class="legal">
            * DOCUMENTO SEM VALOR FISCAL *
            <br>
            Cupom de controle interno
            <br>
            Impresso em <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>
</div>

<script>
// Auto-print: abre e imprime automaticamente com ?auto=1
if (window.location.search.indexOf('auto=1') !== -1) {
    window.onload = function() {
        setTimeout(function() { window.print(); }, 300);
    };
}
</script>

</body>
</html>
