<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Invoice #<?= esc($invoice->increment_id) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Invoices <span>/</span> View
        </div>
        <h1 class="page-title">Invoice #<?= esc($invoice->increment_id) ?></h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/sales/invoices') ?>" class="btn btn-secondary">Back</a>
        <a href="javascript:window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Print Invoice</a>
    </div>
</div>

<div class="order-view-sections">
    <div class="order-box">
        <div class="order-box-title">Invoice Information</div>
        <div class="order-box-content">
            <p><strong>Invoice Date:</strong> <?= date('M d, Y h:i A', strtotime($invoice->created_at)) ?></p>
            <p><strong>Order ID:</strong> <a href="<?= base_url('admin/sales/orders/view/' . $order->id) ?>">#<?= esc($order->increment_id) ?></a></p>
            <p><strong>Order Status:</strong> <?= esc(ucfirst($order->status)) ?></p>
        </div>
    </div>
    <div class="order-box">
        <div class="order-box-title">Customer Information</div>
        <div class="order-box-content">
            <p><strong>Customer Name:</strong> <?= esc($order->customer_firstname . ' ' . $order->customer_lastname) ?></p>
            <p><strong>Email:</strong> <?= esc($order->customer_email) ?></p>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="dashboard-card" style="margin-bottom: 25px;">
    <div class="card-header">Items Invoiced</div>
    <div class="card-body" style="padding: 0;">
        <div class="grid-table-container" style="border:none; box-shadow: none;">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="width: 150px;">SKU</th>
                        <th style="width: 120px; text-align: right;">Price</th>
                        <th style="width: 120px; text-align: center;">Qty Invoiced</th>
                        <th style="width: 120px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <?php if ($item->qty_invoiced > 0) : ?>
                            <tr>
                                <td><strong><?= esc($item->name) ?></strong></td>
                                <td><?= esc($item->sku) ?></td>
                                <td style="text-align: right;">$<?= number_format($item->price, 2) ?></td>
                                <td style="text-align: center;"><strong><?= (int)$item->qty_invoiced ?></strong></td>
                                <td style="text-align: right; font-weight: 600;">$<?= number_format($item->price * $item->qty_invoiced, 2) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="order-view-sections">
    <div></div>
    <!-- Invoice Totals -->
    <div class="order-box">
        <div class="order-box-title">Invoice Totals</div>
        <div class="order-box-content">
            <table class="order-totals-table">
                <tr>
                    <td class="total-label">Subtotal Invoiced</td>
                    <td class="total-value">$<?= number_format($invoice->grand_total - $order->shipping_amount, 2) ?></td>
                </tr>
                <tr>
                    <td class="total-label">Shipping & Handling</td>
                    <td class="total-value">$<?= number_format($order->shipping_amount, 2) ?></td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label">Grand Total</td>
                    <td class="total-value">$<?= number_format($invoice->grand_total, 2) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
