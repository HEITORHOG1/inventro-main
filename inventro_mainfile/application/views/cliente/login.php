<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - <?php echo html_escape(isset($loja->nome) ? $loja->nome : 'Cardapio Digital'); ?></title>
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
            margin-bottom: 32px;
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

        .alert-success {
            background-color: rgba(0, 200, 83, 0.15);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: var(--success);
        }

        .form-group {
            margin-bottom: 20px;
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

        .input-wrapper i {
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

        .auth-links .forgot-link {
            display: block;
            margin-bottom: 16px;
        }

        .auth-links .register-link {
            display: block;
            margin-bottom: 16px;
        }

        .auth-links .register-link span {
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

            <h1 class="auth-title">Entrar na sua conta</h1>

            <?php if (isset($erro) && $erro): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo html_escape($erro); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($sucesso) && $sucesso): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo html_escape($sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo base_url('cliente/login'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" class="form-control" placeholder="Sua senha" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>

            <div class="auth-links">
                <a href="<?php echo base_url('cliente/esqueci-senha'); ?>" class="forgot-link">Esqueci minha senha</a>
                <a href="<?php echo base_url('cliente/registrar'); ?>" class="register-link">
                    Ainda nao tem conta? <span>Criar conta</span>
                </a>
                <a href="<?php echo base_url('cardapio'); ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Voltar ao Cardapio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
