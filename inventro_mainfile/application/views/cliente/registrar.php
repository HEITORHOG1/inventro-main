<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - <?php echo html_escape(isset($loja->nome) ? $loja->nome : 'Cardapio Digital'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            --blue: #3498db;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-logo img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 50%;
            margin-bottom: 12px;
        }

        .auth-logo h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .auth-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .alert-danger {
            background-color: rgba(244, 67, 54, 0.15);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: var(--danger);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(160, 160, 160, 0.6);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .strength-bar {
            flex: 1;
            height: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .strength-text {
            min-width: 80px;
            text-align: right;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .auth-links {
            text-align: center;
            margin-top: 24px;
        }

        .auth-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: var(--primary);
        }

        .auth-links .login-link {
            display: block;
            margin-bottom: 16px;
        }

        .auth-links .login-link span {
            color: var(--primary);
            font-weight: 500;
        }

        .auth-links .back-link {
            display: block;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .auth-links .back-link i {
            margin-right: 6px;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 32px 24px;
            }

            .auth-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <?php if (isset($loja->logo) && $loja->logo): ?>
                    <img src="<?php echo html_escape($loja->logo); ?>" alt="<?php echo html_escape($loja->nome); ?>">
                <?php else: ?>
                    <i class="fas fa-store" style="font-size: 3rem; color: var(--primary); margin-bottom: 12px; display: block;"></i>
                <?php endif; ?>
                <h2><?php echo html_escape(isset($loja->nome) ? $loja->nome : 'Cardapio Digital'); ?></h2>
            </div>

            <h1 class="auth-title">Criar sua conta</h1>

            <?php if (isset($erro) && $erro): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo html_escape($erro); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo base_url('cliente/registrar'); ?>" id="formRegistrar">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="nome" name="nome" class="form-control" placeholder="Seu nome completo" required autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" required autocomplete="tel">
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="senha" name="senha" class="form-control" placeholder="Minimo 6 caracteres" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="password-strength" id="passwordStrength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-text" id="strengthText"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha_confirmar">Confirmar Senha</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="senha_confirmar" name="senha_confirmar" class="form-control" placeholder="Repita sua senha" required minlength="6" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </button>
            </form>

            <div class="auth-links">
                <a href="<?php echo base_url('cliente/login'); ?>" class="login-link">
                    Ja tem conta? <span>Entrar</span>
                </a>
                <a href="<?php echo base_url('cardapio'); ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Voltar ao Cardapio
                </a>
            </div>
        </div>
    </div>

    <script>
        // Phone mask: (00) 00000-0000
        (function() {
            var telefoneInput = document.getElementById('telefone');
            telefoneInput.addEventListener('input', function(e) {
                var value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                if (value.length > 0) {
                    value = '(' + value;
                }
                if (value.length > 3) {
                    value = value.substring(0, 3) + ') ' + value.substring(3);
                }
                if (value.length > 10) {
                    value = value.substring(0, 10) + '-' + value.substring(10);
                }
                e.target.value = value;
            });
        })();

        // Password strength indicator
        (function() {
            var senhaInput = document.getElementById('senha');
            var strengthContainer = document.getElementById('passwordStrength');
            var strengthFill = document.getElementById('strengthFill');
            var strengthText = document.getElementById('strengthText');

            senhaInput.addEventListener('input', function() {
                var password = this.value;

                if (password.length === 0) {
                    strengthContainer.style.display = 'none';
                    return;
                }

                strengthContainer.style.display = 'flex';

                var strength = 0;
                if (password.length >= 6) strength++;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                var percentage, color, label;

                if (password.length < 6) {
                    percentage = 20;
                    color = '#f44336';
                    label = 'Muito curta';
                } else if (strength <= 2) {
                    percentage = 40;
                    color = '#f44336';
                    label = 'Fraca';
                } else if (strength === 3) {
                    percentage = 60;
                    color = '#ff9800';
                    label = 'Razoavel';
                } else if (strength === 4) {
                    percentage = 80;
                    color = '#00c853';
                    label = 'Boa';
                } else {
                    percentage = 100;
                    color = '#00c853';
                    label = 'Forte';
                }

                strengthFill.style.width = percentage + '%';
                strengthFill.style.backgroundColor = color;
                strengthText.style.color = color;
                strengthText.textContent = label;
            });
        })();
    </script>
</body>
</html>
