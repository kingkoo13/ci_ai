<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Orders Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Orders
        </div>
        <h1 class="page-title">Orders</h1>
    </div>
</div>

<!-- Orders Grid Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 120px;">Order ID</th>
                <th>Purchased On</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th style="width: 150px; text-align: right;">Grand Total</th>
                <th style="width: 120px; text-align: center;">Status</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)) : ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No orders found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('admin/sales/orders/view/' . $order->id) ?>" style="font-weight: 600;">
                                #<?= esc($order->increment_id) ?>
                            </a>
                        </td>
                        <td><?= date('M d, Y h:i A', strtotime($order->created_at)) ?></td>
                        <td><?= esc($order->customer_firstname . ' ' . $order->customer_lastname) ?></td>
                        <td><?= esc($order->customer_email) ?></td>
                        <td style="text-align: right; font-weight: 700;">$<?= number_format($order->grand_total, 2) ?></td>
                        <td style="text-align: center;">
                            <span class="status-badge status-<?= esc(strtolower($order->status)) ?>">
                                <?= esc($order->status) ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/sales/orders/view/' . $order->id) ?>" class="btn" style="padding: 3px 10px; font-size: 11px; font-weight: normal; text-transform: none;">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
