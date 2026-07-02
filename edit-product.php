<?php
$pageTitle = 'Edit Listing';
require_once __DIR__ . '/header.php';

if (!$currentUser || !in_array($currentUser['role'], ['seller', 'admin'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$productId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$productData = $product->getById($productId);

if (!$productData || ($currentUser['role'] !== 'admin' && $productData['seller_id'] !== $currentUser['id'])) {
    header('Location: ' . BASE_URL . '/my-shop.php');
    exit;
}

$categoryModel = new Category($db);
$categories    = $categoryModel->getAll(true) ?? [];
?>

<div class="container" style="max-width: 600px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
        <h1 style="font-size: 18px; font-weight: 700;">Edit Listing</h1>
        <a href="<?= BASE_URL ?>/my-shop.php" style="font-size: 13px; color: var(--text-muted);">&larr; Back to shop</a>
    </div>

    <!-- Preview strip -->
    <div style="display: flex; gap: 16px; align-items: center; background: var(--white); border: 1px solid var(--border); border-radius: 8px; padding: 14px; margin-bottom: 28px;">
        <div style="width: 56px; height: 56px; background: #f3f4f6; border-radius: 6px; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
            <?php if (!empty($productData['image'])): ?>
                <img src="<?= htmlspecialchars($productData['image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <span style="font-size: 11px; color: var(--text-muted);">No img</span>
            <?php endif; ?>
        </div>
        <div>
            <p style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($productData['title']) ?></p>
            <p style="font-size: 13px; color: var(--text-muted);">R<?= number_format($productData['price'], 2) ?></p>
        </div>
    </div>

    <!-- Form -->
    <form id="edit-listing-form" style="display: flex; flex-direction: column; gap: 16px;">

        <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($productData['title']) ?>" required
                style="width: 100%; height: 36px; padding: 0 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Price (R)</label>
                <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($productData['price']) ?>" required
                    style="width: 100%; height: 36px; padding: 0 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Status</label>
                <select name="status" style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; background: var(--white); box-sizing: border-box;">
                    <option value="active"   <?= $productData['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $productData['status'] === 'inactive' ? 'selected' : '' ?>>Hidden</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Category</label>
                <select name="category_id" style="width: 100%; height: 36px; padding: 0 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; background: var(--white); box-sizing: border-box;" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (int)$productData['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Description</label>
            <textarea name="description" rows="5"
                style="width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box; resize: vertical;"><?= htmlspecialchars($productData['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 8px; border-top: 1px solid var(--border);">
            <a href="<?= BASE_URL ?>/my-shop.php" style="font-size: 13px; color: var(--text-muted); display: flex; align-items: center;">Cancel</a>
            <button type="submit" class="btn btn-primary" style="font-size: 13px; border: none; cursor: pointer;">Save changes</button>
        </div>
    </form>
</div>

<script>
document.getElementById('edit-listing-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    $.ajax({
        url: '<?= BASE_URL ?>/api/product/update.php?id=<?= $productId ?>',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function (res) {
            const parsed = typeof res === 'string' ? JSON.parse(res) : res;
            if (parsed.success) window.location.href = '<?= BASE_URL ?>/my-shop.php';
            else { alert(parsed.message || 'Error saving.'); btn.disabled = false; }
        },
        error: function () { alert('Request failed.'); btn.disabled = false; }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>