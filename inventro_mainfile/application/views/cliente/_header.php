<!-- Cliente Portal - Header/Navbar -->
<?php
    $current_segment = $this->uri->segment(2);
?>
<nav class="cliente-navbar">
    <div class="navbar-inner">
        <a href="<?php echo base_url('cliente/dashboard'); ?>" class="navbar-brand">
            <?php echo html_escape($loja->title); ?>
        </a>

        <div class="navbar-links" id="navbarLinks">
            <a href="<?php echo base_url('cliente/pedidos'); ?>"
               class="navbar-link<?php echo ($current_segment === 'pedidos') ? ' active' : ''; ?>">
                <i class="fas fa-list"></i>
                <span>Pedidos</span>
            </a>
            <a href="<?php echo base_url('cliente/perfil'); ?>"
               class="navbar-link<?php echo ($current_segment === 'perfil') ? ' active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>
            <a href="<?php echo base_url('cliente/logout'); ?>"
               class="navbar-link navbar-link-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>

        <button class="navbar-toggle" id="navbarToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
<div class="navbar-spacer"></div>

<style>
    .cliente-navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: var(--bg-card);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.3);
    }

    .navbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 960px;
        margin: 0 auto;
        padding: 0 16px;
        height: 56px;
    }

    .navbar-brand {
        font-family: 'Poppins', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .navbar-brand:hover {
        color: var(--primary-dark);
    }

    .navbar-links {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .navbar-link {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .navbar-link:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.06);
    }

    .navbar-link.active {
        color: var(--primary);
        background: rgba(37, 211, 102, 0.1);
    }

    .navbar-link-logout:hover {
        color: var(--danger);
        background: rgba(244, 67, 54, 0.1);
    }

    .navbar-link i {
        font-size: 14px;
        width: 16px;
        text-align: center;
    }

    .navbar-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 20px;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .navbar-toggle:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.06);
    }

    .navbar-spacer {
        height: 56px;
    }

    /* Mobile styles */
    @media (max-width: 600px) {
        .navbar-toggle {
            display: block;
        }

        .navbar-links {
            display: none;
            position: absolute;
            top: 56px;
            left: 0;
            right: 0;
            background: var(--bg-card);
            flex-direction: column;
            padding: 8px 16px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            gap: 2px;
        }

        .navbar-links.open {
            display: flex;
        }

        .navbar-link {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
        }
    }
</style>

<script>
(function() {
    'use strict';
    var toggle = document.getElementById('navbarToggle');
    var links = document.getElementById('navbarLinks');
    if (toggle && links) {
        toggle.addEventListener('click', function() {
            links.classList.toggle('open');
        });
        // Close menu when clicking a link on mobile
        links.querySelectorAll('.navbar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                links.classList.remove('open');
            });
        });
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !links.contains(e.target)) {
                links.classList.remove('open');
            }
        });
    }
})();
</script>
