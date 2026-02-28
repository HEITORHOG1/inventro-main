<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupom - Pedido <?php echo $order->order_number; ?></title>
    <style>
        /* Reset e configuração para impressora térmica 80mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            max-width: 80mm;
            background: white;
            color: #000;
            padding: 5mm;
        }

        .cupom {
            width: 100%;
        }

        /* Cabeçalho */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header .logo {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .header .info {
            font-size: 10px;
        }

        .header .cnpj {
            font-size: 11px;
            margin-top: 5px;
        }

        /* Informações do pedido */
        .pedido-info {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px dashed #000;
        }

        .pedido-numero {
            font-size: 16px;
            font-weight: bold;
        }

        .pedido-data {
            font-size: 10px;
            margin-top: 5px;
        }

        /* Dados do cliente */
        .cliente {
            padding: 8px 0;
            border-bottom: 1px dashed #000;
            font-size: 11px;
        }

        .cliente-label {
            font-weight: bold;
        }

        /* Itens */
        .itens {
            padding: 10px 0;
            border-bottom: 1px dashed #000;
        }

        .itens-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 11px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 5px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }

        .item-nome {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 40mm;
        }

        .item-qtd {
            width: 15mm;
            text-align: center;
        }

        .item-valor {
            width: 20mm;
            text-align: right;
        }

        /* Totais */
        .totais {
            padding: 10px 0;
            border-bottom: 1px dashed #000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }

        .total-row.destaque {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 8px;
        }

        /* Pagamento */
        .pagamento {
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px dashed #000;
        }

        .pagamento-tipo {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .troco {
            font-size: 11px;
            margin-top: 5px;
        }

        /* Observação */
        .observacao {
            padding: 8px 0;
            font-size: 10px;
            font-style: italic;
            border-bottom: 1px dashed #000;
        }

        /* Rodapé */
        .footer {
            text-align: center;
            padding-top: 15px;
            font-size: 10px;
        }

        .footer .obrigado {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer .legal {
            font-size: 9px;
            margin-top: 10px;
            color: #666;
        }

        /* Botões de ação (não imprimem) */
        .actions {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-print {
            background: #25D366;
            color: white;
        }

        .btn-close {
            background: #666;
            color: white;
        }

        /* Separador */
        .separador {
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 5px 0;
        }

        @media print {
            .actions {
                display: none !important;
            }

            body {
                width: 80mm;
                padding: 2mm;
            }
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
    <div class="actions">
        <button class="btn-action btn-print" onclick="window.print()">
            🖨️ IMPRIMIR
        </button>
        <button class="btn-action btn-close" onclick="window.close()">
            ✕ FECHAR
        </button>
    </div>

    <div class="cupom">
        <!-- Cabeçalho -->
        <div class="header">
            <div class="logo"><?php echo html_escape($loja->title ?? 'LOJA'); ?></div>
            <div class="info">
                <?php echo html_escape($loja->address ?? ''); ?>
            </div>
            <div class="info">
                <?php if (!empty($loja->phone)): ?>
                    Tel: <?php echo html_escape($loja->phone); ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($loja->cnpj)): ?>
                <div class="cnpj">CNPJ: <?php echo html_escape($loja->cnpj); ?></div>
            <?php endif; ?>
        </div>

        <!-- Informações do Pedido -->
        <div class="pedido-info">
            <div class="pedido-numero">PEDIDO #<?php echo $order->order_number; ?></div>
            <div class="pedido-data">
                <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
            </div>
            <div style="font-size:10px; margin-top:5px;">
                <?php 
                $origem = $order->tipo_checkout == 'whatsapp' ? 'Via WhatsApp' : 'Via Site';
                echo $origem;
                ?>
            </div>
        </div>

        <!-- Dados do Cliente -->
        <div class="cliente">
            <div><span class="cliente-label">Cliente:</span> <?php echo html_escape($order->cliente_nome); ?></div>
            <div><span class="cliente-label">Tel:</span> <?php echo html_escape($order->cliente_telefone); ?></div>
            <div><span class="cliente-label">End:</span> <?php echo html_escape($order->cliente_endereco); ?></div>
            <?php if (!empty($order->cliente_complemento)): ?>
                <div><?php echo html_escape($order->cliente_complemento); ?></div>
            <?php endif; ?>
            <?php if (!empty($order->cpf_nota)): ?>
                <div style="margin-top:5px;"><span class="cliente-label">CPF:</span> <?php echo html_escape($order->cpf_nota); ?></div>
            <?php endif; ?>
        </div>

        <!-- Itens -->
        <div class="itens">
            <div class="itens-header">
                <span style="flex:1;">ITEM</span>
                <span style="width:15mm;text-align:center;">QTD</span>
                <span style="width:20mm;text-align:right;">VALOR</span>
            </div>
            
            <?php foreach ($order->items as $item): ?>
                <div class="item">
                    <span class="item-nome"><?php echo html_escape($item->product_name); ?></span>
                    <span class="item-qtd"><?php echo $item->quantity; ?>x</span>
                    <span class="item-valor"><?php echo number_format($item->total_price, 2, ',', '.'); ?></span>
                </div>
                <?php if ($item->quantity > 1): ?>
                    <div style="font-size:9px;color:#666;padding-left:5px;">
                        (<?php echo number_format($item->unit_price, 2, ',', '.'); ?> cada)
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Totais -->
        <div class="totais">
            <div class="total-row">
                <span>Subtotal</span>
                <span>R$ <?php echo number_format($order->subtotal, 2, ',', '.'); ?></span>
            </div>
            
            <div class="total-row">
                <span>Taxa de Entrega</span>
                <span>
                    <?php echo $order->taxa_entrega == 0 ? 'GRÁTIS' : 'R$ ' . number_format($order->taxa_entrega, 2, ',', '.'); ?>
                </span>
            </div>
            
            <?php if ($order->desconto > 0): ?>
                <div class="total-row" style="color:green;">
                    <span>Desconto</span>
                    <span>- R$ <?php echo number_format($order->desconto, 2, ',', '.'); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="total-row destaque">
                <span>TOTAL</span>
                <span>R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Pagamento -->
        <div class="pagamento">
            <div class="pagamento-tipo">
                <?php 
                $pagamentos = [
                    'dinheiro' => '💵 DINHEIRO',
                    'cartao' => '💳 CARTÃO',
                    'pix' => '📱 PIX'
                ];
                echo $pagamentos[$order->forma_pagamento] ?? strtoupper($order->forma_pagamento);
                ?>
            </div>
            
            <?php if ($order->forma_pagamento == 'dinheiro' && $order->troco_para > 0): ?>
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

        <!-- Separador -->
        <div class="separador">
            --------------------------------
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <div class="obrigado">OBRIGADO PELA PREFERÊNCIA!</div>
            <div>Volte sempre!</div>
            
            <div class="legal">
                * Documento sem valor fiscal *
                <br>
                Cupom de controle interno
            </div>
        </div>
    </div>

    <script>
        // Auto-print se for impressão direta
        if (window.location.search.includes('auto=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
