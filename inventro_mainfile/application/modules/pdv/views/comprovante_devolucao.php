<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Devolução — <?php echo html_escape($devolucao->return_id ?? ''); ?></title>
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
        .cupom-tipo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 6px 0;
            border: 1px dashed #000;
            margin: 4px 0 8px;
            text-transform: uppercase;
        }
        .cupom-info {
            font-size: 11px;
            margin-bottom: 2px;
        }
        .cupom-itens {
            width: 100%;
            margin: 4px 0;
        }
        .cupom-item {
            margin-bottom: 4px;
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
        .cupom-item-motivo {
            font-size: 10px;
            color: #555;
            font-style: italic;
        }
        .cupom-totais {
            margin: 4px 0;
        }
        .cupom-total-linha {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .cupom-total-linha.cupom-total-destaque {
            font-size: 16px;
            font-weight: bold;
            padding: 4px 0;
        }
        .cupom-footer {
            text-align: center;
            margin-top: 8px;
            font-size: 10px;
            color: #555;
        }
        @media print {
            body { width: 80mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header da loja -->
    <div class="cupom-header">
        <h1><?php echo html_escape($setting->title ?? 'Inventro'); ?></h1>
        <?php if (!empty($setting->address)): ?>
            <p><?php echo html_escape($setting->address); ?></p>
        <?php endif; ?>
        <?php if (!empty($setting->phone)): ?>
            <p>Tel: <?php echo html_escape($setting->phone); ?></p>
        <?php endif; ?>
    </div>

    <div class="cupom-separator"></div>

    <!-- Tipo do documento -->
    <div class="cupom-tipo">COMPROVANTE DE DEVOLUÇÃO</div>

    <!-- Info da devolução -->
    <div class="cupom-info">Devolução: <?php echo html_escape($devolucao->return_id); ?></div>
    <div class="cupom-info">Venda Ref.: <?php echo html_escape($devolucao->venda_codigo ?? '-'); ?></div>
    <div class="cupom-info">Data: <?php echo date('d/m/Y H:i', strtotime($devolucao->created_at)); ?></div>
    <div class="cupom-info">Operador: <?php echo html_escape($devolucao->operador_nome ?? '-'); ?></div>
    <?php if (!empty($devolucao->reason)): ?>
        <div class="cupom-info">Motivo: <?php echo html_escape($devolucao->reason); ?></div>
    <?php endif; ?>

    <div class="cupom-separator"></div>

    <!-- Itens devolvidos -->
    <div class="cupom-itens">
        <?php
        $motivo_labels = [
            'defeito'         => 'Defeito',
            'arrependimento'  => 'Arrependimento',
            'troca'           => 'Troca',
            'erro_caixa'      => 'Erro caixa',
        ];
        ?>
        <?php if (!empty($devolucao->itens)): ?>
            <?php foreach ($devolucao->itens as $item): ?>
                <div class="cupom-item">
                    <div class="cupom-item-nome">
                        <?php echo html_escape($item->nome_produto ?: ('Produto #' . $item->product_id)); ?>
                    </div>
                    <div class="cupom-item-detalhe">
                        <span><?php echo number_format((float) $item->return_qty, $item->return_qty == (int) $item->return_qty ? 0 : 3, ',', '.'); ?> x R$ <?php echo number_format((float) $item->price, 2, ',', '.'); ?></span>
                        <span>R$ <?php echo number_format((float) $item->amount, 2, ',', '.'); ?></span>
                    </div>
                    <?php if (!empty($item->motivo)): ?>
                        <div class="cupom-item-motivo">
                            (<?php echo html_escape($motivo_labels[$item->motivo] ?? $item->motivo); ?>)
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cupom-separator"></div>

    <!-- Totais -->
    <div class="cupom-totais">
        <div class="cupom-total-linha cupom-total-destaque">
            <span>TOTAL DEVOLVIDO</span>
            <span>R$ <?php echo number_format((float) $devolucao->total_amount, 2, ',', '.'); ?></span>
        </div>
    </div>

    <div class="cupom-separator"></div>

    <!-- Footer -->
    <div class="cupom-footer">
        <p>COMPROVANTE NÃO FISCAL</p>
        <p><?php echo date('d/m/Y H:i:s'); ?></p>
    </div>

</body>
</html>
