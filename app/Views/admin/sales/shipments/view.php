<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Shipment #<?= esc($shipment->increment_id) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Shipments <span>/</span> View
        </div>
        <h1 class="page-title">Shipment #<?= esc($shipment->increment_id) ?></h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/sales/shipments') ?>" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="order-view-sections">
    <div class="order-box">
        <div class="order-box-title">Shipment Information</div>
        <div class="order-box-content">
            <p><strong>Shipped On:</strong> <?= date('M d, Y h:i A', strtotime($shipment->created_at)) ?></p>
            <p><strong>Order ID:</strong> <a href="<?= base_url('admin/sales/orders/view/' . $order->id) ?>">#<?= esc($order->increment_id) ?></a></p>
            <p><strong>Order Status:</strong> <?= esc(ucfirst($order->status)) ?></p>
        </div>
    </div>
    <div class="order-box">
        <div class="order-box-title">Tracking Information</div>
        <div class="order-box-content">
            <?php 
            $tracks = json_decode($shipment->tracks, true);
            if (!empty($tracks)) :
            ?>
                <p><strong>Carrier:</strong> <?= esc($tracks[0]['carrier']) ?></p>
                <p><strong>Tracking Number:</strong> <code><?= esc($tracks[0]['number']) ?></code></p>
            <?php else : ?>
                <p style="color: var(--color-text-muted);">No tracking codes associated.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="dashboard-card" style="margin-bottom: 25px;">
    <div class="card-header">Items Shipped</div>
    <div class="card-body" style="padding: 0;">
        <div class="grid-table-container" style="border:none; box-shadow: none;">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="width: 150px;">SKU</th>
                        <th style="width: 120px; text-align: right;">Price</th>
                        <th style="width: 120px; text-align: center;">Qty Shipped</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <?php if ($item->qty_shipped > 0) : ?>
                            <tr>
                                <td><strong><?= esc($item->name) ?></strong></td>
                                <td><?= esc($item->sku) ?></td>
                                <td style="text-align: right;">$<?= number_format($item->price, 2) ?></td>
                                <td style="text-align: center;"><strong><?= (int)$item->qty_shipped ?></strong></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
