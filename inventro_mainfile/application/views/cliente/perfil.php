<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - <?php echo html_escape($loja->title ?? 'Inventro'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --bg-dark: #1a1a2e;
            --bg-card: #16213e;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --success: #00c853;
            --danger: #f44336;
            --warning: #ff9800;
            --blue: #3498db;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 50%, #0f3460 100%);
            min-height: 100vh;
            color: var(--text-primary);
            padding-bottom: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Page Title */
        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 24px 0 16px;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .page-title h1 i {
            color: var(--primary);
            margin-right: 8px;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: var(--danger);
        }

        .alert-success {
            background: rgba(0, 200, 83, 0.15);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: var(--success);
        }

        /* Section Card */
        .section-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.3);
        }

        .form-control.readonly {
            background: rgba(255,255,255,0.02);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        .form-note {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 4px;
            font-style: italic;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 100px;
            gap: 16px;
        }

        /* Submit Button */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-password {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        .btn-password:hover {
            background: rgba(255,255,255,0.15);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .page-title h1 {
                font-size: 1.2rem;
            }

            .form-row,
            .form-row-3 {
                grid-template-columns: 1fr;
            }

            .section-card {
                padding: 18px;
            }
        }
    </style>
</head>
<body>

<?php $this->load->view('cliente/_header'); ?>

<div class="container">

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-user-circle"></i> Meu Perfil</h1>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo html_escape($erro); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo html_escape($sucesso); ?>
        </div>
    <?php endif; ?>

    <!-- Section 1: Personal Data -->
    <div class="section-card">
        <div class="section-title">
            <i class="fas fa-id-card"></i> Dados Pessoais
        </div>

        <form method="POST" action="<?php echo base_url('cliente/perfil'); ?>" id="formPerfil">
            <input type="hidden" name="csrf_test_name" value="<?php echo htmlspecialchars($this->security->get_csrf_hash(), ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label class="form-label" for="name">Nome completo</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?php echo htmlspecialchars($customer->name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Seu nome completo" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="text" class="form-control readonly" id="email" name="email"
                       value="<?php echo htmlspecialchars($customer->email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       readonly>
                <span class="form-note">E-mail nao pode ser alterado</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="mobile">Telefone</label>
                    <input type="text" class="form-control" id="mobile" name="mobile"
                           value="<?php echo htmlspecialchars($customer->mobile ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="(00) 00000-0000" maxlength="15">
                </div>

                <div class="form-group">
                    <label class="form-label" for="cpf">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf"
                           value="<?php echo htmlspecialchars($customer->cpf ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="000.000.000-00" maxlength="14">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="cep">CEP</label>
                <input type="text" class="form-control" id="cep" name="cep"
                       value="<?php echo htmlspecialchars($customer->cep ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="00000-000" maxlength="9">
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Endereco</label>
                <input type="text" class="form-control" id="address" name="address"
                       value="<?php echo htmlspecialchars($customer->address ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Rua, numero, complemento">
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="cidade">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade"
                           value="<?php echo htmlspecialchars($customer->cidade ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Cidade">
                </div>

                <div class="form-group">
                    <label class="form-label" for="estado">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado"
                           value="<?php echo htmlspecialchars($customer->estado ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="UF" maxlength="2">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Salvar Alteracoes
            </button>
        </form>
    </div>

    <!-- Section 2: Change Password -->
    <div class="section-card">
        <div class="section-title">
            <i class="fas fa-lock"></i> Alterar Senha
        </div>

        <form method="POST" action="<?php echo base_url('cliente/alterar-senha'); ?>" id="formSenha">
            <input type="hidden" name="csrf_test_name" value="<?php echo htmlspecialchars($this->security->get_csrf_hash(), ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label class="form-label" for="senha_atual">Senha Atual</label>
                <input type="password" class="form-control" id="senha_atual" name="senha_atual"
                       placeholder="Digite sua senha atual" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="nova_senha">Nova Senha</label>
                <input type="password" class="form-control" id="nova_senha" name="nova_senha"
                       placeholder="Minimo 6 caracteres" minlength="6" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha"
                       placeholder="Repita a nova senha" minlength="6" required>
            </div>

            <button type="submit" class="btn-submit btn-password">
                <i class="fas fa-key"></i> Alterar Senha
            </button>
        </form>
    </div>

</div>

<?php $this->load->view('cliente/_footer'); ?>

<script>
(function() {
    'use strict';

    // Phone mask: (00) 00000-0000
    var phoneInput = document.getElementById('mobile');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 6) {
                v = '(' + v.substring(0, 2) + ') ' + v.substring(2, 7) + '-' + v.substring(7);
            } else if (v.length > 2) {
                v = '(' + v.substring(0, 2) + ') ' + v.substring(2);
            } else if (v.length > 0) {
                v = '(' + v;
            }
            e.target.value = v;
        });
    }

    // CPF mask: 000.000.000-00
    var cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) {
                v = v.substring(0, 3) + '.' + v.substring(3, 6) + '.' + v.substring(6, 9) + '-' + v.substring(9);
            } else if (v.length > 6) {
                v = v.substring(0, 3) + '.' + v.substring(3, 6) + '.' + v.substring(6);
            } else if (v.length > 3) {
                v = v.substring(0, 3) + '.' + v.substring(3);
            }
            e.target.value = v;
        });
    }

    // CEP mask: 00000-000
    var cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            if (v.length > 8) v = v.substring(0, 8);
            if (v.length > 5) {
                v = v.substring(0, 5) + '-' + v.substring(5);
            }
            e.target.value = v;
        });

        // ViaCEP auto-fill on blur
        cepInput.addEventListener('blur', function(e) {
            var cep = e.target.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            fetch('https://viacep.com.br/ws/' + encodeURIComponent(cep) + '/json/')
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.erro) return;

                    var addressInput = document.getElementById('address');
                    var cidadeInput = document.getElementById('cidade');
                    var estadoInput = document.getElementById('estado');

                    if (addressInput && data.logradouro) {
                        addressInput.value = data.logradouro;
                        if (data.bairro) {
                            addressInput.value += ', ' + data.bairro;
                        }
                    }
                    if (cidadeInput && data.localidade) {
                        cidadeInput.value = data.localidade;
                    }
                    if (estadoInput && data.uf) {
                        estadoInput.value = data.uf;
                    }
                })
                .catch(function() {
                    // Silently fail if ViaCEP is unreachable
                });
        });
    }

    // Password confirmation validation
    var formSenha = document.getElementById('formSenha');
    if (formSenha) {
        formSenha.addEventListener('submit', function(e) {
            var novaSenha = document.getElementById('nova_senha').value;
            var confirmarSenha = document.getElementById('confirmar_senha').value;

            if (novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas nao coincidem.');
                return false;
            }

            if (novaSenha.length < 6) {
                e.preventDefault();
                alert('A nova senha deve ter no minimo 6 caracteres.');
                return false;
            }
        });
    }
})();
</script>

</body>
</html>
