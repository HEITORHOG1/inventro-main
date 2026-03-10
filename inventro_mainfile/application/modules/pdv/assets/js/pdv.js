/**
 * PDV.js — Lógica principal da Frente de Caixa Inventro
 *
 * Máquina de estados: idle → venda → pagamento → finalizado → idle
 * Gerencia: carrinho, barcode scanner, teclado, modais, sons, localStorage recovery
 */
(function($) {
'use strict';

// =============================================================================
// ESTADO GLOBAL
// =============================================================================

var state        = 'idle';       // idle | venda | consulta | pagamento | finalizado
var itensCarrinho = [];          // Array de itens da venda em curso
var itemSelecionado = -1;        // Índice do item com foco no espelho (-1 = nenhum)
var cpfCliente    = '';           // CPF informado para a nota
var sequencia     = 0;           // Sequência de itens (não reseta ao remover)
var barcodeBuffer = '';           // Buffer do scanner de barcode
var barcodeTimer  = null;         // Timer de 300ms para scanner USB
var csrfHash      = PDV_CONFIG.csrf_hash;
var modalAberto   = null;         // ID do modal aberto (ou null)
var categoriasCarregadas = false;
var pgtoFormaAtual  = null;    // Forma de pagamento selecionada ('dinheiro', 'debito', etc.)
var pgtoModo        = null;    // 'unico' ou 'misto'
var pgtoParciais    = [];      // Array de {forma, valor, troco} para modo misto
var pgtoLockOwner   = null;    // Lock owner do Redis (retornado por validar_estoque)
var pgtoFinalizando = false;   // Flag para evitar duplo-clique
var bipandoEmAndamento = false; // Flag para evitar biparProduto concorrente

// Fase 9: Devolução / Troca
var creditoDevolucao    = 0;        // Crédito de devolução para abater na próxima venda
var devolucaoRefId      = null;     // ID da devolução referência (vincular à troca)
var devolucaoVendaData  = null;     // Dados da venda buscada para devolução

// Fase 7: Descontos
var descontoVenda           = 0;       // Valor do desconto na venda (R$)
var descontoVendaTipo       = null;    // 'percentual' ou 'valor'
var descontoVendaValor      = 0;       // Valor informado pelo operador
var descontoAutorizadoPor   = null;    // ID do supervisor que autorizou (ou null)

// Fase 8: Fiado / Crediário
var fiadoClienteSelecionado = null;    // {id, name, mobile, cpf, debito_atual, limite, disponivel, bloqueado}
var fiadoSupervisorId       = null;    // ID do supervisor que autorizou fiado acima do limite

// Constantes
var BARCODE_DELAY = 300;  // ms para acumular chars do scanner USB
var STORAGE_KEY   = 'pdv_venda_' + PDV_CONFIG.terminal_id + '_' + PDV_CONFIG.caixa_id;
var STORAGE_TTL   = 8 * 60 * 60 * 1000; // 8 horas

// =============================================================================
// TOAST NOTIFICATIONS
// =============================================================================

/**
 * Exibe uma notificação toast no canto superior direito.
 *
 * @param {string} message  - Texto da mensagem
 * @param {string} [type]   - 'success' | 'error' | 'warning' | 'info' (padrão: 'info')
 * @param {number} [duration] - Tempo em ms antes de auto-fechar (padrão: 4000)
 */
function pdvToast(message, type, duration) {
    type = type || 'info';
    duration = duration || 4000;

    // Criar container se não existir
    var $container = $('.pdv-toast-container');
    if ($container.length === 0) {
        $container = $('<div class="pdv-toast-container"></div>').appendTo('body');
    }

    // Ícone conforme tipo
    var icons = {
        success: 'fas fa-check-circle',
        error:   'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info:    'fas fa-info-circle'
    };
    var iconClass = icons[type] || icons.info;

    // Montar toast
    var $toast = $(
        '<div class="pdv-toast pdv-toast-' + type + '">' +
            '<i class="pdv-toast-icon ' + iconClass + '"></i>' +
            '<span class="pdv-toast-message"></span>' +
            '<button type="button" class="pdv-toast-close">&times;</button>' +
        '</div>'
    );
    $toast.find('.pdv-toast-message').text(message);

    // Botão fechar
    $toast.find('.pdv-toast-close').on('click', function() {
        removeToast($toast);
    });

    $container.append($toast);

    // Som conforme tipo
    if (type === 'error') {
        try { somErro(); } catch(e) {}
    } else if (type === 'success') {
        try { somSucesso(); } catch(e) {}
    }

    // Auto-dismiss
    var timer = setTimeout(function() {
        removeToast($toast);
    }, duration);

    $toast.data('timer', timer);

    function removeToast($el) {
        if ($el.hasClass('pdv-toast-removing')) return;
        clearTimeout($el.data('timer'));
        $el.addClass('pdv-toast-removing');
        setTimeout(function() { $el.remove(); }, 300);
    }
}

/**
 * Modal de confirmação estilizado (substitui window.confirm)
 *
 * @param {string}   message     - Texto da pergunta
 * @param {function} onConfirm   - Callback se confirmar
 * @param {object}   [opts]      - { cancelText, confirmText, danger, onCancel }
 */
function pdvConfirm(message, onConfirm, opts) {
    opts = opts || {};
    var confirmText = opts.confirmText || 'Confirmar';
    var cancelText  = opts.cancelText  || 'Cancelar';
    var dangerClass = opts.danger ? ' pdv-confirm-danger' : '';

    // Remover modal anterior se existir
    $('.pdv-confirm-overlay').remove();

    var $overlay = $(
        '<div class="pdv-confirm-overlay">' +
            '<div class="pdv-confirm-box' + dangerClass + '">' +
                '<div class="pdv-confirm-icon"><i class="fas ' + (opts.danger ? 'fa-exclamation-triangle' : 'fa-question-circle') + '"></i></div>' +
                '<div class="pdv-confirm-msg"></div>' +
                '<div class="pdv-confirm-btns">' +
                    '<button type="button" class="pdv-confirm-cancel"></button>' +
                    '<button type="button" class="pdv-confirm-ok"></button>' +
                '</div>' +
            '</div>' +
        '</div>'
    );

    $overlay.find('.pdv-confirm-msg').text(message);
    $overlay.find('.pdv-confirm-cancel').text(cancelText);
    $overlay.find('.pdv-confirm-ok').text(confirmText);

    // Guard: ignorar Enter nos primeiros 600ms (evita confirm acidental por scanner/teclado stray)
    var ready = false;
    setTimeout(function() { ready = true; }, 600);

    function fechar() {
        $overlay.addClass('pdv-confirm-fadeout');
        setTimeout(function() { $overlay.remove(); }, 200);
    }

    $overlay.find('.pdv-confirm-cancel').on('click', function() {
        fechar();
        if (typeof opts.onCancel === 'function') opts.onCancel();
    });

    $overlay.find('.pdv-confirm-ok').on('click', function() {
        if (!ready) return;
        fechar();
        if (typeof onConfirm === 'function') onConfirm();
    });

    // ESC para cancelar, Enter aciona o botão focado (não sempre confirmar)
    $overlay.on('keydown', function(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            fechar();
            if (typeof opts.onCancel === 'function') opts.onCancel();
        } else if (e.key === 'Enter' && ready) {
            e.preventDefault();
            e.stopPropagation();
            // Respeitar o botão focado: se "Cancelar" está focado, cancelar
            if (document.activeElement && $(document.activeElement).hasClass('pdv-confirm-cancel')) {
                fechar();
                if (typeof opts.onCancel === 'function') opts.onCancel();
            } else {
                fechar();
                if (typeof onConfirm === 'function') onConfirm();
            }
        }
    });

    $('body').append($overlay);
    // Focar botão cancelar por padrão (mais seguro — Enter = Cancelar por padrão)
    $overlay.find('.pdv-confirm-cancel').trigger('focus');
}

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

$(function() {
    verificarVendaPendente();
    iniciarRelogio();

    // Mostrar estado inicial (focarBarcodeGlobal é chamado dentro de mudarEstado)
    mudarEstado('idle');

    // Re-focar campo barcode quando a janela recupera o foco (ex: após popup do cupom)
    $(window).on('focus', function() {
        if (state === 'idle' || state === 'finalizado') {
            focarBarcodeGlobal();
        } else if (state === 'venda') {
            focarBarcode();
        }
    });

    // Click em qualquer lugar da tela no idle → focar barcode
    $(document).on('click', '#pdv-container', function(e) {
        if (state === 'idle' && !$(e.target).closest('button, a, input, select, .pdv-modal-overlay').length) {
            focarBarcodeGlobal();
        }
    });
});

// =============================================================================
// MÁQUINA DE ESTADOS
// =============================================================================

function mudarEstado(novoEstado) {
    state = novoEstado;
    var $container = $('#pdv-container');
    $container.attr('data-state', state);

    // Esconde todos os estados, mostra o ativo
    $('.pdv-state').addClass('pdv-hidden');
    $('[data-show-state="' + state + '"]').removeClass('pdv-hidden');

    // Limpar campos de barcode ao mudar de estado (evita dados residuais)
    if (state === 'idle') {
        $('#pdv-barcode-global').val('');
        $('#pdv-barcode').val('');
    }

    // Foco conforme estado
    if (state === 'idle' || state === 'consulta' || state === 'finalizado') {
        focarBarcodeGlobal();
    } else if (state === 'venda') {
        focarBarcode();
    }
}

// =============================================================================
// BARCODE SCANNER
// =============================================================================

// Campo global invisível (idle + consulta + finalizado)
$(document).on('input', '#pdv-barcode-global', function() {
    // Ignorar input se estiver em estado que não usa barcode-global
    if (state !== 'idle' && state !== 'consulta' && state !== 'finalizado') {
        $(this).val('');
        return;
    }
    var val = $(this).val().trim();
    if (val.length > 0) {
        clearTimeout(barcodeTimer);
        barcodeTimer = setTimeout(function() {
            var codigo = $('#pdv-barcode-global').val().trim();
            $('#pdv-barcode-global').val('');
            // Mínimo 3 caracteres — scanner envia barcodes completos (8-13 dígitos)
            // Isso evita que teclas acidentais (ex: "1", "2") disparem busca
            if (codigo.length >= 3) {
                processarBarcodeLido(codigo);
            }
        }, BARCODE_DELAY);
    }
});

// Enter no campo global (idle + consulta + finalizado) — digitação manual
$(document).on('keydown', '#pdv-barcode-global', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation(); // Evitar que o handler global também processe este Enter
        clearTimeout(barcodeTimer);
        barcodeTimer = null;
        var codigo = $(this).val().trim();
        $(this).val('');
        if (codigo.length > 0) {
            processarBarcodeLido(codigo);
        } else if (state === 'idle') {
            // Enter vazio no idle → abrir nova venda vazia
            abrirNovaVendaVazia();
        }
    }
});

// Campo de barcode na tela de venda
$(document).on('keydown', '#pdv-barcode', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation(); // Evitar que o handler global também processe
        var codigo = $(this).val().trim();
        $(this).val('');
        if (codigo.length > 0) {
            biparProduto(codigo);
        }
    }
});

// Processar barcode lido em qualquer estado
function processarBarcodeLido(codigo) {
    if (state === 'idle') {
        // Primeiro item → transiciona para venda
        biparProduto(codigo);
    } else if (state === 'finalizado') {
        // Scanner durante tela "Obrigado" → voltar para idle e iniciar nova venda
        bipandoEmAndamento = false; // Reset flag antes de nova venda
        voltarParaIdle();
        biparProduto(codigo);
    } else if (state === 'consulta') {
        consultarPreco(codigo);
    }
    // estados 'venda' e 'pagamento': ignorar (barcode de venda vai pelo #pdv-barcode)
}

function focarBarcode() {
    setTimeout(function() { $('#pdv-barcode').trigger('focus'); }, 50);
}

function focarBarcodeGlobal() {
    setTimeout(function() {
        // Não roubar foco se um overlay/modal/confirm está aberto
        if ($('.pdv-confirm-overlay').length || $('.pdv-modal-overlay:visible').length) {
            return;
        }
        var $bg = $('#pdv-barcode-global');
        if ($bg.length) {
            $bg.val(''); // Limpar qualquer valor residual antes de focar
            $bg.trigger('focus');
            // Fallback: se o popup de cupom roubou o foco, re-focar
            setTimeout(function() {
                if ($('.pdv-confirm-overlay').length) return;
                if (document.activeElement !== $bg[0] && (state === 'idle' || state === 'finalizado')) {
                    $bg.trigger('focus');
                }
            }, 200);
        }
    }, 50);
}

// =============================================================================
// BUSCA E ADIÇÃO DE PRODUTO
// =============================================================================

function biparProduto(codigo) {
    // Guard: evitar chamadas concorrentes (scanner duplo, timer + Enter)
    if (bipandoEmAndamento) return;
    bipandoEmAndamento = true;

    var quantidade = parseFloat($('#pdv-quantidade').val()) || 1;

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/buscar_produto',
        type: 'GET',
        dataType: 'json',
        data: { codigo: codigo },
        timeout: 5000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.found) {
                // Se veio quantidade da balança, usa ela
                if (r.barcode_balanca && r.quantidade_balanca) {
                    quantidade = r.quantidade_balanca;
                }

                // Validação de estoque (Fase 4)
                var estoqueResult = verificarEstoqueProduto(r, quantidade);
                if (estoqueResult.bloqueado) {
                    somErro();
                    mostrarUltimoItem('<i class="fas fa-ban"></i> ' + escapeHtml(estoqueResult.mensagem));
                    focarBarcode();
                    return;
                }

                adicionarItem({
                    product_id:       r.id,
                    nome:             r.nome,
                    preco:            r.preco,
                    quantidade:       quantidade,
                    product_code:     r.product_code,
                    ean:              r.ean,
                    unidade:          r.unidade,
                    estoque:          r.estoque_disponivel,
                    estoque_minimo:   r.estoque_minimo || 0,
                    estoque_baixo:    r.estoque_baixo || false,
                    sem_estoque:      r.sem_estoque || false,
                    pesavel:          r.pesavel,
                    generico:         false,
                    descricao_manual: null
                });

                // Alerta visual de estoque (não bloqueante)
                if (estoqueResult.alerta) {
                    mostrarUltimoItem(estoqueResult.alertaHtml);
                }

                // Reset quantidade para 1
                $('#pdv-quantidade').val('1');
                somSucesso();
            } else {
                somErro();
                mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(r.message || 'Produto não encontrado'));
            }
            focarBarcode();
        },
        error: function() {
            somErro();
            mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> Erro de conexão');
            focarBarcode();
        },
        complete: function() {
            bipandoEmAndamento = false;
        }
    });
}

function adicionarItem(item) {
    // Transicionar para venda se estiver idle
    if (state === 'idle') {
        // Limpar flag de venda finalizada (nova venda começando)
        try { localStorage.removeItem(STORAGE_KEY + '_done'); } catch(ex) {}
        // Limpar timer e campo global para evitar disparo duplicado de biparProduto
        clearTimeout(barcodeTimer);
        barcodeTimer = null;
        $('#pdv-barcode-global').val('');
        mudarEstado('venda');
    }

    // Verificar se produto já está no carrinho (não genérico, não pesável)
    if (item.product_id && !item.pesavel && !item.generico) {
        for (var i = 0; i < itensCarrinho.length; i++) {
            if (itensCarrinho[i].product_id === item.product_id && !itensCarrinho[i].generico) {
                itensCarrinho[i].quantidade += item.quantidade;
                var brutoi = round2(itensCarrinho[i].quantidade * itensCarrinho[i].preco);
                // Fase 7: recalcular desconto se existia
                if (itensCarrinho[i].desconto_tipo && itensCarrinho[i].desconto_valor > 0) {
                    if (itensCarrinho[i].desconto_tipo === 'percentual') {
                        itensCarrinho[i].desconto_calculado = round2(brutoi * (itensCarrinho[i].desconto_valor / 100));
                    } else {
                        itensCarrinho[i].desconto_calculado = round2(Math.min(itensCarrinho[i].desconto_valor, brutoi));
                    }
                    itensCarrinho[i].subtotal = round2(brutoi - itensCarrinho[i].desconto_calculado);
                } else {
                    itensCarrinho[i].subtotal = brutoi;
                }
                itemSelecionado = i;
                atualizarEspelho();
                atualizarTotais();
                salvarVendaLocal();
                return;
            }
        }
    }

    sequencia++;
    item.seq = sequencia;
    item.subtotal = round2(item.quantidade * item.preco);
    // Fase 7: Inicializar campos de desconto
    item.desconto_tipo      = null;
    item.desconto_valor     = 0;
    item.desconto_calculado = 0;
    item.desconto_rateio    = 0;

    itensCarrinho.push(item);
    itemSelecionado = itensCarrinho.length - 1;

    atualizarEspelho();
    atualizarTotais();
    salvarVendaLocal();
}

// =============================================================================
// CONTROLE DE ESTOQUE (Fase 4)
// =============================================================================

/**
 * Verifica estoque de um produto ao bipar
 *
 * @param {object} r      Resposta do servidor (buscar_produto)
 * @param {number} qtdAdd Quantidade a adicionar
 * @returns {object} {bloqueado, alerta, mensagem, alertaHtml}
 */
function verificarEstoqueProduto(r, qtdAdd) {
    var resultado = { bloqueado: false, alerta: false, mensagem: '', alertaHtml: '' };

    // Produto genérico ou sem product_id — sem controle de estoque
    if (!r.id) return resultado;

    var estoqueDisp = r.estoque_disponivel || 0;
    var estoqueMin  = r.estoque_minimo || 0;

    // Calcular total que ficará no carrinho (pode já ter esse produto)
    var qtdNoCarrinho = 0;
    for (var i = 0; i < itensCarrinho.length; i++) {
        if (itensCarrinho[i].product_id === r.id && !itensCarrinho[i].generico) {
            qtdNoCarrinho += itensCarrinho[i].quantidade;
        }
    }
    var qtdTotal = qtdNoCarrinho + qtdAdd;

    // Estoque zerado
    if (estoqueDisp <= 0) {
        if (!PDV_CONFIG.permitir_venda_sem_estoque) {
            resultado.bloqueado = true;
            resultado.mensagem = 'SEM ESTOQUE — "' + r.nome + '" (estoque: 0)';
            return resultado;
        }
        // Permitido mas com alerta vermelho
        resultado.alerta = true;
        resultado.alertaHtml = '<i class="fas fa-exclamation-circle" style="color:#f87171"></i> <strong style="color:#f87171">SEM ESTOQUE</strong> — ' + escapeHtml(r.nome) + ' (venda permitida)';
        return resultado;
    }

    // Quantidade pedida > estoque disponível
    if (qtdTotal > estoqueDisp) {
        if (!PDV_CONFIG.permitir_venda_sem_estoque) {
            resultado.bloqueado = true;
            resultado.mensagem = 'Estoque insuficiente — "' + r.nome + '" (disponível: ' + estoqueDisp + ', pedido: ' + qtdTotal + ')';
            return resultado;
        }
        resultado.alerta = true;
        resultado.alertaHtml = '<i class="fas fa-exclamation-circle" style="color:#f87171"></i> <strong style="color:#f87171">Estoque insuficiente</strong> — ' + escapeHtml(r.nome) + ' (disp: ' + estoqueDisp + ', pedido: ' + qtdTotal + ')';
        return resultado;
    }

    // Estoque baixo (abaixo do mínimo)
    if (estoqueMin > 0 && estoqueDisp <= estoqueMin) {
        resultado.alerta = true;
        resultado.alertaHtml = '<i class="fas fa-exclamation-triangle" style="color:#fbbf24"></i> <strong style="color:#fbbf24">Estoque baixo!</strong> — ' + escapeHtml(r.nome) + ' (restam ' + estoqueDisp + ')';
    }

    return resultado;
}

/**
 * Atualiza o painel informativo de estoque do item selecionado
 */
function atualizarEstoqueInfo() {
    var $info = $('#pdv-estoque-info');

    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) {
        $info.addClass('pdv-hidden');
        return;
    }

    var item = itensCarrinho[itemSelecionado];

    // Genérico — sem controle de estoque
    if (item.generico || !item.product_id) {
        $info.addClass('pdv-hidden');
        return;
    }

    var estoque = item.estoque || 0;
    var unidade = item.unidade || 'UN';
    var minimo  = item.estoque_minimo || 0;

    $info.removeClass('pdv-hidden pdv-estoque-ok pdv-estoque-baixo pdv-estoque-zero pdv-estoque-zero-bloqueado');

    if (estoque <= 0) {
        if (PDV_CONFIG.permitir_venda_sem_estoque) {
            $info.addClass('pdv-estoque-zero');
            $('#pdv-estoque-texto').text('SEM ESTOQUE (venda permitida)');
        } else {
            $info.addClass('pdv-estoque-zero-bloqueado');
            $('#pdv-estoque-texto').text('SEM ESTOQUE — BLOQUEADO');
        }
    } else if (minimo > 0 && estoque <= minimo) {
        $info.addClass('pdv-estoque-baixo');
        $('#pdv-estoque-texto').text('Estoque baixo: ' + estoque + ' ' + unidade);
    } else {
        $info.addClass('pdv-estoque-ok');
        $('#pdv-estoque-texto').text('Estoque: ' + estoque + ' ' + unidade);
    }
}

// =============================================================================
// ESPELHO DO CUPOM
// =============================================================================

function atualizarEspelho() {
    var $container = $('#pdv-cupom-itens');
    $container.empty();

    for (var i = 0; i < itensCarrinho.length; i++) {
        var item = itensCarrinho[i];
        var selecionado = (i === itemSelecionado) ? ' pdv-item-selecionado' : '';
        var nomeExibir = item.generico ? 'GEN - ' + escapeHtml(item.descricao_manual) : escapeHtml(item.nome);

        var $row = $('<div>')
            .addClass('pdv-cupom-item' + selecionado)
            .attr('data-index', i);

        var $seq = $('<span>').addClass('pdv-item-seq').text(String(item.seq).padStart(3, '0'));

        var $nome = $('<span>').addClass('pdv-item-nome');
        if (item.generico) {
            $nome.text('GEN - ' + item.descricao_manual);
        } else {
            $nome.text(item.nome);
        }

        // Código de barras (EAN ou product_code)
        var codigoExibir = item.ean || item.product_code || '';
        var $codigo = $('<span>').addClass('pdv-item-codigo');
        if (codigoExibir) {
            $codigo.text(codigoExibir);
        }

        var $detalhe = $('<span>').addClass('pdv-item-detalhe')
            .text(formatarQtd(item.quantidade) + ' ' + (item.unidade || 'UN') +
                  ' x R$ ' + formatarMoeda(item.preco));

        var $subtotal = $('<span>').addClass('pdv-item-subtotal')
            .text('R$ ' + formatarMoeda(item.subtotal));

        $row.append($seq, $nome, $codigo, $detalhe, $subtotal);
        $container.append($row);

        // Fase 7: Mostrar desconto abaixo do item (se houver)
        renderizarDescontoEspelhoItem($container, item);
    }

    // Scroll automático para o último item
    $container.scrollTop($container[0].scrollHeight);

    // Atualizar contagem
    $('#pdv-cupom-count').text(itensCarrinho.length + ' ' + (itensCarrinho.length === 1 ? 'item' : 'itens'));

    // Mostrar último item adicionado na barra
    if (itensCarrinho.length > 0) {
        var ultimo = itensCarrinho[itensCarrinho.length - 1];
        var nomeU = ultimo.generico ? 'GEN - ' + ultimo.descricao_manual : ultimo.nome;
        var codigoU = ultimo.ean || ultimo.product_code || '';
        var codigoHtml = codigoU ? '<span class="pdv-ultimo-codigo">' + escapeHtml(codigoU) + '</span>' : '';
        mostrarUltimoItem(
            '<strong>' + escapeHtml(nomeU) + '</strong> — ' +
            formatarQtd(ultimo.quantidade) + ' x R$ ' + formatarMoeda(ultimo.preco) +
            ' = <strong>R$ ' + formatarMoeda(ultimo.subtotal) + '</strong>' +
            codigoHtml
        );
    }

    // Atualizar valor unitário e total item do campo selecionado
    if (itemSelecionado >= 0 && itemSelecionado < itensCarrinho.length) {
        var sel = itensCarrinho[itemSelecionado];
        $('#pdv-valor-unit').text(formatarMoeda(sel.preco));
        var selSubtotal = sel.subtotal;
        var selDescRateio = sel.desconto_rateio || 0;
        if (selDescRateio > 0) {
            selSubtotal = round2(selSubtotal - selDescRateio);
        }
        $('#pdv-total-item-valor').text('R$ ' + formatarMoeda(selSubtotal));
    }

    // Atualizar painel de estoque (Fase 4)
    atualizarEstoqueInfo();
}

function atualizarTotais() {
    var subtotalBruto = 0;
    var descontoItens = 0;
    var descontoRateio = 0;
    for (var i = 0; i < itensCarrinho.length; i++) {
        var it = itensCarrinho[i];
        subtotalBruto += round2(it.preco * it.quantidade);
        descontoItens += (it.desconto_calculado || 0);
        descontoRateio += (it.desconto_rateio || 0);
    }
    var descontoTotal = round2(descontoItens + descontoRateio + (descontoVenda || 0));
    var total = round2(subtotalBruto - descontoItens - descontoVenda);
    $('#pdv-total-valor').text(formatarMoeda(total));

    // Mostrar desconto na barra total
    if (descontoTotal > 0) {
        $('#pdv-total-desconto').removeClass('pdv-hidden');
        $('#pdv-total-desconto-valor').text(formatarMoeda(descontoTotal));
    } else {
        $('#pdv-total-desconto').addClass('pdv-hidden');
    }
}

function mostrarUltimoItem(html) {
    $('#pdv-ultimo-item').html(html);
}

// Clique no item do espelho para selecionar
$(document).on('click', '.pdv-cupom-item', function() {
    itemSelecionado = parseInt($(this).attr('data-index'));
    atualizarEspelho();
});

// =============================================================================
// NAVEGAÇÃO COM SETAS
// =============================================================================

function navegarCima() {
    if (itensCarrinho.length === 0) return;
    if (itemSelecionado > 0) {
        itemSelecionado--;
    } else {
        itemSelecionado = itensCarrinho.length - 1;
    }
    atualizarEspelho();
}

function navegarBaixo() {
    if (itensCarrinho.length === 0) return;
    if (itemSelecionado < itensCarrinho.length - 1) {
        itemSelecionado++;
    } else {
        itemSelecionado = 0;
    }
    atualizarEspelho();
}

// =============================================================================
// CANCELAR ITEM
// =============================================================================

function cancelarUltimoItem() {
    if (itensCarrinho.length === 0) return;
    var removido = itensCarrinho.pop();
    itemSelecionado = itensCarrinho.length - 1;
    somSucesso();
    mostrarUltimoItem('<i class="fas fa-trash"></i> Removido: ' + escapeHtml(removido.generico ? 'GEN - ' + removido.descricao_manual : removido.nome));

    if (itensCarrinho.length === 0) {
        limparVenda();
        return;
    }

    atualizarEspelho();
    atualizarTotais();
    salvarVendaLocal();
}

function cancelarItemSelecionado() {
    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) return;

    var removido = itensCarrinho.splice(itemSelecionado, 1)[0];
    somSucesso();
    mostrarUltimoItem('<i class="fas fa-trash"></i> Removido: ' + escapeHtml(removido.generico ? 'GEN - ' + removido.descricao_manual : removido.nome));

    if (itensCarrinho.length === 0) {
        limparVenda();
        return;
    }

    if (itemSelecionado >= itensCarrinho.length) {
        itemSelecionado = itensCarrinho.length - 1;
    }

    atualizarEspelho();
    atualizarTotais();
    salvarVendaLocal();
}

function cancelarVenda() {
    if (itensCarrinho.length === 0) {
        mudarEstado('idle');
        return;
    }
    pdvConfirm('Cancelar toda a venda? (' + itensCarrinho.length + ' itens)', function() {
        limparVenda();
        somSucesso();
    }, { danger: true, confirmText: 'Sim, cancelar' });
}

function limparVenda() {
    // Limpar timer e campos de barcode
    clearTimeout(barcodeTimer);
    barcodeTimer = null;
    bipandoEmAndamento = false;
    $('#pdv-barcode-global').val('');
    $('#pdv-barcode').val('');
    itensCarrinho = [];
    itemSelecionado = -1;
    cpfCliente = '';
    sequencia = 0;
    // Fase 7: reset descontos
    descontoVenda = 0;
    descontoVendaTipo = null;
    descontoVendaValor = 0;
    descontoAutorizadoPor = null;
    $('#pdv-quantidade').val('1');
    $('#pdv-valor-unit').text('0,00');
    $('#pdv-total-item-valor').text('R$ 0,00');
    $('#pdv-total-valor').text('0,00');
    $('#pdv-total-desconto').addClass('pdv-hidden');
    $('#pdv-cupom-itens').empty();
    $('#pdv-cupom-count').text('0 itens');
    mostrarUltimoItem('Nenhum item adicionado');
    localStorage.removeItem(STORAGE_KEY);
    mudarEstado('idle');
}

// =============================================================================
// EDITAR QUANTIDADE (Q)
// =============================================================================

function abrirEditarQuantidade() {
    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) return;
    var item = itensCarrinho[itemSelecionado];
    var nome = item.generico ? 'GEN - ' + item.descricao_manual : item.nome;
    $('#editar-qtd-produto').text(nome);
    $('#editar-qtd-input').val(item.quantidade);
    abrirModal('modal-editar-qtd');
    setTimeout(function() { $('#editar-qtd-input').trigger('focus').trigger('select'); }, 100);
}

$(document).on('click', '#btn-confirmar-qtd', confirmarQuantidade);
$(document).on('keydown', '#editar-qtd-input', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); confirmarQuantidade(); }
});

function confirmarQuantidade() {
    var novaQtd = parseFloat($('#editar-qtd-input').val());
    if (isNaN(novaQtd) || novaQtd < 0) return;

    if (novaQtd === 0) {
        // Quantidade 0 = cancelar item
        fecharModal();
        cancelarItemSelecionado();
        return;
    }

    var item = itensCarrinho[itemSelecionado];

    // Validação de estoque ao aumentar quantidade (Fase 4)
    if (item.product_id && !item.generico && novaQtd > item.quantidade) {
        var estoque = item.estoque || 0;
        // Calcular total deste produto no carrinho (excluindo item atual)
        var qtdOutros = 0;
        for (var i = 0; i < itensCarrinho.length; i++) {
            if (i !== itemSelecionado && itensCarrinho[i].product_id === item.product_id && !itensCarrinho[i].generico) {
                qtdOutros += itensCarrinho[i].quantidade;
            }
        }
        var qtdTotal = qtdOutros + novaQtd;

        if (qtdTotal > estoque && !PDV_CONFIG.permitir_venda_sem_estoque) {
            pdvToast('Estoque insuficiente — disponível: ' + estoque + ', pedido total: ' + qtdTotal, 'error');
            return;
        }
    }

    item.quantidade = novaQtd;
    var brutoQ = round2(novaQtd * item.preco);
    // Fase 7: recalcular desconto se existia
    if (item.desconto_tipo && item.desconto_valor > 0) {
        if (item.desconto_tipo === 'percentual') {
            item.desconto_calculado = round2(brutoQ * (item.desconto_valor / 100));
        } else {
            item.desconto_calculado = round2(Math.min(item.desconto_valor, brutoQ));
        }
        item.subtotal = round2(brutoQ - item.desconto_calculado);
    } else {
        item.subtotal = brutoQ;
    }
    fecharModal();
    atualizarEspelho();
    atualizarTotais();
    salvarVendaLocal();
    somSucesso();
}

// =============================================================================
// CONSULTA DE PREÇO (C)
// =============================================================================

function ativarConsulta() {
    mudarEstado('consulta');
    $('#pdv-consulta-body').html('<p class="pdv-consulta-instrucao">Bipe ou digite o código do produto</p>');
}

function consultarPreco(codigo) {
    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/consultar_preco',
        type: 'GET',
        dataType: 'json',
        data: { codigo: codigo },
        success: function(r) {
            atualizarCsrf(r);
            if (r.found) {
                $('#pdv-consulta-body').html(
                    '<div class="pdv-consulta-produto">' +
                    '<h2>' + escapeHtml(r.nome) + '</h2>' +
                    '<div class="pdv-consulta-preco">R$ ' + formatarMoeda(r.preco) + '</div>' +
                    '<div class="pdv-consulta-detalhes">' +
                    '<span>Código: ' + escapeHtml(r.product_code || '-') + '</span>' +
                    '<span>EAN: ' + escapeHtml(r.ean || '-') + '</span>' +
                    '<span>Unidade: ' + escapeHtml(r.unidade) + '</span>' +
                    '<span>Estoque: ' + r.estoque_disponivel + '</span>' +
                    '</div></div>'
                );
                somSucesso();
            } else {
                $('#pdv-consulta-body').html(
                    '<p class="pdv-consulta-instrucao pdv-alert-danger">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(r.message) + '</p>'
                );
                somErro();
            }
        }
    });
}

// =============================================================================
// BUSCA POR NOME (F5)
// =============================================================================

function abrirBuscaNome() {
    abrirModal('modal-busca-nome');
    $('#busca-nome-input').val('').trigger('focus');
    $('#busca-nome-resultados').empty();
}

var buscaTimer = null;
$(document).on('input', '#busca-nome-input', function() {
    var termo = $(this).val().trim();
    clearTimeout(buscaTimer);
    if (termo.length < 2) {
        $('#busca-nome-resultados').empty();
        return;
    }
    buscaTimer = setTimeout(function() {
        $.ajax({
            url: PDV_CONFIG.base_url + 'pdv/buscar_produto_nome',
            type: 'GET',
            dataType: 'json',
            data: { termo: termo },
            success: function(r) {
                atualizarCsrf(r);
                var $res = $('#busca-nome-resultados').empty();
                if (!r.produtos || r.produtos.length === 0) {
                    $res.html('<p class="pdv-text-center">Nenhum produto encontrado</p>');
                    return;
                }
                for (var i = 0; i < r.produtos.length; i++) {
                    var p = r.produtos[i];
                    var $item = $('<div>').addClass('pdv-busca-item')
                        .attr('data-product', JSON.stringify(p));

                    // Estoque visual com cor (Fase 4)
                    var estoqueClass = 'pdv-busca-estoque';
                    var estoqueTexto = 'Est: ' + p.estoque_disponivel;
                    if (p.sem_estoque) {
                        estoqueClass += ' pdv-busca-estoque-zero';
                        estoqueTexto = 'SEM EST.';
                    } else if (p.estoque_baixo) {
                        estoqueClass += ' pdv-busca-estoque-baixo';
                        estoqueTexto = 'Est: ' + p.estoque_disponivel + ' ⚠';
                    }

                    $item.append(
                        $('<span>').addClass('pdv-busca-nome').text(p.nome),
                        $('<span>').addClass('pdv-busca-codigo').text(p.product_code || p.ean || ''),
                        $('<span>').addClass('pdv-busca-preco').text('R$ ' + formatarMoeda(p.preco)),
                        $('<span>').addClass(estoqueClass).text(estoqueTexto)
                    );
                    $res.append($item);
                }
            }
        });
    }, 300);
});

$(document).on('click', '.pdv-busca-item', function() {
    var p = JSON.parse($(this).attr('data-product'));

    // Validação de estoque antes de adicionar (Fase 4)
    var estoqueResult = verificarEstoqueProduto({
        id: p.id,
        nome: p.nome,
        estoque_disponivel: p.estoque_disponivel,
        estoque_minimo: p.estoque_minimo || 0
    }, 1);

    if (estoqueResult.bloqueado) {
        pdvToast(estoqueResult.mensagem, 'error');
        return;
    }

    fecharModal();
    adicionarItem({
        product_id:       p.id,
        nome:             p.nome,
        preco:            p.preco,
        quantidade:       1,
        product_code:     p.product_code,
        ean:              p.ean,
        unidade:          p.unidade,
        estoque:          p.estoque_disponivel,
        estoque_minimo:   p.estoque_minimo || 0,
        pesavel:          p.pesavel,
        generico:         false,
        descricao_manual: null
    });

    if (estoqueResult.alerta) {
        mostrarUltimoItem(estoqueResult.alertaHtml);
    }
    somSucesso();
});

// Navegação por teclado na busca
$(document).on('keydown', '#busca-nome-input', function(e) {
    if (e.key === 'Escape') { fecharModal(); return; }
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        $('.pdv-busca-item:first').trigger('focus');
    }
    if (e.key === 'Enter') {
        e.preventDefault();
        $('.pdv-busca-item:first').trigger('click');
    }
});

// =============================================================================
// PRODUTO GENÉRICO (G)
// =============================================================================

function abrirGenerico() {
    if (!PDV_CONFIG.generico_habilitado) {
        somErro();
        mostrarUltimoItem('<i class="fas fa-ban"></i> Produto genérico desabilitado');
        return;
    }

    // Carregar categorias uma vez
    if (!categoriasCarregadas) {
        $.ajax({
            url: PDV_CONFIG.base_url + 'pdv/get_categorias',
            type: 'GET',
            dataType: 'json',
            async: false,
            success: function(r) {
                atualizarCsrf(r);
                var $sel = $('#generico-categoria').empty().append($('<option>').val('').text('Selecione...'));
                if (r.categorias) {
                    for (var i = 0; i < r.categorias.length; i++) {
                        $sel.append($('<option>').val(r.categorias[i].id).text(r.categorias[i].name));
                    }
                }
                categoriasCarregadas = true;
            }
        });
    }

    $('#generico-descricao').val('');
    $('#generico-preco').val('');
    $('#generico-quantidade').val('1');
    abrirModal('modal-generico');
    setTimeout(function() { $('#generico-categoria').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-generico', confirmarGenerico);

function confirmarGenerico() {
    var categoria  = $('#generico-categoria').val();
    var descricao  = $('#generico-descricao').val().trim();
    var precoRaw   = $('#generico-preco').val().replace(/\./g, '').replace(',', '.');
    var preco      = parseFloat(precoRaw);
    var quantidade = parseInt($('#generico-quantidade').val()) || 1;

    if (!categoria) { pdvToast('Selecione um departamento.', 'warning'); return; }
    if (!descricao) { pdvToast('Informe a descrição.', 'warning'); return; }
    if (isNaN(preco) || preco <= 0) { pdvToast('Informe um preço válido.', 'warning'); return; }

    fecharModal();

    adicionarItem({
        product_id:       null,
        nome:             descricao,
        preco:            preco,
        quantidade:       quantidade,
        product_code:     null,
        ean:              null,
        unidade:          'UN',
        estoque:          999,
        pesavel:          0,
        generico:         true,
        descricao_manual: descricao,
        categoria_id:     categoria
    });
    somSucesso();
}

// =============================================================================
// CPF NA NOTA (F6)
// =============================================================================

function abrirCpf() {
    $('#cpf-input').val(cpfCliente);
    abrirModal('modal-cpf');
    setTimeout(function() { $('#cpf-input').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-cpf', confirmarCpf);
$(document).on('keydown', '#cpf-input', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); confirmarCpf(); }
});

$(document).on('input', '#cpf-input', function() {
    // Máscara simples de CPF
    var v = this.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d{1,3})/, '$1.$2');
    this.value = v;
});

function confirmarCpf() {
    cpfCliente = $('#cpf-input').val().trim();
    fecharModal();
    if (cpfCliente) {
        mostrarUltimoItem('<i class="fas fa-id-card"></i> CPF: ' + escapeHtml(cpfCliente));
    }
}

// =============================================================================
// FASE 7: DESCONTOS
// =============================================================================

/**
 * 7.1 — Desconto por Item (F9)
 */
function abrirDescontoItem() {
    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) {
        somErro();
        mostrarUltimoItem('<i class="fas fa-ban"></i> Selecione um item para aplicar desconto');
        return;
    }

    var item = itensCarrinho[itemSelecionado];
    var nome = item.generico ? 'GEN - ' + item.descricao_manual : item.nome;
    var subtotalBruto = round2(item.preco * item.quantidade);

    // Preencher info do item
    $('#desconto-item-info').html(
        '<strong>' + escapeHtml(nome) + '</strong><br>' +
        formatarQtd(item.quantidade) + ' x R$ ' + formatarMoeda(item.preco) +
        ' = R$ ' + formatarMoeda(subtotalBruto)
    );

    // Reset campos
    $('input[name="desconto_item_tipo"][value="percentual"]').prop('checked', true);
    $('#desconto-item-valor').val('');
    $('#desconto-item-senha').val('').attr('type', 'password');
    $('#desconto-item-supervisor').addClass('pdv-hidden');

    // Preview inicial
    $('#desconto-item-original').text('R$ ' + formatarMoeda(subtotalBruto));
    $('#desconto-item-calc').text('- R$ 0,00');
    $('#desconto-item-novo').text('R$ ' + formatarMoeda(subtotalBruto));

    abrirModal('modal-desconto-item');
    setTimeout(function() { $('#desconto-item-valor').trigger('focus'); }, 100);
}

// Preview em tempo real — Desconto Item
$(document).on('input', '#desconto-item-valor', function() {
    atualizarPreviewDescontoItem();
});
$(document).on('change', 'input[name="desconto_item_tipo"]', function() {
    atualizarPreviewDescontoItem();
});

function atualizarPreviewDescontoItem() {
    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) return;
    var item = itensCarrinho[itemSelecionado];
    var subtotalBruto = round2(item.preco * item.quantidade);
    var tipo = $('input[name="desconto_item_tipo"]:checked').val();
    var raw = $('#desconto-item-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(raw) || 0;

    var preview = calcularDescontoPreview(tipo, valor, subtotalBruto);

    $('#desconto-item-original').text('R$ ' + formatarMoeda(subtotalBruto));
    $('#desconto-item-calc').text('- R$ ' + formatarMoeda(preview.desconto));
    $('#desconto-item-novo').text('R$ ' + formatarMoeda(preview.novo));

    // Mostrar/esconder campo supervisor
    var limite = PDV_CONFIG.desconto_limite_pct || 5;
    if (preview.pctEfetivo > limite) {
        $('#desconto-item-supervisor').removeClass('pdv-hidden');
    } else {
        $('#desconto-item-supervisor').addClass('pdv-hidden');
        $('#desconto-item-senha').val('');
    }
}

// Confirmar desconto item
$(document).on('click', '#btn-confirmar-desconto-item', confirmarDescontoItem);

function confirmarDescontoItem() {
    if (itemSelecionado < 0 || itemSelecionado >= itensCarrinho.length) return;

    var item = itensCarrinho[itemSelecionado];
    var tipo = $('input[name="desconto_item_tipo"]:checked').val();
    var raw = $('#desconto-item-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(raw) || 0;
    var senha = $('#desconto-item-senha').val();

    if (valor <= 0) {
        pdvToast('Informe o valor do desconto.', 'warning');
        $('#desconto-item-valor').trigger('focus');
        return;
    }

    // Desabilitar botão enquanto processa
    $('#btn-confirmar-desconto-item').prop('disabled', true).addClass('pdv-btn-loading');

    var postData = {
        item_index:     itemSelecionado,
        tipo_desconto:  tipo,
        valor_desconto: valor,
        preco_unitario: item.preco,
        quantidade:     item.quantidade
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    if (senha) {
        postData.senha_supervisor = senha;
    }

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/aplicar_desconto_item',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            $('#btn-confirmar-desconto-item').prop('disabled', false).removeClass('pdv-btn-loading');

            if (r.precisa_supervisor) {
                $('#desconto-item-supervisor').removeClass('pdv-hidden');
                $('#desconto-item-senha').trigger('focus');
                somErro();
                return;
            }

            if (r.success) {
                // Aplicar desconto no carrinho
                item.desconto_tipo      = r.desconto_tipo;
                item.desconto_valor     = r.desconto_valor;
                item.desconto_calculado = r.desconto_calculado;
                item.subtotal           = r.novo_subtotal;

                if (r.autorizado_por) {
                    descontoAutorizadoPor = r.autorizado_por;
                }

                fecharModal();
                atualizarEspelho();
                atualizarTotais();
                salvarVendaLocal();
                somSucesso();

                var descLabel = r.desconto_tipo === 'percentual'
                    ? '-' + formatarMoeda(r.desconto_valor) + '%'
                    : '-R$ ' + formatarMoeda(r.desconto_calculado);
                mostrarUltimoItem(
                    '<i class="fas fa-percentage"></i> Desconto ' + descLabel +
                    ' aplicado em <strong>' + escapeHtml(item.generico ? 'GEN - ' + item.descricao_manual : item.nome) + '</strong>'
                );
            } else {
                pdvToast(r.message || 'Erro ao aplicar desconto.', 'error');
            }
        },
        error: function() {
            $('#btn-confirmar-desconto-item').prop('disabled', false).removeClass('pdv-btn-loading');
            pdvToast('Erro de conexão ao aplicar desconto.', 'error');
        }
    });
}

/**
 * 7.2 — Desconto na Venda (F10)
 */
function abrirDescontoVenda() {
    if (itensCarrinho.length === 0) {
        somErro();
        mostrarUltimoItem('<i class="fas fa-ban"></i> Nenhum item na venda');
        return;
    }

    var totalVenda = calcularTotalVenda();

    // Preencher info da venda
    $('#desconto-venda-info').html(
        '<strong>' + itensCarrinho.length + ' ' +
        (itensCarrinho.length === 1 ? 'item' : 'itens') + '</strong><br>' +
        'Total da venda: <strong>R$ ' + formatarMoeda(totalVenda) + '</strong>'
    );

    // Reset campos
    $('input[name="desconto_venda_tipo"][value="percentual"]').prop('checked', true);
    $('#desconto-venda-valor').val('');
    $('#desconto-venda-senha').val('').attr('type', 'password');
    $('#desconto-venda-supervisor').addClass('pdv-hidden');

    // Preview inicial
    $('#desconto-venda-original').text('R$ ' + formatarMoeda(totalVenda));
    $('#desconto-venda-calc').text('- R$ 0,00');
    $('#desconto-venda-novo').text('R$ ' + formatarMoeda(totalVenda));

    abrirModal('modal-desconto-venda');
    setTimeout(function() { $('#desconto-venda-valor').trigger('focus'); }, 100);
}

// Preview em tempo real — Desconto Venda
$(document).on('input', '#desconto-venda-valor', function() {
    atualizarPreviewDescontoVenda();
});
$(document).on('change', 'input[name="desconto_venda_tipo"]', function() {
    atualizarPreviewDescontoVenda();
});

function atualizarPreviewDescontoVenda() {
    var totalVenda = calcularTotalVenda();
    var tipo = $('input[name="desconto_venda_tipo"]:checked').val();
    var raw = $('#desconto-venda-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(raw) || 0;

    var preview = calcularDescontoPreview(tipo, valor, totalVenda);

    $('#desconto-venda-original').text('R$ ' + formatarMoeda(totalVenda));
    $('#desconto-venda-calc').text('- R$ ' + formatarMoeda(preview.desconto));
    $('#desconto-venda-novo').text('R$ ' + formatarMoeda(preview.novo));

    // Mostrar/esconder campo supervisor
    var limite = PDV_CONFIG.desconto_limite_pct || 5;
    if (preview.pctEfetivo > limite) {
        $('#desconto-venda-supervisor').removeClass('pdv-hidden');
    } else {
        $('#desconto-venda-supervisor').addClass('pdv-hidden');
        $('#desconto-venda-senha').val('');
    }
}

// Confirmar desconto venda
$(document).on('click', '#btn-confirmar-desconto-venda', confirmarDescontoVenda);

function confirmarDescontoVenda() {
    if (itensCarrinho.length === 0) return;

    var tipo = $('input[name="desconto_venda_tipo"]:checked').val();
    var raw = $('#desconto-venda-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(raw) || 0;
    var senha = $('#desconto-venda-senha').val();

    if (valor <= 0) {
        pdvToast('Informe o valor do desconto.', 'warning');
        $('#desconto-venda-valor').trigger('focus');
        return;
    }

    // Desabilitar botão
    $('#btn-confirmar-desconto-venda').prop('disabled', true).addClass('pdv-btn-loading');

    var postData = {
        tipo_desconto:  tipo,
        valor_desconto: valor,
        itens:          JSON.stringify(itensCarrinho)
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    if (senha) {
        postData.senha_supervisor = senha;
    }

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/aplicar_desconto_venda',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            $('#btn-confirmar-desconto-venda').prop('disabled', false).removeClass('pdv-btn-loading');

            if (r.precisa_supervisor) {
                $('#desconto-venda-supervisor').removeClass('pdv-hidden');
                $('#desconto-venda-senha').trigger('focus');
                somErro();
                return;
            }

            if (r.success) {
                // Aplicar rateio em cada item
                descontoVenda     = r.desconto_calculado;
                descontoVendaTipo = r.desconto_tipo;
                descontoVendaValor = r.desconto_valor;

                if (r.autorizado_por) {
                    descontoAutorizadoPor = r.autorizado_por;
                }

                if (r.rateio) {
                    for (var i = 0; i < r.rateio.length; i++) {
                        var rt = r.rateio[i];
                        if (rt.index >= 0 && rt.index < itensCarrinho.length) {
                            itensCarrinho[rt.index].desconto_rateio = rt.desconto_rateio;
                        }
                    }
                }

                fecharModal();
                atualizarEspelho();
                atualizarTotais();
                salvarVendaLocal();
                somSucesso();

                var descLabel = r.desconto_tipo === 'percentual'
                    ? formatarMoeda(r.desconto_valor) + '%'
                    : 'R$ ' + formatarMoeda(r.desconto_calculado);
                mostrarUltimoItem(
                    '<i class="fas fa-tags"></i> Desconto de ' + descLabel +
                    ' aplicado na venda (total: R$ ' + formatarMoeda(r.novo_total) + ')'
                );
            } else {
                pdvToast(r.message || 'Erro ao aplicar desconto.', 'error');
            }
        },
        error: function() {
            $('#btn-confirmar-desconto-venda').prop('disabled', false).removeClass('pdv-btn-loading');
            pdvToast('Erro de conexão ao aplicar desconto.', 'error');
        }
    });
}

/**
 * Cálculo de preview de desconto (client-side, para preview em tempo real)
 *
 * @param {string} tipo    'percentual' ou 'valor'
 * @param {number} valor   Valor informado
 * @param {number} base    Subtotal base
 * @returns {object}       {desconto, novo, pctEfetivo}
 */
function calcularDescontoPreview(tipo, valor, base) {
    var desconto = 0;
    if (tipo === 'percentual') {
        var pct = Math.min(valor, 100);
        desconto = round2(base * (pct / 100));
    } else {
        desconto = round2(Math.min(valor, base));
    }
    var novo = round2(base - desconto);
    var pctEfetivo = (base > 0) ? (desconto / base) * 100 : 0;

    return {
        desconto:   desconto,
        novo:       novo,
        pctEfetivo: pctEfetivo
    };
}

/**
 * Renderiza linha de desconto abaixo de um item no espelho do cupom
 */
function renderizarDescontoEspelhoItem($container, item) {
    var descItem  = item.desconto_calculado || 0;
    var descRateio = item.desconto_rateio || 0;
    var descTotal = round2(descItem + descRateio);

    if (descTotal <= 0) return;

    var label = '';
    if (descItem > 0 && item.desconto_tipo === 'percentual') {
        label = '(-' + formatarMoeda(item.desconto_valor) + '% = -R$ ' + formatarMoeda(descItem) + ')';
    } else if (descItem > 0) {
        label = '(-R$ ' + formatarMoeda(descItem) + ')';
    }
    if (descRateio > 0) {
        if (label) label += ' ';
        label += '[rateio: -R$ ' + formatarMoeda(descRateio) + ']';
    }

    var $descRow = $('<div>').addClass('pdv-item-desconto').text(label);
    $container.append($descRow);
}

// =============================================================================
// VENDA SUSPENSA (F12)
// =============================================================================

function suspenderVenda() {
    if (itensCarrinho.length === 0) return;
    abrirModal('modal-suspender');
    $('#suspender-motivo').val('').trigger('focus');
}

$(document).on('click', '#btn-confirmar-suspender', function() {
    var motivo = $('#suspender-motivo').val().trim();
    var total  = 0;
    for (var i = 0; i < itensCarrinho.length; i++) total += itensCarrinho[i].subtotal;

    fecharModal();

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/suspender_venda',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            itens:       JSON.stringify(itensCarrinho),
            total:       round2(total),
            motivo:      motivo,
            cpf_cliente: cpfCliente
        },
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                somSucesso();
                limparVenda();
                mostrarUltimoItem('<i class="fas fa-pause-circle"></i> Venda suspensa #' + r.id);
            } else {
                somErro();
                mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(r.message));
            }
        },
        error: function() {
            somErro();
            mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> Erro ao suspender');
        }
    });
});

function abrirRecuperarVenda() {
    abrirModal('modal-recuperar');
    $('#lista-suspensas').html('<p class="pdv-text-center">Carregando...</p>');

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/listar_suspensas',
        type: 'GET',
        dataType: 'json',
        success: function(r) {
            atualizarCsrf(r);
            var $lista = $('#lista-suspensas').empty();
            if (!r.suspensas || r.suspensas.length === 0) {
                $lista.html('<p class="pdv-text-center">Nenhuma venda suspensa</p>');
                return;
            }
            for (var i = 0; i < r.suspensas.length; i++) {
                var s = r.suspensas[i];
                var qtdItens = s.itens ? s.itens.length : 0;
                var $item = $('<div>').addClass('pdv-suspensa-item').attr('data-id', s.id);
                $item.append(
                    $('<div>').addClass('pdv-suspensa-info').html(
                        '<strong>#' + s.id + '</strong> — R$ ' + formatarMoeda(s.total) +
                        ' (' + qtdItens + ' itens)<br>' +
                        '<small>' + escapeHtml(s.operador_nome) + ' — ' + s.suspensa_em + '</small>' +
                        (s.motivo ? '<br><small><em>' + escapeHtml(s.motivo) + '</em></small>' : '')
                    ),
                    $('<button>').addClass('pdv-btn-recuperar').text('Recuperar')
                        .attr('data-id', s.id)
                );
                $lista.append($item);
            }
        }
    });
}

$(document).on('click', '.pdv-btn-recuperar', function() {
    var id = $(this).attr('data-id');
    fecharModal();

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/recuperar_venda',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            id: id
        },
        success: function(r) {
            atualizarCsrf(r);
            if (r.success && r.itens) {
                itensCarrinho = r.itens;
                // Recalcular sequência
                sequencia = 0;
                for (var i = 0; i < itensCarrinho.length; i++) {
                    sequencia++;
                    itensCarrinho[i].seq = sequencia;
                    itensCarrinho[i].subtotal = round2(itensCarrinho[i].quantidade * itensCarrinho[i].preco);
                }
                itemSelecionado = itensCarrinho.length - 1;
                mudarEstado('venda');
                atualizarEspelho();
                atualizarTotais();
                salvarVendaLocal();
                somSucesso();
                mostrarUltimoItem('<i class="fas fa-play-circle"></i> Venda recuperada — ' + itensCarrinho.length + ' itens');
            } else {
                somErro();
                mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(r.message));
            }
        }
    });
});

// =============================================================================
// PAGAMENTO (Fase 5)
// =============================================================================

/**
 * Inicia o fluxo de pagamento (F2 na tela de venda)
 * 1. Valida estoque no servidor
 * 2. Transiciona para estado 'pagamento'
 */
function iniciarPagamento() {
    if (itensCarrinho.length === 0) return;
    if (pgtoFinalizando) return;

    // Preencher resumo de itens na tela de pagamento
    preencherResumoPagamento();

    // Validar estoque no servidor antes de ir para pagamento
    pgtoFinalizando = true;
    mostrarUltimoItem('<i class="fas fa-spinner fa-spin"></i> Validando estoque...');

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/validar_estoque',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            itens: JSON.stringify(itensCarrinho)
        },
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            pgtoFinalizando = false;

            if (r.success && r.valido) {
                pgtoLockOwner = r.lock_owner || null;
                abrirTelaPagamento();
            } else {
                somErro();
                mostrarUltimoItem('<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(r.message || 'Erro na validação de estoque'));
            }
        },
        error: function() {
            pgtoFinalizando = false;
            // Se não tem Redis/validação, prossegue sem lock
            pgtoLockOwner = null;
            abrirTelaPagamento();
        }
    });
}

function abrirTelaPagamento() {
    pgtoFormaAtual = null;
    pgtoModo = null;
    pgtoParciais = [];

    // Reset UI do pagamento
    $('#pgto-area').html('<p class="pdv-pgto-instrucao">Selecione a forma de pagamento</p>');
    $('#pgto-troco').addClass('pdv-hidden');
    $('#pgto-parciais').addClass('pdv-hidden');
    $('#pgto-parciais-lista').empty();
    $('.pdv-pgto-btn').removeClass('pdv-pgto-btn-ativo');

    mudarEstado('pagamento');
    somSucesso();
}

function preencherResumoPagamento() {
    var $itens = $('#pdv-pagamento-itens').empty();
    var subtotal = 0;

    for (var i = 0; i < itensCarrinho.length; i++) {
        var item = itensCarrinho[i];
        var nomeExibir = item.generico ? 'GEN - ' + item.descricao_manual : item.nome;

        var $row = $('<div>').addClass('pdv-pagamento-item');
        $row.append(
            $('<span>').addClass('pdv-pagamento-item-nome').text(
                formatarQtd(item.quantidade) + 'x ' + nomeExibir
            ),
            $('<span>').addClass('pdv-pagamento-item-valor').text('R$ ' + formatarMoeda(item.subtotal))
        );
        $itens.append($row);
        subtotal += item.subtotal;
    }

    subtotal = round2(subtotal);
    $('#pgto-subtotal').text('R$ ' + formatarMoeda(subtotal));

    // Fase 7: Desconto na tela de pagamento
    var descItensTotal = 0;
    for (var j = 0; j < itensCarrinho.length; j++) {
        descItensTotal += (itensCarrinho[j].desconto_calculado || 0);
    }
    var descTotal = round2(descItensTotal + (descontoVenda || 0));
    var totalFinal = round2(subtotal - descTotal);

    if (descTotal > 0) {
        $('#pgto-desconto-linha').removeClass('pdv-hidden');
        $('#pgto-desconto').text('- R$ ' + formatarMoeda(descTotal));
    } else {
        $('#pgto-desconto-linha').addClass('pdv-hidden');
    }
    $('#pgto-total').text('R$ ' + formatarMoeda(totalFinal));
}

function calcularTotalVenda() {
    var subtotalBruto = 0;
    var descItens = 0;
    for (var i = 0; i < itensCarrinho.length; i++) {
        subtotalBruto += round2(itensCarrinho[i].preco * itensCarrinho[i].quantidade);
        descItens += (itensCarrinho[i].desconto_calculado || 0);
    }
    return round2(subtotalBruto - descItens - (descontoVenda || 0));
}

/**
 * Seleciona forma de pagamento (clique no botão ou F-key)
 */
function selecionarFormaPagamento(forma) {
    pgtoFormaAtual = forma;

    // Highlight botão ativo
    $('.pdv-pgto-btn').removeClass('pdv-pgto-btn-ativo');
    $('.pdv-pgto-btn[data-forma="' + forma + '"]').addClass('pdv-pgto-btn-ativo');

    var total = calcularTotalVenda();
    var restante = total - calcularTotalPago();

    if (forma === 'misto') {
        pgtoModo = 'misto';
        $('#pgto-parciais').removeClass('pdv-hidden');
        atualizarParciais();
        renderizarAreaMisto(restante);
        return;
    }

    pgtoModo = 'unico';
    $('#pgto-parciais').addClass('pdv-hidden');

    if (forma === 'dinheiro') {
        renderizarAreaDinheiro(pgtoModo === 'misto' ? restante : total);
    } else if (forma === 'debito' || forma === 'credito') {
        renderizarAreaCartao(forma, pgtoModo === 'misto' ? restante : total);
    } else if (forma === 'pix') {
        renderizarAreaPix(pgtoModo === 'misto' ? restante : total);
    } else if (forma === 'fiado') {
        renderizarAreaFiado(total);
    }
}

function renderizarAreaDinheiro(valor) {
    var html = '<div class="pdv-pgto-valor-input-group">' +
        '<div class="pdv-pgto-valor-label">Valor Recebido (R$)</div>' +
        '<input type="text" id="pgto-dinheiro-input" class="pdv-pgto-valor-input" ' +
        'placeholder="0,00" autocomplete="off" inputmode="decimal">' +
        '<button class="pdv-pgto-confirmar" id="pgto-confirmar-btn">' +
        '<i class="fas fa-check"></i> CONFIRMAR PAGAMENTO</button>' +
        '</div>';
    $('#pgto-area').html(html);
    $('#pgto-troco').addClass('pdv-hidden');

    setTimeout(function() {
        $('#pgto-dinheiro-input').trigger('focus');
    }, 100);

    // Calcular troco em tempo real
    $(document).off('input.pgto').on('input.pgto', '#pgto-dinheiro-input', function() {
        var raw = this.value.replace(/[^\d,]/g, '').replace(',', '.');
        var recebido = parseFloat(raw) || 0;
        var troco = round2(recebido - valor);

        if (troco >= 0 && recebido > 0) {
            $('#pgto-troco').removeClass('pdv-hidden');
            $('#pgto-troco-valor').text('R$ ' + formatarMoeda(troco));
        } else {
            $('#pgto-troco').addClass('pdv-hidden');
        }
    });
}

function renderizarAreaCartao(forma, valor) {
    var tipoLabel = (forma === 'debito') ? 'Débito' : 'Crédito';
    var html = '<div class="pdv-pgto-maquina-msg">' +
        '<i class="fas fa-credit-card"></i>' +
        '<div class="pgto-valor-destaque">R$ ' + formatarMoeda(valor) + '</div>' +
        '<p>Passe o cartão <strong>' + tipoLabel + '</strong> na maquininha</p>' +
        '<p>Pressione <strong>Enter</strong> após confirmar na máquina</p>' +
        '<button class="pdv-pgto-confirmar" id="pgto-confirmar-btn">' +
        '<i class="fas fa-check"></i> CONFIRMAR — PASSOU NA MÁQUINA</button>' +
        '</div>';
    $('#pgto-area').html(html);
    $('#pgto-troco').addClass('pdv-hidden');
    $(document).off('input.pgto');
}

function renderizarAreaPix(valor) {
    var html = '<div class="pdv-pgto-maquina-msg">' +
        '<i class="fas fa-qrcode"></i>' +
        '<div class="pgto-valor-destaque">R$ ' + formatarMoeda(valor) + '</div>' +
        '<p>Aguardando pagamento <strong>PIX</strong></p>' +
        '<p>Pressione <strong>Enter</strong> após confirmar o recebimento</p>' +
        '<button class="pdv-pgto-confirmar" id="pgto-confirmar-btn">' +
        '<i class="fas fa-check"></i> CONFIRMAR — PIX RECEBIDO</button>' +
        '</div>';
    $('#pgto-area').html(html);
    $('#pgto-troco').addClass('pdv-hidden');
    $(document).off('input.pgto');
}

function renderizarAreaFiado(valor) {
    // Fase 8 — Abre modal de busca de cliente para fiado
    fiadoClienteSelecionado = null;
    fiadoSupervisorId = null;

    var html = '<div class="pdv-pgto-maquina-msg">' +
        '<i class="fas fa-handshake"></i>' +
        '<div class="pgto-valor-destaque">R$ ' + formatarMoeda(valor) + '</div>' +
        '<p>Venda <strong>FIADO</strong></p>' +
        '<p>Selecione o cliente para continuar</p>' +
        '</div>';
    $('#pgto-area').html(html);
    $('#pgto-troco').addClass('pdv-hidden');
    $(document).off('input.pgto');

    // Abrir modal de busca de cliente fiado
    abrirModalFiadoCliente(valor);
}

function renderizarAreaMisto(restante) {
    if (restante <= 0) {
        var html = '<div class="pdv-pgto-maquina-msg">' +
            '<i class="fas fa-check-circle" style="color:#16a34a"></i>' +
            '<p style="font-size:20px;font-weight:700;color:#16a34a">Valor total coberto!</p>' +
            '<button class="pdv-pgto-confirmar" id="pgto-confirmar-btn">' +
            '<i class="fas fa-check"></i> FINALIZAR VENDA</button>' +
            '</div>';
        $('#pgto-area').html(html);
        $(document).off('input.pgto');
        return;
    }

    var html = '<div class="pdv-pgto-valor-input-group">' +
        '<div class="pdv-pgto-valor-label">Restante: R$ ' + formatarMoeda(restante) + '</div>' +
        '<select id="pgto-misto-forma" class="pdv-pgto-misto-forma">' +
        '<option value="">Selecione a forma...</option>' +
        '<option value="dinheiro">Dinheiro</option>' +
        '<option value="debito">Cartão Débito</option>' +
        '<option value="credito">Cartão Crédito</option>' +
        '<option value="pix">PIX</option>' +
        '<option value="fiado">Fiado</option>' +
        '</select>' +
        '<input type="text" id="pgto-misto-valor" class="pdv-pgto-valor-input" ' +
        'placeholder="0,00" autocomplete="off" inputmode="decimal" ' +
        'style="font-size:24px;padding:12px">' +
        '<button class="pdv-pgto-confirmar" id="pgto-adicionar-parcial" style="background:linear-gradient(135deg,#17a2b8,#2874A6)">' +
        '<i class="fas fa-plus"></i> ADICIONAR PAGAMENTO</button>' +
        '</div>';
    $('#pgto-area').html(html);
    $('#pgto-troco').addClass('pdv-hidden');
    $(document).off('input.pgto');

    setTimeout(function() { $('#pgto-misto-forma').trigger('focus'); }, 100);
}

// Adicionar pagamento parcial (modo misto)
$(document).on('click', '#pgto-adicionar-parcial', function() {
    var forma = $('#pgto-misto-forma').val();
    var valorRaw = $('#pgto-misto-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(valorRaw) || 0;

    if (!forma) { pdvToast('Selecione a forma de pagamento.', 'warning'); return; }

    // Fiado no misto: abrir modal de busca de cliente com o restante como valor
    if (forma === 'fiado') {
        var total = calcularTotalVenda();
        var pago = calcularTotalPago();
        var restante = round2(total - pago);
        if (restante <= 0) { pdvToast('Valor total já coberto.', 'warning'); return; }
        // Usar valor informado ou o restante total
        var valorFiado = (valor > 0) ? Math.min(valor, restante) : restante;
        abrirModalFiadoCliente(valorFiado);
        // Quando o fiado for confirmado no modal, o callback adicionará ao pgtoParciais
        $('#modal-fiado-cliente').data('modo-misto', true).data('valor-fiado', valorFiado);
        return;
    }

    if (valor <= 0) { pdvToast('Informe o valor.', 'warning'); return; }

    var total = calcularTotalVenda();
    var pago = calcularTotalPago();
    var restante = round2(total - pago);

    if (valor > restante) {
        // Para dinheiro, permite valor maior (troco)
        if (forma !== 'dinheiro') {
            valor = restante;
        }
    }

    var troco = 0;
    if (forma === 'dinheiro' && valor > restante) {
        troco = round2(valor - restante);
    }

    pgtoParciais.push({ forma: forma, valor: valor, troco: troco });
    atualizarParciais();

    var novoRestante = round2(total - calcularTotalPago());
    renderizarAreaMisto(novoRestante);

    if (novoRestante <= 0 && troco > 0) {
        $('#pgto-troco').removeClass('pdv-hidden');
        $('#pgto-troco-valor').text('R$ ' + formatarMoeda(troco));
    }

    somSucesso();
});

function calcularTotalPago() {
    var total = 0;
    for (var i = 0; i < pgtoParciais.length; i++) {
        // Para dinheiro com troco, o valor efetivo é valor - troco
        total += pgtoParciais[i].valor - (pgtoParciais[i].troco || 0);
    }
    return round2(total);
}

function atualizarParciais() {
    var $lista = $('#pgto-parciais-lista').empty();
    var totalPago = 0;
    var formaLabels = { dinheiro: 'Dinheiro', debito: 'Débito', credito: 'Crédito', pix: 'PIX', fiado: 'Fiado' };

    for (var i = 0; i < pgtoParciais.length; i++) {
        var p = pgtoParciais[i];
        var valorEfetivo = p.valor - (p.troco || 0);
        totalPago += valorEfetivo;
        var $item = $('<div>').addClass('pdv-pgto-parcial-item');
        $item.append(
            $('<span>').text(formaLabels[p.forma] || p.forma),
            $('<span>').text('R$ ' + formatarMoeda(p.valor) + (p.troco > 0 ? ' (troco: ' + formatarMoeda(p.troco) + ')' : ''))
        );
        $lista.append($item);
    }

    totalPago = round2(totalPago);
    var total = calcularTotalVenda();
    var restante = round2(total - totalPago);

    $('#pgto-valor-pago').text('R$ ' + formatarMoeda(totalPago));
    $('#pgto-restante').text('R$ ' + formatarMoeda(Math.max(0, restante)));
}

// Clique nos botões de forma de pagamento
$(document).on('click', '.pdv-pgto-btn', function() {
    selecionarFormaPagamento($(this).attr('data-forma'));
});

// Confirmar pagamento (botão ou Enter)
$(document).on('click', '#pgto-confirmar-btn', confirmarPagamento);

function confirmarPagamento() {
    if (pgtoFinalizando) return;

    var total = calcularTotalVenda();
    var pagamentos = [];

    if (pgtoModo === 'misto') {
        // Modo misto — usar parciais acumulados
        if (pgtoParciais.length === 0) {
            pdvToast('Adicione pelo menos um pagamento.', 'warning');
            return;
        }
        var totalPago = calcularTotalPago();
        if (totalPago < total) {
            pdvToast('Valor pago (R$ ' + formatarMoeda(totalPago) + ') é menor que o total (R$ ' + formatarMoeda(total) + ').', 'error');
            return;
        }
        pagamentos = pgtoParciais;
    } else {
        // Modo único
        if (!pgtoFormaAtual) {
            pdvToast('Selecione a forma de pagamento.', 'warning');
            return;
        }

        var valor = total;
        var troco = 0;

        if (pgtoFormaAtual === 'dinheiro') {
            var raw = $('#pgto-dinheiro-input').val().replace(/[^\d,]/g, '').replace(',', '.');
            var recebido = parseFloat(raw) || 0;

            if (recebido < total) {
                pdvToast('Valor recebido (R$ ' + formatarMoeda(recebido) + ') é menor que o total (R$ ' + formatarMoeda(total) + ').', 'error');
                $('#pgto-dinheiro-input').trigger('focus');
                return;
            }
            valor = recebido;
            troco = round2(recebido - total);
        }

        pagamentos.push({ forma: pgtoFormaAtual, valor: valor, troco: troco });
    }

    // Enviar para o servidor
    pgtoFinalizando = true;
    $('#pgto-confirmar-btn').prop('disabled', true).addClass('pdv-btn-loading');

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/finalizar_venda',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            itens:       JSON.stringify(itensCarrinho),
            pagamentos:  JSON.stringify(pagamentos),
            cpf_cliente: cpfCliente,
            lock_owner:  pgtoLockOwner || '',
            desconto_venda_tipo:  descontoVendaTipo || '',
            desconto_venda_valor: descontoVenda || 0,
            desconto_autorizado_por: descontoAutorizadoPor || 0,
            customer_id: fiadoClienteSelecionado ? fiadoClienteSelecionado.id : 0
        },
        timeout: 30000,
        success: function(r) {
            atualizarCsrf(r);
            pgtoFinalizando = false;

            if (r.success) {
                // Limpar localStorage ANTES de qualquer outra coisa
                localStorage.removeItem(STORAGE_KEY);
                // Marcar que a última venda foi finalizada (evita restauração acidental)
                try { localStorage.setItem(STORAGE_KEY + '_done', '1'); } catch(ex) {}
                itensCarrinho = [];
                // Venda finalizada com sucesso!
                exibirTelaFinalizada(r);
            } else {
                pdvToast(r.message || 'Erro ao finalizar venda.', 'error');
                $('#pgto-confirmar-btn').prop('disabled', false).removeClass('pdv-btn-loading');
            }
        },
        error: function() {
            pgtoFinalizando = false;
            pdvToast('Erro de conexão ao finalizar venda. Tente novamente.', 'error');
            $('#pgto-confirmar-btn').prop('disabled', false).removeClass('pdv-btn-loading');
        }
    });
}

/**
 * Exibe tela finalizada ("Obrigado!") e agenda retorno para idle
 */
function exibirTelaFinalizada(resultado) {
    // Limpar localStorage
    localStorage.removeItem(STORAGE_KEY);

    // Preencher tela finalizada
    var trocoTexto = '';
    if (resultado.troco > 0) {
        trocoTexto = 'Troco: R$ ' + formatarMoeda(resultado.troco);
    }
    $('#pdv-finalizado-troco').text(trocoTexto);

    mudarEstado('finalizado');
    somFinalizou();

    // Abrir cupom para impressão (se auto-imprimir ativo)
    if (PDV_CONFIG.auto_imprimir !== false && resultado.venda_id) {
        abrirCupomImpressao(resultado.venda_id);
    }

    // Auto-retorno para idle após 5 segundos
    var timer = 5;
    $('#pdv-finalizado-timer').text(timer);
    var interval = setInterval(function() {
        timer--;
        $('#pdv-finalizado-timer').text(timer);
        if (timer <= 0) {
            clearInterval(interval);
            voltarParaIdle();
        }
    }, 1000);

    // Permitir bipar para iniciar nova venda imediatamente
    // NÃO consumir o caractere — deixar o input cair no #pdv-barcode-global
    // (que já está focado pelo mudarEstado('finalizado'))
    $(document).on('keydown.finalizado', function(e) {
        if (e.key && e.key !== 'Escape' && e.key !== 'F1' && !e.key.match(/^F\d+$/)) {
            clearInterval(interval);
            // Apenas parar o timer — o processarBarcodeLido() fará a transição
            // quando o barcode completo chegar via input event no campo global
        }
    });
}

function voltarParaIdle() {
    $(document).off('keydown.finalizado');
    // Limpar timer e campo de barcode para evitar dados residuais
    clearTimeout(barcodeTimer);
    barcodeTimer = null;
    bipandoEmAndamento = false;
    $('#pdv-barcode-global').val('');
    $('#pdv-barcode').val('');
    itensCarrinho = [];
    itemSelecionado = -1;
    cpfCliente = '';
    sequencia = 0;
    pgtoFormaAtual = null;
    pgtoModo = null;
    pgtoParciais = [];
    pgtoLockOwner = null;
    pgtoFinalizando = false;
    // Fase 7: reset descontos
    descontoVenda = 0;
    descontoVendaTipo = null;
    descontoVendaValor = 0;
    descontoAutorizadoPor = null;
    // Fase 8: reset fiado
    fiadoClienteSelecionado = null;
    fiadoSupervisorId = null;
    $('#pdv-quantidade').val('1');
    $('#pdv-valor-unit').text('0,00');
    $('#pdv-total-item-valor').text('R$ 0,00');
    $('#pdv-total-valor').text('0,00');
    $('#pdv-total-desconto').addClass('pdv-hidden');
    $('#pdv-cupom-itens').empty();
    $('#pdv-cupom-count').text('0 itens');
    mostrarUltimoItem('Nenhum item adicionado');
    localStorage.removeItem(STORAGE_KEY);
    mudarEstado('idle');
    // Forçar foco na janela principal (popup cupom pode ter roubado)
    window.focus();
    focarBarcodeGlobal();
}

/**
 * Volta do pagamento para venda (Esc)
 */
function voltarParaVenda() {
    // Liberar locks se houver
    if (pgtoLockOwner) {
        $.ajax({
            url: PDV_CONFIG.base_url + 'pdv/liberar_locks_estoque',
            type: 'POST',
            dataType: 'json',
            data: {
                [PDV_CONFIG.csrf_name]: csrfHash,
                lock_owner: pgtoLockOwner,
                itens: JSON.stringify(itensCarrinho)
            },
            success: function(r) { atualizarCsrf(r); }
        });
        pgtoLockOwner = null;
    }

    pgtoFormaAtual = null;
    pgtoModo = null;
    pgtoParciais = [];
    $(document).off('input.pgto');
    mudarEstado('venda');
}

/**
 * Abre popup do cupom para impressão
 */
function abrirCupomImpressao(vendaId) {
    var url = PDV_CONFIG.base_url + 'pdv/cupom_impressao?venda_id=' + vendaId;
    var win = window.open(url, 'cupom_pdv', 'width=350,height=600,scrollbars=yes');
    if (win) {
        win.focus();
        // Devolver o foco para a janela principal após o popup carregar
        // para que o scanner USB continue funcionando
        setTimeout(function() {
            window.focus();
            focarBarcodeGlobal();
        }, 500);
    }
}

function somFinalizou() {
    if (!PDV_CONFIG.som_feedback) return;
    try {
        var ctx = getAudioCtx();
        // Sequência de 3 tons ascendentes
        var notas = [800, 1000, 1200];
        for (var i = 0; i < notas.length; i++) {
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = notas[i];
            gain.gain.value = 0.08;
            osc.start(ctx.currentTime + i * 0.12);
            osc.stop(ctx.currentTime + (i * 0.12) + 0.1);
        }
    } catch (e) {}
}

// =============================================================================
// LOCALSTORAGE RECOVERY
// =============================================================================

function salvarVendaLocal() {
    if (itensCarrinho.length === 0) {
        localStorage.removeItem(STORAGE_KEY);
        return;
    }
    var dados = {
        itens:               itensCarrinho,
        sequencia:           sequencia,
        cpf:                 cpfCliente,
        descontoVenda:       descontoVenda,
        descontoVendaTipo:   descontoVendaTipo,
        descontoVendaValor:  descontoVendaValor,
        descontoAutorizadoPor: descontoAutorizadoPor,
        timestamp:           Date.now()
    };
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dados));
    } catch (e) { /* quota exceeded — silently fail */ }
}

function verificarVendaPendente() {
    try {
        // Limpar qualquer dado residual do localStorage ao inicializar
        // Vendas pendentes são descartadas — o operador refaz se necessário
        // (A restauração via dialog causava bugs: foco roubado, Enter acidental)
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STORAGE_KEY + '_done');
    } catch (e) {
        // silently fail
    }
}

// =============================================================================
// TECLADO — ATALHOS
// =============================================================================

$(document).on('keydown', function(e) {
    // Se modal aberto, processar Esc e teclas específicas do modal
    if (modalAberto) {
        if (e.key === 'Escape') {
            e.preventDefault();
            fecharModal();
            return;
        }
        // Menu caixa: número seleciona opção
        if (modalAberto === 'modal-menu-caixa' && e.key >= '1' && e.key <= '8') {
            e.preventDefault();
            processarOpcaoMenuCaixa(parseInt(e.key));
            return;
        }
        // Menu reimpressão: número seleciona opção
        if (modalAberto === 'modal-reimprimir' && e.key >= '1' && e.key <= '3') {
            e.preventDefault();
            processarOpcaoReimpressao(parseInt(e.key));
            return;
        }
        // Enter nas modais com campo de senha — submit
        if (e.key === 'Enter') {
            var activeId = document.activeElement.id;
            if (modalAberto === 'modal-sangria' && (activeId === 'sangria-senha' || activeId === 'sangria-motivo' || activeId === 'sangria-valor')) {
                e.preventDefault(); $('#btn-confirmar-sangria').trigger('click'); return;
            }
            if (modalAberto === 'modal-suprimento' && (activeId === 'suprimento-senha' || activeId === 'suprimento-motivo' || activeId === 'suprimento-valor')) {
                e.preventDefault(); $('#btn-confirmar-suprimento').trigger('click'); return;
            }
            if (modalAberto === 'modal-troca-operador' && (activeId === 'troca-matricula' || activeId === 'troca-senha')) {
                e.preventDefault(); $('#btn-confirmar-troca').trigger('click'); return;
            }
            if (modalAberto === 'modal-cancelar-cupom' && (activeId === 'cancelar-motivo' || activeId === 'cancelar-senha')) {
                e.preventDefault(); $('#btn-confirmar-cancelar').trigger('click'); return;
            }
            if (modalAberto === 'modal-desconto-item' && (activeId === 'desconto-item-valor' || activeId === 'desconto-item-senha')) {
                e.preventDefault(); $('#btn-confirmar-desconto-item').trigger('click'); return;
            }
            if (modalAberto === 'modal-desconto-venda' && (activeId === 'desconto-venda-valor' || activeId === 'desconto-venda-senha')) {
                e.preventDefault(); $('#btn-confirmar-desconto-venda').trigger('click'); return;
            }
            if (modalAberto === 'modal-buscar-cupom' && activeId === 'buscar-cupom-numero') {
                e.preventDefault(); $('#btn-buscar-cupom').trigger('click'); return;
            }
            if (modalAberto === 'modal-reimprimir-numero' && activeId === 'reimprimir-cupom-numero') {
                e.preventDefault(); $('#btn-reimprimir-numero').trigger('click'); return;
            }
        }
        return;
    }

    var key = e.key;
    if (!key) return;

    // Prevenir comportamento padrão das F-keys
    if (key.match(/^F([1-9]|1[0-2])$/)) {
        e.preventDefault();
    }

    // ---- Atalhos por estado ----

    if (state === 'idle') {
        switch (key) {
            case 'c': case 'C':
                if (document.activeElement.tagName !== 'INPUT') ativarConsulta();
                break;
            case 'F7':  abrirMenuCaixa(); break;
            case 'F8':  abrirMenuReimpressao(); break;
            case 'F11': abrirReceberFiado(); break;
            case 'F12': abrirRecuperarVenda(); break;
            case 'Enter':
                // Submit barcode global se tiver conteúdo, senão abre nova venda
                e.preventDefault();
                var $bg = $('#pdv-barcode-global');
                clearTimeout(barcodeTimer);
                barcodeTimer = null;
                var codigoGlobal = $bg.val().trim();
                $bg.val('');
                if (codigoGlobal.length > 0) {
                    processarBarcodeLido(codigoGlobal);
                } else {
                    // Enter sem barcode no idle → abre nova venda vazia
                    abrirNovaVendaVazia();
                }
                break;
            default:
                // Redirecionar teclas digitáveis para o campo barcode global
                if (key.length === 1 && !e.ctrlKey && !e.altKey && document.activeElement.id !== 'pdv-barcode-global') {
                    $('#pdv-barcode-global').trigger('focus');
                }
                break;
        }
        return;
    }

    if (state === 'consulta') {
        switch (key) {
            case 'Escape': case 'c': case 'C':
                if (key === 'Escape' || document.activeElement.tagName !== 'INPUT') {
                    mudarEstado('idle');
                }
                break;
        }
        return;
    }

    if (state === 'pagamento') {
        var isInput = (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'SELECT');

        switch (key) {
            case 'Escape':
                e.preventDefault();
                voltarParaVenda();
                break;
            case 'F1':
                selecionarFormaPagamento('dinheiro');
                break;
            case 'F2':
                selecionarFormaPagamento('debito');
                break;
            case 'F3':
                selecionarFormaPagamento('pix');
                break;
            case 'F4':
                selecionarFormaPagamento('fiado');
                break;
            case 'F5':
                selecionarFormaPagamento('misto');
                break;
            case 'Enter':
                if (isInput && document.activeElement.id === 'pgto-misto-valor') {
                    e.preventDefault();
                    $('#pgto-adicionar-parcial').trigger('click');
                } else if ($('#pgto-confirmar-btn').length && !$('#pgto-confirmar-btn').prop('disabled')) {
                    e.preventDefault();
                    confirmarPagamento();
                }
                break;
        }
        return;
    }

    if (state === 'finalizado') {
        // Redirecionar teclas digitáveis para #pdv-barcode-global (scanner/teclado)
        if (key === 'Enter') {
            e.preventDefault();
            var $bg = $('#pdv-barcode-global');
            clearTimeout(barcodeTimer);
            var codigoFinal = $bg.val().trim();
            $bg.val('');
            if (codigoFinal.length > 0) {
                processarBarcodeLido(codigoFinal);
            } else {
                // Enter sem barcode → apenas voltar para idle
                voltarParaIdle();
            }
        } else if (key.length === 1 && !e.ctrlKey && !e.altKey && document.activeElement.id !== 'pdv-barcode-global') {
            $('#pdv-barcode-global').trigger('focus');
        }
        return;
    }

    if (state === 'venda') {
        // Se digitando no campo barcode, só processar Enter e F-keys
        var isBarcode = (document.activeElement.id === 'pdv-barcode');
        var isQuantidade = (document.activeElement.id === 'pdv-quantidade');

        // Quantidade: +/- com setas
        if (isQuantidade) {
            if (key === 'Enter') {
                e.preventDefault();
                focarBarcode();
                return;
            }
            if (key === 'ArrowUp') {
                e.preventDefault();
                var q = parseFloat($('#pdv-quantidade').val()) || 1;
                $('#pdv-quantidade').val(q + 1);
                return;
            }
            if (key === 'ArrowDown') {
                e.preventDefault();
                var q2 = parseFloat($('#pdv-quantidade').val()) || 1;
                if (q2 > 1) $('#pdv-quantidade').val(q2 - 1);
                return;
            }
        }

        // Só processar atalhos globais se não está digitando
        if (isBarcode && !key.match(/^F\d+$/) && key !== 'Escape' && key !== 'Delete' && key !== 'ArrowUp' && key !== 'ArrowDown') {
            return;
        }

        switch (key) {
            case 'F2':
                if (itensCarrinho.length > 0) {
                    iniciarPagamento();
                }
                break;
            case 'F3':  cancelarVenda(); break;
            case 'F4':  cancelarUltimoItem(); break;
            case 'F5':  abrirBuscaNome(); break;
            case 'F6':  abrirCpf(); break;
            case 'F7':  abrirMenuCaixa(); break;
            case 'F8':  abrirMenuReimpressao(); break;
            case 'F9':  abrirDescontoItem(); break;
            case 'F10': abrirDescontoVenda(); break;
            case 'F11': abrirReceberFiado(); break;
            case 'F12': suspenderVenda(); break;
            case 'Delete':
                cancelarItemSelecionado();
                break;
            case 'ArrowUp':
                if (!isBarcode && !isQuantidade) {
                    e.preventDefault();
                    navegarCima();
                }
                break;
            case 'ArrowDown':
                if (!isBarcode && !isQuantidade) {
                    e.preventDefault();
                    navegarBaixo();
                }
                break;
            case 'Escape':
                focarBarcode();
                break;
        }

        // Teclas de letra (G, Q) — apenas se não está digitando
        if (!isBarcode && !isQuantidade) {
            switch (key) {
                case 'g': case 'G': abrirGenerico(); break;
                case 'q': case 'Q': abrirEditarQuantidade(); break;
            }
        }
    }
});

// =============================================================================
// MODAIS
// =============================================================================

function abrirModal(id) {
    fecharModal();
    $('#' + id).addClass('pdv-modal-visible');
    modalAberto = id;
}

function fecharModal() {
    if (modalAberto) {
        $('#' + modalAberto).removeClass('pdv-modal-visible');
        modalAberto = null;
    }
    // Restaurar foco
    if (state === 'venda') {
        focarBarcode();
    } else {
        focarBarcodeGlobal();
    }
}

$(document).on('click', '[data-close-modal]', fecharModal);

// Fechar modal ao clicar no overlay
$(document).on('click', '.pdv-modal', function(e) {
    if (e.target === this) fecharModal();
});

// =============================================================================
// SOM DE FEEDBACK (Web Audio API)
// =============================================================================

var audioCtx = null;

function getAudioCtx() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioCtx;
}

function somSucesso() {
    if (!PDV_CONFIG.som_feedback) return;
    try {
        var ctx = getAudioCtx();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 1200;
        gain.gain.value = 0.1;
        osc.start();
        osc.stop(ctx.currentTime + 0.08);
    } catch (e) {}
}

function somErro() {
    if (!PDV_CONFIG.som_feedback) return;
    try {
        var ctx = getAudioCtx();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 300;
        osc.type = 'square';
        gain.gain.value = 0.1;
        osc.start();
        osc.stop(ctx.currentTime + 0.3);
    } catch (e) {}
}

// =============================================================================
// RELÓGIO
// =============================================================================

function iniciarRelogio() {
    function tick() {
        var now = new Date();
        var h = String(now.getHours()).padStart(2, '0');
        var m = String(now.getMinutes()).padStart(2, '0');
        var s = String(now.getSeconds()).padStart(2, '0');
        $('#pdv-clock').text(h + ':' + m + ':' + s);
    }
    tick();
    setInterval(tick, 1000);
}

// =============================================================================
// UTILITÁRIOS
// =============================================================================

function formatarMoeda(valor) {
    return parseFloat(valor).toFixed(2).replace('.', ',');
}

function formatarQtd(qtd) {
    var n = parseFloat(qtd);
    if (n === Math.floor(n)) return String(Math.floor(n));
    return n.toFixed(3).replace('.', ',');
}

function round2(valor) {
    return Math.round(valor * 100) / 100;
}

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function atualizarCsrf(response) {
    if (response && response.csrf_token) {
        csrfHash = response.csrf_token;
        // Also update csrf_name if server provides it (handles regeneration)
        if (response.csrf_name) {
            PDV_CONFIG.csrf_name = response.csrf_name;
        }
    }
}

// =============================================================================
// FASE 6: CONTROLE DE CAIXA
// =============================================================================

// ---- Display Cliente (BroadcastChannel) ----

var displayChannel = null;

function enviarDisplayCliente(evento, dados) {
    if (!PDV_CONFIG.display_habilitado) return;
    try {
        if (!displayChannel) {
            displayChannel = new BroadcastChannel('pdv-display-' + PDV_CONFIG.terminal_id);
        }
        displayChannel.postMessage({
            evento: evento,
            dados: dados || {},
            terminal: PDV_CONFIG.terminal_id,
            timestamp: Date.now()
        });
    } catch (e) { /* BroadcastChannel not supported — silent fail */ }
}

// ---- Helpers ----

/**
 * Formata input como moeda brasileira (centavos → reais)
 * Ex: digita 1234 → exibe 12,34
 */
function formatarInputMoeda($input) {
    $input.on('keyup', function() {
        var val = this.value.replace(/\D/g, '');
        if (val === '') { this.value = ''; return; }
        var num = parseInt(val, 10);
        var formatted = (num / 100).toFixed(2).replace('.', ',');
        this.value = formatted;
    });
}

// Inicializar inputs de moeda
$(document).on('focus', '.pdv-input-moeda', function() {
    if (!$(this).data('moeda-init')) {
        formatarInputMoeda($(this));
        $(this).data('moeda-init', true);
    }
});

/**
 * Parse valor monetário BR → float
 */
function parseMoedaBR(str) {
    if (!str) return 0;
    var raw = str.replace(/\./g, '').replace(',', '.');
    return parseFloat(raw) || 0;
}

// Toggle senha (eye button)
$(document).on('click', '[data-toggle-senha]', function() {
    var targetId = $(this).attr('data-toggle-senha');
    var $input = $('#' + targetId);
    var $icon = $(this).find('i');
    if ($input.attr('type') === 'password') {
        $input.attr('type', 'text');
        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        $input.attr('type', 'password');
        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// ---- 1. F7 Menu do Caixa ----

function abrirMenuCaixa() {
    abrirModal('modal-menu-caixa');
}

function processarOpcaoMenuCaixa(opcao) {
    fecharModal();
    switch (opcao) {
        case 1: abrirSangria(); break;
        case 2: abrirSuprimento(); break;
        case 3: abrirLeituraX(); break;
        case 4: abrirFechamento(); break;
        case 5: abrirTrocaOperador(); break;
        case 6: abrirDevolucao(); break;
        case 7: cancelarUltimoCupom(); break;
        case 8: cancelarCupomNumero(); break;
    }
}

$(document).on('click', '#menu-caixa-opcoes .pdv-menu-item', function() {
    var opcao = parseInt($(this).attr('data-opcao'));
    processarOpcaoMenuCaixa(opcao);
});

// ---- 2. Sangria ----

function abrirSangria() {
    $('#sangria-valor').val('');
    $('#sangria-motivo').val('');
    $('#sangria-senha').val('').attr('type', 'password');
    abrirModal('modal-sangria');
    setTimeout(function() { $('#sangria-valor').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-sangria', function() {
    var valor = parseMoedaBR($('#sangria-valor').val());
    var motivo = $('#sangria-motivo').val().trim();
    var senha = $('#sangria-senha').val();

    if (valor <= 0) { pdvToast('Informe um valor válido.', 'warning'); $('#sangria-valor').trigger('focus'); return; }
    if (!motivo) { pdvToast('Informe o motivo.', 'warning'); $('#sangria-motivo').trigger('focus'); return; }
    if (!senha) { pdvToast('Informe a senha do supervisor.', 'warning'); $('#sangria-senha').trigger('focus'); return; }

    var postData = {
        valor: valor,
        motivo: motivo,
        senha_supervisor: senha
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/sangria',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                fecharModal();
                somSucesso();
                mostrarUltimoItem('<i class="fas fa-arrow-down"></i> Sangria de R$ ' + formatarMoeda(valor) + ' realizada');
            } else {
                pdvToast(r.message || 'Erro ao realizar sangria.', 'error');
            }
        },
        error: function() {
            pdvToast('Erro de conexão ao realizar sangria.', 'error');
        }
    });
});

// ---- 3. Suprimento ----

function abrirSuprimento() {
    $('#suprimento-valor').val('');
    $('#suprimento-motivo').val('');
    $('#suprimento-senha').val('').attr('type', 'password');
    abrirModal('modal-suprimento');
    setTimeout(function() { $('#suprimento-valor').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-suprimento', function() {
    var valor = parseMoedaBR($('#suprimento-valor').val());
    var motivo = $('#suprimento-motivo').val().trim();
    var senha = $('#suprimento-senha').val();

    if (valor <= 0) { pdvToast('Informe um valor válido.', 'warning'); $('#suprimento-valor').trigger('focus'); return; }
    if (!motivo) { pdvToast('Informe o motivo.', 'warning'); $('#suprimento-motivo').trigger('focus'); return; }
    if (!senha) { pdvToast('Informe a senha do supervisor.', 'warning'); $('#suprimento-senha').trigger('focus'); return; }

    var postData = {
        valor: valor,
        motivo: motivo,
        senha_supervisor: senha
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/suprimento',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                fecharModal();
                somSucesso();
                mostrarUltimoItem('<i class="fas fa-arrow-up"></i> Suprimento de R$ ' + formatarMoeda(valor) + ' realizado');
            } else {
                pdvToast(r.message || 'Erro ao realizar suprimento.', 'error');
            }
        },
        error: function() {
            pdvToast('Erro de conexão ao realizar suprimento.', 'error');
        }
    });
});

// ---- 4. Leitura X ----

function abrirLeituraX() {
    abrirModal('modal-leitura-x');
    $('#leitura-x-conteudo').html('<p class="pdv-text-center">Carregando...</p>');

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/leitura_x',
        type: 'GET',
        dataType: 'json',
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                renderizarLeituraX(r.dados, '#leitura-x-conteudo');
            } else {
                somErro();
                $('#leitura-x-conteudo').empty().append(
                    $('<p>').addClass('pdv-text-center pdv-alert-danger').text(r.message || 'Erro ao carregar leitura.')
                );
            }
        },
        error: function() {
            somErro();
            $('#leitura-x-conteudo').empty().append(
                $('<p>').addClass('pdv-text-center pdv-alert-danger').text('Erro de conexão.')
            );
        }
    });
}

function renderizarLeituraX(dados, container) {
    var $c = $(container).empty();

    var $header = $('<div>').addClass('pdv-leitura-header');
    $header.append(
        $('<div>').text('LEITURA X — ' + (dados.data_hora || '')),
        $('<div>').text('Operador: ' + (dados.operador || '')),
        $('<div>').text('Caixa: ' + (dados.terminal || ''))
    );
    $c.append($header);

    // Resumo
    var linhas = [
        { label: 'Fundo de Troco', valor: dados.fundo_troco || 0 },
        { label: 'Total de Vendas', valor: dados.total_vendas || 0 },
        { label: 'Qtd. Vendas', valor: dados.qtd_vendas || 0, isMoeda: false }
    ];

    var $resumo = $('<div>').addClass('pdv-leitura-secao');
    $resumo.append($('<div>').addClass('pdv-leitura-titulo').text('RESUMO'));
    for (var i = 0; i < linhas.length; i++) {
        var $linha = $('<div>').addClass('pdv-leitura-linha');
        $linha.append(
            $('<span>').text(linhas[i].label),
            $('<span>').text(linhas[i].isMoeda === false ? linhas[i].valor : 'R$ ' + formatarMoeda(linhas[i].valor))
        );
        $resumo.append($linha);
    }
    $c.append($resumo);

    // Formas de pagamento
    var formas = dados.formas_pagamento || {};
    var formaLabels = {
        dinheiro: 'Dinheiro', debito: 'Cartão Débito', credito: 'Cartão Crédito',
        pix: 'PIX', fiado: 'Fiado'
    };
    var $formas = $('<div>').addClass('pdv-leitura-secao');
    $formas.append($('<div>').addClass('pdv-leitura-titulo').text('FORMAS DE PAGAMENTO'));
    var formaKeys = Object.keys(formaLabels);
    for (var j = 0; j < formaKeys.length; j++) {
        var key = formaKeys[j];
        var val = formas[key] || 0;
        var $fl = $('<div>').addClass('pdv-leitura-linha');
        $fl.append(
            $('<span>').text(formaLabels[key]),
            $('<span>').text('R$ ' + formatarMoeda(val))
        );
        $formas.append($fl);
    }
    $c.append($formas);

    // Movimentações
    var $mov = $('<div>').addClass('pdv-leitura-secao');
    $mov.append($('<div>').addClass('pdv-leitura-titulo').text('MOVIMENTAÇÕES'));
    var movLinhas = [
        { label: 'Sangrias', valor: dados.total_sangrias || 0 },
        { label: 'Suprimentos', valor: dados.total_suprimentos || 0 },
        { label: 'SALDO ESTIMADO', valor: dados.saldo_estimado || 0, destaque: true }
    ];
    for (var k = 0; k < movLinhas.length; k++) {
        var $ml = $('<div>').addClass('pdv-leitura-linha');
        if (movLinhas[k].destaque) $ml.addClass('pdv-leitura-destaque');
        $ml.append(
            $('<span>').text(movLinhas[k].label),
            $('<span>').text('R$ ' + formatarMoeda(movLinhas[k].valor))
        );
        $mov.append($ml);
    }
    $c.append($mov);
}

$(document).on('click', '#btn-imprimir-leitura-x', function() {
    var url = PDV_CONFIG.base_url + 'pdv/leitura_x?format=print';
    var win = window.open(url, 'leitura_x_print', 'width=400,height=600,scrollbars=yes');
    if (win) win.focus();
});

// ---- 5. Fechamento de Caixa ----

function abrirFechamento() {
    abrirModal('modal-fechamento');
    $('#fechamento-conteudo').html('<p class="pdv-text-center">Carregando...</p>');

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/leitura_x',
        type: 'GET',
        dataType: 'json',
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                renderizarFechamento(r.dados);
            } else {
                somErro();
                $('#fechamento-conteudo').empty().append(
                    $('<p>').addClass('pdv-text-center pdv-alert-danger').text(r.message || 'Erro ao carregar dados.')
                );
            }
        },
        error: function() {
            somErro();
            $('#fechamento-conteudo').empty().append(
                $('<p>').addClass('pdv-text-center pdv-alert-danger').text('Erro de conexão.')
            );
        }
    });
}

function renderizarFechamento(dados) {
    var $c = $('#fechamento-conteudo').empty();
    var cego = PDV_CONFIG.fechamento_cego;

    // Header
    var $header = $('<div>').addClass('pdv-leitura-header');
    $header.append(
        $('<div>').text('FECHAMENTO DE CAIXA'),
        $('<div>').text('Operador: ' + (dados.operador || '')),
        $('<div>').text('Caixa: ' + (dados.terminal || ''))
    );
    $c.append($header);

    // Se não é cego, mostrar resumo
    if (!cego) {
        renderizarLeituraX(dados, $('<div>').appendTo($c));
    }

    // Formulário de contagem
    var $form = $('<div>').addClass('pdv-fechamento-form');
    $form.append($('<div>').addClass('pdv-leitura-titulo').text('CONTAGEM'));

    if (!cego) {
        var $esperado = $('<div>').addClass('pdv-leitura-linha pdv-leitura-destaque');
        $esperado.append(
            $('<span>').text('Saldo Esperado'),
            $('<span>').text('R$ ' + formatarMoeda(dados.saldo_estimado || 0))
        );
        $form.append($esperado);
    }

    var $valorGroup = $('<div>').addClass('pdv-form-group');
    $valorGroup.append(
        $('<label>').text('Valor Contado (R$) *'),
        $('<input>').attr({
            type: 'text', id: 'fechamento-valor-contado',
            placeholder: '0,00', autocomplete: 'off', inputmode: 'decimal'
        }).addClass('pdv-form-control pdv-input-moeda')
    );
    $form.append($valorGroup);

    var $obsGroup = $('<div>').addClass('pdv-form-group');
    $obsGroup.append(
        $('<label>').text('Observação'),
        $('<input>').attr({
            type: 'text', id: 'fechamento-observacao',
            placeholder: 'Obrigatório se houver diferença', maxlength: 255, autocomplete: 'off'
        }).addClass('pdv-form-control')
    );
    $form.append($obsGroup);

    var $btn = $('<button>').addClass('pdv-btn-danger').attr('id', 'btn-confirmar-fechamento')
        .html('<i class="fas fa-lock"></i> FECHAR CAIXA');
    $form.append($btn);

    $c.append($form);

    // Store dados for later use
    $c.data('dados-fechamento', dados);

    setTimeout(function() { $('#fechamento-valor-contado').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-fechamento', function() {
    var valor = parseMoedaBR($('#fechamento-valor-contado').val());
    var obs = $('#fechamento-observacao').val().trim();
    var dados = $('#fechamento-conteudo').data('dados-fechamento') || {};
    var saldoEsperado = dados.saldo_estimado || 0;
    var diferenca = round2(valor - saldoEsperado);

    // Se há diferença e sem observação
    if (Math.abs(diferenca) > 0.01 && !obs) {
        pdvToast('Informe a observação — existe diferença de R$ ' + formatarMoeda(Math.abs(diferenca)), 'warning');
        $('#fechamento-observacao').trigger('focus');
        return;
    }

    function executarFechamento() {
        var postData = {
        valor_contado: valor,
        observacao: obs
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    $('#btn-confirmar-fechamento').prop('disabled', true);

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/fechamento_caixa',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 30000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                somSucesso();
                var $c = $('#fechamento-conteudo').empty();

                var $ok = $('<div>').addClass('pdv-text-center').css('padding', '30px 0');
                $ok.append(
                    $('<div>').css({ fontSize: '60px', color: '#16a34a', marginBottom: '20px' })
                        .html('<i class="fas fa-check-circle"></i>'),
                    $('<h2>').text('Caixa Fechado com Sucesso!')
                );

                // Se fechamento cego, revelar a diferença
                if (PDV_CONFIG.fechamento_cego && r.diferenca !== undefined) {
                    var difColor = Math.abs(r.diferenca) < 0.01 ? '#16a34a' : '#dc2626';
                    $ok.append(
                        $('<div>').css({ marginTop: '20px', fontSize: '18px' }).append(
                            $('<div>').text('Valor esperado: R$ ' + formatarMoeda(r.saldo_esperado || saldoEsperado)),
                            $('<div>').text('Valor contado: R$ ' + formatarMoeda(valor)),
                            $('<div>').css({ fontWeight: 'bold', color: difColor, fontSize: '24px', marginTop: '10px' })
                                .text('Diferença: R$ ' + formatarMoeda(Math.abs(r.diferenca)) +
                                      (r.diferenca > 0.01 ? ' (SOBRA)' : r.diferenca < -0.01 ? ' (FALTA)' : ' (OK)'))
                        )
                    );
                }

                $ok.append(
                    $('<p>').css({ marginTop: '20px', color: '#999' })
                        .text('Redirecionando para login em 5 segundos...')
                );
                $c.append($ok);

                // Redirecionar
                setTimeout(function() {
                    window.location.href = PDV_CONFIG.base_url + 'login';
                }, 5000);
            } else {
                pdvToast(r.message || 'Erro ao fechar caixa.', 'error');
                $('#btn-confirmar-fechamento').prop('disabled', false);
            }
        },
        error: function() {
            pdvToast('Erro de conexão ao fechar caixa.', 'error');
            $('#btn-confirmar-fechamento').prop('disabled', false);
        }
    });
    } // fim executarFechamento

    if (valor <= 0) {
        pdvConfirm('Valor contado é R$ 0,00. Deseja continuar?', function() {
            pdvConfirm('Confirma o FECHAMENTO DO CAIXA? Esta ação não pode ser desfeita.', executarFechamento, { danger: true, confirmText: 'Fechar Caixa' });
        }, { danger: true, confirmText: 'Sim, continuar' });
    } else {
        pdvConfirm('Confirma o FECHAMENTO DO CAIXA? Esta ação não pode ser desfeita.', executarFechamento, { danger: true, confirmText: 'Fechar Caixa' });
    }
});

// ---- 6. Trocar Operador ----

function abrirTrocaOperador() {
    $('#troca-matricula').val('');
    $('#troca-senha').val('').attr('type', 'password');
    abrirModal('modal-troca-operador');
    setTimeout(function() { $('#troca-matricula').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-troca', function() {
    var matricula = $('#troca-matricula').val().trim();
    var senha = $('#troca-senha').val();

    if (!matricula) { pdvToast('Informe a matrícula.', 'warning'); $('#troca-matricula').trigger('focus'); return; }
    if (!senha) { pdvToast('Informe a senha.', 'warning'); $('#troca-senha').trigger('focus'); return; }

    var postData = {
        matricula: matricula,
        senha: senha
    };
    postData[PDV_CONFIG.csrf_name] = csrfHash;

    $('#btn-confirmar-troca').prop('disabled', true);

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/trocar_operador',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success) {
                somSucesso();
                // Recarregar a página para nova sessão do operador
                window.location.reload();
            } else {
                pdvToast(r.message || 'Erro ao trocar operador.', 'error');
                $('#btn-confirmar-troca').prop('disabled', false);
            }
        },
        error: function() {
            pdvToast('Erro de conexão.', 'error');
            $('#btn-confirmar-troca').prop('disabled', false);
        }
    });
});

// ---- 7. Cancelar Último Cupom ----

function cancelarUltimoCupom() {
    abrirModal('modal-cancelar-cupom');
    $('#cancelar-cupom-detalhes').html('<p class="pdv-text-center">Carregando...</p>');
    $('#cancelar-cupom-form').hide();

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/reimprimir_cupom',
        type: 'GET',
        dataType: 'json',
        data: { ultimo: 1 },
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success && r.venda) {
                renderizarDetalhesCupom(r.venda);
            } else {
                somErro();
                $('#cancelar-cupom-detalhes').empty().append(
                    $('<p>').addClass('pdv-text-center pdv-alert-danger').text(r.message || 'Nenhuma venda encontrada.')
                );
            }
        },
        error: function() {
            somErro();
            $('#cancelar-cupom-detalhes').empty().append(
                $('<p>').addClass('pdv-text-center pdv-alert-danger').text('Erro de conexão.')
            );
        }
    });
}

function renderizarDetalhesCupom(venda) {
    var $d = $('#cancelar-cupom-detalhes').empty();

    var $header = $('<div>').addClass('pdv-leitura-header');
    $header.append(
        $('<div>').append($('<strong>').text('Cupom #' + (venda.id || venda.venda_id || ''))),
        $('<div>').text('Data: ' + (venda.data || venda.created_at || '')),
        $('<div>').text('Operador: ' + (venda.operador || ''))
    );
    $d.append($header);

    // Itens
    if (venda.itens && venda.itens.length > 0) {
        var $itens = $('<div>').addClass('pdv-leitura-secao');
        $itens.append($('<div>').addClass('pdv-leitura-titulo').text('ITENS'));
        for (var i = 0; i < venda.itens.length; i++) {
            var item = venda.itens[i];
            var $linha = $('<div>').addClass('pdv-leitura-linha');
            $linha.append(
                $('<span>').text((item.nome || item.product_name || 'Item') + ' x' + (item.quantidade || item.qty || 1)),
                $('<span>').text('R$ ' + formatarMoeda(item.subtotal || item.total || 0))
            );
            $itens.append($linha);
        }
        $d.append($itens);
    }

    // Total
    var $total = $('<div>').addClass('pdv-leitura-linha pdv-leitura-destaque');
    $total.append(
        $('<span>').text('TOTAL'),
        $('<span>').text('R$ ' + formatarMoeda(venda.total || 0))
    );
    $d.append($total);

    // Mostrar form de cancelamento
    $('#cancelar-venda-id').val(venda.id || venda.venda_id || '');
    $('#cancelar-motivo').val('');
    $('#cancelar-senha').val('').attr('type', 'password');
    $('#cancelar-cupom-form').show();
    setTimeout(function() { $('#cancelar-motivo').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-confirmar-cancelar', function() {
    var vendaId = $('#cancelar-venda-id').val();
    var motivo = $('#cancelar-motivo').val().trim();
    var senha = $('#cancelar-senha').val();

    if (!vendaId) { pdvToast('Venda não identificada.', 'error'); return; }
    if (!motivo) { pdvToast('Informe o motivo do cancelamento.', 'warning'); $('#cancelar-motivo').trigger('focus'); return; }
    if (!senha) { pdvToast('Informe a senha do supervisor.', 'warning'); $('#cancelar-senha').trigger('focus'); return; }

    pdvConfirm('Confirma o CANCELAMENTO do cupom #' + vendaId + '?', function() {
        var postData = {
            venda_id: vendaId,
            motivo: motivo,
            senha_supervisor: senha
        };
        postData[PDV_CONFIG.csrf_name] = csrfHash;

        $('#btn-confirmar-cancelar').prop('disabled', true);

        $.ajax({
            url: PDV_CONFIG.base_url + 'pdv/cancelar_cupom',
            type: 'POST',
            dataType: 'json',
            data: postData,
            timeout: 15000,
            success: function(r) {
                atualizarCsrf(r);
                if (r.success) {
                    fecharModal();
                    somSucesso();
                    mostrarUltimoItem('<i class="fas fa-ban"></i> Cupom #' + escapeHtml(vendaId) + ' cancelado');
                } else {
                    pdvToast(r.message || 'Erro ao cancelar cupom.', 'error');
                    $('#btn-confirmar-cancelar').prop('disabled', false);
                }
            },
            error: function() {
                pdvToast('Erro de conexão ao cancelar cupom.', 'error');
                $('#btn-confirmar-cancelar').prop('disabled', false);
            }
        });
    }, { danger: true, confirmText: 'Sim, cancelar cupom' });
});

// ---- 8. Cancelar Cupom por Número ----

function cancelarCupomNumero() {
    $('#buscar-cupom-numero').val('');
    abrirModal('modal-buscar-cupom');
    setTimeout(function() { $('#buscar-cupom-numero').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-buscar-cupom', function() {
    var numero = $('#buscar-cupom-numero').val().trim();
    if (!numero) { pdvToast('Informe o número do cupom.', 'warning'); return; }

    fecharModal();
    abrirModal('modal-cancelar-cupom');
    $('#cancelar-cupom-detalhes').html('<p class="pdv-text-center">Carregando...</p>');
    $('#cancelar-cupom-form').hide();

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/reimprimir_cupom',
        type: 'GET',
        dataType: 'json',
        data: { venda_id: numero },
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success && r.venda) {
                renderizarDetalhesCupom(r.venda);
            } else {
                somErro();
                $('#cancelar-cupom-detalhes').empty().append(
                    $('<p>').addClass('pdv-text-center pdv-alert-danger').text(r.message || 'Cupom não encontrado.')
                );
            }
        },
        error: function() {
            somErro();
            $('#cancelar-cupom-detalhes').empty().append(
                $('<p>').addClass('pdv-text-center pdv-alert-danger').text('Erro de conexão.')
            );
        }
    });
});

// ---- 9. F8 Menu de Reimpressão ----

function abrirMenuReimpressao() {
    abrirModal('modal-reimprimir');
}

function processarOpcaoReimpressao(opcao) {
    fecharModal();
    switch (opcao) {
        case 1: reimprimirUltimoCupom(); break;
        case 2: reimprimirPorNumero(); break;
        case 3: abrirLeituraXPrint(); break;
    }
}

$(document).on('click', '#menu-reimprimir-opcoes .pdv-menu-item', function() {
    var opcao = parseInt($(this).attr('data-opcao-reimp'));
    processarOpcaoReimpressao(opcao);
});

function reimprimirUltimoCupom() {
    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/reimprimir_cupom',
        type: 'GET',
        dataType: 'json',
        data: { ultimo: 1 },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            if (r.success && r.venda) {
                var vendaId = r.venda.id || r.venda.venda_id;
                var url = PDV_CONFIG.base_url + 'pdv/cupom_impressao?venda_id=' + vendaId + '&segunda_via=1';
                var win = window.open(url, 'cupom_pdv', 'width=350,height=600,scrollbars=yes');
                if (win) win.focus();
                somSucesso();
            } else {
                pdvToast(r.message || 'Nenhuma venda encontrada para reimpressão.', 'error');
            }
        },
        error: function() {
            pdvToast('Erro de conexão.', 'error');
        }
    });
}

function reimprimirPorNumero() {
    $('#reimprimir-cupom-numero').val('');
    abrirModal('modal-reimprimir-numero');
    setTimeout(function() { $('#reimprimir-cupom-numero').trigger('focus'); }, 100);
}

$(document).on('click', '#btn-reimprimir-numero', function() {
    var numero = $('#reimprimir-cupom-numero').val().trim();
    if (!numero) { pdvToast('Informe o número do cupom.', 'warning'); return; }

    fecharModal();
    var url = PDV_CONFIG.base_url + 'pdv/cupom_impressao?venda_id=' + encodeURIComponent(numero) + '&segunda_via=1';
    var win = window.open(url, 'cupom_pdv', 'width=350,height=600,scrollbars=yes');
    if (win) win.focus();
    somSucesso();
});

function abrirLeituraXPrint() {
    var url = PDV_CONFIG.base_url + 'pdv/leitura_x?format=print';
    var win = window.open(url, 'leitura_x_print', 'width=400,height=600,scrollbars=yes');
    if (win) win.focus();
}

// =============================================================================
// FIADO / CREDIÁRIO — Fase 8
// =============================================================================

/**
 * Abre o modal de busca de cliente para fiado
 */
function abrirModalFiadoCliente(valor) {
    var $m = $('#modal-fiado-cliente');
    $m.data('valor-fiado', valor || calcularTotalVenda());
    $m.data('modo-misto', false);
    // Limpar estado anterior
    $('#fiado-busca-input').val('');
    $('#fiado-resultados').empty();
    $('#fiado-resumo').addClass('pdv-hidden');
    $('#fiado-supervisor-area').addClass('pdv-hidden');
    $('#fiado-senha-supervisor').val('');
    $('#fiado-status-msg').empty().removeClass('pdv-fiado-status-verde pdv-fiado-status-amarelo pdv-fiado-status-vermelho');
    $('#btn-confirmar-fiado').prop('disabled', true);
    fiadoClienteSelecionado = null;
    fiadoSupervisorId = null;

    $m.show();
    setTimeout(function() { $('#fiado-busca-input').trigger('focus'); }, 150);
}

/**
 * Fecha o modal de fiado cliente
 */
function fecharModalFiadoCliente() {
    $('#modal-fiado-cliente').hide();
}

/**
 * Busca clientes para fiado (keyup no input de busca)
 */
var fiadoBuscaTimer = null;
$(document).on('keyup', '#fiado-busca-input', function(e) {
    if (e.key === 'Escape') {
        fecharModalFiadoCliente();
        return;
    }
    var termo = $(this).val().trim();
    clearTimeout(fiadoBuscaTimer);
    if (termo.length < 2) {
        $('#fiado-resultados').empty();
        return;
    }
    fiadoBuscaTimer = setTimeout(function() {
        buscarClienteFiado(termo);
    }, 350);
});

function buscarClienteFiado(termo) {
    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/buscar_cliente_fiado',
        type: 'GET',
        dataType: 'json',
        data: { termo: termo },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            var $res = $('#fiado-resultados').empty();
            if (!r.success || !r.clientes || r.clientes.length === 0) {
                $res.append(
                    $('<div>').addClass('pdv-fiado-sem-resultado').text('Nenhum cliente encontrado.')
                );
                return;
            }
            for (var i = 0; i < r.clientes.length; i++) {
                var c = r.clientes[i];
                var $item = $('<div>').addClass('pdv-fiado-resultado-item')
                    .attr('data-id', c.id)
                    .attr('data-json', JSON.stringify(c));

                var info = c.name;
                if (c.mobile) info += ' \u2014 ' + c.mobile;
                if (c.cpf) info += ' \u2014 CPF: ' + c.cpf;

                var debitoLabel = 'D\u00e9bito: R$ ' + formatarMoeda(c.debito_atual || 0);

                $item.append(
                    $('<div>').addClass('pdv-fiado-resultado-nome').text(info),
                    $('<div>').addClass('pdv-fiado-resultado-debito').text(debitoLabel)
                );
                $res.append($item);
            }
        },
        error: function() {
            $('#fiado-resultados').html(
                '<div class="pdv-fiado-sem-resultado">Erro ao buscar clientes.</div>'
            );
        }
    });
}

/**
 * Selecionar cliente da lista de resultados
 */
$(document).on('click', '.pdv-fiado-resultado-item:not(.pdv-fiado-resultado-receber)', function() {
    var clienteData = JSON.parse($(this).attr('data-json'));
    selecionarClienteFiado(clienteData);
});

function selecionarClienteFiado(cliente) {
    fiadoClienteSelecionado = cliente;
    fiadoSupervisorId = null;

    // Highlight item selecionado
    $('.pdv-fiado-resultado-item').removeClass('pdv-fiado-resultado-ativo');
    $('.pdv-fiado-resultado-item[data-id="' + cliente.id + '"]').addClass('pdv-fiado-resultado-ativo');

    // Exibir resumo de cr\u00e9dito
    $('#fiado-resumo').removeClass('pdv-hidden');
    $('#fiado-debito-atual').text('R$ ' + formatarMoeda(cliente.debito_atual || 0));
    $('#fiado-limite').text('R$ ' + formatarMoeda(cliente.limite || 0));
    $('#fiado-disponivel').text('R$ ' + formatarMoeda(cliente.disponivel || 0));

    var valorVenda = parseFloat($('#modal-fiado-cliente').data('valor-fiado')) || calcularTotalVenda();
    $('#fiado-valor-venda').text('R$ ' + formatarMoeda(valorVenda));

    // Avaliar status de cr\u00e9dito
    var $msg = $('#fiado-status-msg')
        .empty()
        .removeClass('pdv-fiado-status-verde pdv-fiado-status-amarelo pdv-fiado-status-vermelho');
    var $btnConfirmar = $('#btn-confirmar-fiado');
    var $supArea = $('#fiado-supervisor-area').addClass('pdv-hidden');
    $('#fiado-senha-supervisor').val('');

    if (cliente.bloqueado == 1) {
        // VERMELHO — bloqueado
        $msg.addClass('pdv-fiado-status-vermelho')
            .text('CLIENTE BLOQUEADO para fiado. Contate o gerente.');
        $btnConfirmar.prop('disabled', true);
        return;
    }

    if (valorVenda <= (cliente.disponivel || 0)) {
        // VERDE — dentro do limite
        $msg.addClass('pdv-fiado-status-verde')
            .text('Cr\u00e9dito dispon\u00edvel. Venda autorizada.');
        $btnConfirmar.prop('disabled', false);
    } else {
        // AMARELO — acima do limite, precisa supervisor
        $msg.addClass('pdv-fiado-status-amarelo')
            .text('Venda acima do limite dispon\u00edvel (R$ ' + formatarMoeda(cliente.disponivel || 0) +
                '). Necess\u00e1ria autoriza\u00e7\u00e3o do supervisor.');
        $supArea.removeClass('pdv-hidden');
        $btnConfirmar.prop('disabled', true);
        setTimeout(function() { $('#fiado-senha-supervisor').trigger('focus'); }, 100);
    }
}

/**
 * Validar senha do supervisor para fiado acima do limite
 */
$(document).on('click', '#btn-validar-fiado-supervisor', validarFiadoSupervisor);
$(document).on('keydown', '#fiado-senha-supervisor', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        validarFiadoSupervisor();
    }
});

function validarFiadoSupervisor() {
    var senha = $('#fiado-senha-supervisor').val().trim();
    if (!senha) {
        pdvToast('Informe a senha do supervisor.', 'warning');
        return;
    }

    $('#btn-validar-fiado-supervisor').prop('disabled', true);

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/validar_fiado_supervisor',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            senha_supervisor: senha,
            customer_id: fiadoClienteSelecionado ? fiadoClienteSelecionado.id : 0,
            valor: $('#modal-fiado-cliente').data('valor-fiado') || calcularTotalVenda()
        },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            $('#btn-validar-fiado-supervisor').prop('disabled', false);

            if (r.success) {
                fiadoSupervisorId = r.supervisor_id;
                $('#fiado-supervisor-area').addClass('pdv-hidden');
                $('#fiado-status-msg')
                    .removeClass('pdv-fiado-status-amarelo')
                    .addClass('pdv-fiado-status-verde')
                    .text('Autorizado pelo supervisor. Venda liberada.');
                $('#btn-confirmar-fiado').prop('disabled', false);
                somSucesso();
            } else {
                pdvToast(r.message || 'Senha inv\u00e1lida.', 'error');
                $('#fiado-senha-supervisor').val('').trigger('focus');
            }
        },
        error: function() {
            $('#btn-validar-fiado-supervisor').prop('disabled', false);
            pdvToast('Erro de conex\u00e3o.', 'error');
        }
    });
}

/**
 * Confirmar fiado — fecha modal e habilita finaliza\u00e7\u00e3o
 */
$(document).on('click', '#btn-confirmar-fiado', function() {
    if (!fiadoClienteSelecionado) {
        pdvToast('Selecione um cliente.', 'warning');
        return;
    }

    var $m = $('#modal-fiado-cliente');
    var modoMisto = $m.data('modo-misto');
    var valorFiado = parseFloat($m.data('valor-fiado')) || 0;

    fecharModalFiadoCliente();

    if (modoMisto) {
        // Modo misto — adicionar fiado como parcial
        pgtoParciais.push({ forma: 'fiado', valor: valorFiado, troco: 0 });
        atualizarParciais();
        var total = calcularTotalVenda();
        var novoRestante = round2(total - calcularTotalPago());
        renderizarAreaMisto(novoRestante);
        somSucesso();
        return;
    }

    // Modo \u00fanico — atualizar \u00e1rea de pagamento com o cliente selecionado
    var total = calcularTotalVenda();
    var html = '<div class="pdv-pgto-maquina-msg">' +
        '<i class="fas fa-handshake" style="color:#16a34a"></i>' +
        '<div class="pgto-valor-destaque">R$ ' + formatarMoeda(total) + '</div>' +
        '<p>Venda <strong>FIADO</strong> para:</p>' +
        '<p style="font-size:18px;font-weight:700">' + escapeHtml(fiadoClienteSelecionado.name) + '</p>' +
        '<button class="pdv-pgto-confirmar" id="pgto-confirmar-btn">' +
        '<i class="fas fa-check"></i> FINALIZAR VENDA FIADO</button>' +
        '</div>';
    $('#pgto-area').html(html);
    somSucesso();
});

/**
 * Fechar modal fiado cliente via bot\u00e3o X ou Esc
 */
$(document).on('click', '#modal-fiado-cliente [data-close-modal]', fecharModalFiadoCliente);

/**
 * Abrir cadastro r\u00e1pido de cliente (a partir do modal de fiado)
 */
function abrirCadastroRapido() {
    $('#cadastro-rapido-nome').val('');
    $('#cadastro-rapido-telefone').val('');
    $('#cadastro-rapido-cpf').val('');
    $('#cadastro-rapido-erro').text('').addClass('pdv-hidden');
    $('#modal-cadastro-rapido').show();
    setTimeout(function() { $('#cadastro-rapido-nome').trigger('focus'); }, 150);
}

$(document).on('click', '#btn-abrir-cadastro-rapido', abrirCadastroRapido);

function fecharCadastroRapido() {
    $('#modal-cadastro-rapido').hide();
}

$(document).on('click', '#modal-cadastro-rapido [data-close-modal]', fecharCadastroRapido);

/**
 * Salvar cadastro r\u00e1pido de cliente
 */
$(document).on('click', '#btn-confirmar-cadastro-rapido', salvarCadastroRapido);
$(document).on('keydown', '#modal-cadastro-rapido input', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        salvarCadastroRapido();
    }
    if (e.key === 'Escape') {
        fecharCadastroRapido();
    }
});

function salvarCadastroRapido() {
    var nome = $('#cadastro-rapido-nome').val().trim();
    var telefone = $('#cadastro-rapido-telefone').val().trim();
    var cpf = $('#cadastro-rapido-cpf').val().trim();

    if (!nome) {
        $('#cadastro-rapido-erro').text('Nome \u00e9 obrigat\u00f3rio.').removeClass('pdv-hidden');
        $('#cadastro-rapido-nome').trigger('focus');
        return;
    }
    if (!telefone) {
        $('#cadastro-rapido-erro').text('Telefone \u00e9 obrigat\u00f3rio.').removeClass('pdv-hidden');
        $('#cadastro-rapido-telefone').trigger('focus');
        return;
    }

    $('#btn-confirmar-cadastro-rapido').prop('disabled', true);

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/cadastrar_cliente_rapido',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            nome: nome,
            telefone: telefone,
            cpf: cpf
        },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            $('#btn-confirmar-cadastro-rapido').prop('disabled', false);

            if (r.success) {
                fecharCadastroRapido();
                somSucesso();
                // Selecionar o cliente rec\u00e9m-cadastrado
                selecionarClienteFiado(r.cliente);
                // Atualizar input de busca
                $('#fiado-busca-input').val(r.cliente.name);
            } else {
                somErro();
                $('#cadastro-rapido-erro').text(r.message || 'Erro ao cadastrar.').removeClass('pdv-hidden');
            }
        },
        error: function() {
            $('#btn-confirmar-cadastro-rapido').prop('disabled', false);
            somErro();
            $('#cadastro-rapido-erro').text('Erro de conex\u00e3o.').removeClass('pdv-hidden');
        }
    });
}

// =============================================================================
// RECEBER FIADO — F11
// =============================================================================

/**
 * Abre o modal de recebimento de fiado
 */
function abrirReceberFiado() {
    var $m = $('#modal-receber-fiado');
    // Resetar para etapa 1
    $('#receber-fiado-etapa1').show();
    $('#receber-fiado-etapa2').hide();
    $('#receber-fiado-etapa3').hide();
    $('#receber-fiado-busca').val('');
    $('#receber-fiado-resultados').empty();
    $m.show();
    setTimeout(function() { $('#receber-fiado-busca').trigger('focus'); }, 150);
}

function fecharReceberFiado() {
    $('#modal-receber-fiado').hide();
}

$(document).on('click', '#modal-receber-fiado [data-close-modal]', fecharReceberFiado);

/**
 * Busca de cliente para recebimento de fiado
 */
var receberBuscaTimer = null;
$(document).on('keyup', '#receber-fiado-busca', function(e) {
    if (e.key === 'Escape') {
        fecharReceberFiado();
        return;
    }
    var termo = $(this).val().trim();
    clearTimeout(receberBuscaTimer);
    if (termo.length < 2) {
        $('#receber-fiado-resultados').empty();
        return;
    }
    receberBuscaTimer = setTimeout(function() {
        buscarClienteReceber(termo);
    }, 350);
});

function buscarClienteReceber(termo) {
    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/buscar_cliente_fiado',
        type: 'GET',
        dataType: 'json',
        data: { termo: termo },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            var $res = $('#receber-fiado-resultados').empty();
            if (!r.success || !r.clientes || r.clientes.length === 0) {
                $res.append(
                    $('<div>').addClass('pdv-fiado-sem-resultado').text('Nenhum cliente encontrado.')
                );
                return;
            }
            for (var i = 0; i < r.clientes.length; i++) {
                var c = r.clientes[i];
                // S\u00f3 mostrar clientes com d\u00e9bito
                if ((c.debito_atual || 0) <= 0) continue;

                var $item = $('<div>').addClass('pdv-fiado-resultado-item pdv-fiado-resultado-receber')
                    .attr('data-id', c.id);

                var info = c.name;
                if (c.mobile) info += ' \u2014 ' + c.mobile;

                $item.append(
                    $('<div>').addClass('pdv-fiado-resultado-nome').text(info),
                    $('<div>').addClass('pdv-fiado-resultado-debito')
                        .text('D\u00e9bito: R$ ' + formatarMoeda(c.debito_atual || 0))
                );
                $res.append($item);
            }
            // Se nenhum com d\u00e9bito
            if ($res.children().length === 0) {
                $res.append(
                    $('<div>').addClass('pdv-fiado-sem-resultado').text('Nenhum cliente com d\u00e9bito encontrado.')
                );
            }
        },
        error: function() {
            $('#receber-fiado-resultados').html(
                '<div class="pdv-fiado-sem-resultado">Erro ao buscar clientes.</div>'
            );
        }
    });
}

/**
 * Selecionar cliente para recebimento — carregar d\u00e9bitos (etapa 2)
 */
$(document).on('click', '.pdv-fiado-resultado-receber', function() {
    var clienteId = $(this).attr('data-id');
    carregarDebitosCliente(clienteId);
});

function carregarDebitosCliente(clienteId) {
    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/get_debitos_cliente',
        type: 'GET',
        dataType: 'json',
        data: { customer_id: clienteId },
        timeout: 10000,
        success: function(r) {
            atualizarCsrf(r);
            if (!r.success) {
                pdvToast(r.message || 'Erro ao carregar d\u00e9bitos.', 'error');
                return;
            }

            // Mudar para etapa 2
            $('#receber-fiado-etapa1').hide();
            $('#receber-fiado-etapa2').show().data('customer-id', clienteId);

            // Info do cliente
            var cliente = r.cliente || {};
            $('#receber-fiado-cliente-nome').text(cliente.name || 'Cliente');
            $('#receber-fiado-cliente-tel').text(cliente.mobile || '');

            // Listar d\u00e9bitos
            var $lista = $('#receber-fiado-debitos').empty();
            var debitos = r.debitos || [];
            for (var i = 0; i < debitos.length; i++) {
                var d = debitos[i];
                var $item = $('<div>').addClass('pdv-fiado-debito-item');
                var dataStr = d.created_at ? d.created_at.substring(0, 10).split('-').reverse().join('/') : '';
                $item.append(
                    $('<span>').addClass('pdv-fiado-debito-data').text(dataStr),
                    $('<span>').addClass('pdv-fiado-debito-ref').text('NF: ' + (d.invoice_id || '-')),
                    $('<span>').addClass('pdv-fiado-debito-valor').text('R$ ' + formatarMoeda(d.valor || 0)),
                    $('<span>').addClass('pdv-fiado-debito-pago').text('Pago: R$ ' + formatarMoeda(d.valor_pago || 0)),
                    $('<span>').addClass('pdv-fiado-debito-saldo').text('Saldo: R$ ' + formatarMoeda(d.saldo || 0))
                );
                $lista.append($item);
            }

            // Total d\u00e9bito
            var resumo = r.resumo || {};
            $('#receber-fiado-total-aberto').text('R$ ' + formatarMoeda(resumo.debito_atual || 0));

            // Limpar campo de valor e forma
            $('#receber-fiado-valor').val('');
            $('#receber-fiado-forma').val('dinheiro');

            setTimeout(function() { $('#receber-fiado-valor').trigger('focus'); }, 100);
        },
        error: function() {
            pdvToast('Erro de conex\u00e3o ao carregar d\u00e9bitos.', 'error');
        }
    });
}

/**
 * Processar recebimento de fiado
 */
$(document).on('click', '#btn-confirmar-receber-fiado', processarRecebimentoFiado);
$(document).on('keydown', '#receber-fiado-valor', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        processarRecebimentoFiado();
    }
});

function processarRecebimentoFiado() {
    var customerId = $('#receber-fiado-etapa2').data('customer-id');
    var valorRaw = $('#receber-fiado-valor').val().replace(/[^\d,]/g, '').replace(',', '.');
    var valor = parseFloat(valorRaw) || 0;
    var forma = $('#receber-fiado-forma').val();

    if (valor <= 0) {
        pdvToast('Informe o valor do recebimento.', 'warning');
        $('#receber-fiado-valor').trigger('focus');
        return;
    }
    if (!forma) {
        pdvToast('Selecione a forma de pagamento.', 'warning');
        return;
    }

    $('#btn-confirmar-receber-fiado').prop('disabled', true);

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/receber_fiado',
        type: 'POST',
        dataType: 'json',
        data: {
            [PDV_CONFIG.csrf_name]: csrfHash,
            customer_id: customerId,
            valor: valor,
            forma_pagamento: forma
        },
        timeout: 15000,
        success: function(r) {
            atualizarCsrf(r);
            $('#btn-confirmar-receber-fiado').prop('disabled', false);

            if (r.success) {
                somSucesso();
                // Etapa 3 — recibo
                $('#receber-fiado-etapa2').hide();
                $('#receber-fiado-etapa3').show();

                var recibo = r.recibo || {};
                var $recibo = $('#receber-fiado-recibo').empty();

                $recibo.append(
                    $('<div>').addClass('pdv-fiado-recibo-titulo').text('RECIBO DE PAGAMENTO \u2014 FIADO'),
                    $('<div>').addClass('pdv-fiado-recibo-linha').text('Cliente: ' + (recibo.cliente_nome || '')),
                    $('<div>').addClass('pdv-fiado-recibo-linha').text('Valor recebido: R$ ' + formatarMoeda(recibo.valor_recebido || 0)),
                    $('<div>').addClass('pdv-fiado-recibo-linha').text('Forma: ' + (recibo.forma_pagamento || '')),
                    $('<div>').addClass('pdv-fiado-recibo-linha').text('Saldo anterior: R$ ' + formatarMoeda(recibo.saldo_anterior || 0)),
                    $('<div>').addClass('pdv-fiado-recibo-linha').text('Saldo atual: R$ ' + formatarMoeda(recibo.saldo_atual || 0))
                );

                // Abatimentos
                var abatimentos = r.abatimentos || [];
                if (abatimentos.length > 0) {
                    $recibo.append($('<div>').addClass('pdv-fiado-recibo-separador'));
                    $recibo.append($('<div>').addClass('pdv-fiado-recibo-subtitulo').text('Abatimentos:'));
                    for (var i = 0; i < abatimentos.length; i++) {
                        var ab = abatimentos[i];
                        $recibo.append(
                            $('<div>').addClass('pdv-fiado-recibo-abatimento').text(
                                'NF ' + (ab.invoice_id || '-') + ' \u2014 R$ ' + formatarMoeda(ab.valor_abatido || 0) +
                                (ab.quitado ? ' (QUITADO)' : '')
                            )
                        );
                    }
                }
            } else {
                pdvToast(r.message || 'Erro ao processar recebimento.', 'error');
            }
        },
        error: function() {
            $('#btn-confirmar-receber-fiado').prop('disabled', false);
            pdvToast('Erro de conex\u00e3o.', 'error');
        }
    });
}

/**
 * Imprimir recibo de recebimento
 */
$(document).on('click', '#btn-imprimir-recibo-fiado', function() {
    var conteudo = $('#receber-fiado-recibo').html();
    var win = window.open('', '_blank', 'width=400,height=600');
    win.document.write('<html><head><title>Recibo Fiado</title>');
    win.document.write('<style>body{font-family:monospace;font-size:12px;padding:10px;} .pdv-fiado-recibo-titulo{font-weight:bold;text-align:center;margin-bottom:10px;} .pdv-fiado-recibo-linha{margin:3px 0;} .pdv-fiado-recibo-separador{border-top:1px dashed #000;margin:8px 0;} .pdv-fiado-recibo-subtitulo{font-weight:bold;margin:5px 0;}</style>');
    win.document.write('</head><body>');
    win.document.write(conteudo);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
});

/**
 * Voltar da etapa 3 (recibo) para fechar
 */
$(document).on('click', '#btn-fechar-recibo-fiado', fecharReceberFiado);

/**
 * Voltar da etapa 2 para etapa 1
 */
$(document).on('click', '#btn-voltar-receber-fiado', function() {
    $('#receber-fiado-etapa2').hide();
    $('#receber-fiado-etapa1').show();
    setTimeout(function() { $('#receber-fiado-busca').trigger('focus'); }, 100);
});

/**
 * Escape HTML helper (seguran\u00e7a XSS)
 */
function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// ---- 10. Integrar Display Cliente nas funções existentes ----

// Patch adicionarItem — enviar display após adição
var _adicionarItemOriginal = adicionarItem;
adicionarItem = function(item) {
    _adicionarItemOriginal(item);
    enviarDisplayCliente('item_adicionado', {
        nome: item.generico ? 'GEN - ' + item.descricao_manual : item.nome,
        quantidade: item.quantidade,
        preco: item.preco,
        subtotal: item.subtotal,
        total_venda: calcularTotalVenda(),
        itens_count: itensCarrinho.length
    });
};

// Patch cancelarUltimoItem — enviar display após remoção
var _cancelarUltimoItemOriginal = cancelarUltimoItem;
cancelarUltimoItem = function() {
    _cancelarUltimoItemOriginal();
    enviarDisplayCliente('item_removido', {
        total_venda: calcularTotalVenda(),
        itens_count: itensCarrinho.length
    });
};

// Patch atualizarTotais — enviar display
var _atualizarTotaisOriginal = atualizarTotais;
atualizarTotais = function() {
    _atualizarTotaisOriginal();
    enviarDisplayCliente('total_atualizado', {
        total: calcularTotalVenda(),
        itens_count: itensCarrinho.length
    });
};

// Patch exibirTelaFinalizada — enviar display
var _exibirTelaFinalizadaOriginal = exibirTelaFinalizada;
exibirTelaFinalizada = function(resultado) {
    _exibirTelaFinalizadaOriginal(resultado);
    enviarDisplayCliente('venda_finalizada', {
        total: resultado.total || calcularTotalVenda(),
        troco: resultado.troco || 0,
        forma: resultado.forma_pagamento || ''
    });
};

// Patch voltarParaIdle — enviar display
var _voltarParaIdleOriginal = voltarParaIdle;
voltarParaIdle = function() {
    _voltarParaIdleOriginal();
    enviarDisplayCliente('idle', {});
};

/* --- Helpers auxiliares (Fase 9) --- */

/**
 * Som breve de tecla (beep suave)
 */
function somTecla() {
    if (!PDV_CONFIG.som_feedback) return;
    try {
        var ctx = getAudioCtx();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 800;
        gain.gain.value = 0.05;
        osc.start();
        osc.stop(ctx.currentTime + 0.04);
    } catch (e) {}
}

/**
 * Exibe alerta temporário na tela do PDV
 * @param {string} msg
 * @param {string} tipo  success|warning|danger|info
 */
function exibirAlerta(msg, tipo) {
    tipo = tipo || 'info';
    var cores = {
        success: '#16a34a',
        warning: '#d97706',
        danger:  '#dc2626',
        info:    '#2563eb'
    };
    var cor = cores[tipo] || cores.info;
    var $el = $('<div>').css({
        position: 'fixed',
        top: '20px',
        left: '50%',
        transform: 'translateX(-50%)',
        background: cor,
        color: '#fff',
        padding: '12px 28px',
        borderRadius: '8px',
        fontSize: '16px',
        fontWeight: '600',
        zIndex: 99999,
        boxShadow: '0 4px 12px rgba(0,0,0,.3)',
        whiteSpace: 'nowrap'
    }).text(msg).appendTo('body');
    setTimeout(function() { $el.fadeOut(400, function() { $el.remove(); }); }, 3000);
}

/**
 * Abre nova venda vazia a partir do estado idle (atalho por Enter).
 * Garante que o carrinho esteja limpo antes de entrar na tela de venda.
 */
function abrirNovaVendaVazia() {
    if (state !== 'idle') return;
    // Garantir limpeza completa antes de abrir a tela de venda
    clearTimeout(barcodeTimer);
    barcodeTimer     = null;
    bipandoEmAndamento = false;
    itensCarrinho    = [];
    itemSelecionado  = -1;
    sequencia        = 0;
    cpfCliente       = '';
    descontoVenda    = 0;
    descontoVendaTipo   = null;
    descontoVendaValor  = 0;
    descontoAutorizadoPor = null;
    pgtoFormaAtual   = null;
    pgtoModo         = null;
    pgtoParciais     = [];
    pgtoLockOwner    = null;
    pgtoFinalizando  = false;
    try { localStorage.removeItem(STORAGE_KEY); } catch(ex) {}
    $('#pdv-barcode-global').val('');
    $('#pdv-barcode').val('');
    $('#pdv-quantidade').val('1');
    $('#pdv-valor-unit').text('0,00');
    $('#pdv-total-item-valor').text('R$ 0,00');
    $('#pdv-total-valor').text('0,00');
    $('#pdv-total-desconto').addClass('pdv-hidden');
    $('#pdv-cupom-itens').empty();
    $('#pdv-cupom-count').text('0 itens');
    mostrarUltimoItem('Nenhum item adicionado');
    mudarEstado('venda');
}

/**
 * Inicia uma nova venda (atalho para mudar estado)
 */
function iniciarNovaVenda() {
    if (state === 'idle') {
        abrirNovaVendaVazia();
    } else {
        mudarEstado('venda');
    }
}

/* ====================================================================
 *  FASE 9 — DEVOLUÇÃO / TROCA
 * ==================================================================== */

/**
 * Abre o modal de devolução (F7 > 6)
 */
function abrirDevolucao() {
    if (state !== 'idle' && state !== 'venda') {
        somErro();
        return;
    }
    creditoDevolucao   = 0;
    devolucaoRefId     = null;
    devolucaoVendaData = null;

    $('#devolucao-busca-input').val('');
    $('#devolucao-itens-lista').empty();
    $('#devolucao-venda-info').hide();
    $('#devolucao-auth-supervisor').hide();
    $('#devolucao-resultado').hide();
    $('#devolucao-etapa-busca').show();
    $('#devolucao-etapa-itens').hide();

    $('#modal-devolucao').fadeIn(150, function() {
        $('#devolucao-busca-input').focus();
    });
    somTecla();
}

/**
 * Fecha modal de devolução
 */
function fecharDevolucao() {
    $('#modal-devolucao').fadeOut(150);
    devolucaoVendaData = null;
}

/**
 * Busca venda para devolução via AJAX
 */
function buscarVendaDevolucao() {
    var codigo = $('#devolucao-busca-input').val().trim();
    if (!codigo) {
        somErro();
        return;
    }

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/buscar_venda_devolucao',
        type: 'GET',
        dataType: 'json',
        data: { invoice_id: codigo },
        timeout: 15000,
        beforeSend: function() {
            $('#devolucao-busca-input').prop('disabled', true);
        },
        success: function(r) {
            $('#devolucao-busca-input').prop('disabled', false);
            if (!r.success) {
                exibirAlerta(r.message || 'Venda não encontrada', 'warning');
                somErro();
                return;
            }
            // Normalizar dados para uso interno
            devolucaoVendaData = {
                invoice_pk:        r.venda.id,
                invoice_id:        r.venda.invoice_id,
                total:             r.venda.grand_total,
                data:              r.venda.created_date || r.venda.date,
                cliente:           r.venda.operador_nome || 'Consumidor',
                dias_desde_venda:  r.venda.dias_desde_venda || 0,
                requer_supervisor: false,
                itens:             r.itens || []
            };
            renderizarItensDevolucao(devolucaoVendaData);
            somTecla();
        },
        error: function() {
            $('#devolucao-busca-input').prop('disabled', false);
            exibirAlerta('Erro ao buscar venda', 'danger');
            somErro();
        }
    });
}

/**
 * Renderiza os itens da venda no modal de devolução
 */
function renderizarItensDevolucao(dados) {
    // Info da venda
    var $info = $('#devolucao-venda-info');
    $info.find('.dev-venda-codigo').text(dados.invoice_id || '');
    $info.find('.dev-venda-data').text(dados.data || '');
    $info.find('.dev-venda-cliente').text(dados.cliente || 'Consumidor');
    $info.find('.dev-venda-total').text('R$ ' + formatarMoeda(dados.total || 0));
    $info.show();

    // Itens
    var $lista = $('#devolucao-itens-lista').empty();

    if (!dados.itens || dados.itens.length === 0) {
        $lista.append($('<div class="pdv-devolucao-vazio">').text('Nenhum item disponível para devolução'));
        $('#devolucao-etapa-busca').hide();
        $('#devolucao-etapa-itens').show();
        return;
    }

    $.each(dados.itens, function(i, item) {
        var maxDev   = parseFloat(item.max_devolver) || 0;
        var disabled = maxDev <= 0;
        var preco    = parseFloat(item.preco) || parseFloat(item.price) || 0;

        var $row = $('<div class="pdv-devolucao-item">');
        if (disabled) $row.addClass('pdv-devolucao-item-disabled');

        // Checkbox
        var $chk = $('<input type="checkbox" class="dev-item-check">')
            .prop('disabled', disabled)
            .data('index', i);

        // Info
        var $itemInfo = $('<div class="pdv-devolucao-item-info">');
        $itemInfo.append($('<span class="dev-item-nome">').text(item.product_name || ('Produto #' + item.product_id)));
        $itemInfo.append($('<span class="dev-item-detalhe">').text(
            'Vendido: ' + formatarQtd(item.qty_original || item.qty) + ' | Já devolvido: ' + formatarQtd(item.ja_devolvido || 0) + ' | Máx: ' + formatarQtd(maxDev)
        ));

        // Qty input
        var $qty = $('<input type="number" class="dev-item-qty" min="0" step="1">')
            .val(disabled ? 0 : maxDev)
            .attr('max', maxDev)
            .prop('disabled', disabled)
            .data('index', i)
            .data('max', maxDev)
            .data('price', preco);

        // Motivo
        var $motivo = $('<select class="dev-item-motivo">')
            .prop('disabled', disabled)
            .append($('<option value="defeito">').text('Defeito'))
            .append($('<option value="arrependimento">').text('Arrependimento'))
            .append($('<option value="troca">').text('Troca'))
            .append($('<option value="erro_caixa">').text('Erro caixa'));

        // Subtotal
        var $sub = $('<span class="dev-item-subtotal">').text('R$ ' + formatarMoeda(disabled ? 0 : (maxDev * preco)));

        $row.append($chk).append($itemInfo).append($qty).append($motivo).append($sub);

        // Store item data
        $row.data('item', item);
        $lista.append($row);

        // Auto-check if not disabled
        if (!disabled) $chk.prop('checked', true);
    });

    $('#devolucao-etapa-busca').hide();
    $('#devolucao-etapa-itens').show();
    calcularTotalDevolucao();
}

/**
 * Calcula o total da devolução baseado nos itens selecionados
 */
function calcularTotalDevolucao() {
    var total = 0;
    $('#devolucao-itens-lista .pdv-devolucao-item').each(function() {
        var $row = $(this);
        var checked = $row.find('.dev-item-check').is(':checked');
        if (!checked) {
            $row.find('.dev-item-subtotal').text('R$ 0,00');
            return;
        }
        var qty   = parseFloat($row.find('.dev-item-qty').val()) || 0;
        var price = $row.find('.dev-item-qty').data('price') || 0;
        var sub   = qty * price;
        $row.find('.dev-item-subtotal').text('R$ ' + formatarMoeda(sub));
        total += sub;
    });
    $('#devolucao-total-valor').text('R$ ' + formatarMoeda(total));
    return total;
}

/**
 * Formata quantidade (inteiro se possível, senão 3 casas)
 */
function formatarQtd(v) {
    v = parseFloat(v) || 0;
    return (v === Math.floor(v)) ? v.toFixed(0) : v.toFixed(3).replace('.', ',');
}

/**
 * Verifica se precisa de autorização de supervisor
 */
function verificarAutorizacaoDevolucao() {
    var total = calcularTotalDevolucao();
    if (total <= 0) {
        exibirAlerta('Selecione pelo menos um item', 'warning');
        somErro();
        return;
    }

    var dias = devolucaoVendaData ? (devolucaoVendaData.dias_desde_venda || 0) : 0;
    var needAuth = devolucaoVendaData ? (devolucaoVendaData.requer_supervisor || false) : false;

    if (needAuth) {
        $('#devolucao-auth-supervisor').show();
        $('#devolucao-supervisor-senha').val('').focus();
    } else {
        confirmarDevolucao(null);
    }
}

/**
 * Confirma a devolução (envia ao servidor)
 */
function confirmarDevolucao(senhaSuper) {
    var itens = [];
    $('#devolucao-itens-lista .pdv-devolucao-item').each(function() {
        var $row = $(this);
        if (!$row.find('.dev-item-check').is(':checked')) return;

        var item  = $row.data('item');
        var qty   = parseFloat($row.find('.dev-item-qty').val()) || 0;
        var motivo = $row.find('.dev-item-motivo').val();

        if (qty > 0 && item) {
            itens.push({
                detail_id:  item.detail_id,
                product_id: item.product_id,
                qty:        qty,
                price:      parseFloat(item.preco) || parseFloat(item.price) || 0,
                motivo:     motivo
            });
        }
    });

    if (itens.length === 0) {
        exibirAlerta('Nenhum item selecionado', 'warning');
        somErro();
        return;
    }

    var postData = {
        csrf_test_name: $('input[name="csrf_test_name"]').val(),
        invoice_id:     devolucaoVendaData.invoice_id,
        invoice_pk:     devolucaoVendaData.invoice_pk,
        itens:          JSON.stringify(itens)
    };
    if (senhaSuper) {
        postData.senha_supervisor = senhaSuper;
    }

    $.ajax({
        url: PDV_CONFIG.base_url + 'pdv/processar_devolucao',
        type: 'POST',
        dataType: 'json',
        data: postData,
        timeout: 30000,
        beforeSend: function() {
            $('#btn-confirmar-devolucao').prop('disabled', true);
        },
        success: function(r) {
            $('#btn-confirmar-devolucao').prop('disabled', false);
            if (r.csrf_token) {
                $('input[name="csrf_test_name"]').val(r.csrf_token);
            }
            if (!r.success) {
                // Se o servidor pede autorização de supervisor
                if (r.requer_supervisor) {
                    $('#devolucao-auth-supervisor').show();
                    $('#devolucao-supervisor-senha').val('').focus();
                    exibirAlerta(r.message || 'Autorização necessária', 'warning');
                } else {
                    exibirAlerta(r.message || 'Erro ao processar devolução', 'danger');
                }
                somErro();
                return;
            }
            mostrarResultadoDevolucao(r);
            somSucesso();
        },
        error: function() {
            $('#btn-confirmar-devolucao').prop('disabled', false);
            exibirAlerta('Falha na comunicação', 'danger');
            somErro();
        }
    });
}

/**
 * Mostra o resultado da devolução e opção de troca
 */
function mostrarResultadoDevolucao(r) {
    devolucaoRefId = r.return_id || null;
    var totalDev   = parseFloat(r.total) || 0;

    $('#devolucao-etapa-itens').hide();
    $('#devolucao-auth-supervisor').hide();

    $('#devolucao-resultado-id').text(r.return_id || '');
    $('#devolucao-resultado-total').text('R$ ' + formatarMoeda(totalDev));
    $('#devolucao-resultado').show();

    // Abrir comprovante em nova janela
    if (r.return_pk) {
        window.open(PDV_CONFIG.base_url + 'pdv/comprovante_devolucao/' + r.return_pk, '_blank', 'width=350,height=600');
    }
}

/**
 * Inicia troca: fecha modal, aplica crédito e inicia nova venda
 */
function iniciarTroca() {
    var totalDev = calcularTotalDevolucao();
    creditoDevolucao = totalDev;

    fecharDevolucao();

    // Se estiver idle, iniciar nova venda
    if (state === 'idle') {
        iniciarNovaVenda();
    }

    renderizarCreditoDevolucao();
    exibirAlerta('Crédito de R$ ' + formatarMoeda(creditoDevolucao) + ' aplicado. Registre os itens da troca.', 'success');
    somSucesso();
}

/**
 * Renderiza a exibição do crédito de devolução na tela de venda e pagamento
 */
function renderizarCreditoDevolucao() {
    if (creditoDevolucao > 0) {
        var $bar = $('#credito-devolucao-bar');
        if ($bar.length) {
            $bar.text('CRÉDITO DEVOLUÇÃO: R$ ' + formatarMoeda(creditoDevolucao)).show();
        }
        var $linha = $('#pgto-credito-linha');
        if ($linha.length) {
            $linha.find('.pgto-credito-valor').text('R$ ' + formatarMoeda(creditoDevolucao));
            $linha.show();
        }
    } else {
        $('#credito-devolucao-bar').hide();
        $('#pgto-credito-linha').hide();
    }
}

/* --- Event bindings para devolução --- */

// Checkbox / qty change -> recalcular
$(document).on('change', '.dev-item-check, .dev-item-qty', function() {
    var $row = $(this).closest('.pdv-devolucao-item');
    var $qty = $row.find('.dev-item-qty');
    var max  = parseFloat($qty.data('max')) || 0;
    var val  = parseFloat($qty.val()) || 0;
    if (val > max) $qty.val(max);
    if (val < 0)   $qty.val(0);
    calcularTotalDevolucao();
});

// Buscar venda
$(document).on('click', '#btn-buscar-devolucao', function() {
    buscarVendaDevolucao();
});

// Enter no campo de busca
$(document).on('keydown', '#devolucao-busca-input', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        buscarVendaDevolucao();
    }
    if (e.key === 'Escape') {
        fecharDevolucao();
    }
});

// Confirmar devolução
$(document).on('click', '#btn-confirmar-devolucao', function() {
    verificarAutorizacaoDevolucao();
});

// Enter na senha supervisor
$(document).on('keydown', '#devolucao-supervisor-senha', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        confirmarDevolucao($(this).val());
    }
});

// Confirmar supervisor
$(document).on('click', '#btn-devolucao-auth-ok', function() {
    confirmarDevolucao($('#devolucao-supervisor-senha').val());
});

// Cancelar devolução
$(document).on('click', '#btn-cancelar-devolucao', function() {
    fecharDevolucao();
});

// Fechar pelo X
$(document).on('click', '#btn-fechar-devolucao', function() {
    fecharDevolucao();
});

// Troca SIM
$(document).on('click', '#btn-devolucao-troca-sim', function() {
    iniciarTroca();
});

// Troca NÃO
$(document).on('click', '#btn-devolucao-troca-nao', function() {
    creditoDevolucao = 0;
    devolucaoRefId   = null;
    fecharDevolucao();
});

// Escape global no modal
$(document).on('keydown', '#modal-devolucao', function(e) {
    if (e.key === 'Escape') {
        fecharDevolucao();
    }
});

/* --- Monkey-patches para integrar crédito de devolução --- */

// Patch preencherResumoPagamento para exibir crédito
var _preencherResumoPagamentoOrig = typeof preencherResumoPagamento === 'function' ? preencherResumoPagamento : null;
if (_preencherResumoPagamentoOrig) {
    preencherResumoPagamento = function() {
        _preencherResumoPagamentoOrig.apply(this, arguments);
        renderizarCreditoDevolucao();
    };
}

// Patch calcularTotalVenda para subtrair crédito
var _calcularTotalVendaOrig = typeof calcularTotalVenda === 'function' ? calcularTotalVenda : null;
if (_calcularTotalVendaOrig) {
    calcularTotalVenda = function() {
        var total = _calcularTotalVendaOrig.apply(this, arguments);
        // Se há crédito de devolução, mostrar na interface mas NÃO alterar o total retornado
        // O crédito é aplicado no pagamento, não no total da venda
        renderizarCreditoDevolucao();
        return total;
    };
}

// Patch limparVenda para resetar crédito
var _limparVendaOrig = typeof limparVenda === 'function' ? limparVenda : null;
if (_limparVendaOrig) {
    limparVenda = function() {
        creditoDevolucao  = 0;
        devolucaoRefId    = null;
        devolucaoVendaData = null;
        $('#credito-devolucao-bar').hide();
        $('#pgto-credito-linha').hide();
        _limparVendaOrig.apply(this, arguments);
    };
}

// Patch confirmarPagamento para incluir crédito e referência de devolução
var _confirmarPagamentoOrig = typeof confirmarPagamento === 'function' ? confirmarPagamento : null;
if (_confirmarPagamentoOrig) {
    confirmarPagamento = function() {
        // Inject credit data into the payment form/data before calling original
        if (creditoDevolucao > 0) {
            // Add hidden fields or modify post data
            var $form = $('#form-pagamento');
            if ($form.length) {
                // Remove old hidden fields
                $form.find('.dev-credit-hidden').remove();
                $form.append($('<input type="hidden" name="credito_devolucao" class="dev-credit-hidden">').val(creditoDevolucao));
                $form.append($('<input type="hidden" name="devolucao_ref_id" class="dev-credit-hidden">').val(devolucaoRefId || ''));
            }
        }
        return _confirmarPagamentoOrig.apply(this, arguments);
    };
}

})(jQuery);
