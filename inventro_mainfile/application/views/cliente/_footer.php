<!-- Cliente Portal - Footer -->
<footer class="cliente-footer">
    <div class="footer-inner">
        <a href="<?php echo base_url('cardapio'); ?>" class="footer-link-cardapio">
            <i class="fas fa-utensils"></i>
            Voltar ao Cardapio
        </a>
        <p class="footer-store"><?php echo html_escape($loja->title); ?></p>
    </div>
</footer>

<style>
    .cliente-footer {
        margin-top: 40px;
        padding: 24px 16px 32px;
        text-align: center;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .footer-inner {
        max-width: 960px;
        margin: 0 auto;
    }

    .footer-link-cardapio {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        border: 1px solid rgba(37, 211, 102, 0.3);
        transition: all 0.2s ease;
    }

    .footer-link-cardapio:hover {
        background: rgba(37, 211, 102, 0.1);
        border-color: var(--primary);
    }

    .footer-store {
        margin-top: 16px;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        color: var(--text-secondary);
        opacity: 0.6;
    }
</style>
