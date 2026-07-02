<?php
$pageTitle = 'Home';
require_once 'header.php';
$featuredProducts = array_slice($product->getAll() ?? [], 0, 4);
?>

<div class="hero-wrapper">
    <div class="hero-content">
        <h1>Marketplace for Local Traders</h1>
        <p>Buy from small businesses across South Africa. Safe, verified, delivered to your door.</p>
        <div class="button-group">
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary">Browse Products</a>
            <a href="<?= BASE_URL ?>/sell.php" class="btn btn-secondary">Become a Seller</a>
        </div>
    </div>
</div>

<div class="container">
    <section>
        <h2 class="section-title">Featured Items</h2>
        <div class="grid">
            <?php foreach ($featuredProducts as $p): ?>
                <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" class="card">
                    <div class="card-img">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>">
                        <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 12px;">No image</span>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p>R<?= number_format($p['price'], 2) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section style="border-top: 1px solid var(--border); padding-top: 40px;">
        <h2 class="section-title">Shop by Category</h2>
        <div class="cat-group">
            <?php foreach (['Clothing', 'Hair', 'Electronics', 'Food', 'Second-Hand'] as $cat): ?>
                <a href="<?= BASE_URL ?>/products.php?category=<?= strtolower($cat) ?>" class="cat-link">
                    <?= $cat ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'footer.php'; ?>