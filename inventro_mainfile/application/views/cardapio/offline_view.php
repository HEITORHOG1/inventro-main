<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sem Conexao - <?php echo html_escape($loja->title ?? 'Cardapio Digital'); ?></title>
    <meta name="theme-color" content="#25D366">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --bg-dark: #1a1a2e;
            --bg-card: #16213e;
            --bg-light: #0f3460;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --border-radius: 16px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 50%, var(--bg-light) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .offline-card {
            background: rgba(22, 33, 62, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            text-align: center;
            max-width: 420px;
            width: 100%;
        }

        .offline-icon {
            font-size: 64px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.05); }
        }

        .offline-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .offline-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .btn-retry {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-retry:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
        }

        .btn-retry:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .offline-card {
                padding: 30px 20px;
            }

            .offline-icon {
                font-size: 52px;
            }

            .offline-title {
                font-size: 20px;
            }

            .offline-subtitle {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="offline-card">
        <div class="offline-icon">
            <i class="fas fa-wifi-slash"></i>
        </div>
        <h1 class="offline-title">Sem conexao com internet</h1>
        <p class="offline-subtitle">Verifique sua conexao e tente novamente</p>
        <button class="btn-retry" onclick="location.reload()">
            <i class="fas fa-rotate-right"></i>
            Tentar novamente
        </button>
    </div>
</body>
</html>
