<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cupom — <?php echo html_escape($venda->invoice_id ?? ''); ?></title>
    <style>
        @page { margin: 0; size: 80mm auto; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            padding: 4mm;
            color: #000;
        }
        .cupom-center { text-align: center; }
        .cupom-bold { font-weight: bold; }
        .cupom-separator {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .cupom-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .cupom-header h1 {
            font-size: 16px;
            margin-bottom: 2px;
        }
        .cupom-header p {
            font-size: 10px;
            color: #333;
        }
        .cupom-nao-fiscal {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 0;
            border: 1px dashed #000;
            margin: 4px 0 8px;
        }
        .cupom-2via {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 4px 0;
            margin-bottom: 4px;
        }
        .cupom-info {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .cupom-info span {
            display: inline-block;
        }
        .cupom-itens {
            width: 100%;
            margin: 4px 0;
        }
        .cupom-item {
            margin-bottom: 2px;
        }
        .cupom-item-nome {
            font-size: 11px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .cupom-item-detalhe {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .cupom-totais {
            margin: 4px 0;
        }
        .cupom-total-linha {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .cupom-total-destaque {
            font-size: 16px;
            font-weight: bold;
        }
        .cupom-pagamento {
            margin: 4px 0;
        }
        .cupom-pgto-linha {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .cupom-troco {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            padding: 6px 0;
        }
        .cupom-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
            color: #666;
        }
        @media print {
            body { width: 80mm; padding: 2mm; }
        }
    </style>
</head>
<body>
<?php if (!empty($segunda_via)): ?>
    <div class="cupom-2via">*** 2ª VIA ***</div>
<?php endif; ?>

    <div class="cupom-header">
        <h1><?php echo html_escape($setting->title ?? 'Inventro'); ?></h1>
        <?php if (!empty($setting->address)): ?>
            <p><?php echo html_escape($setting->address); ?></p>
        <?php endif; ?>
        <?php if (!empty($setting->phone)): ?>
            <p>Tel: <?php echo html_escape($setting->phone); ?></p>
        <?php endif; ?>
    </div>

    <div class="cupom-nao-fiscal">*** NAO E DOCUMENTO FISCAL ***</div>

    <div class="cupom-info">
        <span>Cupom: <?php echo html_escape($venda->invoice_id); ?></span><br>
        <span>Data: <?php echo date('d/m/Y H:i', strtotime(isset($venda->created_at) ? $venda->created_at : $venda->date)); ?></span><br>
        <span>Caixa: <?php echo html_escape($venda->terminal_numero ?? '-'); ?></span>
        <span> | Op: <?php echo html_escape($venda->operador_nome ?? '-'); ?></span>
    </div>

    <?php if (!empty($venda->description)): ?>
    <div class="cupom-info"><?php echo html_escape($venda->description); ?></div>
    <?php endif; ?>

    <div class="cupom-separator"></div>

    <div class="cupom-itens">
        <?php $seq = 0; foreach ($itens as $item): $seq++; ?>
        <div class="cupom-item">
            <div class="cupom-item-nome"><?php echo str_pad($seq, 3, '0', STR_PAD_LEFT); ?> <?php echo html_escape($item->descricao_manual ?: $item->product_name); ?></div>
            <?php $ean_display = !empty($item->ean_gtin) ? $item->ean_gtin : (!empty($item->product_code) ? $item->product_code : ''); ?>
            <?php if ($ean_display): ?>
            <div class="cupom-item-codigo" style="font-size:10px;color:#666;font-family:monospace;margin-left:28px;"><?php echo html_escape($ean_display); ?></div>
            <?php endif; ?>
            <div class="cupom-item-detalhe">
                <span><?php $q = isset($item->quantity) ? $item->quantity : 0; echo number_format($q, $q == intval($q) ? 0 : 3, ',', '.'); ?> x R$ <?php echo number_format(isset($item->price) ? $item->price : 0, 2, ',', '.'); ?></span>
                <span>R$ <?php echo number_format(isset($item->total_price) ? $item->total_price : 0, 2, ',', '.'); ?></span>
            </div>
            <?php
                $desc_item_val = (isset($item->desconto_valor) ? (float) $item->desconto_valor : 0)
                               + (isset($item->desconto_rateio) ? (float) $item->desconto_rateio : 0);
                if ($desc_item_val > 0):
            ?>
            <div class="cupom-item-desconto" style="color:#c00;font-size:11px;text-align:right;">
                <?php
                    $label_parts = [];
                    if (!empty($item->desconto_pct) && (float) $item->desconto_pct > 0) {
                        $label_parts[] = '-' . number_format($item->desconto_pct, 0) . '%';
                    }
                    $label_parts[] = '-R$ ' . number_format($desc_item_val, 2, ',', '.');
                    echo html_escape(implode(' = ', $label_parts));
                ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="cupom-separator"></div>

    <div class="cupom-totais">
        <div class="cupom-total-linha">
            <span>Subtotal</span>
            <span>R$ <?php echo number_format(isset($venda->total_amount) ? $venda->total_amount : 0, 2, ',', '.'); ?></span>
        </div>
        <?php $desc = isset($venda->invoice_discount) ? (float)$venda->invoice_discount : 0; if ($desc > 0): ?>
        <div class="cupom-total-linha">
            <span>Desconto</span>
            <span>- R$ <?php echo number_format($desc, 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <div class="cupom-total-linha cupom-total-destaque">
            <span>TOTAL</span>
            <span>R$ <?php echo number_format(isset($venda->paid_amount) ? $venda->paid_amount : (isset($venda->total_amount) ? $venda->total_amount : 0), 2, ',', '.'); ?></span>
        </div>
    </div>

    <div class="cupom-separator"></div>

    <div class="cupom-pagamento">
        <?php
        $formaLabels = [
            'dinheiro' => 'Dinheiro',
            'debito'   => 'Cartão Débito',
            'credito'  => 'Cartão Crédito',
            'pix'      => 'PIX',
            'fiado'    => 'Fiado',
        ];
        $trocoTotal = 0;
        foreach ($pagamentos as $pgto):
            $trocoTotal += (float) $pgto->troco;
        ?>
        <div class="cupom-pgto-linha">
            <span><?php $f = isset($pgto->forma) ? $pgto->forma : (isset($pgto->forma_pagamento) ? $pgto->forma_pagamento : ''); echo html_escape(isset($formaLabels[$f]) ? $formaLabels[$f] : $f); ?></span>
            <span>R$ <?php echo number_format($pgto->valor, 2, ',', '.'); ?></span>
        </div>
        <?php endforeach; ?>

        <?php if ($trocoTotal > 0): ?>
        <div class="cupom-separator"></div>
        <div class="cupom-troco">TROCO: R$ <?php echo number_format($trocoTotal, 2, ',', '.'); ?></div>
        <?php endif; ?>
    </div>

    <?php
    // Fase 8: Seção de fiado no cupom
    $temFiado = false;
    foreach ($pagamentos as $pgto) {
        if (($pgto->forma_pagamento ?? '') === 'fiado') {
            $temFiado = true;
            break;
        }
    }
    if ($temFiado && !empty($fiado)):
    ?>
    <div class="cupom-separator"></div>
    <div class="cupom-center cupom-bold" style="padding:4px 0;">*** COMPROVANTE DE DEBITO ***</div>
    <div class="cupom-info">
        <span>Cliente: <?php echo html_escape($fiado->cliente_nome ?? '-'); ?></span><br>
        <?php if (!empty($fiado->debito_anterior)): ?>
        <span>Dívida anterior: R$ <?php echo number_format((float)$fiado->debito_anterior, 2, ',', '.'); ?></span><br>
        <?php endif; ?>
        <span>Valor desta compra: R$ <?php echo number_format((float)$fiado->valor_fiado, 2, ',', '.'); ?></span><br>
        <span style="font-weight:bold;">Dívida total: R$ <?php echo number_format((float)$fiado->debito_total, 2, ',', '.'); ?></span>
    </div>
    <?php endif; ?>

    <div class="cupom-separator"></div>

    <div class="cupom-footer">
        <p>Qtd itens: <?php echo count($itens); ?></p>
        <p><?php echo date('d/m/Y H:i:s', strtotime($venda->created_at)); ?></p>
        <p>Obrigado pela preferência!</p>
    </div>

    <script>
    window.onload = function() {
        window.print();
    };
    </script>
</body>
</html>
