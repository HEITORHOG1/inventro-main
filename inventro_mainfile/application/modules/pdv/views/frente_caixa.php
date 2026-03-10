<!DOCTYPE html>
<html lang="pt-BR" class="pdv-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDV — Caixa <?php echo html_escape($terminal->numero); ?> | <?php echo html_escape($setting->title ?? 'Inventro'); ?></title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- PDV CSS -->
    <link rel="stylesheet" href="<?php echo base_url('application/modules/pdv/assets/css/pdv.css?v=' . time()); ?>">
</head>
<body>
    <div class="pdv-container" id="pdv-container" data-state="idle">

        <!-- ====================================================================
             ESTADO: CAIXA LIVRE (idle)
             ==================================================================== -->
        <div class="pdv-state pdv-state-idle" data-show-state="idle">
            <div class="pdv-caixa-livre">
                <div>
                    <h1>CAIXA LIVRE</h1>
                    <p>Bipe um produto ou pressione uma tecla para iniciar</p>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             ESTADO: CONSULTA DE PREÇO (consulta)
             ==================================================================== -->
        <div class="pdv-state pdv-state-consulta" data-show-state="consulta">
            <div class="pdv-consulta-wrapper">
                <div class="pdv-consulta-header">
                    <i class="fas fa-search-dollar"></i> MODO CONSULTA DE PREÇO
                    <small>(Esc para sair)</small>
                </div>
                <div class="pdv-consulta-body" id="pdv-consulta-body">
                    <p class="pdv-consulta-instrucao">Bipe ou digite o código do produto</p>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             ESTADO: VENDA EM CURSO (venda)
             ==================================================================== -->
        <div class="pdv-state pdv-state-venda" data-show-state="venda">
            <div class="pdv-venda-layout">

                <!-- ZONA 1: Painel de Entrada (esquerda) -->
                <div class="pdv-entrada">
                    <div class="pdv-barcode-area">
                        <input type="text"
                               id="pdv-barcode"
                               class="pdv-barcode-input"
                               placeholder="Código de barras / EAN"
                               autocomplete="off"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               name="pdv_barcode_nofill">
                    </div>

                    <div class="pdv-campos-info">
                        <div class="pdv-campo">
                            <label>QUANTIDADE</label>
                            <input type="text"
                                   id="pdv-quantidade"
                                   class="pdv-campo-valor"
                                   value="1"
                                   autocomplete="off">
                        </div>
                        <div class="pdv-campo">
                            <label>VALOR UNITÁRIO</label>
                            <span id="pdv-valor-unit" class="pdv-campo-valor pdv-readonly">0,00</span>
                        </div>
                    </div>

                    <div class="pdv-estoque-info pdv-hidden" id="pdv-estoque-info">
                        <i class="fas fa-boxes"></i>
                        <span id="pdv-estoque-texto">Estoque: --</span>
                    </div>

                    <div class="pdv-total-item">
                        <label>TOTAL ITEM</label>
                        <span id="pdv-total-item-valor">R$ 0,00</span>
                    </div>

                    <!-- Atalhos rápidos visíveis -->
                    <div class="pdv-atalhos-rapidos">
                        <span>F2 Finalizar</span>
                        <span>F3 Cancelar</span>
                        <span>F4 Rem.Último</span>
                        <span>F5 Buscar</span>
                        <span>F9 Desc.Item</span>
                        <span>F10 Desc.Venda</span>
                        <span>F12 Suspender</span>
                    </div>
                </div>

                <!-- ZONA 2: Espelho do Cupom (direita) -->
                <div class="pdv-cupom">
                    <div class="pdv-cupom-header">
                        <span>CUPOM DE VENDA</span>
                        <span id="pdv-cupom-count">0 itens</span>
                    </div>
                    <div class="pdv-cupom-itens" id="pdv-cupom-itens">
                        <!-- Itens inseridos dinamicamente pelo JS -->
                    </div>
                </div>
            </div>

            <!-- ZONA 3: Barra Total (inferior) -->
            <div class="pdv-total-bar">
                <div class="pdv-ultimo-item" id="pdv-ultimo-item">
                    Nenhum item adicionado
                </div>
                <div class="pdv-total-desconto pdv-hidden" id="pdv-total-desconto">
                    <i class="fas fa-tags"></i>
                    <span>Desc: -R$ <span id="pdv-total-desconto-valor">0,00</span></span>
                </div>
                <div class="pdv-credito-bar" id="credito-devolucao-bar" style="display:none"></div>
                <div class="pdv-total-geral">
                    <i class="fas fa-shopping-cart"></i>
                    <small>R$</small>
                    <span id="pdv-total-valor">0,00</span>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             ESTADO: PAGAMENTO (pagamento)
             ==================================================================== -->
        <div class="pdv-state pdv-state-pagamento" data-show-state="pagamento">
            <div class="pdv-pagamento-layout">
                <!-- Coluna esquerda: resumo da venda -->
                <div class="pdv-pagamento-resumo">
                    <div class="pdv-pagamento-header">
                        <i class="fas fa-shopping-cart"></i> RESUMO DA VENDA
                    </div>
                    <div class="pdv-pagamento-itens" id="pdv-pagamento-itens">
                        <!-- Itens renderizados pelo JS -->
                    </div>
                    <div class="pdv-pagamento-totais">
                        <div class="pdv-pagamento-linha">
                            <span>Subtotal</span>
                            <span id="pgto-subtotal">R$ 0,00</span>
                        </div>
                        <div class="pdv-pagamento-linha pdv-pagamento-desconto pdv-hidden" id="pgto-desconto-linha">
                            <span>Desconto</span>
                            <span id="pgto-desconto">- R$ 0,00</span>
                        </div>
                        <div class="pdv-pagamento-linha pdv-pagamento-credito" id="pgto-credito-linha" style="display:none">
                            <span>Crédito devolução</span>
                            <span id="pgto-credito" class="pgto-credito-valor">- R$ 0,00</span>
                        </div>
                        <div class="pdv-pagamento-linha pdv-pagamento-total-destaque">
                            <span>TOTAL A PAGAR</span>
                            <span id="pgto-total">R$ 0,00</span>
                        </div>
                    </div>
                </div>

                <!-- Coluna direita: formas de pagamento -->
                <div class="pdv-pagamento-formas">
                    <div class="pdv-pagamento-header">
                        <i class="fas fa-credit-card"></i> FORMA DE PAGAMENTO
                    </div>

                    <!-- Botões das formas de pagamento -->
                    <div class="pdv-pagamento-botoes" id="pgto-botoes">
                        <button class="pdv-pgto-btn" data-forma="dinheiro">
                            <span class="pdv-pgto-tecla">F1</span>
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Dinheiro</span>
                        </button>
                        <button class="pdv-pgto-btn" data-forma="debito">
                            <span class="pdv-pgto-tecla">F2</span>
                            <i class="fas fa-credit-card"></i>
                            <span>Débito</span>
                        </button>
                        <button class="pdv-pgto-btn" data-forma="pix">
                            <span class="pdv-pgto-tecla">F3</span>
                            <i class="fas fa-qrcode"></i>
                            <span>PIX</span>
                        </button>
                        <button class="pdv-pgto-btn" data-forma="fiado">
                            <span class="pdv-pgto-tecla">F4</span>
                            <i class="fas fa-handshake"></i>
                            <span>Fiado</span>
                        </button>
                        <button class="pdv-pgto-btn" data-forma="misto">
                            <span class="pdv-pgto-tecla">F5</span>
                            <i class="fas fa-layer-group"></i>
                            <span>Misto</span>
                        </button>
                    </div>

                    <!-- Área de interação da forma escolhida -->
                    <div class="pdv-pgto-area" id="pgto-area">
                        <p class="pdv-pgto-instrucao">Selecione a forma de pagamento</p>
                    </div>

                    <!-- Pagamentos parciais (modo misto) -->
                    <div class="pdv-pgto-parciais pdv-hidden" id="pgto-parciais">
                        <div class="pdv-pgto-parciais-header">
                            <span>Pagamentos adicionados:</span>
                        </div>
                        <div id="pgto-parciais-lista"></div>
                        <div class="pdv-pgto-parciais-total">
                            <span>VALOR PAGO:</span>
                            <span id="pgto-valor-pago">R$ 0,00</span>
                        </div>
                        <div class="pdv-pgto-parciais-total">
                            <span>RESTANTE:</span>
                            <span id="pgto-restante" class="pdv-pgto-restante-valor">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Área do troco (dinheiro) -->
                    <div class="pdv-pgto-troco pdv-hidden" id="pgto-troco">
                        <div class="pdv-pgto-troco-label">TROCO</div>
                        <div class="pdv-pgto-troco-valor" id="pgto-troco-valor">R$ 0,00</div>
                    </div>

                    <!-- Atalhos -->
                    <div class="pdv-pgto-atalhos">
                        <span>Esc = Voltar</span>
                        <span>Enter = Confirmar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             ESTADO: FINALIZADO (finalizado) — placeholder para Fase 5
             ==================================================================== -->
        <div class="pdv-state pdv-state-finalizado" data-show-state="finalizado">
            <div class="pdv-finalizado-wrapper">
                <div class="pdv-finalizado-icon"><i class="fas fa-check-circle"></i></div>
                <h1>OBRIGADO!</h1>
                <p id="pdv-finalizado-troco"></p>
                <p class="pdv-finalizado-tempo">Próxima venda em <span id="pdv-finalizado-timer">5</span>s</p>
            </div>
        </div>

        <!-- ====================================================================
             BARCODE GLOBAL (captura em qualquer estado)
             ==================================================================== -->
        <input type="text"
               id="pdv-barcode-global"
               class="pdv-barcode-global"
               autocomplete="off"
               autocorrect="off"
               autocapitalize="off"
               spellcheck="false"
               name="pdv_barcode_global_nofill"
               tabindex="-1">

        <!-- ====================================================================
             MODAIS
             ==================================================================== -->

        <!-- Modal: Busca por Nome (F5) -->
        <div class="pdv-modal" id="modal-busca-nome">
            <div class="pdv-modal-content pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-search"></i> Buscar Produto por Nome</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <input type="text"
                           id="busca-nome-input"
                           class="pdv-form-control"
                           placeholder="Digite o nome do produto..."
                           autocomplete="off">
                    <div class="pdv-busca-resultados" id="busca-nome-resultados">
                        <!-- Resultados preenchidos pelo JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Venda Suspensa — Suspender (F12 em venda) -->
        <div class="pdv-modal" id="modal-suspender">
            <div class="pdv-modal-content">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-pause-circle"></i> Suspender Venda</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Motivo (opcional)</label>
                        <input type="text"
                               id="suspender-motivo"
                               class="pdv-form-control"
                               placeholder="Ex: cliente foi buscar carteira"
                               maxlength="100"
                               autocomplete="off">
                    </div>
                    <button class="pdv-btn-primary" id="btn-confirmar-suspender">
                        <i class="fas fa-pause"></i> SUSPENDER VENDA
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Venda Suspensa — Recuperar (F12 em idle) -->
        <div class="pdv-modal" id="modal-recuperar">
            <div class="pdv-modal-content pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-play-circle"></i> Vendas Suspensas</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div id="lista-suspensas">
                        <p class="pdv-text-center">Carregando...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Produto Genérico (G) -->
        <div class="pdv-modal" id="modal-generico">
            <div class="pdv-modal-content">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-box-open"></i> Produto Genérico</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Departamento *</label>
                        <select id="generico-categoria" class="pdv-form-control">
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="pdv-form-group">
                        <label>Descrição *</label>
                        <input type="text"
                               id="generico-descricao"
                               class="pdv-form-control"
                               placeholder="Ex: Sacola grande"
                               maxlength="255"
                               autocomplete="off">
                    </div>
                    <div class="pdv-form-group">
                        <label>Preço Unitário (R$) *</label>
                        <input type="text"
                               id="generico-preco"
                               class="pdv-form-control"
                               placeholder="0,50"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>
                    <div class="pdv-form-group">
                        <label>Quantidade</label>
                        <input type="number"
                               id="generico-quantidade"
                               class="pdv-form-control"
                               value="1"
                               min="1"
                               step="1">
                    </div>
                    <button class="pdv-btn-primary" id="btn-confirmar-generico">
                        <i class="fas fa-plus"></i> ADICIONAR AO CARRINHO
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Quantidade (Q) -->
        <div class="pdv-modal" id="modal-editar-qtd">
            <div class="pdv-modal-content pdv-modal-sm">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-edit"></i> Alterar Quantidade</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <p id="editar-qtd-produto"></p>
                    <div class="pdv-form-group">
                        <label>Nova Quantidade</label>
                        <input type="number"
                               id="editar-qtd-input"
                               class="pdv-form-control"
                               min="0"
                               step="0.001"
                               autocomplete="off">
                    </div>
                    <button class="pdv-btn-primary" id="btn-confirmar-qtd">
                        <i class="fas fa-check"></i> CONFIRMAR
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: CPF na nota (F6) -->
        <div class="pdv-modal" id="modal-cpf">
            <div class="pdv-modal-content pdv-modal-sm">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-id-card"></i> CPF na Nota</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>CPF do Cliente</label>
                        <input type="text"
                               id="cpf-input"
                               class="pdv-form-control"
                               placeholder="000.000.000-00"
                               maxlength="14"
                               autocomplete="off"
                               inputmode="numeric">
                    </div>
                    <button class="pdv-btn-primary" id="btn-confirmar-cpf">
                        <i class="fas fa-check"></i> CONFIRMAR
                    </button>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             MODAIS — DESCONTOS (Fase 7)
             ==================================================================== -->

        <!-- Modal: Desconto por Item (F9) -->
        <div class="pdv-modal" id="modal-desconto-item">
            <div class="pdv-modal-content">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-percentage"></i> Desconto no Item (F9)</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-desconto-item-info" id="desconto-item-info">
                        <!-- Preenchido pelo JS -->
                    </div>

                    <div class="pdv-form-group">
                        <label>Tipo de Desconto</label>
                        <div class="pdv-desconto-tipo-group">
                            <label class="pdv-radio-label">
                                <input type="radio" name="desconto_item_tipo" value="percentual" checked>
                                <span>% Percentual</span>
                            </label>
                            <label class="pdv-radio-label">
                                <input type="radio" name="desconto_item_tipo" value="valor">
                                <span>R$ Valor</span>
                            </label>
                        </div>
                    </div>
                    <div class="pdv-form-group">
                        <label>Valor do Desconto</label>
                        <input type="text"
                               id="desconto-item-valor"
                               class="pdv-form-control pdv-input-desconto"
                               placeholder="0"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>

                    <!-- Preview em tempo real -->
                    <div class="pdv-desconto-preview" id="desconto-item-preview">
                        <div class="pdv-desconto-preview-linha">
                            <span>Subtotal original:</span>
                            <span id="desconto-item-original">R$ 0,00</span>
                        </div>
                        <div class="pdv-desconto-preview-linha pdv-desconto-preview-desc">
                            <span>Desconto:</span>
                            <span id="desconto-item-calc">- R$ 0,00</span>
                        </div>
                        <div class="pdv-desconto-preview-linha pdv-desconto-preview-novo">
                            <span>Novo subtotal:</span>
                            <span id="desconto-item-novo">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Supervisor (oculto até necessário) -->
                    <div class="pdv-supervisor-auth pdv-hidden" id="desconto-item-supervisor">
                        <div class="pdv-form-group">
                            <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                            <div class="pdv-senha-group">
                                <input type="password"
                                       id="desconto-item-senha"
                                       class="pdv-form-control"
                                       placeholder="Desconto acima do limite"
                                       autocomplete="off">
                                <button type="button" class="pdv-btn-eye" data-toggle-senha="desconto-item-senha">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-desconto-item">
                            <i class="fas fa-check"></i> APLICAR DESCONTO
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Desconto na Venda (F10) -->
        <div class="pdv-modal" id="modal-desconto-venda">
            <div class="pdv-modal-content">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-tags"></i> Desconto na Venda (F10)</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-desconto-venda-info" id="desconto-venda-info">
                        <!-- Preenchido pelo JS -->
                    </div>

                    <div class="pdv-form-group">
                        <label>Tipo de Desconto</label>
                        <div class="pdv-desconto-tipo-group">
                            <label class="pdv-radio-label">
                                <input type="radio" name="desconto_venda_tipo" value="percentual" checked>
                                <span>% Percentual</span>
                            </label>
                            <label class="pdv-radio-label">
                                <input type="radio" name="desconto_venda_tipo" value="valor">
                                <span>R$ Valor</span>
                            </label>
                        </div>
                    </div>
                    <div class="pdv-form-group">
                        <label>Valor do Desconto</label>
                        <input type="text"
                               id="desconto-venda-valor"
                               class="pdv-form-control pdv-input-desconto"
                               placeholder="0"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>

                    <!-- Preview em tempo real -->
                    <div class="pdv-desconto-preview" id="desconto-venda-preview">
                        <div class="pdv-desconto-preview-linha">
                            <span>Total da venda:</span>
                            <span id="desconto-venda-original">R$ 0,00</span>
                        </div>
                        <div class="pdv-desconto-preview-linha pdv-desconto-preview-desc">
                            <span>Desconto:</span>
                            <span id="desconto-venda-calc">- R$ 0,00</span>
                        </div>
                        <div class="pdv-desconto-preview-linha pdv-desconto-preview-novo">
                            <span>Novo total:</span>
                            <span id="desconto-venda-novo">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Supervisor (oculto até necessário) -->
                    <div class="pdv-supervisor-auth pdv-hidden" id="desconto-venda-supervisor">
                        <div class="pdv-form-group">
                            <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                            <div class="pdv-senha-group">
                                <input type="password"
                                       id="desconto-venda-senha"
                                       class="pdv-form-control"
                                       placeholder="Desconto acima do limite"
                                       autocomplete="off">
                                <button type="button" class="pdv-btn-eye" data-toggle-senha="desconto-venda-senha">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-desconto-venda">
                            <i class="fas fa-check"></i> APLICAR DESCONTO
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             MODAIS — CONTROLE DE CAIXA (Fase 6)
             ==================================================================== -->

        <!-- Modal: Menu do Caixa (F7) -->
        <div id="modal-menu-caixa" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-menu">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-bars"></i> Menu do Caixa (F7)</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-menu-opcoes" id="menu-caixa-opcoes">
                        <button class="pdv-menu-opcao" data-opcao="1">
                            <span class="pdv-menu-num">[1]</span> Sangria
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="2">
                            <span class="pdv-menu-num">[2]</span> Suprimento
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="3">
                            <span class="pdv-menu-num">[3]</span> Leitura X
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="4">
                            <span class="pdv-menu-num">[4]</span> Fechamento de Caixa
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="5">
                            <span class="pdv-menu-num">[5]</span> Trocar Operador
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="6">
                            <span class="pdv-menu-num">[6]</span> Devolução
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="7">
                            <span class="pdv-menu-num">[7]</span> Cancelar Último Cupom
                        </button>
                        <button class="pdv-menu-opcao" data-opcao="8">
                            <span class="pdv-menu-num">[8]</span> Cancelar Cupom por Número
                        </button>
                    </div>
                </div>
                <div class="pdv-modal-footer">
                    <span class="pdv-modal-hint">Pressione o número da opção ou ESC para sair</span>
                </div>
            </div>
        </div>

        <!-- Modal: Sangria (F7 > 1) -->
        <div id="modal-sangria" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-arrow-down"></i> Sangria</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Valor da Sangria (R$) *</label>
                        <input type="text"
                               id="sangria-valor"
                               class="pdv-form-control pdv-input-moeda"
                               placeholder="0,00"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>
                    <div class="pdv-form-group">
                        <label>Motivo *</label>
                        <input type="text"
                               id="sangria-motivo"
                               class="pdv-form-control"
                               placeholder="Ex: pagamento de fornecedor"
                               maxlength="255"
                               autocomplete="off">
                    </div>
                    <div class="pdv-supervisor-auth">
                        <div class="pdv-form-group">
                            <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                            <div class="pdv-senha-group">
                                <input type="password"
                                       id="sangria-senha"
                                       class="pdv-form-control"
                                       placeholder="Senha do supervisor"
                                       autocomplete="off">
                                <button type="button" class="pdv-btn-eye" data-toggle-senha="sangria-senha">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-sangria">
                            <i class="fas fa-check"></i> CONFIRMAR SANGRIA
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Suprimento (F7 > 2) -->
        <div id="modal-suprimento" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-arrow-up"></i> Suprimento</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Valor do Suprimento (R$) *</label>
                        <input type="text"
                               id="suprimento-valor"
                               class="pdv-form-control pdv-input-moeda"
                               placeholder="0,00"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>
                    <div class="pdv-form-group">
                        <label>Motivo *</label>
                        <input type="text"
                               id="suprimento-motivo"
                               class="pdv-form-control"
                               placeholder="Ex: reforço de troco"
                               maxlength="255"
                               autocomplete="off">
                    </div>
                    <div class="pdv-supervisor-auth">
                        <div class="pdv-form-group">
                            <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                            <div class="pdv-senha-group">
                                <input type="password"
                                       id="suprimento-senha"
                                       class="pdv-form-control"
                                       placeholder="Senha do supervisor"
                                       autocomplete="off">
                                <button type="button" class="pdv-btn-eye" data-toggle-senha="suprimento-senha">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-suprimento">
                            <i class="fas fa-check"></i> CONFIRMAR SUPRIMENTO
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Leitura X (F7 > 3) -->
        <div id="modal-leitura-x" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-file-alt"></i> Leitura X</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-leitura-x" id="leitura-x-conteudo">
                        <p class="pdv-text-center">Carregando...</p>
                    </div>
                </div>
                <div class="pdv-modal-footer">
                    <button class="pdv-btn-primary" id="btn-imprimir-leitura-x">
                        <i class="fas fa-print"></i> IMPRIMIR
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Fechamento de Caixa (F7 > 4) -->
        <div id="modal-fechamento" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-cash-register"></i> Fechamento de Caixa</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <!-- Resumo do caixa (oculto em fechamento cego) -->
                    <div class="pdv-fechamento-resumo" id="fechamento-resumo">
                        <p class="pdv-text-center">Carregando resumo...</p>
                    </div>

                    <div class="pdv-form-group">
                        <label>Valor Contado em Caixa (R$) *</label>
                        <input type="text"
                               id="fechamento-valor-contado"
                               class="pdv-form-control pdv-input-moeda"
                               placeholder="0,00"
                               autocomplete="off"
                               inputmode="decimal">
                    </div>
                    <div class="pdv-form-group">
                        <label>Observação</label>
                        <textarea id="fechamento-observacao"
                                  class="pdv-form-control"
                                  rows="3"
                                  placeholder="Observações sobre o fechamento..."
                                  maxlength="500"></textarea>
                    </div>

                    <!-- Resultado (aparece após submeter no modo cego) -->
                    <div class="pdv-fechamento-resultado pdv-hidden" id="fechamento-resultado">
                        <div class="pdv-fechamento-diferenca" id="fechamento-diferenca">
                            <!-- Preenchido pelo JS via .text() -->
                        </div>
                    </div>

                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-fechamento">
                            <i class="fas fa-lock"></i> CONFIRMAR FECHAMENTO
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Trocar Operador (F7 > 5) -->
        <div id="modal-troca-operador" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-user-tag"></i> Trocar Operador</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Matrícula *</label>
                        <input type="text"
                               id="troca-matricula"
                               class="pdv-form-control"
                               placeholder="Matrícula do operador"
                               autocomplete="off">
                    </div>
                    <div class="pdv-form-group">
                        <label>Senha *</label>
                        <div class="pdv-senha-group">
                            <input type="password"
                                   id="troca-senha"
                                   class="pdv-form-control"
                                   placeholder="Senha do operador"
                                   autocomplete="off">
                            <button type="button" class="pdv-btn-eye" data-toggle-senha="troca-senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-troca">
                            <i class="fas fa-exchange-alt"></i> TROCAR OPERADOR
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Cancelar Último Cupom (F7 > 7) -->
        <div id="modal-cancelar-cupom" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-ban"></i> Cancelar Cupom</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <!-- Informações do cupom (preenchido pelo JS via .text()) -->
                    <div class="pdv-cancelar-info" id="cancelar-cupom-info">
                        <p class="pdv-text-center">Carregando informações do cupom...</p>
                    </div>

                    <div id="cancelar-cupom-form" style="display:none">
                        <div class="pdv-form-group">
                            <label>Motivo do Cancelamento *</label>
                            <textarea id="cancelar-motivo"
                                      class="pdv-form-control"
                                      rows="3"
                                      placeholder="Informe o motivo do cancelamento"
                                      maxlength="500"
                                      required></textarea>
                        </div>
                        <div class="pdv-supervisor-auth">
                            <div class="pdv-form-group">
                                <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                                <div class="pdv-senha-group">
                                    <input type="password"
                                           id="cancelar-senha"
                                           class="pdv-form-control"
                                           placeholder="Senha do supervisor"
                                           autocomplete="off">
                                    <button type="button" class="pdv-btn-eye" data-toggle-senha="cancelar-senha">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="cancelar-venda-id" value="">
                        <div class="pdv-modal-actions">
                            <button class="pdv-btn-cancelar-cupom" id="btn-confirmar-cancelar">
                                <i class="fas fa-ban"></i> CONFIRMAR CANCELAMENTO
                            </button>
                            <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Cancelar Cupom por Número (F7 > 8) -->
        <div id="modal-cancelar-numero" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-search"></i> Cancelar Cupom por Número</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Número do Cupom</label>
                        <div class="pdv-input-busca-grupo">
                            <input type="text"
                                   id="cancelar-numero-cupom"
                                   class="pdv-form-control"
                                   placeholder="Ex: T250309ABCDE"
                                   autocomplete="off">
                            <button class="pdv-btn-primary pdv-btn-buscar" id="btn-buscar-cupom">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Informações do cupom encontrado (oculto até a busca) -->
                    <div class="pdv-hidden" id="cancelar-numero-resultado">
                        <div class="pdv-cancelar-info" id="cancelar-numero-info">
                            <!-- Preenchido pelo JS via .text() -->
                        </div>

                        <div class="pdv-form-group">
                            <label>Motivo do Cancelamento *</label>
                            <textarea id="cancelar-numero-motivo"
                                      class="pdv-form-control"
                                      rows="3"
                                      placeholder="Informe o motivo do cancelamento"
                                      maxlength="500"
                                      required></textarea>
                        </div>
                        <div class="pdv-supervisor-auth">
                            <div class="pdv-form-group">
                                <label><i class="fas fa-lock"></i> Senha do Supervisor *</label>
                                <div class="pdv-senha-group">
                                    <input type="password"
                                           id="cancelar-numero-senha"
                                           class="pdv-form-control"
                                           placeholder="Senha do supervisor"
                                           autocomplete="off">
                                    <button type="button" class="pdv-btn-eye" data-toggle-senha="cancelar-numero-senha">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="cancelar-numero-venda-id" value="">
                        <div class="pdv-modal-actions">
                            <button class="pdv-btn-cancelar-cupom" id="btn-confirmar-cancelar-numero">
                                <i class="fas fa-ban"></i> CONFIRMAR CANCELAMENTO
                            </button>
                            <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Reimpressão (F8) -->
        <div id="modal-reimprimir" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-menu">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-print"></i> Reimpressão (F8)</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-menu-opcoes" id="menu-reimprimir-opcoes">
                        <button class="pdv-menu-opcao" data-reimprimir="1">
                            <span class="pdv-menu-num">[1]</span> Último Cupom
                        </button>
                        <button class="pdv-menu-opcao" data-reimprimir="2">
                            <span class="pdv-menu-num">[2]</span> Por Número
                        </button>
                        <button class="pdv-menu-opcao" data-reimprimir="3">
                            <span class="pdv-menu-num">[3]</span> Leitura X
                        </button>
                    </div>

                    <!-- Campo para número do cupom (opção 2) -->
                    <div class="pdv-hidden pdv-mt-16" id="reimprimir-numero-grupo">
                        <div class="pdv-form-group">
                            <label>Número do Cupom</label>
                            <input type="text"
                                   id="reimprimir-numero-input"
                                   class="pdv-form-control"
                                   placeholder="Ex: T250309ABCDE"
                                   autocomplete="off">
                        </div>
                        <button class="pdv-btn-primary" id="btn-reimprimir-numero">
                            <i class="fas fa-print"></i> REIMPRIMIR
                        </button>
                    </div>
                </div>
                <div class="pdv-modal-footer">
                    <span class="pdv-modal-hint">Pressione o número da opção ou ESC para sair</span>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             MODAIS — FIADO / CREDIÁRIO (Fase 8)
             ==================================================================== -->

        <!-- Modal: Busca Cliente Fiado (F4 no pagamento) -->
        <div id="modal-fiado-cliente" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-handshake"></i> Venda Fiado — Identificar Cliente</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Buscar por nome, CPF ou telefone</label>
                        <input type="text"
                               id="fiado-busca-input"
                               class="pdv-form-control"
                               placeholder="Digite nome, CPF ou telefone..."
                               autocomplete="off">
                    </div>
                    <div class="pdv-fiado-resultados" id="fiado-resultados">
                    </div>

                    <!-- Resumo do crédito (aparece ao selecionar cliente) -->
                    <div class="pdv-fiado-resumo pdv-hidden" id="fiado-resumo">
                        <div class="pdv-fiado-cliente-info">
                            <strong id="fiado-cliente-nome"></strong>
                            <span id="fiado-cliente-cpf"></span>
                        </div>
                        <div class="pdv-fiado-credito-grid">
                            <div class="pdv-fiado-credito-item">
                                <span class="pdv-fiado-credito-label">Débito Atual</span>
                                <span class="pdv-fiado-credito-valor pdv-fiado-val-debito" id="fiado-debito-atual">R$ 0,00</span>
                            </div>
                            <div class="pdv-fiado-credito-item">
                                <span class="pdv-fiado-credito-label">Limite</span>
                                <span class="pdv-fiado-credito-valor" id="fiado-limite">R$ 0,00</span>
                            </div>
                            <div class="pdv-fiado-credito-item">
                                <span class="pdv-fiado-credito-label">Disponível</span>
                                <span class="pdv-fiado-credito-valor" id="fiado-disponivel">R$ 0,00</span>
                            </div>
                            <div class="pdv-fiado-credito-item">
                                <span class="pdv-fiado-credito-label">Esta Venda</span>
                                <span class="pdv-fiado-credito-valor" id="fiado-valor-venda">R$ 0,00</span>
                            </div>
                        </div>
                        <div id="fiado-status-msg" class="pdv-fiado-status-msg"></div>

                        <div class="pdv-supervisor-auth pdv-hidden" id="fiado-supervisor-area">
                            <div class="pdv-form-group">
                                <label><i class="fas fa-lock"></i> Senha do Supervisor (limite excedido) *</label>
                                <div class="pdv-senha-group">
                                    <input type="password"
                                           id="fiado-senha-supervisor"
                                           class="pdv-form-control"
                                           placeholder="Senha do supervisor"
                                           autocomplete="off">
                                    <button type="button" class="pdv-btn-eye" data-toggle-senha="fiado-senha-supervisor">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <button type="button" class="pdv-btn-fiado-supervisor" id="btn-validar-fiado-supervisor" style="margin-top:8px">
                                    <i class="fas fa-key"></i> Validar Supervisor
                                </button>
                            </div>
                        </div>

                        <input type="hidden" id="fiado-customer-id" value="">
                        <div class="pdv-modal-actions">
                            <button class="pdv-btn-confirmar" id="btn-confirmar-fiado">
                                <i class="fas fa-check"></i> CONFIRMAR FIADO
                            </button>
                            <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                        </div>
                    </div>

                    <div class="pdv-fiado-cadastro-rapido" id="fiado-cadastro-rapido-area">
                        <button class="pdv-btn-secondary" id="btn-abrir-cadastro-rapido">
                            <i class="fas fa-user-plus"></i> Cadastro Rápido
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Cadastro Rápido de Cliente -->
        <div id="modal-cadastro-rapido" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-user-plus"></i> Cadastro Rápido de Cliente</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div class="pdv-form-group">
                        <label>Nome Completo *</label>
                        <input type="text" id="cadastro-rapido-nome" class="pdv-form-control"
                               placeholder="Nome do cliente" maxlength="255" autocomplete="off">
                    </div>
                    <div class="pdv-form-group">
                        <label>Telefone *</label>
                        <input type="text" id="cadastro-rapido-telefone" class="pdv-form-control"
                               placeholder="(00) 00000-0000" maxlength="15" autocomplete="off" inputmode="tel">
                    </div>
                    <div class="pdv-form-group">
                        <label>CPF (opcional)</label>
                        <input type="text" id="cadastro-rapido-cpf" class="pdv-form-control"
                               placeholder="000.000.000-00" maxlength="14" autocomplete="off" inputmode="numeric">
                    </div>
                    <div id="cadastro-rapido-erro" class="pdv-fiado-cadastro-erro pdv-hidden"></div>
                    <div class="pdv-modal-actions">
                        <button class="pdv-btn-confirmar" id="btn-confirmar-cadastro-rapido">
                            <i class="fas fa-check"></i> CADASTRAR E SELECIONAR
                        </button>
                        <button class="pdv-btn-cancelar" data-close-modal>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Receber Fiado (F11) -->
        <div id="modal-receber-fiado" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-hand-holding-usd"></i> Receber Fiado (F11)</h3>
                    <span class="pdv-modal-close" data-close-modal>&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <div id="receber-fiado-etapa1">
                        <div class="pdv-form-group">
                            <label>Buscar cliente</label>
                            <input type="text" id="receber-fiado-busca" class="pdv-form-control"
                                   placeholder="Nome, CPF ou telefone..." autocomplete="off">
                        </div>
                        <div class="pdv-fiado-resultados" id="receber-fiado-resultados"></div>
                    </div>

                    <div id="receber-fiado-etapa2" class="pdv-hidden">
                        <div class="pdv-fiado-cliente-info pdv-mb-24">
                            <strong id="receber-fiado-cliente-nome"></strong>
                            <span id="receber-fiado-cliente-tel"></span>
                        </div>
                        <div class="pdv-fiado-debitos-lista" id="receber-fiado-debitos"></div>
                        <div class="pdv-fiado-total-debito">
                            <span>TOTAL EM ABERTO:</span>
                            <span id="receber-fiado-total-aberto">R$ 0,00</span>
                        </div>
                        <div class="pdv-form-group">
                            <label>Valor a Receber (R$) *</label>
                            <input type="text" id="receber-fiado-valor" class="pdv-form-control pdv-input-moeda"
                                   placeholder="0,00" autocomplete="off" inputmode="decimal">
                        </div>
                        <div class="pdv-form-group">
                            <label>Forma de Pagamento *</label>
                            <select id="receber-fiado-forma" class="pdv-form-control">
                                <option value="">Selecione...</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="debito">Cartão Débito</option>
                                <option value="credito">Cartão Crédito</option>
                                <option value="pix">PIX</option>
                            </select>
                        </div>
                        <input type="hidden" id="receber-fiado-customer-id" value="">
                        <div class="pdv-modal-actions">
                            <button class="pdv-btn-confirmar" id="btn-confirmar-receber-fiado">
                                <i class="fas fa-check"></i> RECEBER PAGAMENTO
                            </button>
                            <button class="pdv-btn-cancelar" id="btn-voltar-receber-fiado">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </button>
                        </div>
                    </div>

                    <div id="receber-fiado-etapa3" class="pdv-hidden">
                        <div class="pdv-fiado-recibo" id="receber-fiado-recibo"></div>
                        <div class="pdv-fiado-recibo-acoes">
                            <button class="pdv-btn-confirmar" id="btn-imprimir-recibo-fiado">
                                <i class="fas fa-print"></i> IMPRIMIR RECIBO
                            </button>
                            <button class="pdv-btn-cancelar" id="btn-fechar-recibo-fiado">
                                <i class="fas fa-times"></i> FECHAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             Modal: Devolução (F7 > 6) — Fase 9
             ==================================================================== -->
        <div id="modal-devolucao" class="pdv-modal-overlay" style="display:none">
            <div class="pdv-modal pdv-modal-lg">
                <div class="pdv-modal-header">
                    <h3><i class="fas fa-undo-alt"></i> Devolução de Produtos</h3>
                    <span class="pdv-modal-close" id="btn-fechar-devolucao">&times;</span>
                </div>
                <div class="pdv-modal-body">
                    <!-- Etapa 1: Buscar cupom -->
                    <div id="devolucao-etapa-busca">
                        <div class="pdv-form-group">
                            <label>Número do Cupom</label>
                            <div class="pdv-input-busca-grupo">
                                <input type="text"
                                       id="devolucao-busca-input"
                                       class="pdv-form-control"
                                       placeholder="Ex: T250309ABCDE"
                                       autocomplete="off">
                                <button class="pdv-btn-primary pdv-btn-buscar" id="btn-buscar-devolucao">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 2: Selecionar itens -->
                    <div id="devolucao-etapa-itens" style="display:none">
                        <div class="pdv-devolucao-venda-info" id="devolucao-venda-info" style="display:none">
                            <strong>Venda:</strong> <span class="dev-venda-codigo"></span> |
                            <strong>Data:</strong> <span class="dev-venda-data"></span> |
                            <strong>Cliente:</strong> <span class="dev-venda-cliente"></span> |
                            <strong>Total:</strong> <span class="dev-venda-total"></span>
                        </div>
                        <div class="pdv-devolucao-itens-lista" id="devolucao-itens-lista"></div>

                        <div class="pdv-form-group pdv-mt-16">
                            <label>Motivo da devolução *</label>
                            <select id="devolucao-motivo" class="pdv-form-control">
                                <option value="">Selecione o motivo...</option>
                                <option value="defeito">Defeito no produto</option>
                                <option value="arrependimento">Arrependimento do cliente</option>
                                <option value="troca">Troca de produto</option>
                                <option value="erro_caixa">Erro do caixa</option>
                            </select>
                        </div>
                        <div class="pdv-form-group">
                            <label>Detalhes (opcional)</label>
                            <textarea id="devolucao-detalhe"
                                      class="pdv-form-control"
                                      rows="2"
                                      placeholder="Detalhes adicionais..."
                                      maxlength="500"></textarea>
                        </div>

                        <div class="pdv-supervisor-auth" id="devolucao-auth-supervisor" style="display:none">
                            <div class="pdv-form-group">
                                <label><i class="fas fa-lock"></i> Senha do Supervisor * (requerida)</label>
                                <div class="pdv-senha-group">
                                    <input type="password"
                                           id="devolucao-supervisor-senha"
                                           class="pdv-form-control"
                                           placeholder="Senha do supervisor"
                                           autocomplete="off">
                                    <button type="button" class="pdv-btn-eye" data-toggle-senha="devolucao-supervisor-senha">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="pdv-btn-primary" id="btn-devolucao-auth-ok">OK</button>
                                </div>
                                <small id="devolucao-supervisor-motivo" style="color:#d32f2f;display:block;margin-top:4px"></small>
                            </div>
                        </div>

                        <div class="pdv-devolucao-total">
                            <span>TOTAL DEVOLUÇÃO:</span>
                            <span id="devolucao-total-valor">R$ 0,00</span>
                        </div>

                        <div class="pdv-modal-actions">
                            <button class="pdv-btn-danger" id="btn-confirmar-devolucao">
                                <i class="fas fa-undo-alt"></i> CONFIRMAR DEVOLUÇÃO
                            </button>
                            <button class="pdv-btn-cancelar" id="btn-cancelar-devolucao">Cancelar</button>
                        </div>
                    </div>

                    <!-- Etapa 3: Resultado -->
                    <div id="devolucao-resultado" style="display:none">
                        <div class="pdv-text-center" style="padding:20px 0">
                            <div style="font-size:60px;color:#16a34a;margin-bottom:16px">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>Devolução processada!</h3>
                            <p style="margin:12px 0;color:#64748b">
                                Código: <strong id="devolucao-resultado-id"></strong><br>
                                Total devolvido: <strong id="devolucao-resultado-total"></strong>
                            </p>
                            <div id="devolucao-troca-opcoes">
                                <p style="font-weight:700;margin:16px 0 8px">Iniciar venda para troca?</p>
                                <div class="pdv-modal-actions" style="justify-content:center">
                                    <button class="pdv-btn-confirmar" id="btn-devolucao-troca-sim">
                                        <i class="fas fa-exchange-alt"></i> SIM — INICIAR TROCA
                                    </button>
                                    <button class="pdv-btn-cancelar" id="btn-devolucao-troca-nao">
                                        NÃO — VOLTAR AO CAIXA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             STATUS BAR (rodapé)
             ==================================================================== -->
        <div class="pdv-status-bar">
            <span><i class="fas fa-store"></i> LOJA: <?php echo html_escape($setting->title ?? 'Inventro'); ?></span>
            <span><i class="fas fa-cash-register"></i> CAIXA: <?php echo html_escape($terminal->numero); ?></span>
            <span>Série: <?php echo html_escape($terminal->serie_nfce ?? '1'); ?></span>
            <span class="<?php echo ($terminal->ambiente === 'producao') ? 'status-active' : 'status-warning'; ?>">
                <?php echo ($terminal->ambiente === 'producao') ? 'PRODUÇÃO' : 'HOMOLOGAÇÃO'; ?>
            </span>
            <span class="status-active"><i class="fas fa-user"></i> OP: <?php echo html_escape($operador->nome); ?></span>
            <span id="pdv-clock"></span>
            <span>v1.0</span>
        </div>

    </div>

    <!-- ====================================================================
         Configuração PHP → JS (sem interpolar dados do usuário em JS)
         ==================================================================== -->
    <script>
    var PDV_CONFIG = <?php echo json_encode([
        'base_url'            => base_url(),
        'csrf_name'           => $this->security->get_csrf_token_name(),
        'csrf_hash'           => $this->security->get_csrf_hash(),
        'terminal_id'         => (int) $terminal->id,
        'terminal_numero'     => $terminal->numero,
        'caixa_id'            => (int) $caixa->id,
        'operador_id'         => (int) $operador->id,
        'operador_nome'       => $operador->nome,
        'som_feedback'        => (getenv('PDV_SOM_FEEDBACK') ?: 'true') === 'true',
        'generico_habilitado'         => (getenv('PDV_GENERICO_HABILITADO') ?: 'true') === 'true',
        'suspensa_max'                => (int) (getenv('PDV_SUSPENSA_MAX_POR_TERMINAL') ?: 3),
        'permitir_venda_sem_estoque'  => (getenv('PDV_PERMITIR_VENDA_SEM_ESTOQUE') ?: 'false') === 'true',
        'auto_imprimir'               => (getenv('PDV_AUTO_IMPRIMIR') ?: 'true') === 'true',
        'display_habilitado'          => (getenv('PDV_DISPLAY_HABILITADO') ?: 'false') === 'true',
        'fechamento_cego'             => (getenv('PDV_FECHAMENTO_CEGO') ?: 'false') === 'true',
        'desconto_limite_pct'         => (float) (getenv('PDV_DESCONTO_OPERADOR') ?: 5),
    ]); ?>;
    </script>

    <!-- JS -->
    <script src="<?php echo base_url('admin_assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('application/modules/pdv/assets/js/pdv.js?v=' . time()); ?>"></script>
</body>
</html>
