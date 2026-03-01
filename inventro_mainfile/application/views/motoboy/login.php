<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal do Entregador - <?php echo html_escape($loja->title ?? 'Loja'); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a5c2e 0%, #2d8a4e 50%, #1a5c2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            border-radius: 16px;
            padding: 40px 30px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d8a4e, #1a5c2e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 36px;
            color: #fff;
        }

        .login-header h1 {
            font-size: 22px;
            color: #1a5c2e;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #888;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
            outline: none;
            -webkit-appearance: none;
        }

        .form-group input:focus {
            border-color: #2d8a4e;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2d8a4e, #1a5c2e);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover { opacity: 0.9; }
        .btn-login:active { transform: scale(0.98); }

        .error-msg {
            background: #ffe0e0;
            color: #c0392b;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .loja-nome {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon">&#x1F6F5;</div>
            <h1>Portal do Entregador</h1>
            <p><?php echo html_escape($loja->title ?? 'Loja'); ?></p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="error-msg"><?php echo html_escape($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo base_url('motoboy/login'); ?>">
            <input type="hidden" name="csrf_test_name" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel"
                       id="telefone"
                       name="telefone"
                       placeholder="(00) 00000-0000"
                       inputmode="tel"
                       autocomplete="tel"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password"
                       id="senha"
                       name="senha"
                       placeholder="Sua senha"
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn-login">ENTRAR</button>
        </form>

        <div class="loja-nome">
            <?php echo html_escape($loja->title ?? ''); ?> &bull; Portal de Entregas
        </div>
    </div>
</body>
</html>
