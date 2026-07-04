<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Order Success<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="max-width: 600px; margin: 40px auto; text-align:center; padding: 40px; border: 1px solid var(--color-border-light); border-radius: var(--border-radius); background: #ffffff; box-shadow: var(--shadow-sm);">
    
    <div style="color: var(--color-success); font-size: 64px; margin-bottom: 20px;">
        <i class="fa-regular fa-circle-check"></i>
    </div>

    <h1 style="font-size: 32px; font-weight: 300; margin-bottom: 15px; color: var(--color-text);">Thank you for your purchase!</h1>
    
    <?php if ($order) : ?>
        <p style="font-size: 16px; margin-bottom: 10px;">Your order number is: <strong><?= esc($order->increment_id) ?></strong>.</p>
        <p style="color: var(--color-text-light); margin-bottom: 30px;">We'll send you an email confirmation with order details and tracking info shortly.</p>
    <?php else : ?>
        <p style="font-size: 16px; margin-bottom: 30px;">Your order was successfully placed.</p>
    <?php endif; ?>

    <div style="display:flex; gap:15px; justify-content:center;">
        <a href="<?= base_url('/') ?>" class="btn btn-primary" style="padding:10px 25px;">Continue Shopping</a>
        <?php if (session()->get('customer_logged_in')) : ?>
            <a href="<?= base_url('customer/account') ?>" class="btn btn-secondary" style="padding:10px 25px;">View My Orders</a>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
