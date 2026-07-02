<?php
$pageTitle = 'Admin';
require_once 'header.php';
require_once 'components/_pagination.php';

if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$extraJs     = [BASE_URL . '/assets/js/admin.js'];
$allUsers    = $user->getAll() ?? [];
$allProducts = $product->getAllAdmin() ?? [];
$allOrders   = $order->getAll() ?? [];
?>

<div class="container">

    <div style="display: flex; gap: 48px; align-items: start;">

        <!-- Sidebar nav -->
        <aside style="width: 160px; flex-shrink: 0; padding-top: 4px;">
            <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 14px;">Admin Panel</p>
            <div style="display: flex; flex-direction: column; gap: 2px;">
                <a href="#users" style="font-size: 13px; padding: 7px 0; color: var(--text-sub); border-bottom: 1px solid var(--border);">Users <span style="float:right; color: var(--text-muted);"><?= count($allUsers) ?></span></a>
                <a href="#products" style="font-size: 13px; padding: 7px 0; color: var(--text-sub); border-bottom: 1px solid var(--border);">Products <span style="float:right; color: var(--text-muted);"><?= count($allProducts) ?></span></a>
                <a href="#orders" style="font-size: 13px; padding: 7px 0; color: var(--text-sub); border-bottom: 1px solid var(--border);">Orders <span style="float:right; color: var(--text-muted);"><?= count($allOrders) ?></span></a>
            </div>
        </aside>

        <!-- Main content -->
        <div style="flex: 1; min-width: 0;">

            <!-- Users -->
            <section id="users" style="margin-bottom: 48px;">
                <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 14px;">Users</h2>
                <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <div style="background: #f9fafb; padding: 9px 16px; display: grid; grid-template-columns: 1fr 1fr 80px 70px 70px; gap: 12px;">
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Name</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Email</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Role</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Status</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);"></p>
                    </div>
                    <?php foreach ($allUsers as $u): ?>
                        <div id="user-row-<?= $u['id'] ?>" style="background: var(--white); padding: 10px 16px; display: grid; grid-template-columns: 1fr 1fr 80px 70px 70px; align-items: center; gap: 12px;">
                            <p style="font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($u['name']) ?></p>
                            <p style="font-size: 12px; color: var(--text-sub); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($u['email']) ?></p>
                            <p style="font-size: 12px; text-transform: capitalize; color: var(--text-sub);"><?= htmlspecialchars($u['role']) ?></p>
                            <p style="font-size: 12px; color: var(--text-muted);"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></p>
                            <div>
                                <?php if ($u['id'] !== $currentUser['id']): ?>
                                    <button onclick="toggleUser(<?= $u['id'] ?>, <?= $u['is_active'] ? 0 : 1 ?>, this)"
                                        style="font-size: 12px; font-weight: 600; color: var(--purple); background: none; border: none; cursor: pointer; padding: 0;">
                                        <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php renderPagination('users-table'); ?>
            </section>

            <!-- Products -->
            <section id="products" style="margin-bottom: 48px;">
                <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 14px;">Products</h2>
                <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <div style="background: #f9fafb; padding: 9px 16px; display: grid; grid-template-columns: 1fr 120px 80px 80px 60px; gap: 12px;">
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Title</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Seller</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Price</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Status</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);"></p>
                    </div>
                    <?php foreach ($allProducts as $p): ?>
                        <div id="product-row-<?= $p['id'] ?>" style="background: var(--white); padding: 10px 16px; display: grid; grid-template-columns: 1fr 120px 80px 80px 60px; align-items: center; gap: 12px;">
                            <p style="font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($p['title']) ?></p>
                            <p style="font-size: 12px; color: var(--text-sub); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($p['seller_name']) ?></p>
                            <p style="font-size: 13px;">R<?= number_format($p['price'], 2) ?></p>
                            <p id="product-status-<?= $p['id'] ?>" style="font-size: 12px; color: var(--text-muted); text-transform: capitalize;"><?= htmlspecialchars($p['status']) ?></p>
                            <div>
                                <?php if ($p['status'] === 'active'): ?>
                                    <button onclick="removeProduct(<?= $p['id'] ?>, this)"
                                        style="font-size: 12px; font-weight: 600; color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;">Remove</button>
                                <?php else: ?>
                                    <button onclick="restoreProduct(<?= $p['id'] ?>, this)"
                                        style="font-size: 12px; font-weight: 600; color: var(--purple); background: none; border: none; cursor: pointer; padding: 0;">Restore</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php renderPagination('products-table'); ?>
            </section>

            <!-- Orders -->
            <section id="orders">
                <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 14px;">Orders</h2>
                <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <div style="background: #f9fafb; padding: 9px 16px; display: grid; grid-template-columns: 80px 1fr 90px 90px 150px; gap: 12px;">
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Order</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Buyer</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Total</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Status</p>
                        <p style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Update</p>
                    </div>
                    <?php foreach ($allOrders as $o): ?>
                        <div id="order-row-<?= $o['id'] ?>" style="background: var(--white); padding: 10px 16px; display: grid; grid-template-columns: 80px 1fr 90px 90px 150px; align-items: center; gap: 12px;">
                            <p style="font-size: 12px; font-weight: 700; color: var(--text-muted);">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></p>
                            <p style="font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($o['buyer_name']) ?></p>
                            <p style="font-size: 13px;">R<?= number_format($o['total'], 2) ?></p>
                            <p id="order-status-<?= $o['id'] ?>" style="font-size: 12px; text-transform: capitalize; color: var(--text-sub);"><?= htmlspecialchars($o['status']) ?></p>
                            <select onchange="updateOrderStatus(<?= $o['id'] ?>, this.value)"
                                style="height: 28px; padding: 0 6px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px; background: var(--white);">
                                <?php foreach (['pending','paid','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php renderPagination('orders-table'); ?>
            </section>

        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>