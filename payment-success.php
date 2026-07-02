<?php
require_once __DIR__ . '/header.php';
?>

<main style="max-width: 768px; margin: 0 auto; padding: 64px 16px; text-align: center;">
    <h1 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 16px;">
        Payment Successful!
    </h1>
    
    <p style="color: var(--text-secondary); margin-bottom: 32px;">
        Thank you for your purchase. We are processing your order now.
    </p>
    
    <a href="<?= BASE_URL ?>/account.php?tab=orders" 
       style="display: inline-block; background: var(--button-bg); color: var(--button-text); padding: 12px 32px; border-radius: 9999px; font-weight: 600; text-decoration: none;">
        View My Orders
    </a>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>