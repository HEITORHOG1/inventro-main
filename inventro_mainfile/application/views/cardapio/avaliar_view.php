<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Pedido - <?php echo html_escape($loja->title ?? 'Cardapio Digital'); ?></title>

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
            --star-color: #ffc107;
            --star-empty: #555;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .rating-card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 24px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .icon-circle i { font-size: 3rem; color: white; }

        .icon-circle--success {
            background: linear-gradient(135deg, var(--success), var(--primary));
            animation: pulse 2s infinite;
        }

        .icon-circle--star {
            background: linear-gradient(135deg, var(--star-color), #ff9800);
            animation: pulse 2s infinite;
        }

        .icon-circle--info {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h1.star-gradient {
            background: linear-gradient(135deg, var(--star-color), #ff9800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .order-number {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0 25px;
        }

        /* Info box */
        .info-box {
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-secondary); display: flex; align-items: center; gap: 8px; }
        .info-value { font-weight: 600; }

        .total-row {
            background: rgba(37, 211, 102, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }

        .total-row .info-value { font-size: 1.4rem; color: var(--primary); }

        /* Message box */
        .message-box {
            background: rgba(37, 211, 102, 0.1);
            border: 1px solid rgba(37, 211, 102, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .message-box i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; }
        .message-box p { color: var(--text-secondary); line-height: 1.6; }

        /* Star rating - CSS only with row-reverse trick */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 8px;
            margin: 25px 0;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2.8rem;
            color: var(--star-empty);
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        /* Hover: highlight hovered star and all stars to its right (which are visually to its left due to row-reverse) */
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: var(--star-color);
            transform: scale(1.1);
        }

        /* Checked: highlight checked star and all stars after it (visually to the left) */
        .star-rating input[type="radio"]:checked ~ label {
            color: var(--star-color);
        }

        /* Display-only stars (for already rated state) */
        .stars-display {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }

        .stars-display i {
            font-size: 2.5rem;
            color: var(--star-empty);
        }

        .stars-display i.active {
            color: var(--star-color);
        }

        /* Textarea */
        .form-group {
            margin: 20px 0;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group textarea {
            width: 100%;
            min-height: 100px;
            padding: 15px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            transition: border-color 0.3s;
        }

        .form-group textarea::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
        }

        /* Buttons */
        .btn {
            padding: 16px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s;
            width: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            margin-bottom: 12px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        .btn-secondary:hover { background: rgba(255,255,255,0.2); }

        /* Validation hint */
        .hint {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .hint.visible {
            display: block;
        }

        /* Comment display */
        .comment-display {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 15px;
            margin: 15px 0;
            text-align: left;
        }

        .comment-display p {
            color: var(--text-secondary);
            font-style: italic;
            line-height: 1.6;
        }

        .comment-display .comment-label {
            color: var(--text-primary);
            font-weight: 600;
            font-style: normal;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        @media (max-width: 480px) {
            .rating-card { padding: 25px; }
            .order-number { font-size: 1.3rem; }
            .star-rating label { font-size: 2.2rem; }
            .stars-display i { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="rating-card animate-in">

        <?php if (!empty($avaliacao_salva)): ?>
            <!-- Thank you state: rating just saved -->
            <div class="icon-circle icon-circle--success">
                <i class="fas fa-heart"></i>
            </div>

            <h1>Obrigado pela Avaliacao!</h1>
            <p class="subtitle"><?php echo html_escape($loja->title ?? 'Cardapio Digital'); ?></p>
            <p class="order-number">#<?php echo html_escape($order->order_number); ?></p>

            <div class="message-box">
                <i class="fas fa-star"></i>
                <p>Sua avaliacao foi registrada com sucesso! Agradecemos o seu feedback, ele nos ajuda a melhorar cada vez mais.</p>
            </div>

            <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-primary">
                <i class="fas fa-utensils"></i> Fazer Novo Pedido
            </a>

        <?php elseif (!empty($ja_avaliou)): ?>
            <!-- Already rated state -->
            <div class="icon-circle icon-circle--info">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1 class="star-gradient">Pedido ja Avaliado</h1>
            <p class="subtitle"><?php echo html_escape($loja->title ?? 'Cardapio Digital'); ?></p>
            <p class="order-number">#<?php echo html_escape($order->order_number); ?></p>

            <p class="subtitle">Voce ja avaliou este pedido. Obrigado!</p>

            <div class="stars-display">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?php echo $i <= (int)$order->avaliacao_nota ? 'active' : ''; ?>"></i>
                <?php endfor; ?>
            </div>

            <?php if (!empty($order->avaliacao_comentario)): ?>
                <div class="comment-display">
                    <p class="comment-label"><i class="fas fa-comment"></i> Seu comentario:</p>
                    <p><?php echo html_escape($order->avaliacao_comentario); ?></p>
                </div>
            <?php endif; ?>

            <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary" style="margin-top: 20px;">
                <i class="fas fa-utensils"></i> Ir para o Cardapio
            </a>

        <?php else: ?>
            <!-- Rating form -->
            <div class="icon-circle icon-circle--star">
                <i class="fas fa-star"></i>
            </div>

            <h1 class="star-gradient">Avalie seu Pedido</h1>
            <p class="subtitle"><?php echo html_escape($loja->title ?? 'Cardapio Digital'); ?></p>
            <p class="order-number">#<?php echo html_escape($order->order_number); ?></p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-user"></i> Cliente</span>
                    <span class="info-value"><?php echo html_escape($order->cliente_nome); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar"></i> Data</span>
                    <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></span>
                </div>
                <div class="total-row">
                    <div class="info-row" style="border: none; padding: 0;">
                        <span class="info-label" style="font-size: 1.1rem; color: var(--text-primary);">
                            <i class="fas fa-receipt"></i> Total
                        </span>
                        <span class="info-value">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <form method="POST" id="rating-form">
                <p class="subtitle" style="margin-bottom: 5px;">Como foi sua experiencia?</p>

                <div class="star-rating">
                    <input type="radio" id="star5" name="nota" value="5">
                    <label for="star5" title="5 estrelas"><i class="fas fa-star"></i></label>

                    <input type="radio" id="star4" name="nota" value="4">
                    <label for="star4" title="4 estrelas"><i class="fas fa-star"></i></label>

                    <input type="radio" id="star3" name="nota" value="3">
                    <label for="star3" title="3 estrelas"><i class="fas fa-star"></i></label>

                    <input type="radio" id="star2" name="nota" value="2">
                    <label for="star2" title="2 estrelas"><i class="fas fa-star"></i></label>

                    <input type="radio" id="star1" name="nota" value="1">
                    <label for="star1" title="1 estrela"><i class="fas fa-star"></i></label>
                </div>

                <p class="hint" id="star-hint"><i class="fas fa-exclamation-circle"></i> Por favor, selecione uma nota.</p>

                <div class="form-group">
                    <label for="comentario"><i class="fas fa-comment-dots"></i> Deixe um comentario (opcional)</label>
                    <textarea
                        id="comentario"
                        name="comentario"
                        placeholder="Conte como foi sua experiencia..."
                        maxlength="500"
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fas fa-paper-plane"></i> Enviar Avaliacao
                </button>
            </form>

        <?php endif; ?>

        <a href="<?php echo base_url('cardapio'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao Cardapio
        </a>
    </div>

    <?php if (empty($avaliacao_salva) && empty($ja_avaliou)): ?>
    <script>
        (function() {
            var form = document.getElementById('rating-form');
            var hint = document.getElementById('star-hint');

            form.addEventListener('submit', function(e) {
                var selected = document.querySelector('input[name="nota"]:checked');
                if (!selected) {
                    e.preventDefault();
                    hint.classList.add('visible');
                    return false;
                }
                hint.classList.remove('visible');
            });

            // Hide hint when a star is selected
            var radios = document.querySelectorAll('input[name="nota"]');
            for (var i = 0; i < radios.length; i++) {
                radios[i].addEventListener('change', function() {
                    hint.classList.remove('visible');
                });
            }
        })();
    </script>
    <?php endif; ?>
</body>
</html>
