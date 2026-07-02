<?php
session_start();
require_once 'functions.php';

$currentUser = !empty($_SESSION['user_id']) ? $user->findById((int)$_SESSION['user_id']) : null;
$cartCount   = $currentUser ? $cart->count($currentUser['id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | SpazaConnect' : 'SpazaConnect' ?></title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --blue:         #0000ff;
            --blue-dark:    #00008b;
            --white:        #ffffff;
            --bg:           #ffffff;
            --border:       #000000;
            --text:         #000000;
            --text-sub:     #333333;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: Arial, sans-serif; background: var(--bg); color: var(--text); }

        a { text-decoration: none; color: inherit; }

        /* ─── Header ── */
        .sc-header {
            background: var(--white);
            border-bottom: 2px solid var(--blue);
            position: relative;
            z-index: 100;
        }

        .sc-topbar {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            height: 54px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .sc-logo {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: var(--blue);
            text-transform: uppercase;
        }

        .sc-logo span { color: var(--text); }

        .sc-spacer { flex: 1; }

        /* Hamburger Toggle Button */
        .sc-hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text);
            padding: 5px;
            align-items: center;
            justify-content: center;
        }

        .sc-nav-container {
            display: flex;
            align-items: center;
            gap: 24px;
            flex: 1;
        }

        .sc-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sc-nav a, .sc-browse-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            text-transform: uppercase;
        }

        .sc-nav a:hover, .sc-browse-link:hover { color: var(--blue); }

        .sc-nav .sc-nav-register {
            color: var(--blue);
            border: 1px solid var(--blue);
            padding: 4px 8px;
        }

        .sc-nav .sc-nav-cart {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text);
            font-weight: 700;
        }

        .sc-cart-badge {
            background: var(--blue);
            color: var(--white);
            font-size: 11px;
            font-weight: 700;
            padding: 1px 6px;
            line-height: 1.6;
        }

        /* ─── Category strip ── */
        .sc-cats {
            background: #eeeeee;
            border-bottom: 1px solid var(--border);
        }

        .sc-cats-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            height: 40px;
            display: flex;
            align-items: center;
            gap: 2px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sc-cats-inner::-webkit-scrollbar {
            display: none;
        }

        .sc-cats-inner a {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            padding: 4px 12px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .sc-cats-inner a:hover {
            color: var(--blue);
        }

        /* ─── Layout ── */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 20px;
        }

        .hero-wrapper {
            background: #eeeeee;
            border: 1px solid var(--border);
            padding: 72px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-content { max-width: 680px; margin: 0 auto; }

        .hero-content h1 {
            font-size: 40px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .hero-content p {
            font-size: 17px;
            color: var(--text-sub);
            margin-bottom: 32px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 26px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid var(--border);
            cursor: pointer;
        }

        .btn-primary { background: var(--blue); color: var(--white); }

        .btn-secondary {
            background: var(--white);
            color: var(--text);
        }

        .section-title {
            font-size: 17px;
            font-weight: 900;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 48px;
        }

        .card {
            border: 1px solid var(--border);
            padding: 12px;
            display: block;
            background: var(--white);
        }

        .card-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #dddddd;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img img { width: 100%; height: 100%; object-fit: cover; }

        .card h3 { font-size: 13px; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }

        .card p { font-size: 13px; font-weight: 700; color: var(--text); }

        .cat-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cat-link {
            padding: 8px 18px;
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ─── Responsive Media Queries ── */
        @media (max-width: 868px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sc-hamburger {
                display: flex;
            }

            .sc-nav-container {
                display: none;
                position: absolute;
                top: 54px;
                left: 0;
                right: 0;
                background: var(--white);
                border-bottom: 2px solid var(--blue);
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
                gap: 16px;
            }

            .sc-nav-container.open {
                display: flex;
            }

            .sc-nav {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .sc-browse-link, .sc-nav a {
                padding: 10px 0;
                border-bottom: 1px solid #eeeeee;
                width: 100%;
                display: block;
            }

            .sc-nav .sc-nav-register {
                border: 1px solid var(--blue);
                padding: 10px;
                text-align: center;
            }

            .sc-nav .sc-nav-cart {
                justify-content:间-between;
                padding: 10px 0;
            }

            .hero-content h1 {
                font-size: 28px;
            }

            .hero-wrapper {
                padding: 40px 16px;
            }
        }

        @media (max-width: 480px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .button-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<header class="sc-header">
    <div class="sc-topbar">
        <a href="<?= BASE_URL ?>/index.php" class="sc-logo">Spaza<span>Connect</span></a>

        <div class="sc-spacer"></div>

        <button class="sc-hamburger" aria-label="Toggle Menu">
            <i data-lucide="menu"></i>
        </button>

        <div class="sc-nav-container">
            <a href="<?= BASE_URL ?>/products.php" class="sc-browse-link">
                Browse
            </a>

            <nav class="sc-nav">
                <?php if ($currentUser): ?>
                    <?php if (in_array($currentUser['role'], ['seller', 'admin'])): ?>
                        <a href="<?= BASE_URL ?>/my-shop.php">My Shop</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/account.php">Account</a>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth/login.php">Login</a>
                    <a href="<?= BASE_URL ?>/auth/register.php" class="sc-nav-register">Register</a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/cart.php" class="sc-nav-cart">
                    <span>Cart</span>
                    <?php if ($cartCount > 0): ?> 
                        <span class="sc-cart-badge"><?= $cartCount ?></span>
                    <?php else: ?>
                        <span class="sc-cart-badge">0</span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </div>
</header>

<main class="container">
</main>

<script>
    lucide.createIcons();

    $(document).ready(function() {
        $('.sc-hamburger').on('click', function() {
            const container = $('.sc-nav-container');
            container.toggleClass('open');
            
            const icon = $(this).find('i');
            if (container.hasClass('open')) {
                icon.attr('data-lucide', 'x');
            } else {
                icon.attr('data-lucide', 'menu');
            }
            lucide.createIcons();
        });
    });
</script>
</body>
</html>