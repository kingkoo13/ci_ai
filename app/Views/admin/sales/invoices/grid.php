<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Invoices Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Sales <span>/</span> Invoices
        </div>
        <h1 class="page-title">Invoices</h1>
    </div>
</div>

<!-- Invoices Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 120px;">Invoice ID</th>
                <th style="width: 120px;">Order ID</th>
                <th>Invoiced On</th>
                <th>Customer Name</th>
                <th style="width: 150px; text-align: right;">Amount Invoiced</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)) : ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No invoices found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($invoices as $invoice) : ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('admin/sales/invoices/view/' . $invoice->id) ?>" style="font-weight: 600;">
                                #<?= esc($invoice->increment_id) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/sales/orders/view/' . $invoice->order_id) ?>">
                                #<?= esc($invoice->order_increment_id) ?>
                            </a>
                        </td>
                        <td><?= date('M d, Y h:i A', strtotime($invoice->created_at)) ?></td>
                        <td><?= esc($invoice->customer_firstname . ' ' . $invoice->customer_lastname) ?></td>
                        <td style="text-align: right; font-weight: 700; color: var(--color-success);">$<?= number_format($invoice->grand_total, 2) ?></td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/sales/invoices/view/' . $invoice->id) ?>" class="btn" style="padding: 3px 10px; font-size: 11px; font-weight: normal; text-transform: none;">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
