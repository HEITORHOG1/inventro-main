<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante — <?php echo html_escape($tipo ?? 'Movimento'); ?></title>
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
        .comp-center { text-align: center; }
        .comp-bold { font-weight: bold; }
        .comp-separator {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .comp-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .comp-header h1 {
            font-size: 16px;
            margin-bottom: 2px;
        }
        .comp-header p {
            font-size: 10px;
            color: #333;
        }
        .comp-tipo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            padding: 6px 0;
            border: 1px dashed #000;
            margin: 4px 0 8px;
            text-transform: uppercase;
        }
        .comp-info {
            font-size: 11px;
            margin-bottom: 2px;
        }
        .comp-info-linha {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .comp-valor-destaque {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 8px 0;
        }
        .comp-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
            color: #666;
        }
        .comp-assinatura {
            margin-top: 24px;
            text-align: center;
            font-size: 10px;
        }
        .comp-assinatura-linha {
            border-top: 1px solid #000;
            width: 60%;
            margin: 0 auto 4px;
        }
        @media print {
            body { width: 80mm; padding: 2mm; }
        }
    </style>
</head>
<body>

    <div class="comp-header">
        <h1><?php echo html_escape($setting->title ?? 'Inventro'); ?></h1>
        <?php if (!empty($setting->address)): ?>
            <p><?php echo html_escape($setting->address); ?></p>
        <?php endif; ?>
        <?php if (!empty($setting->phone)): ?>
            <p>Tel: <?php echo html_escape($setting->phone); ?></p>
        <?php endif; ?>
    </div>

    <div class="comp-tipo"><?php echo html_escape($tipo ?? 'MOVIMENTO'); ?></div>

    <div class="comp-separator"></div>

    <div class="comp-info">
        <div class="comp-info-linha">
            <span>Data/Hora:</span>
            <span><?php echo html_escape($data_hora ?? date('d/m/Y H:i:s')); ?></span>
        </div>
        <div class="comp-info-linha">
            <span>Terminal:</span>
            <span>Caixa <?php echo html_escape($terminal_numero ?? '-'); ?></span>
        </div>
        <div class="comp-info-linha">
            <span>Operador:</span>
            <span><?php echo html_escape($operador_nome ?? '-'); ?></span>
        </div>
        <?php if (!empty($supervisor_nome)): ?>
        <div class="comp-info-linha">
            <span>Supervisor:</span>
            <span><?php echo html_escape($supervisor_nome); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="comp-separator"></div>

    <div class="comp-valor-destaque">
        R$ <?php echo number_format((float)($valor ?? 0), 2, ',', '.'); ?>
    </div>

    <?php if (!empty($motivo)): ?>
    <div class="comp-separator"></div>
    <div class="comp-info">
        <span class="comp-bold">Motivo:</span><br>
        <?php echo html_escape($motivo); ?>
    </div>
    <?php endif; ?>

    <?php
    // Detalhes extras para fechamento de caixa
    $tipo_upper = strtoupper($tipo ?? '');
    if ($tipo_upper === 'FECHAMENTO' || $tipo_upper === 'FECHAMENTO DE CAIXA'):
    ?>
    <div class="comp-separator"></div>

    <?php if (!empty($resumo)): ?>
    <div class="comp-info">
        <?php if (isset($resumo['fundo_troco'])): ?>
        <div class="comp-info-linha">
            <span>Fundo de Troco:</span>
            <span>R$ <?php echo number_format((float)$resumo['fundo_troco'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['total_vendas'])): ?>
        <div class="comp-info-linha">
            <span>Total Vendas:</span>
            <span>R$ <?php echo number_format((float)$resumo['total_vendas'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['total_sangrias'])): ?>
        <div class="comp-info-linha">
            <span>Sangrias:</span>
            <span>- R$ <?php echo number_format((float)$resumo['total_sangrias'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['total_suprimentos'])): ?>
        <div class="comp-info-linha">
            <span>Suprimentos:</span>
            <span>+ R$ <?php echo number_format((float)$resumo['total_suprimentos'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['total_cancelamentos'])): ?>
        <div class="comp-info-linha">
            <span>Cancelamentos:</span>
            <span>- R$ <?php echo number_format((float)$resumo['total_cancelamentos'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>

        <div class="comp-separator"></div>

        <?php if (isset($resumo['saldo_esperado'])): ?>
        <div class="comp-info-linha comp-bold">
            <span>Saldo Esperado:</span>
            <span>R$ <?php echo number_format((float)$resumo['saldo_esperado'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['valor_contado'])): ?>
        <div class="comp-info-linha comp-bold">
            <span>Valor Contado:</span>
            <span>R$ <?php echo number_format((float)$resumo['valor_contado'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($resumo['diferenca'])): ?>
        <div class="comp-info-linha comp-bold">
            <span>Diferença:</span>
            <span>R$ <?php echo number_format((float)$resumo['diferenca'], 2, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($observacao)): ?>
    <div class="comp-separator"></div>
    <div class="comp-info">
        <span class="comp-bold">Obs:</span> <?php echo html_escape($observacao); ?>
    </div>
    <?php endif; ?>

    <?php endif; /* end fechamento */ ?>

    <div class="comp-separator"></div>

    <?php if ($tipo_upper === 'SANGRIA' || $tipo_upper === 'SUPRIMENTO'): ?>
    <div class="comp-assinatura">
        <br><br>
        <div class="comp-assinatura-linha"></div>
        <span>Supervisor</span>
    </div>
    <?php endif; ?>

    <div class="comp-footer">
        <p><?php echo date('d/m/Y H:i:s'); ?></p>
        <p>*** NAO E DOCUMENTO FISCAL ***</p>
    </div>

    <script>
    window.onload = function() {
        window.print();
    };
    </script>
</body>
</html>
