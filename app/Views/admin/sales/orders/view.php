<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Order #<?= esc($order->increment_id) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Orders <span>/</span> View
        </div>
        <h1 class="page-title">Order #<?= esc($order->increment_id) ?> <span class="status-badge status-<?= esc(strtolower($order->status)) ?>" style="font-size: 13px; vertical-align: middle; margin-left: 10px;"><?= esc($order->status) ?></span></h1>
    </div>
    
    <div class="page-actions">
        <a href="<?= base_url('admin/sales/orders') ?>" class="btn btn-secondary">Back</a>
        
        <?php if ($order->status === 'pending' || $order->status === 'processing') : ?>
            <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel</button>
        <?php endif; ?>

        <?php if ($order->status === 'pending') : ?>
            <button type="button" class="btn btn-primary" onclick="confirmInvoice()">Invoice</button>
        <?php endif; ?>

        <?php if ($order->status === 'processing') : ?>
            <button type="button" class="btn btn-primary" id="open-shipping-dialog">Ship</button>
        <?php endif; ?>
    </div>
</div>

<!-- Detailed Grid Layout -->
<div class="order-view-sections">
    <!-- Order & Account Information -->
    <div class="order-box">
        <div class="order-box-title">Order & Account Information</div>
        <div class="order-box-content">
            <p><strong>Order Date:</strong> <?= date('M d, Y h:i A', strtotime($order->created_at)) ?></p>
            <p><strong>Order Status:</strong> <?= esc(ucfirst($order->status)) ?></p>
            <hr style="border:0; border-top:1px solid var(--color-border-light); margin: 15px 0;">
            <p><strong>Customer Name:</strong> <?= esc($order->customer_firstname . ' ' . $order->customer_lastname) ?></p>
            <p><strong>Email:</strong> <?= esc($order->customer_email) ?></p>
            <p><strong>Customer Group:</strong> General</p>
        </div>
    </div>
    
    <!-- Address & Payment Information -->
    <div class="order-box">
        <div class="order-box-title">Address & Payment Information</div>
        <div class="order-box-content">
            <p><strong>Billing & Shipping Address:</strong></p>
            <?php if ($address) : ?>
                <p style="padding-left: 10px; font-style: italic; color: var(--color-text-light);">
                    <?= esc($address->street) ?><br>
                    <?= esc($address->city) ?>, <?= esc($address->region) ?>, <?= esc($address->postcode) ?><br>
                    <?= esc($address->country) ?><br>
                    T: <?= esc($address->telephone) ?>
                </p>
            <?php else : ?>
                <p style="padding-left: 10px; font-style: italic; color: var(--color-text-light);">Jane Doe<br>100 Broadway Ave<br>New York, New York, 10005<br>United States<br>T: 123-456-7890</p>
            <?php endif; ?>
            <hr style="border:0; border-top:1px solid var(--color-border-light); margin: 15px 0;">
            <p><strong>Payment Method:</strong> Check / Money Order</p>
            <p><strong>Shipping Method:</strong> <?= esc($order->shipping_description ?: 'Flat Rate - Fixed') ?> (<strong>$<?= number_format($order->shipping_amount, 2) ?></strong>)</p>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="dashboard-card" style="margin-bottom: 25px;">
    <div class="card-header">Items Ordered</div>
    <div class="card-body" style="padding: 0;">
        <div class="grid-table-container" style="border:none; box-shadow: none;">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="width: 150px;">SKU</th>
                        <th style="width: 120px; text-align: right;">Price</th>
                        <th style="width: 150px; text-align: center;">Qty</th>
                        <th style="width: 120px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><strong><?= esc($item->name) ?></strong></td>
                            <td><?= esc($item->sku) ?></td>
                            <td style="text-align: right;">$<?= number_format($item->price, 2) ?></td>
                            <td style="text-align: center;">
                                <span style="font-size: 12px; display:inline-block; text-align:left;">
                                    Ordered: <strong><?= (int)$item->qty_ordered ?></strong><br>
                                    Invoiced: <strong><?= (int)$item->qty_invoiced ?></strong><br>
                                    Shipped: <strong><?= (int)$item->qty_shipped ?></strong>
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 600;">$<?= number_format($item->row_total, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bottom Totals & Documents History split -->
<div class="order-view-sections">
    <!-- Documents History -->
    <div class="order-box">
        <div class="order-box-title">Order Documents & History</div>
        <div class="order-box-content" style="font-size: 13px;">
            <p><strong>Invoices:</strong></p>
            <ul style="padding-left: 15px; margin-bottom: 15px; list-style: square;">
                <?php if (empty($invoices)) : ?>
                    <li style="color: var(--color-text-muted);">No invoices created.</li>
                <?php else : ?>
                    <?php foreach ($invoices as $invoice) : ?>
                        <li>
                            <a href="<?= base_url('admin/sales/invoices/view/' . $invoice->id) ?>">
                                Invoice #<?= esc($invoice->increment_id) ?>
                            </a>
                            <span style="color: var(--color-text-muted);"> - $<?= number_format($invoice->grand_total, 2) ?> (Created on <?= date('M d, Y', strtotime($invoice->created_at)) ?>)</span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <p><strong>Shipments:</strong></p>
            <ul style="padding-left: 15px; list-style: square;">
                <?php if (empty($shipments)) : ?>
                    <li style="color: var(--color-text-muted);">No shipments created.</li>
                <?php else : ?>
                    <?php foreach ($shipments as $shipment) : ?>
                        <li>
                            <a href="<?= base_url('admin/sales/shipments/view/' . $shipment->id) ?>">
                                Shipment #<?= esc($shipment->increment_id) ?>
                            </a>
                            <span style="color: var(--color-text-muted);"> (Created on <?= date('M d, Y', strtotime($shipment->created_at)) ?>)</span>
                            <?php 
                            $tracks = json_decode($shipment->tracks, true);
                            if (!empty($tracks)) : 
                            ?>
                                <div style="font-size: 11px; padding-left: 10px; margin-top: 3px; color: var(--color-text-light);">
                                    Track: <strong><?= esc($tracks[0]['carrier']) ?></strong> - <code><?= esc($tracks[0]['number']) ?></code>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Order Totals -->
    <div class="order-box">
        <div class="order-box-title">Order Totals</div>
        <div class="order-box-content">
            <table class="order-totals-table">
                <tr>
                    <td class="total-label">Subtotal</td>
                    <td class="total-value">$<?= number_format($order->subtotal, 2) ?></td>
                </tr>
                <tr>
                    <td class="total-label">Shipping & Handling</td>
                    <td class="total-value">$<?= number_format($order->shipping_amount, 2) ?></td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label">Grand Total</td>
                    <td class="total-value">$<?= number_format($order->grand_total, 2) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Native Dialog overlay for entering Shipment tracking info -->
<dialog id="shipping-dialog" style="border: none; border-radius: 8px; box-shadow: var(--shadow-md); padding: 0; width: 100%; max-width: 450px; background: white;">
    <div style="background: var(--color-bg-dark); color: white; padding: 15px 20px; font-weight: 600; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
        <span>Create Shipment & Track</span>
        <button type="button" onclick="document.getElementById('shipping-dialog').close()" style="background: none; border: none; color: white; cursor: pointer; font-size: 16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div style="padding: 20px;">
        <form id="shipping-form" action="<?= base_url('admin/sales/orders/ship/' . $order->id) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="grid-template-columns: 120px 1fr; margin-bottom: 15px;">
                <label for="carrier">Carrier</label>
                <div class="form-control-wrapper">
                    <select id="carrier" name="carrier" class="form-control">
                        <option value="FedEx">FedEx</option>
                        <option value="UPS">UPS</option>
                        <option value="DHL">DHL</option>
                        <option value="USPS">USPS</option>
                        <option value="Custom">Custom</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="grid-template-columns: 120px 1fr; margin-bottom: 20px;">
                <label for="tracking_number">Tracking Code <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="tracking_number" name="tracking_number" class="form-control" required placeholder="e.g. 1Z9999999999999999">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('shipping-dialog').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Shipment</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Workflow forms -->
<form id="invoice-form" action="<?= base_url('admin/sales/orders/invoice/' . $order->id) ?>" method="POST" style="display:none;"><?= csrf_field() ?></form>
<form id="cancel-form" action="<?= base_url('admin/sales/orders/cancel/' . $order->id) ?>" method="POST" style="display:none;"><?= csrf_field() ?></form>

<script>
    // Native Dialog opening
    const shippingDialog = document.getElementById('shipping-dialog');
    const openShippingBtn = document.getElementById('open-shipping-dialog');
    
    if (openShippingBtn && shippingDialog) {
        openShippingBtn.addEventListener('click', () => {
            shippingDialog.showModal();
        });
    }

    function confirmInvoice() {
        if (confirm("Are you sure you want to invoice this order? This will mark the items as fully invoiced and change order status to 'Processing'.")) {
            document.getElementById('invoice-form').submit();
        }
    }

    function confirmCancel() {
        if (confirm("Are you sure you want to cancel this order? Restocking logic will restore product counts and change order status to 'Canceled'.")) {
            document.getElementById('cancel-form').submit();
        }
    }
</script>

<style>
    /* Styling native backdrop overlay */
    #shipping-dialog::backdrop {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }
</style>
<?= $this->endSection() ?>
