<?php
$pageTitle = 'Product';
require_once __DIR__ . '/header.php';

if (!isset($_GET['id'])) { header('Location: ' . BASE_URL . '/index.php'); exit; }
$product_detail = $product->getById((int)$_GET['id']);
if (!$product_detail) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$is_owner = $currentUser && $currentUser['id'] === $product_detail['seller_id'];
$can_buy  = $currentUser && !$is_owner;
$extraJs  = [BASE_URL . '/assets/js/cart.js'];
?>

<div class="container" style="max-width: 760px;">

    <a href="<?= BASE_URL ?>/products.php" style="font-size: 13px; color: var(--text-muted); display: inline-block; margin-bottom: 28px;">
        &larr; Back
    </a>

    <!-- Image -->
    <div style="width: 100%; max-height: 360px; background: #f3f4f6; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 32px;">
        <?php if ($product_detail['image']): ?>
            <img src="<?= htmlspecialchars($product_detail['image']) ?>" style="width: 100%; max-height: 360px; object-fit: contain;">
        <?php else: ?>
            <p style="font-size: 12px; color: var(--text-muted); padding: 80px 0;">No image</p>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div style="display: grid; grid-template-columns: 1fr auto; align-items: start; gap: 24px; margin-bottom: 16px;">
        <div>
            <p style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                <?= htmlspecialchars($product_detail['category_name'] ?? '') ?>
            </p>
            <h1 style="font-size: 20px; font-weight: 800; line-height: 1.2; margin-bottom: 4px;"><?= htmlspecialchars($product_detail['title']) ?></h1>
            <p style="font-size: 13px; color: var(--text-sub);">by <?= htmlspecialchars($product_detail['seller_name']) ?></p>
        </div>
        <p style="font-size: 24px; font-weight: 900; white-space: nowrap;">R<?= number_format($product_detail['price'], 2) ?></p>
    </div>

    <div style="padding: 14px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); margin-bottom: 20px; display: flex; gap: 32px;">
        <div>
            <p style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px;">Condition</p>
            <p style="font-size: 13px; text-transform: capitalize;"><?= htmlspecialchars($product_detail['condition']) ?></p>
        </div>
        <?php if (!empty($product_detail['category_name'])): ?>
        <div>
            <p style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px;">Category</p>
            <p style="font-size: 13px;"><?= htmlspecialchars($product_detail['category_name']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <p style="font-size: 13px; color: var(--text-sub); line-height: 1.7; margin-bottom: 28px;"><?= nl2br(htmlspecialchars($product_detail['description'])) ?></p>

    <?php if ($is_owner): ?>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="<?= BASE_URL ?>/edit-product.php?id=<?= $product_detail['id'] ?>" class="btn btn-primary" style="font-size: 13px;">Edit listing</a>
            <button id="btn-delete-listing" data-product-id="<?= $product_detail['id'] ?>"
                style="font-size: 13px; font-weight: 600; color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;">
                Delete
            </button>
        </div>
    <?php elseif ($can_buy && $product_detail['status'] === 'active'): ?>
        <button class="btn-add-cart btn btn-primary" data-product-id="<?= $product_detail['id'] ?>"
            style="font-size: 13px; border: none; cursor: pointer; width: 100%;">
            Add to Cart
        </button>
    <?php elseif (!$currentUser): ?>
        <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-primary" style="font-size: 13px; display: block; text-align: center;">
            Login to buy
        </a>
    <?php endif; ?>

</div>

<script>
document.getElementById('btn-delete-listing')?.addEventListener('click', function () {
    if (!confirm('Delete this listing?')) return;
    this.disabled = true;
    $.post('<?= BASE_URL ?>/api/product/delete.php', { id: this.dataset.productId }, function (res) {
        const parsed = typeof res === 'string' ? JSON.parse(res) : res;
        if (parsed.success) window.location.href = '<?= BASE_URL ?>/mystore.php';
        else { alert(parsed.message); document.getElementById('btn-delete-listing').disabled = false; }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>