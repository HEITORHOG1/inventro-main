<?php
/**
 * Kanban Board - Painel de Pedidos em Tempo Real
 *
 * Dados recebidos do controller:
 *   $orders_by_status - array associativo (pendente, confirmado, preparando, saiu_entrega, entregue, cancelado)
 *   $loja_pausada     - boolean
 */

// Status progression map for "Avancar" button
$status_next = [
    'pendente'      => 'confirmado',
    'confirmado'    => 'preparando',
    'preparando'    => 'pronto_coleta',
    'pronto_coleta' => 'saiu_entrega',
    'saiu_entrega'  => 'entregue',
];

// Kanban columns configuration (cancelado excluded from board columns)
$kanban_columns = [
    'pendente'      => ['label' => 'Novos',           'color' => '#f39c12', 'icon' => 'fa-clock-o'],
    'confirmado'    => ['label' => 'Confirmado',       'color' => '#00bcd4', 'icon' => 'fa-check'],
    'preparando'    => ['label' => 'Preparando',       'color' => '#9b59b6', 'icon' => 'fa-cutlery'],
    'pronto_coleta' => ['label' => 'Pronto p/ Coleta', 'color' => '#e67e22', 'icon' => 'fa-archive'],
    'saiu_entrega'  => ['label' => 'A Caminho',        'color' => '#3498db', 'icon' => 'fa-motorcycle'],
    'entregue'      => ['label' => 'Entregue',         'color' => '#27ae60', 'icon' => 'fa-check-circle'],
];

// Payment method icons
$pagamento_icons = [
    'dinheiro' => 'fa-money',
    'cartao'   => 'fa-credit-card',
    'pix'      => 'fa-qrcode',
];

// Count active orders (non-entregue) for title badge
$active_count = 0;
foreach (['pendente', 'confirmado', 'preparando', 'pronto_coleta', 'saiu_entrega'] as $s) {
    $active_count += count($orders_by_status[$s] ?? []);
}
?>

<!-- Content Header -->
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fa fa-columns"></i> Painel Kanban
            <?php if ($active_count > 0): ?>
                <span class="badge badge-warning" id="kanban-active-count"><?php echo (int)$active_count; ?></span>
            <?php endif; ?>
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('delivery/orders'); ?>">Pedidos</a></li>
            <li class="breadcrumb-item active">Kanban</li>
        </ol>
    </div>
</div>

<!-- Store Paused Alert -->
<?php if ($loja_pausada): ?>
<div class="alert alert-danger alert-dismissible" id="alert-loja-pausada">
    <i class="fa fa-pause-circle fa-lg"></i>
    <strong>Loja Pausada</strong> - nao esta recebendo pedidos
</div>
<?php endif; ?>

<!-- Top Action Bar -->
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group mr-2">
            <button type="button" class="btn <?php echo $loja_pausada ? 'btn-success' : 'btn-danger'; ?> btn-sm" id="btn-toggle-pause">
                <i class="fa <?php echo $loja_pausada ? 'fa-play' : 'fa-pause'; ?>"></i>
                <?php echo $loja_pausada ? 'Ativar Loja' : 'Pausar Loja'; ?>
            </button>
        </div>
        <a href="<?php echo base_url('delivery/orders'); ?>" class="btn btn-default btn-sm mr-2">
            <i class="fa fa-list"></i> Lista de Pedidos
        </a>
        <button type="button" class="btn btn-default btn-sm" id="btn-toggle-sound" title="Som de notificacao">
            <i class="fa fa-volume-up" id="sound-icon"></i> Som
        </button>
        <span class="ml-3 text-muted" id="last-update-label">
            <i class="fa fa-refresh fa-spin" style="display:none;" id="polling-spinner"></i>
            <small>Atualizado agora</small>
        </span>
    </div>
</div>

<!-- Kanban Board -->
<div class="kanban-board-wrapper">
    <div class="kanban-board" id="kanban-board">
        <?php foreach ($kanban_columns as $status_key => $col): ?>
            <?php $orders = $orders_by_status[$status_key] ?? []; ?>
            <div class="kanban-column" data-status="<?php echo htmlspecialchars($status_key, ENT_QUOTES, 'UTF-8'); ?>">
                <!-- Column Header -->
                <div class="kanban-column-header" style="background-color: <?php echo $col['color']; ?>;">
                    <i class="fa <?php echo $col['icon']; ?>"></i>
                    <span class="kanban-column-title"><?php echo html_escape($col['label']); ?></span>
                    <span class="badge badge-light kanban-count" id="count-<?php echo htmlspecialchars($status_key, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo count($orders); ?>
                    </span>
                </div>

                <!-- Column Body -->
                <div class="kanban-column-body" id="col-<?php echo htmlspecialchars($status_key, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (empty($orders)): ?>
                        <div class="kanban-empty text-muted text-center py-4">
                            <i class="fa fa-inbox fa-2x"></i>
                            <p class="mt-2">Nenhum pedido</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            // Summarize first 3 items
                            $items_summary = [];
                            $items_list = isset($order->items) ? $order->items : [];
                            $shown = 0;
                            foreach ($items_list as $item) {
                                if ($shown >= 3) break;
                                $items_summary[] = (int)$item->quantity . 'x ' . html_escape($item->product_name);
                                $shown++;
                            }
                            $remaining = count($items_list) - $shown;
                            $pag_icon = $pagamento_icons[$order->forma_pagamento] ?? 'fa-question';
                            $next_status = $status_next[$status_key] ?? null;
                            $next_label_map = [
                                'confirmado'    => 'Confirmar',
                                'preparando'    => 'Preparar',
                                'pronto_coleta' => 'Pronto Coleta',
                                'saiu_entrega'  => 'Saiu Entrega',
                                'entregue'      => 'Entregue',
                            ];
                            ?>
                            <div class="kanban-card" id="order-card-<?php echo (int)$order->id; ?>" data-order-id="<?php echo (int)$order->id; ?>">
                                <div class="kanban-card-header">
                                    <strong class="text-primary">#<?php echo html_escape($order->order_number); ?></strong>
                                    <small class="text-muted float-right">
                                        <i class="fa fa-clock-o"></i>
                                        <?php echo date('H:i', strtotime($order->created_at)); ?>
                                    </small>
                                </div>
                                <div class="kanban-card-body">
                                    <div class="kanban-card-client">
                                        <i class="fa fa-user"></i>
                                        <?php echo html_escape($order->cliente_nome); ?>
                                    </div>
                                    <div class="kanban-card-items text-muted">
                                        <small>
                                            <?php echo implode(', ', $items_summary); ?>
                                            <?php if ($remaining > 0): ?>
                                                <em>+<?php echo (int)$remaining; ?> mais</em>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="kanban-card-footer mt-2">
                                        <span class="kanban-card-total">
                                            <strong>R$ <?php echo number_format($order->total, 2, ',', '.'); ?></strong>
                                        </span>
                                        <span class="kanban-card-payment float-right" title="<?php echo htmlspecialchars(ucfirst($order->forma_pagamento), ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa <?php echo $pag_icon; ?>"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="kanban-card-actions">
                                    <?php if ($next_status !== null): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-success btn-block btn-avancar"
                                                data-order-id="<?php echo (int)$order->id; ?>"
                                                data-next-status="<?php echo htmlspecialchars($next_status, ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa fa-arrow-right"></i>
                                            <?php echo html_escape($next_label_map[$next_status] ?? 'Avancar'); ?>
                                        </button>
                                    <?php endif; ?>
                                    <div class="btn-group btn-group-sm mt-1" style="width:100%;">
                                        <button type="button" class="btn btn-default btn-enviar-cupom" style="width:34%;"
                                                data-order-id="<?php echo (int)$order->id; ?>" title="Enviar Cupom WhatsApp">
                                            <i class="fa fa-whatsapp"></i>
                                        </button>
                                        <a href="<?php echo base_url('delivery/orders/print_order/' . (int)$order->id); ?>?auto=1"
                                           target="_blank" class="btn btn-default" style="width:33%;" title="Imprimir Cupom">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <a href="<?php echo base_url('delivery/orders/view/' . (int)$order->id); ?>"
                                           class="btn btn-default" style="width:33%;" title="Ver detalhes">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Inline CSS -->
<style>
/* Kanban Board Layout */
.kanban-board-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 15px;
}
.kanban-board {
    display: flex;
    gap: 12px;
    min-width: max-content;
    align-items: flex-start;
}
.kanban-column {
    width: 280px;
    min-width: 260px;
    flex-shrink: 0;
    background: #f4f6f9;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}
.kanban-column-header {
    color: #fff;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}
.kanban-column-header .badge {
    margin-left: auto;
    font-size: 13px;
    min-width: 24px;
}
.kanban-column-body {
    padding: 8px;
    min-height: 200px;
    max-height: calc(100vh - 280px);
    overflow-y: auto;
}

/* Kanban Cards */
.kanban-card {
    background: #fff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    border-left: 3px solid transparent;
    transition: box-shadow 0.2s ease;
}
.kanban-card:hover {
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.kanban-card-header {
    margin-bottom: 6px;
    line-height: 1.4;
}
.kanban-card-client {
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kanban-card-items {
    font-size: 12px;
    line-height: 1.4;
    max-height: 38px;
    overflow: hidden;
}
.kanban-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.kanban-card-total {
    color: #27ae60;
    font-size: 14px;
}
.kanban-card-payment {
    font-size: 16px;
    color: #777;
}
.kanban-card-actions {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #eee;
}
.kanban-card-actions .btn-avancar {
    font-weight: 600;
}

/* Card border colors per parent column */
.kanban-column[data-status="pendente"] .kanban-card     { border-left-color: #f39c12; }
.kanban-column[data-status="confirmado"] .kanban-card   { border-left-color: #00bcd4; }
.kanban-column[data-status="preparando"] .kanban-card    { border-left-color: #9b59b6; }
.kanban-column[data-status="pronto_coleta"] .kanban-card { border-left-color: #e67e22; }
.kanban-column[data-status="saiu_entrega"] .kanban-card  { border-left-color: #3498db; }
.kanban-column[data-status="entregue"] .kanban-card     { border-left-color: #27ae60; }

/* Card highlight animation for new orders */
.kanban-card.card-new {
    animation: cardPulse 1s ease-in-out 3;
}
@keyframes cardPulse {
    0%, 100% { background: #fff; }
    50% { background: #fff9e6; }
}

/* Empty state */
.kanban-empty {
    opacity: 0.5;
}

/* Scrollbar styling for columns */
.kanban-column-body::-webkit-scrollbar {
    width: 5px;
}
.kanban-column-body::-webkit-scrollbar-track {
    background: transparent;
}
.kanban-column-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
.kanban-column-body::-webkit-scrollbar-thumb:hover {
    background: #aaa;
}

/* Responsive: allow scroll on small screens */
@media (max-width: 992px) {
    .kanban-column {
        width: 250px;
        min-width: 240px;
    }
}
@media (max-width: 576px) {
    .kanban-column {
        width: 230px;
        min-width: 220px;
    }
}

/* Button loading state */
.btn-avancar.loading {
    opacity: 0.7;
    pointer-events: none;
}
</style>

<!-- JavaScript -->
<script>
(function() {
    'use strict';

    // -------------------------------------------
    // Configuration
    // -------------------------------------------
    var baseUrl    = $('#mainsiteurl').val() || '<?php echo base_url(); ?>';
    var csrfName   = 'csrf_test_name';
    var csrfHash   = $('#csrf_token').val() || '<?php echo $this->security->get_csrf_hash(); ?>';
    var pollInterval = 15000; // 15 seconds
    var lastTimestamp = '<?php echo date('Y-m-d H:i:s'); ?>';
    var soundEnabled = true;
    var previousPendenteCount = <?php echo count($orders_by_status['pendente'] ?? []); ?>;
    var audioContext = null;

    // Status progression map (matches PHP)
    var statusNext = {
        'pendente':      'confirmado',
        'confirmado':    'preparando',
        'preparando':    'pronto_coleta',
        'pronto_coleta': 'saiu_entrega',
        'saiu_entrega':  'entregue'
    };

    var nextLabelMap = {
        'confirmado':    'Confirmar',
        'preparando':    'Preparar',
        'pronto_coleta': 'Pronto Coleta',
        'saiu_entrega':  'Saiu Entrega',
        'entregue':      'Entregue'
    };

    var statusColors = {
        'pendente':      '#f39c12',
        'confirmado':    '#00bcd4',
        'preparando':    '#9b59b6',
        'pronto_coleta': '#e67e22',
        'saiu_entrega':  '#3498db',
        'entregue':      '#27ae60'
    };

    var pagamentoIcons = {
        'dinheiro': 'fa-money',
        'cartao':   'fa-credit-card',
        'pix':      'fa-qrcode'
    };

    // -------------------------------------------
    // Initialize Web Audio API for beep
    // -------------------------------------------
    function initAudio() {
        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        } catch (e) {
            // Web Audio not supported
        }
    }

    function playBeep() {
        if (!soundEnabled || !audioContext) return;
        try {
            var oscillator = audioContext.createOscillator();
            var gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 880; // A5 note
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.5);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);

            // Play second beep after a short pause
            setTimeout(function() {
                if (!audioContext) return;
                var osc2 = audioContext.createOscillator();
                var gain2 = audioContext.createGain();
                osc2.connect(gain2);
                gain2.connect(audioContext.destination);
                osc2.frequency.value = 1100;
                osc2.type = 'sine';
                gain2.gain.setValueAtTime(0.3, audioContext.currentTime);
                gain2.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.4);
                osc2.start(audioContext.currentTime);
                osc2.stop(audioContext.currentTime + 0.4);
            }, 300);
        } catch (e) {
            // Ignore audio errors
        }
    }

    // -------------------------------------------
    // Browser Notification API
    // -------------------------------------------
    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    function showBrowserNotification(title, body) {
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                var notification = new Notification(title, {
                    body: body,
                    icon: baseUrl + 'admin_assets/img/icons/mini-logo.png',
                    tag: 'kanban-new-order'
                });
                notification.onclick = function() {
                    window.focus();
                    notification.close();
                };
                setTimeout(function() { notification.close(); }, 8000);
            } catch (e) {
                // Notification failed
            }
        }
    }

    // -------------------------------------------
    // Update page title with active count
    // -------------------------------------------
    function updatePageTitle(count) {
        if (count > 0) {
            document.title = '(' + count + ') Kanban - Pedidos';
        } else {
            document.title = 'Kanban - Pedidos';
        }
        var $badge = $('#kanban-active-count');
        if (count > 0) {
            if ($badge.length) {
                $badge.text(count);
            } else {
                $('h1.m-0').first().append(' <span class="badge badge-warning" id="kanban-active-count">' + count + '</span>');
            }
        } else {
            $badge.remove();
        }
    }

    // -------------------------------------------
    // Build order card HTML (for dynamic insertion)
    // -------------------------------------------
    function buildOrderCardHtml(order, statusKey) {
        var items = order.items || [];
        var summaryParts = [];
        var shown = 0;
        for (var i = 0; i < items.length && shown < 3; i++) {
            summaryParts.push(parseInt(items[i].quantity) + 'x ' + escapeHtml(items[i].product_name));
            shown++;
        }
        var remaining = items.length - shown;
        var pagIcon = pagamentoIcons[order.forma_pagamento] || 'fa-question';
        var nextStatus = statusNext[statusKey] || null;

        var total = parseFloat(order.total) || 0;
        var totalFormatted = 'R$ ' + total.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        var createdAt = order.created_at || '';
        var timeStr = '';
        if (createdAt) {
            var parts = createdAt.split(' ');
            if (parts.length > 1) {
                timeStr = parts[1].substring(0, 5);
            }
        }

        var html = '<div class="kanban-card card-new" id="order-card-' + parseInt(order.id) + '" data-order-id="' + parseInt(order.id) + '">';
        html += '<div class="kanban-card-header">';
        html += '<strong class="text-primary">#' + escapeHtml(order.order_number) + '</strong>';
        html += '<small class="text-muted float-right"><i class="fa fa-clock-o"></i> ' + escapeHtml(timeStr) + '</small>';
        html += '</div>';
        html += '<div class="kanban-card-body">';
        html += '<div class="kanban-card-client"><i class="fa fa-user"></i> ' + escapeHtml(order.cliente_nome) + '</div>';
        html += '<div class="kanban-card-items text-muted"><small>' + summaryParts.join(', ');
        if (remaining > 0) {
            html += ' <em>+' + remaining + ' mais</em>';
        }
        html += '</small></div>';
        html += '<div class="kanban-card-footer mt-2">';
        html += '<span class="kanban-card-total"><strong>' + totalFormatted + '</strong></span>';
        html += '<span class="kanban-card-payment float-right" title="' + escapeHtml(capitalize(order.forma_pagamento || '')) + '">';
        html += '<i class="fa ' + pagIcon + '"></i></span>';
        html += '</div></div>';

        html += '<div class="kanban-card-actions">';
        if (nextStatus) {
            html += '<button type="button" class="btn btn-sm btn-success btn-block btn-avancar" ';
            html += 'data-order-id="' + parseInt(order.id) + '" data-next-status="' + escapeHtml(nextStatus) + '">';
            html += '<i class="fa fa-arrow-right"></i> ' + escapeHtml(nextLabelMap[nextStatus] || 'Avancar');
            html += '</button>';
        }
        html += '<div class="btn-group btn-group-sm mt-1" style="width:100%;">';
        html += '<button type="button" class="btn btn-default btn-enviar-cupom" style="width:34%;" data-order-id="' + parseInt(order.id) + '" title="Enviar Cupom WhatsApp">';
        html += '<i class="fa fa-whatsapp"></i></button>';
        html += '<a href="' + baseUrl + 'delivery/orders/print_order/' + parseInt(order.id) + '?auto=1" target="_blank" class="btn btn-default" style="width:33%;" title="Imprimir Cupom">';
        html += '<i class="fa fa-print"></i></a>';
        html += '<a href="' + baseUrl + 'delivery/orders/view/' + parseInt(order.id) + '" class="btn btn-default" style="width:33%;" title="Ver detalhes">';
        html += '<i class="fa fa-eye"></i></a>';
        html += '</div></div></div>';

        return html;
    }

    // -------------------------------------------
    // Utility: escape HTML to prevent XSS
    // -------------------------------------------
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // -------------------------------------------
    // AJAX: Advance order status
    // -------------------------------------------
    function advanceStatus(orderId, nextStatus, $btn) {
        $btn.addClass('loading');
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Aguarde...');

        var postData = {
            'order_id': orderId,
            'status': nextStatus
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: baseUrl + 'delivery/orders/ajax_update_status',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                    $('#csrf_token').val(csrfHash);
                }
                if (response.success) {
                    // Move card to target column
                    var $card = $('#order-card-' + orderId);
                    var $targetCol = $('#col-' + nextStatus);

                    // Remove empty state from target if present
                    $targetCol.find('.kanban-empty').remove();

                    // Update card's Avancar button for new status
                    var furtherNext = statusNext[nextStatus] || null;
                    var $actions = $card.find('.kanban-card-actions');
                    $actions.find('.btn-avancar').remove();

                    if (furtherNext) {
                        var newBtn = '<button type="button" class="btn btn-sm btn-success btn-block btn-avancar" ';
                        newBtn += 'data-order-id="' + orderId + '" data-next-status="' + escapeHtml(furtherNext) + '">';
                        newBtn += '<i class="fa fa-arrow-right"></i> ' + escapeHtml(nextLabelMap[furtherNext] || 'Avancar');
                        newBtn += '</button>';
                        $actions.prepend(newBtn);
                    }

                    // Animate move
                    $card.fadeOut(200, function() {
                        $card.prependTo($targetCol).fadeIn(300);
                        $card.addClass('card-new');
                        setTimeout(function() { $card.removeClass('card-new'); }, 3000);
                    });

                    updateColumnCounts();

                    // Auto-print cupom ao confirmar pedido
                    if (nextStatus === 'confirmado') {
                        window.open(baseUrl + 'delivery/orders/print_order/' + orderId + '?auto=1', '_blank', 'width=350,height=600');
                    }

                    // WhatsApp: automatico ou manual
                    if (response.auto_notified) {
                        toastr.success('Notificacao WhatsApp enviada automaticamente!');
                    } else if (response.whatsapp_link) {
                        window.open(response.whatsapp_link, '_blank');
                    }
                } else {
                    alert(response.message || 'Erro ao atualizar status');
                    $btn.removeClass('loading');
                    $btn.html('<i class="fa fa-arrow-right"></i> ' + escapeHtml(nextLabelMap[nextStatus] || 'Avancar'));
                }
            },
            error: function() {
                alert('Erro de conexao. Tente novamente.');
                $btn.removeClass('loading');
                $btn.html('<i class="fa fa-arrow-right"></i> ' + escapeHtml(nextLabelMap[nextStatus] || 'Avancar'));
            }
        });
    }

    // -------------------------------------------
    // Update column counts
    // -------------------------------------------
    function updateColumnCounts() {
        var activeTotal = 0;
        $('.kanban-column').each(function() {
            var status = $(this).data('status');
            var count = $(this).find('.kanban-card').length;
            $('#count-' + status).text(count);

            // Check if column is now empty
            var $body = $(this).find('.kanban-column-body');
            if (count === 0 && $body.find('.kanban-empty').length === 0) {
                $body.html('<div class="kanban-empty text-muted text-center py-4">' +
                    '<i class="fa fa-inbox fa-2x"></i><p class="mt-2">Nenhum pedido</p></div>');
            }

            if (status !== 'entregue') {
                activeTotal += count;
            }
        });
        updatePageTitle(activeTotal);
    }

    // -------------------------------------------
    // AJAX: Toggle store pause
    // -------------------------------------------
    function togglePause() {
        var postData = {};
        postData[csrfName] = csrfHash;

        $.ajax({
            url: baseUrl + 'delivery/config/toggle_pause',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                    $('#csrf_token').val(csrfHash);
                }
                if (response.success) {
                    var isPaused = response.loja_pausada;
                    var $btn = $('#btn-toggle-pause');

                    if (isPaused) {
                        $btn.removeClass('btn-danger').addClass('btn-success');
                        $btn.html('<i class="fa fa-play"></i> Ativar Loja');
                        if ($('#alert-loja-pausada').length === 0) {
                            $('.row.mb-3').before(
                                '<div class="alert alert-danger alert-dismissible" id="alert-loja-pausada">' +
                                '<i class="fa fa-pause-circle fa-lg"></i> ' +
                                '<strong>Loja Pausada</strong> - nao esta recebendo pedidos</div>'
                            );
                        }
                    } else {
                        $btn.removeClass('btn-success').addClass('btn-danger');
                        $btn.html('<i class="fa fa-pause"></i> Pausar Loja');
                        $('#alert-loja-pausada').remove();
                    }
                } else {
                    alert(response.message || 'Erro ao alterar status da loja');
                }
            },
            error: function() {
                alert('Erro de conexao. Tente novamente.');
            }
        });
    }

    // -------------------------------------------
    // Polling: Check for new / updated orders
    // -------------------------------------------
    function pollNewOrders() {
        $('#polling-spinner').show();

        $.ajax({
            url: baseUrl + 'delivery/orders/api_novos',
            type: 'GET',
            data: { desde: lastTimestamp },
            dataType: 'json',
            success: function(response) {
                $('#polling-spinner').hide();
                if (!response.success) return;

                // Keep CSRF token fresh (fixes multi-tab invalidation)
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                    $('#csrf_token').val(csrfHash);
                }

                var now = new Date();
                var timeStr = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);
                $('#last-update-label small').text('Atualizado ' + timeStr);

                // Check if counts changed -- if so, do a full page refresh for simplicity
                // For new pendente orders, show notification
                if (response.counts) {
                    var newPendenteCount = parseInt(response.counts.pendente) || 0;
                    if (newPendenteCount > previousPendenteCount) {
                        var diff = newPendenteCount - previousPendenteCount;
                        playBeep();
                        showBrowserNotification(
                            'Novo pedido!',
                            diff + (diff === 1 ? ' novo pedido recebido' : ' novos pedidos recebidos')
                        );
                    }
                    previousPendenteCount = newPendenteCount;
                }

                // If there are updated orders, reload the page to refresh the board
                if (response.orders && response.orders.length > 0) {
                    lastTimestamp = response.timestamp || lastTimestamp;
                    // Full reload to get fresh data with items
                    location.reload();
                } else {
                    lastTimestamp = response.timestamp || lastTimestamp;
                }

                // Update counts from server
                if (response.counts) {
                    var activeCnt = 0;
                    var statuses = ['pendente', 'confirmado', 'preparando', 'pronto_coleta', 'saiu_entrega', 'entregue'];
                    for (var i = 0; i < statuses.length; i++) {
                        var s = statuses[i];
                        var cnt = parseInt(response.counts[s]) || 0;
                        $('#count-' + s).text(cnt);
                        if (s !== 'entregue') {
                            activeCnt += cnt;
                        }
                    }
                    updatePageTitle(activeCnt);
                }
            },
            error: function() {
                $('#polling-spinner').hide();
                $('#last-update-label small').text('Erro ao atualizar');
            }
        });
    }

    // -------------------------------------------
    // Sound toggle
    // -------------------------------------------
    function toggleSound() {
        soundEnabled = !soundEnabled;
        var $icon = $('#sound-icon');
        if (soundEnabled) {
            $icon.removeClass('fa-volume-off').addClass('fa-volume-up');
            // Init audio context on user gesture
            if (!audioContext) initAudio();
        } else {
            $icon.removeClass('fa-volume-up').addClass('fa-volume-off');
        }
    }

    // -------------------------------------------
    // Event bindings
    // -------------------------------------------
    $(document).ready(function() {
        // Request browser notification permission
        requestNotificationPermission();

        // Initialize audio context on first user interaction
        $(document).one('click', function() {
            if (!audioContext) initAudio();
        });

        // Set initial page title
        updatePageTitle(<?php echo (int)$active_count; ?>);

        // Advance status button (delegated for dynamic cards)
        $(document).on('click', '.btn-avancar', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var orderId = parseInt($btn.data('order-id'));
            var nextStatus = $btn.data('next-status');
            if (orderId && nextStatus) {
                advanceStatus(orderId, nextStatus, $btn);
            }
        });

        // Enviar cupom via WhatsApp
        $(document).on('click', '.btn-enviar-cupom', function(e) {
            e.preventDefault();
            var orderId = parseInt($(this).data('order-id'));
            $.ajax({
                url: baseUrl + 'delivery/orders/enviar_cupom/' + orderId,
                type: 'GET',
                dataType: 'json',
                success: function(r) {
                    if (r.success && r.whatsapp_link) {
                        window.open(r.whatsapp_link, '_blank');
                    } else {
                        alert(r.message || 'Erro ao gerar cupom');
                    }
                }
            });
        });

        // Toggle store pause
        $('#btn-toggle-pause').on('click', function(e) {
            e.preventDefault();
            togglePause();
        });

        // Toggle sound
        $('#btn-toggle-sound').on('click', function(e) {
            e.preventDefault();
            toggleSound();
        });

        // Start polling
        setInterval(pollNewOrders, pollInterval);
    });

})();
</script>
