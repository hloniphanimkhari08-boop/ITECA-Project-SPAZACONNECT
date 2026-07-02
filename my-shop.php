<?php
$pageTitle = 'My Store';
require_once 'header.php';
require_once 'components/_pagination.php';

if (!$currentUser || !in_array($currentUser['role'], ['seller', 'admin'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$my_products   = $product->getAll(['seller_id' => $currentUser['id'], 'status' => 'all']) ?? [];
$my_orders     = ($currentUser['role'] === 'admin') ? ($order->getAll() ?? []) : ($order->getBySeller($currentUser['id']) ?? []);
$active_count  = count(array_filter($my_products, fn($p) => $p['status'] === 'active'));
$pending_count = count(array_filter($my_orders,   fn($o) => in_array(strtolower($o['status']), ['pending', 'processing'])));
?>

<div class="container">

    <!-- Page title + action -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
        <div>
            <h1 style="font-size: 18px; font-weight: 700; margin-bottom: 2px;">My Store</h1>
            <p style="font-size: 13px; color: var(--text-muted);"><?= htmlspecialchars($currentUser['name']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>/sell.php" class="btn btn-primary" style="font-size: 13px;">+ New Listing</a>
    </div>

    <!-- Stats as plain numbers -->
    <div style="display: flex; gap: 40px; padding: 20px 0; margin-bottom: 32px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
        <div>
            <p style="font-size: 28px; font-weight: 900; line-height: 1;"><?= $active_count ?></p>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Active listings</p>
        </div>
        <div>
            <p style="font-size: 28px; font-weight: 900; line-height: 1;"><?= $pending_count ?></p>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Pending orders</p>
        </div>
        <div>
            <p style="font-size: 28px; font-weight: 900; line-height: 1;"><?= count($my_orders) ?></p>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Total orders</p>
        </div>
    </div>

    <!-- Orders full width -->
    <section style="margin-bottom: 40px;">
        <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 14px;">Orders</h2>

        <?php if (empty($my_orders)): ?>
            <p style="font-size: 13px; color: var(--text-muted);">No orders yet.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                <!-- Header -->
                <div style="background: #f9fafb; padding: 10px 18px; display: grid; grid-template-columns: 80px 1fr 100px 140px; gap: 12px;">
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Order</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Date</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Status</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-align: right;">Update</p>
                </div>
                <?php foreach ($my_orders as $o): ?>
                    <div style="background: var(--white); padding: 12px 18px; display: grid; grid-template-columns: 80px 1fr 100px 140px; align-items: center; gap: 12px;">
                        <p style="font-size: 12px; font-weight: 700; color: var(--text-muted);">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></p>
                        <p style="font-size: 13px;"><?= date('d M Y', strtotime($o['created_at'])) ?></p>
                        <p style="font-size: 12px; text-transform: capitalize; color: var(--text-sub);"><?= htmlspecialchars($o['status']) ?></p>
                        <div style="text-align: right;">
                            <select onchange="updateOrderStatus(<?= $o['id'] ?>, this.value)"
                                style="height: 28px; padding: 0 6px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px; background: var(--white);">
                                <option value="pending"    <?= $o['status'] === 'pending'    ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped"    <?= $o['status'] === 'shipped'    ? 'selected' : '' ?>>Shipped</option>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php renderPagination('merchant-orders-table', 8); ?>
        <?php endif; ?>
    </section>

    <!-- Inventory full width -->
    <section>
        <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 14px;">Inventory</h2>

        <?php if (empty($my_products)): ?>
            <p style="font-size: 13px; color: var(--text-muted);">No listings yet. <a href="<?= BASE_URL ?>/sell.php" style="color: var(--purple); font-weight: 600;">Create one</a></p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                <div style="background: #f9fafb; padding: 10px 18px; display: grid; grid-template-columns: 1fr 100px 80px 60px; gap: 12px;">
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Title</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Price</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Status</p>
                    <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);"></p>
                </div>
                <?php foreach ($my_products as $p): ?>
                    <div style="background: var(--white); padding: 12px 18px; display: grid; grid-template-columns: 1fr 100px 80px 60px; align-items: center; gap: 12px;">
                        <p style="font-size: 13px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($p['title']) ?></p>
                        <p style="font-size: 13px;">R<?= number_format($p['price'], 2) ?></p>
                        <p style="font-size: 12px; text-transform: capitalize; color: var(--text-muted);"><?= htmlspecialchars($p['status']) ?></p>
                        <a href="<?= BASE_URL ?>/edit-product.php?id=<?= $p['id'] ?>"
                            style="font-size: 13px; color: var(--purple); font-weight: 600;">Edit</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php renderPagination('merchant-catalog-table', 8); ?>
        <?php endif; ?>
    </section>

</div>

<script src="<?= BASE_URL ?>/assets/js/account.js"></script>
<?php require_once 'footer.php'; ?>