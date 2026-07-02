<?php
$pageTitle = 'My Account';
require_once 'header.php';
require_once 'components/_pagination.php';

if (!$currentUser) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$my_orders = $order->getByUser($currentUser['id']) ?? [];
$extraJs   = [BASE_URL . '/assets/js/account.js'];
?>

<div class="container">

    <div style="display: grid; grid-template-columns: 220px 1fr; gap: 40px; align-items: start;">

        <!-- Sidebar -->
        <aside>
            <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 16px; letter-spacing: 0.05em;">Account</p>

            <div style="display: flex; flex-direction: column; gap: 2px;">
                <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 2px;">Name</p>
                    <p style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($currentUser['name']) ?></p>
                </div>
                <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 2px;">Email</p>
                    <p style="font-size: 13px; word-break: break-all;"><?= htmlspecialchars($currentUser['email']) ?></p>
                </div>
                <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 2px;">Role</p>
                    <p style="font-size: 13px; text-transform: capitalize;"><?= htmlspecialchars($currentUser['role']) ?></p>
                </div>
                <div style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 2px;">Status</p>
                    <p style="font-size: 13px;"><?= $currentUser['is_active'] ? 'Active' : 'Inactive' ?></p>
                </div>
            </div>

            <?php if (in_array($currentUser['role'], ['seller', 'admin'])): ?>
                <a href="<?= BASE_URL ?>/my-shop.php" style="display: block; margin-top: 20px; font-size: 13px; color: var(--purple); font-weight: 600;">
                    Go to My Store &rarr;
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/auth/logout.php" style="display: block; margin-top: 12px; font-size: 13px; color: var(--text-muted);">
                Logout
            </a>
        </aside>

        <!-- Main -->
        <main>
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 20px;">
                <h1 style="font-size: 18px; font-weight: 700;">Orders</h1>
                <span style="font-size: 13px; color: var(--text-muted);"><?= count($my_orders) ?> total</span>
            </div>

            <?php if (empty($my_orders)): ?>
                <p style="font-size: 13px; color: var(--text-muted); padding: 40px 0;">
                    No orders yet. <a href="<?= BASE_URL ?>/products.php" style="color: var(--purple); font-weight: 600;">Browse products</a>
                </p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <?php foreach ($my_orders as $o):
                        $s = strtolower(trim($o['status']));
                    ?>
                        <div style="background: var(--white); padding: 14px 18px; display: grid; grid-template-columns: 80px 1fr 80px 100px 120px; align-items: center; gap: 12px;" id="order-status-<?= $o['id'] ?>">
                            <p style="font-size: 12px; font-weight: 700; color: var(--text-muted);">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></p>
                            <p style="font-size: 13px; color: var(--text-sub); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($o['address']) ?>"><?= htmlspecialchars($o['address']) ?></p>
                            <p style="font-size: 13px; font-weight: 600;">R<?= number_format($o['total'], 2) ?></p>
                            <p style="font-size: 12px; color: var(--text-sub); text-transform: capitalize;"><?= htmlspecialchars($o['status']) ?></p>
                            <p style="font-size: 12px; color: var(--text-muted); text-align: right;">
                                <?php if ($s === 'shipped'): ?>
                                    <button onclick="markOrderAsDelivered(<?= $o['id'] ?>, '<?= BASE_URL ?>')"
                                        style="font-size: 12px; font-weight: 700; color: var(--purple); background: none; border: none; cursor: pointer; padding: 0;">
                                        Mark Delivered
                                    </button>
                                <?php elseif ($s === 'completed' || $s === 'delivered'): ?>
                                    Delivered
                                <?php else: ?>
                                    <?= date('d M Y', strtotime($o['created_at'])) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php renderPagination('customer-orders-table', 10); ?>
            <?php endif; ?>
        </main>

    </div>
</div>

<?php require_once 'footer.php'; ?>