<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Shipments Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Shipments
        </div>
        <h1 class="page-title">Shipments</h1>
    </div>
</div>

<!-- Shipments Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 120px;">Shipment ID</th>
                <th style="width: 120px;">Order ID</th>
                <th>Shipped On</th>
                <th>Customer Name</th>
                <th>Carrier & Tracking Number</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($shipments)) : ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No shipments found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($shipments as $shipment) : ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('admin/sales/shipments/view/' . $shipment->id) ?>" style="font-weight: 600;">
                                #<?= esc($shipment->increment_id) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/sales/orders/view/' . $shipment->order_id) ?>">
                                #<?= esc($shipment->order_increment_id) ?>
                            </a>
                        </td>
                        <td><?= date('M d, Y h:i A', strtotime($shipment->created_at)) ?></td>
                        <td><?= esc($shipment->customer_firstname . ' ' . $shipment->customer_lastname) ?></td>
                        <td>
                            <?php 
                            $tracks = json_decode($shipment->tracks, true);
                            if (!empty($tracks)) {
                                echo '<strong>' . esc($tracks[0]['carrier']) . '</strong> - <code>' . esc($tracks[0]['number']) . '</code>';
                            } else {
                                echo '<span style="color: var(--color-text-muted);">None</span>';
                            }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/sales/shipments/view/' . $shipment->id) ?>" class="btn" style="padding: 3px 10px; font-size: 11px; font-weight: normal; text-transform: none;">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
