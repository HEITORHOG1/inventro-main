<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - <?php echo html_escape(isset($loja->nome) ? $loja->nome : 'Cardapio Digital'); ?></title>
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

        .auth-icon {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-icon .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(37, 211, 102, 0.15), rgba(18, 140, 126, 0.15));
            border: 2px solid rgba(37, 211, 102, 0.3);
        }

        .auth-icon .icon-circle i {
            font-size: 2rem;
            color: var(--primary);
        }

        .auth-icon .icon-circle.icon-error {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.15), rgba(244, 67, 54, 0.05));
            border-color: rgba(244, 67, 54, 0.3);
        }

        .auth-icon .icon-circle.icon-error i {
            color: var(--danger);
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

        .token-invalid {
            text-align: center;
            padding: 20px 0;
        }

        .token-invalid p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 24px;
            line-height: 1.6;
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

        .btn-outline {
            display: inline-block;
            padding: 12px 24px;
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background-color: rgba(37, 211, 102, 0.1);
            transform: translateY(-2px);
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

        .auth-links a i {
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
            <?php if (isset($token_invalido) && $token_invalido): ?>
                <div class="auth-icon">
                    <div class="icon-circle icon-error">
                        <i class="fas fa-times"></i>
                    </div>
                </div>

                <h1 class="auth-title">Link invalido</h1>

                <div class="token-invalid">
                    <p>Este link de redefinicao de senha expirou ou e invalido. Solicite um novo link para redefinir sua senha.</p>
                    <a href="<?php echo base_url('cliente/esqueci-senha'); ?>" class="btn-outline">
                        <i class="fas fa-redo"></i> Solicitar novo link
                    </a>
                </div>
            <?php else: ?>
                <div class="auth-icon">
                    <div class="icon-circle">
                        <i class="fas fa-key"></i>
                    </div>
                </div>

                <h1 class="auth-title">Redefinir senha</h1>

                <?php if (isset($erro) && $erro): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo html_escape($erro); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo base_url('cliente/redefinir-senha/' . html_escape($token)); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="token" value="<?php echo html_escape($token); ?>">

                    <div class="form-group">
                        <label for="senha">Nova senha</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="senha" name="senha" class="form-control" placeholder="Minimo 6 caracteres" required minlength="6" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="senha_confirmar">Confirmar nova senha</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="senha_confirmar" name="senha_confirmar" class="form-control" placeholder="Repita a nova senha" required minlength="6" autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Redefinir Senha
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-links">
                <a href="<?php echo base_url('cliente/login'); ?>">
                    <i class="fas fa-arrow-left"></i> Voltar ao login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
